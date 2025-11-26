<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ForecastingController extends Controller
{
    /**
     * Display forecasting dashboard.
     */
    public function index()
    {
        $forecasts = $this->generateForecasts();
        $services = Service::where('is_active', true)->get();
        
        return view('admin.forecasting.index', compact('forecasts', 'services'));
    }

    /**
     * Generate forecasts using Simple Moving Average (SMA).
     */
    private function generateForecasts()
    {
        $forecasts = [];
        
        // Get appointment demand for the last 6 months
        $monthlyDemand = $this->getMonthlyDemand(6);
        
        // Calculate 3-month moving average forecast
        $forecasts['appointments'] = $this->calculateSMA($monthlyDemand, 3);
        
        // Get service-specific demand
        $services = Service::where('is_active', true)->get();
        foreach ($services as $service) {
            $serviceDemand = $this->getServiceDemand($service->id, 6);
            $forecasts['services'][$service->id] = [
                'name' => $service->name,
                'forecast' => $this->calculateSMA($serviceDemand, 3),
                'historical' => $serviceDemand
            ];
        }
        
        return $forecasts;
    }

    /**
     * Get monthly appointment demand.
     */
    private function getMonthlyDemand($months = 6)
    {
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        
        $demand = Appointment::select(
                DB::raw('YEAR(appointment_date) as year'),
                DB::raw('MONTH(appointment_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('appointment_date', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                    'demand' => $item->count
                ];
            })
            ->toArray();

        return $demand;
    }

    /**
     * Get service-specific demand.
     */
    private function getServiceDemand($serviceId, $months = 6)
    {
        $startDate = Carbon::now()->subMonths($months)->startOfMonth();
        
        $demand = Appointment::select(
                DB::raw('YEAR(appointment_date) as year'),
                DB::raw('MONTH(appointment_date) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('service_id', $serviceId)
            ->where('appointment_date', '>=', $startDate)
            ->where('status', '!=', 'cancelled')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                    'demand' => $item->count
                ];
            })
            ->toArray();

        return $demand;
    }

    /**
     * Calculate Simple Moving Average (SMA).
     * Formula: Forecast(n+1) = (D1 + D2 + D3 + ... + Dn) / n
     */
    private function calculateSMA($historicalData, $periods = 3)
    {
        if (count($historicalData) < $periods) {
            return [
                'forecast' => 0,
                'confidence' => 'Low',
                'historical_average' => 0,
                'trend' => 'Stable',
                'periods_used' => 0,
                'message' => 'Insufficient historical data for accurate forecasting',
                'calculation_details' => [
                    'sum' => 0,
                    'periods' => $periods,
                    'formula' => 'Insufficient data'
                ]
            ];
        }

        // Get the last n periods for calculation
        $lastPeriods = array_slice($historicalData, -$periods);
        $sum = array_sum(array_column($lastPeriods, 'demand'));
        $forecast = round($sum / $periods);

        // Calculate confidence based on data consistency
        $demands = array_column($lastPeriods, 'demand');
        $average = array_sum($demands) / count($demands);
        $variance = array_sum(array_map(function($x) use ($average) { 
            return pow($x - $average, 2); 
        }, $demands)) / count($demands);
        $standardDeviation = sqrt($variance);
        
        // Determine confidence level
        $coefficientOfVariation = $average > 0 ? ($standardDeviation / $average) : 1;
        if ($coefficientOfVariation < 0.2) {
            $confidence = 'High';
        } elseif ($coefficientOfVariation < 0.5) {
            $confidence = 'Medium';
        } else {
            $confidence = 'Low';
        }

        return [
            'forecast' => $forecast,
            'confidence' => $confidence,
            'historical_average' => round($average),
            'trend' => $this->calculateTrend($lastPeriods),
            'periods_used' => $periods,
            'calculation_details' => [
                'sum' => $sum,
                'periods' => $periods,
                'formula' => "({$sum}) / {$periods} = {$forecast}"
            ]
        ];
    }

    /**
     * Calculate trend direction.
     */
    private function calculateTrend($data)
    {
        if (count($data) < 2) return 'Stable';
        
        $first = $data[0]['demand'];
        $last = end($data)['demand'];
        
        if ($last > $first * 1.1) {
            return 'Increasing';
        } elseif ($last < $first * 0.9) {
            return 'Decreasing';
        } else {
            return 'Stable';
        }
    }

    /**
     * Get forecast for specific service.
     */
    public function getServiceForecast(Request $request)
    {
        $serviceId = $request->input('service_id');
        $periods = $request->input('periods', 3);
        
        $service = Service::findOrFail($serviceId);
        $historicalData = $this->getServiceDemand($serviceId, 6);
        $forecast = $this->calculateSMA($historicalData, $periods);
        
        return response()->json([
            'service' => $service->name,
            'forecast' => $forecast,
            'historical' => $historicalData
        ]);
    }

    /**
     * Export forecasting report.
     */
    public function export()
    {
        $forecasts = $this->generateForecasts();
        
        $csvData = [];
        $csvData[] = ['Type', 'Service/Category', 'Next Month Forecast', 'Confidence', 'Trend', 'Historical Average'];
        
        // Add overall appointments forecast
        $csvData[] = [
            'Overall Appointments',
            'All Services',
            $forecasts['appointments']['forecast'],
            $forecasts['appointments']['confidence'],
            $forecasts['appointments']['trend'],
            $forecasts['appointments']['historical_average']
        ];
        
        // Add service-specific forecasts
        if (isset($forecasts['services'])) {
            foreach ($forecasts['services'] as $serviceData) {
                $csvData[] = [
                    'Service Demand',
                    $serviceData['name'],
                    $serviceData['forecast']['forecast'],
                    $serviceData['forecast']['confidence'],
                    $serviceData['forecast']['trend'],
                    $serviceData['forecast']['historical_average']
                ];
            }
        }
        
        $filename = 'forecasting_report_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
