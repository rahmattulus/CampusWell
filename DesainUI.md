# DesainUI — CampusWell
Panduan desain antarmuka untuk platform CampusWell (Habit Tracker + CDSS + Telemedicine Chat Klinik Kampus).

- **Stack frontend:** Laravel Blade + Tailwind CSS (Alpine.js untuk interaksi ringan, Livewire opsional untuk chat)
- **Mode tampilan:** Light mode saja (MVP)
- **Bahasa antarmuka:** Indonesia
- **Status dokumen:** v1.0. Field parameter CDS mengikuti Spesifikasi CDS v1; akan disesuaikan setelah CDS v2.1 final dan validasi ahli.

---

## 1. Prinsip Desain

1. **Bersih dan tenang.** Latar netral hangat, kartu putih/pastel membulat, sedikit dekorasi. Aplikasi kesehatan harus terasa menenangkan, bukan ramai.
2. **Angka dan status sebagai fokus.** Informasi terpenting (skor, status CDS, jumlah antrian) ditampilkan besar dan dibaca dalam sekilas.
3. **Satu layar, satu tujuan.** Setiap halaman punya satu aksi utama yang jelas (tombol hitam pekat).
4. **Cepat diisi.** Input harian mahasiswa < 1 menit: slider, chip, dan segmented button, bukan ketik teks.
5. **Jelas sebelum cantik (sisi dokter/admin).** Hierarki visual tegas, kontras tinggi, label eksplisit, minim animasi, mudah dipindai saat sibuk.
6. **Status tidak hanya warna.** Setiap status memakai warna + ikon + teks agar tetap terbaca (aksesibilitas).
7. **Skrining, bukan diagnosis.** Setiap hasil CDS memuat disclaimer klinis yang konsisten.

---

## 2. Platform, Breakpoint, dan Pendekatan Responsif

| Peran | Pendekatan | Target utama | Fallback |
| :--- | :--- | :--- | :--- |
| Mahasiswa | **Mobile-first** | 360–430 px (ponsel) | Tablet/desktop: konten dipusatkan, lebar maks 480 px dengan latar kanvas di sisi |
| Dokter | **Desktop-first** | ≥ 1280 px | Tablet (768–1279): sidebar menciut jadi ikon; < 768: layout satu kolom dengan tab (hanya untuk keadaan darurat) |
| Admin | **Desktop-first** | ≥ 1280 px | Sama seperti dokter |

**Breakpoint Tailwind (default):** `sm 640`, `md 768`, `lg 1024`, `xl 1280`, `2xl 1536`.

- Mahasiswa: tulis kelas dasar untuk mobile, tambah `md:`/`lg:` hanya untuk memusatkan kontainer.
- Dokter/Admin: tulis untuk `lg:`/`xl:` lalu tangani penyusutan di bawahnya.

---

## 3. Design Tokens

### 3.1 Palet Warna

**Netral (dipakai semua peran)**

| Token | Hex | Penggunaan |
| :--- | :--- | :--- |
| `canvas` | `#F4F3F0` | Latar halaman |
| `surface` | `#FFFFFF` | Kartu, panel, tabel |
| `surface-soft` | `#FAF9F7` | Kartu sekunder, hover |
| `line` | `#E7E5E0` | Garis pemisah, border kartu |
| `ink` | `#17181A` | Teks utama, tombol primer |
| `ink-soft` | `#4B5058` | Teks sekunder |
| `ink-mute` | `#7A808A` | Label, placeholder, keterangan |

**Pastel aksen (kartu fitur, mahasiswa)**

| Token | Latar | Aksen kuat | Penggunaan |
| :--- | :--- | :--- | :--- |
| `sage` | `#E3EBD3` | `#7F9A66` | Kartu utama "Kesejahteraan", tidur |
| `lavender` | `#E8E1F3` | `#8E7BBF` | Pola makan, bottom nav, konsultasi |
| `sky` | `#DCE7F7` | `#6F93CC` | Grafik tren, riwayat |
| `butter` | `#F8ECCB` | `#C99A2E` | Stres, ringkasan harian |

**Status CDS (konsisten di seluruh sistem)**

