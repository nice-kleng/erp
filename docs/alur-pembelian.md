# Alur Pembelian (PO → GR → AP)

## Overview

```
PO (Purchase Order)       → ng, status: draft
  ↓ kirim ke supplier
PO (status: ordered)
  ↓  barang datang
GR (Goods Receipt)        → Stok + StockMovement, PO status auto-update
  ↓  otomatis
AP (Account Payable)      → Hutang ke supplier tercatat
  ↓  bayar
AP Payments               → Kurangi sisa hutang
```

## Flow Detail

### 1. Purchase Order (PO)
Pesanan ke supplier. User input produk & qty yang dipesan.

**Status PO:**
| Status | Arti | Trigger |
|---|---|---|
| `draft` | Belum dikirim ke supplier | Default saat create |
| `ordered` | Sudah dipesan ke supplier | User ubah manual |
| `partially_received` | Barang datang sebagian | **Auto** saat GR complete |
| `received` | Barang lengkap | **Auto** saat semua qty terpenuhi |
| `cancelled` | Dibatalkan | User ubah manual |

**Form:**
- Informasi Pesanan: supplier, tanggal, notes
- Item Pesanan: product, qty_ordered, unit_price, subtotal (otomatis)
- Ringkasan: subtotal, discount, tax, total

### 2. Goods Receipt (GR)
Mencatat barang yang benar-benar diterima. Bisa beberapa GR untuk 1 PO (partial receiving).

**Saat GR disimpan dengan status `completed`:**

| # | Aksi Otomatis | Detail |
|---|---|---|
| 1 | **StockMovement** | `type=in` per item, reference=goods_receipt |
| 2 | **PO item qty_received** | Direkalkulasi dari semua GR terkait |
| 3 | **PO status** | `partial` jika baru sebagian, `received` jika lunas |
| 4 | **AP** | Auto-create jika belum ada |

**Contoh:**
```
PO: Mi Instan 1 Dus, qty=10
  → GR #1: terima 6 dus → PO status = partially_received, qty_received=6
  → GR #2: terima 4 dus → PO status = received, qty_received=10
```

**Validasi:**
- `qty_received` minimum 1

### 3. Account Payable (AP)
Hutang ke supplier. Auto-create saat GR complete, bisa juga dibuat manual.

**AP Status:** `unpaid` → `partial` → `paid`
- Terisi otomatis saat GR complete (total = sum of GR items)
- Pembayaran dicatat via AP Payments
- Sisa tagihan = total_amount - amount_paid

## Menu di Owner Panel

| Menu | Grup | Urutan |
|---|---|---|
| Purchase Orders | Pembelian | 1 |
| Goods Receipts | Pembelian | 2 |
| Hutang (AP) | Pembelian | 3 |
| Pembayaran Hutang | Pembelian | 4 |

## Catatan Penting

### Yang Perlu Ditambahkan
- [ ] **Delete GR cleanup**: saat GR dihapus, StockMovement & PO qty_received harus di-update
- [ ] **Auto ubah PO ke `ordered`**: bisa via tombol "Kirim PO" dengan validasi
- [ ] **Over-receive validation**: cegah qty_received > sisa qty_ordered di form
- [ ] **Laporan Hutang**: daftar AP jatuh tempo

### Status PO tidak bisa manual ke `received`
User tidak bisa ubah PO status ke `received` atau `partially_received` secara manual — hanya system yang mengubah saat GR di-*complete*.
