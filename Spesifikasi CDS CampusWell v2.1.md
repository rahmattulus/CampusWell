# Spesifikasi CDS CampusWell v2.1

Revisi dari Spesifikasi CDS v2.0 · 13 November 2026 · Status: **DRAFT, menunggu validasi ahli**

> Semua ambang dan bobot adalah **usulan tim**, disimpan di `cds_config.json` agar mudah diubah setelah validasi. Prinsip arsitektur: **RBR sebagai inti** (wajib), **CBR sebagai pendukung opsional** yang bisa dimatikan tanpa mengganggu sistem. Keluaran CDS adalah **indikasi risiko (skrining awal)**, bukan diagnosis.

## 1. Ringkasan Perubahan dari v2.0

| Area | v2.0 | v2.1 |
| --- | --- | --- |
| Gejala | Tidak ada | **Keluhan harian (D6)** berupa chip |
| Tidur | Durasi + streak | + **kesegaran saat bangun (D7, skala 5)** + variasi tidur tidak beruntun |
| Stres | Slider 1–5 | Tetap 1–5, tetapi tiap level diberi **deskripsi perilaku** (usulan; menunggu konfirmasi tim) |
| Gejala bahaya (red flag) | Checklist + aturan R-00 | **Dihapus** (keputusan tim) |
| Profil pengguna | Tidak ada | **Profil faktor risiko wajib** (P1–P3) |
| Check-in mingguan | Tidak ada | Dirancang, **tahap 2** (bukan MVP) |
| Periode ujian | Tidak ada | **Ditunda** |
| Pre-fill isian | Tidak ada | Tidak dipakai |
| Fitur CBR | 6 fitur | **8 fitur** (tambah keluhan lambung dan kesegaran) |

Dampak penghapusan red flag: tidak ada lagi jalur darurat di luar skor. Disclaimer "skrining awal, bukan diagnosis; segera berobat bila kondisi memburuk" tetap ditampilkan di setiap hasil dan di halaman tentang.

## 2. Katalog Parameter

### 2.1 Input harian (MVP, target ≤ 60 detik)

| Kode | Parameter | Bentuk input | Nilai |
| --- | --- | --- | --- |
| D1 | Durasi tidur | Angka | 0–24 jam |
| D2 | Frekuensi makan | Pilihan | 0–5 (5 = 5 atau lebih) |
| D3 | Kategori makanan/minuman | Multi-select | `Pedas`, `Asam`, `Manis`, `High-Caffeine`, `Netral` (Netral eksklusif) |
| D4 | Jarak makan terakhir ke tidur | Pilihan | 0–6 jam (6 = 6 jam atau lebih); `null` = tidak makan malam |
| D5 | Tingkat stres | 5 level berdeskripsi | 1 Tenang · 2 Ringan · 3 Cukup menekan tetapi terkendali · 4 Berat, mulai mengganggu tidur/makan/fokus · 5 Kewalahan, sulit fokus atau tidur |
| D6 | Keluhan hari ini | Chip multi-select | `perih` (perih/nyeri ulu hati), `asam_naik` (panas di dada/asam naik), `mual`, `kembung`, `lelah` (lelah berat), `konsentrasi` (sulit konsentrasi/mengantuk berlebihan), `tidak_ada` (default, eksklusif) |
| D7 | Kesegaran saat bangun | 5 level | 1 Sangat tidak segar … 5 Sangat segar |

Hari tanpa keluhan: lima isian rutin + dua tap, sisanya default.

### 2.2 Profil (wajib diisi saat registrasi atau melengkapi profil, dengan consent)

| Kode | Parameter | Nilai | Peran |
| --- | --- | --- | --- |
| P1 | Riwayat maag/GERD yang pernah didiagnosis dokter | Ya / Tidak / Tidak tahu | Penguat skor lambung |
| P2 | Merokok | Tidak / Kadang / Rutin | Penguat skor lambung |
| P3 | Pemakaian rutin obat pereda nyeri (NSAID) | Tidak / Kadang / Rutin | Penguat skor lambung |
| P4 | Kondisi kronis lain (opsional) | Teks/pilihan | Konteks untuk dokter, **tidak masuk skor** |

