# SRS — Software Requirements Specification

**Proyek:** Pitou Cafe — Point of Sale (POS) Coffee Shop
**Versi Dokumen:** 1.0
**Stack:** Laravel 13, Tailwind CSS v3, Alpine.js, MySQL (Laragon), DomPDF, Blade Lucide Icons
**Status:** Modul 1–8 (near-complete), sisa Barista Station Page

---

## 1. Introduction

### 1.1 Tujuan Sistem

Pitou Cafe adalah aplikasi berbasis web untuk mengelola operasional harian coffee shop, mulai dari pencatatan pesanan oleh waiter, distribusi order ke dapur (Kitchen) dan bar (Barista), proses pembayaran di kasir, cetak struk, sampai laporan penjualan untuk admin.

### 1.2 Ruang Lingkup

Sistem mencakup:

* Autentikasi & manajemen pengguna berbasis role
* Manajemen menu & meja
* Pembuatan order oleh Waiters
* Antrian station (Kitchen & Barista) dengan notifikasi suara + polling
* Pembayaran kasir dengan logika pecahan Rupiah + kembalian
* Cetak struk thermal 58mm & export invoice PDF (DomPDF)
* Dashboard & laporan penjualan (Chart.js)

Di luar ruang lingkup versi ini: integrasi payment gateway online, inventory/stok bahan baku, multi-outlet, dan aplikasi mobile native.

### 1.3 Definisi Istilah

| Istilah | Penjelasan |
|---|---|
| **Admin** | Mengelola user, menu, dan melihat laporan penjualan |
| **Waiters** | Membuat order pelanggan dari meja |
| **Kitchen** | Menyiapkan item kategori **makanan** |
| **Barista** | Menyiapkan item kategori **minuman** |
| **Kasir** | Memproses pembayaran & mencetak struk |
| **Order** | Satu pesanan aktif yang menempel pada satu meja |
| **Order Item** | Baris item di dalam order (menu + qty), punya `station` |
| **Station** | Tujuan penyiapan item: `kitchen` atau `barista` |
| **Checker** | Tiket cetak berisi daftar item untuk station (dipakai Kitchen/Barista sebagai panduan siapkan pesanan) |
| **paid_at** | Timestamp pembayaran — penanda tunggal transaksi selesai |

---

## 2. Overall Description

Pitou Cafe adalah aplikasi web server-side rendered (Laravel Blade + Alpine.js) yang berjalan di jaringan lokal coffee shop. Setiap role login ke dashboard sesuai perannya dan hanya bisa mengakses halaman yang menjadi tanggung jawabnya.

**Role:**

```
- Admin
- Waiters
- Barista
- Kitchen
- Kasir
```

**Alur inti sistem:**

```
Waiters buat order
        ↓
Order dipecah per kategori
   ↓                ↓
Kitchen (makanan)   Barista (minuman)
   ↓                ↓
Cetak checker → siapkan pesanan
        ↓
Selesai → diinfokan manual (lonceng)
        ↓
        Kasir proses pembayaran
                ↓
        Cetak struk → Meja kosong
```

### 2.1 Matriks Akses Role

| Role | Bisa Akses | Tidak Bisa Akses |
|---|---|---|
| **Admin** | User, Menu, Dashboard, Laporan | — |
| **Waiters** | Buat order, lihat meja | Menu/halaman **Kasir** (pembayaran), manajemen menu |
| **Kasir** | **Order & transaksi** (pembayaran), struk | Station Kitchen/Barista |
| **Kitchen** | Antrian station makanan + cetak checker | Order/transaksi & pembayaran |
| **Barista** | Antrian station minuman + cetak checker | Order/transaksi & pembayaran |

> Ringkasnya: Waiters tidak bisa masuk menu Kasir; Kasir yang pegang order/transaksi; Kitchen & Barista tidak bisa mengakses order/transaksi — mereka **hanya** melihat antrian dan mencetak checker.

**Karakteristik teknis:**

* Real-time antrian station memakai **polling** (bukan websocket) + notifikasi suara
* Desain: brand coklat `#7C4A2D` / brand-light `#A9714B`, background cream `#FAF6F0`, font Inter + Playfair Display
* Aturan brand ketat: semua hero header wajib `bg-gradient-to-br from-brand to-brand-light`