| Status | Latar | Teks/ikon | Ikon |
| :--- | :--- | :--- | :--- |
| Normal | `#DDEBD8` | `#2F6B3A` | centang-lingkaran |
| Perhatian Mandiri | `#FBEBC8` | `#8A5A00` | lampu/info |
| Perlu Perhatian | `#FBDDD8` | `#A5372B` | segitiga peringatan |

**Status sistem:** `info #DCE7F7 / #2F5A94`, `sukses #DDEBD8 / #2F6B3A`, `error #FBDDD8 / #A5372B`. Status dokter: `online #2F6B3A` (titik hijau), `offline #7A808A` (titik abu).

**Aturan pemakaian:** Sisi mahasiswa memakai pastel bebas pada kartu. Sisi dokter/admin memakai `surface` putih sebagai dasar, pastel hanya sebagai aksen kecil (kartu ringkasan, badge). Jangan memakai lebih dari 2 warna pastel dalam satu layar dokter.

### 3.2 Tipografi

- **Font:** `Plus Jakarta Sans` (Google Fonts, bobot 400/500/600/700). Cadangan: `ui-sans-serif, system-ui, sans-serif`.
- Seluruh peran memakai satu keluarga font agar konsisten.

| Token | Ukuran / Line-height | Bobot | Penggunaan |
| :--- | :--- | :--- | :--- |
| `display` | 56 / 60 | 500 | Angka besar (skor 82, jam tidur) — mahasiswa |
| `h1` | 28 / 34 | 600 | Judul halaman |
| `h2` | 20 / 26 | 600 | Judul kartu/bagian |
| `body` | 15 / 22 | 400 | Teks isi (dokter/admin: 14 / 20 untuk tabel) |
| `caption` | 12 / 16 | 500 | Label, keterangan, badge |

Minimum ukuran teks isi: 14 px (dokter/admin), 15 px (mahasiswa).

### 3.3 Radius, Jarak, Bayangan

- **Radius:** `card 24px` (mahasiswa) / `card 16px` (dokter/admin), `control 12px`, `pill 9999px`, `chip 9999px`.
- **Jarak (skala 4):** 4, 8, 12, 16, 20, 24, 32, 40. Padding kartu mahasiswa 16–20; kartu dokter 16.
- **Bayangan:** hampir tidak ada. Kedalaman lewat kontras latar. `shadow-card: 0 1px 2px rgba(23,24,26,.04)`; `shadow-float: 0 8px 24px rgba(23,24,26,.10)` hanya untuk modal dan bottom sheet.
- **Ikon:** Lucide (outline, stroke 1.75). Ukuran 20 px (UI), 24 px (navigasi).

### 3.4 Konfigurasi Tailwind (`tailwind.config.js`)

```js
export default {
  content: ['./resources/**/*.blade.php', './resources/**/*.js'],
  theme: {
    extend: {
      fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
      colors: {
        canvas: '#F4F3F0', surface: '#FFFFFF', 'surface-soft': '#FAF9F7', line: '#E7E5E0',
        ink: { DEFAULT: '#17181A', soft: '#4B5058', mute: '#7A808A' },
        sage: { DEFAULT: '#E3EBD3', strong: '#7F9A66' },
        lavender: { DEFAULT: '#E8E1F3', strong: '#8E7BBF' },
        sky: { DEFAULT: '#DCE7F7', strong: '#6F93CC' },
        butter: { DEFAULT: '#F8ECCB', strong: '#C99A2E' },
        status: {
          'normal-bg': '#DDEBD8', 'normal-fg': '#2F6B3A',
          'self-bg': '#FBEBC8', 'self-fg': '#8A5A00',
          'risk-bg': '#FBDDD8', 'risk-fg': '#A5372B',
        },
      },
      borderRadius: { card: '24px', 'card-sm': '16px', control: '12px' },
      boxShadow: { card: '0 1px 2px rgba(23,24,26,.04)', float: '0 8px 24px rgba(23,24,26,.10)' },
    },
  },
};
```

---

## 4. Pustaka Komponen

Implementasi sebagai Blade anonymous component (`resources/views/components/...`), dipanggil dengan `<x-nama />`.

