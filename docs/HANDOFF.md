# HANDOFF — Pitou Cafe POS

**Proyek:** Pitou Cafe — Point of Sale (POS) Coffee Shop
**Versi handoff:** setelah Modul 12 (Laporan Penjualan, FR-011) selesai — **SELURUH FR SRS (FR-001 s.d. FR-013) SELESAI**
**Tujuan dokumen:** konteks lengkap agar sesi/agen berikutnya (Claude Code) bisa lanjut tanpa mengulang analisis.

> **Urutan sumber kebenaran (disepakati bersama user):**
> 1. Kondisi kode aktual → 2. `02-SRS.md` → 3. `04-Database.md` → 4. `03-ERD.md` → 5. `01-PRD.md` (saat ini TIDAK ada di repo) → 6. `HANDOFF.md` → 7. `00-PROJECT_CONTEXT.md`.
> Abaikan bagian dokumen yang bertentangan dengan kondisi kode aktual.

---

## 1. Stack & Environment

| Item | Nilai |
|---|---|
| Framework | Laravel 13 |
| View | Blade (server-side rendered) |
| CSS | Tailwind CSS v3 |
| JS | Alpine.js |
| DB | MySQL via Laragon (Windows) |
| PDF | DomPDF (dipakai nanti di FR-009/FR-011) |
| Ikon | Blade Lucide Icons (`mallardduck/blade-lucide-icons`) — dipakai sebagai `<x-lucide-*>` |

**Larangan stack:** TIDAK memakai Livewire maupun Inertia. Semua interaktivitas via Alpine.js + form submit biasa.

---

## 2. Progress Project

| Tahap | Status |
|---|---|
| Setup Project | ✅ Selesai |
| **Modul 1** — Auth, Login, Role Middleware, Layout, Dashboard (placeholder) | ✅ Selesai |
| **Modul 2** — User Management (FR-002) | ✅ Selesai |
| **Modul 3** — Manajemen Menu & Kategori (FR-003) | ✅ DONE (lihat §13) |
| **Modul 4** — Manajemen Meja (FR-004) | ✅ DONE (lihat §14) |
| **Modul 5 Tahap 1** — Flow Order Waiters (FR-005) | ✅ DONE (lihat §15) |
| **Modul 5 Tahap 2** — FR-012 (Menu/Stok) + FR-013 (Pesanan Aktif) | ✅ DONE (lihat §16) |
| **Modul 6** — Station Kitchen (FR-006) | ✅ DONE (lihat §17) |
| **Modul 6 Penyempurnaan** — Checker sekali cetak (BR-014) + VIP (BR-015) | ✅ DONE (lihat §18) |
| **Modul 6 Partial Refresh** — Polling 3s tanpa `location.reload()` | ✅ DONE (lihat §19) |
| **Modul 7** — Station Barista (FR-007) + refactor `StationController` | ✅ DONE (lihat §20) |
| **Modul 8** — Pembayaran Kasir (FR-008) + Struk 58mm | ✅ DONE (lihat §21) |
| **Modul 8 Penyempurnaan** — Auto Print Struk + Pajak Dinamis (BR-016) | ✅ DONE (lihat §22) |
| **Modul 8 Penyempurnaan 2** — Flow QRIS Terpisah (BR-017) | ✅ DONE (lihat §23) |
| **Modul 9** — Dashboard Admin (FR-010, data asli + Chart.js) | ✅ DONE (lihat §24) |
| **Modul 11** — Riwayat Transaksi + Invoice PDF (FR-009) | ✅ DONE (lihat §25) |
| **Modul 12** — Laporan Penjualan (FR-011) | ✅ **DONE (lihat §26)** |
| **SELURUH FR SRS SELESAI** — tersisa opsional: Modul Settings penuh (`pengaturan.png`) & polishing | 🎉 |

Dashboard admin (`admin/dashboard.blade.php`) saat ini masih **placeholder** dengan angka dummy — akan diisi data asli di modul Dashboard (FR-010) & Reports (FR-011).

---

## 3. Fitur yang Sudah Selesai

### Modul 1
- Login berbasis email + password (FR-001).
- Middleware `role:{role}` — pembatasan akses per role.
- Redirect pasca-login ke dashboard sesuai role via `User::dashboardRoute()`.
- Layout utama `layouts.app` + komponen `<x-sidebar />` & `<x-navbar />`.
- Dashboard admin placeholder.

### Modul 2 — User Management (FR-002)
- CRUD user penuh (index, create, store, edit, update, destroy).
- Search (nama/email) + filter Role + filter Status + pagination (10/hal), semua state kebawa via `withQueryString()`.
- Kartu statistik: Total User (+N baru 7 hari terakhir), Admin, Kasir, Waiters.
- Badge role (hardcode per-role, purge-safe).
- Toggle aktif/non-aktif via route PATCH terpisah.
- Modal hapus global `$store.deleteModal` (Alpine store).
- Flash message success/error (`<x-flash />`).
- Guard business rule BR-006 di UI **dan** server-side.

---

## 4. Struktur Folder (relevan)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── UserController.php            [BARU - Modul 2]
│   │   ├── DashboardController.php       (Modul 1)
│   │   ├── WaiterController.php          (Modul 1)
│   │   ├── KitchenController.php         (Modul 1)
│   │   ├── BaristaController.php         (Modul 1)
│   │   └── CashierController.php         (Modul 1)
│   └── Requests/
│       ├── StoreUserRequest.php          [BARU - Modul 2]
│       └── UpdateUserRequest.php         [BARU - Modul 2]
└── Models/
    └── User.php                          (Modul 1 - TIDAK diubah)

resources/views/
├── layouts/
│   └── app.blade.php                     [DIUBAH - sisip <x-flash/> & <x-delete-modal/>]
├── components/
│   ├── sidebar.blade.php                 [DIUBAH - aktifkan link User Management]
│   ├── navbar.blade.php                  (Modul 1)
│   ├── delete-modal.blade.php            [BARU - Modul 2, global]
│   ├── flash.blade.php                   [BARU - Modul 2, global]
│   └── role-badge.blade.php              [BARU - Modul 2, global]
└── admin/
    ├── dashboard.blade.php               (Modul 1 - placeholder)
    └── users/                            [BARU - Modul 2]
        ├── index.blade.php
        ├── create.blade.php
        ├── edit.blade.php
        └── partials/
            └── form.blade.php

routes/
└── web.php                               [DIUBAH - route user di group role:admin]
```

---

## 5. Daftar File: Dibuat & Diubah (Modul 2)

### Dibuat (baru)
| File | Fungsi |
|---|---|
| `app/Http/Controllers/UserController.php` | Resource controller + `toggleStatus()` |
| `app/Http/Requests/StoreUserRequest.php` | Validasi create (password wajib) |
| `app/Http/Requests/UpdateUserRequest.php` | Validasi update (email unique-ignore-self, password nullable) |
| `resources/views/components/delete-modal.blade.php` | Modal hapus global (`$store.deleteModal`) |
| `resources/views/components/flash.blade.php` | Render flash `success`/`error` |
| `resources/views/components/role-badge.blade.php` | Badge role hardcode per-role |
| `resources/views/admin/users/index.blade.php` | Halaman daftar user |
| `resources/views/admin/users/create.blade.php` | Halaman tambah user |
| `resources/views/admin/users/edit.blade.php` | Halaman edit user |
| `resources/views/admin/users/partials/form.blade.php` | Form dipakai bersama create & edit |

### Diubah
| File | Perubahan |
|---|---|
| `routes/web.php` | Tambah `Route::resource('users')` (except `show`, names `admin.users`) + `admin.users.toggle-status`, di dalam group `role:admin`. Import `UserController`. |
| `resources/views/layouts/app.blade.php` | Sisip `<x-flash />` di dalam `<main>` sebelum `@yield('content')`; sisip `<x-delete-modal />` sebelum `@stack('scripts')`. |
| `resources/views/components/sidebar.blade.php` | Link "User Management" (blok admin) dari `href="#"` → `route('admin.users.index')` + active state `request()->routeIs('admin.users.*')`. Desain/ikon/label tidak diubah. |

### Diverifikasi tapi TIDAK diubah
| File | Alasan |
|---|---|
| `app/Models/User.php` | `$fillable` & `casts` sudah lengkap (`is_active` boolean, `password` hashed), sudah ada helper `isRole()` & `dashboardRoute()`. Dimanfaatkan apa adanya. |

---

## 6. Route yang Ditambahkan (Modul 2)

Semua di dalam `Route::middleware('role:admin')`:

```
PATCH  users/{user}/toggle-status  →  admin.users.toggle-status
GET    users                       →  admin.users.index
GET    users/create                →  admin.users.create
POST   users                       →  admin.users.store
GET    users/{user}/edit           →  admin.users.edit
PUT    users/{user}                →  admin.users.update
DELETE users/{user}                →  admin.users.destroy
```

> Verifikasi: `php artisan route:list --name=users`
> Catatan: `toggle-status` didaftarkan **sebelum** `Route::resource` agar tidak tertimpa wildcard.

---

## 7. Keputusan Arsitektur

1. **Controller flat** di `App\Http\Controllers` (TANPA subfolder `Admin\`) — mengikuti konvensi Modul 1 yang sudah flat. `UserController` bukan `Admin\UserController`.
2. **Route naming pattern**: prefix nama `admin.` tanpa prefix URL. URL tetap `/users`, nama route `admin.users.*` — konsisten dengan `admin.dashboard` (URL `/dashboard`) dari Modul 1.
3. **View pattern**: `@extends('layouts.app')` + `@section('title')` + `@section('content')`. Bukan slot komponen.
4. **Hero header via utility `.hero-header`** (bukan inline `bg-gradient-to-br ...`). Class ini sudah membungkus gradient brand di CSS — dipakai konsisten sejak Modul 1. Semua halaman baru pakai `<div class="hero-header">`.
5. **Validasi via Form Request** (`Store`/`Update`), bukan validasi inline di controller. `authorize()` mengecek `isRole('admin')`.
6. **Delete modal = Alpine store global** `$store.deleteModal`:
   - API: `trigger(url, name)` untuk buka, `close()` untuk tutup.
   - Didaftarkan di event `alpine:init` (aman terhadap urutan load).
   - Konfirmasi men-submit form `DELETE` (CSRF + `@method('DELETE')`).
   - Di-include SEKALI di layout; dipanggil dari tombol mana pun.
7. **Toggle status** = route PATCH terpisah + form submit per baris (bukan fetch JS). Lebih sederhana & robust.
8. **Purge-safe class**:
   - Badge role → `@switch` dengan class literal per-role (hindari interpolasi `'bg-'.$var`).
   - Toggle di form → `:class` ternary dengan nama class **utuh** (`'bg-brand'` / `'bg-slate-300'`), bukan potongan string. Ini aman karena Tailwind tetap memindai string literalnya.
9. **Form partial tunggal** (`partials/form.blade.php`) dikendalikan cek `$user`:
   - `$user` null → mode create (POST ke `store`).
   - `$user` ada → mode edit (PUT ke `update`).
   - `create.blade.php` WAJIB `@include(..., ['user' => null])` secara eksplisit.
10. **Password**: cast `hashed` di model meng-hash otomatis. Saat update, jika password kosong → di-`unset` agar password lama tidak berubah.
11. **Filter via GET** (`search`, `role`, `status`) memakai `->when(...)` + `withQueryString()`.

---

## 8. Business Rule yang Sudah Diimplementasikan

Referensi: **BR-006** (SRS) / **FR-002**.

| Rule | Implementasi |
|---|---|
| Tidak boleh hapus diri sendiri | Guard di `destroy()` (return `back()` + error) **+** tombol hapus terkunci di UI |
| Tidak boleh nonaktifkan diri sendiri | Guard di `toggleStatus()` **dan** `update()` **+** toggle terkunci di UI baris sendiri |
| Admin terakhir tidak boleh dihapus | Guard di `destroy()` (`role='admin' && count(admin) <= 1`) **+** tombol terkunci di UI |
| Email harus unique | Form Request (`unique:users,email`, saat update pakai `ignore($userId)`) |
| Password minimal 8 karakter | Form Request (`min:8`; `required` saat create, `nullable` saat update) |
| Role terbatas 5 nilai | `Rule::in(['admin','waiters','kitchen','barista','kasir'])` |

**Prinsip:** guard tidak boleh hanya di UI. Semua BR-006 divalidasi ulang di server (defense in depth).

---

## 9. Hal yang TIDAK BOLEH Diubah (Aturan Ketat)

1. **Hero header WAJIB gradient brand** `bg-gradient-to-br from-brand to-brand-light` (via utility `.hero-header`). **JANGAN PERNAH** ganti ke emerald, violet, slate, atau skema warna lain. Non-negotiable.
2. **Palet brand:** `brand = #7C4A2D` (coklat), `brand-light = #A9714B`, background cream `#FAF6F0`. Font: Inter + Playfair Display (`font-display`).
3. **Tanpa Livewire / Inertia.** Interaktivitas hanya Alpine.js + form.
4. **Jangan ubah DB, migration, model, atau setup** yang sudah jadi kecuali benar-benar diperlukan. `User.php` sudah final untuk kebutuhan saat ini.
5. **Blok HTML per-role di-hardcode** (bukan interpolasi class dinamis) untuk menghindari Tailwind purge. Berlaku untuk sidebar & badge role.
6. **Delete pakai `$store.deleteModal`** — jangan bikin modal hapus baru per halaman. Reuse store global.
7. **Modul 1 & setup** dianggap stabil — jangan refactor tanpa alasan kuat.