---

## 3. Functional Requirements

### FR-001 Login

**Deskripsi:** Pengguna login dengan email & password, lalu diarahkan ke dashboard sesuai role.

**Actor:** Semua Role.

**Flow**

```
Input Email → Input Password → Klik Login → Validasi → Dashboard sesuai Role
```

**Validation**

* Email wajib
* Password wajib
* Password salah → tampilkan error
* Akun `is_active = false` → tidak bisa login

---

### FR-002 Manajemen User (Admin)

**Deskripsi:** Admin melakukan CRUD user beserta role-nya.

**Actor:** Admin.

**Flow**

```
Buka User Management → Tambah/Edit/Hapus user → Set role → Simpan
```

**Business Rules**

* Admin tidak boleh menghapus akunnya sendiri
* Admin tidak boleh menonaktifkan akunnya sendiri
* Admin terakhir tidak boleh dihapus
* Blok HTML per-role di-hardcode (bukan interpolasi class Alpine dinamis) untuk menghindari purge Tailwind

---

### FR-003 Manajemen Menu (Admin)

**Deskripsi:** Admin mengelola menu, kategori, harga, dan status ketersediaan.

**Actor:** Admin.

**Flow**

```
Buka Menu → Tambah/Edit menu → Pilih kategori → Set harga → Set status → Simpan
```

**Business Rules**

* Kategori menentукan station tujuan (makanan → kitchen, minuman → barista)
* Menu status "Tidak tersedia" tidak bisa dipilih Waiters

---

### FR-004 Manajemen Meja

**Deskripsi:** Sistem mengelola status meja (KOSONG / TERISI).

**Actor:** Sistem (otomatis) + Waiters (baca).

**Business Rules**

* Meja mulai berstatus KOSONG
* Setelah order dibuat → status meja TERISI
* Setelah pembayaran sukses → status meja kembali KOSONG

---

### FR-005 Waiters Membuat Order

**Deskripsi:** Waiters membuat pesanan pelanggan.

**Actor:** Waiters.

**Flow**

```
Klik meja kosong
        ↓
Input nama customer
        ↓
Pilih menu → Tambah quantity
        ↓
Klik Kirim
        ↓
Order tersimpan
        ↓
Muncul di antrian Kitchen melalui polling (item makanan)
        ↓
Muncul di antrian Barista melalui polling (item minuman)
```

**Business Rules**

* Meja harus KOSONG
* Nama customer wajib
* Minimal 1 menu
* Quantity minimal 1
* Setelah order dibuat, status meja menjadi TERISI
* Setiap `order_item` diberi `station` sesuai kategori menu

---

### FR-006 Station Kitchen

**Deskripsi:** Kitchen melihat antrian item makanan dan mencetak checker. Kitchen **tidak** mengubah status apa pun di sistem.

**Actor:** Kitchen.

**Flow**

```
Item baru masuk antrian (polling)
        ↓
Notifikasi suara / lonceng berbunyi
        ↓
Cetak checker (tiket item makanan)
        ↓
Siapkan pesanan
        ↓
Selesai → bunyikan lonceng (manual, tidak dicatat sistem)
```

**Business Rules**

* Hanya menampilkan item dengan `station = 'kitchen'`
* **Tidak ada status `cooking` maupun `ready`** — station hanya melihat antrian & mencetak checker
* Penyelesaian pesanan diinformasikan secara **manual (lonceng)**, tidak ada tracking status di sistem
* Kitchen **tidak bisa** mengakses halaman order/transaksi maupun pembayaran
* Polling menyegarkan antrian secara berkala

---

### FR-007 Station Barista

**Deskripsi:** Mirror dari Kitchen, khusus item minuman.

**Actor:** Barista.

**Flow**

```
Sama seperti FR-006, difilter untuk minuman:
antrian (polling) → lonceng → cetak checker → siapkan → selesai manual
```

**Business Rules**