| Komponen | Spesifikasi |
| :--- | :--- |
| **Card** (`x-card`) | `bg-surface rounded-card p-5`, varian warna pastel lewat prop `tone="sage|lavender|sky|butter"`. Tanpa border; dokter/admin memakai `border border-line`. |
| **Button primer** | Hitam `bg-ink text-white rounded-full h-12 px-6 font-semibold`. Satu per layar. |
| **Button sekunder** | `bg-surface border border-line text-ink`, bentuk sama. |
| **Button bahaya** | `bg-status-risk-bg text-status-risk-fg`, untuk aksi destruktif. |
| **Icon button** | Lingkaran 44×44, `bg-surface`, ikon 20 px (seperti tombol `…`, cari, kembali pada referensi). |
| **FAB "+"** | Lingkaran hitam 56 px, kanan atas judul Beranda, membuka **modal Habit Tracker** (M-04). |
| **Slider** | Track 8 px, thumb 28 px hitam, nilai terkini ditampilkan besar di kanan label. Hit area ≥ 44 px. |
| **Segmented / angka** | Baris tombol sama lebar (0–5, 1–5); yang aktif hitam, teks putih. |
| **Chip pilihan ganda** | Pil `h-10 px-4`; aktif `bg-ink text-white`; tidak aktif `bg-surface border-line`. |
| **Badge status CDS** | Pil kecil sesuai tabel status (warna + ikon + teks). |
| **Alert banner** | Kartu dengan strip kiri 4 px sesuai status, ikon, judul tebal, penjelasan 1–2 kalimat, tombol aksi. |
| **Tabel (dokter/admin)** | Header `caption` huruf besar kecil abu, baris tinggi 48 px, hover `surface-soft`, kolom aksi rata kanan. |
| **Bottom nav (mahasiswa)** | Bar membulat `bg-lavender rounded-full` melayang 12 px dari tepi bawah; item aktif = lingkaran hitam dengan ikon putih. |
| **Sidebar (dokter/admin)** | Lebar 248 px, putih, item aktif `bg-ink text-white rounded-control`; menciut 72 px (ikon) di bawah `xl`. |
| **Bubble chat** | Mahasiswa: `bg-ink text-white` kanan; dokter: `bg-surface-soft` kiri; radius 20, ekor 6 px; stempel waktu `caption`. |
| **Modal / bottom sheet** (`x-modal`) | Mobile: bottom sheet dengan handle; desktop: modal tengah 440–480 px; latar gelap 40%. Header dan footer menempel, isi bergulir, focus trap, `Esc` menutup (kecuali varian yang ditandai wajib dipilih). Dipakai untuk M-04 (form tracker) dan M-05 (hasil CDS); dua modal tidak tampil bersamaan, M-04 menutup lalu M-05 terbuka. |
| **Toast** | Kanan atas (desktop) / atas tengah (mobile), 4 detik, ikon + teks. |
| **Skeleton / empty / error** | Setiap daftar dan grafik punya tiga keadaan: memuat (skeleton abu), kosong (ilustrasi ikon + teks + aksi), gagal (pesan + "Coba lagi"). |
| **Form field** | Label di atas, tinggi 48 px, radius 12, border `line`, fokus ring 2 px `ink`; pesan galat merah di bawah field. |

**Disclaimer klinis (standar):** *"Hasil ini merupakan skrining awal dan bukan diagnosis. Keputusan diagnosis dan terapi ditentukan oleh dokter."* Tampil kecil di bawah setiap alert dan hasil evaluasi.

---

## 5. Tampilan Mahasiswa (Mobile-First)

### 5.1 Kerangka Layout
- Kontainer `max-w-[480px] mx-auto px-4`, latar `canvas`.
- Header: avatar kiri, tombol cari dan menu kanan (mengikuti referensi). Judul besar `h1` gaya "Kesehatanmu hari ini" di bawahnya.
- Konten berupa tumpukan kartu membulat dengan jarak 12 px.
- **Bottom nav (4 item):** Beranda · Riwayat · Konsultasi · Profil. Habit Tracker bukan halaman, melainkan **modal** yang dibuka dari FAB "+" di Beranda atau tombol "Isi sekarang" pada kartu log harian.
- Padding bawah konten 96 px agar tidak tertutup bottom nav; hormati `env(safe-area-inset-bottom)`.

### 5.2 Daftar Halaman

#### M-01 · Login
- Logo + judul "Masuk ke CampusWell", field NIM dan kata sandi, tombol primer "Masuk", tautan "Lupa kata sandi" dan "Daftar".
- Galat: pesan di bawah field; akun belum diverifikasi admin → banner info "Akunmu menunggu verifikasi NIM".

