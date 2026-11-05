# CLAUDE.md — Inventori ATK (ATEKA) · Kementerian PKP RI

Dokumen ini dibaca otomatis di setiap sesi Claude Code. Isinya: gambaran proyek, aturan kerja,
keputusan yang sudah diambil, dan **status terakhir** agar pekerjaan bisa dilanjutkan dengan aman
setelah `/clear`, `/compact`, atau sesi baru. Perbarui bagian "Status terakhir" setiap selesai satu blok kerja.

## 1. Ringkasan
Aplikasi web internal pencatatan persediaan Alat Tulis Kantor (ATK): e-katalog, pengajuan barang,
verifikasi Kasubbag TU, pengadaan, distribusi, mutasi stok, kartu persediaan, dan laporan bulanan BMN.
Bahasa antarmuka: **Indonesia**. Zona waktu aplikasi: `Asia/Jakarta`.

- **Stack:** Laravel 12, PHP ≥ 8.2, Blade, template Hope UI (Bootstrap 5) di `public/hope-ui`, jQuery + DataTables.
- **Basis data:** MySQL di lokal/hosting (dump awal `database/ateka_jwp_2026.sql`). `.env.example` bawaan = sqlite.
- **Tanpa build front-end:** halaman memakai CSS/JS statis di `public/` (Vite ada tetapi tidak dipakai UI). Tidak perlu `npm run build`.
- **Branch kerja: `deploy`** (siap hosting). Push langsung ke `deploy`; jangan buat PR kecuali diminta.

## 2. Perintah
```bash
php artisan serve                      # jalankan lokal (http://127.0.0.1:8000)
php artisan migrate                    # migrasi
php artisan migrate:fresh --seed       # reset total + data awal (kategori/barang BMN + akun Kasubbag TU)
php artisan view:clear && php artisan cache:clear   # setelah mengubah Blade/konfigurasi
php artisan atk:pulihkan-mutasi-keluar [--simpan]   # pulihkan mutasi barang keluar yang hilang (simulasi dulu)
php artisan test                       # test fitur (tests/Feature, 20 file) — butuh MySQL, lihat §8
```
Akun seed: `utami@gmail.com` / `password123` (Kasubbag TU). Akun lain dibuat lewat Master Data → Manajemen Pengguna.

## 3. Role & hak akses
Middleware `role:` (`app/Http/Middleware/EnsureRole.php`) + helper di `app/Models/User.php`.

| Role (`users.role`) | Tugas |
|---|---|
| `kasubag_tu` (dan `admin`) | Verifikasi/setujui/tolak (boleh kurangi jumlah), kelola master data & pengguna, laporan. **Satu-satunya yang boleh menghapus barang.** |
| `pegawai_gudang` + `is_report_examiner` | Pemeriksa laporan: mengisi stok barang pengadaan sesuai jumlah yang disetujui. |
| `pegawai_gudang` + `is_distributor` | Distribusi: serah terima barang, foto bukti, cetak bukti. |
| `pic_distribusi` | Pemohon kolektif (subdit); unduh & unggah bukti serah terima bertanda tangan. |
| `pegawai` | Pemohon perorangan; selesai ketika PIC mengunggah bukti. |

Pegawai/PIC dialihkan ke dashboard bila membuka halaman gudang/laporan (itu normal).

## 4. Alur bisnis (jangan diubah tanpa diminta)
1. Pegawai/PIC membuat pengajuan (barang katalog; stok boleh 0) → tipe `individu` / `subdit`, bisa `is_procurement`.
2. Kasubbag TU setujui (jumlah boleh dikurangi → `approved_quantity`) atau tolak dengan alasan (notifikasi lonceng ke pemohon berisi catatan). Pemohon hanya diberi notifikasi: disetujui, ditolak (+catatan), dan barang diserahkan — tidak ada notifikasi "stok terisi".
3. Pengadaan: pemeriksa gudang mengisi stok → notifikasi ke petugas distribusi.
4. Distribusi menyerahkan barang → **otomatis tercatat barang keluar** (`StockTransaction` type `keluar`, `no_faktur` = kode tiket).
5. Selesai ketika `signed_proof` (bukti TTD) diunggah. Status tiket: `pending → approved → distributed` (atau `rejected`).