* Hanya menampilkan item dengan `station = 'barista'`
* Skema warna amber (membedakan dari Kitchen)
* **Tidak ada status `cooking`/`ready`** — hanya melihat antrian & mencetak checker
* Penyelesaian pesanan diinformasikan manual (lonceng)
* Barista **tidak bisa** mengakses halaman order/transaksi maupun pembayaran
* Notifikasi suara + polling sama seperti Kitchen

> Catatan: FR-007 masih outstanding di proyek — halaman ini mirror Kitchen dengan filter `station = 'barista'` dan skema amber.

---

### FR-008 Pembayaran (Kasir)

**Deskripsi:** Kasir memproses pembayaran order aktif.

**Actor:** Kasir.

**Flow**

```
Cari meja
        ↓
Pilih order aktif
        ↓
Input nominal bayar
        ↓
Sistem hitung kembalian (logika pecahan Rupiah)
        ↓
Cetak struk thermal 58mm
        ↓
Order = selesai, Meja = kosong
```

**Business Rules**

* Order harus aktif
* Nominal bayar ≥ total
* Sistem menyarankan pecahan Rupiah (denomination logic) untuk mempercepat input
* Setelah sukses: `paid_at` terisi, order selesai, meja kosong

---

### FR-009 Struk & Invoice

**Deskripsi:** Sistem mencetak struk thermal 58mm dan bisa export invoice PDF.

**Actor:** Kasir / Admin.

**Business Rules**

* Struk dioptimalkan untuk printer thermal 58mm
* Invoice PDF di-generate via DomPDF
* Struk memuat: nama customer, meja, item + qty + harga, total, bayar, kembalian, waktu

---

### FR-010 Dashboard

**Deskripsi:** Menampilkan ringkasan operasional & penjualan real.

**Actor:** Admin.

**Business Rules**

* Data dashboard dihitung dari transaksi dengan `paid_at` terisi (penanda tunggal transaksi selesai)
* Order yang belum dibayar tidak dihitung sebagai pendapatan

---

### FR-011 Laporan Penjualan

**Deskripsi:** Laporan penjualan dengan grafik.

**Actor:** Admin.

**Business Rules**

* Grafik batang memakai Chart.js
* Batang tertinggi (peak) di-highlight warna gold
* Bisa difilter berdasarkan rentang tanggal
* Bisa export (PDF DomPDF)

---

### FR-012 Toggle Menu Habis

**Deskripsi:** Waiters menandai menu tersedia / tidak tersedia (mis. stok habis) secara cepat, tanpa harus lewat manajemen menu Admin.

**Actor:** Waiters.

**Flow**

```
Buka daftar menu
        ↓
Toggle status menu (tersedia ⇄ habis)
        ↓
Tersimpan
        ↓
Menu "habis" tidak bisa dipilih saat buat order
```

**Business Rules**

* Waiters bisa mengubah status ketersediaan menu
* Menu "habis" tidak bisa dipilih Waiters saat buat order (lihat BR-002)
* Kitchen & Barista **hanya memberi informasi** (mis. lisan/manual), **tidak bisa** mengubah status menu di sistem
* Admin tetap punya akses penuh atas menu via FR-003

---

### FR-013 Monitoring Order Waiters

**Deskripsi:** Waiters melihat daftar order aktif untuk memantau pesanan yang sedang berjalan.

**Actor:** Waiters.

**Flow**

```
Buka halaman order aktif
        ↓
Lihat daftar order (meja, customer, item)
        ↓
Pantau (read-only)
```

**Business Rules**

* Hanya menampilkan order yang masih aktif (belum dibayar)
* Bersifat **read-only** — tidak ada tombol ubah status
* Penyiapan pesanan tetap manual di station (Waiters tidak mengubah status apa pun)

---

## 4. Business Rules (Ringkasan)

