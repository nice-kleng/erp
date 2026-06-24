<?php

namespace App\Filament\Pos\Pages;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Filament\Facades\Filament;
use Filament\Pages\Page;

class KasirPage extends Page
{
    protected string $view = 'filament.pos.pages.kasir';

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-shopping-cart';
    }

    public string $search = '';

    public ?int $selectedCategoryId = null;

    public array $cart = [];

    public ?int $customerId = null;

    public ?float $discount = 0;

    public bool $showPaymentModal = false;

    public string $paymentMethod = 'cash';

    public ?float $amountPaid = null;

    public bool $showSuccessModal = false;

    public ?string $lastInvoice = null;

    public ?float $lastChange = null;

    public bool $showItemDiscountModal = false;

    public ?int $editingCartIndex = null;

    public ?float $itemDiscountValue = null;

    public function mount(): void
    {
        $this->discount = 0;
    }

    public function getProductsProperty()
    {
        $query = Product::where('store_id', Filament::getTenant()->id)
            ->where('is_active', true)
            ->with('unit', 'variants');

        if ($this->selectedCategoryId) {
            $query->where('category_id', $this->selectedCategoryId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('barcode', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%');
            });
        }

        return $query->get();
    }

    public function getCategoriesProperty()
    {
        return Category::where('store_id', Filament::getTenant()->id)->get();
    }

    public function getCustomersProperty()
    {
        return Customer::where('store_id', Filament::getTenant()->id)
            ->orderBy('name')
            ->get();
    }

    public function getCartSubtotalProperty(): float
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function getCartTotalProperty(): float
    {
        return $this->cart_subtotal - (float) ($this->discount ?? 0);
    }

    public function getCartCountProperty(): int
    {
        return (int) collect($this->cart)->sum('qty');
    }

    public function getChangeDueProperty(): float
    {
        if (! $this->amountPaid) {
            return 0;
        }

        return max(0, (float) $this->amountPaid - $this->cart_total);
    }

    public function addToCart(int $productId, ?int $variantId = null): void
    {
        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        $unitPrice = $product->selling_price;
        $name = $product->name;
        $variantName = null;

        if ($variantId) {
            $variant = ProductVariant::find($variantId);

            if ($variant) {
                $unitPrice = $variant->selling_price ?? $unitPrice;
                $variantName = $variant->name;
            }
        }

        $key = $variantId ? $productId.'-'.$variantId : (string) $productId;

        foreach ($this->cart as $i => $item) {
            if ($item['key'] === $key) {
                $this->cart[$i]['qty']++;
                $qty = (float) $this->cart[$i]['qty'];
                $price = (float) $this->cart[$i]['unit_price'];
                $itemDisc = (float) ($this->cart[$i]['discount'] ?? 0);
                $this->cart[$i]['subtotal'] = ($qty * $price) - $itemDisc;

                return;
            }
        }

        $this->cart[] = [
            'key' => $key,
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'name' => $name,
            'variant_name' => $variantName,
            'unit_price' => $unitPrice,
            'qty' => 1,
            'discount' => 0,
            'subtotal' => $unitPrice,
        ];
    }

    public function quickAddByBarcode(): void
    {
        if (! $this->search) {
            return;
        }

        $product = Product::where('store_id', Filament::getTenant()->id)
            ->where('barcode', $this->search)
            ->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->search = '';
        }
    }

    public function updateQty(int $index, int $delta): void
    {
        if (! isset($this->cart[$index])) {
            return;
        }

        $newQty = (float) $this->cart[$index]['qty'] + $delta;

        if ($newQty <= 0) {
            $this->removeFromCart($index);

            return;
        }

        $this->cart[$index]['qty'] = $newQty;
        $price = (float) $this->cart[$index]['unit_price'];
        $itemDisc = (float) ($this->cart[$index]['discount'] ?? 0);
        $this->cart[$index]['subtotal'] = ($newQty * $price) - $itemDisc;
    }

    public function removeFromCart(int $index): void
    {
        array_splice($this->cart, $index, 1);
    }

    public function openItemDiscount(int $index): void
    {
        if (! isset($this->cart[$index])) {
            return;
        }

        $this->editingCartIndex = $index;
        $this->itemDiscountValue = (float) ($this->cart[$index]['discount'] ?? 0);
        $this->showItemDiscountModal = true;
    }

    public function applyItemDiscount(): void
    {
        if ($this->editingCartIndex === null || ! isset($this->cart[$this->editingCartIndex])) {
            return;
        }

        $this->cart[$this->editingCartIndex]['discount'] = (float) ($this->itemDiscountValue ?? 0);
        $qty = (float) $this->cart[$this->editingCartIndex]['qty'];
        $price = (float) $this->cart[$this->editingCartIndex]['unit_price'];
        $this->cart[$this->editingCartIndex]['subtotal'] = ($qty * $price) - (float) $this->itemDiscountValue;

        $this->showItemDiscountModal = false;
        $this->editingCartIndex = null;
        $this->itemDiscountValue = null;
    }

    public function cancelItemDiscount(): void
    {
        $this->showItemDiscountModal = false;
        $this->editingCartIndex = null;
        $this->itemDiscountValue = null;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->amountPaid = null;
    }

    public function openPaymentModal(): void
    {
        if (empty($this->cart)) {
            return;
        }

        $this->paymentMethod = 'cash';
        $this->amountPaid = null;
        $this->showPaymentModal = true;
    }

    public function processPayment(): void
    {
        $total = $this->cart_total;

        $this->validate([
            'paymentMethod' => 'required|in:cash,transfer,qris,debit,credit_card',
            'amountPaid' => 'required|numeric|min:'.$total,
        ]);

        $storeId = Filament::getTenant()->id;
        $change = (float) ($this->amountPaid ?? 0) - $total;

        $invoiceNumber = $this->generateInvoiceNumber();

        $sale = Sale::create([
            'store_id' => $storeId,
            'user_id' => auth()->id(),
            'customer_id' => $this->customerId ?: null,
            'invoice_number' => $invoiceNumber,
            'subtotal' => $this->cart_subtotal,
            'tax' => 0,
            'discount' => $this->discount ?? 0,
            'total' => $total,
            'payment_method' => $this->paymentMethod,
            'amount_paid' => $this->amountPaid ?? $total,
            'change' => max(0, $change),
            'status' => 'completed',
        ]);

        foreach ($this->cart as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'] ?: null,
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'subtotal' => $item['subtotal'],
            ]);

            StockMovement::create([
                'store_id' => $storeId,
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'] ?: null,
                'type' => 'out',
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'reference_type' => 'sale',
                'reference_id' => $sale->id,
                'description' => 'Penjualan: '.$invoiceNumber,
                'created_by' => auth()->id(),
            ]);
        }

        $this->lastInvoice = $invoiceNumber;
        $this->lastChange = max(0, $change);

        $this->showPaymentModal = false;
        $this->showSuccessModal = true;
    }

    public function resetCart(): void
    {
        $this->cart = [];
        $this->customerId = null;
        $this->discount = 0;
        $this->search = '';
        $this->selectedCategoryId = null;
        $this->showSuccessModal = false;
        $this->lastInvoice = null;
        $this->lastChange = null;
    }

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->format('Ymd').'-';
        $last = Sale::where('invoice_number', 'like', $prefix.'%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last->invoice_number, -4);

            return $prefix.str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix.'0001';
    }

    public static function getNavigationLabel(): string
    {
        return 'POS Kasir';
    }

    public function getTitle(): string
    {
        return 'POS Kasir';
    }
}
