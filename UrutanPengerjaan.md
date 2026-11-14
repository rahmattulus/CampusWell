# Urutan Pengerjaan & Tracking Progres — CampusWell

Dokumen hidup untuk melacak progres. Siapa pun yang melanjutkan pekerjaan cukup membaca bagian **1** dan **2**, lalu mulai dari tugas yang ditunjuk.

- **Stack:** Laravel (PHP) + Blade + Tailwind CSS (Alpine.js; Livewire opsional untuk chat)
- **Dokumen rujukan:** Konsep Proyek · Spesifikasi CDS · SRS · `DesainUI.md` (kode halaman M-xx, D-xx, A-xx merujuk ke sana)
- **Target:** 4 minggu · **Tanggal mulai:** `____` · **Terakhir diperbarui:** `____` oleh `____`

---

## 1. Cara Memakai Dokumen Ini

**Status tugas**

| Tanda | Arti |
| :--- | :--- |
| `[ ]` | Belum dikerjakan |
| `[~]` | Sedang dikerjakan (tulis inisial + tanggal di akhir baris) |
| `[x]` | Selesai dan sudah digabung ke branch utama |
| `[!]` | Terblokir (tulis penyebab di akhir baris) |

**Peran:** `FE` frontend · `CDS` mesin CDS · `CHAT` telemedicine/chat · `ALL` seluruh tim.

**Aturan memilih tugas berikutnya**
1. Buka bagian **2. Titik Mulai Saat Ini** dan baca catatan serah terima terakhir.
2. Pilih tugas `[ ]` pertama sesuai perananmu yang **semua dependensinya (`butuh:`) sudah `[x]`**.
3. Ubah menjadi `[~]` + inisial + tanggal sebelum mulai (agar tidak bentrok).
4. Setelah selesai: ubah ke `[x]`, perbarui tabel ringkasan (bagian 3), tambahkan satu entri di **Log Serah Terima** (bagian 9), lalu perbarui bagian 2.

**Definisi selesai (Definition of Done)** — tugas boleh `[x]` bila:
- Berjalan di lokal tanpa galat dan sudah di-merge lewat pull request.
- UI sesuai `DesainUI.md` (token warna, komponen, keadaan memuat/kosong/galat).
- Halaman mahasiswa dicek di lebar 360–430 px; halaman dokter/admin di ≥ 1280 px.
- Pembatasan akses peran (RBAC) berlaku bila menyentuh data.
- Untuk logika CDS: ada uji otomatis dan nilai ambang berasal dari konfigurasi, bukan ditulis di kode.

**Konvensi commit:** `[ID-TUGAS] pesan singkat`, mis. `[HAB-02] modal form habit tracker`.

---

## 2. Titik Mulai Saat Ini

> **Perbarui bagian ini setiap selesai satu tugas.**

- **Posisi terakhir:** Fase 1 & Fase 2 selesai (Database bersih 100% tanpa tabel bawaan Laravel; Fondasi UI, Layouts & Komponen Blade siap sesuai DesainUI.md).
- **Mulai dari:** Fase 3 (`AUTH-01` s.d. `AUTH-05` Autentikasi & RBAC), lalu Fase 4 (Habit Tracker & CDS).
- **Bisa dikerjakan paralel sekarang:**
  - `FE` → `AUTH-01`–`AUTH-03`, `HAB-01`, `HAB-02`
  - `CDS` → `AUTH-04` (middleware RBAC), `CDS-01` (kontrak & kerangka service), `CDS-03` (RBR v2.1)
  - `CHAT` → `TEL-01` (status & jadwal dokter)
- **Blocker aktif:** Validasi aturan CDS oleh ahli (`DOC-06`), batas akhir minggu ke-2. Hingga selesai, sistem memakai aturan v2.1.0 sebagai draf.

---

## 3. Ringkasan Progres

