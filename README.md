# Website Dinas Sosial

<p align="center">
  Website Dinas Sosial Daerah dengan layanan publik, berita, form permohonan, dan tracking.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-%23777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/Bootstrap-5-%237952B3?style=for-the-badge&logo=bootstrap&logoColor=white"/>
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge"/>
  <img src="https://img.shields.io/badge/PRs-Welcome-brightgreen?style=for-the-badge"/>
</p>



## Highlight

- **Front-end first** - repository berisi tampilan depan (public UI) yang siap jalan
- **Ringan & cepat** - tanpa framework berat, load cepat
- **Mudah di-deploy** - cukup PHP + database, tanpa setup rumit
- **Keamanan dasar terpasang** - prepared statements, sanitization, password hashing

## Fitur Utama

- Halaman publik: beranda, berita, layanan, profile
- Form permohonan online dengan upload berkas
- Tracking status permohonan
- Pencarian layanan & berita
- Responsive - mobile friendly

## Teknologi

<details>
<summary><b>Lihat detail teknologi</b></summary>

**Backend**
- PHP 8.x - server-side scripting dengan **PDO** + **prepared statements** (anti SQL Injection)
- Arsitektur modular (folder includes, pages, config)
- Session-based authentication dengan password hashing (**bcrypt**)
- Input validation & sanitization CSRF-aware

**Frontend**
- HTML5 semantik, CSS3 (Flexbox/Grid), JavaScript (ES6+)
- Bootstrap 5 responsive grid system
- AJAX untuk form & tracking real-time

**Database**
- MySQL 8 / MariaDB - skema ternormalisasi (3NF)
- Query engine dengan prepared statements

**Tooling & DevOps**
- Composer untuk dependency management
- Git & GitHub - version control & CI-ready
- Laragon/WAMP - local development environment
</details>

## Struktur Proyek

```
dinsosda
  includes/    # Komponen yang di-include (header, footer, dll)
  assets/      # CSS, JS, gambar
  *.php        # Halaman tampilan depan
```

## Menjalankan

Prasyarat: [Laragon](https://laragon.org) / [XAMPP](https://www.apachefriends.org)

1. Clone repository:

   ```bash
   git clone https://github.com/Celieln/dinsosda.git
   ```

2. Letakkan folder di `laragon/www/` atau `htdocs/`.
3. Buka `http://localhost/dinsosda`.

## Kontribusi

Kontribusi sangat diterima! Baca [CONTRIBUTING](CONTRIBUTING.md) dahulu, lalu buat Pull Request atau buka [Issues](https://github.com/Celieln/dinsosda/issues) untuk melaporkan bug / request fitur.

## Lisensi

Distributed under the [MIT](LICENSE) License. (c) [Celieln](https://github.com/Celieln)