#### M-02 · Registrasi
- Langkah 1: nama, NIM, email kampus, kata sandi (indikator kekuatan).
- Langkah 2: **Profil kesehatan dasar / faktor risiko** (wajib diisi; daftar field final menunggu CDS v2.1).
- Indikator langkah di atas (2 titik). Setelah selesai: layar "Menunggu verifikasi admin".

#### M-03 · Beranda ("Kesehatanmu hari ini")
Urutan kartu dari atas:
1. **Kartu Kesejahteraan** (`sage`): status CDS terbaru sebagai badge, angka besar ringkasan (mis. jam tidur semalam), mini-grafik 7 hari, perubahan mingguan.
2. **Tiga kartu mini** (`sage` terang): Tidur, Makan (kali/hari), Stres hari ini.
3. **Kartu "Log hari ini"** (`butter`): bila belum diisi → tombol primer "Isi sekarang" (membuka modal M-04); bila sudah → centang + jam pengisian.
4. **Alert aktif** (bila ada): banner Perlu Perhatian/Perhatian Mandiri dengan aksi "Konsultasi Dokter".
5. **Kartu Konsultasi** (`lavender`): sesi aktif/terakhir atau "Lihat dokter tersedia".
- Keadaan kosong (pengguna baru): ilustrasi sederhana + "Mulai catat kebiasaanmu hari ini".

#### M-04 · Modal Form Habit Tracker
Form tampil sebagai **modal di atas Beranda**, bukan halaman terpisah.

**Perilaku modal**
- **Mobile:** bottom sheet hampir penuh (tinggi 92 vh), sudut atas membulat 28 px, handle di puncak. **Desktop (≥ `md`):** modal tengah lebar 480 px, tinggi maks 90 vh.
- **Header menempel:** judul "Log Hari Ini", tanggal, tombol tutup (X).
- **Isi bergulir** di tengah; **footer menempel** berisi tombol primer "Simpan & Lihat Hasil" (satu-satunya aksi primer).
- **Menutup:** tombol X, `Esc`, tarik ke bawah (mobile), atau ketuk latar. Bila sudah ada isian → dialog kecil "Buang isian hari ini?" (Buang / Lanjut mengisi). Bila belum ada isian → langsung tertutup.
- Fokus berpindah ke modal saat dibuka dan dikunci di dalamnya (focus trap); fokus kembali ke tombol pemicu saat ditutup. Latar halaman tidak dapat digulir selama modal terbuka.
- Bila log hari ini sudah ada, tombol pemicu berubah menjadi "Ubah log hari ini" dan modal terbuka dengan nilai tersimpan.

**Isi form** (≈ 30–45 detik pengisian)

| Urutan | Field (kode) | Kontrol UI | Catatan |
| :--- | :--- | :--- | :--- |
| 1 | Durasi tidur (`PARAM_SLEEP_HOURS`) | Slider 0–12 jam, langkah 0,5; nilai besar di kanan | Streak kurang tidur dihitung otomatis dan ditampilkan sebagai keterangan ("Hari ke-3 kurang tidur") |
| 2 | Frekuensi makan (`PARAM_MEAL_FREQ`) | Segmented 0–5+ | |
| 3 | Kategori makanan (`PARAM_FOOD_CATEGORY`) | Chip multi-pilih: Netral, Pedas, Asam, Manis, Kafein tinggi | "Netral" menghapus pilihan lain |
| 4 | Jarak makan–tidur (`PARAM_LATE_EATING`) | Slider 0–6+ jam | Label: 0 = langsung tidur |
| 5 | Tingkat stres (`PARAM_STRESS_LEVEL`) | Segmented 1–5 dengan label ujung "Santai"–"Sangat stres" | |

- Setiap field memiliki label + nilai terkini; kelompok diberi blok pastel berbeda (tidur `sage`, makan `lavender`, stres `butter`).
- Validasi: semua field wajib, tanpa pre-fill agar tidak terisi asal. Field yang belum diisi diberi tanda dan modal menggulir ke field pertama yang kosong saat tombol ditekan.
- Saat menyimpan: tombol menampilkan keadaan memuat (nonaktif + spinner kecil), lalu modal ini **menutup dan modal hasil (M-05) langsung terbuka**.
- Galat simpan/jaringan: toast "Gagal menyimpan, coba lagi" di dalam modal; isian tidak hilang.