Tracker dashboard (`resources/views/dashboard/tracker.blade.php` + `tracker_steps.blade.php`):
pengadaan & stok-tersedia sama-sama 4 langkah (Pengajuan → Verifikasi TU → Penyerahan → Selesai/TTD); tracker tampil untuk pegawai & PIC. **Pegawai perorangan tanpa langkah bukti TTD** (3 langkah, selesai saat barang diserahkan); langkah Selesai/TTD hanya untuk PIC Subdit. Yang ditolak tidak masuk tracker, cukup notifikasi + catatan Kasubbag.
Menampilkan pengajuan **aktif lintas periode** (tidak dibatasi bulan dashboard).

## 5. Peta kode penting
- Rute: `routes/web.php`. Controller: `app/Http/Controllers/*` (Approval, Distribution, Product, Report, Request, Dashboard, Inventory, BarangMasuk/Keluar, UserManagement, Notification).
- Model: `RequestItem` (tabel `requests`, tiket), `RequestDetail`, `Product`, `Category`, `StockTransaction`, `UserNotification`, `User`.
- Layout: `resources/views/layouts/app.blade.php` (memuat `public/css/ateka-modern.css` lalu **`ateka-responsive.css` terakhir**), `partials/{sidebar,navbar,scripts,footer}.blade.php`.
- Dashboard `dashboard/index.blade.php` sangat besar (±2.300 baris): **jangan dibaca penuh**; pakai `grep -n` lalu `Read` dengan offset/limit.
- Laporan: `reports/*`; kode barang laporan = `Product::kode_barang_laporan` (kategori.kode_barang → bmn_code → code).

## 6. Konvensi UI/UX & responsif
- Semua penyesuaian responsif ada di **`public/css/ateka-responsive.css`** (bernomor per bagian). Edit di sana, bukan menimpa `ateka-modern.css`.
- Breakpoint: HP < 768, tablet < 992/1200 (sidebar off-canvas + backdrop), laptop 1200–1599 (root 14px), monitor ≥ 1920 (root 16px, ≥ 2400 18px, konten dibatasi 1760px).
- Judul halaman h1 memakai variabel `--ateka-h1-size` (h1 dengan `style` inline **!important menang atas CSS** → pakai variabel itu, jangan angka tetap).
- Tabel: `.table-responsive` otomatis dibungkus untuk DataTables (skrip di `partials/scripts.blade.php`); tabel lebar pakai `.ateka-fit-table`; antrean dashboard memakai `.ateka-queue` + pagination sisi-klien `data-page-size="3"`.
- Halaman auth (login, register, lupa-password) memakai `layouts/auth.blade.php` + `public/css/ateka-auth.css` (prefiks `au-`); wajib muat satu layar tanpa scroll di laptop 1280×600 ke atas.
- Notifikasi: dropdown `.ateka-notif-*`, toast `.modern-floating-toast`, semua diringkas di bagian 12 CSS responsif.
- Target: layak dipakai di HP; periksa dengan sapuan 8 ukuran (§8) sebelum menyatakan selesai.

