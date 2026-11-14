# Software Requirements Specification (SRS) CampusWell

Platform Pemantauan Kebiasaan Sehat, Skrining Risiko (CDSS), dan Tele-konsultasi Klinik Kampus · Versi 0.1 (Draft) · 13 November 2026

> Dokumen ini merangkum seluruh keputusan hasil diskusi tim: stack Laravel, tiga peran pengguna, CDS hibrida (RBR inti, CBR pendukung opsional), parameter v2.1, dan penghapusan checklist gejala bahaya. Aturan CDS yang bersifat normatif ada di *Spesifikasi CDS v2.1*; bila ada selisih, dokumen itu yang berlaku. Semua ambang bersifat usulan sampai divalidasi ahli (batas paling lambat Jumat, 27 November 2026).

## 1. Pendahuluan

### 1.1 Tujuan

Dokumen ini mendefinisikan kebutuhan fungsional dan non-fungsional aplikasi **CampusWell** sebagai acuan pengembangan, pengujian, dan penerimaan oleh tim pengembang (Frontend, CDS, Chat), dosen pembimbing, serta ahli yang memvalidasi.

### 1.2 Ruang Lingkup

CampusWell adalah aplikasi web responsif berbahasa Indonesia yang:

1. membantu mahasiswa mencatat kebiasaan tidur, makan, stres, dan keluhan harian (*Habit Tracker*);
2. memberi **indikasi risiko awal** kelelahan dan gangguan lambung secara otomatis (*CDSS*);
3. menghubungkan mahasiswa ke dokter klinik kampus lewat **chat teks** (*Telemedicine*).

**Di luar lingkup MVP:** aplikasi native, panggilan suara/video, push notification, resep dan pembayaran, integrasi SIMRS/BPJS, diagnosis atau terapi otomatis, perangkat wearable, check-in mingguan dan periode ujian (tahap 2).

### 1.3 Definisi dan Singkatan

| Istilah | Arti |
| --- | --- |
| CDSS | *Clinical Decision Support System*, mesin skrining risiko |
| RBR | *Rule-Based Reasoning*, aturan IF-THEN dengan skor poin |
| CBR | *Case-Based Reasoning*, pencocokan dengan pustaka kasus (pendukung) |
| Habit log | Satu entri catatan harian per mahasiswa per tanggal |
| Level | `NORMAL`, `PERHATIAN_MANDIRI`, `PERLU_PERHATIAN` |
| Tiket | Permintaan konsultasi mahasiswa kepada dokter |
| Ahli | Dosen keperawatan yang memvalidasi aturan RBR |
| RBAC | *Role-Based Access Control* |

### 1.4 Dokumen Terkait

Konsep Proyek CampusWell · Spesifikasi CDS v2.1 · PRD & Timeline Pengerjaan · Dokumen Validasi Ahli v2.1 · Lampiran Kasus Sintetis CBR v2.1.

## 2. Deskripsi Umum

### 2.1 Gambaran Produk

Aplikasi web satu codebase **Laravel (Blade + Livewire)** dengan basis data relasional (MySQL atau PostgreSQL). Modul inti: Autentikasi dan Profil, Habit Tracker, CDS (`CdsEvaluatorService`), Telemedicine, Rekam Medis, dan Administrasi. CDS dipanggil sinkron setiap habit log disimpan.

### 2.2 Pengguna

| Peran | Deskripsi dan hak akses |
| --- | --- |
| **Mahasiswa** | Mengisi profil dan habit log, melihat hasil dan grafik, mengajukan konsultasi, chat dengan dokter |
| **Dokter / petugas klinik** | Mengatur status online/offline, menerima tiket, chat, menutup sesi dengan catatan medis, menandai kasus untuk pembelajaran |
| **Administrator IT** | Memverifikasi dan mengelola akun, mengelola konfigurasi CDS yang sudah disetujui ahli, melihat log audit |

### 2.3 Batasan Desain