#### M-05 · Modal Hasil Evaluasi CDS
Muncul **otomatis** begitu form M-04 berhasil disimpan, sebagai modal kedua (bottom sheet dengan tinggi menyesuaikan isi di mobile; modal tengah 440 px di desktop). Tidak ada halaman hasil terpisah.

| Varian | Tampilan | Aksi |
| :--- | :--- | :--- |
| **Normal** | Ikon centang di lingkaran `sage`, judul "Kebiasaanmu hari ini baik", pesan edukasi ringan 1–2 kalimat | Primer "Selesai"; sekunder "Lihat grafik" (menutup modal, menuju Riwayat) |
| **Perhatian Mandiri** | Ikon info di lingkaran `butter`, judul jenis perhatian, 2–3 poin saran spesifik (higiene tidur / nutrisi), tanpa mendorong konsultasi | Primer "Selesai"; sekunder "Lihat grafik" |
| **Perlu Perhatian** | Ikon peringatan di lingkaran `status-risk-bg`, judul jenis risiko (mis. "Risiko Kelelahan Ekstrem & Burnout"), 1–2 kalimat alasan bahasa awam, disclaimer klinis | Primer **"Rekomendasi Konsultasi Dokter"** (menutup modal, menuju M-07 dengan alert terlampir); sekunder "Nanti saja" |

- Semua varian menampilkan disclaimer klinis di bagian bawah modal.
- **Perlu Perhatian tidak dapat ditutup dengan mengetuk latar atau `Esc`;** pengguna harus memilih salah satu tombol agar peringatan tidak terlewat. Varian lain boleh ditutup dengan X, latar, atau `Esc`.
- Bila dua alert terpicu sekaligus, keduanya bertumpuk dalam satu modal (isi bergulir) dengan **satu** tombol konsultasi yang melampirkan kedua jenis alert.
- Menutup modal (termasuk "Nanti saja") mengembalikan pengguna ke Beranda yang sudah diperbarui. **Alert Perlu Perhatian tetap tampil sebagai banner di Beranda** (M-03), sehingga pengguna tetap bisa berkonsultasi nanti.
- Tombol back browser/ponsel menutup modal, bukan meninggalkan halaman.
- Nada bahasa: tenang, tidak menakut-nakuti, tanpa istilah diagnosis ("indikasi risiko", bukan "kamu menderita").

#### M-06 · Riwayat dan Grafik
- Tab segmented **Hari · Minggu · Bulan** (seperti referensi).
- Kartu `sky` berisi grafik garis tren (tidur) dengan titik terpilih; kartu `lavender` untuk pola makan (bar frekuensi); rentang harian dan rata-rata di kartu kecil.
- Daftar log harian di bawah, tiap baris: tanggal, ringkasan, badge status. Ketuk → detail log.
- Kalimat ringkasan otomatis ("Tidurmu rata-rata 5j 40m minggu ini").

#### M-07 · Daftar Dokter
- Daftar kartu dokter: foto/inisial, nama, spesialisasi/bagian, jadwal praktik, titik status Online/Offline.
- Dokter online di atas; dokter offline dinonaktifkan dengan keterangan jadwal berikutnya.
- Bila datang dari alert CDS: banner di atas "Ringkasan habit & alert akan dikirim ke dokter".
- Aksi per kartu: "Mulai Konsultasi" (hanya jika online).

#### M-08 · Konfirmasi Sebelum Chat
- Bottom sheet: dokter terpilih, daftar lampiran otomatis (ringkasan habit 7 hari, jenis alert), kolom keluhan singkat (opsional), tombol primer "Kirim Permintaan".

#### M-09 · Menunggu Dokter
- Status tiket: *Menunggu → Diterima → Berlangsung → Selesai / Kedaluwarsa*.
- Hitung mundur **30 menit** sebelum tiket kedaluwarsa. Saat kedaluwarsa: layar info + tombol "Pilih dokter lain".

#### M-10 · Ruang Chat
- Header: tombol kembali, nama dokter, titik status, menu `…`.
- Kartu **ringkasan terlampir** (dapat dilipat) di puncak percakapan: grafik mini 7 hari + badge alert.
- Daftar bubble, indikator mengetik, status terkirim/dibaca, kolom input menempel bawah dengan tombol kirim bulat hitam.
- Saat sesi ditutup dokter: banner "Sesi selesai", input dinonaktifkan, tampil catatan/rekomendasi dokter dalam kartu.