```
BR-001  Satu meja hanya boleh punya satu order aktif.

BR-002  Menu status "Tidak tersedia" tidak boleh dipilih Waiters.

BR-003  Order dipisah otomatis berdasarkan kategori:
          Minuman → Barista
          Makanan → Kitchen

BR-004  Setelah pembayaran berhasil:
          Order  = selesai
          Meja   = kosong

BR-005  paid_at adalah satu-satunya penanda transaksi selesai
        (dipakai konsisten oleh dashboard & laporan).

BR-006  Guard User Management:
          - tidak bisa hapus diri sendiri
          - tidak bisa nonaktifkan diri sendiri
          - tidak bisa hapus admin terakhir

BR-007  Nominal pembayaran harus ≥ total order.

BR-008  Setiap role hanya boleh mengakses halaman miliknya:
          - Waiters tidak bisa mengakses menu/halaman Kasir
          - Kasir bisa mengakses order & transaksi (pembayaran)
          - Barista & Kitchen tidak bisa mengakses order/transaksi

BR-009  Station (Kitchen & Barista) TIDAK mengubah status item.
          Tidak ada status cooking maupun ready.
          Station hanya melihat antrian & mencetak checker.

BR-010  Penyelesaian pesanan diinformasikan secara manual (lonceng),
          tidak dicatat sebagai status di dalam sistem.

BR-011  Satu order hanya bisa dibayar satu kali.

BR-012  Meja yang berstatus TERISI tidak bisa dibuatkan order baru.

BR-013  Kasir hanya bisa memproses order yang masih aktif.
```

---

## 5. Non Functional Requirements

**Performance**

```
Item order baru muncul di layar Kitchen & Barista dalam beberapa detik
melalui polling berkala + notifikasi suara.
```

**Security**

```
Akses berbasis role (middleware). Role tidak boleh membuka halaman role lain.
- Waiters tidak bisa akses halaman Kasir/pembayaran.
- Kasir yang memegang akses order & transaksi.
- Kitchen & Barista tidak bisa akses order/transaksi.
Akun tidak aktif tidak bisa login.
```

**Availability**

```
Sistem berjalan selama jam operasional coffee shop di jaringan lokal (Laragon).
```

**Responsive**

```
Target utama: tablet minimal 10 inch (station & waiter).
Desktop minimal 1366px (kasir & admin).
```

**Maintainability / Konsistensi UI**

```
Hero header wajib bg-gradient-to-br from-brand to-brand-light (aturan brand ketat).
Modal hapus global memakai Alpine store ($store.deleteModal).
Blok HTML per-role di-hardcode untuk menghindari Tailwind purge.
```

---

## 6. Database Mapping

| Tabel | Dipakai oleh |
|---|---|
| `users` | FR-001, FR-002 |
| `categories` | FR-003 |
| `menus` | FR-003, FR-005 |
| `tables` | FR-004, FR-005, FR-008 |
| `orders` | FR-005, FR-008, FR-010, FR-011 |
| `order_items` | FR-005, FR-006, FR-007 (punya kolom `station`) |
| `payments` | FR-008, FR-009, FR-010, FR-011 (punya `paid_at`) |

**Kolom kunci:**

* `menus.is_available` — kontrol BR-002
* `menus.category_id` → menentukan `order_items.station` (BR-003)
* `order_items` — tidak menyimpan status masak (tidak ada `cooking`/`ready`); cukup simpan `station`
* `tables.status` — `kosong | terisi`
* `orders.status` — `active | paid`
* `payments.paid_at` — BR-005

---

## 7. Route / Endpoint Mapping

> Catatan: Pitou Cafe adalah aplikasi Laravel Blade (server-side), jadi ini named route, bukan REST API murni.

```
Auth
  GET  /login
  POST /login
  POST /logout

Admin
  RESOURCE /users            (FR-002)
  RESOURCE /menus            (FR-003)
  GET      /dashboard        (FR-010)
  GET      /reports          (FR-011)
  GET      /reports/export   (FR-011)

Waiters
  GET   /waiter                      (daftar meja)
  POST  /orders                      (FR-005)
  PATCH /menus/{id}/availability     (FR-012, toggle menu habis)
  GET   /waiter/orders               (FR-013, monitoring order aktif)

Station
  GET   /kitchen                     (FR-006, antrian polling)
  GET   /barista                     (FR-007, antrian polling)
  GET   /orders/{id}/checker/kitchen (FR-006, cetak checker makanan)
  GET   /orders/{id}/checker/barista (FR-007, cetak checker minuman)

Kasir
  GET  /cashier              (FR-008)
  POST /payments             (FR-008)
  GET  /orders/{id}/receipt  (FR-009, struk 58mm)
  GET  /orders/{id}/invoice  (FR-009, PDF DomPDF)
```

