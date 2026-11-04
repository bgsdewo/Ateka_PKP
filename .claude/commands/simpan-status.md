---
description: Simpan status pekerjaan ke CLAUDE.md lalu commit & push (jalankan sebelum /clear)
---
Siapkan serah-terima pekerjaan agar aman dilanjutkan di sesi baru:

1. Jalankan `git status --short` dan `git log --oneline -8` untuk melihat apa yang berubah di sesi ini.
2. Perbarui HANYA bagian "## 11. Status terakhir" di `CLAUDE.md`:
   - ubah tanggal "Diperbarui";
   - tambahkan butir pada "Selesai" untuk pekerjaan sesi ini (singkat, satu baris per butir);
   - perbarui "Backlog / belum dikerjakan" (hapus yang sudah selesai, tambah temuan baru);
   - bila ada keputusan baru dari pemilik proyek, tambahkan ke bagian 7 "Keputusan & jebakan".
3. Pastikan tidak ada file sementara ikut ter-commit (shim SQLite di `AppServiceProvider.php`, `.env`, data uji). Batalkan shim dengan `git checkout -- app/Providers/AppServiceProvider.php` bila ada.
4. Commit semua perubahan yang memang bagian pekerjaan dengan pesan Bahasa Indonesia yang jelas, lalu `git push -u origin deploy`.
5. Tutup dengan 3–5 baris: apa yang sudah selesai, apa yang tersisa, dan perintah yang disarankan berikutnya (`/clear`).