#### M-11 · Riwayat Konsultasi
- Daftar sesi (tanggal, dokter, status). Detail: percakapan hanya-baca + catatan dokter + rekomendasi rujukan.

#### M-12 · Notifikasi
- Daftar: hasil evaluasi, tiket diterima/ditutup, pengingat isi log harian. Titik biru untuk belum dibaca.

#### M-13 · Profil dan Pengaturan
- Data akun, profil kesehatan dasar (dapat diperbarui), pengaturan notifikasi/pengingat, ubah kata sandi, keluar, informasi privasi dan disclaimer klinis.

---

## 6. Tampilan Dokter (Desktop-First)

### 6.1 Prinsip UX Khusus Dokter
1. **Status dan antrian selalu terlihat** di topbar (toggle Online/Offline + jumlah permintaan menunggu).
2. **Tiga pertanyaan per layar:** siapa pasiennya, apa konteksnya, apa aksi berikutnya. Letakkan jawabannya tanpa perlu scroll.
3. **Densitas sedang:** teks 14 px, baris tabel 48 px, tidak terlalu rapat.
4. **Minim klik:** dari notifikasi permintaan ke chat ≤ 2 klik. Konteks habit sudah terlampir, tidak perlu menanyakan ulang.
5. **Kontras tinggi dan warna fungsional:** merah hanya untuk "Perlu Perhatian", jangan dipakai dekoratif.
6. **Tanpa animasi mencolok;** hanya transisi 150 ms. Suara/notifikasi browser untuk permintaan baru (dapat dimatikan).
7. **Pintasan keyboard:** `Ctrl+Enter` kirim pesan, `/` fokus pencarian, `Esc` tutup panel.

### 6.2 Kerangka Layout
- **Sidebar kiri 248 px:** logo, menu (Dashboard, Permintaan, Konsultasi Aktif, Pasien & Rekam Medis, Jadwal & Status, Profil), kartu profil dokter di bawah.
- **Topbar 64 px:** judul halaman, pencarian pasien (NIM/nama), toggle status, ikon notifikasi, avatar.
- **Area konten:** `max-w-[1440px]`, padding 24–32, latar `canvas`, panel putih `rounded-card-sm border border-line`.

### 6.3 Daftar Halaman

#### D-01 · Login
- Kartu tengah 400 px: email/ID dokter, kata sandi, "Masuk". Galat inline.

#### D-02 · Dashboard
- Baris atas 4 kartu ringkasan: **Menunggu**, **Berlangsung**, **Selesai hari ini**, **Pasien dengan alert Perlu Perhatian**.
- Panel kiri lebar (2/3): **Antrian permintaan** (tabel ringkas 5 teratas): pasien, jenis alert (badge), waktu tunggu, sisa waktu kedaluwarsa, tombol "Terima".
- Panel kanan (1/3): **Sesi berlangsung** (daftar dengan pasien dan pesan belum dibaca) dan **Jadwal praktik hari ini**.
- Keadaan kosong: "Belum ada permintaan konsultasi" + status Online/Offline.

#### D-03 · Permintaan Konsultasi
- Tabel: pasien (nama, NIM), jenis alert, ringkasan keluhan, waktu masuk, sisa waktu, aksi **Terima / Tolak** (tolak wajib alasan singkat).
- Filter: semua · hanya "Perlu Perhatian" · menunggu terlama. Pengurutan default: alert Perlu Perhatian di atas, lalu terlama.
- Klik baris → panel samping pratinjau konteks tanpa meninggalkan halaman.

#### D-04 · Ruang Konsultasi (halaman terpenting)
Layout tiga kolom:

| Kolom | Lebar | Isi |
| :--- | :--- | :--- |
| Kiri | 280 px | Daftar sesi aktif (nama, pratinjau pesan, badge belum dibaca) |
| Tengah | fleksibel | Chat: header pasien + status sesi, daftar pesan, input dengan balasan cepat/templat, tombol **Tutup Sesi** |
| Kanan | 360 px | **Panel konteks pasien:** badge alert + aturan yang terpicu (mis. F-02), grafik habit 7 hari (tidur, makan, stres), nilai parameter terakhir, riwayat konsultasi sebelumnya |