- Stack: PHP Laravel, Livewire, Eloquent; autentikasi Breeze; RBAC Spatie Permission atau Gate/Policy; grafik Chart.js; pengujian Pest/PHPUnit.
- Chat memakai **polling** (3–5 detik), tanpa WebSocket pada MVP.
- Deploy ke VPS, Railway, atau Render dengan HTTPS (bukan Vercel).
- Jadwal: 16 November – 11 Desember 2026, tim 3 orang.
- Hasil sistem adalah **skrining awal, bukan diagnosis**.
- Modul CDS harus terpisah dari controller dan UI, dengan semua ambang di konfigurasi.

### 2.4 Asumsi dan Ketergantungan

- Ahli tersedia untuk validasi paling lambat 27 November 2026; sebelum itu sistem memakai konfigurasi draf.
- Minimal satu dokter klinik bersedia menjadi pengguna uji.
- Mahasiswa memiliki akses ke peramban modern di ponsel atau komputer.
- Kasus CBR awal berasal dari kasus sintetis yang dilabel ahli, bukan data pasien nyata.

## 3. Kebutuhan Fungsional

Prioritas: **P0** wajib MVP, **P1** sangat penting, **P2** bila waktu cukup.

### 3.1 Autentikasi dan Akun (AUTH)

| ID | Kebutuhan | Prio |
| --- | --- | --- |
| FR-AUTH-01 | Sistem harus menyediakan registrasi mahasiswa dengan NIM, nama, dan kata sandi. | P0 |
| FR-AUTH-02 | Sistem harus menyediakan login dan logout untuk mahasiswa (NIM), dokter, dan admin | P0 |
| FR-AUTH-03 | Kata sandi harus disimpan dalam bentuk hash; sesi berakhir setelah tidak aktif (nilai awal 2 jam) | P0 |
| FR-AUTH-04 | Akses harus dibatasi per peran di sisi server (RBAC); mahasiswa hanya data miliknya, dokter hanya data pasien yang memiliki tiket dengannya | P0 |
| FR-AUTH-05 | Sistem harus menyediakan reset kata sandi | P1 |

### 3.2 Profil Faktor Risiko (PRF)

| ID | Kebutuhan | Prio |
| --- | --- | --- |
| FR-PRF-01 | Saat pertama kali masuk, mahasiswa **wajib** melengkapi profil: P1 riwayat maag/GERD (Ya/Tidak/Tidak tahu), P2 merokok (Tidak/Kadang/Rutin), P3 pemakaian rutin NSAID (Tidak/Kadang/Rutin); P4 kondisi kronis lain bersifat opsional | P0 |
| FR-PRF-02 | Sebelum menyimpan data kesehatan, sistem harus meminta persetujuan (*consent*) penyimpanan dan pemrosesan data | P0 |
| FR-PRF-03 | Habit Tracker tidak dapat dipakai sebelum profil wajib dilengkapi | P0 |
| FR-PRF-04 | Mahasiswa dapat mengubah profil kapan saja; evaluasi berikutnya memakai nilai terbaru | P1 |
| FR-PRF-05 | Profil hanya dapat dilihat dokter bila mahasiswa menyetujui berbagi data pada tiket terkait | P0 |

### 3.3 Habit Tracker (HAB)