Data profil bersifat sensitif: dienkripsi, hanya dapat dilihat dokter setelah mahasiswa menyetujui berbagi data, dan dapat diubah kapan saja.

### 2.3 Check-in mingguan (tahap 2, tidak berlaku di MVP)

| Kode | Parameter | Peran |
| --- | --- | --- |
| W1 | Frekuensi nyeri ulu hati/asam naik minggu ini | Skor lambung |
| W2 | Terbangun malam karena keluhan lambung | Skor lambung |
| W3 | Pemakaian antasida/pereda maag | Skor lambung |
| W4 | Tingkat kelelahan umum seminggu | Skor kelelahan |
| W5 | Stres skala baku singkat (mengacu PSS-4; cek versi bahasa Indonesia dan izin penggunaan) | Skor kelelahan/lambung |
| W6 | Suasana hati dan minat beraktivitas (2 butir, opsional) | Penguat skor kelelahan + jalur konseling |

W6 tidak menyimpulkan kondisi jiwa. Bila skornya tinggi, sistem hanya menampilkan arahan ke layanan konseling kampus. Penggunaannya memerlukan consent, validasi ahli, dan rujukan yang nyata.

### 2.4 Fitur turunan otomatis

| Kode | Turunan | Dipakai oleh |
| --- | --- | --- |
| A1 | `SLEEP_STREAK`: hari beruntun tidur < 6 jam sampai hari ini | Skor F, CBR |
| A2 | `LOW_SLEEP_DAYS_7`: jumlah hari tidur < 6 jam dalam 7 hari (tidak harus beruntun) | Skor F |
| A3 | `GASTRIC_HIT_DAYS_5`: hari dengan skor lambung ≥ 3 dalam 5 hari terakhir | Aturan pola |
| A4 | `GASTRIC_COMPLAINT_DAYS_5`: hari dengan chip `perih` atau `asam_naik` dalam 5 hari | Aturan keluhan |
| A5 | Tren 7 hari (membaik/memburuk) | Tampilan, CBR (opsional) |
| A6 | Konsistensi pengisian | Penyaring kualitas data |

Hari yang tidak diisi memutus streak dan dihitung "tidak diketahui". Aturan pola hanya berjalan bila minimal 3 hari dalam jendela terisi.

## 3. Tiga Level Keputusan

| Level | Perilaku UI |
| --- | --- |
| `NORMAL` | Simpan log, perbarui grafik, pesan edukasi ringan |
| `PERHATIAN_MANDIRI` | Kartu tips (higiene tidur / nutrisi), tanpa pop-up alarm |
| `PERLU_PERHATIAN` | Pop-up alert dengan alasan, tombol "Rekomendasi Konsultasi Dokter" aktif |

## 4. Aturan Kelelahan (Kelompok F)

Skor F = F1 + F2 + F3 + F4.

| Komponen | Kondisi | Poin |
| --- | --- | --- |
| F1 Durasi tidur hari ini | < 4,5 jam / 4,5 sampai < 6 / 6 sampai < 7 / ≥ 7 | 3 / 2 / 1 / 0 |
| F2 Kurang tidur | `SLEEP_STREAK` ≥ 3 | +2 |
|  | `SLEEP_STREAK` = 2 | +1 |
|  | streak < 2 **dan** `LOW_SLEEP_DAYS_7` ≥ 3 (kurang tidur tidak beruntun) | +1 |
| F3 Stres | `D5` ≥ 4 | +1 |
| F4 Kelelahan subjektif | `D7` ≤ 2 **atau** chip `lelah`/`konsentrasi` | +1 |
|  | `D7` ≤ 2 **dan** chip `lelah`/`konsentrasi` | +2 (menggantikan +1) |

