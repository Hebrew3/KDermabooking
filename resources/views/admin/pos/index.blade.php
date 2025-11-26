<x-app-layout>
<x-mobile-header />
<x-admin-sidebar />

<style>
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
        }
        to {
            transform: translateX(0);
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    
    .slide-in-right {
        animation: slideInRight 0.3s ease-out;
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    
    .product-card:hover {
        transform: translateY(-4px);
    }
    
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #ec4899;
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #db2777;
    }
</style>

<div class="lg:ml-64">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-pink-50">
        <!-- Enhanced Header -->
        <div class="bg-white/95 backdrop-blur-sm shadow-md border-b border-gray-200 sticky top-0 z-30">
            <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <div class="hidden sm:flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl shadow-lg">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold bg-gradient-to-r from-pink-600 via-rose-500 to-pink-600 bg-clip-text text-transparent">
                                    Point of Sale
                                </h1>
                                <p class="text-xs sm:text-sm text-gray-600 mt-0.5">Process sales efficiently</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
                        <a href="{{ route('admin.sales.index') }}" class="p-2 sm:p-2.5 text-gray-600 hover:text-pink-600 hover:bg-pink-50 rounded-xl transition-all duration-200 group">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </a>
                        <div class="hidden sm:flex items-center space-x-2 text-xs sm:text-sm text-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 px-3 py-2 rounded-xl border border-gray-200">
                            <div class="relative">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <div class="absolute inset-0 w-2 h-2 bg-green-500 rounded-full animate-ping opacity-75"></div>
                            </div>
                            <span class="font-semibold truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Cart Toggle Button -->
        <div class="lg:hidden fixed bottom-6 right-6 z-40">
            <button onclick="toggleMobileCart()" id="mobileCartToggle" class="relative bg-gradient-to-r from-pink-500 to-rose-500 text-white p-4 sm:p-5 rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:scale-110 active:scale-95 group">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span id="cartBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-lg hidden transform scale-0 group-hover:scale-100 transition-transform">0</span>
            </button>
        </div>

        <div class="flex flex-col lg:flex-row h-[calc(100vh-70px)] sm:h-[calc(100vh-80px)] lg:h-[calc(100vh-100px)]">
            <!-- Products Section -->
            <div class="w-full lg:w-2/3 flex flex-col bg-white/50 backdrop-blur-sm lg:border-r border-gray-200">
                <!-- Enhanced Search and Filter -->
                <div class="bg-white/80 backdrop-blur-sm p-3 sm:p-4 border-b border-gray-200 sticky top-[70px] sm:top-[80px] lg:top-0 z-20 shadow-sm">
                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                        <div class="flex-1 relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" 
                                   id="productSearch" 
                                   placeholder="Search products by name, SKU..."
                                   class="w-full pl-10 pr-4 py-2.5 sm:py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all text-sm sm:text-base bg-white shadow-sm">
                        </div>
                        <select id="categoryFilter" class="px-4 py-2.5 sm:py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white text-sm sm:text-base shadow-sm font-medium">
                            <option value="">All Categories</option>
                            @foreach($products->pluck('category')->unique()->sort() as $category)
                                <option value="{{ $category }}">{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Enhanced Products Grid -->
                <div class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6 bg-gradient-to-b from-gray-50/50 to-white custom-scrollbar">
                    <div id="productsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2 sm:gap-3 md:gap-4">
                        @foreach($products as $product)
                        <div class="product-card group bg-white rounded-2xl shadow-md border-2 border-gray-100 p-3 sm:p-4 cursor-pointer hover:shadow-2xl hover:border-pink-300 transition-all duration-300 transform hover:-translate-y-2 relative overflow-hidden"
                             data-product-id="{{ $product->id }}"
                             data-product-name="{{ $product->name }}"
                             data-product-sku="{{ $product->sku }}"
                             data-product-price="{{ $product->selling_price }}"
                             data-product-stock="{{ $product->current_stock }}"
                             data-product-category="{{ $product->category }}">
                            <!-- Hover gradient overlay -->
                            <div class="absolute inset-0 bg-gradient-to-br from-pink-500/0 to-rose-500/0 group-hover:from-pink-500/5 group-hover:to-rose-500/5 transition-all duration-300 pointer-events-none"></div>
                            
                            <div class="relative z-10">
                                <div class="mb-2 sm:mb-3">
                                    <h3 class="font-bold text-gray-900 text-xs sm:text-sm md:text-base line-clamp-2 min-h-[2.5rem] sm:min-h-[3rem] leading-tight">{{ $product->name }}</h3>
                                    <p class="text-xs text-gray-500 mt-1 font-mono">{{ $product->sku }}</p>
                                </div>
                                <div class="mb-2 sm:mb-3">
                                    <span class="text-base sm:text-lg md:text-xl font-extrabold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">₱{{ number_format($product->selling_price, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs mb-2">
                                    <span class="text-gray-600 font-medium">Stock: <span class="font-bold text-gray-900">{{ $product->current_stock }}</span></span>
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $product->stock_status_color }} shadow-sm">
                                        {{ ucfirst(str_replace('_', ' ', $product->stock_status)) }}
                                    </span>
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-100 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                                    <button class="w-full bg-gradient-to-r from-pink-500 to-rose-500 text-white text-xs font-bold py-2 rounded-xl hover:from-pink-600 hover:to-rose-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($products->isEmpty())
                    <div class="text-center py-16 sm:py-20">
                        <div class="inline-flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 rounded-full mb-4 sm:mb-6">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <p class="text-gray-700 font-bold text-lg sm:text-xl mb-2">No products available</p>
                        <p class="text-gray-500 text-sm sm:text-base">Add products to inventory to start selling</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Enhanced Cart Section -->
            <div id="cartSection" class="hidden lg:flex lg:w-1/3 flex-col bg-white/95 backdrop-blur-sm border-l border-gray-200 shadow-xl h-full">
                <!-- Enhanced Cart Header -->
                <div class="p-3 sm:p-4 border-b-2 border-gray-200 bg-gradient-to-br from-pink-50 via-rose-50 to-pink-50 flex-shrink-0">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 flex items-center space-x-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <span>Shopping Cart</span>
                            <span id="cartItemCount" class="hidden bg-pink-500 text-white text-xs font-bold px-2 py-1 rounded-full">0</span>
                        </h2>
                        <button onclick="closeMobileCart()" class="lg:hidden text-gray-500 hover:text-gray-700 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Customer (Optional)</label>
                        <select id="customerSelect" class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm font-medium">
                            <option value="">Walk-in Customer</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Enhanced Cart Items - Scrollable -->
                <div class="flex-1 overflow-y-auto p-3 custom-scrollbar" style="min-height: 200px; max-height: 400px;">
                    <div id="cartItems" class="space-y-2.5">
                        <div class="text-center text-gray-500 py-8">
                            <div class="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-full mb-3">
                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <p class="font-bold text-gray-600 text-sm">Cart is empty</p>
                            <p class="text-xs text-gray-400 mt-1">Click on products to add them</p>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Cart Summary - Fixed at Bottom -->
                <div class="border-t-2 border-gray-200 bg-gradient-to-br from-gray-50 to-white shadow-inner flex-shrink-0 overflow-y-auto" style="max-height: 400px;">
                    <!-- Order Summary -->
                    <div class="p-3 sm:p-4 space-y-2.5 border-b border-gray-200">
                        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Order Summary</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-600 font-medium">Subtotal:</span>
                                <span id="cartSubtotal" class="font-bold text-gray-900 text-sm">₱0.00</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-600 font-medium">Discount:</span>
                                <input type="number" 
                                       id="cartDiscount" 
                                       value="0" 
                                       step="0.01" 
                                       min="0"
                                       class="w-24 px-2 py-1.5 border-2 border-gray-200 rounded-lg text-right text-xs focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm font-semibold">
                            </div>
                            <div class="border-t-2 border-gray-300 pt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-900 font-extrabold text-base">Total:</span>
                                    <span id="cartTotal" class="text-pink-600 font-extrabold text-xl">₱0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="p-3 sm:p-4 space-y-2.5 border-b border-gray-200">
                        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wide">Payment Details</h3>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Payment Method</label>
                            <select id="paymentMethod" class="w-full px-2.5 py-2 border-2 border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm font-medium">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="gcash">GCash</option>
                                <option value="paymaya">PayMaya</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Amount Paid</label>
                            <input type="number" 
                                   id="amountPaid" 
                                   step="0.01" 
                                   min="0"
                                   placeholder="0.00"
                                   class="w-full px-2.5 py-2 border-2 border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-pink-500 focus:border-pink-500 bg-white shadow-sm font-semibold">
                        </div>
                        <div class="flex justify-between items-center bg-gradient-to-r from-green-50 to-emerald-50 p-2.5 rounded-lg border-2 border-green-200">
                            <span class="text-gray-700 font-bold text-xs">Change:</span>
                            <span id="changeAmount" class="font-extrabold text-green-600 text-base">₱0.00</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="p-3 sm:p-4 space-y-2">
                        <button id="processSaleBtn" 
                                class="w-full bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white py-2.5 rounded-lg font-extrabold text-sm shadow-xl hover:shadow-2xl transition-all duration-300 disabled:from-gray-400 disabled:to-gray-500 disabled:cursor-not-allowed disabled:shadow-none transform hover:scale-[1.02] active:scale-[0.98] disabled:transform-none"
                                disabled>
                            <span class="flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Process Sale</span>
                            </span>
                        </button>
                        <button id="clearCartBtn" 
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg font-semibold text-xs transition-all duration-200 border-2 border-gray-200 hover:border-gray-300">
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Cart Overlay -->
<div id="mobileCartOverlay" class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden fade-in" onclick="closeMobileCart()"></div>

<!-- Enhanced Modal -->
<div id="modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 py-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm fade-in" onclick="closeModal()"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-8 z-50 slide-in-right">
            <div class="flex items-center justify-between mb-6">
                <h3 id="modalTitle" class="text-xl sm:text-2xl font-extrabold text-gray-900"></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-gray-100 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="mb-6">
                <p id="modalMessage" class="text-gray-700 whitespace-pre-line text-sm sm:text-base leading-relaxed"></p>
            </div>
            <div class="flex justify-end space-x-3">
                <button id="modalCancelBtn" onclick="closeModal()" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-all font-semibold shadow-sm">
                    Cancel
                </button>
                <button id="modalConfirmBtn" onclick="confirmModalAction()" class="px-5 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 hover:from-pink-600 hover:to-rose-600 text-white rounded-xl transition-all font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let modalAction = null;
let isMobileCartOpen = false;

function showModal(title, message, showCancel = false, onConfirm = null) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('modal').classList.remove('hidden');
    
    const cancelBtn = document.getElementById('modalCancelBtn');
    const confirmBtn = document.getElementById('modalConfirmBtn');
    
    if (showCancel) {
        cancelBtn.style.display = 'block';
        confirmBtn.textContent = 'Confirm';
    } else {
        cancelBtn.style.display = 'none';
        confirmBtn.textContent = 'OK';
    }
    
    modalAction = onConfirm;
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
    modalAction = null;
}

function confirmModalAction() {
    if (modalAction) {
        modalAction();
    }
    closeModal();
}

function toggleMobileCart() {
    const cartSection = document.getElementById('cartSection');
    const overlay = document.getElementById('mobileCartOverlay');
    
    if (isMobileCartOpen) {
        cartSection.classList.add('hidden');
        overlay.classList.add('hidden');
        isMobileCartOpen = false;
    } else {
        cartSection.classList.remove('hidden');
        cartSection.classList.add('fixed', 'inset-y-0', 'right-0', 'w-full', 'sm:w-96', 'z-50', 'shadow-2xl', 'slide-in-right');
        overlay.classList.remove('hidden');
        isMobileCartOpen = true;
    }
}

function closeMobileCart() {
    const cartSection = document.getElementById('cartSection');
    const overlay = document.getElementById('mobileCartOverlay');
    
    cartSection.classList.add('hidden');
    cartSection.classList.remove('fixed', 'inset-y-0', 'right-0', 'w-full', 'sm:w-96', 'z-50', 'shadow-2xl', 'slide-in-right');
    overlay.classList.add('hidden');
    isMobileCartOpen = false;
}

let cart = [];
let products = @json($products);

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateCartDisplay();
    setupEventListeners();
});