| ID | Kebutuhan | Prio |
| --- | --- | --- |
| FR-HAB-01 | Sistem harus menyediakan form harian dengan: D1 durasi tidur (jam), D2 frekuensi makan (0–5), D3 kategori makanan/minuman (Pedas, Asam, Manis, High-Caffeine, Netral), D4 jarak makan terakhir ke tidur (0–6 jam atau "tidak makan malam"), D5 stres (5 level berdeskripsi), D6 keluhan (perih, asam\_naik, mual, kembung, lelah, konsentrasi, tidak\_ada), D7 kesegaran saat bangun (skala 1–5) | P0 |
| FR-HAB-02 | Pilihan `Netral` pada D3 dan `tidak_ada` pada D6 bersifat eksklusif; nilai bawaan D6 adalah `tidak_ada` | P0 |
| FR-HAB-03 | Sistem harus memvalidasi rentang setiap isian dan menolak data tidak valid dengan pesan jelas | P0 |
| FR-HAB-04 | Hanya satu habit log per mahasiswa per tanggal; entri hari berjalan dapat diedit dan memicu evaluasi ulang; hari lampau tidak dapat diubah | P0 |
| FR-HAB-05 | Sistem harus menghitung otomatis fitur turunan: streak kurang tidur, jumlah hari kurang tidur dalam 7 hari, hari berpola lambung dan hari berkeluhan dalam 5 hari; hari tidak terisi memutus streak | P0 |
| FR-HAB-06 | Sistem harus menampilkan grafik tren 7 hari (durasi tidur, frekuensi makan, stres, kesegaran) | P1 |
| FR-HAB-07 | Sistem harus menampilkan riwayat habit log dan hasil evaluasinya | P1 |
| FR-HAB-08 | Form harian harus dapat diisi dalam kurang dari 60 detik pada ponsel | P0 |

### 3.4 Mesin CDS (CDS)

| ID | Kebutuhan | Prio |
| --- | --- | --- |
| FR-CDS-01 | `CdsEvaluatorService` harus berupa fungsi murni: menerima habit log hari ini, riwayat 7 hari, profil, dan konfigurasi; mengembalikan hasil evaluasi tanpa akses langsung ke basis data | P0 |
| FR-CDS-02 | Sistem harus menghitung skor kelelahan (F) sesuai Spesifikasi CDS v2.1 bagian 4 | P0 |
| FR-CDS-03 | Sistem harus menghitung skor lambung (M), penguat profil, aturan keluhan (M-K1, M-K2, M-K3), dan aturan pola (M-P) sesuai Spesifikasi CDS v2.1 bagian 5 | P0 |
| FR-CDS-04 | Level akhir RBR adalah level tertinggi dari kelompok F dan M; bila keduanya `PERLU_PERHATIAN`, kedua alert ditampilkan | P0 |
| FR-CDS-05 | Seluruh ambang, poin, dan bobot harus dibaca dari konfigurasi; setiap perubahan menaikkan `rules_version` | P0 |
| FR-CDS-06 | Evaluasi harus dijalankan setiap habit log disimpan atau diedit, dan hasilnya disimpan | P0 |
| FR-CDS-07 | Setiap alert harus memuat alasan yang dapat dibaca (`reasons[]`) dan skor komponen | P1 |
| FR-CDS-08 | Alert `PERLU_PERHATIAN` yang ditolak tidak ditampilkan ulang selama 3 hari, kecuali skor naik; evaluasi tetap dicatat | P1 |
| FR-CDS-09 | Setiap evaluasi `PERLU_PERHATIAN` harus dicatat di log audit: waktu, input, skor tiap komponen, `rule_ids`, hasil CBR, dan `rules_version` | P1 |
| FR-CDS-10 | **CBR (pendukung):** bila aktif, sistem menghitung vektor 8 fitur ternormalisasi, similarity berbobot dengan k = 5 tetangga, dan hasil `RISIKO` bila ≥ 3 tetangga berisiko dengan similarity ≥ 0,80 | P2 |
| FR-CDS-11 | CBR hanya berjalan bila hasil RBR `PERHATIAN_MANDIRI` dan hanya boleh menaikkan ke `PERLU_PERHATIAN`; CBR tidak pernah menurunkan hasil RBR | P2 |
| FR-CDS-12 | CBR harus nonaktif otomatis bila kasus `approved` kurang dari 30 atau `cbr.enabled` bernilai false; sistem tetap berfungsi penuh tanpa CBR | P2 |

### 3.5 Tampilan Hasil dan Edukasi (ALR)