| Skor F | Level |
| --- | --- |
| 0–1 | `NORMAL` |
| 2–3 | `PERHATIAN_MANDIRI` |
| ≥ 4 | `PERLU_PERHATIAN` (alert: Risiko Kelelahan Ekstrem & Burnout) |

## 5. Aturan Lambung (Kelompok M)

### 5.1 Skor harian

Skor dasar M = M1 + M2 + M3 + M4 + M5. Skor M akhir = skor dasar + M6.

| Komponen | Kondisi | Poin |
| --- | --- | --- |
| M1 Frekuensi makan | ≤ 1 kali / 2 kali / ≥ 3 kali | 3 / 2 / 0 |
| M2 Pemicu | tiap kategori di antara `Pedas`, `Asam`, `High-Caffeine` | +1 per kategori, maks +2 |
| M3 Makan dekat tidur | `D4` bukan `null` dan < 2 jam | +2 |
| M4 Stres | `D5` ≥ 3 | +1 |
| M5 Keluhan lambung | chip `perih` atau `asam_naik` | +2 |
|  | chip `mual` atau `kembung` (nonspesifik) | +1 |
| M6 Penguat profil | P1 = Ya (+1), P2 = Rutin (+1), P3 = Rutin (+1), maks +2; **hanya berlaku bila skor dasar ≥ 1** | +0 sampai +2 |

M6 hanya memperkuat, bukan memicu: orang dengan riwayat maag tetapi hari itu tanpa pemicu atau keluhan tidak mendapat peringatan hanya karena profilnya.

| Skor M harian | Level |
| --- | --- |
| 0–2 | `NORMAL` |
| 3–4 | `PERHATIAN_MANDIRI` |
| ≥ 5 | `PERLU_PERHATIAN` (alert: Risiko Gangguan Asam Lambung / Maag) |

### 5.2 Aturan keluhan dan pola

```text
M-K1: IF chip perih atau asam_naik hari ini
      THEN level lambung minimal PERHATIAN_MANDIRI

M-K2: IF GASTRIC_COMPLAINT_DAYS_5 >= 2 AND Skor M hari ini >= 3
      THEN PERLU_PERHATIAN   (reason: keluhan berulang disertai pemicu)

M-K3: IF GASTRIC_COMPLAINT_DAYS_5 >= 3
      THEN PERLU_PERHATIAN   (reason: keluhan lambung berulang)

M-P:  IF GASTRIC_HIT_DAYS_5 >= 3 (dan hari terisi >= 3)
      THEN PERLU_PERHATIAN   (reason: pola pemicu berulang)
```

### 5.3 Penggabungan dan cooldown

- `FINAL_RBR` = level tertinggi dari kelompok F dan M. Jika keduanya `PERLU_PERHATIAN`, kedua alert tampil dan konteks keduanya dikirim ke dokter.
- Alert yang ditolak mahasiswa tidak ditampilkan ulang selama 3 hari, kecuali skor naik. Evaluasi tetap dicatat di log.
- Setiap alert menyertakan `reasons[]` dalam bahasa sederhana.

## 6. CBR sebagai Pendukung

CBR **tidak dilatih**: pengetahuannya adalah pustaka kasus, dan Colab hanya dipakai untuk kalibrasi dan evaluasi (nilai `k`, ambang, bobot). Sistem harus tetap berfungsi penuh tanpa CBR.

### 6.1 Vektor Fitur (agregat 7 hari)

| Fitur | Cara hitung | Normalisasi ke 0–1 | Bobot |
| --- | --- | --- | --- |
| `avg_sleep` | rata-rata jam tidur | `1 - min(avg, 9) / 9` | 0.15 |
| `max_sleep_streak` | streak terpanjang | `min(streak, 7) / 7` | 0.15 |
| `avg_meal_freq` | rata-rata frekuensi makan | `1 - min(avg, 3) / 3` | 0.10 |
| `trigger_day_ratio` | proporsi hari dengan pemicu makanan | sudah 0–1 | 0.10 |
| `late_eating_ratio` | proporsi hari makan < 2 jam sebelum tidur | sudah 0–1 | 0.10 |
| `avg_stress` | rata-rata stres | `(avg - 1) / 4` | 0.10 |
| `gastric_complaint_ratio` | proporsi hari dengan chip keluhan lambung (`perih`, `asam_naik`, `mual`, `kembung`) | sudah 0–1 | 0.15 |
| `avg_freshness` | rata-rata kesegaran D7 | `1 - (avg - 1) / 4` | 0.15 |
| **Total** |  |  | **1.00** |

