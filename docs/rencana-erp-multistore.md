# Rencana Lengkap ERP Multi-store

## Informasi Proyek

- **Teknologi**: Laravel 13, PHP 8.3, SQLite (dev), Tailwind CSS 4
- **Admin Panel**: Filament 3 (3 panel: Super Admin, Owner, POS)
- **Testing**: Pest 4
- **Bahasa**: Bahasa Indonesia
- **Model**: SaaS Multi-tenant (Single Database, owner_id scope)

---

## Panel Structure

| Panel | Path | Untuk |
|-------|------|-------|
| Super Admin | `/superadmin` | Admin pusat kelola semua owner & sistem |
| Owner | `/panel` | Pemilik toko kelola bisnis & semua modul |
| POS Kasir | `/pos` | UI khusus kasir — transaksi cepat, cetak struk thermal |

---

## Modul & Fitur Lengkap

### 1. Master Data
- Manajemen Owner & Store
- User & Roles (Spatie Permission + Filament Shield)
- Kategori Produk (per store)
- Produk & Varian (rasa/ukuran) + Barcode/SKU
- Satuan (pcs, kg, porsi, cup, dll)
- Supplier
- Pelanggan

### 2. Inventory & Stok
- Stok per Store (terpisah antar toko)
- Mutasi Stok (audit trail — semua pergerakan tercatat)
- Transfer Stok antar Store
- Opname Stok (stock adjustment)
- Minimum stock alert

### 3. Purchasing & Hutang (AP)
- Purchase Order (ke supplier)
- Penerimaan Barang (partial — sebagian dari PO)
- Hutang Pembelian — jatuh tempo, pelunasan, sisa tagihan

### 4. Sales / POS
- POS kasir cepat — cari produk, keranjang, bayar
- Multi payment: Tunai, QRIS, Transfer Bank, Debit
- Invoice number otomatis per store
- Piutang (AR) — pelanggan bon, tempo, pelunasan
- Cetak struk thermal (ESC/POS via browser + mike42/escpos-php)

### 5. Promo & Diskon
- Diskon per produk / per kategori
- Promo periode tertentu (tanggal mulai-selesai)
- Diskon per store / global owner

### 6. Produksi & Resep (F&B)
- Bill of Materials (BOM) / Resep — daftar bahan baku + takaran
- Produksi: bahan baku -> proses -> produk jadi
- Otomatis kurangi stok bahan baku & tambah stok produk jadi saat produksi

### 7. Finance & Accounting
- **Chart of Accounts (COA)** — kode akun debet/kredit
- **Buku Kas & Bank** — catat pemasukan/pengeluaran harian per kasir
- **Biaya Operasional** — listrik, gaji, sewa, ATK, dll
- **Jurnal Otomatis** dari transaksi:
  - Penjualan → Debit Kas, Kredit Penjualan + HPP
  - Pembelian → Debit Persediaan, Kredit Hutang/Kas
  - Biaya → Debit Beban, Kredit Kas
  - Penerimaan Piutang → Debit Kas, Kredit Piutang
  - Bayar Hutang → Debit Hutang, Kredit Kas
- **Laporan Keuangan**:
  - Laba Rugi (Profit & Loss)
  - Neraca (Balance Sheet)
  - Buku Besar (General Ledger)
  - Arus Kas

### 8. Notifikasi & Alert
- Stok menipis / habis
- Purchase Order sudah tiba
- Hutang jatuh tempo
- Piutang jatuh tempo

### 9. Subscription (Struktur)
- `plans` — paket langganan (Basic / Pro / Enterprise)
- `owner_subscriptions` — status aktif/nonaktif, batas store/user
- Integrasi payment gateway menyusul

### 10. Laporan & Export
- Laporan Penjualan per store / per hari / per bulan
- Laporan Stop (stok opname)
- Laporan Laba Rugi
- Laporan Hutang & Piutang
- Export Excel (pxlrbt/filament-excel)

---

## Teknologi & Package

| Package | Fungsi |
|---------|--------|
| `laravel/framework` 13.x | Core |
| `filament/filament` | Admin panel (3 panel) |
| `spatie/laravel-permission` | Roles & permissions |
| `bezhansalleh/filament-shield` | UI untuk role management |
| `awcodes/filament-curator` | Upload gambar produk/logo |
| `pxlrbt/filament-excel` | Export Excel |
| `mike42/escpos-php` | Printer thermal POS |

---

## Database (25+ Tables)

### Core / Multi-tenant
- `users`
- `stores`
- `store_user` (pivot)

### Master Data
- `categories`
- `units`
- `products`
- `product_variants`
- `suppliers`
- `customers`

### Inventory
- `stock`
- `stock_movements`
- `stock_transfers`
- `stock_transfer_items`

### Purchasing & AP
- `purchase_orders`
- `purchase_order_items`
- `goods_receipts`
- `goods_receipt_items`

### Sales & AR
- `transactions`
- `transaction_items`
- `transaction_payments`

### Production
- `recipes` (BOM)
- `recipe_ingredients`
- `productions`
- `production_ingredients`

### Finance
- `chart_of_accounts`
- `journals`
- `journal_entries`
- `cash_registers`
- `cash_register_transactions`

### Promo
- `promotions`
- `promotion_items`

### Subscription
- `plans`
- `owner_subscriptions`

---

# Rencana Eksekusi (Step-by-Step)

## Fase 0: Setup Project
1. Install Filament 3 multi-panel
2. Setup Spatie Permission + Filament Shield
3. Konfigurasi multi-tenancy (global scope owner_id)
4. Siapkan model User, migrasi, factory

## Fase 1: Master Data
5. Migration Store, Category, Unit, Product, ProductVariant
6. CRUD Filament untuk master data
7. Migration Supplier, Customer
8. CRUD Supplier & Customer

## Fase 2: Inventory & Purchasing
9. Migration Stock, StockMovement, StockTransfer
10. Purchase Order + Penerimaan Barang
11. Transfer Stok antar store
12. Hutang Pembelian (AP)

## Fase 3: POS & Sales
13. UI POS (Livewire + Tailwind)
14. Transaksi + Multi Payment
15. Cetak struk thermal
16. Piutang (AR)

## Fase 4: Produksi (Resep F&B)
17. Migration Recipe, Production
18. Produksi barang jadi dari bahan baku

## Fase 5: Finance & Accounting
19. Chart of Accounts
20. Buku Kas & Bank
21. Jurnal Otomatis
22. Laporan Laba Rugi + Neraca

## Fase 6: Promo, Notifikasi, Subscription
23. Diskon & Promo
24. Notifikasi & Alert
25. Subscription Plans

## Fase 7: Laporan & Finishing
26. Laporan & Export Excel
27. Dashboard grafik per store & owner
28. Testing Pest
