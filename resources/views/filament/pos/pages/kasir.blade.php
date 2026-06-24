<?php use function Filament\Support\get_component_url; ?>

<div class="flex gap-4 h-full">
    {{-- Left Column: Products --}}
    <div class="flex-1 min-w-0 flex flex-col gap-4">
        {{-- Search & Categories --}}
        <div class="flex gap-3 items-start">
            <div class="flex-1">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari produk (nama / barcode / SKU)..."
                        wire:keydown.enter="quickAddByBarcode"
                    />
                </x-filament::input.wrapper>
            </div>
            <div class="w-48">
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.live="selectedCategoryId">
                        <option value="">Semua Kategori</option>
                        @foreach($this->categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto">
            <div class="grid grid-cols-3 xl:grid-cols-4 gap-3">
                @forelse($this->products as $product)
                    <button
                        type="button"
                        wire:click="addToCart({{ $product->id }})"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-3 text-left hover:shadow-md hover:border-primary-300 transition-all text-sm"
                    >
                        <div class="font-semibold text-gray-900 dark:text-white truncate">
                            {{ $product->name }}
                        </div>
                        @if($product->unit)
                            <div class="text-xs text-gray-500 mt-0.5">
                                Satuan: {{ $product->unit->name }}
                            </div>
                        @endif
                        <div class="text-primary-600 dark:text-primary-400 font-bold mt-2 text-base">
                            Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                        </div>
                        @if($product->has_variants && $product->variants->isNotEmpty())
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($product->variants->where('is_active', true) as $variant)
                                    <span
                                        wire:click.stop="addToCart({{ $product->id }}, {{ $variant->id }})"
                                        class="inline-block px-2 py-0.5 text-xs rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-primary-100 dark:hover:bg-primary-900"
                                    >
                                        {{ $variant->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </button>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400">
                        Tidak ada produk ditemukan
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right Column: Cart --}}
    <div class="w-[420px] flex flex-col gap-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 h-full">
        <h3 class="font-semibold text-gray-800 dark:text-gray-200">
            🛒 Keranjang Belanja
            @if($this->cart_count > 0)
                <span class="text-xs text-gray-400 font-normal">({{ $this->cart_count }} item)</span>
            @endif
        </h3>

        <div class="flex-1 overflow-y-auto min-h-0">
            @if(empty($cart))
                <div class="text-center text-gray-400 py-8 text-sm">
                    Belum ada item
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-400 text-xs uppercase border-b dark:border-gray-700">
                        <tr>
                            <th class="py-1 pr-2">Produk</th>
                            <th class="py-1 px-2 text-center w-20">Qty</th>
                            <th class="py-1 px-2 text-right w-24">Subtotal</th>
                            <th class="py-1 pl-2 w-8"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart as $i => $item)
                            <tr class="border-b dark:border-gray-700/50" wire:key="cart-{{ $item['key'] }}">
                                <td class="py-2 pr-2">
                                    <div class="font-medium text-gray-900 dark:text-white truncate">
                                        {{ $item['name'] }}
                                    </div>
                                    @if($item['variant_name'])
                                        <div class="text-xs text-gray-500">{{ $item['variant_name'] }}</div>
                                    @endif
                                    <div class="text-xs text-gray-400">
                                        Rp {{ number_format($item['unit_price'], 0, ',', '.') }}
                                    </div>
                                    @if((float)($item['discount'] ?? 0) > 0)
                                        <div class="text-xs text-danger-500">
                                            Diskon: -Rp {{ number_format($item['discount'], 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <button
                                            type="button"
                                            wire:click="updateQty({{ $i }}, -1)"
                                            class="w-6 h-6 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 text-xs font-bold"
                                        >−</button>
                                        <span class="w-8 text-center font-medium text-sm">{{ $item['qty'] }}</span>
                                        <button
                                            type="button"
                                            wire:click="updateQty({{ $i }}, 1)"
                                            class="w-6 h-6 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 text-xs font-bold"
                                        >+</button>
                                    </div>
                                </td>
                                <td class="py-2 px-2 text-right">
                                    <button
                                        type="button"
                                        wire:click="openItemDiscount({{ $i }})"
                                        class="hover:text-primary-600 transition-colors"
                                    >
                                        <span class="font-medium">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </span>
                                    </button>
                                </td>
                                <td class="py-2 pl-2 text-center">
                                    <button
                                        type="button"
                                        wire:click="removeFromCart({{ $i }})"
                                        class="text-gray-400 hover:text-danger-500 transition-colors text-lg leading-none"
                                    >&times;</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Customer --}}
        <div>
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="customerId">
                    <option value="">Pelanggan Umum</option>
                    @foreach($this->customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>

        {{-- Totals --}}
        <div class="space-y-1.5 text-sm border-t dark:border-gray-700 pt-3">
            <div class="flex justify-between text-gray-500">
                <span>Subtotal</span>
                <span>Rp {{ number_format($this->cart_subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center gap-2">
                <span class="text-gray-500 whitespace-nowrap">Diskon</span>
                <x-filament::input
                    type="number"
                    wire:model.live="discount"
                    class="w-28 text-right text-sm"
                    min="0"
                    step="100"
                />
            </div>
            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white pt-1 border-t dark:border-gray-700">
                <span>Total</span>
                <span class="text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($this->cart_total, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Action Buttons --}}
        <button
            type="button"
            wire:click="openPaymentModal"
            @disabled(empty($cart))
            class="w-full py-3 rounded-xl font-semibold text-white text-lg transition-all
                {{ empty($cart) ? 'bg-gray-300 dark:bg-gray-600 cursor-not-allowed' : 'bg-primary-600 hover:bg-primary-500 active:bg-primary-700' }}"
        >
            💳 Bayar Rp {{ number_format($this->cart_total, 0, ',', '.') }}
        </button>
    </div>

    {{-- Item Discount Modal --}}
    @if($showItemDiscountModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="cancelItemDiscount">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-96 max-w-full mx-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Diskon Item</h3>
                @if($editingCartIndex !== null && isset($cart[$editingCartIndex]))
                    <div class="mb-3 text-sm text-gray-500">
                        {{ $cart[$editingCartIndex]['name'] }}
                        @if($cart[$editingCartIndex]['variant_name'])
                            — {{ $cart[$editingCartIndex]['variant_name'] }}
                        @endif
                    </div>
                    <div class="mb-4">
                        <x-filament::input
                            type="number"
                            wire:model="itemDiscountValue"
                            placeholder="Jumlah diskon (Rp)"
                            min="0"
                            step="100"
                        />
                    </div>
                @endif
                <div class="flex gap-2 justify-end">
                    <x-filament::button color="gray" wire:click="cancelItemDiscount">
                        Batal
                    </x-filament::button>
                    <x-filament::button wire:click="applyItemDiscount">
                        Terapkan
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif

    {{-- Payment Modal --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" wire:click.self="closePaymentModal">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-[420px] max-w-full mx-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4 text-lg">Pembayaran</h3>

                {{-- Total --}}
                <div class="text-center mb-4">
                    <div class="text-sm text-gray-500">Total Belanja</div>
                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($this->cart_total, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metode Pembayaran</label>
                    <x-filament::input.select wire:model.live="paymentMethod">
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                        <option value="debit">Kartu Debit</option>
                        <option value="credit_card">Kartu Kredit</option>
                    </x-filament::input.select>
                </div>

                {{-- Amount Paid --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Dibayar</label>
                    <x-filament::input
                        type="number"
                        wire:model.live="amountPaid"
                        placeholder="Masukkan jumlah..."
                        min="{{ $this->cart_total }}"
                        step="100"
                    />
                    @if($this->change_due > 0)
                        <div class="mt-2 text-sm text-success-600 dark:text-success-400 font-medium">
                            Kembalian: Rp {{ number_format($this->change_due, 0, ',', '.') }}
                        </div>
                    @endif
                </div>

                <div class="flex gap-2 justify-end">
                    <x-filament::button color="gray" wire:click="showPaymentModal = false">
                        Batal
                    </x-filament::button>
                    <x-filament::button wire:click="processPayment" color="success" size="lg">
                        Konfirmasi Pembayaran
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif

    {{-- Success Modal --}}
    @if($showSuccessModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-8 w-[420px] max-w-full mx-4 text-center">
                <div class="text-5xl mb-4">✅</div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2 text-lg">Pembayaran Berhasil</h3>
                <div class="text-sm text-gray-500 mb-1">No. Invoice</div>
                <div class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $lastInvoice }}</div>
                @if($lastChange > 0)
                    <div class="text-sm text-gray-500 mt-3">
                        Kembalian
                    </div>
                    <div class="text-2xl font-bold text-success-600 dark:text-success-400">
                        Rp {{ number_format($lastChange, 0, ',', '.') }}
                    </div>
                @endif
                <button
                    type="button"
                    wire:click="resetCart"
                    class="mt-6 w-full py-3 rounded-xl font-semibold text-white bg-primary-600 hover:bg-primary-500 transition-all"
                >
                    Transaksi Baru
                </button>
            </div>
        </div>
    @endif
</div>