function setupEventListeners() {
    // Product cards
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const productId = parseInt(this.dataset.productId);
            const product = products.find(p => p.id === productId);
            if (product && product.current_stock > 0) {
                addToCart(product);
                // Show mobile cart if on mobile
                if (window.innerWidth < 1024 && !isMobileCartOpen) {
                    toggleMobileCart();
                }
            } else {
                showModal('Out of Stock', 'This product is currently out of stock.');
            }
        });
    });

    // Search
    document.getElementById('productSearch').addEventListener('input', filterProducts);
    document.getElementById('categoryFilter').addEventListener('change', filterProducts);

    // Cart calculations
    document.getElementById('cartDiscount').addEventListener('input', updateCartTotals);
    document.getElementById('amountPaid').addEventListener('input', calculateChange);

    // Buttons
    document.getElementById('processSaleBtn').addEventListener('click', processSale);
    document.getElementById('clearCartBtn').addEventListener('click', clearCart);
}

function addToCart(product) {
    const existingItem = cart.find(item => item.inventory_item_id === product.id);
    
    if (existingItem) {
        if (existingItem.quantity < product.current_stock) {
            existingItem.quantity++;
        } else {
            showModal('Insufficient Stock', `Only ${product.current_stock} units available in stock.`);
            return;
        }
    } else {
        cart.push({
            inventory_item_id: product.id,
            item_name: product.name,
            item_sku: product.sku,
            unit_price: parseFloat(product.selling_price),
            quantity: 1,
            discount: 0,
        });
    }
    
    updateCartDisplay();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartDisplay();
}