---

## 8. UI Mapping

| FR | Halaman UI |
|---|---|
| FR-001 | Login Page |
| FR-002 | User Management Page |
| FR-003 | Menu Management Page |
| FR-005 | Waiter Dashboard → Input Order Page |
| FR-006 | Kitchen Station Page |
| FR-007 | Barista Station Page (amber) |
| FR-008 | Cashier / Payment Page |
| FR-009 | Receipt (58mm) + Invoice PDF |
| FR-010 | Admin Dashboard |
| FR-011 | Sales Report Page (Chart.js) |
| FR-012 | Toggle ketersediaan menu (di Waiter / Order Page) |
| FR-013 | Waiter Active Orders Page (read-only) |

---

## 9. Acceptance Criteria

### FR-001 Login
- ✅ User bisa login dengan kredensial valid
- ✅ Diarahkan ke dashboard sesuai role
- ✅ Password salah menampilkan error
- ✅ Akun nonaktif ditolak

### FR-002 Manajemen User
- ✅ Admin bisa CRUD user
- ✅ Tidak bisa hapus / nonaktifkan diri sendiri
- ✅ Admin terakhir tidak bisa dihapus

### FR-003 Manajemen Menu
- ✅ Admin bisa CRUD menu & set kategori
- ✅ Status "Tidak tersedia" menyembunyikan menu dari Waiters

### FR-005 Waiters Membuat Order
- ✅ Waiters dapat memilih meja KOSONG
- ✅ Nama customer wajib diisi
- ✅ Order tersimpan
- ✅ Kitchen menerima item makanan
- ✅ Barista menerima item minuman
- ✅ Meja berubah menjadi TERISI

### FR-006 Kitchen Station
- ✅ Hanya item makanan yang tampil di antrian
- ✅ Notifikasi suara/lonceng berbunyi saat item baru
- ✅ Checker bisa dicetak
- ✅ Tidak ada status cooking/ready (selesai manual via lonceng)
- ✅ Kitchen tidak bisa membuka halaman order/transaksi

### FR-007 Barista Station
- ✅ Hanya item minuman yang tampil di antrian
- ✅ Skema warna amber
- ✅ Checker bisa dicetak
- ✅ Perilaku sama dengan Kitchen (suara, polling, cetak checker, selesai manual)
- ✅ Barista tidak bisa membuka halaman order/transaksi

### FR-008 Pembayaran
- ✅ Kasir bisa cari meja & pilih order aktif
- ✅ Hanya order aktif yang bisa diproses; order yang sudah dibayar tidak bisa dibayar lagi
- ✅ Nominal bayar ≥ total
- ✅ Kembalian dihitung otomatis
- ✅ Struk tercetak
- ✅ Order selesai & meja kembali kosong

### FR-010 Dashboard
- ✅ Angka dihitung hanya dari transaksi ber-`paid_at`

### FR-011 Laporan
- ✅ Grafik Chart.js tampil
- ✅ Batang peak di-highlight gold
- ✅ Filter tanggal berfungsi
- ✅ Export berjalan

### FR-012 Toggle Menu Habis
- ✅ Waiters bisa mengubah status menu jadi tersedia/habis
- ✅ Menu "habis" tidak bisa dipilih saat buat order
- ✅ Kitchen & Barista tidak bisa mengubah status menu

### FR-013 Monitoring Order Waiters
- ✅ Waiters bisa melihat daftar order aktif
- ✅ Tampilan read-only, tidak ada tombol ubah status
- ✅ Hanya order aktif (belum dibayar) yang tampil

---
Implementasikan Modul 2 sesuai dokumentasi proyek dan desain Figma yang saya lampirkan.

Gunakan layout yang sudah ada.
Ikuti spacing, typography, warna, icon, dan hierarchy pada Figma semaksimal mungkin.

Jangan mengubah business rule, migration, atau model.

> Kalau semua checklist Acceptance Criteria lolos, fitur dianggap **selesai**.