| Fase | Nama | Peran utama | Target minggu | Selesai |
| :--- | :--- | :--- | :--- | :--- |
| 0 | Dokumen & desain | ALL | 1 | 5 / 7 |
| 1 | Setup proyek | ALL | 1 | 4 / 5 |
| 2 | Fondasi UI | FE | 1 | 7 / 7 |
| 3 | Autentikasi & RBAC | FE + CDS | 2 | 0 / 5 |
| 4 | Habit Tracker & CDS (inti) | FE + CDS | 2 | 0 / 10 |
| 5 | Riwayat & grafik | FE | 2–3 | 0 / 3 |
| 6 | Telemedicine sisi mahasiswa | CHAT + FE | 3 | 0 / 8 |
| 7 | Dashboard dokter | CHAT + FE | 3 | 0 / 8 |
| 8 | Admin | FE + CDS | 3–4 | 0 / 5 |
| 9 | CBR & dataset Kaggle (pendukung) | CDS | 3–4 | 0 / 5 |
| 10 | Pengujian & rilis | ALL | 4 | 0 / 5 |

*Perbarui kolom "Selesai" saat tugas dicentang.*

---

## 4. Kontrak Antar-Modul (Spesifikasi CDS v2.1)

Pakai data tiruan (mock) sesuai kontrak ini sampai modul aslinya siap.

**Input `CdsEvaluatorService::evaluate(array $todayLog, array $historyLogs7Days, ?UserProfile $profile = null)`**
```json
{
  "sleep_hours": 5.0,
  "meal_freq": 2,
  "food_categories": ["Pedas", "High-Caffeine"],
  "late_eating_hours": 1,
  "stress_level": 4,
  "complaints": ["perih", "lelah"],
  "freshness": 2
}
```

**Output**
```json
{
  "final_level": "PERLU_PERHATIAN",
  "fatigue_score": 4,
  "fatigue_level": "PERLU_PERHATIAN",
  "gastric_score": 5,
  "gastric_level": "PERLU_PERHATIAN",
  "rules_triggered": ["F-01", "F-02", "M-02", "M-K1"],
  "alerts": [
    {
      "type": "FATIGUE",
      "title": "Risiko Kelelahan Ekstrem & Burnout",
      "score": 4,
      "rule_ids": ["F-01", "F-02"],
      "reasons": ["Kurang tidur beruntun dan tingkat kesegaran bangun rendah"]
    },
    {
      "type": "GASTRIC",
      "title": "Risiko Gangguan Asam Lambung / Maag",
      "score": 5,
      "rule_ids": ["M-02", "M-K1"],
      "reasons": ["Keluhan perih lambung disertai pola makan pemicu"]
    }
  ],
  "cbr": {
    "enabled": false,
    "result": null,
    "top_similarity": null
  },
  "rules_version": "2.1.0",
  "cooldown_suppressed": false
}
```
`final_level` ∈ `NORMAL` · `PERHATIAN_MANDIRI` · `PERLU_PERHATIAN`.

**Lampiran konsultasi (tiket):** ringkasan habit 7 hari + daftar `alerts` yang terpicu.

---

## 5. Fase 0 — Dokumen & Desain

- [x] **DOC-01** Konsep proyek (blueprint)
- [x] **DOC-02** Spesifikasi CDS (RBR + CBR)
- [x] **DOC-03** SRS
- [x] **DOC-04** `DesainUI.md`
- [x] **DOC-05** Prototipe form parameter dan alert untuk ahli
- [ ] **DOC-06** Validasi aturan RBR oleh ahli (dosen keperawatan) — `CDS` + `ALL` · **batas: akhir minggu 2**
- [ ] **DOC-07** Perbarui spesifikasi CDS (v2.1) dan `DesainUI.md` setelah parameter final — butuh: DOC-06

## 6. Fase 1 — Setup Proyek