- Panel kanan dapat diciutkan; disclaimer "skrining awal" tampil di atasnya.
- **Templat balasan cepat** (edukasi higiene tidur, saran nutrisi, anjuran ke klinik) dapat disisipkan ke input.

#### D-05 · Tutup Sesi (modal/panel formulir)
- Field: **diagnosis akhir** (pilihan umum + teks bebas), **catatan medis**, **rekomendasi** (edukasi mandiri / kontrol ulang / rujuk ke klinik), tanggal kontrol (opsional).
- Teks bantu: "Diagnosis akhir akan dicatat sebagai kasus untuk memperkaya pustaka kasus CDS."
- Tombol primer "Simpan & Tutup Sesi"; konfirmasi karena tidak dapat diubah (koreksi lewat addendum).

#### D-06 · Pasien dan Rekam Medis
- Pencarian pasien yang pernah berkonsultasi dengannya (RBAC: hanya pasien miliknya).
- Detail pasien: profil singkat, tab **Ringkasan · Riwayat Habit · Konsultasi · Catatan Medis**. Grafik tren memanjang (bulan), daftar alert yang pernah terpicu.

#### D-07 · Jadwal dan Status
- Toggle besar Online/Offline dengan keterangan efeknya ("Mahasiswa dapat melihat dan mengirim permintaan").
- Editor jadwal praktik mingguan (hari, jam mulai–selesai). Opsi "otomatis offline di luar jadwal".

#### D-08 · Profil Dokter
- Data diri, spesialisasi, foto, ubah kata sandi, preferensi notifikasi (suara/browser).

---

## 7. Tampilan Admin (Desktop-First)

Kerangka layout sama dengan dokter (sidebar + topbar) agar pola navigasi konsisten.

#### A-01 · Dashboard Admin
- Kartu: pengguna aktif, akun menunggu verifikasi, evaluasi CDS hari ini, persentase Normal / Perhatian Mandiri / Perlu Perhatian (bar bertumpuk).
- Panel "Perlu tindakan": verifikasi tertunda, kegagalan pencadangan.

#### A-02 · Manajemen Pengguna
- Tabs: **Mahasiswa · Dokter · Admin**. Tabel: nama, NIM/ID, status (aktif, menunggu, nonaktif), tanggal daftar, aksi.
- **Verifikasi NIM:** antrian akun menunggu dengan tombol Setujui/Tolak (alasan wajib bila ditolak). Aksi massal untuk persetujuan.
- Form tambah akun dokter (nama, email, spesialisasi, kata sandi sementara), tombol nonaktifkan akun.

#### A-03 · Konfigurasi Aturan CDS
- Tabel aturan (ID, nama, kondisi, ambang, status aktif).
- Edit ambang lewat form terstruktur (bukan teks bebas): setiap parameter ditampilkan dengan input angka dan batas wajar; pratinjau "aturan ini akan menjadi: …" dalam kalimat IF-THEN.
- **Versioning:** setiap perubahan butuh catatan alasan (mis. "Revisi validasi ahli"), tersimpan dengan siapa, kapan, nilai lama → baru. Tombol "Kembalikan ke versi sebelumnya".
- Banner peringatan: perubahan ambang memengaruhi evaluasi seluruh pengguna.

#### A-04 · Log Audit CDS
- Tabel evaluasi berstatus Perlu Perhatian: waktu, ID aturan terpicu, nilai input, pengguna (dengan akses terbatas), status tindak lanjut. Filter tanggal, aturan, status. Ekspor CSV.
- Log perubahan konfigurasi dan akses data ditampilkan di tab terpisah.

#### A-05 · Sistem dan Keamanan
- Status pencadangan terakhir, tombol cadangan manual, riwayat cadangan.
- Sesi pengguna aktif, kebijakan kata sandi, pengaturan masa kedaluwarsa tiket (default 30 menit).

---

## 8. Pola Interaksi dan Keadaan Umum

| Situasi | Perilaku |
| :--- | :--- |
| Memuat | Skeleton sesuai bentuk kartu/tabel, bukan spinner penuh layar |
| Kosong | Ikon + satu kalimat + satu aksi |
| Galat jaringan | Banner "Koneksi terputus" + coba lagi; draf chat dan form tidak hilang |
| Sukses simpan | Toast singkat + perubahan langsung terlihat |
| Aksi destruktif | Dialog konfirmasi dengan tombol bahaya |
| Sesi kedaluwarsa (login) | Arahkan ke login, simpan halaman tujuan |
| Real-time chat | Pesan masuk otomatis (Reverb/Pusher atau polling) + indikator "terhubung kembali" |

