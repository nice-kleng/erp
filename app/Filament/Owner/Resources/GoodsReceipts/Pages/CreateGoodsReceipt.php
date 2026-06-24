<?php

namespace App\Filament\Owner\Resources\GoodsReceipts\Pages;

use App\Filament\Owner\Resources\GoodsReceipts\GoodsReceiptResource;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateGoodsReceipt extends CreateRecord
{
    protected static string $resource = GoodsReceiptResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['store_id'] = Filament::getTenant()->id;
        $data['created_by'] = auth()->id();

        return $data;
    }

    public function getAvailablePoItems(): array
    {
        $poId = $this->data['purchase_order_id'] ?? 0;

        return PurchaseOrderItem::where('purchase_order_id', $poId)
            ->whereColumn('qty_received', '<', 'qty_ordered')
            ->with('product', 'productVariant')
            ->get()
            ->mapWithKeys(fn ($item) => [
                $item->id => $item->product->name
                    .($item->productVariant ? ' - '.$item->productVariant->name : '')
                    .' (Qty: '.$item->qty_ordered.', Sisa: '.($item->qty_ordered - $item->qty_received).')',
            ])
            ->toArray();
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $record->refresh();

        if ($record->status !== 'completed') {
            return;
        }

        DB::transaction(function () use ($record) {
            $this->syncGoodsReceipt($record);
            $this->upsertAccountPayable($record);
        });
    }

    private function syncGoodsReceipt(GoodsReceipt $record): void
    {
        $po = $record->purchaseOrder;

        foreach ($record->items as $grItem) {
            StockMovement::create([
                'store_id' => $record->store_id,
                'product_id' => $grItem->product_id,
                'product_variant_id' => $grItem->product_variant_id,
                'type' => 'in',
                'qty' => $grItem->qty_received,
                'unit_price' => $grItem->unit_price,
                'reference_type' => 'goods_receipt',
                'reference_id' => $record->id,
                'description' => 'Penerimaan dari PO: '.$po->order_number,
                'created_by' => auth()->id(),
            ]);
        }

        $this->recalculatePurchaseOrder($po);
    }

    private function recalculatePurchaseOrder($po): void
    {
        $po->load('items.goodsReceiptItems');

        foreach ($po->items as $poItem) {
            $totalReceived = $poItem->goodsReceiptItems->sum('qty_received');
            $poItem->update(['qty_received' => $totalReceived]);
        }

        $po->load('items');
        $totalOrdered = $po->items->sum('qty_ordered');
        $totalReceived = $po->items->sum('qty_received');

        match (true) {
            $totalReceived >= $totalOrdered => $po->update(['status' => 'received']),
            $totalReceived > 0 => $po->update(['status' => 'partially_received']),
            default => null,
        };
    }

    private function upsertAccountPayable(GoodsReceipt $record): void
    {
        $grSubtotal = $record->items->sum(fn ($item) => $item->qty_received * $item->unit_price);
        $po = $record->purchaseOrder;
        $poSubtotal = (float) $po->subtotal;
        $poTotal = (float) ($poSubtotal - $po->discount + $po->tax);

        $totalAmount = $poSubtotal > 0
            ? round(($grSubtotal / $poSubtotal) * $poTotal)
            : $grSubtotal;

        $record->accountPayable()->create([
            'store_id' => $record->store_id,
            'supplier_id' => $po->supplier_id,
            'ap_number' => 'AP-'.now()->format('Ymd').'-'.Str::upper(Str::random(4)),
            'total_amount' => $totalAmount,
            'amount_paid' => 0,
            'due_date' => now()->addDays(30),
            'status' => 'unpaid',
            'created_by' => auth()->id(),
        ]);
    }
}