## 7. Keputusan & jebakan yang sudah ditetapkan
- **`stock_transactions.recipient_type` adalah acuan utama** subdit/individu; kata "Subdit" di catatan hanya cadangan data lama (`StockTransaction::scopeForRecipient`, accessor `is_subdit`).
- **Filter tanggal "Dari" saja** di Barang Masuk/Keluar/Daftar Barang = filter **harian** (disengaja, dikunci test `InventoryPeriodFilterTest`). Di Laporan/Persetujuan "Dari" = sejak tanggal itu.
- `stock_transactions.user_id` = `nullOnDelete` (dulu cascade → menghapus akun menghapus mutasi). Hapus pengguna **ditolak** bila punya riwayat permohonan.
- Fitur "Sinkronkan BMN" & rute `/system/sync-bmn-database` **sudah dihapus** (celah keamanan). Data BMN datang dari seeder `Lembar2AtkCatalogSeeder`.
- Halaman Laporan hanya punya tombol **Export Excel** (Cetak Laporan & Kartu Persediaan dilepas dari header; Kartu Persediaan tetap di menu).
- Master Data → Kategori Barang boleh diakses `pegawai_gudang` (lihat/tambah/ubah); **hapus kategori tetap hanya Kasubbag TU**.
- Menu "Rekapitulasi Permintaan" sengaja dinonaktifkan (redirect ke dashboard).
- Bukti Serah Terima (`distribusi/bukti`) masih memakai kode rincian, bukan kode kategori (belum diminta diubah).

## 8. Cara menguji tanpa MySQL (lingkungan cloud/sandbox)
- Composer: unduhan zip GitHub diblokir; `composer install --no-scripts` jatuh ke git clone (lambat tapi jalan). CDN (datatables/cdnjs) diblokir → ekstensi tombol Excel tidak termuat saat uji.
- Pakai SQLite: `.env` dengan `DB_CONNECTION=sqlite`, `DB_DATABASE=/tmp/ateka.sqlite`. Beberapa migrasi khusus MySQL gagal di sqlite
  (`DATE_ADD`, `SHOW INDEX`, unique) → tandai selesai manual di tabel `migrations`, lalu buat unique index `categories(name)` sebelum seed.
- Halaman yang memakai `YEAR()/MONTH()` (Barang Masuk/Keluar, Daftar Barang) butuh **shim sementara** di `AppServiceProvider::boot()`
  (`sqliteCreateFunction`). **JANGAN di-commit** — batalkan dengan `git checkout -- app/Providers/AppServiceProvider.php`.
- Server uji: jalankan `php artisan serve --port=8000 --no-reload` sebagai proses latar (`run_in_background`); server mati bila induk perintahnya selesai.
- Login dibatasi `throttle:5,1` → jalankan `php artisan cache:clear` di antara login berulang pada skrip uji.
- Verifikasi tampilan/filter: Playwright (Chromium sudah terpasang) untuk screenshot & cek overflow; verifikasi filter lewat harness PHP
  (login sebagai user → `app()->handle(Request::create(...))` → bandingkan `view data` dengan hitungan DB).
- Pengecekan keamanan Bash bisa menolak perintah gabungan yang panjang (Python heredoc + banyak langkah): pecah jadi perintah pendek.

## 9. Aturan kerja & git
- Commit kecil dan jelas (Bahasa Indonesia), akhiri dengan trailer `Co-Authored-By` / `Claude-Session` sesuai pengaturan sesi. Push ke `deploy` (`git push -u origin deploy`).
- Hook "Stop" meminta semua perubahan di-commit & di-push; **jangan pernah** meng-commit `.env`, shim SQLite, atau data uji.
- File Blade/CSS di repo ini memiliki baris kosong ekstra di antara baris → `Edit` dengan string multi-baris sering gagal; gunakan `grep -n` lalu
  penggantian regex (Python) atau `Edit` pada satu baris unik.
- Jangan mengubah alur bisnis (§4) atau menghapus fitur tanpa diminta; laporkan temuan dan tanyakan dulu bila ragu.
- Setelah mengubah controller/model: `php -l <file>`; setelah mengubah Blade: `php artisan view:cache` (cek kompilasi) lalu `view:clear`.
- Jangan menyebut/menyimpan model AI di commit, kode, atau dokumen proyek.

