# Modul Produksi (Resep F&B) — Dokumentasi

## 1. Arsitektur Database

### Tables (5)

| Table | Deskripsi |
|-------|-----------|
| `products.type` | Kolom baru: `'raw'` (bahan baku) / `'finished'` (produk jadi) |
| `recipes` | Resep — menghubungkan produk jadi dengan bahan baku |
| `recipe_ingredients` | Takaran bahan baku per resep (qty per 1x produksi) |
| `productions` | Eksekusi produksi — header (nomor, status, qty, cost) |
| `production_ingredients` | Bahan yang digunakan saat produksi (qty_required, qty_used, unit_price) |

### Entity Relationship

```
products (type='raw')
    ↑
recipe_ingredients ──→ recipes ──→ products (type='finished')
    ↑                      ↑
production_ingredients ──→ productions
                                ↓
                          stock_movements (type='out' untuk bahan, 'in' untuk hasil)
```

## 2. Alur Lengkap

### Step 1: Setup Produk

- Produk **bahan baku** → `type = 'raw'` (misal: Kopi Bubuk, Susu UHT, Gula Pasir)
- Produk **jadi** → `type = 'finished'` (misal: Es Kopi Susu, Cafe Latte)
- Setiap bahan baku harus punya `purchase_price` (untuk kalkulasi biaya)

### Step 2: Buat Resep

Menu: **Produksi → Resep → Buat Resep**

```
Resep: Es Kopi Susu        (Produk Jadi)
Hasil Per Resep: 1
──────────────────────────────────
Bahan:
  Kopi Bubuk      20 gr    (type:raw)
  Susu UHT       150 ml    (type:raw)
  Gula Pasir      10 gr    (type:raw)
  Es Batu         50 gr    (type:raw)
```

- Produk **hanya menampilkan** yang `type = 'finished'`
- Bahan **hanya menampilkan** yang `type = 'raw'`
- `qty` = takaran untuk SATU kali produksi sesuai `qty_produced`

### Step 3: Eksekusi Produksi

Menu: **Produksi → Produksi → Buat Produksi**

#### Form

| Field | Sumber Data |
|-------|-------------|
| **Resep** | Select → pilih resep |
| **Produk Jadi** | Auto-fill dari resep (read-only) |
| **Qty Produksi** | Default 1 — jika diubah, semua bahan dikalikan |
| **Status** | Draft / Selesai / Dibatalkan |
| **Tanggal Produksi** | Default hari ini |
| **Bahan Baku** | Auto-fill dari resep × qty produksi |

#### Auto-Fill Bahan

Saat pilih resep → sistem:

1. Load `recipe_ingredients` × `qty_produced`
2. Set `qty_required = ingredient.qty × qty_produced`
3. Set `qty_used = qty_required` (bisa diedit manual jika stok terbatas)
4. Set `unit_price = product.purchase_price` (harga beli terakhir)
5. Set `subtotal = qty_used × unit_price`

### Step 4: Konfirmasi → Stock Movement

Saat status diubah ke **"Selesai"** (`completed`):

#### Yang terjadi di `afterCreate()` / `afterSave()`:

```php
DB::transaction(function () {
    // 1. Untuk setiap bahan baku — KURANGI stok
    StockMovement::create([
        type: 'out',
        qty: qty_used,
        reference: 'production',
        description: 'Produksi: PRD-20260625-XXXX (Es Kopi Susu)',
    ]);

    // 2. Untuk produk jadi — TAMBAH stok
    StockMovement::create([
        type: 'in',
        qty: qty_produced,
        unit_price: total_cost / qty_produced,  // harga pokok produksi
        reference: 'production',
        description: 'Hasil produksi: PRD-20260625-XXXX',
    ]);

    // 3. Hitung total biaya produksi
    production.update(['total_cost' => sum(subtotal semua bahan)]);
});
```

#### Efek di Stok:

| Produk | Sebelum | Movement | Sesudah |
|--------|---------|----------|---------|
| Kopi Bubuk (bahan) | 1000 gr | **OUT** 200 gr | 800 gr |
| Susu UHT (bahan) | 5000 ml | **OUT** 1500 ml | 3500 ml |
| Gula Pasir (bahan) | 2000 gr | **OUT** 100 gr | 1900 gr |
| Es Kopi Susu (jadi) | 0 pcs | **IN** 10 pcs | 10 pcs |

### Step 5: Batal / Hapus Produksi

Jika produksi dihapus (delete) → `booted::deleted()`:

- Semua `StockMovement` dengan `reference_type = 'production'` dan `reference_id` terkait dihapus
- Stok kembali seperti sebelum produksi

## 3. Costing

### Harga Pokok Produksi (HPP)

```
HPP per unit = total_cost / qty_produced
```

Dimana:

```
total_cost = Σ (qty_used × unit_price) untuk semua bahan
unit_price = purchase_price produk bahan baku (bisa diedit manual)
```

### Contoh

| Bahan | qty_used | unit_price | subtotal |
|-------|----------|------------|----------|
| Kopi Bubuk | 200 gr | Rp 50/gr | Rp 10.000 |
| Susu UHT | 1500 ml | Rp 20/ml | Rp 30.000 |
| Gula Pasir | 100 gr | Rp 15/gr | Rp 1.500 |
| **Total Cost** | | | **Rp 41.500** |
| Qty Produksi | 10 pcs | | |
| **HPP per pcs** | | | **Rp 4.150** |

HPP ini tercatat di `StockMovement.unit_price` untuk produk jadi, berguna untuk laporan laba kotor nanti.

## 4. Integrasi dengan Modul Lain

| Modul | Hubungan |
|-------|----------|
| **POS** | Produk jadi (type: finished) bisa dijual di POS |
| **Purchasing** | Bahan baku (type: raw) dibeli via Purchase Order |
| **StockMovement** | Semua pergerakan stok produksi tercatat (audit trail) |
| **Accounting (future)** | HPP produksi bisa dipakai untuk jurnal akuntansi |

## 5. Struktur File

```
app/Models/
  ├── Recipe.php
  ├── RecipeIngredient.php
  ├── Production.php
  └── ProductionIngredient.php

app/Filament/Owner/Resources/
  ├── Recipes/
  │   ├── RecipeResource.php
  │   └── Pages/ (List, Create, Edit)
  └── Productions/
      ├── ProductionResource.php
      └── Pages/ (List, Create, Edit)

database/migrations/
  ├── *_add_type_to_products.php
  ├── *_create_recipes_table.php
  ├── *_create_recipe_ingredients_table.php
  ├── *_create_productions_table.php
  └── *_create_production_ingredients_table.php
```