- [x] **SET-01** Repositori Git, branch `main`, aturan PR dan review — `ALL` (Dikerjakan mandiri oleh user)
- [x] **SET-02** Instal Laravel, konfigurasi `.env`, koneksi database (MySQL `campuswell`) — `ALL`
- [x] **SET-03** Pasang Tailwind v4, token warna/bayangan/radius, font Plus Jakarta Sans, Alpine.js, Lucide — `FE`
- [x] **SET-04** Migrasi skema dasar: `users` (+peran, NIM, status verifikasi), `user_profiles`, `doctors`, `habit_logs`, `cds_evaluations`, `alert_dismissals`, `consult_tickets`, `chat_messages`, `medical_records`, `cds_cases`, `cds_config_versions`, `audit_logs` — `CDS` + `CHAT`
- [x] **SET-05** Seeder: akun demo untuk 3 peran (Admin, 2 Dokter, 2 Mahasiswa) + CDS config v2.1.0 + data habit contoh 7 hari — `ALL`

## 7. Fase 2–5 — Fondasi UI, Autentikasi, Habit Tracker & CDS

### Fase 2 — Fondasi UI (`FE`)
- [x] **UI-01** `tailwind.config.js` dengan token warna/radius/bayangan (`DesainUI` §3) — butuh: SET-03
- [x] **UI-02** Layout `guest`, `student` (bottom nav), `staff` (sidebar + topbar) (`DesainUI` §5.1, §6.2) — butuh: UI-01
- [x] **UI-03** Komponen dasar: `card`, `button`, `badge-status`, `form-field` — butuh: UI-01
- [x] **UI-04** Komponen form: `slider-field`, `segmented`, `chip-group` — butuh: UI-03
- [x] **UI-05** `x-modal` (bottom sheet mobile + modal desktop, focus trap), toast, `empty-state`, skeleton — butuh: UI-03
- [x] **UI-06** `alert-cds` (tiga level) — butuh: UI-03
- [x] **UI-07** `data-table`, `chat-bubble` — butuh: UI-03

### Fase 3 — Autentikasi & RBAC
- [ ] **AUTH-01** Login mahasiswa dengan NIM (M-01) — `FE` · butuh: UI-02, SET-05
- [ ] **AUTH-02** Registrasi 2 langkah (M-02). Field profil faktor risiko menunggu DOC-07; buat kerangkanya dulu — `FE` · butuh: AUTH-01
- [ ] **AUTH-03** Login dokter/admin (D-01) — `FE` · butuh: UI-02
- [ ] **AUTH-04** Middleware RBAC: mahasiswa hanya data sendiri, dokter hanya pasien yang berkonsultasi dengannya — `CDS` · butuh: SET-04
- [ ] **AUTH-05** Status akun "menunggu verifikasi" dan layar penunggu — `FE` + `CDS` · butuh: AUTH-02

### Fase 4 — Habit Tracker & CDS (inti)
- [ ] **CDS-01** Tetapkan kontrak `CdsEvaluatorService` (bagian 4) dan kerangka kelas — `CDS`
- [ ] **CDS-02** `cds_config.json`/tabel `cds_rules` berisi seluruh ambang aturan — `CDS` · butuh: CDS-01
- [ ] **CDS-03** Implementasi aturan F-01, F-02, M-01, M-02 + uji otomatis memakai 8 skenario prototipe — `CDS` · butuh: CDS-02
- [ ] **CDS-04** Pencatatan log audit evaluasi Perlu Perhatian (waktu, input, ID aturan) — `CDS` · butuh: CDS-03
- [ ] **HAB-01** Beranda mahasiswa (M-03), awalnya data tiruan — `FE` · butuh: UI-04, UI-06
- [ ] **HAB-02** Modal Form Habit Tracker (M-04) — `FE` · butuh: UI-04, UI-05
- [ ] **HAB-03** Endpoint simpan log + hitung otomatis `sleep_streak` — `CDS` · butuh: SET-04
- [ ] **HAB-04** Modal Hasil CDS (M-05, 3 varian) — `FE` · butuh: UI-05, UI-06, kontrak bagian 4
- [ ] **HAB-05** Hubungkan form → evaluator → modal hasil; banner alert tetap di Beranda — `FE` + `CDS` · butuh: HAB-02, HAB-03, HAB-04, CDS-03
- [ ] **HAB-06** Pengingat/keadaan "log hari ini sudah diisi" dan mode ubah log — `FE` · butuh: HAB-05

