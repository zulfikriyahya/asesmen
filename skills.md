# Skills & Project Convention Reference

Dokumen ini digunakan sebagai konteks bagi AI assistant saat membantu pengembangan project **MADRASAH UNIVERSE — ASESMEN MADRASAH - v2.0**.

---

## Profile

| Field | Value |
|---|---|
| **Author Name** | Yahya Zulfikri |
| **Level** | Senior Developer |
| **Project Name** | MADRASAH UNIVERSE |
| **App Name** | ASESMEN MADRASAH |
| **App Version** | v2.0 |
| **Bahasa** | Indonesia (penjelasan), English (kode & nama teknis) |

**Asumsi:** Developer sudah memahami konsep dasar seperti instalasi, struktur folder standar, dan cara kerja framework — tidak perlu dijelaskan ulang kecuali diminta secara eksplisit.

---

## Aturan Pengembangan

### Kode
- Tidak menggunakan komentar berlebihan
- Tidak menggunakan emoticon
- Clean code tanpa mengubah struktur project
- Tidak mengubah logic yang sudah ada

### UI / Refactor
- Refactor tampilan menjadi responsive, modern, elegan, rapi, dan simetris
- Gunakan dark mode dengan efek glassmorphism
- Gunakan font **Lexend**
- Gunakan tema warna **cyan, biru, atau hijau**
- Perhatikan jarak (spacing & padding) secara konsisten

### Output
- Konsistenkan tema dan penulisan kode di seluruh file
- Berikan full kode hasil refactor per file
- Sertakan lokasi file yang diubah

---

## Stack & Teknologi

### Backend
| | |
|---|---|
| **Framework** | CodeIgniter 3 |
| **Language** | PHP 8.0 |
| **Database** | MySQL (via `ext-mysqli`) |
| **Server** | Nginx |
| **Auth** | Ion Auth |

### PHP Dependencies (`composer.json`)
| Package | Versi |
|---|---|
| `phpoffice/phpspreadsheet` | 1.* |
| `phpoffice/phpword` | ^0.18.1 |
| `alhimik1986/php-excel-templator` | ^1.0 |
| `dompdf/dompdf` | ^2.0 |

### PHP Extensions
`ext-dom` `ext-mysqli` `ext-fileinfo` `ext-zip` `ext-json` `ext-gd` `ext-iconv` `ext-calendar` `ext-libxml`

### Dev Dependencies
| Package | Versi |
|---|---|
| `phpunit/phpunit` | ^9.0 |
| `mikey179/vfsstream` | 1.1.* |
| `nikic/php-parser` | ^5.0 |

### Frontend — UI
| | |
|---|---|
| **UI Framework** | Bootstrap 4.4.1 |
| **Admin Template** | AdminLTE |
| **Rich Text** | Summernote (bs4) |
| **Select** | Select2 |
| **File Input** | Dropify, bs-custom-file-input |
| **Alert / Toast** | SweetAlert2, Toastr 2.1.4 |
| **Switch** | Bootstrap Switch, iOS Switch |
| **Progress** | Pace Progress |
| **Scrollbar** | OverlayScrollbars |

### Frontend — JavaScript
| | |
|---|---|
| **Core** | jQuery 3.4.1, jQuery UI 1.12.1 |
| **Table** | DataTables 1.10.20 (+ extensions: AutoFill, Buttons, ColReorder, FixedColumns, FixedHeader, KeyTable, Responsive, RowGroup, RowReorder, Scroller, Select) |
| **Chart** | Chart.js |
| **Math** | KaTeX, math.js |
| **Date / Time** | Moment.js 2.24.0, Timeago, jQuery Datetimepicker, Tempusdominus Bootstrap 4 |
| **Zip** | JSZip 3.2.0, JSZip (app) |
| **Export** | PDFMake, FileSaver, tableToExcel, jQuery Word Export, html-docx |
| **Calendar** | Pignose Calendar |
| **Map** | jqvmap |
| **Mask** | Inputmask |
| **Dual List** | Bootstrap4 Duallistbox, DualSelectList |
| **Context Menu** | jQuery contextmenu |
| **Countdown** | jQuery Countdown |
| **Pagination** | jQuery twbsPagination |
| **Spreadsheet** | jExcel |
| **Misc** | Sparklines, jQuery Knob, jQuery Easing, jQuery Backstretch, jQuery Marquee |

