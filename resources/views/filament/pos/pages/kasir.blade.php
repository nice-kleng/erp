<div class="flex gap-6 h-screen p-6">
    {{-- Left: Product Area --}}
    <div class="flex-1 min-w-0 flex flex-col gap-6">
        {{-- Search + Categories --}}
        <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-2xl rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-white/50 dark:border-slate-800/50 p-5 space-y-4 transition-all duration-300">
            <div class="flex items-center gap-4">
                <a
                    href="{{ filament()->getUrl() }}"
                    class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-primary-500 dark:hover:text-primary-400 hover:bg-slate-100 dark:hover:bg-slate-700 shadow-sm border border-slate-200/60 dark:border-slate-700 transition-all duration-300 active:scale-95 flex-shrink-0"
                    title="Kembali ke Dashboard"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="relative group flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-transform duration-300 group-focus-within:scale-110 group-focus-within:text-primary-500">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama produk, barcode, atau SKU..."
                        wire:keydown.enter="quickAddByBarcode"
                        class="w-full pl-12 pr-12 py-3.5 rounded-2xl border-0 bg-slate-100/50 dark:bg-slate-950/50 text-base text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900 focus:ring-4 focus:ring-primary-500/20 shadow-inner transition-all duration-300"
                    />
                    @if($this->search)
                        <button
                            type="button"
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-rose-500 transition-colors"
                        >
                            <div class="p-1 rounded-full bg-slate-200/50 dark:bg-slate-800/50 hover:bg-rose-100 dark:hover:bg-rose-900/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>
                        </button>
                    @endif
                </div>

                <button
                    type="button"
                    x-data="{ isDark: document.documentElement.classList.contains('dark') }"
                    @click="isDark = !isDark; localStorage.setItem('theme', isDark ? 'dark' : 'light'); document.documentElement.classList.toggle('dark', isDark)"
                    class="p-3.5 rounded-2xl bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-primary-500 dark:hover:text-primary-400 shadow-sm border border-slate-200 dark:border-slate-700 transition-all duration-300 active:scale-95 flex-shrink-0"
                    title="Toggle Tema"
                >
                    <svg x-show="!isDark" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="isDark" x-cloak style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
            </div>

            {{-- Category Pills --}}
            <div class="flex gap-2.5 overflow-x-auto pb-2 pt-1 scrollbar-hide hide-scroll-bar" style="-ms-overflow-style: none; scrollbar-width: none;">
                <button
                    type="button"
                    wire:click="$set('selectedCategoryId', null)"
                    class="whitespace-nowrap px-5 py-2 rounded-2xl text-sm font-bold tracking-wide transition-all duration-300 active:scale-95
                        {{ is_null($selectedCategoryId) ? 'bg-slate-900 dark:bg-primary-600 text-white shadow-lg shadow-slate-900/20 dark:shadow-primary-600/30 translate-y-[-2px]' : 'bg-white/80 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 shadow-sm hover:shadow-md border border-slate-100 dark:border-transparent' }}"
                >
                    Semua
                </button>
                @foreach($this->categories as $cat)
                    <button
                        type="button"
                        wire:click="$set('selectedCategoryId', {{ $cat->id }})"
                        class="whitespace-nowrap px-5 py-2 rounded-2xl text-sm font-bold tracking-wide transition-all duration-300 active:scale-95
                            {{ $selectedCategoryId === $cat->id ? 'bg-slate-900 dark:bg-primary-600 text-white shadow-lg shadow-slate-900/20 dark:shadow-primary-600/30 translate-y-[-2px]' : 'bg-white/80 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 shadow-sm hover:shadow-md border border-slate-100 dark:border-transparent' }}"
                    >
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Product Grid --}}
        <div class="flex-1 overflow-y-auto -mx-1 px-1 pb-4 scrollbar-hide" style="-ms-overflow-style: none; scrollbar-width: none;">
            @if($this->products->isEmpty())
                <div class="flex flex-col items-center justify-center h-full text-slate-400">
                    <div class="w-24 h-24 mb-6 rounded-[2rem] bg-slate-100 dark:bg-slate-800/50 flex items-center justify-center shadow-inner">
                        <svg class="w-10 h-10 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <p class="text-base font-bold text-slate-700 dark:text-slate-300">Tidak ada produk ditemukan</p>
                    <p class="text-sm mt-1.5 opacity-70">Coba ubah kata kunci pencarian atau kategori</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($this->products as $product)
                        <div
                            wire:key="prod-{{ $product->id }}"
                            class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-[1.5rem] shadow-[0_4px_20px_rgb(0,0,0,0.03)] dark:shadow-[0_4px_20px_rgb(0,0,0,0.1)] border border-slate-100 dark:border-slate-800 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] dark:hover:shadow-[0_8px_30px_rgb(0,0,0,0.3)] hover:-translate-y-1 hover:border-primary-200 dark:hover:border-primary-900 transition-all duration-300 overflow-hidden cursor-pointer group flex flex-col"
                            wire:click="addToCart({{ $product->id }})"
                        >
                            {{-- Product Image Placeholder (Premium Mesh Gradient) --}}
                            <div class="aspect-[4/3] w-full relative overflow-hidden bg-slate-100 dark:bg-slate-800">
                                <div class="absolute inset-0 opacity-80 group-hover:opacity-100 transition-opacity duration-500 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-indigo-200 via-slate-100 to-rose-100 dark:from-indigo-900/50 dark:via-slate-800 dark:to-rose-900/30"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-400/50 dark:text-slate-500/50 group-hover:scale-110 group-hover:text-primary-500/60 transition-all duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="p-3.5 flex-1 flex flex-col justify-between">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-tight group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2">
                                        {{ $product->name }}
                                    </h4>
                                    @if($product->unit)
                                        <p class="text-[11px] font-medium text-slate-400 dark:text-slate-500 mt-1 uppercase tracking-wider">{{ $product->unit->name }}</p>
                                    @endif
                                </div>
                                <div class="mt-3 flex items-center justify-between">
                                    <p class="text-[15px] font-black text-primary-600 dark:text-white tracking-tight">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </p>
                                    <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-primary-500 group-hover:text-white transition-colors duration-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Variant Pills --}}
                            @if($product->has_variants && $product->variants->where('is_active', true)->isNotEmpty())
                                <div class="px-3.5 pb-3.5 pt-0 flex flex-wrap gap-1.5">
                                    @foreach($product->variants->where('is_active', true) as $variant)
                                        <button
                                            type="button"
                                            wire:click.stop="addToCart({{ $product->id }}, {{ $variant->id }})"
                                            class="inline-block px-2.5 py-1 text-[10px] font-bold tracking-wide rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-primary-500 hover:text-white dark:hover:bg-primary-500 transition-all duration-200 active:scale-95"
                                        >
                                            {{ $variant->name }}
                                        </button>
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
    <div class="w-[440px] flex-shrink-0 flex flex-col bg-white/70 dark:bg-slate-900/70 backdrop-blur-2xl rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-[0_8px_30px_rgb(0,0,0,0.2)] border border-white/50 dark:border-slate-800/50 overflow-hidden relative">
        {{-- Glassmorphic Edge Highlight --}}
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white dark:via-slate-700 to-transparent opacity-50"></div>

        {{-- Cart Header --}}
        <div class="px-6 py-5 border-b border-slate-100/50 dark:border-slate-800/50 flex items-center justify-between bg-white/30 dark:bg-slate-900/30">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-slate-900 dark:bg-primary-600 shadow-md shadow-slate-900/20 dark:shadow-primary-600/30 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                </div>
                <h3 class="font-black text-slate-800 dark:text-slate-100 text-lg tracking-tight">Keranjang</h3>
            </div>
            @if($this->cart_count > 0)
                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-slate-900 dark:bg-primary-900/40 text-white dark:text-primary-400 text-sm font-bold animate-in zoom-in duration-300">
                    {{ $this->cart_count }} Item
                </span>
            @endif
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto scrollbar-hide" style="-ms-overflow-style: none; scrollbar-width: none;">
            @if(empty($cart))
                <div class="flex flex-col items-center justify-center h-full text-slate-300 dark:text-slate-600 px-6">
                    <div class="w-24 h-24 mb-5 rounded-full border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center">
                        <svg class="w-10 h-10 text-slate-200 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                    </div>
                    <p class="text-base font-bold text-slate-400 dark:text-slate-500">Keranjang masih kosong</p>
                    <p class="text-sm mt-1 opacity-75">Klik produk di samping untuk menambahkan</p>
                </div>
            @else
                <div class="divide-y divide-slate-100/50 dark:divide-slate-800/50 px-2">
                    @foreach($cart as $i => $item)
                        <div class="p-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors rounded-2xl my-1 group animate-in slide-in-from-right-4 duration-300" wire:key="cart-{{ $item['key'] }}">
                            <div class="flex items-start gap-4">
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-[15px] font-bold text-slate-900 dark:text-white truncate">{{ $item['name'] }}</p>
                                            @if($item['variant_name'])
                                                <p class="text-xs font-semibold text-slate-400 mt-0.5">{{ $item['variant_name'] }}</p>
                                            @endif
                                        </div>
                                        <button
                                            type="button"
                                            wire:click="removeFromCart({{ $i }})"
                                            class="p-1.5 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-all flex-shrink-0 opacity-0 group-hover:opacity-100"
                                            title="Hapus"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Qty + Price Row --}}
                                    <div class="flex items-center justify-between mt-3">
                                        {{-- Qty Controls --}}
                                        <div class="inline-flex items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-sm">
                                            <button
                                                type="button"
                                                wire:click="updateQty({{ $i }}, -1)"
                                                class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-lg font-medium active:bg-slate-100"
                                            >−</button>
                                            <span class="w-10 h-8 flex items-center justify-center text-sm font-bold text-slate-900 dark:text-white border-x border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                                {{ $item['qty'] }}
                                            </span>
                                            <button
                                                type="button"
                                                wire:click="updateQty({{ $i }}, 1)"
                                                class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-lg font-medium active:bg-slate-100"
                                            >+</button>
                                        </div>

                                        {{-- Price / Subtotal --}}
                                        <div class="text-right">
                                            <button
                                                type="button"
                                                wire:click="openItemDiscount({{ $i }})"
                                                class="font-black text-[15px] text-slate-900 dark:text-white hover:text-primary-500 dark:hover:text-primary-400 transition-colors decoration-dashed decoration-slate-300 underline-offset-4 hover:underline"
                                                title="Ubah Diskon Item"
                                            >
                                                <span class="dark:text-white">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                                            </button>
                                            @if((float)($item['discount'] ?? 0) > 0)
                                                <div class="text-[11px] font-bold text-rose-500 bg-rose-50 dark:bg-rose-500/10 inline-block px-1.5 py-0.5 rounded mt-0.5">
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
        <div class="border-t border-slate-100/50 dark:border-slate-800/50 bg-white/40 dark:bg-slate-900/40 px-6 py-5 space-y-4">
            {{-- Customer --}}
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <x-filament::input.select wire:model.live="customerId" class="text-sm dark:bg-slate-800 dark:text-slate-100 dark:border-slate-600 border-slate-200 shadow-sm focus:ring-primary-500">
                        <option value="">Pelanggan Umum</option>
                        @foreach($this->customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </x-filament::input.select>
                </div>
            </div>

            {{-- Totals --}}
            <div class="space-y-2.5 bg-slate-50/50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/50">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-500 font-medium">Subtotal</span>
                    <span class="text-slate-800 dark:text-slate-200 font-bold">Rp {{ number_format($this->cart_subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center gap-3">
                    <span class="text-sm text-slate-500 font-medium whitespace-nowrap">Diskon Global</span>
                    <div class="relative w-36">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-rose-400">Rp</span>
                        <input
                            type="number"
                            wire:model.live="discount"
                            class="w-full pl-9 pr-3 py-1.5 text-right text-sm font-bold border-0 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 rounded-xl bg-white dark:bg-slate-900 text-rose-500 focus:ring-2 focus:ring-inset focus:ring-rose-500 outline-none transition-all shadow-sm"
                            min="0"
                            step="100"
                            placeholder="0"
                        />
                    </div>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-slate-200/60 dark:border-slate-700/60">
                    <span class="text-base font-black text-slate-800 dark:text-slate-200">Total Tagihan</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white">
                        Rp {{ number_format($this->cart_total, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Pay Button --}}
            <button
                type="button"
                wire:click="openPaymentModal"
                @disabled(empty($cart))
                class="w-full py-4 rounded-2xl font-black text-lg transition-all duration-300 flex items-center justify-center gap-2 group relative overflow-hidden
                    {{ empty($cart) ? 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 cursor-not-allowed border border-slate-200 dark:border-slate-700' : 'bg-slate-900 hover:bg-slate-800 dark:bg-primary-600 dark:hover:bg-primary-500 text-white shadow-xl shadow-slate-900/20 dark:shadow-primary-600/30 hover:-translate-y-0.5 active:translate-y-0' }}"
            >
                @if(!empty($cart))
                    <div class="absolute inset-0 bg-white/10 dark:bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out"></div>
                @endif
                <span class="relative flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    BAYAR SEKARANG
                </span>
            </button>
        </div>
    </div>

    {{-- ITEM DISCOUNT MODAL --}}
    @if($showItemDiscountModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 dark:bg-slate-900/60 backdrop-blur-md transition-all duration-300" wire:click.self="cancelItemDiscount">
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_50px_rgb(0,0,0,0.1)] dark:shadow-[0_20px_50px_rgb(0,0,0,0.3)] border border-white/50 dark:border-slate-700/50 p-7 w-full max-w-sm mx-auto animate-in zoom-in-95 fade-in duration-200">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-100 to-primary-100 dark:from-indigo-900/40 dark:to-primary-900/40 flex items-center justify-center shadow-inner">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-lg text-slate-900 dark:text-white">Diskon Item</h3>
                        @if($editingCartIndex !== null && isset($cart[$editingCartIndex]))
                            <p class="text-sm font-medium text-slate-500 line-clamp-1">{{ $cart[$editingCartIndex]['name'] }} @if($cart[$editingCartIndex]['variant_name']) — {{ $cart[$editingCartIndex]['variant_name'] }} @endif</p>
                        @endif
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jumlah Diskon (Rp)</label>
                    <input
                        type="number"
                        wire:model="itemDiscountValue"
                        class="w-full px-4 py-3.5 rounded-2xl border-0 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 bg-slate-50 dark:bg-slate-800 text-lg font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-inset focus:ring-primary-500 shadow-sm transition-all"
                        placeholder="0"
                        min="0"
                        step="100"
                        autofocus
                    />
                </div>

                <div class="flex gap-3">
                    <button type="button" wire:click="cancelItemDiscount" class="flex-1 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-bold active:scale-95 duration-200">
                        Batal
                    </button>
                    <button type="button" wire:click="applyItemDiscount" class="flex-1 py-3.5 rounded-xl bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-500 hover:to-primary-400 text-white shadow-lg shadow-primary-500/30 text-sm font-bold transition-all active:scale-95 duration-200">
                        Terapkan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- PAYMENT MODAL --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 dark:bg-slate-950/80 backdrop-blur-md transition-all duration-300" wire:click.self="closePaymentModal">
            <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl rounded-[2.5rem] shadow-[0_20px_50px_rgb(0,0,0,0.1)] dark:shadow-[0_20px_50px_rgb(0,0,0,0.4)] border border-white/50 dark:border-slate-700/50 p-8 w-full max-w-md mx-auto animate-in zoom-in-95 fade-in duration-200">
                {{-- Header --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-[1.5rem] bg-gradient-to-br from-indigo-100 to-primary-100 dark:from-indigo-900/40 dark:to-primary-900/40 flex items-center justify-center mx-auto mb-4 shadow-inner">
                        <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white">Pembayaran</h3>
                    <div class="mt-2">
                        <span class="text-3xl font-black text-slate-900 dark:text-white">Rp {{ number_format($this->cart_total, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Payment Method Grid --}}
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-3">Pilih Metode</label>
                    <div class="grid grid-cols-3 gap-3">
                        @php
                            $methods = [
                                'cash' => ['Tunai', 'M19 14c1.5-2.5 1.5-5 0-7', 'from-emerald-400 to-emerald-500'],
                                'transfer' => ['Transfer', 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'from-blue-400 to-blue-500'],
                                'qris' => ['QRIS', 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z', 'from-violet-400 to-violet-500'],
                                'debit' => ['Debit', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'from-amber-400 to-amber-500'],
                                'credit_card' => ['Kredit', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'from-rose-400 to-rose-500'],
                                'credit' => ['Bon', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'from-orange-400 to-orange-500'],
                            ];
                        @endphp
                        @foreach($methods as $val => [$label, $path, $gradient])
                            @php $selected = $paymentMethod === $val; @endphp
                            <button
                                type="button"
                                wire:click="$set('paymentMethod', '{{ $val }}')"
                                class="flex flex-col items-center justify-center gap-2 p-3.5 rounded-2xl border-2 transition-all duration-300 active:scale-95 relative overflow-hidden group
                                    {{ $selected ? 'border-transparent text-white shadow-lg' : 'border-slate-100 dark:border-slate-700/50 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}"
                            >
                                @if($selected)
                                    <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }} opacity-100"></div>
                                @endif
                                <svg class="w-6 h-6 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $path }}"/>
                                </svg>
                                <span class="text-[11px] font-bold tracking-wide relative z-10 uppercase">{{ $label }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Amount Paid --}}
                <div class="mb-7">
                    @if($paymentMethod === 'credit')
                        <div class="px-5 py-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800/30 rounded-2xl text-center">
                            <p class="text-sm font-bold text-orange-600 dark:text-orange-400">Pembayaran Bon/Kredit</p>
                            <p class="text-xs text-orange-500 dark:text-orange-400 mt-1">Piutang akan dicatat dan ditagih nanti</p>
                            @if(!$customerId)
                                <p class="text-xs font-bold text-rose-500 mt-2">⚠️ Pilih pelanggan terlebih dahulu di keranjang</p>
                            @endif
                        </div>
                    @else
                        <label class="block text-sm font-bold text-slate-600 dark:text-slate-400 mb-2">Jumlah Dibayar (Rp)</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-lg text-slate-400 font-bold group-focus-within:text-primary-500 transition-colors">Rp</span>
                            <input
                                type="number"
                                wire:model.live="amountPaid"
                                class="w-full pl-12 pr-4 py-4 rounded-2xl border-0 ring-1 ring-inset ring-slate-200 dark:ring-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-2xl font-black text-right focus:ring-2 focus:ring-inset focus:ring-primary-500 outline-none transition-all shadow-inner"
                                placeholder="0"
                                min="{{ $this->cart_total }}"
                                step="100"
                                autofocus
                            />
                        </div>
                        @if($this->change_due > 0)
                            <div class="mt-3 flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-emerald-500/10 to-emerald-500/5 border border-emerald-500/20 rounded-2xl animate-in fade-in slide-in-from-top-2">
                                <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Kembalian</span>
                                <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($this->change_due, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex gap-3">
                    <button type="button" wire:click="closePaymentModal" class="flex-1 py-4 rounded-2xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-sm font-bold active:scale-95 duration-200">
                        Batal
                    </button>
                    <button type="button" wire:click="processPayment" class="flex-[2] py-4 rounded-2xl bg-slate-900 hover:bg-slate-800 dark:bg-primary-600 dark:hover:bg-primary-500 text-white shadow-xl shadow-slate-900/20 dark:shadow-primary-600/30 text-base font-black transition-all active:scale-95 duration-200">
                        KONFIRMASI
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- SUCCESS MODAL --}}
    @if($showSuccessModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
            <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl rounded-[2.5rem] shadow-[0_20px_50px_rgb(0,0,0,0.2)] border border-white/50 dark:border-slate-700/50 p-10 w-full max-w-sm mx-auto text-center animate-in zoom-in-95 fade-in slide-in-from-bottom-4 duration-300 relative overflow-hidden">
                {{-- Decorative background glow --}}
                <div class="absolute inset-0 bg-gradient-to-b from-emerald-500/10 to-transparent"></div>

                {{-- Success Icon --}}
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-500 shadow-lg shadow-emerald-500/30 flex items-center justify-center mx-auto mb-6 relative z-10 animate-bounce">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h3 class="text-2xl font-black text-slate-900 dark:text-white mb-2 relative z-10">Pembayaran Berhasil!</h3>
                <p class="text-sm font-medium text-slate-500 relative z-10 mb-6">Transaksi telah disimpan ke sistem.</p>

                {{-- Invoice Info --}}
                <div class="bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-700/50 relative z-10 mb-6">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">No. Invoice</p>
                    <p class="text-lg font-black text-slate-800 dark:text-slate-200 tracking-wider">{{ $lastInvoice }}</p>
                </div>

                @if($lastChange > 0)
                    <div class="bg-gradient-to-r from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-900/10 p-4 rounded-2xl border border-emerald-100 dark:border-emerald-800/30 relative z-10 mb-6">
                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-500 uppercase tracking-widest mb-1">Kembalian</p>
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($lastChange, 0, ',', '.') }}</p>
                    </div>
                @endif

                <button
                    type="button"
                    wire:click="resetCart"
                    class="w-full py-4 rounded-2xl font-black text-white bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 transition-all shadow-xl hover:-translate-y-1 active:translate-y-0 relative z-10"
                >
                    Transaksi Baru
                </button>
            </div>
        </div>
    @endif
</div>