### Fase 5 — Riwayat & Grafik (`FE`)
- [ ] **HIS-01** Halaman Riwayat dengan tab Hari/Minggu/Bulan (M-06) — butuh: HAB-05
- [ ] **HIS-02** Grafik tren tidur dan pola makan (Chart.js) — butuh: HIS-01
- [ ] **HIS-03** Detail log harian dan ringkasan kalimat otomatis — butuh: HIS-01

## 8. Fase 6–10 — Telemedicine, Dokter, Admin, CBR, Pengujian

### Fase 6 — Telemedicine sisi mahasiswa
- [ ] **TEL-01** Status dokter Online/Offline + jadwal praktik (data dan API) — `CHAT` · butuh: SET-04
- [ ] **TEL-02** Daftar dokter (M-07) — `FE` · butuh: TEL-01
- [ ] **TEL-03** Konfirmasi + pembuatan tiket dengan lampiran habit 7 hari dan alert (M-08) — `CHAT` · butuh: TEL-02, HAB-05
- [ ] **TEL-04** Tiket menunggu dan kedaluwarsa otomatis 30 menit via scheduler (M-09) — `CHAT` · butuh: TEL-03
- [ ] **TEL-05** Keputusan teknologi real-time (Laravel Reverb/Pusher + Echo atau polling) — `CHAT`
- [ ] **TEL-06** Ruang chat mahasiswa (M-10) — `CHAT` + `FE` · butuh: TEL-04, TEL-05, UI-07
- [ ] **TEL-07** Riwayat konsultasi (M-11) dan notifikasi (M-12) — `FE` · butuh: TEL-06
- [ ] **TEL-08** Profil dan pengaturan (M-13) — `FE` · butuh: AUTH-02

### Fase 7 — Dashboard Dokter
- [ ] **DR-01** Dashboard dokter (D-02) — `FE` · butuh: UI-02, UI-07, TEL-01
- [ ] **DR-02** Daftar permintaan: terima/tolak + pratinjau konteks (D-03) — `CHAT` + `FE` · butuh: TEL-03
- [ ] **DR-03** Ruang konsultasi tiga kolom dengan panel konteks pasien (D-04) — `CHAT` + `FE` · butuh: TEL-06, DR-02
- [ ] **DR-04** Templat balasan cepat — `CHAT` · butuh: DR-03
- [ ] **DR-05** Tutup sesi: diagnosis akhir, catatan, rekomendasi → simpan rekam medis (D-05) — `CHAT` · butuh: DR-03
- [ ] **DR-06** Pasien dan rekam medis (D-06) — `FE` · butuh: DR-05
- [ ] **DR-07** Jadwal dan status dokter (D-07) — `FE` · butuh: TEL-01
- [ ] **DR-08** Profil dokter (D-08) — `FE` · butuh: AUTH-03

### Fase 8 — Admin
- [ ] **AD-01** Dashboard admin (A-01) — `FE` · butuh: CDS-04
- [ ] **AD-02** Manajemen pengguna + verifikasi NIM (A-02) — `FE` + `CDS` · butuh: AUTH-05
- [ ] **AD-03** Konfigurasi aturan CDS dengan versioning dan catatan alasan (A-03) — `CDS` + `FE` · butuh: CDS-02
- [ ] **AD-04** Log audit CDS dan ekspor CSV (A-04) — `CDS` · butuh: CDS-04
- [ ] **AD-05** Halaman sistem dan keamanan: cadangan, masa kedaluwarsa tiket (A-05) — `FE` · butuh: TEL-04