| ID | Kebutuhan | Prio |
| --- | --- | --- |
| FR-ALR-01 | Level `NORMAL`: data tersimpan, grafik diperbarui, pesan edukasi ringan ditampilkan | P0 |
| FR-ALR-02 | Level `PERHATIAN_MANDIRI`: kartu saran (tidur atau nutrisi) tanpa pop-up alarm; isi saran berasal dari ahli dan dapat diubah lewat konfigurasi/konten | P0 |
| FR-ALR-03 | Level `PERLU_PERHATIAN`: pop-up alert berisi jenis risiko, alasan, dan tombol "Rekomendasi Konsultasi Dokter" | P0 |
| FR-ALR-04 | Setiap hasil harus menampilkan pernyataan bahwa ini skrining awal dan bukan diagnosis | P0 |
| FR-ALR-05 | Tombol konsultasi pada alert harus membawa mahasiswa ke daftar dokter dengan tiket yang sudah tertaut ke evaluasi tersebut | P0 |

### 3.6 Telemedicine (TEL)

| ID | Kebutuhan | Prio |
| --- | --- | --- |
| FR-TEL-01 | Mahasiswa dapat membuka Konsultasi Dokter dari menu utama atau dari alert (dua pintu masuk) | P0 |
| FR-TEL-02 | Sistem menampilkan daftar dokter beserta status Online/Offline dan jadwal praktik | P1 |
| FR-TEL-03 | Dokter dapat mengubah status Online/Offline | P1 |
| FR-TEL-04 | Mahasiswa dapat mengajukan tiket ke dokter pilihannya; tiket mencatat sumber (menu atau alert) dan tautan evaluasi bila ada | P0 |
| FR-TEL-05 | Sebelum chat dimulai, sistem meminta persetujuan berbagi ringkasan habit 7 hari dan jenis alert; profil hanya dibagikan bila disetujui terpisah | P1 |
| FR-TEL-06 | Dokter melihat antrean tiket dengan penanda permintaan baru (*badge* in-app) | P0 |
| FR-TEL-07 | Dokter dapat menerima atau menolak tiket; penolakan membuat mahasiswa dapat memilih dokter lain | P0 |
| FR-TEL-08 | Chat teks dua arah dengan pembaruan melalui polling 3–5 detik; pesan tersimpan dan berurutan waktu | P0 |
| FR-TEL-09 | Bila semua dokter Offline, mahasiswa tetap dapat mengirim tiket (mode asinkron) dan diberi tahu bahwa balasan menyusul | P1 |
| FR-TEL-10 | Tiket berstatus `requested` kedaluwarsa dalam **30 menit** tanpa respons (lihat catatan keputusan terbuka 7.1) | P1 |
| FR-TEL-11 | Dokter dapat menutup sesi dengan catatan medis atau rekomendasi rujukan | P1 |
| FR-TEL-12 | Mahasiswa hanya dapat memiliki satu tiket aktif pada satu waktu (usulan) | P1 |
| FR-TEL-13 | Mahasiswa dapat melihat riwayat konsultasinya | P1 |

### 3.7 Rekam Medis dan Pembelajaran Kasus (REK)

| ID | Kebutuhan | Prio |
| --- | --- | --- |
| FR-REK-01 | Catatan penutupan sesi harus tersimpan sebagai rekam medis tertaut tiket | P1 |
| FR-REK-02 | Dokter hanya dapat mengakses rekam medis pasien yang memiliki tiket dengannya | P0 |
| FR-REK-03 | Dokter dapat menandai kasus "valid untuk pembelajaran"; sistem membuat kasus berstatus `pending_review` dengan snapshot fitur 7 hari | P2 |
| FR-REK-04 | Kasus `pending_review` hanya menjadi `approved` (dipakai CBR) setelah ditinjau dokter/ahli | P2 |

### 3.8 Administrasi (ADM)

