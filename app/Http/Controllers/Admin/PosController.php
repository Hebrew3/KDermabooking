<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use App\Models\InventoryUsageLog;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Display the POS interface.
     */
    public function index()
    {
        $products = InventoryItem::active()
            ->where('current_stock', '>', 0)
            ->whereNotNull('selling_price')
            ->orderBy('name')
            ->get();

        $clients = User::where('role', 'client')
            ->orderBy('first_name')
            ->get();

        return view('admin.pos.index', compact('products', 'clients'));
    }

    /**
     * Get available products for POS.
     */
    public function getProducts(Request $request)
    {
        $query = InventoryItem::active()
            ->where('current_stock', '>', 0)
            ->whereNotNull('selling_price');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        $products = $query->orderBy('name')->get();

        return response()->json($products);
    }

    /**
     * Process a sale.
     */
    public function processSale(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.inventory_item_id' => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,gcash,paymaya,other',
            'client_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Validate stock availability
            foreach ($request->items as $item) {
                $inventoryItem = InventoryItem::findOrFail($item['inventory_item_id']);
                
                // Check stock based on tracking type
                if ($inventoryItem->usesMlTracking()) {
                    // For mL-tracked items, check virtual stock (total_volume_ml)
                    // Calculate volume needed: quantity * content_per_unit (converted to mL)
                    $quantity = (int) $item['quantity'];
                    $contentPerUnit = (float) ($inventoryItem->content_per_unit ?? 0);
                    $contentUnit = $inventoryItem->content_unit ?? 'mL';
                    
                    // Convert content_per_unit to mL
                    $contentPerUnitInMl = $this->convertContentToMl($contentPerUnit, $contentUnit);
                    $volumeNeeded = $quantity * $contentPerUnitInMl;
                    
                    $availableVolume = (float) ($inventoryItem->total_volume_ml ?? 0);
                    
                    if ($availableVolume < $volumeNeeded) {
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for {$inventoryItem->name}. Available: " . number_format($availableVolume, 2) . " mL, Required: " . number_format($volumeNeeded, 2) . " mL",
                        ], 400);
                    }
                } else {
                    // For quantity-based items, check current_stock
                    if ($inventoryItem->current_stock < $item['quantity']) {
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for {$inventoryItem->name}. Available: {$inventoryItem->current_stock} {$inventoryItem->unit}",
                        ], 400);
                    }
                }
            }

            // Calculate change
            $change = max(0, $request->amount_paid - $request->total_amount);

            // Create sale
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'client_id' => $request->client_id,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'total_amount' => $request->total_amount,
                'amount_paid' => $request->amount_paid,
                'change' => $change,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            // Create sale items and update inventory
            foreach ($request->items as $item) {
                $inventoryItem = InventoryItem::findOrFail($item['inventory_item_id']);
                
                $subtotal = $item['unit_price'] * $item['quantity'];
                $discount = $item['discount'] ?? 0;
                $total = $subtotal - $discount;

                // Create sale item
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'inventory_item_id' => $inventoryItem->id,
                    'item_name' => $inventoryItem->name,
                    'item_sku' => $inventoryItem->sku,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                ]);

                // Deduct from inventory based on tracking type
                $quantity = (int) $item['quantity'];
                $stockBefore = null;
                $stockAfter = null;
                $volumeDeducted = 0;
                $quantityDeducted = 0;
                
                if ($inventoryItem->usesMlTracking()) {
                    // For mL-tracked items, use virtual deduction
                    $contentPerUnit = (float) ($inventoryItem->content_per_unit ?? 0);
                    $contentUnit = $inventoryItem->content_unit ?? 'mL';
                    
                    // Convert content_per_unit to mL
                    $contentPerUnitInMl = $this->convertContentToMl($contentPerUnit, $contentUnit);
                    $volumeMl = $quantity * $contentPerUnitInMl;
                    
                    // Store stock before deduction
                    $stockBefore = (float) ($inventoryItem->total_volume_ml ?? 0);
                    
                    // Deduct using virtual deduction system
                    $success = $inventoryItem->deductVolumeMl($volumeMl);
                    
                    if ($success) {
                        $inventoryItem->refresh();
                        $stockAfter = (float) ($inventoryItem->total_volume_ml ?? 0);
                        $volumeDeducted = $volumeMl;
                        
                        \Log::info("POS: Deducted {$volumeMl} mL of {$inventoryItem->name} (Sale #{$sale->id}). Remaining: {$stockAfter} mL");
                    } else {
                        \Log::warning("POS: Failed to deduct {$volumeMl} mL of {$inventoryItem->name} (Sale #{$sale->id})");
                    }
                } else {
                    // For quantity-based items, deduct whole units
                    $stockBefore = $inventoryItem->current_stock;
                    
                    // Deduct whole units
                    $inventoryItem->decrement('current_stock', $quantity);
                    $inventoryItem->refresh();
                    
                    $stockAfter = $inventoryItem->current_stock;
                    $quantityDeducted = $quantity;
                    
                    \Log::info("POS: Deducted {$quantity} {$inventoryItem->unit} of {$inventoryItem->name} (Sale #{$sale->id}). Remaining: {$stockAfter}");
                }
                
                // Create inventory usage log for POS sale
                InventoryUsageLog::create([
                    'appointment_id' => null, // POS sales don't have appointments
                    'inventory_item_id' => $inventoryItem->id,
                    'service_id' => null, // POS sales don't have services
                    'item_name' => $inventoryItem->name,
                    'item_sku' => $inventoryItem->sku,
                    'usage_type' => 'pos',
                    'quantity_deducted' => $quantityDeducted,
                    'volume_ml_deducted' => $volumeDeducted,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'unit' => $inventoryItem->usesMlTracking() ? 'mL' : $inventoryItem->unit,
                    'is_ml_tracking' => $inventoryItem->usesMlTracking(),
                    'notes' => "POS Sale #{$sale->sale_number} - Quantity: {$quantity}",
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale processed successfully.',
                'sale' => $sale->load('items'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get sale receipt.
     */
    public function getReceipt(Sale $sale)
    {
        $sale->load(['items.inventoryItem', 'user', 'client']);
        
        return view('admin.pos.receipt', compact('sale'));
    }

    /**
     * Print receipt.
     */
    public function printReceipt(Sale $sale)
    {
        $sale->load(['items.inventoryItem', 'user', 'client']);
        
        return view('admin.pos.print-receipt', compact('sale'));
    }

    /**
     * Import past sales data.
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240', // 10MB max
                'skip_headers' => 'nullable|boolean',
            ], [
                'file.required' => 'Please select a file to upload',
                'file.file' => 'The uploaded file is not valid',
                'file.mimes' => 'The file must be a CSV or Excel file (CSV, XLSX, XLS)',
                'file.max' => 'The file size must not exceed 10MB',
            ]);

            $file = $request->file('file');
            $skipHeaders = $request->has('skip_headers');

            // Get file extension
            $extension = $file->getClientOriginalExtension();
            
            // Read file based on extension
            if (in_array(strtolower($extension), ['csv', 'txt'])) {
                $data = $this->readCSV($file, $skipHeaders);
            } else {
                $data = $this->readExcel($file, $skipHeaders);
            }

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found in the file'
                ], 400);
            }

            // Process sales data
            $result = $this->importSales($data);

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$result['imported']} sales records",
                'imported' => $result['imported'],
                'failed' => $result['failed'],
                'errors' => $result['errors']
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                $errorMessages[] = ucfirst($field) . ': ' . implode(', ', $messages);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(' | ', $errorMessages),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('POS import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to import data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Read CSV file.
     */
    private function readCSV($file, $skipHeaders = true)
    {
        $data = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        if ($handle === false) {
            throw new \Exception('Unable to open file');
        }
        
        $isFirstRow = true;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            // Skip empty rows
            if (empty(array_filter($row, function($cell) { return trim($cell) !== ''; }))) {
                continue;
            }
            
            if ($skipHeaders && $isFirstRow) {
                $isFirstRow = false;
                continue;
            }
            
            // Clean up the row data
            $cleanedRow = array_map('trim', $row);
            $data[] = $cleanedRow;
        }

        fclose($handle);
        
        if (empty($data)) {
            throw new \Exception('No data found in file. Please check if the file has data rows.');
        }
        
        return $data;
    }

    /**
     * Read Excel file.
     */
    private function readExcel($file, $skipHeaders = true)
    {
        // Check if PhpSpreadsheet is available
        if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $worksheet = $spreadsheet->getActiveSheet();
                $data = [];
                $isFirstRow = true;

                foreach ($worksheet->getRowIterator() as $row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    
                    $rowData = [];
                    foreach ($cellIterator as $cell) {
                        $rowData[] = $cell->getCalculatedValue();
                    }
                    
                    // Skip empty rows
                    if (empty(array_filter($rowData, function($cell) { return trim($cell) !== ''; }))) {
                        continue;
                    }
                    
                    if ($skipHeaders && $isFirstRow) {
                        $isFirstRow = false;
                        continue;
                    }
                    
                    $data[] = array_map('trim', $rowData);
                }
                
                if (empty($data)) {
                    throw new \Exception('No data found in Excel file. Please check if the file has data rows.');
                }
                
                return $data;
            } catch (\Exception $e) {
                throw new \Exception('Error reading Excel file: ' . $e->getMessage());
            }
        } else {
            // Fallback: Try to read as CSV (some Excel files can be read as CSV)
            return $this->readCSV($file, $skipHeaders);
        }
    }

    /**
     * Import sales data.
     */
    private function importSales($data)
    {
        $imported = 0;
        $failed = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                // Expected format: client_id, total_amount, status, payment_method, sale_date (optional)
                // Minimum required: total_amount
                if (count($row) < 1) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": Insufficient columns (need at least total_amount)";
                    continue;
                }

                // Parse the row data
                $clientId = !empty($row[0]) ? (int)$row[0] : null;
                $totalAmount = isset($row[1]) && is_numeric($row[1]) ? (float)$row[1] : (isset($row[0]) && is_numeric($row[0]) ? (float)$row[0] : 0);
                $status = isset($row[2]) && !empty($row[2]) ? trim($row[2]) : 'completed';
                $paymentMethod = isset($row[3]) && !empty($row[3]) ? trim($row[3]) : 'cash';
                $saleDate = isset($row[4]) && !empty($row[4]) ? \Carbon\Carbon::parse($row[4]) : now();

                // Validate client_id if provided
                if ($clientId && !User::where('id', $clientId)->where('role', 'client')->exists()) {
                    $clientId = null; // Set to null if client doesn't exist
                }

                // Validate payment method
                $validPaymentMethods = ['cash', 'card', 'gcash', 'paymaya', 'other'];
                if (!in_array(strtolower($paymentMethod), $validPaymentMethods)) {
                    $paymentMethod = 'cash';
                }

                // Validate status
                $validStatuses = ['pending', 'completed', 'cancelled', 'refunded'];
                if (!in_array(strtolower($status), $validStatuses)) {
                    $status = 'completed';
                }

                if ($totalAmount <= 0) {
                    $failed++;
                    $errors[] = "Row " . ($index + 1) . ": Total amount must be greater than 0";
                    continue;
                }

                Sale::create([
                    'user_id' => auth()->id(),
                    'client_id' => $clientId,
                    'subtotal' => $totalAmount,
                    'discount' => 0,
                    'tax' => 0,
                    'total_amount' => $totalAmount,
                    'amount_paid' => $totalAmount,
                    'change' => 0,
                    'payment_method' => $paymentMethod,
                    'status' => $status,
                    'sale_date' => $saleDate,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                \Log::error("Sales import error for row " . ($index + 1) . ": " . $e->getMessage(), [
                    'row_data' => $row,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return ['imported' => $imported, 'failed' => $failed, 'errors' => $errors];
    }

    /**
     * Convert content_per_unit to mL based on the unit.
     * 
     * @param float $value The value to convert
     * @param string $unit The unit of the value (mL, L, g, kg, oz, fl oz, etc.)
     * @return float The value converted to mL
     */
    private function convertContentToMl(float $value, string $unit): float
    {
        $unit = strtolower(trim($unit));
        
        switch ($unit) {
            case 'ml':
            case 'milliliter':
            case 'millilitre':
                return $value;
            
            case 'l':
            case 'liter':
            case 'litre':
                return $value * 1000; // 1 L = 1000 mL
            
            case 'fl oz':
            case 'fluid ounce':
            case 'fluid oz':
                return $value * 29.5735; // 1 fl oz ≈ 29.5735 mL
            
            case 'oz':
            case 'ounce':
                // For weight (oz), we can't directly convert to mL without density
                // Assume it's fluid ounce if not specified
                return $value * 29.5735;
            
            case 'g':
            case 'gram':
            case 'grams':
                // For weight, we can't directly convert to mL without density
                // For water: 1g = 1mL, but for other substances it varies
                // We'll assume 1g = 1mL as a reasonable default for most liquids
                return $value;
            
            case 'kg':
            case 'kilogram':
            case 'kilograms':
                // For weight, assume 1kg = 1000mL (for water-like density)
                return $value * 1000;
            
            case 'pc':
            case 'pcs':
            case 'piece':
            case 'pieces':
                // For pieces, we can't convert to mL
                // Return 0 to indicate conversion not possible
                return 0;
            
            default:
                // Unknown unit, assume it's already in mL
                \Log::warning("Unknown unit '{$unit}' for content conversion in POS. Assuming mL.");
                return $value;
        }
    }
}