## 10. Menjaga konteks Claude Code tetap stabil
Konteks penuh membuat Claude lupa detail dan mahal. Kebiasaan yang disarankan:

| Perintah | Kapan dipakai |
|---|---|
| `/context` | Lihat pemakaian konteks (Messages, tools, skills). Mulai waspada di ±60–70%. |
| `/compact` | Konteks mulai penuh tetapi masih satu topik. Contoh: `/compact fokus pada filter laporan & keputusan di CLAUDE.md`. |
| `/clear` | **Pindah topik/tugas baru.** Mengosongkan percakapan; CLAUDE.md tetap dibaca ulang. Commit & push dulu. |
| `/resume` | Melanjutkan sesi lama bila perlu riwayatnya. |
| `/simpan-status` | (perintah proyek) Memperbarui bagian "Status terakhir" di CLAUDE.md lalu commit & push. Jalankan **sebelum** `/clear`. |
| `/lanjutkan` | (perintah proyek) Di sesi baru: baca CLAUDE.md, cek `git status/log`, ringkas posisi pekerjaan, lalu lanjut. |

Praktik hemat konteks:
1. Satu sesi = satu topik. Selesai → `/simpan-status` → commit/push → `/clear`.
2. Jangan `Read` file besar penuh (dashboard/index, ateka-modern.css, persetujuan/index): `grep -n` dulu, baca potongan.
3. Hasil uji panjang: tulis ke file di scratchpad dan baca ringkasannya, jangan tempel seluruh keluaran.
4. Tangkapan layar dilihat seperlunya (gabungkan beberapa jadi satu gambar kontak).
5. Tulis keputusan penting ke CLAUDE.md (bukan hanya di chat) agar tidak hilang saat `/clear`.

Prompt awal untuk sesi baru (salin):
> Baca CLAUDE.md, lalu `git status` dan `git log --oneline -10`. Ringkas posisi pekerjaan dan sisa backlog, lalu tanyakan prioritas saya sebelum mengubah apa pun.

## 11. Status terakhir
_Diperbarui: 2026-11-05 · branch `deploy` · semua perubahan sudah di-push._

**Selesai (urut waktu):**
- UI responsif global (`ateka-responsive.css`): skala laptop, sidebar HP off-canvas, tabel, modal, notifikasi, monitor besar. Sapuan 45 halaman × 8 ukuran layar: 0 overflow.
- Dashboard: tabel antrean gudang tanpa scroll + pagination 3 data; tracker pengajuan disusun ulang sesuai alur nyata; hitungan distribusi per permohonan.
- Perbaikan data: FK mutasi `nullOnDelete`, penjaga hapus pengguna, perintah pemulihan mutasi keluar.
- Laporan: kode barang dari kategori; hanya Export Excel; klasifikasi penerima konsisten.
- Audit filter semua halaman (hasil cocok dengan hitungan DB); pencarian Persetujuan/Distribusi mencakup barang custom.
- Riwayat Pengajuan (pegawai/PIC): pencarian, filter, pagination, kartu status bisa diklik.
- Fitur Sinkronkan BMN dihapus.

**Backlog / belum dikerjakan (tunggu keputusan pemilik):**
- Pengajuan **ditolak** belum masuk daftar pelacakan dashboard (hanya pending/approved/distributed).
- Pelacakan menyembunyikan pengajuan yang sudah diserahkan tanpa TTD setelah 24 jam (aturan lama di `DashboardController`).
- Rute & halaman cetak `reports.print` masih ada di kode meski tombolnya dihapus.
- Bukti Serah Terima memakai kode rincian (bukan kode kategori).
- Belum ada fitur menonaktifkan akun (hanya hapus, yang diblokir bila punya riwayat).
- `php artisan test` belum dijalankan di MySQL setelah perubahan terakhir — jalankan di lokal.
- Perlu uji manual di HP sungguhan (iOS/Android): dropdown kustom, keyboard layar, unggah foto.