### 6.2 Aturan Pemakaian

```latex
\text{Similarity}(X, Y) = 1 - \sqrt{\sum_{i=1}^{n} w_i \cdot (x_i - y_i)^2}
```

- `k` = 5; tetangga mendukung bila similarity ≥ 0,80 (nilai awal, dikalibrasi di Colab).
- Hasil `RISIKO` bila ≥ 3 dari 5 tetangga mendukung dan berlabel `PERLU_PERHATIAN`.
- CBR berjalan hanya bila hasil RBR `PERHATIAN_MANDIRI`, dan **hanya boleh menaikkannya ke `PERLU_PERHATIAN`**, tidak pernah menurunkan hasil RBR.
- CBR dinonaktifkan otomatis bila kasus `approved` kurang dari 30, atau bila `cbr.enabled` bernilai false di konfigurasi.
- Kasus baru dari konsultasi berstatus `pending_review` sampai ahli/dokter menyetujuinya.

### 6.3 Status Seed Kasus

Kasus sintetis versi sebelumnya (39 kasus) belum memuat D6 dan D7, serta memakai versi aturan lama. Kasus perlu dibuat ulang dengan fitur baru dan dilabel ulang (kasus abu-abu oleh ahli, kasus jelas oleh RBR v2.1) sebelum dipakai atau dibawa ke pertemuan validasi.

## 7. Bagian `cds_config.json` yang Berubah

```json
{
  "rules_version": "2.1.0",
  "status": "draft_pending_expert_validation",
  "fatigue": {
    "streak_non_consecutive": { "window_days": 7, "min_low_days": 3, "points": 1 },
    "subjective": { "fresh_max": 2, "complaint_chips": ["lelah", "konsentrasi"], "points_one": 1, "points_both": 2 }
  },
  "gastric": {
    "complaint_points": { "specific": ["perih", "asam_naik"], "specific_points": 2,
                          "nonspecific": ["mual", "kembung"], "nonspecific_points": 1 },
    "profile_booster": { "p1_yes": 1, "p2_regular": 1, "p3_regular": 1, "max": 2, "requires_base_min": 1 },
    "complaint_rules": { "window_days": 5, "k2_min_days": 2, "k2_score_min": 3, "k3_min_days": 3 }
  },
  "cbr": {
    "enabled": true, "k": 5, "similarity_min": 0.80, "min_supporting": 3, "min_approved_cases": 30,
    "weights": { "avg_sleep": 0.15, "max_sleep_streak": 0.15, "avg_meal_freq": 0.10,
                 "trigger_day_ratio": 0.10, "late_eating_ratio": 0.10, "avg_stress": 0.10,
                 "gastric_complaint_ratio": 0.15, "avg_freshness": 0.15 }
  }
}
```

Bagian yang tidak tercantum (poin tidur, frekuensi makan, pemicu, cooldown, dan lainnya) tidak berubah dari v2.0. Kunci `red_flags` dihapus dari konfigurasi.

## 8. Skenario Uji

Hasil berikut dihitung dengan implementasi referensi aturan v2.1 dan dipakai sebagai unit test. Tanpa keterangan khusus, hari lain dalam riwayat adalah hari normal (tidur 7,5 jam, makan 3×, Netral, makan malam 3 jam sebelum tidur, stres 2, segar 4).

