# PROJECT CONTEXT — Pitou Cafe POS

**Project:** Pitou Cafe POS
**Version:** 1.0
**Status:** Active Development

---

# 1. Project Overview

Pitou Cafe adalah aplikasi Point of Sale (POS) berbasis web untuk Coffee Shop.

Project ini dikembangkan secara modular menggunakan dokumentasi sebagai sumber kebenaran (Source of Truth).

Semua implementasi WAJIB mengikuti dokumentasi proyek.

Urutan prioritas:

1. 02-SRS.md (Business Rules & Functional Requirements)
2. 04-Database.md
3. 03-ERD.md
4. 01-PRD.md
5. Desain Figma (UI saja)

Behavior mengikuti SRS.

Tampilan mengikuti Figma.

---

# 2. Tech Stack

Backend
- Laravel 13
- PHP 8.3+
- MySQL

Frontend
- Blade
- Tailwind CSS v3
- Alpine.js

Package
- Blade Lucide Icons
- DomPDF

Development
- Vite
- Laragon

---

# 3. Coding Standard

Gunakan standar Laravel.

Selalu gunakan:

- Resource Controller
- Form Request Validation
- Eloquent Relationship
- Route Model Binding
- Named Route
- Blade Component jika diperlukan
- Service Class bila logic mulai besar

Jangan membuat kode yang duplikat.

Usahakan reusable.

Ikuti PSR-12.

---

# 4. Database Rules

Database sudah final.

JANGAN mengubah:

- migration
- model
- relasi
- enum
- foreign key
- business rule

kecuali diminta secara eksplisit.

Jika merasa database kurang, JANGAN langsung mengubah.

Berikan alasan terlebih dahulu.

---

# 5. Business Rules

Seluruh Business Rule berasal dari:

docs/02-SRS.md

Seluruh Functional Requirement berasal dari:

docs/02-SRS.md

Jangan membuat behavior baru yang tidak ada di SRS.

---

# 6. UI Rules

Selalu mengikuti desain pada folder:

docs/figma/

Gunakan file sesuai modul yang sedang dikerjakan.

Prioritas:

- Layout
- Spacing
- Typography
- Color
- Border Radius
- Shadow
- Icon
- Responsive

Tidak perlu pixel-perfect 100%, tetapi harus sangat mirip.

---

# 7. Brand Identity

Primary

#7C4A2D

Secondary

#A9714B

Background

#FAF6F0

Hero Header wajib menggunakan:

bg-gradient-to-br from-brand to-brand-light

Font:

- Inter
- Playfair Display

---

# 8. UI Component Rules

Gunakan komponen yang konsisten.

Misalnya:

Card

Button

Modal

Badge

Input

Table

Pagination

Search

Alert

Semua halaman harus memiliki style yang sama.

---

# 9. Authentication

Manual Authentication.

Bukan Breeze.

Bukan Jetstream.

Bukan Livewire.

Login berdasarkan role.

Role:

- admin
- waiters
- kitchen
- barista
- kasir

Redirect sesuai role.

Role middleware sudah tersedia.

---

# 10. Current Project Status

Sudah selesai:

✅ Setup Project

✅ Authentication

✅ Dashboard per Role

✅ Database

✅ Migration

✅ Seeder

✅ Model

✅ Modul 1
Admin
- CRUD Menu
- CRUD Category

✅ Modul 2
Admin
- CRUD User

Belum selesai:

⬜ Modul 3
Waiters

⬜ Modul 4
Kitchen

⬜ Modul 5
Barista

⬜ Modul 6
Kasir

⬜ Modul 7
Reports

⬜ Modul 8
Settings

⬜ Modul 9
Printing

⬜ Modul 10
Realtime

---

# 11. Development Workflow

Setiap modul WAJIB mengikuti langkah berikut:

STEP 1

Analisis:

- PRD
- SRS
- ERD
- Database
- Figma

STEP 2

Jelaskan:

- daftar file yang dibuat
- daftar file yang diubah
- alasan implementasi

STEP 3

Implementasi.

STEP 4

Self Review.

Pastikan seluruh Acceptance Criteria terpenuhi.

---

# 12. Coding Constraints

Jangan:

- install package baru tanpa izin
- mengubah struktur project tanpa izin
- mengubah migration tanpa izin
- mengubah database tanpa izin
- mengubah business rule tanpa izin

Jika ada masalah:

Jelaskan dulu.

Jangan langsung mengubah arsitektur.

---

# 13. Output Rules

Sebelum coding:

Selalu jelaskan:

- Analisis
- Pendekatan
- File yang diubah

Sesudah coding:

Berikan checklist:

✅ Acceptance Criteria

✅ Business Rules

✅ Validasi

✅ Testing Manual

Jika ada asumsi, tuliskan dengan jelas.

Jangan membuat asumsi diam-diam.

---

# 14. Documents

Selalu gunakan:

docs/01-PRD.md

docs/02-SRS.md

docs/03-ERD.md

docs/04-Database.md

Folder Figma:

docs/figma/

Gunakan file Figma sesuai modul yang sedang dikerjakan.