| ID | Kebutuhan | Prio |
| --- | --- | --- |
| FR-ADM-01 | Admin dapat membuat, dan menonaktifkan akun mahasiswa dan dokter | P0 |
| FR-ADM-02 | Admin dapat mengubah konfigurasi CDS (berversi, mencatat pengubah dan persetujuan ahli); minimal dapat dilakukan lewat berkas konfigurasi | P2 |
| FR-ADM-03 | Admin dapat melihat log audit evaluasi dan akses data | P1 |
| FR-ADM-04 | Admin dapat mengatur kalender konten edukasi/saran Perhatian Mandiri dari ahli | P2 |

## 4. Kebutuhan Antarmuka

### 4.1 Antarmuka Pengguna

| Peran | Layar utama |
| --- | --- |
| Mahasiswa | Login, Registrasi, Lengkapi Profil dan Consent, Beranda, Form Harian, Hasil Evaluasi, Grafik dan Riwayat, Daftar Dokter, Ruang Chat, Riwayat Konsultasi, Edit Profil |
| Dokter | Dashboard Antrean, Detail Tiket (ringkasan 7 hari dan alert), Ruang Chat, Form Catatan Medis, Pengaturan Status, Tinjau Kasus |
| Admin | Kelola Akun, Konfigurasi CDS, Log Audit |

Antarmuka mobile-first, bahasa Indonesia, dan dapat dipakai dengan satu tangan pada form harian.

### 4.2 Antarmuka Perangkat Lunak

- Basis data: MySQL  melalui Eloquent; kolom JSON untuk kasus CBR dan hasil alert.
- `CdsEvaluatorService` dan `CbrMatcherService` dipanggil oleh service layer; konfigurasi dibaca dari `cds_config.json` atau tabel versi konfigurasi.

### 4.3 Kontrak Keluaran CDS (ringkas)

| Field | Isi |
| --- | --- |
| `final_level` | `NORMAL` / `PERHATIAN_MANDIRI` / `PERLU_PERHATIAN` |
| `alerts[]` | `type` (FATIGUE/MAAG), `title`, `score`, `rule_ids`, `reasons[]` |
| `cbr` | `enabled`, `result`, `top_similarity` |
| `rules_version` | Versi aturan yang dipakai |
| `cooldown_suppressed` | Apakah alert ditekan oleh cooldown |

### 4.4 Antarmuka Komunikasi

Semua lalu lintas melalui HTTPS. Pembaruan chat memakai permintaan berkala (polling) ke endpoint pesan per tiket.

## 5. Kebutuhan Non-Fungsional

| ID | Aspek | Kebutuhan |
| --- | --- | --- |
| NFR-01 | Kinerja | Halaman utama dimuat ≤ 3 detik pada jaringan seluler; evaluasi CDS < 1 detik; pesan chat tampil ≤ 5 detik |
| NFR-02 | Keamanan | HTTPS; hash kata sandi; perlindungan CSRF, XSS, dan SQL injection; pembatasan percobaan login; validasi input di server |
| NFR-03 | Privasi | Data profil, habit log, pesan chat, dan rekam medis terenkripsi *at rest* (atau setidaknya kolom sensitif); akses dicatat; berbagi data ke dokter hanya dengan persetujuan; mengacu prinsip UU PDP |
| NFR-04 | Etika klinis | Setiap hasil menampilkan disclaimer; sistem tidak memakai istilah diagnosis; tidak ada saran terapi |
| NFR-05 | Kegunaan | Mobile-first, form harian < 60 detik, bahasa sederhana |
| NFR-06 | Keandalan | Pencadangan basis data harian; kegagalan CBR tidak boleh menggagalkan evaluasi RBR |
| NFR-07 | Maintainability | CDS terpisah dari controller dan UI; ambang di konfigurasi berversi; unit test untuk seluruh skenario CDS |
| NFR-08 | Portabilitas | Berjalan di dua versi terbaru Chrome, Firefox, Safari, dan Edge |
| NFR-09 | Auditabilitas | Setiap evaluasi `PERLU_PERHATIAN` dan setiap akses rekam medis tercatat dengan waktu dan pelaku |