---

## 9. Aksesibilitas

- Kontras teks ≥ 4,5:1 (teks `ink` di atas semua latar pastel memenuhi); teks `ink-mute` hanya untuk label non-kritis.
- Target sentuh ≥ 44×44 px di mobile.
- Fokus keyboard terlihat (ring 2 px `ink`) di semua kontrol; urutan tab logis.
- Status CDS selalu ikon + teks + warna. Grafik memakai label nilai, tidak hanya warna.
- Semua input punya `label` terkait; pesan galat dibaca pembaca layar (`aria-live="polite"`).
- Hormati `prefers-reduced-motion`.

---

## 10. Microcopy (Bahasa Indonesia)

- Nada: ramah, singkat, tenang. Gunakan "kamu" pada mahasiswa dan bahasa profesional netral pada dokter/admin.
- Contoh alert risiko kelelahan: *"Kamu sudah beberapa hari kurang tidur. Pola ini bisa meningkatkan risiko kelelahan ekstrem. Yuk, konsultasi dengan dokter klinik kampus."*
- Contoh alert risiko lambung: *"Pola makanmu beberapa waktu terakhir berisiko memicu gangguan asam lambung. Dokter klinik dapat membantu memeriksanya."*
- Hindari: "Kamu sakit/menderita…", istilah diagnosis tanpa konteks, tanda seru berlebihan pada alert risiko.

---

## 11. Struktur Blade dan Konvensi

```
resources/views/
├── layouts/
│   ├── student.blade.php      # mobile shell + bottom nav
│   ├── staff.blade.php        # sidebar + topbar (dokter & admin)
│   └── guest.blade.php        # login/registrasi
├── components/
│   ├── card.blade.php
│   ├── button.blade.php
│   ├── badge-status.blade.php
│   ├── alert-cds.blade.php
│   ├── modal.blade.php
│   ├── slider-field.blade.php
│   ├── segmented.blade.php
│   ├── chip-group.blade.php
│   ├── chat-bubble.blade.php
│   ├── data-table.blade.php
│   └── empty-state.blade.php
├── student/   (home, history, doctors, chat, profile; modal: tracker-modal, cds-result-modal)
├── doctor/    (dashboard, requests, consultation, patients, schedule)
└── admin/     (dashboard, users, cds-rules, audit-log, system)
```

- Satu sumber kebenaran warna status: komponen `badge-status` dan `alert-cds` menerima prop `level` (`normal|self|risk`).
- Jangan menulis kelas warna heksa langsung di view; pakai token Tailwind.
- Grafik: Chart.js dengan warna dari token yang sama.

---

## 12. Urutan Pengerjaan UI (usulan)

1. Siapkan `tailwind.config.js`, font, dan layout (`student`, `staff`, `guest`).
2. Bangun komponen dasar: card, button, badge-status, alert-cds, slider, segmented, chip.
3. Halaman mahasiswa inti: Beranda → komponen `x-modal` → Modal Habit Tracker (M-04) → Modal Hasil CDS (M-05, 3 varian) → Riwayat.
4. Alur konsultasi mahasiswa: Daftar dokter → Konfirmasi → Menunggu → Chat.
5. Dokter: layout staff → Dashboard → Permintaan → Ruang Konsultasi → Tutup Sesi.
6. Admin: Manajemen pengguna → Konfigurasi CDS → Log audit.
7. Login/registrasi, notifikasi, profil, lalu uji responsif dan aksesibilitas.

---

## 13. Hal Terbuka

- Field profil faktor risiko (M-02) dan parameter tambahan (mis. kesegaran bangun, suasana hati) menunggu spesifikasi CDS v2.1.
- Ambang dan teks alert dapat berubah setelah validasi ahli; teks diambil dari konfigurasi, bukan ditulis permanen di view.
- Tidak ada panggilan video/suara pada MVP; tombol serupa tidak ditampilkan.
- Logo, ilustrasi, dan foto dokter belum ditentukan (gunakan inisial sebagai pengganti).