### Fase 9 — CBR & Dataset Kaggle (pendukung, boleh digeser bila waktu mepet)
- [ ] **CBR-01** Cari dan periksa dataset Kaggle (gaya hidup/tidur/kesehatan mahasiswa) — `CDS` · *belum dicek*
- [ ] **CBR-02** Olah dan petakan fitur ke struktur kasus, seed ke `cds_cases` — `CDS` · butuh: CBR-01
- [ ] **CBR-03** Modul pencocokan kemiripan (weighted Euclidean) — `CDS` · butuh: CBR-02
- [ ] **CBR-04** Integrasi ke evaluator untuk kasus area samar — `CDS` · butuh: CBR-03, CDS-03
- [ ] **CBR-05** Umpan balik: diagnosis akhir dokter masuk ke pustaka kasus — `CDS` · butuh: DR-05, CBR-03

### Fase 10 — Pengujian & Rilis
- [ ] **TST-01** Uji fungsional (black-box) seluruh alur mahasiswa → dokter — `ALL` · butuh: Fase 6–7
- [ ] **TST-02** Simulasi skenario alert CDS (8 skenario prototipe) dan pencocokan hasil dengan aturan tervalidasi — `CDS` · butuh: DOC-06
- [ ] **TST-03** Uji keamanan akses data (RBAC, sesi, HTTPS, hashing sandi) — `ALL` · butuh: AUTH-04
- [ ] **TST-04** Cek responsif dan aksesibilitas (`DesainUI` §9) — `FE`
- [ ] **TST-05** Perbaikan bug dan persiapan demo final — `ALL`

---

## 9. Log Serah Terima

Tambahkan entri baru **di atas** entri lama. Satu entri per sesi kerja yang menghasilkan perubahan.

| Tanggal | Oleh | Selesai | Sedang jalan / belum tuntas | Lanjut dari |
| :--- | :--- | :--- | :--- | :--- |
| 14 Nov 2026 | Antigravity AI | Fase 2: UI-01 s.d. UI-07 (Layouts guest/student/staff, card, button, badge-status, form-field, slider-field, segmented, chip-group, modal, toast, empty-state, skeleton, alert-cds, data-table, chat-bubble) + Pembersihan tabel bawaan Laravel di DB | SET-01 (mandiri oleh user) | Fase 3: `AUTH-01` |
| 14 Nov 2026 | Antigravity AI | Fase 1: SET-02 (MySQL .env), SET-03 (Tailwind v4 token, Plus Jakarta Sans, Alpine, Lucide), SET-04 (11 migration tabel inti), SET-05 (Seeders 3 peran + dummy 7 hari + CDS v2.1) | SET-01 (dikerjakan mandiri oleh user) | `UI-01` / Fase 2 |
| 13 Nov 2026 | Tim | Fase 0: DOC-01 s.d. DOC-05 | DOC-06 menunggu jadwal ahli | `SET-01` |

---

## 10. Risiko & Keputusan Terbuka

| Item | Dampak | Penanggung jawab | Status |
| :--- | :--- | :--- | :--- |
| Validasi ahli belum terlaksana | Ambang dan aturan CDS bisa berubah | `CDS` | Terbuka — batas minggu 2 |
| Parameter final (CDS v2.1) | Mengubah form M-04, profil M-02, dan tabel `habit_logs` | `CDS` + `FE` | Terbuka |
| Dataset Kaggle belum dicek | CBR tidak bisa di-seed | `CDS` | Terbuka |
| Teknologi real-time chat | Menentukan arsitektur TEL-06 | `CHAT` | Terbuka (Reverb/Pusher vs polling) |
| CBR bersifat pendukung | Bila waktu mepet, MVP tetap berjalan dengan RBR saja | `CDS` | Disepakati sebagai opsional |

**Strategi bila waktu mepet:** pertahankan alur inti (Fase 3–4, 6, 7). Geser dulu `CBR-*`, `AD-05`, `DR-04`, `DR-06`, dan `TEL-07` (notifikasi).