---

## 10. Technical Debt / Catatan Terbuka

| Item | Status / Tindakan |
|---|---|
| **Pagination Tailwind** | Jika markup pagination tampil polos (ke-purge), jalankan `php artisan vendor:publish --tag=laravel-pagination` lalu pastikan `resources/views/vendor/pagination` masuk `content` di `tailwind.config.js`. **Perlu diverifikasi di environment.** |
| **`[x-cloak]` CSS** | Diasumsikan sudah ada (dipakai di overlay sidebar Modul 1). Pastikan `[x-cloak]{display:none}` benar-benar terdaftar, karena dipakai delete-modal & form. |
| **Flash message** | Hanya `success`/`error` yang dirender. Belum ada ringkasan error validasi global (mengandalkan `@error` inline per field). Cukup untuk sekarang. |
| **Password confirmation & strength meter** | Sengaja TIDAK diimplementasikan (di luar scope FR-002). Tambahkan bila diminta (`password_confirmation` + rule `confirmed`). |
| **Avatar user** | UI memakai inisial huruf, bukan foto. Tabel `users` tidak punya kolom `avatar` (sesuai Database Design). Foto di Figma diabaikan by design. |
| **Tombol "Filter Lanjutan" (Figma)** | Belum diimplementasikan — filter aktif hanya Role + Status. Placeholder bila perlu diperluas. |
| **Apostrof pada nama** | Sudah aman: tombol hapus memakai `@js($user->name)` (auto-escape ke JS). |

---

## 11. Modul Berikutnya

### ✅ Modul 3 — Manajemen Menu (FR-003) — SELESAI, lihat §13 untuk detail implementasi

**Ruang lingkup:**
- CRUD menu (`menus`) + kategori (`categories`).
- Field menu: `name`, `description`, `image` (upload), `price`, `is_available`, `category_id`.
- Kategori punya kolom `station` (`kitchen`/`barista`) — **sumber kebenaran routing** BR-003 (makanan → kitchen, minuman → barista).
- Toggle `is_available`; menu "habis" tidak bisa dipilih Waiters (BR-002).

**Tabel terkait:** `categories`, `menus` (lihat `03-ERD.md` & `04-Database.md`).

**Pola yang bisa di-reuse dari Modul 2:** struktur controller + Form Request, partial form create/edit, `$store.deleteModal`, `<x-flash />`, badge (buat `<x-station-badge>` mengikuti pola `role-badge`), hero `.hero-header`, filter GET + pagination.

**Catatan baru vs Modul 2:** ada **upload gambar** (`image`) — perlu handling `store()`/`update()` file + validasi, dan folder storage (`storage:link`).

### Roadmap sisa modul (dari SRS)

| FR | Modul | Status |
|---|---|---|
| FR-003 | Manajemen Menu | ✅ Selesai |
| FR-004 | Manajemen Meja | ✅ Selesai |
| FR-005 | Waiters Buat Order | ✅ Selesai |
| FR-006 | Station Kitchen | ✅ Selesai |
| FR-007 | Station Barista (amber) | ✅ Selesai |
| FR-008 | Pembayaran Kasir (denominasi Rupiah) | ✅ Selesai |
| FR-009 | Struk 58mm + Invoice PDF (DomPDF) | ✅ Selesai penuh (struk Modul 8, invoice + riwayat Modul 11) |
| FR-010 | Dashboard data asli | ✅ Selesai |
| FR-011 | Laporan Penjualan (Chart.js) | ✅ Selesai |
| FR-012 | Toggle menu habis (Waiters) | ✅ Selesai |
| FR-013 | Monitoring order aktif (Waiters, read-only) | ✅ Selesai |

---

## 12. Workflow yang Dipakai (untuk agen berikutnya)

1. Tiap modul dikerjakan **bertahap**, bukan sekaligus.
2. **STEP 1** dulu: analisis + daftar file yang akan dibuat/diubah → **berhenti**, tunggu konfirmasi.
3. **STEP 2**: sebelum coding, **cek struktur project yang sudah ada** (layout, middleware, model, konvensi folder). Jangan berasumsi jika file/namespace sudah ada — ikuti yang ada, jangan bikin struktur baru.
4. Implementasi dipecah per-part (backend dulu, lalu view) agar tiap slice bisa dites.
5. Sediakan file lengkap yang bisa langsung copy-paste beserta path eksplisit.
6. Referensi acuan: `02-SRS.md`, `03-ERD.md`, `04-Database.md`, screenshot Figma.

---

## 13. Modul 3 — Manajemen Menu & Kategori (FR-003) — ✅ DONE

**Status:** Selesai. Diverifikasi user di browser + smoke test otomatis (render 6 halaman, 13 uji validasi/guard PASS).
**Figma acuan:** `docs/figma/menu.png`, `docs/figma/kategori.png`.

### 13.1 File yang Dibuat

| File | Fungsi |
|---|---|
| `app/Http/Controllers/MenuController.php` | Resource controller menu + `toggleAvailability()`; index pakai `with('category')` + `withExists('orderItems')` (hindari N+1), filter kategori + search + statistik |
| `app/Http/Controllers/CategoryController.php` | Resource controller kategori; index pakai `withCount('menus')` + search + statistik |
| `app/Http/Requests/StoreMenuRequest.php` | Validasi create menu (image opsional, max 2 MB, jpg/png/webp) |
| `app/Http/Requests/UpdateMenuRequest.php` | Sama dengan Store; image kosong = foto lama dipertahankan |
| `app/Http/Requests/StoreCategoryRequest.php` | Validasi create kategori (`name` unique, `station` in kitchen/barista) |
| `app/Http/Requests/UpdateCategoryRequest.php` | Sama, `unique` dengan `ignore($this->route('category'))` |
| `resources/views/admin/menus/index.blade.php` | Hero + 3 kartu statistik (Total/Tersedia/Habis) + chip filter kategori + tabel (foto, nama+deskripsi, badge kategori, harga, toggle stok, aksi) + pagination |
| `resources/views/admin/menus/create.blade.php` | Wrapper form (pola Modul 2) |
| `resources/views/admin/menus/edit.blade.php` | Wrapper form |
| `resources/views/admin/menus/partials/form.blade.php` | Form tunggal create/edit (`$menu` null = create); upload foto + preview Alpine (`URL.createObjectURL`), `enctype="multipart/form-data"` |
| `resources/views/admin/categories/index.blade.php` | Hero + breadcrumb + kartu ringkasan (Total Kategori/Produk) + tombol besar Tambah + tabel (no, nama, jumlah produk, station, aksi) + info cards |
| `resources/views/admin/categories/create.blade.php` | Wrapper form |
| `resources/views/admin/categories/edit.blade.php` | Wrapper form |
| `resources/views/admin/categories/partials/form.blade.php` | Form tunggal create/edit; select station + warning saat edit station kategori ber-menu |
| `resources/views/components/station-badge.blade.php` | Badge station global: kitchen = orange, barista = amber (hardcode per-station, purge-safe, pola `role-badge`) |

### 13.2 File yang Diubah

| File | Perubahan |
|---|---|
| `routes/web.php` | Import `MenuController` & `CategoryController`; di group `role:admin`: `admin.menus.toggle-availability` (PATCH, didaftarkan SEBELUM resource), `Route::resource('menus')` & `Route::resource('categories')` keduanya `except(['show'])` + `names('admin.*')` |
| `resources/views/components/sidebar.blade.php` | Link "Menu" & "Kategori" (blok admin) dari `#` → route asli + active state `request()->routeIs(...)`. Tidak ada perubahan lain |

### 13.3 Route yang Ditambahkan

Semua di dalam `Route::middleware('role:admin')`:

```
PATCH  menus/{menu}/toggle-availability →  admin.menus.toggle-availability
GET    menus                            →  admin.menus.index
GET    menus/create                     →  admin.menus.create
POST   menus                            →  admin.menus.store
GET    menus/{menu}/edit                →  admin.menus.edit
PUT    menus/{menu}                     →  admin.menus.update
DELETE menus/{menu}                     →  admin.menus.destroy

GET    categories                       →  admin.categories.index
GET    categories/create                →  admin.categories.create
POST   categories                       →  admin.categories.store
GET    categories/{category}/edit       →  admin.categories.edit
PUT    categories/{category}            →  admin.categories.update
DELETE categories/{category}            →  admin.categories.destroy
```

> Verifikasi: `php artisan route:list --name=menus` dan `--name=categories`.

### 13.4 Komponen Baru

- `<x-station-badge :station="...">` — global, dipakai di tabel kategori; siap di-reuse untuk halaman Kitchen/Barista/order (Modul 4+).
- Komponen lama yang di-reuse: `<x-flash />`, `$store.deleteModal`, pola toggle purge-safe, pola form partial.

### 13.5 Business Rule yang Diimplementasikan

| Rule | Implementasi |
|---|---|
| BR-003 (kategori → station) | Form kategori mewajibkan `station`; opsi kategori di form menu menampilkan station tujuannya; badge station di tabel kategori |
| Kategori ber-menu tidak bisa dihapus (FK RESTRICT) | Guard di `CategoryController::destroy()` (`menus()->exists()` → back + error) **+** tombol hapus terkunci di UI (`menus_count > 0`) |
| Menu ber-riwayat-order tidak bisa dihapus (FK RESTRICT) | Guard di `MenuController::destroy()` (`orderItems()->exists()`) **+** tombol terkunci di UI (via `withExists`); pesan menyarankan nonaktifkan status |
| BR-002 (persiapan) | Kolom `is_available` dikelola via toggle di index + form; **penegakan** "menu habis tidak bisa dipilih Waiters" adalah tugas FR-005 (Modul Waiters) |
| Nama kategori unik | Validasi app-level di Form Request (BUKAN perubahan skema — DB tidak diubah) |

### 13.6 Catatan Implementasi (keputusan & asumsi)