### Font & Icon
| | |
|---|---|
| **Font** | Google Font API — **Lexend** (utama), Poppins (AdminLTE), Amiri, Calibri |
| **Icon** | Font Awesome, Bootstrap Icons, Ionicons, Icomoon |

### Tooling & Monitoring
| | |
|---|---|
| **Analytics** | Google Analytics UA |
| **Dokumentasi** | BookStack |

---

## Struktur Project

```
/
├── application/
│   ├── config/          # Konfigurasi CI & library
│   ├── controllers/     # Semua controller
│   ├── helpers/         # Helper custom (my_helper, db_size)
│   ├── hooks/           # Hook custom (Db_log)
│   ├── language/        # Bahasa (english, indonesian)
│   ├── libraries/       # Library custom (Ion_auth, Datatables, dll)
│   ├── models/          # Semua model
│   └── views/
│       ├── _templates/  # Layout global (dashboard, auth, topnav)
│       ├── auth/        # Halaman auth
│       ├── cbt/         # Modul CBT (ujian online)
│       ├── kelas/       # Modul kelas (absen, jadwal, nilai, materi)
│       ├── master/      # Data master (siswa, guru, mapel, kelas, dll)
│       ├── members/
│       │   ├── guru/    # View & template khusus guru
│       │   └── siswa/   # View & template khusus siswa
│       ├── rapor/       # Cetak & edit rapor
│       ├── setting/     # Halaman pengaturan
│       └── users/       # Manajemen user (admin, guru, siswa)
│
├── assets/
│   ├── adminlte/        # Template AdminLTE (CSS, JS, font)
│   ├── app/
│   │   ├── css/         # CSS custom project
│   │   ├── img/         # Gambar & icon project
│   │   └── js/          # JS custom per modul
│   ├── fonts/           # Font custom (Icomoon)
│   ├── img/             # Asset gambar global
│   └── plugins/         # Semua plugin third-party
│
├── uploads/
│   ├── bank_soal/       # File soal CBT
│   ├── file_siswa/      # Dokumen siswa
│   ├── foto_siswa/      # Foto profil siswa
│   ├── import/format/   # Template import Excel/Word
│   ├── materi/          # File materi pelajaran
│   ├── profiles/        # Foto profil user
│   ├── settings/        # File pengaturan (logo, dll)
│   └── tugas/           # File tugas siswa
│
├── system/              # Core CodeIgniter (jangan diubah)
├── vendor/              # Composer dependencies
├── installer/           # Script instalasi awal
├── backups/             # Backup database
└── composer.json
```

### Konvensi Path Asset
- CSS custom project: `assets/app/css/`
- JS custom per modul: `assets/app/js/{modul}/`
- Plugin third-party: `assets/plugins/{nama-plugin}/`
- Gambar global: `assets/img/`
- Template view global: `application/views/_templates/`

---

## Konvensi Penamaan

### PHP
- Controller & Model — `PascalCase` (contoh: `RaporModel`)
- Method — `camelCase` (contoh: `getNilaiRapor()`)
- Variabel — `snake_case` (contoh: `$id_siswa`)
- Konstanta — `UPPER_SNAKE_CASE`

### Database
- Nama tabel — `snake_case`, plural (contoh: `rapor_nilai_harian`)
- Primary key — `id_nama_tabel` (contoh: `id_siswa`)
- Foreign key — mengikuti nama primary key tabel yang dirujuk

### CSS / View
- Class custom — `kebab-case` (contoh: `.card-nilai`)
- ID — `camelCase` (contoh: `#tableRapor`)

---

## Struktur Response AI

- Jawab langsung tanpa basa-basi pembuka
- Penjelasan menggunakan Bahasa Indonesia, kode tetap dalam Bahasa Inggris
- Jika ada lebih dari satu solusi, tampilkan perbandingan singkat sebelum memilih
- Untuk refactor UI, selalu sertakan lokasi file di atas blok kode
- Jika ada potensi breaking change, sebutkan secara eksplisit sebelum kode

---

## Batasan & Larangan

- Tidak mengganti framework atau library utama tanpa konfirmasi
- Tidak mengubah struktur tabel database
- Tidak mengubah nama method atau variabel yang sudah ada
- Tidak menambahkan dependency baru tanpa konfirmasi
- Tidak menggunakan `inline style` kecuali tidak ada alternatif
- Tidak menggunakan `!important` dalam CSS kecuali terpaksa
