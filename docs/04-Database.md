# Database Design — Pitou Cafe POS

**Versi:** 1.0
**Terkait:** `03-ERD.md`
**DBMS:** MySQL (Laragon) — konvensi Laravel (id bigint, `created_at`/`updated_at`)

> Catatan uang: kolom nominal ditulis `decimal(12,2)` mengikuti spesifikasi. Karena Rupiah tidak punya sen, `bigint`/`unsignedBigInteger` juga valid sebagai alternatif — pilih salah satu dan konsisten di seluruh tabel.

---

## Master Tables

### `users`

| Field | Type | Constraint | Keterangan |
|---|---|---|---|
| id | bigint | PK, auto increment | |
| name | varchar(100) | NOT NULL | |
| email | varchar(150) | UNIQUE, NOT NULL | Dipakai untuk login |
| password | varchar(255) | NOT NULL | Hash bcrypt |
| role | enum | NOT NULL | `admin`, `waiters`, `kitchen`, `barista`, `kasir` |
| is_active | boolean | NOT NULL, default `true` | Akun nonaktif tidak bisa login (FR-001) |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |

### `roles` *(opsional — hanya jika tidak pakai enum di `users`)*

| Field | Type | Constraint | Keterangan |
|---|---|---|---|
| id | bigint | PK | |
| name | varchar(50) | UNIQUE, NOT NULL | |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |

### `categories`

| Field | Type | Constraint | Keterangan |
|---|---|---|---|
| id | bigint | PK | |
| name | varchar(100) | NOT NULL | mis. "Makanan", "Minuman" |
| station | enum | NOT NULL | `kitchen` / `barista` — sumber routing BR-003 |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |

### `menus`

| Field | Type | Constraint | Keterangan |
|---|---|---|---|
| id | bigint | PK | |
| category_id | bigint | FK → `categories.id`, NOT NULL | |
| name | varchar(150) | NOT NULL | |
| description | text | nullable | Deskripsi menu untuk halaman menu |
| image | varchar(255) | nullable | Path/URL gambar menu |
| price | decimal(12,2) | NOT NULL | |
| is_available | boolean | NOT NULL, default `true` | Toggle habis oleh Waiters (FR-012) |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |

### `tables`

| Field | Type | Constraint | Keterangan |
|---|---|---|---|
| id | bigint | PK | |
| name | varchar(50) | NOT NULL | mis. "Meja 1" |
| status | enum | NOT NULL, default `kosong` | `kosong` / `terisi` (FR-004) |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |

---

## Transaction Tables

### `orders`

| Field | Type | Constraint | Keterangan |
|---|---|---|---|
| id | bigint | PK | |
| order_number | varchar(20) | UNIQUE, NOT NULL | Nomor order untuk struk, mis. `ORD-20260707-0001` (bukan ID database) |
| table_id | bigint | FK → `tables.id`, NOT NULL | |
| customer_name | varchar(100) | NOT NULL | Wajib (FR-005) |
| total | decimal(12,2) | NOT NULL, default 0 | Total order |
| status | enum | NOT NULL, default `active` | `active` / `paid` |
| created_by | bigint | FK → `users.id`, NOT NULL | Waiters pembuat order |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |

### `order_items`

| Field | Type | Constraint | Keterangan |
|---|---|---|---|
| id | bigint | PK | |
| order_id | bigint | FK → `orders.id`, NOT NULL | |
| menu_id | bigint | FK → `menus.id`, NOT NULL | |
| qty | integer | NOT NULL, min 1 | |
| price | decimal(12,2) | NOT NULL | Harga saat order (snapshot) |
| subtotal | decimal(12,2) | NOT NULL | `qty × price` |
| station | enum | NOT NULL | `kitchen` / `barista` (disalin dari `categories.station`) |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |

### `payments`

| Field | Type | Constraint | Keterangan |
|---|---|---|---|
| id | bigint | PK | |
| order_id | bigint | FK → `orders.id`, UNIQUE, NOT NULL | UNIQUE = 1 order 1 pembayaran (BR-011) |
| amount_paid | decimal(12,2) | NOT NULL | Nominal dibayar (≥ `orders.total`, BR-007) |
| change | decimal(12,2) | NOT NULL, default 0 | Kembalian |
| payment_method | enum | NOT NULL, default `cash` | `cash` (siap diperluas ke `qris`) |
| received_by | bigint | FK → `users.id`, NOT NULL | Kasir yang menerima pembayaran |
| paid_at | timestamp | NOT NULL | Penanda transaksi selesai (BR-005) |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |

---

## Enum Reference

| Tabel.Kolom | Nilai |
|---|---|
| `users.role` | `admin`, `waiters`, `kitchen`, `barista`, `kasir` |
| `categories.station` | `kitchen`, `barista` |
| `order_items.station` | `kitchen`, `barista` |
| `tables.status` | `kosong`, `terisi` |
| `orders.status` | `active`, `paid` |
| `payments.payment_method` | `cash` (default) — siap diperluas ke `qris` |

---

## Foreign Key & onDelete

| FK | Referensi | Saran onDelete |
|---|---|---|
| `menus.category_id` | `categories.id` | `RESTRICT` (jangan hapus kategori yang masih dipakai) |
| `orders.table_id` | `tables.id` | `RESTRICT` |
| `orders.created_by` | `users.id` | `RESTRICT` |
| `order_items.order_id` | `orders.id` | `CASCADE` (item ikut terhapus bila order dihapus) |
| `order_items.menu_id` | `menus.id` | `RESTRICT` |
| `payments.order_id` | `orders.id` | `CASCADE` |
| `payments.received_by` | `users.id` | `RESTRICT` |

---

## Index yang Disarankan

| Tabel | Index | Alasan |
|---|---|---|
| `users` | `email` (unique) | Login |
| `menus` | `category_id` | Join kategori, filter menu |
| `menus` | `is_available` | Filter menu tersedia (FR-005/FR-012) |
| `orders` | `table_id` | Cari order per meja (kasir) |
| `orders` | `status` | Filter order aktif (FR-013, kasir) |
| `orders` | `order_number` (unique) | Nomor struk, pencarian order |
| `orders` | `created_by` | Riwayat per waiter |
| `order_items` | `order_id` | Ambil item per order |
| `order_items` | `station` | Antrian per station (FR-006/FR-007 polling) |
| `payments` | `order_id` (unique) | Enforce BR-011 |
| `payments` | `received_by` | Rekap pembayaran per kasir |
| `payments` | `paid_at` | Dashboard & laporan (FR-010/FR-011) |

---

## Aturan Integritas (didukung skema)

* **BR-011** — 1 order 1 pembayaran → `payments.order_id` **UNIQUE**.
* **BR-005** — transaksi selesai ditandai `payments.paid_at` (dan `orders.status = 'paid'`).
* **BR-001 / BR-012** — 1 meja hanya 1 order aktif → divalidasi di aplikasi (cek `orders` dengan `table_id = ? AND status = 'active'` sebelum membuat order baru). Bisa diperkuat dengan partial/unique index bila DBMS mendukung.
* **BR-003** — routing station → `order_items.station` disalin dari `categories.station` saat order dibuat.
* **`order_number`** — di-generate saat order dibuat, format `ORD-YYYYMMDD-####` (mis. `ORD-20260707-0001`), counter reset per hari. Nomor ini yang dipakai di struk & checker, bukan `id` database.
* **`payments.received_by`** — diisi otomatis dari kasir yang login saat memproses pembayaran (FR-008).

---

## Perubahan Additive — Penyempurnaan Modul 6 (disetujui user, 2026-07-08)

> Ditambahkan via migration BARU; migration lama TIDAK diubah.

### `checker_prints` (tabel baru)

| Field | Type | Constraint | Keterangan |
|---|---|---|---|
| id | bigint | PK | |
| order_id | bigint | FK → `orders.id`, CASCADE | |
| station | varchar(20) | NOT NULL | `kitchen` / `barista` — varchar (bukan enum) agar station baru tidak butuh ALTER |
| printed_at | timestamp | NOT NULL | Waktu checker dicetak |
| created_at / updated_at | timestamp | nullable | |

**Constraint kunci:** `UNIQUE(order_id, station)` — **BR-014**: checker hanya bisa dicetak SATU KALI per station per order; ditegakkan atomik oleh DB (kebal race/refresh).

**Relasi:** `orders 1 — N checker_prints` (maksimal 1 baris per station). Model: `App\Models\CheckerPrint`; relasi `Order::checkerPrints()`.

### `tables.is_vip` (kolom baru)

| Field | Type | Constraint | Keterangan |
|---|---|---|---|
| is_vip | boolean | NOT NULL, default `false` | **BR-015**: order meja VIP diprioritaskan di antrian station (VIP dulu → FIFO). Diatur Admin via form meja |