## 6. Model Data (Entitas Utama)

| Entitas | Atribut penting |
| --- | --- |
| `users` | id, role, nim/email, password\_hash, nama, status\_verifikasi |
| `user_profiles` | user\_id, p1\_riwayat\_maag, p2\_merokok, p3\_nsaid, p4\_kondisi\_lain, consent\_at |
| `doctors` | user\_id, jadwal praktik, status (online/offline) |
| `habit_logs` | id, user\_id, tanggal (unik per user), sleep\_hours, meal\_freq, food\_categories, late\_eating\_hours (nullable), stress\_level, complaints (array), freshness |
| `cds_evaluations` | id, habit\_log\_id, final\_level, alerts (JSON), skor F dan M, hasil CBR, rules\_version, cooldown\_suppressed, created\_at |
| `alert_dismissals` | user\_id, alert\_type, dismissed\_at, score\_at\_dismissal |
| `consult_tickets` | id, student\_id, doctor\_id, source, evaluation\_id, status, consent\_share\_data, consent\_share\_profile, timestamps |
| `chat_messages` | id, ticket\_id, sender\_id, isi, created\_at |
| `medical_records` | id, ticket\_id, catatan, rekomendasi, learning\_flag |
| `cds_cases` | case\_id, source, status (pending\_review/approved/rejected), features, outcome, expert\_label, validated\_by, rules\_version |
| `cds_config_versions` | version, config\_json, changed\_by, approved\_by\_expert, created\_at |
| `audit_logs` | id, actor\_id, aksi, objek, waktu |

## 7. Status Tiket Konsultasi

| Status | Arti | Transisi berikutnya |
| --- | --- | --- |
| `requested` | Mahasiswa mengajukan; menunggu dokter | `accepted`, `declined`, `expired` |
| `accepted` | Dokter menerima | `active` |
| `active` | Chat berjalan | `closed` |
| `closed` | Dokter menutup dengan catatan | Akhir |
| `declined` | Dokter menolak/mengalihkan | Mahasiswa memilih dokter lain |
| `expired` | Tidak direspons dalam batas waktu | Mahasiswa dapat mengajukan ulang |

### 7.1 Keputusan Terbuka Terkait Tiket

Kedaluwarsa 30 menit bertentangan dengan mode asinkron (dokter offline, balasan menyusul). **Usulan:** 30 menit hanya berlaku bila dokter yang dipilih berstatus Online; untuk dokter Offline, tiket bertahan sampai jam praktik berikutnya. Menunggu konfirmasi tim.

## 8. Use Case Utama

| ID | Use case | Alur utama | Pascakondisi |
| --- | --- | --- | --- |
| UC-01 | Mengisi habit dan menerima evaluasi | Mahasiswa membuka form harian → mengisi D1–D7 → menyimpan → sistem menghitung turunan dan menjalankan CDS → hasil tampil sesuai level | Habit log dan evaluasi tersimpan; grafik diperbarui |
| UC-02 | Konsultasi dari alert | Hasil `PERLU_PERHATIAN` → mahasiswa menekan tombol konsultasi → memilih dokter → menyetujui berbagi data → tiket `requested` → dokter menerima → chat | Tiket `active` tertaut ke evaluasi |
| UC-03 | Konsultasi dari menu | Mahasiswa membuka Konsultasi Dokter dari beranda → memilih dokter → consent → tiket dibuat | Tiket tanpa tautan evaluasi |
| UC-04 | Dokter memproses tiket | Dokter melihat antrean → membuka detail (ringkasan 7 hari, alert) → menerima → chat → menutup dengan catatan | Rekam medis tersimpan; opsional kasus `pending_review` |
| UC-05 | Verifikasi akun | Mahasiswa mendaftar → admin memverifikasi NIM → akun aktif → mahasiswa melengkapi profil dan consent | Akun aktif dan profil lengkap |
| UC-06 | Peninjauan kasus CBR | Dokter/ahli membuka daftar `pending_review` → menyetujui atau menolak | Kasus `approved` masuk pustaka CBR |