function updateQuantity(index, change) {
    const item = cart[index];
    const product = products.find(p => p.id === item.inventory_item_id);
    
    const newQuantity = item.quantity + change;
    if (newQuantity < 1) {
        removeFromCart(index);
        return;
    }
    if (newQuantity > product.current_stock) {
        showModal('Insufficient Stock', `Only ${product.current_stock} units available in stock.`);
        return;
    }
    
    item.quantity = newQuantity;
    updateCartDisplay();
}

function updateCartDisplay() {
    const cartItemsDiv = document.getElementById('cartItems');
    const cartBadge = document.getElementById('cartBadge');
    const cartItemCount = document.getElementById('cartItemCount');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = `
            <div class="text-center text-gray-500 py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <p class="font-bold text-gray-600 text-base">Cart is empty</p>
                <p class="text-sm text-gray-400 mt-1">Click on products to add them</p>
            </div>
        `;
        document.getElementById('processSaleBtn').disabled = true;
        cartBadge.classList.add('hidden');
        if (cartItemCount) cartItemCount.classList.add('hidden');
    } else {
        cartItemsDiv.innerHTML = cart.map((item, index) => `
            <div class="bg-white rounded-lg p-3 border-2 border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 mb-2.5">
                <!-- Item Header -->
                <div class="flex justify-between items-start mb-2.5">
                    <div class="flex-1 min-w-0 pr-2">
                        <h4 class="font-bold text-sm text-gray-900 leading-tight mb-1 line-clamp-2">${item.item_name}</h4>
                        <p class="text-xs text-gray-500 font-mono mb-1.5">SKU: ${item.item_sku}</p>
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="text-xs font-semibold text-gray-600">Price:</span>
                            <span class="text-sm font-extrabold bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent">₱${parseFloat(item.unit_price).toFixed(2)}</span>
                        </div>
                    </div>
                    <button onclick="removeFromCart(${index})" class="ml-2 text-red-500 hover:text-red-700 hover:bg-red-50 p-1.5 rounded-lg transition-all flex-shrink-0 transform hover:scale-110 active:scale-95" title="Remove item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Quantity Controls -->
                <div class="flex items-center justify-between pt-2.5 border-t border-gray-100">
                    <div class="flex items-center space-x-2">
                        <label class="text-xs font-semibold text-gray-600">Qty:</label>
                        <div class="flex items-center space-x-1 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-0.5 border-2 border-gray-200">
                            <button onclick="updateQuantity(${index}, -1)" class="w-7 h-7 rounded-md border-2 border-gray-300 hover:bg-white hover:border-pink-400 flex items-center justify-center transition-all font-bold text-gray-700 hover:text-pink-600 transform hover:scale-110 active:scale-95" title="Decrease">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span class="w-10 text-center font-extrabold text-gray-900 text-xs">${item.quantity}</span>
                            <button onclick="updateQuantity(${index}, 1)" class="w-7 h-7 rounded-md border-2 border-gray-300 hover:bg-white hover:border-pink-400 flex items-center justify-center transition-all font-bold text-gray-700 hover:text-pink-600 transform hover:scale-110 active:scale-95" title="Increase">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold text-gray-600 block mb-0.5">Subtotal</span>
                        <span class="font-extrabold text-gray-900 text-sm">₱${(item.unit_price * item.quantity).toFixed(2)}</span>
                    </div>
                </div>
            </div>
        `).join('');
        document.getElementById('processSaleBtn').disabled = false;
        cartBadge.textContent = totalItems;
        cartBadge.classList.remove('hidden');
        if (cartItemCount) {
            cartItemCount.textContent = totalItems;
            cartItemCount.classList.remove('hidden');
        }
    }
    
    updateCartTotals();
}

function updateCartTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
    const total = subtotal - discount;
    
    document.getElementById('cartSubtotal').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('cartTotal').textContent = `₱${total.toFixed(2)}`;
    
    calculateChange();
}

function calculateChange() {
    const total = parseFloat(document.getElementById('cartTotal').textContent.replace('₱', '').replace(',', ''));
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const change = Math.max(0, paid - total);
    
    document.getElementById('changeAmount').textContent = `₱${change.toFixed(2)}`;
}

function filterProducts() {
    const search = document.getElementById('productSearch').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    
    document.querySelectorAll('.product-card').forEach(card => {
        const name = card.dataset.productName.toLowerCase();
        const sku = card.dataset.productSku.toLowerCase();
        const productCategory = card.dataset.productCategory;
        
        const matchesSearch = name.includes(search) || sku.includes(search);
        const matchesCategory = !category || productCategory === category;
        
        card.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
    });
}

function clearCart() {
    showModal('Clear Cart', 'Are you sure you want to clear the cart? All items will be removed.', true, function() {
        cart = [];
        updateCartDisplay();
        document.getElementById('cartDiscount').value = 0;
        document.getElementById('amountPaid').value = '';
        document.getElementById('customerSelect').value = '';
    });
}

function processSale() {
    if (cart.length === 0) {
        showModal('Empty Cart', 'Your cart is empty. Please add products before processing a sale.');
        return;
    }
    
    const subtotal = cart.reduce((sum, item) => sum + (item.unit_price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('cartDiscount').value) || 0;
    const total = subtotal - discount;
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
    
    if (amountPaid < total) {
        showModal('Insufficient Payment', `Amount paid (₱${amountPaid.toFixed(2)}) is less than total amount (₱${total.toFixed(2)}). Please enter the correct amount.`);
        return;
    }
    
    const saleData = {
        items: cart.map(item => ({
            inventory_item_id: item.inventory_item_id,
            quantity: item.quantity,
            unit_price: item.unit_price,
            discount: item.discount || 0,
        })),
        subtotal: subtotal,
        discount: discount,
        tax: 0,
        total_amount: total,
        amount_paid: amountPaid,
        payment_method: document.getElementById('paymentMethod').value,
        client_id: document.getElementById('customerSelect').value || null,
    };
    
    // Disable button during processing
    const btn = document.getElementById('processSaleBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="flex items-center justify-center space-x-2"><svg class="animate-spin w-5 h-5 sm:w-6 sm:h-6" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Processing...</span></span>';
    
    fetch('{{ route("admin.pos.process-sale") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(saleData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Open receipt in new window
            window.open(`{{ url('admin/pos/receipt') }}/${data.sale.id}`, '_blank');
            
            // Clear cart and reset
            cart = [];
            updateCartDisplay();
            document.getElementById('cartDiscount').value = 0;
            document.getElementById('amountPaid').value = '';
            document.getElementById('customerSelect').value = '';
            
            // Close mobile cart if open
            if (isMobileCartOpen) {
                closeMobileCart();
            }
            
            showModal('Success', 'Sale processed successfully! Receipt has been opened in a new window.');
        } else {
            showModal('Error', data.message || 'An error occurred while processing the sale.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('Error', 'An error occurred while processing the sale. Please try again.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<span class="flex items-center justify-center space-x-2"><svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span>Process Sale</span></span>';
    });
}

// Handle window resize
window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024 && isMobileCartOpen) {
        closeMobileCart();
    }
});

// Update cart badge on scroll for mobile
let lastScrollTop = 0;
window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const cartBadge = document.getElementById('cartBadge');
    if (scrollTop > lastScrollTop && scrollTop > 100) {
        cartBadge.classList.add('scale-0');
    } else {
        cartBadge.classList.remove('scale-0');
    }
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
});
</script>

</x-app-layout>