| # | Input | F | M | Hasil |
| --- | --- | --- | --- | --- |
| 1 | Semua hari normal | 0 | 0 | `NORMAL` |
| 2 | Hari ini tidur 5,5 jam, segar 3 | 2 | 0 | `PERHATIAN_MANDIRI` |
| 3 | Tidur 5 jam selama 3 hari terakhir, stres 3, segar 3 | 4 | 1 | `PERLU_PERHATIAN` (Kelelahan) |
| 4 | Tidur 6,5 jam, stres 4, segar 2, chip `lelah` | 4 | 1 | `PERLU_PERHATIAN` (Kelelahan) |
| 5 | Hari ini chip `perih`, makan 3× Netral | 0 | 2 | `PERHATIAN_MANDIRI` (aturan M-K1) |
| 6 | Chip `perih`, makan 2×, Pedas, makan malam 3 jam sebelum tidur | 0 | 5 | `PERLU_PERHATIAN` (Maag) |
| 7 | Chip `perih` pada 3 dari 5 hari terakhir, hari ini makan 3× Netral | 0 | 2 | `PERLU_PERHATIAN` (aturan M-K3) |
| 8 | Tidur selang-seling 4, 8, 4, 8, 4, 8, 5 jam; stres 3, segar 3 | 3 | 1 | `PERHATIAN_MANDIRI` (F2 non-beruntun) |
| 9a | Hari ini makan 2× Netral, tanpa keluhan, tanpa profil berisiko | 0 | 2 | `NORMAL` |
| 9b | Kasus 9a dengan P1 = Ya dan P3 = Rutin | 0 | 4 | `PERHATIAN_MANDIRI` (penguat profil) |
| 10 | Semua hari normal, P1 = Ya dan P3 = Rutin | 0 | 0 | `NORMAL` (penguat profil tidak berlaku tanpa skor dasar) |
| 11 | Tidur 4 jam, makan 2× Pedas, makan malam 1 jam sebelum tidur, stres 4, segar 1, chip `lelah` + `perih` selama 5 hari | 8 | 8 | `PERLU_PERHATIAN` (Kelelahan + Maag) |
| 12 | Alert Kelelahan ditolak kemarin, skor sama hari ini | n/a | n/a | Ditekan (cooldown), tetap dilog |

Skenario CBR (RBR `PERHATIAN_MANDIRI` + 3 dari 5 tetangga berisiko → naik ke `PERLU_PERHATIAN`; RBR `NORMAL` + CBR berisiko → tetap `NORMAL`) tidak berubah dari v2.0.

## 9. Butir Tambahan untuk Divalidasi Ahli

Selain butir v2.0 (ambang tidur, poin makan, pemicu, pola 5 hari), ahli diminta menilai:

1. Daftar chip keluhan D6 dan pembagian **spesifik** (`perih`, `asam_naik`) vs **nonspesifik** (`mual`, `kembung`).
2. Poin keluhan lambung (+2 / +1) dan aturan M-K1, M-K2, M-K3.
3. Batas kesegaran bangun (D7 ≤ 2 dianggap tidak segar) dan poin F4.
4. Penguat profil P1–P3 beserta bobotnya, dan apakah merokok dan NSAID layak dihitung.
5. Deskripsi lima level stres (D5) dan apakah ambang stres ≥ 3 untuk lambung tidak terlalu rendah.
6. Aturan kurang tidur tidak beruntun (F2).
7. (Tahap 2) Isi check-in mingguan W1–W6 dan penggunaan W6 sebagai penguat serta jalur konseling.
8. Apakah penghapusan checklist gejala bahaya dapat diterima, dan teks disclaimer apa yang perlu ditampilkan.

## 10. Pekerjaan Lanjutan

1. Buat ulang 39 kasus sintetis dengan D6 dan D7 dan hitung label RBR v2.1.
2. Terjemahkan 13 skenario di atas menjadi test Pest/PHPUnit untuk `CdsEvaluatorService`.
3. Perbarui dokumen validasi untuk ahli (dosen keperawatan) dengan tabel di bagian 4–5 dan butir di bagian 9.
4. Perbarui PRD (form harian, profil wajib saat registrasi, penghapusan R-00).
5. Susun SRS berdasarkan dokumen ini.