**Alternatif penting:** semua dokter Offline (UC-02/03) → tiket asinkron (FR-TEL-09); tiket tidak direspons → `expired` (FR-TEL-10); alert ditolak → cooldown 3 hari (FR-CDS-08); entri hari ini diedit → evaluasi diulang (FR-HAB-04, FR-CDS-06).

## 9. Kriteria Penerimaan dan Pengujian

| Area | Kriteria |
| --- | --- |
| CDS (RBR) | 13 skenario uji pada Spesifikasi CDS v2.1 bagian 8 lulus 100% sebagai unit test |
| CDS (kasus) | 25 kasus sintetis "jelas" menghasilkan label sesuai; hasil pada 19 kasus abu-abu tercatat dan dibandingkan dengan label ahli |
| Alur end-to-end | Registrasi → profil → habit → alert → tiket → chat → tutup sesi berjalan tanpa error |
| Keamanan akses | Uji RBAC: mahasiswa tidak dapat melihat data mahasiswa lain; dokter tidak dapat melihat pasien tanpa tiket; nol kebocoran |
| Privasi | Ringkasan dan profil tidak terlihat dokter tanpa consent |
| Kegunaan | UAT 3–5 mahasiswa dan satu dokter: form harian selesai rata-rata < 60 detik |
| Ketahanan | CBR dimatikan: seluruh alur RBR tetap berfungsi |

## 10. Keterlacakan terhadap Jadwal

| Milestone | Tanggal | Kebutuhan yang tercakup |
| --- | --- | --- |
| M1 | 20 Nov | Skema DB; FR-CDS-01 sampai FR-CDS-06 (RBR lulus skenario) |
| M2 | 27 Nov | FR-AUTH, FR-PRF, FR-HAB, FR-ALR; validasi ahli selesai paling lambat hari ini |
| M3 | 4 Des | FR-TEL, FR-REK-01/02, FR-CDS-07 sampai FR-CDS-09, FR-ADM-01 |
| M4 | 11 Des | FR-CDS-10 sampai FR-CDS-12 dan FR-REK-03/04 (P2), FR-ADM-02/03, pengujian, deploy, demo |

Bila terlambat, pemotongan mengikuti urutan: CBR dan umpan balik kasus (P2) → konfigurasi CDS lewat UI → notifikasi tambahan → grafik tambahan → mode asinkron lanjutan. Seluruh P0 tidak boleh dipotong.

## 11. Isu Terbuka

| No | Isu | Pemilik/Keterangan |
| --- | --- | --- |
| 1 | Kedaluwarsa tiket vs mode asinkron (bagian 7.1) | Tim |
| 2 | Deskripsi lima level stres (D5) belum dikonfirmasi final | Tim dan ahli |
| 3 | Hasil validasi ahli dapat mengubah ambang, poin, dan daftar keluhan | Ahli, batas 27 Nov |
| 4 | Pengisian terlambat (*backfill*) hari lampau belum diputuskan | Tim |
| 5 | Hosting demo dan pilihan MySQL atau PostgreSQL | Tim |
| 6 | Isi pesan edukasi Perhatian Mandiri dan teks disclaimer | Ahli |
| 7 | Check-in mingguan dan butir suasana hati (tahap 2) | Tim dan ahli |
| 8 | Penghapusan checklist gejala bahaya perlu penilaian ahli (butir 14 lembar validasi) | Ahli |
| 9 | Nilai awal sesi tidak aktif (2 jam) dan batas satu tiket aktif | Tim |

## 12. Riwayat Dokumen

| Versi | Tanggal | Perubahan |
| --- | --- | --- |
| 0.1 | 13 November 2026 | Draf awal berdasarkan keputusan tim dan Spesifikasi CDS v2.1 |
