<div class="flex gap-5 h-screen p-5">
    {{-- Left: Product Area --}}
    <div class="flex-1 min-w-0 flex flex-col gap-4">
        {{-- Search + Categories --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 space-y-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama produk, barcode, atau SKU..."
                    wire:keydown.enter="quickAddByBarcode"
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900 outline-none transition-all"
                />
                @if($this->search)
                    <button
                        type="button"
                        wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>

            {{-- Category Pills --}}
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin">
                <button
                    type="button"
                    wire:click="$set('selectedCategoryId', null)"
                    class="whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-medium transition-all
                        {{ is_null($selectedCategoryId) ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                >
                    Semua
                </button>
                @foreach($this->categories as $cat)
                    <button
                        type="button"
                        wire:click="$set('selectedCategoryId', {{ $cat->id }})"
                        class="whitespace-nowrap px-4 py-1.5 rounded-full text-xs font-medium transition-all
                            {{ $selectedCategoryId === $cat->id ? 'bg-primary-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}"
                    >
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto -mx-1 px-1">
            @if($this->products->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-gray-400">
                    <svg class="w-16 h-16 mb-3 text-gray-200 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="text-sm font-medium">Tidak ada produk ditemukan</p>
                    <p class="text-xs mt-1">Coba ubah kata kunci atau kategori</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    @foreach($this->products as $product)
                        <div
                            wire:key="prod-{{ $product->id }}"
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-primary-200 dark:hover:border-primary-700 transition-all duration-150 overflow-hidden cursor-pointer group"
                            wire:click="addToCart({{ $product->id }})"
                        >
                            {{-- Product Image Placeholder --}}
                            <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-750 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>

                            {{-- Info --}}
                            <div class="p-2.5">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white truncate leading-tight">
                                    {{ $product->name }}
                                </h4>
                                @if($product->unit)
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ $product->unit->name }}</p>
                                @endif
                                <p class="text-sm font-bold text-primary-600 dark:text-primary-400 mt-1.5">
                                    Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                </p>
                            </div>

                            {{-- Variant Pills (overlay at bottom) --}}
                            @if($product->has_variants && $product->variants->where('is_active', true)->isNotEmpty())
                                <div class="px-2.5 pb-2.5 flex flex-wrap gap-1">
                                    @foreach($product->variants->where('is_active', true) as $variant)
                                        <span
                                            wire:click.stop="addToCart({{ $product->id }}, {{ $variant->id }})"
                                            class="inline-block px-2 py-0.5 text-[10px] font-medium rounded-md bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-800/50 transition-colors cursor-pointer"
                                        >
                                            {{ $variant->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Right: Cart Sidebar --}}
    <div class="w-[440px] flex-shrink-0 flex flex-col bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        {{-- Cart Header --}}
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Keranjang</h3>
            </div>
            @if($this->cart_count > 0)
                <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 text-xs font-bold">
                    {{ $this->cart_count }}
                </span>
            @endif
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto">
            @if(empty($cart))
                <div class="flex flex-col items-center justify-center h-full text-gray-300 dark:text-gray-600 px-5">
                    <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <p class="text-sm font-medium">Keranjang kosong</p>
                    <p class="text-xs mt-1">Klik produk untuk menambahkan</p>
                </div>
            @else
                <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @foreach($cart as $i => $item)
                        <div class="px-5 py-3 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors" wire:key="cart-{{ $item['key'] }}">
                            <div class="flex items-start gap-3">
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item['name'] }}</p>
                                            @if($item['variant_name'])
                                                <p class="text-xs text-gray-400">{{ $item['variant_name'] }}</p>
                                            @endif
                                        </div>
                                        <button
                                            type="button"
                                            wire:click="removeFromCart({{ $i }})"
                                            class="p-0.5 text-gray-300 hover:text-red-400 transition-colors flex-shrink-0"
                                            title="Hapus"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Qty + Price Row --}}
                                    <div class="flex items-center justify-between mt-2">
                                        {{-- Qty Controls --}}
                                        <div class="inline-flex items-center border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                            <button
                                                type="button"
                                                wire:click="updateQty({{ $i }}, -1)"
                                                class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-500 transition-colors text-sm font-bold"
                                            >−</button>
                                            <span class="w-9 h-7 flex items-center justify-center text-sm font-semibold text-gray-900 dark:text-white border-x border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900">
                                                {{ $item['qty'] }}
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="updateQty({{ $i }}, 1)"
                                                class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-500 transition-colors text-sm font-bold"
                                            >+</button>
                                        </div>

                                        {{-- Price / Subtotal --}}
                                        <div class="text-right">
                                            <button
                                                type="button"
                                                wire:click="openItemDiscount({{ $i }})"
                                                class="font-semibold text-sm text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                                                title="Klik untuk diskon"
                                            >
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </button>
                                            @if((float)($item['discount'] ?? 0) > 0)
                                                <div class="text-[11px] text-red-500 font-medium">
                                                    -Rp {{ number_format($item['discount'], 0, ',', '.') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Bottom Section --}}
        <div class="border-t border-gray-100 dark:border-gray-700 px-5 py-4 space-y-3">
            {{-- Customer --}}
            <div class="flex items-center gap-3">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <div class="flex-1">
                    <x-filament::input.select wire:model.live="customerId" class="text-sm">
                        <option value="">Pelanggan Umum</option>
                        @foreach($this->customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </div>
            </div>

            {{-- Totals --}}
            <div class="space-y-1.5">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Rp {{ number_format($this->cart_subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center gap-3">
                    <span class="text-sm text-gray-500 whitespace-nowrap">Diskon</span>
                    <div class="relative w-32">
                        <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-xs text-gray-400">Rp</span>
                        <input
                            type="number"
                            wire:model.live="discount"
                            class="w-full pl-8 pr-2 py-1.5 text-right text-sm border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900 outline-none transition-all"
                            min="0"
                            step="100"
                            placeholder="0"
                        />
                    </div>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-base font-bold text-gray-800 dark:text-gray-200">Total</span>
                    <span class="text-xl font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($this->cart_total, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Pay Button --}}
            <button
                type="button"
                wire:click="openPaymentModal"
                @disabled(empty($cart))
                class="w-full py-3.5 rounded-xl font-bold text-base transition-all duration-150 flex items-center justify-center gap-2
                    {{ empty($cart) ? 'bg-gray-200 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed' : 'bg-primary-600 hover:bg-primary-500 active:bg-primary-700 text-white shadow-sm shadow-primary-200 dark:shadow-primary-900/30' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Bayar Rp {{ number_format($this->cart_total, 0, ',', '.') }}
            </button>
        </div>
    </div>

    {{-- ITEM DISCOUNT MODAL --}}
    @if($showItemDiscountModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" wire:click.self="cancelItemDiscount">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-auto animate-in zoom-in-95 duration-150">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Diskon Item</h3>
                        @if($editingCartIndex !== null && isset($cart[$editingCartIndex]))
                            <p class="text-xs text-gray-500">{{ $cart[$editingCartIndex]['name'] }} @if($cart[$editingCartIndex]['variant_name']) — {{ $cart[$editingCartIndex]['variant_name'] }} @endif</p>
                        @endif
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Jumlah Diskon (Rp)</label>
                    <input
                        type="number"
                        wire:model="itemDiscountValue"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900 outline-none transition-all"
                        placeholder="0"
                        min="0"
                        step="100"
                        autofocus
                    />
                </div>

                <div class="flex gap-2">
                    <button type="button" wire:click="cancelItemDiscount" class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button type="button" wire:click="applyItemDiscount" class="flex-1 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-500 text-white text-sm font-medium transition-colors">
                        Terapkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- PAYMENT MODAL --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" wire:click.self="closePaymentModal">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-md mx-auto animate-in zoom-in-95 duration-150">
                {{-- Header --}}
                <div class="text-center mb-5">
                    <div class="w-14 h-14 rounded-2xl bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pembayaran</h3>
                    <div class="mt-1">
                        <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">Rp {{ number_format($this->cart_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Payment Method Grid --}}
                <div class="mb-5">
                    <label class="block text-xs font-medium text-gray-500 mb-2">Metode Pembayaran</label>
                    <div class="grid grid-cols-3 gap-2">
                        @php
                            $methods = [
                                'cash' => ['Tunai', 'M19 14c1.5-2.5 1.5-5 0-7', 'bg-emerald-50 text-emerald-600 border-emerald-200'],
                                'transfer' => ['Transfer', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'bg-blue-50 text-blue-600 border-blue-200'],
                                'qris' => ['QRIS', 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'bg-violet-50 text-violet-600 border-violet-200'],
                                'debit' => ['Debit', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'bg-amber-50 text-amber-600 border-amber-200'],
                                'credit_card' => ['Kredit', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'bg-rose-50 text-rose-600 border-rose-200'],
                            ];
                        @endphp
                        @foreach($methods as $val => [$label, $path, $colors])
                            @php $selected = $paymentMethod === $val; @endphp
                            <button
                                type="button"
                                wire:click="$set('paymentMethod', '{{ $val }}')"
                                class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all text-xs font-medium
                                    {{ $selected ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' : 'border-gray-100 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:border-gray-200 dark:hover:border-gray-500' }}"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"/>
                                </svg>
                                <span>{{ $label }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Amount Paid --}}
                <div class="mb-5">
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Jumlah Dibayar</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm text-gray-400 font-medium">Rp</span>
                        <input
                            type="number"
                            wire:model.live="amountPaid"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white text-lg font-bold text-right focus:border-primary-400 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-900 outline-none transition-all"
                            placeholder="0"
                            min="{{ $this->cart_total }}"
                            step="100"
                            autofocus
                        />
                    </div>
                    @if($this->change_due > 0)
                        <div class="mt-2 flex items-center justify-between px-3.5 py-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                            <span class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">Kembalian</span>
                            <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($this->change_due, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex gap-2">
                    <button type="button" wire:click="closePaymentModal" class="flex-1 py-3 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button type="button" wire:click="processPayment" class="flex-[2] py-3 rounded-xl bg-primary-600 hover:bg-primary-500 text-white text-sm font-bold transition-colors shadow-sm">
                        Konfirmasi & Bayar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- SUCCESS MODAL --}}
    @if($showSuccessModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 w-full max-w-sm mx-auto text-center animate-in zoom-in-95 duration-150">
                {{-- Success Icon --}}
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Pembayaran Berhasil</h3>

                {{-- Invoice --}}
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                    <p class="text-xs text-gray-500 mb-0.5">No. Invoice</p>
                    <p class="text-base font-bold text-gray-900 dark:text-white tracking-wide">{{ $lastInvoice }}</p>
                </div>

                @if($lastChange > 0)
                    <div class="mt-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 mb-0.5">Kembalian</p>
                        <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($lastChange, 0, ',', '.') }}</p>
                    </div>
                @endif

                <button
                    type="button"
                    wire:click="resetCart"
                    class="mt-6 w-full py-3 rounded-xl font-bold text-white bg-primary-600 hover:bg-primary-500 transition-colors shadow-sm"
                >
                    + Transaksi Baru
                </button>
            </div>
        </div>
    @endif
</div>