1. **Toggle STATUS di Figma kategori TIDAK diimplementasikan** — tabel `categories` tidak punya kolom status dan skema final. Diganti kolom **Station** (badge kitchen/barista). Asumsi disetujui user.
2. **Info cards Figma kategori** ("Auto-Grouping", "Riwayat Perubahan") diganti konten yang akurat dengan sistem: "Tips POS" + "Routing Station".
3. **Upload foto**: disk `public`, folder `menus/`; ganti foto saat edit = file lama dihapus (`Storage::disk('public')->delete()`); hapus menu = file ikut dihapus. Tampil via `asset('storage/'.$menu->image)`; placeholder ikon bila kosong. `php artisan storage:link` SUDAH dijalankan di environment ini.
4. **Toggle ketersediaan admin** = route PATCH terpisah (`admin.menus.toggle-availability`) + form submit per baris — pola sama dengan toggle user Modul 2. Route Waiters (FR-012, `PATCH /menus/{id}/availability`) BELUM dibuat, itu scope modul Waiters.
5. **Chip filter kategori** (Figma) = link GET `?category={id}`, state kebawa dengan `array_filter` bersama `search`. Search box ditambahkan di toolbar tabel (tidak ada di Figma, konsisten dengan Modul 2 — untuk usability).
6. **Harga** ditampilkan `Rp{{ number_format($price, 0, ',', '.') }}`; input number dengan prefix "Rp", `step=500`.
7. Controller baru diletakkan **flat** di `App\Http\Controllers` — konsisten dengan `UserController` (Modul 2). Controller Modul 1 lama memang di subfolder (`Admin\`, `Waiter\`, dst.) — biarkan, jangan dipindah.

### 13.7 Acceptance Criteria FR-003 (terpenuhi)

- ✅ Admin bisa CRUD menu & set kategori (create/edit/hapus + upload foto + toggle tersedia/habis)
- ✅ Admin bisa CRUD kategori & set station (kitchen/barista)
- ⚠️ "Status Tidak tersedia menyembunyikan menu dari Waiters" — data & toggle SIAP; penegakan filternya di FR-005 saat halaman order Waiters dibuat (belum ada halamannya)

### 13.8 Technical Debt / Catatan Terbuka (Modul 3)

| Item | Status / Tindakan |
|---|---|
| `tailwind.config.js` punya blok `export default` GANDA (baris 1–8 sisa scaffold, pre-existing) | Build tetap jalan (loader men-transpile, assignment terakhir menang), tapi sebaiknya blok pertama dihapus. JANGAN sentuh blok kedua (berisi brand colors) |
| `storage:link` | Sudah dibuat di environment ini; environment/clone baru harus menjalankan ulang `php artisan storage:link` |
| Validasi dimensi/rasio gambar | Tidak ada (hanya tipe + ukuran 2 MB). Thumbnail dipaksa `object-cover` 12×12 — cukup untuk sekarang |
| Filter status (tersedia/habis) di index menu | Belum ada — filter yang tersedia: chip kategori + search nama. Tambahkan bila diminta |
| `docs/01-PRD.md` | Tidak ada di repo (disebut di 00-PROJECT_CONTEXT). Abaikan sampai user menyediakan |

### 13.9 Catatan untuk Modul 4 — Manajemen Meja (FR-004)

**Ruang lingkup (dari SRS):** meja berstatus `kosong`/`terisi`; status berubah **otomatis oleh sistem** (order dibuat → `terisi`; pembayaran sukses → `kosong`). Admin kemungkinan hanya perlu CRUD data meja (nama) + lihat status; Waiters membaca status di modulnya sendiri (FR-005).

- **Figma acuan:** `docs/figma/tables.png`.
- **Tabel terkait:** `tables` (`name` varchar(50), `status` enum default `kosong`) — sudah di-seed 6 meja via `MasterSeeder`.
- **Perhatikan BR-001/BR-012:** satu meja satu order aktif — validasinya di aplikasi saat buat order (FR-005), bukan di modul ini; tapi guard hapus meja perlu cek FK `orders.table_id` RESTRICT (meja yang punya riwayat order tidak bisa dihapus — pola sama dengan guard menu/kategori Modul 3).
- **Pola reuse:** resource controller flat + Form Request, `$store.deleteModal`, `<x-flash />`, hero `.hero-header`, toggle/badge purge-safe, pagination + `withQueryString()`.
- Status meja JANGAN bisa diubah manual sembarangan dari UI admin — sumber perubahan status adalah alur order/pembayaran (SRS FR-004: actor = Sistem). Kalau Figma menampilkan kontrol manual, diskusikan dulu dengan user sebelum implementasi.
- Sidebar admin: link "Meja" masih `#` — aktifkan dengan pola yang sama seperti Modul 3.

> **Update:** Modul 4 SUDAH selesai — lihat §14. Catatan di atas dipertahankan sebagai riwayat.

---

## 14. Modul 4 — Manajemen Meja (FR-004) — ✅ DONE

**Status:** Selesai. Diverifikasi user di browser (tanpa bug) + smoke test otomatis.
**Figma acuan:** `docs/figma/tables.png`.

### 14.1 File yang Dibuat

| File | Fungsi |
|---|---|
| `app/Http/Controllers/TableController.php` | Resource controller (except show). Index: filter status (`kosong`/`terisi`) + search + statistik, `withExists('orders')` (hindari N+1), `paginate(8)` sesuai grid Figma. Store TANPA input status (default DB `kosong`). Destroy: guard dua lapis |
| `app/Http/Requests/StoreTableRequest.php` | `name` required, string, max:50, `unique:tables,name` (app-level, skema TIDAK diubah) |
| `app/Http/Requests/UpdateTableRequest.php` | Sama, `Rule::unique(...)->ignore($this->route('table'))` |
| `resources/views/admin/tables/index.blade.php` | Hero + tombol Tambah, 3 kartu statistik (Total/Terisi/Kosong, angka ber-padding nol mis. "08"), chip filter Semua/Kosong/Terisi + search, **grid kartu meja** (bukan tabel), kartu putus-putus "Tambah Meja" di akhir grid, footer pagination |
| `resources/views/admin/tables/create.blade.php` | Wrapper form (pola modul sebelumnya) |
| `resources/views/admin/tables/edit.blade.php` | Wrapper form |
| `resources/views/admin/tables/partials/form.blade.php` | Form tunggal create/edit — field **nama saja**; saat edit menampilkan status sebagai badge read-only + info box "status dikelola sistem" |
| `resources/views/components/table-status-badge.blade.php` | Badge status global: `terisi` = bg-brand putih, `kosong` = emerald (hardcode purge-safe). Reusable untuk halaman Waiter & Kasir |

### 14.2 File yang Diubah

| File | Perubahan |
|---|---|
| `routes/web.php` | Import `TableController`; `Route::resource('tables')->except(['show'])->names('admin.tables')` di group `role:admin`. **TIDAK ada route toggle status** (by design) |
| `resources/views/components/sidebar.blade.php` | Link "Meja" (blok admin) `#` → `route('admin.tables.index')` + active state `admin.tables.*`. Tidak ada perubahan lain |

### 14.3 Route yang Ditambahkan

Semua di dalam `Route::middleware('role:admin')`:

```
GET    tables                 →  admin.tables.index
GET    tables/create          →  admin.tables.create
POST   tables                 →  admin.tables.store
GET    tables/{table}/edit    →  admin.tables.edit
PUT    tables/{table}         →  admin.tables.update
DELETE tables/{table}         →  admin.tables.destroy
```

> Verifikasi: `php artisan route:list --name=tables`.

### 14.4 Komponen Baru

- `<x-table-status-badge :status="...">` — global; pakai ulang di Modul 5 (kartu meja Waiter) dan Modul Kasir.
- Reuse: `<x-flash />`, `$store.deleteModal`, `.hero-header`, pola chip filter GET (dari Modul 3), pola form partial.

### 14.5 Business Rule yang Diimplementasikan

| Rule | Implementasi |
|---|---|
| FR-004: status meja milik SISTEM | Tidak ada toggle/field status di seluruh UI admin; form hanya nama; store/update tidak menerima input status; halaman edit menampilkan status read-only |
| Meja baru mulai `kosong` | Default DB dipakai (store tidak mengirim status) — teruji |
| Meja TERISI tidak boleh dihapus | Guard di `destroy()` **+** tombol hapus terkunci di UI dengan tooltip alasan |
| Meja ber-riwayat order tidak boleh dihapus (FK `orders.table_id` RESTRICT) | Guard `orders()->exists()` di `destroy()` **+** tombol terkunci via `withExists('orders')` |
| Nama meja unik | Validasi app-level di Form Request (bukan perubahan skema) |

### 14.6 Acceptance Criteria FR-004 (terpenuhi)

- ✅ Admin bisa CRUD data meja (nama); meja baru otomatis KOSONG
- ✅ Status meja tampil akurat (badge + statistik terisi/kosong + filter)
- ✅ Status TIDAK bisa diubah manual dari UI admin
- ✅ Guard delete (terisi / riwayat order) di UI dan server
- ⚠️ Transisi status otomatis (order dibuat → TERISI; bayar → KOSONG) adalah titik penegakan FR-005 & FR-008 — belum ada alur ordernya

### 14.7 Hasil Testing (smoke test, semua PASS)

- `route:list` — 6 route benar; `view:cache` — seluruh Blade terkompilasi
- Render sebagai admin: index (tanpa filter, filter kosong, filter terisi), create, edit — 5 render OK
- Validasi: meja valid lolos; tanpa nama / nama duplikat / nama >50 karakter ditolak
- Guard (dalam transaksi, **di-rollback** — DB dev tidak berubah): meja baru default `kosong`; meja kosong tanpa riwayat bisa dihapus; kondisi `terisi` terdeteksi untuk guard destroy
- Verifikasi browser oleh user: OK, tanpa bug

### 14.8 Technical Debt / Catatan Terbuka (Modul 4)

| Item | Status / Tindakan |
|---|---|
| Warna badge "Kosong" = emerald | Figma memakai krem/pink muda; emerald dipilih sebagai sinyal "tersedia" yang lebih jelas. User sudah verifikasi & terima. Ubah di `table-status-badge` bila ingin persis Figma |
| Guard riwayat order belum teruji dengan data nyata | Saat smoke test belum ada baris `orders` di DB dev. Logika `orders()->exists()` sama persis dengan guard Modul 3 yang terbukti. Uji ulang setelah Modul 5 menghasilkan order |
| Kapasitas & Area (Figma) | TIDAK diimplementasikan — tidak ada kolomnya, skema final (keputusan user). Jangan tambahkan tanpa persetujuan eksplisit |

### 14.9 Catatan Implementasi

1. Chip filter area di Figma (Indoor/Outdoor/VIP) diganti filter **status** Semua/Kosong/Terisi (keputusan user — data area tidak ada di skema).
2. Kartu meja: chip nama (bg-brand) + badge status + ikon armchair besar di tengah + aksi edit/hapus; border kartu meja terisi diberi aksen `border-brand/30`.
3. `paginate(8)` (bukan 10) agar pas dengan grid 4 kolom × 2 baris Figma; kartu "Tambah Meja" selalu dirender di akhir grid setiap halaman.
4. Filter chip = link GET dengan `array_filter` membawa `search`; form search membawa `status` via hidden input — pola sama dengan Modul 3.

### 14.10 Catatan untuk Modul 5 — Waiters / Order (FR-005, + FR-012, FR-013)

**Ruang lingkup inti (FR-005):** Waiters memilih meja KOSONG → input nama customer → pilih menu (hanya yang `is_available`) → set qty → simpan order.

**Figma acuan:** `docs/figma/waiter dashboard.png`.

**Hal-hal KRITIS yang harus benar saat membuat order:**
- **BR-001/BR-012:** meja harus KOSONG dan belum punya order `active` — validasi server-side (`orders` where `table_id` + `status='active'`), bukan cuma cek `tables.status`.
- **BR-002:** menu `is_available = false` tidak boleh bisa dipilih (filter di query DAN validasi ulang saat submit — menu bisa berubah habis di antara render & submit).
- **BR-003:** `order_items.station` **disalin** dari `categories.station` milik menu saat order dibuat (snapshot, bukan join runtime).
- **Snapshot harga:** `order_items.price` diambil dari `menus.price` saat itu; `subtotal = qty × price`; `orders.total` = Σ subtotal — hitung **di server**, jangan percaya input client.
- **`order_number`:** format `ORD-YYYYMMDD-####`, counter reset per hari (lihat `04-Database.md`). Generate di server, perhatikan race condition (buat di dalam transaksi).
- **Transaksi DB:** pembuatan order + items + update `tables.status='terisi'` wajib dibungkus `DB::transaction()`.
- `orders.created_by` = waiter yang login.

**Scope tambahan yang satu rumpun (konfirmasi ke user apakah ikut modul ini atau dipisah):**
- FR-012 — toggle menu habis oleh Waiters (`PATCH /menus/{id}/availability`, route TERPISAH dari `admin.menus.toggle-availability`, di group `role:waiters`).
- FR-013 — halaman monitoring order aktif Waiters (read-only).

**Kondisi awal yang sudah tersedia:** `WaiterController` (`app/Http/Controllers/Waiter/WaiterController.php`, subfolder — konvensi Modul 1, biarkan) + route `GET /waiter` + view `waiter/dashboard.blade.php` (placeholder). Sidebar waiters: link "Meja", "Buat Order", "Order Aktif", "Menu (Stok)" masih `#`.

**Komponen siap reuse:** `<x-table-status-badge>` (kartu meja), `<x-station-badge>`, `<x-flash />`, `$store.deleteModal`, pola chip filter + grid kartu (Modul 4), pola form + validasi Form Request.

**SRS route mapping (§7):** `POST /orders` untuk simpan order — pertimbangkan `OrderController` baru (flat) dengan Form Request `StoreOrderRequest` (array items + qty min 1 + minimal 1 item).

> **Update:** Modul 5 Tahap 1 (FR-005) SUDAH selesai — lihat §15. FR-012 & FR-013 sengaja DITUNDA (keputusan user). Catatan di atas dipertahankan sebagai riwayat.

---

## 15. Modul 5 Tahap 1 — Flow Order Waiters (FR-005) — ✅ DONE

**Status:** ✅ Selesai. Smoke test end-to-end 17/17 PASS **+ diverifikasi user di browser** (UI sesuai Figma, flow order berjalan, validasi sesuai, tanpa bug).
**Figma acuan:** `docs/figma/waiter dashboard.png`.
**Keputusan scope (user):** Modul 5 dipecah — Tahap 1 = flow order saja. **FR-012 (Menu/Stok) & FR-013 (Pesanan Aktif) BELUM diimplementasikan**, termasuk strip "PESANAN AKTIF" di bawah dashboard Figma (itu bagian FR-013).

### 15.1 File yang Dibuat

| File | Fungsi |
|---|---|
| `app/Http/Controllers/OrderController.php` | Flat, hanya `store()` (FR-005). Seluruh proses dalam SATU `DB::transaction()`: lock meja (`lockForUpdate`) → guard BR-001/BR-012 → lock menu + guard BR-002 → generate `order_number` → hitung total/subtotal DI SERVER dari harga DB → create order + `createMany` items (snapshot `price`, `subtotal`, `station`) → meja jadi `terisi` → redirect + flash |
| `app/Http/Requests/StoreOrderRequest.php` | `authorize()` = role waiters. Rules: `table_id` required+exists, `customer_name` required max:100 (**SRS menang atas Figma yang menulis "opsional"**), `items` array min:1, `items.*.menu_id` required+distinct+exists, `items.*.qty` integer min:1. Pesan Indonesia |

### 15.2 File yang Diubah

| File | Perubahan |
|---|---|
| `resources/views/waiter/dashboard.blade.php` | Placeholder DIGANTI TOTAL → satu halaman FR-005 sesuai Figma (detail di 15.5) |
| `app/Http/Controllers/Waiter/WaiterController.php` | `index()` kini menyuplai: `$tables` (semua meja), `$categories`, `$menuData` (hanya menu `is_available`, payload ringan utk Alpine: id/name/price int/image url/category_id) |
| `routes/web.php` | Import `OrderController`; `POST /orders → orders.store` di group `role:waiters` |
| `resources/views/components/sidebar.blade.php` | Blok waiters: "Meja" & "Buat Order" → `route('waiter.dashboard')` (flow satu halaman), TANPA active-state (hanya link Dashboard yang punya). "Order Aktif" & "Menu (Stok)" tetap `#` (Tahap 2) |

### 15.3 Route yang Ditambahkan

```
POST /orders  →  orders.store  (group role:waiters)   // sesuai SRS §7
```

### 15.4 Business Rule yang Diimplementasikan

| Rule | Implementasi |
|---|---|
| BR-001 / BR-012 | Server: `lockForUpdate` baris meja + cek `status !== 'kosong'` ATAU ada order `active` di meja → `ValidationException`. UI: meja terisi dirender non-klik |
| BR-002 | UI: menu habis TIDAK dirender di grid. Server: re-validasi `is_available` semua item saat submit (menu bisa berubah habis antara render & submit) dengan pesan menyebut nama menunya |
| BR-003 | `order_items.station` disalin dari `categories.station` milik menu, di server, saat create |
| Snapshot harga | `order_items.price` = `menus.price` saat itu; `subtotal = qty × price`; `orders.total` = Σ subtotal — semua dihitung server, input client tidak dipercaya |
| `order_number` | `ORD-YYYYMMDD-####`, counter reset harian; digenerate DI DALAM transaksi via `max()` + `lockForUpdate`, unique index sebagai pengaman terakhir race condition |
| FR-004 transisi | Meja `kosong → terisi` dalam transaksi yang sama |
| `created_by` | Diisi `auth()->id()` (waiter login) |

### 15.5 Arsitektur Halaman Dashboard (penting untuk maintenance)

- **Satu `<form>` membungkus seluruh halaman** (POST `orders.store`). Konsekuensi: SEMUA tombol interaktif di dalamnya wajib `type="button"` — hanya "Kirim Pesanan" yang `type="submit"`.
- **Alpine component `orderPage()`** didaftarkan via `Alpine.data` di `@push('scripts')` (event `alpine:init`). State: `selectedTable`, `customer`, `category`, `search`, `cart[]`.
- **Filter kategori + search menu = client-side** (instan, tanpa reload) atas payload `@js($menuData)`.
- **Cart → server** via hidden inputs `items[i][menu_id]` & `items[i][qty]` yang dirender `<template x-for>`; `table_id` hidden ber-`:value`.
- **Old-input restore di `init()`**: bila validasi server gagal, pilihan meja + nama + isi cart dipulihkan dari `old()` (item yang sudah tidak tersedia otomatis tersaring karena tidak ada di payload menu).
- **Tombol submit `:disabled="!canSubmit"`** (meja + nama + ≥1 item) dengan hint alasan; guard tambahan `@submit` mencegah submit paksa.
- Error validasi server dirender sebagai ringkasan `$errors->all()` di atas halaman + `@error` inline nama pelanggan.
- Panel cart `sticky top-6`; format uang `toLocaleString('id-ID')`.

### 15.6 Acceptance Criteria FR-005 (terpenuhi)

- ✅ Waiters hanya bisa memilih meja KOSONG (terisi dirender non-klik + guard server)
- ✅ Nama customer wajib (client hint + validasi server)
- ✅ Order tersimpan (order + items, satu transaksi)
- ✅ Item makanan ber-station `kitchen`, minuman ber-station `barista` (siap dikonsumsi FR-006/FR-007)
- ✅ Meja berubah TERISI
- ✅ Minimal 1 menu, qty ≥ 1, menu duplikat ditolak

### 15.7 Hasil Testing (smoke test tinker, transaksi luar di-ROLLBACK — DB tidak berubah)

17/17 PASS: render dashboard; format `order_number` (`ORD-20260708-0001`); total server-side; status active + created_by; 2 items tersimpan; station kitchen/barista benar; snapshot harga+subtotal; meja terisi; redirect ke dashboard; counter harian naik (`-0001 → -0002`); meja terisi ditolak; menu habis ditolak saat submit; validasi tanpa customer / tanpa items / qty 0 / menu duplikat / meja tidak valid semua ditolak; rollback bersih.

> Teknik smoke test yang terbukti dipakai (reusable untuk modul berikutnya): bangun `StoreOrderRequest::create(...)` manual + `setLaravelSession(app('session')->driver('array'))` + `app()->instance('request', ...)` + `setUserResolver` + `validateResolved()`, panggil controller langsung, bungkus `DB::beginTransaction()`/`rollBack()` di luar (transaksi controller jadi savepoint).

### 15.8 Technical Debt / Catatan Terbuka (Modul 5 Tahap 1)

| Item | Status / Tindakan |
|---|---|
| Strip "PESANAN AKTIF" (Figma, bawah dashboard) | SENGAJA belum — itu FR-013 (Tahap 2). Tempatnya sudah jelas: di bawah grid menu, full-width |
| Badge MENUNGGU/DIMASAK/SELESAI di Figma | TIDAK AKAN diimplementasikan — bertentangan dengan BR-009/BR-010 (tidak ada status masak). Ganti badge netral "AKTIF" saat FR-013 dikerjakan |
| Waktu di header cart Figma ("14:30 WIB") | Tidak ditampilkan (order belum ada sebelum submit). Bisa ditambah jam client-side bila diminta |
| Sidebar "Meja" & "Buat Order" menunjuk halaman yang sama dengan Dashboard | Konsekuensi flow satu halaman (Figma). Tanpa active-state agar tidak triple-highlight |
| `items.*.qty` tanpa batas atas | SRS hanya menetapkan min 1. Tambahkan `max` bila user minta |

### 15.9 Catatan untuk Modul 5 Tahap 2 — FR-012 + FR-013 (berikutnya)

- **FR-012 (Menu/Stok Waiters):** halaman `GET /waiter/menus` daftar SEMUA menu + toggle tersedia/habis; route `PATCH /menus/{menu}/availability` (nama beda dari admin: `waiter.menus.availability`) di group `role:waiters`. Reuse pola toggle purge-safe Modul 3. Kitchen/Barista TIDAK boleh punya akses ini.
- **FR-013 (Pesanan Aktif, read-only):** halaman `GET /waiter/orders` (SRS §7) + strip "PESANAN AKTIF" di dashboard. Query: `Order::where('status','active')` + eager load `table`, `items` (ringkasan "N Makanan, N Minuman" dari `station`), waktu relatif (`diffForHumans`). TANPA tombol aksi apa pun (BR-009/BR-010). Pertimbangkan partial `waiter/partials/order-card.blade.php` dipakai dashboard + halaman penuh.
- Sidebar waiters: aktifkan link "Order Aktif" & "Menu (Stok)".
- Setelah Tahap 2, guard "meja ber-riwayat order tidak bisa dihapus" (Modul 4 §14.8) bisa diuji dengan data order nyata.

> **Update:** Tahap 2 SUDAH selesai — lihat §16.

---

## 16. Modul 5 Tahap 2 — FR-012 (Menu/Stok) + FR-013 (Pesanan Aktif) — ✅ DONE

**Status:** ✅ Selesai. Smoke test 10/10 PASS dengan rollback **+ diverifikasi user di browser** (UI sesuai Figma, flow Menu (Stok) & Order Aktif berjalan, validasi & business rule sesuai, tanpa bug).
**Dengan ini Modul 5 (FR-005 + FR-012 + FR-013) SELESAI PENUH.**

### 16.1 File yang Dibuat

| File | Fungsi |
|---|---|
| `resources/views/waiter/menus.blade.php` | Halaman "Menu (Stok)" (FR-012): hero + 3 kartu statistik (Total/Tersedia/Habis) + chip filter kategori + search + tabel menu dengan **hanya toggle stok** (tanpa tambah/edit/hapus — CRUD milik Admin FR-003), pagination 10/hal |
| `resources/views/waiter/orders.blade.php` | Halaman "Order Aktif" (FR-013): hero + grid kartu order aktif + pagination 9/hal + empty state. **Murni read-only** |
| `resources/views/waiter/partials/order-card.blade.php` | Kartu order aktif reusable: order_number, badge netral "AKTIF" (emerald), "Meja — Customer", ringkasan item per station ("N Makanan, N Minuman" dari sum qty), waktu relatif `diffForHumans(short)`. Dipakai strip dashboard + halaman penuh |

### 16.2 File yang Diubah

| File | Perubahan |
|---|---|
| `app/Http/Controllers/Waiter/WaiterController.php` | `index()` + `$activeOrders` (6 terbaru, eager load `table`+`items`) & `$activeOrdersTotal`; method baru: `menus(Request)` (filter+search+stats), `toggleAvailability(Menu)` (FR-012), `orders()` (paginate 9, status active saja) |
| `routes/web.php` | Di group `role:waiters`: `GET /waiter/menus → waiter.menus`, `PATCH /menus/{menu}/availability → waiter.menus.availability` (sesuai SRS §7; TERPISAH dari `admin.menus.toggle-availability`), `GET /waiter/orders → waiter.orders` |
| `resources/views/waiter/dashboard.blade.php` | Tambah strip "Pesanan Aktif" **setelah `</form>`** (tidak mengganggu form order): header + jumlah + link "Lihat semua" (muncul bila total > yang ditampilkan) + grid kartu / empty state |
| `resources/views/components/sidebar.blade.php` | Blok waiters: link "Order Aktif" & "Menu (Stok)" diaktifkan + active state |

### 16.3 Route yang Ditambahkan

```
GET   /waiter/menus                →  waiter.menus                (FR-012, halaman)
PATCH /menus/{menu}/availability   →  waiter.menus.availability   (FR-012, toggle)
GET   /waiter/orders               →  waiter.orders               (FR-013)
```

### 16.4 Business Rule yang Diimplementasikan

| Rule | Implementasi |
|---|---|
| FR-012: Waiters bisa toggle ketersediaan menu | Halaman + route PATCH di group `role:waiters`; menu habis otomatis hilang dari grid order dashboard (payload hanya `is_available = true`) |
| FR-012: Kitchen/Barista TIDAK bisa toggle | Route hanya di group `role:waiters` (admin punya route sendiri via FR-003) |
| FR-013: hanya order aktif | Query `where('status','active')` — order paid tidak tampil (teruji) |
| FR-013: read-only | Tidak ada tombol aksi/form apa pun di kartu & halaman order |
| BR-009/BR-010 | Badge status masak Figma (MENUNGGU/DIMASAK/SELESAI) TIDAK diimplementasikan → badge netral "AKTIF"; tidak ada tracking status |

### 16.5 Acceptance Criteria (terpenuhi)

**FR-012:** ✅ Waiters bisa ubah status tersedia/habis; ✅ menu habis tidak bisa dipilih saat buat order (hilang dari payload + guard server Tahap 1); ✅ Kitchen/Barista tidak punya akses toggle.
**FR-013:** ✅ daftar order aktif tampil (dashboard strip + halaman penuh); ✅ read-only tanpa tombol ubah status; ✅ hanya order belum dibayar yang tampil.

### 16.6 Hasil Testing (smoke test tinker, rollback — DB tidak berubah)

10/10 PASS: render dashboard + strip berisi order baru; tidak ada badge status masak; halaman orders tampil + ringkasan "2 Minuman" benar; halaman menus + search bekerja; **order paid hilang dari Order Aktif**; toggle FR-012 bolak-balik bekerja; menu habis hilang dari payload dashboard; rollback bersih.

> Catatan harness uji: pesan flash dari `store()` tetap tinggal di session store **default** pada konteks tinker (tidak ter-flush oleh flush driver `array`) dan sempat membuat asersi `str_contains` false-FAIL — bila menulis smoke test render berikutnya, `session()->flush()` DAN `app('session')->driver('array')->flush()` dua-duanya.

### 16.7 Technical Debt / Catatan Terbuka (Tahap 2)

| Item | Status / Tindakan |
|---|---|
| Halaman Menu (Stok) tidak ada di Figma | Mengikuti pola visual halaman menu admin (keputusan yang disetujui user di STEP 1) |
| Strip Pesanan Aktif menampilkan 6 order terbaru | Angka bisa disesuaikan; link "Lihat semua" muncul otomatis bila total lebih banyak |
| Polling/auto-refresh strip pesanan aktif | TIDAK diimplementasikan — SRS hanya mensyaratkan polling untuk station Kitchen/Barista (FR-006/FR-007). Refresh manual cukup untuk Waiters |

### 16.8 Catatan untuk Modul 6 — Station Kitchen (FR-006)

- **Scope:** halaman antrian `GET /kitchen` — hanya item `station='kitchen'` dari order aktif; **polling berkala** (bukan websocket) + **notifikasi suara/lonceng** saat item baru; **cetak checker** `GET /orders/{id}/checker/kitchen`; TANPA tombol status apa pun (BR-009/BR-010).
- **Figma acuan:** `docs/figma/barista&kitchen.png`.
- **Kondisi awal:** `Station\KitchenController` + route `GET /kitchen` + view `kitchen/index.blade.php` (placeholder) sudah ada dari Modul 1.
- **Teknis polling:** aturan proyek melarang Livewire/Inertia tapi polling butuh endpoint — pertimbangkan endpoint JSON ringan (mis. `GET /kitchen/queue` mengembalikan hash/last-id) yang dipanggil `setInterval` Alpine, lalu reload halaman/segmen saat ada perubahan + bunyikan audio. Diskusikan pendekatan di STEP 1.
- **Query antrian:** `OrderItem::where('station','kitchen')` + `whereHas('order', status active)` + eager load `order.table`, `menu`; urut tertua dulu.
- **Checker:** tiket cetak berisi order_number, meja, customer, daftar item kitchen + qty — CSS print sederhana (pola serupa akan dipakai struk 58mm FR-009).
- **Kitchen TIDAK boleh** mengakses order/transaksi/pembayaran (BR-008) — cukup middleware `role:kitchen` yang sudah ada.
- Barista (FR-007) = mirror kitchen dengan filter `barista` + skema amber — pertimbangkan partial/komponen bersama agar Modul 7 tinggal pakai.

> **Update:** Modul 6 SUDAH selesai — lihat §17.

---

## 17. Modul 6 — Station Kitchen (FR-006) — ✅ DONE

**Status:** Selesai. Smoke test 12/12 PASS dengan rollback (menunggu verifikasi user di browser).
**Figma acuan:** `docs/figma/barista&kitchen.png`.

### 17.1 File yang Dibuat

| File | Fungsi |
|---|---|
| `resources/views/station/checker.blade.php` | **View checker SHARED Kitchen & Barista** — standalone (tanpa `layouts.app`/Vite), tiket cetak ±58mm monospace, auto `window.print()`. Parameter: `$order` (+relasi `table`), `$items` (item station terkait +`menu`), `$station` (`'kitchen'`\|`'barista'`). **Modul 7 tinggal kirim parameter — JANGAN duplikasi view ini** |

### 17.2 File yang Diubah

| File | Perubahan |
|---|---|
| `app/Http/Controllers/Station/KitchenController.php` | `index()`: antrian order aktif ber-item kitchen (eager load `table` + item kitchen + `menu`), **FIFO `oldest()`**, + `$lastItemId` (max id item kitchen — baseline polling). Method baru: `queueStatus()` (JSON `{last_id}`), `checker(Order)` (render `station.checker`, `abort 404` bila order tak punya item kitchen) |
| `resources/views/kitchen/index.blade.php` | Placeholder → halaman antrian penuh: hero (badge jumlah antrian + jam live + **toggle suara**), banner "Pesanan Masuk Terbaru" + tombol Lihat Pesanan (anchor ke kartu), grid kartu per-order (order_number, jam masuk, "Lama Tunggu: Xm", meja besar, customer, daftar `Nx item`, tombol **Cetak Checker** target `_blank`), kartu putus-putus "Menunggu Pesanan" (pulse). Script polling di `@push('scripts')` |
| `routes/web.php` | Di group `role:kitchen`: `GET /kitchen/queue-status → kitchen.queue-status`, `GET /orders/{order}/checker/kitchen → kitchen.checker` (sesuai SRS §7) |

### 17.3 Route yang Ditambahkan

```
GET /kitchen/queue-status             →  kitchen.queue-status   (JSON polling)
GET /orders/{order}/checker/kitchen   →  kitchen.checker        (SRS §7)
```

### 17.4 Arsitektur Polling + Suara (REUSABLE untuk Modul 7)

- **`const POLL_INTERVAL = 5000`** — SATU-SATUNYA tempat mengubah interval (permintaan user); `const POLL_URL = route(...)`.
- Komponen Alpine **`stationQueue(lastId)`**: jam live (`tick` 1 detik), `poll()` tiap `POLL_INTERVAL` → `fetch` JSON; bila `last_id` naik → lonceng + `location.reload()` (delay 800ms agar lonceng terdengar). Error fetch di-swallow (jaringan lokal, retry interval berikutnya).
- **Lonceng "ding-dong" via Web Audio API** (2 oscillator sine 1319Hz + 1568Hz) — tanpa file audio/package. Toggle suara (`localStorage.station_sound`) sekaligus meng-unlock AudioContext (kebijakan autoplay browser: butuh gesture user).
- **Modul 7 (Barista):** pakai komponen `stationQueue` yang sama — cukup ganti `POLL_URL` ke endpoint barista + skema warna amber. Pertimbangkan memindah script ke partial bersama saat implementasi.

### 17.5 Business Rule yang Diimplementasikan

| Rule | Implementasi |
|---|---|
| Hanya item `station='kitchen'` | Filter di query index, checker, dan endpoint polling |
| Hanya order aktif | `where('status','active')` — order paid hilang dari antrian (teruji) |
| BR-009/BR-010: tanpa status cooking/ready | Satu-satunya aksi = Cetak Checker; tidak ada form/tombol status; selesai = lonceng manual (tertulis juga di footer checker) |
| BR-008: kitchen tidak akses order/transaksi | Semua route di group `role:kitchen`; tidak ada link keluar station |
| Polling "beberapa detik" (NFR) | 5 detik via `POLL_INTERVAL` |

### 17.6 Keputusan Figma (disetujui user di STEP 1)

"Takeaway" dilewati (order selalu ber-meja); tag "PRIORITY" dilewati (tidak ada datanya); label "Cetak Struk" → "**Cetak Checker**" (struk = milik Kasir FR-009); kartu placeholder "Menunggu Pesanan" dipertahankan sesuai Figma.

### 17.7 Acceptance Criteria FR-006 (terpenuhi)

- ✅ Hanya item makanan tampil di antrian (order minuman-saja tidak muncul; item minuman pada order campuran tidak ikut)
- ✅ Notifikasi suara saat item baru (lonceng Web Audio + toggle; berbunyi saat `last_id` naik)
- ✅ Checker bisa dicetak (halaman print standalone, auto-print, hanya item kitchen)
- ✅ Tidak ada status cooking/ready
- ✅ Kitchen tidak bisa membuka halaman order/transaksi (middleware)

### 17.8 Hasil Testing (smoke test tinker, rollback — DB tidak berubah)

12/12 PASS: render antrian; order ber-item kitchen tampil; order minuman-saja TIDAK tampil; item minuman tidak ikut di kartu; placeholder + banner ada; `POLL_INTERVAL` tunggal + komponen reusable; tidak ada kontrol status; `queue-status` mengembalikan `last_id` terbaru; checker memuat item kitchen saja; checker order tanpa item kitchen → 404; order paid hilang dari antrian; rollback bersih.

> Catatan harness: dua FAIL sementara adalah bug ASERSI skrip, bukan kode — (1) regex `/i` menangkap teks placeholder "Menunggu Pesanan", (2) form `logout` (navbar) & shell `$store.deleteModal` (layout) tertangkap cek "tidak ada form". Saat menguji "tidak ada kontrol status" pada halaman ber-layout, kecualikan kedua form bawaan layout itu.

### 17.9 Technical Debt / Catatan Terbuka (Modul 6)

| Item | Status / Tindakan |
|---|---|
| Suara butuh satu interaksi user dulu | Kebijakan autoplay browser. Toggle suara meng-unlock audio; default ON tapi baru bisa berbunyi setelah ada gesture. Operator kitchen cukup klik sekali di halaman |
| Badge "N Pesanan Baru" topbar Figma | Diwakili badge "N order dalam antrian" di hero (topbar global tidak diubah — komponen Modul 1) |
| Reload penuh saat ada item baru | Sederhana & andal untuk layar station; kalau kelak terasa berat bisa diganti fetch partial — JANGAN tanpa diskusi |

### 17.10 Catatan untuk Modul 7 — Station Barista (FR-007)

- **Mirror persis Kitchen** dengan: filter `station='barista'`, **skema warna amber** (SRS: pembeda dari kitchen; hero tetap gradient brand — aturan ketat; amber untuk aksen kartu/badge/banner).
- Route (group `role:barista`, SRS §7): `GET /barista` (sudah ada), `GET /barista/queue-status`, `GET /orders/{order}/checker/barista`.
- `Station\BaristaController` (sudah ada, placeholder) — isi mengikuti pola `KitchenController` dengan filter barista.
- **Checker: REUSE `station/checker.blade.php`** — kirim `station: 'barista'`, JANGAN buat view checker baru.
- **Polling: REUSE pola `stationQueue`** — `POLL_INTERVAL` tetap satu konstanta; pertimbangkan ekstrak script ke partial bersama (mis. `station/partials/queue-script.blade.php` ber-parameter URL) supaya tidak ada duplikasi JS di dua view.
- View `barista/index.blade.php` (placeholder) — struktur sama dengan kitchen; teks "item minuman"; ikon `cup-soda`.

> **Update:** lihat juga §18 — Barista WAJIB mengikuti perilaku baru (BR-014 sekali cetak + BR-015 VIP).

---

## 18. Modul 6 Penyempurnaan — Checker Sekali Cetak (BR-014) + Prioritas VIP (BR-015) — ✅ DONE

**Status:** ✅ Selesai. Smoke test 18/18 PASS dengan rollback **+ diverifikasi user di browser** (tanpa bug).
**Business rule BARU (disetujui user — perluasan atas SRS, didokumentasikan di sini, SRS tidak diedit):**

```
BR-014  Checker hanya bisa dicetak SATU KALI per station per order.
        "Dicetak" = halaman checker dibuka (browser tidak melaporkan hasil dialog print).
        Ditegakkan atomik oleh UNIQUE(order_id, station) di tabel checker_prints.
        TIDAK ada mekanisme cetak ulang dari halaman station (risiko printer macet
        diterima user; reset hanya via DB manual / fitur admin masa depan).

BR-015  Order dari meja VIP diprioritaskan di antrian station:
        kelompok "Belum Dicetak" diurutkan VIP dulu, lalu FIFO created_at.
        VIP adalah atribut meja (tables.is_vip), diatur Admin via form meja.
```

### 18.1 Perubahan Skema (ADDITIVE — disetujui user; migration lama tidak diubah)

| Perubahan | Alasan desain |
|---|---|
| Tabel baru **`checker_prints`** (`order_id` FK CASCADE, `station` varchar(20), `printed_at`, `UNIQUE(order_id, station)`) | Dipilih daripada 2 kolom `checker_*_printed_at` di `orders`: (1) station baru = nilai string baru tanpa ALTER; (2) UNIQUE constraint = penegakan sekali-cetak atomik di DB, kebal race; (3) `orders` tidak disentuh. `station` varchar bukan enum — scalable |
| Kolom baru **`tables.is_vip`** boolean default false | Mengikuti konvensi `is_active`/`is_available` |

Detail lengkap juga ditambahkan di `04-Database.md` (seksi "Perubahan Additive"). Migration: `2026_07_08_200001_create_checker_prints_table.php`, `2026_07_08_200002_add_is_vip_to_tables_table.php` — **sudah di-migrate** di environment ini.

### 18.2 File yang Dibuat

| File | Fungsi |
|---|---|
| `database/migrations/2026_07_08_200001_create_checker_prints_table.php` | Tabel `checker_prints` + UNIQUE |
| `database/migrations/2026_07_08_200002_add_is_vip_to_tables_table.php` | `tables.is_vip` |
| `app/Models/CheckerPrint.php` | Model baru (fillable order_id/station/printed_at, cast datetime, belongsTo Order) |

### 18.3 File yang Diubah

| File | Perubahan |
|---|---|
| `app/Models/Order.php` | ADDITIVE: relasi `checkerPrints(): HasMany` |
| `app/Models/Table.php` | ADDITIVE: `is_vip` di `$fillable` + cast boolean |
| `Station/KitchenController.php` | `index()`: eager load `checkerPrints`, `partition()` jadi `$unprinted`/`$printed`; `$unprinted` di-sort `[VIP desc, created_at asc]`; `checker()`: `CheckerPrint::create` dalam try/catch `UniqueConstraintViolationException` → cetak kedua `abort(403)` |
| `resources/views/kitchen/index.blade.php` | Dua seksi (heading "Belum Dicetak" + counter / "Sudah Dicetak" + counter); kartu printed redup (opacity) + tombol nonaktif "Sudah Dicetak"; badge VIP (crown, amber) + border `border-amber-400` + ring pada kartu VIP; banner dua varian hardcode purge-safe (VIP mencolok: amber pekat + crown pulse + teks "prioritaskan order ini" / reguler seperti semula). Polling/Web Audio/`POLL_INTERVAL` TIDAK diubah |
| `resources/views/station/checker.blade.php` | Blok `*** VIP ***` berbingkai (kondisional `$order->table->is_vip`) + CSS `.vip` — tetap shared ber-parameter |
| `app/Http/Requests/StoreTableRequest.php` + `UpdateTableRequest.php` | Rule `is_vip required|boolean` + `prepareForValidation` (absen → false) |
| `resources/views/admin/tables/partials/form.blade.php` | Toggle "Meja VIP" (Alpine, pola toggle status user Modul 2) + hint |
| `resources/views/admin/tables/index.blade.php` | Badge VIP (crown) di kartu meja |

**Route:** TIDAK ada perubahan (penandaan "dicetak" terjadi di route checker yang sudah ada).

### 18.4 Hasil Testing (smoke test tinker, rollback — DB tidak berubah)

18/18 PASS: VIP tampil sebelum reguler meski dibuat belakangan; seksi terpisah muncul sesuai kondisi; banner VIP mencolok; badge VIP di kartu; checker memuat `*** VIP ***` (reguler tidak); baris `checker_prints` tercatat; **cetak kedua ditolak 403**; order dicetak TIDAK hilang & pindah ke bawah; validasi `is_vip` (termasuk absen → false); badge + toggle VIP di halaman admin; rollback bersih.

> Catatan harness: DB dev berisi order aktif sisa pengujian manual user — asersi global seperti "tidak ada tombol Cetak Checker" bisa false-FAIL; selalu asersikan pada data smoke spesifik (posisi relatif terhadap heading).

### 18.5 Catatan untuk Modul 7 — Station Barista (WAJIB ikut aturan baru)

Selain catatan §17.10, Barista kini juga harus:
- **BR-014**: `checker()` barista memakai pola yang sama — `CheckerPrint::create(['station' => 'barista', ...])` + catch `UniqueConstraintViolationException` → 403. View checker shared SUDAH menampilkan VIP otomatis.
- **BR-015**: `index()` barista memakai `partition` + sort VIP→FIFO yang sama (filter `station='barista'`).
- Pertimbangkan mengekstrak logika bersama (query antrian + partition + sort + mark-printed) ke method/service bersama bila duplikasi Kitchen↔Barista terasa — diskusikan di STEP 1 Modul 7.

---

## 19. Modul 6 Partial Refresh — Polling Tanpa `location.reload()` — ✅ DONE

**Status:** ✅ Selesai. Smoke test 14/14 PASS dengan rollback **+ diverifikasi user di browser** (tanpa reload/kedipan, jam tetap jalan, order baru muncul otomatis, scroll tetap, lonceng hanya saat order baru, kartu pindah ke "Sudah Dicetak" tanpa reload, badge ter-update — tanpa bug).
**Permintaan user:** polling 3 detik; hanya daftar antrian yang diperbarui; header/jam/toggle/layout tidak di-render ulang; scroll tetap; lonceng sekali saat order baru; tanpa perubahan = tanpa update DOM; tetap Alpine (tanpa Livewire/WebSocket); business rule & DB tidak diubah.

### 19.1 Arsitektur (WAJIB ditiru Modul 7 Barista)

- **Fragment antrian dirender server-side** di partial baru `kitchen/partials/queue.blade.php` (banner + kelompok Belum/Sudah Dicetak + kartu placeholder). Dipakai dua arah: include awal di index (dalam `<div id="kitchen-queue">`) dan dikembalikan endpoint polling.
- **Endpoint `kitchen.queue-status` (route TIDAK berubah)** kini menerima `?signature=` milik klien dan mengembalikan JSON: `{ last_id, signature, unprinted_count, html? }`. **`html` hanya disertakan bila signature beda** → tanpa perubahan, klien tidak menyentuh DOM sama sekali.
- **`signature`** = md5 dari `[ids unprinted terurut, ids printed terurut]` — berubah bila order masuk/keluar/dibayar/dicetak/urutan berubah.
- **Klien (`stationQueue`)**: swap `innerHTML` container `#kitchen-queue` saja → hero/jam/toggle di luar container tidak pernah dirender ulang; scroll window otomatis tetap. Badge jumlah di hero reaktif via `x-text="unprintedCount"` (update angka, bukan re-render). Guard `polling` mencegah request tumpang tindih.
- **Lonceng SEKALI** hanya saat `last_id` naik (order/item baru); perubahan karena cetak/bayar mengubah signature tapi TIDAK membunyikan lonceng.
- `POLL_INTERVAL = 3000` — tetap satu-satunya konstanta.

### 19.2 File

| File | Perubahan |
|---|---|
| `resources/views/kitchen/partials/queue.blade.php` | **BARU** — fragment antrian (dipindah utuh dari index) |
| `resources/views/kitchen/index.blade.php` | Hero statis + badge reaktif; `#kitchen-queue` + include partial; script polling partial (fetch + swap kondisional, tanpa `location.reload()`) |
| `app/Http/Controllers/Station/KitchenController.php` | Refactor: private `queueData()` / `lastItemId()` / `signature()` dipakai `index()` & `queueStatus(Request)`; `queueStatus` kini mengembalikan fragment kondisional. `checker()` tidak berubah |

Route, business rule, DB: **tidak berubah**.

### 19.3 Hasil Testing (rollback — DB tidak berubah)

14/14 PASS: container + fragment dirender; `POLL_INTERVAL = 3000`; tidak ada `location.reload`; badge hero reaktif; hero di luar container; request tanpa signature dapat html+metadata; **signature sama → html tidak dikirim**; order baru → signature berubah + `last_id` naik + fragment memuat order baru + fragment tanpa hero; **dicetak → signature berubah tapi `last_id` tetap** (tanpa lonceng) + fragment menampilkan "Sudah Dicetak"; rollback bersih.

### 19.4 Catatan / Trade-off

| Item | Keterangan |
|---|---|
| "Lama Tunggu: Xm" di kartu | Hanya ter-update saat komposisi antrian berubah (konsekuensi "tanpa perubahan = tanpa update DOM" yang diminta). Bisa dibuat live client-side bila user minta |
| Swap `innerHTML` & Alpine | Fragment sengaja bebas dari komponen Alpine (murni HTML statis + link), jadi swap aman tanpa perlu `Alpine.initTree()` — pertahankan sifat ini saat mengubah fragment |
| Harness uji | DB dev bisa tanpa meja kosong — smoke test membuat meja sementara di dalam transaksi (`Table::create`), ikut rollback |

---

## 20. Modul 7 — Station Barista (FR-007) + Refactor `StationController` — ✅ DONE

**Status:** ✅ Selesai. Regression Kitchen 6/6 PASS + smoke Barista 14/14 PASS (rollback) **+ diverifikasi user di browser** (UI amber sesuai, partial refresh & lonceng normal, order campuran terpisah per station, VIP teratas, checker barista hanya item barista & sekali cetak, regression Kitchen tetap normal — tanpa bug).
**Keputusan user:** Opsi A — refactor agar TIDAK ada duplikasi Kitchen↔Barista.

### 20.1 Arsitektur Setelah Refactor

- **`Station\StationController` (abstract, BARU)** — seluruh logika station: `index()`, `queueStatus()` (partial refresh ber-signature), `checker()` (BR-014 sekali cetak via `checker_prints`), `queueData()` (partition Belum/Sudah Dicetak + sort VIP→FIFO BR-015), `signature()`, `lastItemId()`. Konvensi view diturunkan dari station: `"{station}.index"` & `"{station}.partials.queue"`.
- **`KitchenController` & `BaristaController`** = subclass tipis, HANYA `station(): string`. Menambah station baru = subclass baru + 2 view + 3 route.
- **`station/partials/queue-script.blade.php` (BARU)** — script polling shared; **`POLL_INTERVAL = 3000` kini benar-benar hanya ada DI SINI untuk semua station**. Parameter include: `pollUrl`, `containerId`. Kitchen & Barista sama-sama memakainya.
- Checker cetak tetap shared `station/checker.blade.php` (tinggal terima `station: 'barista'` — tanpa perubahan).
- **Perilaku Kitchen TIDAK berubah** — dibuktikan regression test (lihat 20.4).

### 20.2 File

**Dibuat (3):** `Station/StationController.php` (abstract base), `resources/views/barista/partials/queue.blade.php` (fragment amber), `resources/views/station/partials/queue-script.blade.php` (script shared).

**Diubah (5):**
| File | Perubahan |
|---|---|
| `Station/KitchenController.php` | ±100 baris → subclass 8 baris (`station() = 'kitchen'`) |
| `Station/BaristaController.php` | Placeholder → subclass (`station() = 'barista'`) |
| `resources/views/kitchen/index.blade.php` | Script inline → include script shared (markup lain tidak berubah) |
| `resources/views/barista/index.blade.php` | Placeholder → mirror kitchen index: hero brand + badge reaktif + jam + toggle suara (aksen `text-amber-700`), container `#barista-queue`, ikon `cup-soda`, teks "item minuman" |
| `routes/web.php` | Group `role:barista`: `GET /barista/queue-status → barista.queue-status`, `GET /orders/{order}/checker/barista → barista.checker` (SRS §7) |

### 20.3 Skema Amber Barista (pembeda dari Kitchen; layout identik)

Hero tetap gradient brand (aturan ketat). Amber di: chip nomor order (`bg-amber-100 text-amber-800`), counter Belum Dicetak, angka qty (`text-amber-700`), hover tombol Cetak Checker, tombol banner reguler (`bg-amber-600`), ikon placeholder. VIP tetap terbedakan (crown + border tebal `amber-400` + ring). Semua class literal hardcode (purge-safe).

### 20.4 Hasil Testing (rollback — DB tidak berubah)

**Regression Kitchen 6/6 PASS:** index dirender lengkap; partial refresh 3s via script shared tanpa `location.reload`; `queue-status` html kondisional; deteksi order baru (`last_id` naik + fragment); checker hanya item makanan; cetak kedua 403.

**Barista 14/14 PASS:** index dirender; order ber-item minuman tampil (termasuk order campuran); order makanan-saja TIDAK tampil; item makanan tidak ikut di kartu; BR-015 VIP dulu; aksen amber ada; script shared dengan URL barista; `queue-status` html kondisional; **checker barista independen dari kitchen pada order yang sama** (UNIQUE per station — kitchen sudah cetak, barista tetap bisa cetak pertama kali) dan hanya memuat item minuman; cetak kedua 403; checker VIP memuat `*** VIP ***`; order tanpa minuman → 404; order dicetak pindah ke Sudah Dicetak; rollback bersih.

> Catatan harness: `@js()` meng-escape slash (`barista\/queue-status`) — asersi `str_contains` atas URL harus memperhitungkan escape JSON.

### 20.5 Catatan untuk Modul 8 — Pembayaran Kasir (FR-008)

- **Scope:** `GET /cashier` (placeholder sudah ada di `Cashier\CashierController`) — cari meja → pilih order aktif → input nominal (saran **pecahan Rupiah**/denominasi) → hitung kembalian → simpan `payments` → `orders.status='paid'` + `paid_at` + meja `kosong` — semua dalam `DB::transaction()`. Route SRS §7: `GET /cashier`, `POST /payments`, `GET /orders/{id}/receipt` (struk 58mm), `GET /orders/{id}/invoice` (PDF DomPDF — FR-009, konfirmasi ke user apakah ikut modul ini).
- **BR-007** bayar ≥ total; **BR-011** 1 order 1 pembayaran (`payments.order_id` UNIQUE — pakai pola catch `UniqueConstraintViolationException` seperti BR-014); **BR-013** hanya order aktif; **BR-004/BR-005** setelah sukses order selesai + meja kosong + `paid_at` = penanda tunggal.
- **Figma acuan:** `docs/figma/kasir.png` (+ `traksaksi.png` bila halaman transaksi ikut).
- Struk 58mm bisa meniru pola `station/checker.blade.php` (standalone + auto-print).
- Sidebar kasir: link "Transaksi" masih `#`.
- `payments.received_by` = kasir login; `payment_method` default `cash`.

> **Update:** Modul 8 SUDAH selesai — lihat §21.

---

## 21. Modul 8 — Pembayaran Kasir (FR-008) + Struk 58mm — ✅ DONE

**Status:** Selesai. Smoke test 18/18 PASS dengan rollback (menunggu verifikasi user di browser).
**Figma acuan:** `docs/figma/kasir.png`.
**Keputusan user (STEP 1):** metode hanya Cash+QRIS (sesuai enum, Debit/Transfer Figma di-drop); struk 58mm ikut modul ini & **boleh reprint kapan saja** (tidak ikut BR-014); Invoice PDF + halaman Riwayat Transaksi admin (`traksaksi.png`) DITUNDA; pemilihan order server-side `?order={id}`; saran pecahan Rupiah; strip Riwayat Hari Ini; sidebar "Transaksi" tetap `#`.

### 21.1 File yang Dibuat

| File | Fungsi |
|---|---|
| `app/Http/Controllers/PaymentController.php` | Flat. `store()` — satu `DB::transaction()`: lock order → BR-013 (harus aktif) → BR-007 (bayar ≥ total DB) → `Payment::create` (BR-011 dijaga UNIQUE `payments.order_id` + catch `UniqueConstraintViolationException`) → BR-004/BR-005 (order `paid`, meja `kosong`, `paid_at` = now, `received_by` = kasir login, `change` dihitung server) → redirect `?paid={id}`. `receipt(Order)` — struk, `abort 404` bila order belum paid |
| `app/Http/Requests/StorePaymentRequest.php` | `order_id` required+exists, `payment_method` in `cash,qris`, `amount_paid` numeric ≥ 0; BR-007 sengaja di controller (terhadap total DB) |
| `resources/views/cashier/receipt.blade.php` | Struk 58mm standalone auto-print (pola checker): order_number, meja, customer, waktu `paid_at`, kasir, item+qty+harga+subtotal, TOTAL, Bayar (metode), Kembalian |

### 21.2 File yang Diubah

| File | Perubahan |
|---|---|
| `Cashier\CashierController.php` | Placeholder → `index(Request)`: order aktif FIFO + search (order_number/customer/nama meja), `$selected` via `?order={id}` (hanya status active), `$todayPayments` (paid_at hari ini, 8 terbaru), `$justPaid` via `?paid={id}` |
| `resources/views/cashier/index.blade.php` | Placeholder → halaman Figma: hero; error summary; kartu sukses pasca-bayar + tombol Cetak Struk; kiri = Cari Tagihan (search + grid kartu order, terpilih di-highlight) + strip Riwayat Hari Ini (chip = link reprint struk); kanan = panel Detail Pembayaran sticky (rincian item, subtotal, metode Cash/QRIS, nominal + **saran pecahan** Alpine `paymentPanel`, kembalian preview hijau/merah, preview struk mini, tombol Bayar & Selesaikan `:disabled` sampai nominal cukup) |
| `routes/web.php` | Group `role:kasir`: `POST /payments → payments.store`, `GET /orders/{order}/receipt → cashier.receipt` (SRS §7) + import `PaymentController` |

### 21.3 Business Rule yang Diimplementasikan

BR-007 (server, terhadap total DB — bukan input client), BR-011 (UNIQUE + catch race), BR-013 (status active + lock), BR-004 (order paid + meja kosong dalam transaksi yang sama), BR-005 (`paid_at` terisi — siap dipakai Dashboard/Laporan). Saran pecahan: "Uang Pas" + pembulatan total ke pecahan 1rb/2rb/5rb/10rb/20rb/50rb/100rb di atasnya (maks 5 tombol).

### 21.4 Hasil Testing (rollback — DB tidak berubah)

18/18 PASS: halaman kasir + panel kosong; panel detail via `?order=`; metode hanya Cash+QRIS; search menyaring; **BR-007 bayar kurang ditolak**; struk order belum dibayar 404; payment tercatat + kembalian server (7000) + `paid_at`/`received_by` benar; order paid; meja kosong; redirect `?paid=`; **bayar kedua ditolak**; struk memuat semua field wajib SRS + auto-print; **reprint OK**; muncul di Riwayat Hari Ini; hilang dari daftar tagihan aktif; metode `debit` ditolak validasi; rollback bersih.

### 21.5 Catatan untuk Modul Berikutnya

Sisa roadmap: **FR-010 Dashboard admin** (ganti placeholder; semua angka HANYA dari transaksi ber-`paid_at` — BR-005), **FR-011 Laporan** (Chart.js, bar peak gold, filter tanggal, export PDF DomPDF), **Invoice PDF (FR-009 sisa)** + **halaman Riwayat Transaksi** (`traksaksi.png` — halaman admin; sidebar admin "Transaksi" & "Reports" masih `#`; sidebar kasir "Transaksi" juga masih `#` — konfirmasi ke user siapa pemilik halaman riwayat). Konfirmasi urutan modul ke user. Data pembayaran kini nyata — placeholder dashboard admin (`admin/dashboard.blade.php`, angka dummy) sudah bisa diisi.

---

## 22. Modul 8 Penyempurnaan — Auto Print Struk + Pajak Dinamis (BR-016) — ✅ DONE

**Status:** Selesai. Smoke test 17/17 PASS dengan rollback (menunggu verifikasi user di browser).
**Keputusan user:** auto print via **iframe tersembunyi** (Opsi 1, anti popup-blocker); pajak Opsi B — diatur Admin, kasir hanya baca, snapshot di payment, BR-007 vs Grand Total.

**Business rule BARU (BR-016, disetujui user — didokumentasikan di sini, SRS tidak diedit):**

```
BR-016  Pajak dinamis:
        - Persentase pajak disimpan di settings (key 'tax_percent'), HANYA Admin yang
          bisa mengubah (halaman Settings). Default 0 bila belum diset.
        - Grand Total = Subtotal (orders.total) + Pajak. Pajak = round(subtotal × % / 100),
          dibulatkan ke rupiah terdekat, dihitung SERVER-SIDE.
        - BR-007 kini divalidasi terhadap Grand Total; kembalian = bayar − Grand Total.
        - % dan nominal pajak di-SNAPSHOT ke payments (tax_percent, tax_amount) —
          struk lama & laporan historis TIDAK berubah saat Admin mengubah pajak.
        - Dashboard/laporan berikutnya memakai total setelah pajak
          (= orders.total + payments.tax_amount, atau amount_paid − change).
```

### 22.1 Perubahan Skema (ADDITIVE — disetujui; sudah di-migrate)

| Perubahan | Alasan |
|---|---|
| Tabel baru **`settings`** (`key` varchar(50) UNIQUE, `value` varchar(255)) | Satu sumber data konfigurasi; scalable — Modul Settings penuh nanti tinggal tambah key tanpa migration. Default pajak 0 via fallback helper (tanpa seeder) |
| Kolom **`payments.tax_percent`** decimal(5,2) default 0 + **`payments.tax_amount`** decimal(12,2) default 0 | Snapshot pajak saat bayar; default 0 = backward compatible dengan pembayaran lama |

Migration: `2026_07_08_210001_create_settings_table.php`, `2026_07_08_210002_add_tax_to_payments_table.php`. Detail juga PERLU dibaca bersama seksi additive di `04-Database.md` §Modul 6.

### 22.2 File yang Dibuat (6)

`database/migrations/…210001_create_settings_table.php`; `…210002_add_tax_to_payments_table.php`; `app/Models/Setting.php` (helper `get`/`put`/`taxPercent()` — **satu-satunya pintu baca pajak**); `app/Http/Controllers/SettingController.php` (`edit`+`update`, admin); `app/Http/Requests/UpdateSettingsRequest.php` (`tax_percent` numeric 0–100, authorize admin); `resources/views/admin/settings/index.blade.php` (kartu "Pengaturan Pembayaran" ala `pengaturan.png`; kartu lain menyusul di Modul Settings).

### 22.3 File yang Diubah (7)

| File | Perubahan |
|---|---|
| `app/Models/Payment.php` | ADDITIVE: `tax_percent`, `tax_amount` di fillable+casts |
| `PaymentController.php` | `store()`: baca `Setting::taxPercent()` → `tax = round(total×%/100)` → grand → BR-007 vs grand → simpan snapshot → `change = bayar − grand` |
| `Cashier\CashierController.php` | Kirim `taxPercent` (baca-saja) ke view |
| `resources/views/cashier/index.blade.php` | Panel: baris Subtotal / Pajak (X%) / **Grand Total**; Alpine `paymentPanel` menerima grand total (saran pecahan & kembalian otomatis ikut pajak); preview struk mini 5 baris; **iframe tersembunyi auto-print** dirender saat `?paid={id}` (tombol jadi "Cetak Ulang Struk" sebagai fallback) |
| `resources/views/cashier/receipt.blade.php` | Baris Subtotal / Pajak (%) / **GRAND TOTAL** / Bayar / Kembalian — dari SNAPSHOT payment |
| `routes/web.php` | Group `role:admin`: `GET /settings → admin.settings.edit`, `PUT /settings → admin.settings.update` |
| `components/sidebar.blade.php` | Link "Settings" admin diaktifkan + active state |

### 22.4 Hasil Testing (17/17 PASS, rollback — DB tidak berubah)

Pajak default 0 tanpa seeder; admin set 10% tersimpan; halaman settings menampilkan nilai; pajak >100% ditolak; **kasir tidak bisa mengubah pajak** (authorize gagal); panel kasir menampilkan Pajak (10%) + Grand Total dengan nilai benar; **BR-007 vs grand** (bayar = subtotal ditolak); snapshot 10% + nominal tersimpan; kembalian dari grand; struk memuat 6 baris; **pajak diubah ke 7% → struk lama TETAP 10%** (snapshot) & pembayaran baru otomatis 7%; pajak 0% → grand = subtotal; iframe auto-print dirender saat `?paid=`; tombol fallback tetap ada; rollback bersih (settings/orders/tables).

### 22.5 Catatan Teknis PENTING (jebakan Blade yang ditemukan)

**JANGAN mencampur blok `@php` … pasangan penutupnya dengan bentuk inline `@php(...)` dalam SATU file Blade** — compiler memasangkan kemunculan pertama dengan penutup pertama secara mentah, menelan markup di antaranya (error "unexpected token else"). Bahkan menulis kata direktifnya di dalam komentar `{{-- --}}` ikut tertangkap. Di file yang sudah memakai `@php(...)` inline, gunakan inline untuk semua statement tambahan. (Sudah diterapkan di `cashier/index.blade.php`.)

### 22.6 Catatan Modul Berikutnya

- Dashboard (FR-010) & Laporan (FR-011): pendapatan = **total setelah pajak** → `SUM(amount_paid − change)` dari `payments` ber-`paid_at` (BR-005 + BR-016).
- Halaman Settings siap diperluas (key-value) untuk Modul Settings penuh (`pengaturan.png`).

---

## 23. Modul 8 Penyempurnaan 2 — Flow QRIS Terpisah (BR-017) — ✅ DONE

**Status:** Selesai. Smoke test 12/12 PASS dengan rollback (menunggu verifikasi user di browser).
**Tanpa migration/route baru** — murni validasi + perhitungan + tampilan.

**Business rule BARU (BR-017, spesifikasi user — didokumentasikan di sini, SRS tidak diedit):**

```
BR-017  Pembayaran QRIS selalu bernilai TEPAT sebesar Grand Total.
        - Kasir tidak dapat menginput/mengubah nominal; tidak ada kembalian (change = 0).
        - Server MENGABAIKAN amount_paid dari client saat payment_method = qris:
          amount_paid di-exclude dari data tervalidasi (exclude_if) dan di-set = grand_total.
        - Cash: flow lama tetap (input nominal, uang pas, pecahan, kembalian, BR-007 vs grand).
        - Struk QRIS: tanpa baris Bayar/Kembalian → "Metode Pembayaran: QRIS" +
          "Status Pembayaran: LUNAS". Struk cash tidak berubah.
```

### 23.1 File yang Diubah (4 — tidak ada file/route/migration baru)

| File | Perubahan | Alasan teknis |
|---|---|---|
| `app/Http/Requests/StorePaymentRequest.php` | `amount_paid` → `['exclude_if:payment_method,qris', 'required', 'numeric', ...]` | `exclude_if` MEMBUANG field dari data tervalidasi saat QRIS (bukan sekadar nullable) — server mustahil memakai nominal client; saat cash tetap wajib |
| `app/Http/Controllers/PaymentController.php` | `$isQris ? $amountPaid = $grandTotal : dari input`; BR-007 hanya dicek utk cash; `change = $amountPaid − $grandTotal` (QRIS otomatis 0) | Semua nilai QRIS ditentukan server di dalam transaksi yang sama; snapshot pajak (BR-016) tidak tersentuh |
| `resources/views/cashier/index.blade.php` | Blok Nominal Bayar + saran pecahan + baris Kembalian dibungkus `x-show="method === 'cash'"`; badge hijau "Pembayaran QRIS — Nominal dibayar otomatis sesuai Grand Total" `x-show="method === 'qris'"`; preview struk mini kondisional (`template x-if`): cash = Bayar+Kembalian, qris = Metode+LUNAS; `canPay`: qris selalu true, cash tetap `sufficient`; hint BR-007 hanya utk cash | Alpine murni sesuai aturan proyek; Grand Total selalu tampil di kedua mode |
| `resources/views/cashier/receipt.blade.php` | Kondisional dari SNAPSHOT `payment_method`: qris = "Metode Pembayaran: QRIS" + "Status Pembayaran: LUNAS" (tanpa Bayar/Kembalian); cash = tetap | Struk lama cash tidak berubah; QRIS lama (pra-BR-017, ada kembalian) tetap akurat karena kondisi berbasis metode, nilai dari snapshot |

### 23.2 Hasil Testing (12/12 PASS, rollback — DB tidak berubah)

QRIS tanpa `amount_paid` → lolos validasi, `amount_paid` = grand total (55.000, termasuk pajak 10%), `change` = 0; **QRIS dengan nominal ngawur 999 juta dari client → DIABAIKAN**, tetap grand total; struk QRIS memuat Metode+LUNAS **tanpa** Bayar/Kembalian tapi tetap ada Subtotal/Pajak/Grand Total; regression cash: tanpa nominal ditolak, kurang dari grand ditolak (BR-007), kembalian benar, struk cash tetap Bayar+Kembalian tanpa LUNAS; panel memuat badge hijau QRIS + blok kondisional cash/qris + Grand Total selalu tampil; rollback bersih.

### 23.3 Catatan

- Pembayaran QRIS **lama** (dibuat sebelum BR-017, mungkin ber-kembalian) tetap tampil benar di struk karena semua nilai dari snapshot; hanya barisnya kini format Metode+LUNAS.
- Auto print (iframe), pajak dinamis, waiter/kitchen/barista: tidak tersentuh.

---

## 24. Modul 9 — Dashboard Admin (FR-010) — ✅ DONE

**Status:** Selesai. Smoke test 15/15 PASS dengan rollback (menunggu verifikasi user di browser).
**Read-only murni, data asli DB, tanpa dummy.** Route tidak berubah (`admin.dashboard`, group `role:admin`).

### 24.1 Keputusan Teknis (disetujui user)

- **Chart.js via npm** (`npm install chart.js`), di-bundle Vite: `resources/js/app.js` meng-import `chart.js/auto` → `window.Chart` — **offline-safe** (POS jaringan lokal, tanpa CDN). Bundle `app.js` menjadi ±300 KB (gzip ±105 KB) — wajar. `window.Chart` siap dipakai ulang Laporan (FR-011).
- Cash/QRIS = **bulan ini** (+persentase dari total keduanya).
- Layout: Baris 1 (Pendapatan Hari/Bulan, Order Hari Ini, Order Aktif) → strip 4 mini-card (Order Bulan Ini, Total Menu/Meja/User) → Baris 2 (Cash/QRIS bulan ini, Pajak Hari/Bulan) → Baris 3 (bar chart 7 hari, warna brand, tooltip & sumbu Y berformat Rupiah) → Baris 4 (Menu Terlaris top-5, peringkat #1 badge gold; Aktivitas Terbaru 5 pembayaran).
- Empty state per seksi (chart / menu terlaris / aktivitas) dengan kartu putus-putus; angka Rp0 aman.
- Tidak ada file Figma dashboard admin di `docs/figma/` — layout mengikuti spesifikasi eksplisit user + design system project.
- Tanpa service/helper baru — agregat sederhana cukup di controller.

### 24.2 Query Statistik (±13 query total, tanpa N+1)

| Data | Teknik |
|---|---|
| Pendapatan+pajak+order hari ini / bulan ini | 2 query agregat `selectRaw('SUM(amount_paid - change), SUM(tax_amount), COUNT(*)')` atas `payments` ber-`paid_at` (BR-005; nilai = grand total termasuk pajak BR-016; pajak dari SNAPSHOT `tax_amount`) |
| Cash vs QRIS bulan ini | 1 query `GROUP BY payment_method`; persentase dihitung PHP |
| Menu Terlaris top-5 | 1 query agregat `OrderItem` `SUM(qty)` `GROUP BY menu_id` `whereHas('order', status paid)` + `with('menu:id,name')` |
| Grafik 7 hari | 1 query `GROUP BY DATE(paid_at)`; hari kosong diisi 0 di PHP; label `d/m` |
| Aktivitas terbaru | `Payment::with('order.table')->latest('paid_at')->take(5)` |
| Master counts | 4 count (menus/tables/users/order aktif) |

> `change` adalah reserved word MySQL — di `selectRaw` WAJIB di-backtick: `` SUM(amount_paid - `change`) ``.

### 24.3 File yang Diubah (4 — tidak ada file baru)

`Admin/DashboardController.php` (placeholder → seluruh query di atas); `resources/views/admin/dashboard.blade.php` (placeholder dummy → layout penuh + `@push('scripts')` inisialisasi Chart.js bar chart); `resources/js/app.js` (+import Chart); `package.json` (+chart.js). `npm run build` sukses.

### 24.4 Hasil Testing (15/15 PASS, rollback — DB tidak berubah)

Empty state murni (semua order dihapus DALAM transaksi): chart/menu/aktivitas menampilkan pesan kosong + Rp0 tanpa error. Data terkontrol (pajak 10%, 1 cash berkembalian + 1 qris + 1 order aktif): **13 query** utk seluruh dashboard (≤15, tanpa N+1); pendapatan hari/bulan = jumlah grand total (kembalian TIDAK ikut); cash & qris bulan ini + persentase (52.1%/47.9%) benar; pajak hari/bulan dari snapshot benar; Order Hari Ini = 2 (order aktif tidak dihitung); Menu Terlaris qty 4 — **item order aktif tidak dihitung**; chart berisi angka hari ini; aktivitas memuat 2 pembayaran dengan grand total benar, order belum dibayar tidak muncul; rollback mengembalikan data asli user.

> Catatan harness: untuk uji empty-state / angka deterministik di DB dev yang berisi data manual, hapus `orders` (bukan hanya `payments`) di dalam transaksi — FK cascade membersihkan payments/order_items/checker_prints, dan Menu Terlaris ikut bersih.

### 24.5 Hal yang Perlu Diperhatikan Modul 10 (Laporan FR-011 / sisa FR-009)

- **FR-011 Laporan:** grafik batang Chart.js (`window.Chart` SUDAH tersedia), **batang peak di-highlight gold** (SRS), filter rentang tanggal, export PDF DomPDF. Figma: `laporan.png`. Pola query = §24.2 dengan `whereBetween` dari filter tanggal. Pendapatan tetap `SUM(amount_paid − change)`.
- **Sisa FR-009:** Invoice PDF (DomPDF — package sudah ada di composer sesuai PROJECT_CONTEXT, verifikasi dulu) + halaman **Riwayat Transaksi** (`traksaksi.png`, admin; sidebar admin "Transaksi" & "Reports" masih `#`; sidebar kasir "Transaksi" juga masih `#`).
- Konfirmasi urutan Modul 10 ke user (Laporan dulu atau Riwayat Transaksi+Invoice dulu).

> **Update:** FR-009 SUDAH selesai penuh di Modul 11 — lihat §25.

---

## 25. Modul 11 — Riwayat Transaksi + Invoice PDF (FR-009) — ✅ DONE

**Status:** ✅ Selesai. Smoke test 18/18 PASS dengan rollback **+ diverifikasi user di browser** (riwayat, filter, detail, invoice PDF, reprint, snapshot pajak, middleware role, integrasi kasir — semua benar, tanpa bug).
**Read-only murni; TANPA migration/perubahan DB.** DomPDF: `barryvdh/laravel-dompdf` ^3.1 (sudah terpasang, tidak install apa pun).

### 25.1 Keputusan Teknis (disetujui user)

- **Sumber data = `payments`** (route `{payment}`) → riwayat otomatis hanya order dibayar; order unpaid tidak punya baris payment → **404 struktural** via route model binding.
- **Nomor invoice deterministik** tanpa kolom baru: `INV-{Ymd paid_at}-{payment_id 4 digit}` — stabil selamanya untuk reprint (private `invoiceNumber()` di controller).
- **Alamat cafe**: `Setting::get('store_address', 'Jl. Kopi Nusantara No. 1, Indonesia')` — identitas toko (bukan nilai finansial), kelak diatur Modul Settings tanpa mengubah kode invoice.
- **Semua nilai finansial dari SNAPSHOT payment** (`tax_percent/tax_amount/amount_paid/change`; grand = `amount_paid − change`; subtotal = `orders.total`; harga item dari snapshot `order_items`) — TIDAK pernah membaca Setting pajak. Reprint kebal perubahan pajak (teruji: Setting diubah 10%→99%, tampilan tetap 10%).
- Search satu field (`order_number` ATAU `customer_name`) + filter tanggal awal/akhir (`whereDate`) + metode; sort `latest(paid_at)`; `paginate(10)->withQueryString()`; kartu ringkasan hasil filter (1 query agregat `clone $base` SEBELUM `with()` — hindari eager load pada baris agregat).
- Detail & invoice QRIS konsisten BR-017 (LUNAS tanpa baris Kembalian di detail; invoice menampilkan ringkasan lengkap + metode). Struk thermal kasir Modul 8 tidak disentuh.
- Template PDF = Blade khusus (inline CSS, font DejaVu Sans, tanpa Tailwind/Vite/JS — DomPDF tidak mengeksekusinya); `Pdf::loadView(...)->stream('INV-... .pdf')`.

### 25.2 File

**Dibuat (4):** `app/Http/Controllers/TransactionController.php` (flat: `index`/`show`/`invoice`), `resources/views/admin/transactions/index.blade.php` (hero + 3 kartu ringkasan hasil filter + form filter GET + tabel 10 kolom + badge metode + empty state + pagination), `show.blade.php` (header info + tabel item + panel ringkasan sticky + tombol Cetak Invoice), `invoice.blade.php` (PDF A4).

**Diubah (2):** `routes/web.php` (3 route di group `role:admin`: `admin.transactions.index|show|invoice`), `components/sidebar.blade.php` (link "Transaksi" admin aktif + active state).

### 25.3 Hasil Testing (18/18 PASS, rollback — DB tidak berubah)

**6 query** untuk index (eager, tanpa N+1); daftar tampil (2 pembayaran); order aktif TIDAK muncul; Total Pendapatan = grand (kembalian tidak ikut); pagination benar; filter tanggal (range kemarin kosong / hari ini ada); filter metode cash & qris; search nomor order & nama customer; detail lengkap (header+item+Pajak 10%+Bayar+Kembalian+Lunas); detail QRIS = LUNAS tanpa Kembalian; template invoice memuat seluruh field wajib (nama+alamat cafe, nomor invoice/order, tanggal, customer/meja/kasir, item, subtotal/pajak%/nominal/grand/bayar/kembalian/metode, footer); **DomPDF menghasilkan `%PDF` valid (±880 KB)**; **snapshot kebal perubahan Setting (10% tetap, bukan 99%)**; reprint pasca-perubahan Setting berhasil; payment tidak ada → 404; rollback bersih.

### 25.4 Catatan Modul Berikutnya — Laporan Penjualan (FR-011, FR terakhir)

- Grafik batang Chart.js (`window.Chart` sudah ter-bundle dari Modul 9), **batang peak di-highlight GOLD** (SRS eksplisit), filter rentang tanggal, **export PDF DomPDF** (pola `Pdf::loadView` §25.1 bisa ditiru; ingat: template PDF tanpa Tailwind, dan chart TIDAK bisa dirender DomPDF — export PDF berisi tabel data, bukan gambar chart, kecuali kirim base64 image dari client).
- Figma: `laporan.png`. Pendapatan tetap `SUM(amount_paid − change)`, pajak dari snapshot. Reserved word `change` wajib backtick di selectRaw (§24.2).
- Sidebar admin "Reports" masih `#`. Setelah FR-011: seluruh FR SRS selesai — tersisa Modul Settings penuh (opsional) & polising.

> **Update:** FR-011 SUDAH selesai di Modul 12 — lihat §26.

---

## 26. Modul 12 — Laporan Penjualan (FR-011) — ✅ DONE — SELURUH FR SRS SELESAI

**Status:** Selesai. Smoke test 15/15 PASS dengan rollback (menunggu verifikasi user di browser).
**Read-only; TANPA migration/DB/package baru** (Chart.js dari Modul 9, DomPDF dari Modul 11).

### 26.1 Keputusan Teknis (disetujui user)

- Filter rentang tanggal, default **7 hari terakhir**, + preset link GET: 7 Hari / 30 Hari / Bulan Ini; rentang terbalik dinormalkan otomatis.
- **Peak GOLD (SRS)**: warna batang dihitung **server-side** (array `backgroundColor`: nilai max → `#F59E0B`, sisanya brand `#7C4A2D`) — deterministik & ter-uji (tepat 1 batang gold).
- Kolom "Trend" Figma **dilewati** (tidak ada data pembanding periode; tanpa logika/DB tambahan).
- **Export PDF tanpa grafik** (DomPDF tidak merender canvas) → diganti **tabel Penjualan Harian** (tanggal, jumlah order, pendapatan; baris peak disorot gold) + ringkasan + distribusi metode + produk terlaris LENGKAP — semuanya menghormati filter yang sama.
- `reportData()` privat dipakai `index()` DAN `export()` — halaman & PDF mustahil beda angka.
- Produk terlaris: agregat `OrderItem` (`SUM(qty)`, `SUM(subtotal)` snapshot) dengan `whereHas('order.payment', whereBetween paid_at)` — hanya order dibayar dalam rentang; `paginate(5)` di halaman, `get()` penuh di PDF; `with('menu.category')`.
- Pendapatan `SUM(amount_paid − `change`)`, pajak `SUM(tax_amount)` — konsisten BR-005/BR-016.

### 26.2 File

**Dibuat (3):** `app/Http/Controllers/ReportController.php` (flat: `index`/`export` + private `reportData`/`topProductsQuery`); `resources/views/admin/reports/index.blade.php` (hero + Unduh Laporan + filter & preset + 4 kartu [Pendapatan/Pesanan/Rata-rata/Produk Terlaris #1] + bar chart peak gold + panel distribusi Cash/QRIS ber-progress-bar + Total Pajak Terkumpul + tabel produk terlaris paginate 5 + empty state per seksi); `resources/views/admin/reports/pdf.blade.php` (A4, pola invoice).

**Diubah (2):** `routes/web.php` (`GET /reports → admin.reports.index`, `GET /reports/export → admin.reports.export`, group `role:admin` — sesuai SRS §7); `components/sidebar.blade.php` (link "Reports" aktif — **link `#` terakhir di sidebar admin kini habis**).

### 26.3 Hasil Testing (15/15 PASS, rollback — DB tidak berubah)

10 query utk seluruh halaman (eager, tanpa N+1); Total Pendapatan benar (grand, kembalian tidak ikut); Total Pesanan = 2 (order aktif tidak dihitung); Rata-rata benar; kartu Produk Terlaris benar; **chart 7 batang dengan TEPAT 1 batang GOLD di peak**; data chart memuat kedua hari (payment kemarin via mundurkan `paid_at` di harness); distribusi Cash/QRIS + persentase; tabel produk terlaris qty & pendapatan snapshot (order aktif tidak ikut); filter rentang bekerja (hari-ini-saja mengeksklusi kemarin); empty state rentang 2020; **export PDF valid `%PDF` (±880 KB)**; template PDF memuat ringkasan + tabel harian ber-baris peak + metode + produk; export menghormati filter (2020 = Rp0); rollback bersih.

### 26.4 Status Proyek & Catatan Lanjutan

**Seluruh FR SRS (FR-001–FR-013) + BR-001–BR-017 SELESAI.** Yang tersisa bersifat opsional/polishing:
- **Modul Settings penuh** (`pengaturan.png`): profil toko (nama/alamat → dipakai invoice via `Setting::get('store_address')`), jam operasional, preferensi — infrastruktur key-value `settings` sudah siap.
- Technical debt lama yang masih terbuka: duplikat `export default` di `tailwind.config.js` (§13.8); label "Lama Tunggu" station hanya update saat komposisi berubah (§19.4); badge kosong meja emerald vs krem Figma (§14.8); `01-PRD.md` tidak ada di repo.
- Jika deploy ke environment baru: `php artisan migrate`, `storage:link`, `npm run build`, seeder.
