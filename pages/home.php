<?php

$banner = mysqli_query($conn, "
    SELECT *
    FROM banners
    WHERE is_active = 1
    ORDER BY sort ASC, id DESC
");

$departments = mysqli_query($conn, "
    SELECT *
    FROM departments
    WHERE is_active = 1
    ORDER BY sort ASC, id DESC
    LIMIT 6
");

$services = mysqli_query($conn, "
    SELECT *
    FROM services
    WHERE is_active = 1
    ORDER BY sort ASC, id DESC
    LIMIT 6
");

$news = mysqli_query($conn, "
    SELECT *
    FROM news
    WHERE status = 'published'
    ORDER BY COALESCE(published_at, created_at) DESC, id DESC
    LIMIT 3
");

$contact = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT *
    FROM contacts
    ORDER BY id ASC
    LIMIT 1
")) ?: [];

$socials = mysqli_query($conn, "
    SELECT *
    FROM socials
    WHERE is_active = 1
    ORDER BY sort ASC, id ASC
");

$cntDepartments = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM departments WHERE is_active = 1"))['total'];
$cntServices    = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM services WHERE is_active = 1"))['total'];
$cntNews        = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM news WHERE status = 'published'"))['total'];
$cntRequests    = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM requests"))['total'];

if (!function_exists('e')) {
    function e($v)
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('short_text')) {
    function short_text($text, $len = 120)
    {
        $text = trim(strip_tags((string) $text));
        if (mb_strlen($text) <= $len) return $text;
        return mb_substr($text, 0, $len) . '...';
    }
}

$siteName     = $setting['site_name'] ?? 'Dinas Sosial Daerah Provinsi Sulawesi Utara';
$siteTagline  = $setting['site_tagline'] ?? 'Melayani masyarakat dengan cepat, transparan, dan akurat';
$siteDesc     = $setting['site_desc'] ?? 'Website resmi Dinas Sosial Daerah Provinsi Sulawesi Utara';

$logoMain   = !empty($setting['logo_main']) ? $base_url . '/uploads/profile/' . $setting['logo_main'] : $base_url . '/assets/favicon.ico';
$govPhoto   = !empty($setting['photo_gov']) ? $base_url . '/uploads/profile/' . $setting['photo_gov'] : $base_url . '/assets/favicon.ico';
$wagovPhoto = !empty($setting['photo_wagov']) ? $base_url . '/uploads/profile/' . $setting['photo_wagov'] : $base_url . '/assets/favicon.ico';
$govName    = $setting['gov_name'] ?? 'Gubernur Provinsi Sulawesi Utara';
$wagovName  = $setting['wagov_name'] ?? 'Wakil Gubernur Provinsi Sulawesi Utara';

$defaultBanner = $base_url . '/uploads/banner/default.jpg';
$defaultNews   = $base_url . '/uploads/berita/default.jpg';
$defaultDept   = $base_url . '/uploads/bidang/default.jpg';
$defaultSrv    = $base_url . '/uploads/layanan/default.jpg';

$contactAddress = $contact['address'] ?? 'Jl. Pingkan Matindas No.125, Ranomuut, Paal Dua, Kota Manado, Sulawesi Utara';
$contactPhone   = $contact['phone'] ?? '';
$contactEmail   = $contact['email'] ?? '';
$contactHours   = $contact['open_hours'] ?? 'Senin - Jumat, 08.00 - 16.00';
$contactMaps    = $contact['maps_embed'] ?? '';

$googleMapsQuery = urlencode($contactAddress);

$bannerCount = mysqli_num_rows($banner);
$headPhoto = $base_url . '/uploads/profile/kepala_dinas.jpg';
$headName = 'Kepala Dinas Sosial Daerah Provinsi Sulawesi Utara';
?>

<style>
    :root{
        --hp-primary:#0A4DA2;
        --hp-primary-2:#1565C0;
        --hp-accent:#1E88E5;
        --hp-soft:#F4F8FD;
        --hp-line:#E4EDF8;
        --hp-text:#17324D;
        --hp-muted:#64748B;
        --hp-shadow:0 16px 38px rgba(15,23,42,.08);
        --hp-shadow-hover:0 24px 46px rgba(15,23,42,.12);
        --hp-radius:24px;
    }

    @keyframes hpFadeUp {
        from { opacity:0; transform:translateY(18px); }
        to { opacity:1; transform:translateY(0); }
    }

    @keyframes hpFloat {
        0%,100% { transform:translateY(0); }
        50% { transform:translateY(-8px); }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation:none !important;
            transition:none !important;
            scroll-behavior:auto !important;
        }
    }

    .hp-hero{
        position:relative;
        overflow:hidden;
        color:#fff;
        background:
            radial-gradient(circle at 18% 18%, rgba(255,255,255,.16), transparent 18%),
            radial-gradient(circle at 82% 14%, rgba(255,255,255,.12), transparent 18%),
            linear-gradient(135deg, var(--hp-primary) 0%, var(--hp-primary-2) 48%, var(--hp-accent) 100%);
        padding:72px 0 104px;
    }

    .hp-hero::before{
        content:'';
        position:absolute;
        inset:auto auto -140px -80px;
        width:280px;
        height:280px;
        border-radius:50%;
        background:rgba(255,255,255,.08);
        filter:blur(10px);
        pointer-events:none;
        animation:hpFloat 8s ease-in-out infinite;
    }

    .hp-hero::after{
        content:'';
        position:absolute;
        inset:-120px -120px auto auto;
        width:300px;
        height:300px;
        border-radius:50%;
        background:rgba(255,255,255,.08);
        filter:blur(12px);
        pointer-events:none;
        animation:hpFloat 10s ease-in-out infinite;
    }

    .hp-hero .container{
        position:relative;
        z-index:2;
    }

    .hp-pill{
        display:inline-flex;
        align-items:center;
        gap:.5rem;
        padding:.55rem .92rem;
        border-radius:999px;
        background:rgba(255,255,255,.12);
        border:1px solid rgba(255,255,255,.16);
        backdrop-filter:blur(10px);
        font-weight:600;
        font-size:.9rem;
    }

    .hp-title{
        font-size:clamp(2.3rem, 4vw, 4rem);
        line-height:1.05;
        font-weight:800;
        letter-spacing:-.6px;
        margin:1rem 0 .9rem;
        animation:hpFadeUp .55s ease both;
    }

    .hp-lead{
        font-size:1.02rem;
        line-height:1.8;
        max-width:60ch;
        color:rgba(255,255,255,.92);
        margin-bottom:0;
        animation:hpFadeUp .55s ease .08s both;
    }

    .hp-actions{
        display:flex;
        flex-wrap:wrap;
        gap:.7rem;
        margin-top:1.5rem;
        animation:hpFadeUp .55s ease .12s both;
    }

    .hp-btn{
        border-radius:14px;
        padding:.88rem 1.05rem;
        font-weight:700;
        border:0;
        box-shadow:0 12px 28px rgba(0,0,0,.10);
    }

    .hp-btn-light{
        background:#fff;
        color:var(--hp-primary);
    }

    .hp-btn-light:hover{
        background:#f7fbff;
        color:var(--hp-primary);
    }

    .hp-btn-outline{
        background:transparent;
        color:#fff;
        border:1px solid rgba(255,255,255,.35);
    }

    .hp-btn-outline:hover{
        background:rgba(255,255,255,.10);
        color:#fff;
    }

    .hp-chip-row{
        display:flex;
        flex-wrap:wrap;
        gap:.65rem;
        margin-top:1.35rem;
        animation:hpFadeUp .55s ease .16s both;
    }

    .hp-chip{
        display:inline-flex;
        align-items:center;
        gap:.45rem;
        padding:.56rem .82rem;
        border-radius:999px;
        background:rgba(255,255,255,.10);
        border:1px solid rgba(255,255,255,.15);
        color:#fff;
        font-weight:600;
        font-size:.88rem;
        backdrop-filter:blur(8px);
    }

    .hp-hero-card{
        background:rgba(255,255,255,.10);
        border:1px solid rgba(255,255,255,.16);
        backdrop-filter:blur(14px);
        border-radius:28px;
        box-shadow:0 22px 48px rgba(0,0,0,.18);
        overflow:hidden;
        animation:hpFadeUp .55s ease .10s both;
    }

    .hp-carousel{
        overflow:hidden;
        border-radius:24px;
    }

    .hp-carousel .carousel-item img{
        height:410px;
        object-fit:cover;
        width:100%;
    }

    .hp-carousel .carousel-caption{
        left:1rem;
        right:1rem;
        bottom:1rem;
        background:rgba(15,23,42,.52);
        backdrop-filter:blur(8px);
        border:1px solid rgba(255,255,255,.12);
        border-radius:18px;
        padding:1rem 1rem .95rem;
        text-align:left;
    }

    .hp-carousel .carousel-caption h5{
        font-weight:800;
        margin-bottom:.35rem;
    }

    .hp-carousel .carousel-caption p{
        margin-bottom:.75rem;
        color:rgba(255,255,255,.9);
        line-height:1.65;
    }

    /* ---- Baris foto pejabat (baru) ---- */
    .hp-official-row {
        display: flex;
        gap: 0.9rem;
        margin-top: 1rem;
    }
    .hp-official-row .hp-official {
        flex: 1;
        min-width: 0;
    }
    @media (max-width: 575.98px) {
        .hp-official-row {
            flex-wrap: wrap;
        }
        .hp-official-row .hp-official {
            flex: 1 1 100%;
        }
    }

    .hp-official{
        display:flex;
        gap:.8rem;
        align-items:center;
        padding:.85rem;
        border-radius:18px;
        background:rgba(255,255,255,.08);
        border:1px solid rgba(255,255,255,.14);
    }

    .hp-official img{
        width:62px;
        height:78px;
        object-fit:cover;
        border-radius:14px;
        border:3px solid rgba(255,255,255,.18);
        flex-shrink:0;
    }

    .hp-official small{
        display:block;
        color:rgba(255,255,255,.72);
        font-size:.78rem;
        margin-bottom:.12rem;
    }

    .hp-official strong{
        display:block;
        font-size:.92rem;
        line-height:1.32;
    }

    .hp-wave{
        position:absolute;
        left:0;
        bottom:0;
        width:100%;
        line-height:0;
        z-index:2;
    }

    .hp-section{
        padding:78px 0;
    }

    .hp-section-soft{
        background:linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .hp-section-title{
        display:inline-block;
        font-size:clamp(1.65rem, 2.4vw, 2.3rem);
        font-weight:800;
        color:var(--hp-text);
        letter-spacing:-.3px;
        margin-bottom:.7rem;
    }

    .hp-section-title::after{
        content:'';
        display:block;
        width:72px;
        height:4px;
        border-radius:999px;
        margin-top:.7rem;
        background:linear-gradient(135deg, var(--hp-primary), var(--hp-accent));
    }

    .hp-section-subtitle{
        color:var(--hp-muted);
        line-height:1.8;
        max-width:68ch;
    }

    .hp-panel,
    .hp-card,
    .hp-stat-card,
    .hp-contact-card{
        background:#fff;
        border:1px solid var(--hp-line);
        box-shadow:var(--hp-shadow);
        border-radius:24px;
        overflow:hidden;
        transition:.28s ease;
    }

    .hp-card:hover,
    .hp-stat-card:hover,
    .hp-contact-card:hover{
        transform:translateY(-6px);
        box-shadow:var(--hp-shadow-hover);
    }

    .hp-menu-card{
        background:linear-gradient(145deg, #ffffff, #f3f8ff);
        border:1px solid #e4edf8;
        border-radius:22px;
        padding:1.25rem;
        height:100%;
        box-shadow:0 12px 28px rgba(15,23,42,.06);
        transition:.28s ease;
    }

    .hp-menu-card:hover{
        transform:translateY(-6px);
        box-shadow:0 22px 36px rgba(15,23,42,.10);
    }

    .hp-menu-icon{
        width:72px;
        height:72px;
        display:flex;
        align-items:center;
        justify-content:center;
        margin:0 auto 1rem;
        border-radius:22px;
        color:#fff;
        background:linear-gradient(135deg, var(--hp-primary), var(--hp-accent));
        box-shadow:0 14px 28px rgba(33,150,243,.22);
        font-size:1.65rem;
    }

    .hp-menu-card h5{
        font-weight:800;
        color:var(--hp-text);
        margin-bottom:.35rem;
    }

    .hp-menu-card p{
        color:var(--hp-muted);
        margin-bottom:0;
        line-height:1.65;
        font-size:.95rem;
    }

    .hp-about{
        display:grid;
        grid-template-columns:1.1fr .9fr;
        gap:1rem;
    }

    .hp-about-box{
        padding:1.25rem;
        border-radius:24px;
        background:linear-gradient(145deg, #ffffff, #f6faff);
        border:1px solid #e6eef8;
        box-shadow:0 12px 28px rgba(15,23,42,.06);
    }

    .hp-feature{
        display:flex;
        gap:.95rem;
        padding:1rem 0;
        border-bottom:1px solid #edf3fa;
    }

    .hp-feature:last-child{
        border-bottom:0;
        padding-bottom:0;
    }

    .hp-feature i{
        width:48px;
        height:48px;
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:16px;
        background:#eaf3ff;
        color:var(--hp-primary);
        flex-shrink:0;
        font-size:1.1rem;
    }

    .hp-feature h6{
        margin-bottom:.2rem;
        font-weight:800;
        color:var(--hp-text);
    }

    .hp-feature p{
        margin-bottom:0;
        color:var(--hp-muted);
        line-height:1.7;
        font-size:.95rem;
    }

    /* hp-head-card sudah tidak digunakan, kita hapus atau biarkan saja */
    .hp-head-card{
        display:flex;
        gap:1rem;
        align-items:center;
        padding:1rem;
        border-radius:22px;
        background:linear-gradient(145deg, #fff, #f6faff);
        border:1px solid #e6eef8;
        box-shadow:0 12px 28px rgba(15,23,42,.06);
    }

    .hp-head-card img{
        width:120px;
        height:150px;
        object-fit:cover;
        border-radius:18px;
        border:1px solid #e5edf7;
        flex-shrink:0;
    }

    .hp-head-info small{
        display:block;
        color:var(--hp-muted);
        margin-bottom:.12rem;
        font-weight:700;
    }

    .hp-head-info strong{
        display:block;
        color:var(--hp-text);
        line-height:1.35;
        font-size:1rem;
    }

    .hp-head-info p{
        color:var(--hp-muted);
        line-height:1.7;
        font-size:.95rem;
    }

    .hp-stat-card{
        position:relative;
        min-height:150px;
        padding:1.2rem 1.25rem 1.2rem 1.35rem;
    }

    .hp-stat-card::before{
        content:'';
        position:absolute;
        left:0;
        top:0;
        bottom:0;
        width:6px;
        background:linear-gradient(180deg, var(--hp-primary), var(--hp-accent));
    }

    .hp-stat-label{
        color:var(--hp-muted);
        font-size:.78rem;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.8px;
        margin-bottom:.2rem;
    }

    .hp-stat-value{
        font-size:2.15rem;
        line-height:1;
        font-weight:900;
        letter-spacing:-.5px;
        color:var(--hp-primary);
        margin:.2rem 0 .45rem;
    }

    .hp-stat-desc{
        color:var(--hp-muted);
        line-height:1.65;
        margin-bottom:0;
    }

    .hp-badge{
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        padding:.46rem .72rem;
        border-radius:999px;
        background:#eaf3ff;
        border:1px solid #d7e8ff;
        color:var(--hp-primary);
        font-weight:800;
        font-size:.82rem;
    }

    .hp-dept-card,
    .hp-service-card,
    .hp-news-card{
        border:1px solid #e6eef8;
        border-radius:24px;
        background:#fff;
        overflow:hidden;
        box-shadow:var(--hp-shadow);
        transition:.28s ease;
        height:100%;
    }

    .hp-dept-card:hover,
    .hp-service-card:hover,
    .hp-news-card:hover{
        transform:translateY(-6px);
        box-shadow:var(--hp-shadow-hover);
    }

    .hp-media{
        position:relative;
        overflow:hidden;
    }

    .hp-media img{
        width:100%;
        height:220px;
        object-fit:cover;
        background:#eef4ff;
        transition:.35s ease;
    }

    .hp-dept-card:hover .hp-media img,
    .hp-service-card:hover .hp-media img,
    .hp-news-card:hover .hp-media img{
        transform:scale(1.04);
    }

    .hp-body{
        padding:1.25rem;
    }

    .hp-card-title{
        font-size:1.1rem;
        font-weight:800;
        color:var(--hp-text);
        line-height:1.4;
        margin-bottom:.5rem;
    }

    .hp-card-text{
        color:var(--hp-muted);
        line-height:1.75;
        margin-bottom:1rem;
    }

    .hp-actions-row{
        display:flex;
        flex-wrap:wrap;
        gap:.65rem;
    }

    .hp-outline-btn,
    .hp-primary-btn{
        border-radius:14px;
        padding:.75rem 1rem;
        font-weight:800;
    }

    .hp-outline-btn{
        border:1px solid #d7e8ff;
        color:var(--hp-primary);
        background:#fff;
    }

    .hp-outline-btn:hover{
        background:#f7fbff;
        color:var(--hp-primary);
    }

    .hp-primary-btn{
        background:linear-gradient(135deg, var(--hp-primary), var(--hp-accent));
        border:0;
        box-shadow:0 14px 26px rgba(33,150,243,.20);
        color:#fff;
    }

    .hp-primary-btn:hover{
        background:linear-gradient(135deg, var(--hp-primary-2), var(--hp-accent));
        color:#fff;
    }

    .hp-service-meta{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:.7rem;
        margin-bottom:.8rem;
    }

    .hp-service-code,
    .hp-service-type{
        display:inline-flex;
        align-items:center;
        padding:.42rem .68rem;
        border-radius:999px;
        font-size:.82rem;
        font-weight:800;
        white-space:nowrap;
    }

    .hp-service-code{
        color:var(--hp-primary);
        background:#f0f7ff;
        border:1px solid #ddebf9;
    }

    .hp-service-type{
        color:#11845a;
        background:#effaf6;
        border:1px solid #d8f0e5;
        text-transform:capitalize;
    }

    .hp-news-date{
        position:absolute;
        top:1rem;
        left:1rem;
        z-index:2;
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        padding:.44rem .68rem;
        border-radius:999px;
        background:rgba(255,255,255,.92);
        color:var(--hp-primary);
        box-shadow:0 10px 18px rgba(15,23,42,.10);
        font-size:.82rem;
        font-weight:800;
    }

    .hp-news-card .hp-media::after{
        content:'';
        position:absolute;
        inset:auto 0 0 0;
        height:84px;
        background:linear-gradient(180deg, transparent, rgba(2,6,23,.72));
        pointer-events:none;
    }

    .hp-tracking{
        position:relative;
        overflow:hidden;
        border-radius:28px;
        background:
            radial-gradient(circle at top right, rgba(255,255,255,.16), transparent 24%),
            linear-gradient(135deg, var(--hp-primary), var(--hp-primary-2) 48%, var(--hp-accent) 100%);
        color:#fff;
        box-shadow:0 20px 48px rgba(10,77,162,.16);
    }

    .hp-tracking::before{
        content:'';
        position:absolute;
        right:-70px;
        bottom:-90px;
        width:230px;
        height:230px;
        border-radius:50%;
        background:rgba(255,255,255,.10);
    }

    .hp-tracking .form-control{
        border:0;
        border-radius:16px;
        padding:1rem 1.05rem;
        box-shadow:none;
    }

    .hp-checklist{
        list-style:none;
        padding:0;
        margin:1rem 0 0;
        display:grid;
        gap:.55rem;
    }

    .hp-checklist li{
        display:flex;
        gap:.6rem;
        align-items:flex-start;
        line-height:1.6;
        color:rgba(255,255,255,.93);
    }

    .hp-checklist i{
        margin-top:.18rem;
        flex-shrink:0;
    }

    .hp-contact-card{
        padding:1.3rem;
        height:100%;
    }

    .hp-contact-item{
        display:flex;
        gap:.9rem;
        padding:1rem 0;
        border-bottom:1px solid #edf3fa;
    }

    .hp-contact-item:last-child{
        border-bottom:0;
        padding-bottom:0;
    }

    .hp-contact-icon{
        width:48px;
        height:48px;
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:16px;
        background:#eaf3ff;
        color:var(--hp-primary);
        flex-shrink:0;
        font-size:1.08rem;
    }

    .hp-contact-item h6{
        margin-bottom:.2rem;
        font-weight:800;
        color:var(--hp-text);
    }

    .hp-contact-item p{
        margin-bottom:0;
        color:var(--hp-muted);
        line-height:1.7;
    }

    .hp-social{
        width:44px;
        height:44px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border-radius:50%;
        color:#fff;
        text-decoration:none;
        margin-right:.55rem;
        background:linear-gradient(135deg, var(--hp-primary), var(--hp-accent));
        box-shadow:0 12px 24px rgba(33,150,243,.20);
        transition:.22s ease;
    }

    .hp-social:hover{
        transform:translateY(-3px);
        color:#fff;
    }

    .hp-map{
        overflow:hidden;
        border:1px solid #e6eef8;
        border-radius:24px;
        box-shadow:var(--hp-shadow);
        min-height:100%;
    }

    .hp-map iframe{
        width:100%;
        height:100%;
        min-height:420px;
        border:0;
        display:block;
    }

    .hp-empty{
        padding:1rem;
        border-radius:16px;
        border:1px dashed #dbe6f2;
        background:#fbfdff;
        color:var(--hp-muted);
    }

    @media (max-width: 991.98px){
        .hp-hero{
            padding:56px 0 92px;
        }

        .hp-carousel .carousel-item img{
            height:340px;
        }

        .hp-official-grid,
        .hp-about{
            grid-template-columns:1fr;
        }

        .hp-map iframe{
            min-height:340px;
        }
    }

    @media (max-width: 575.98px){
        .hp-actions .btn,
        .hp-actions-row .btn{
            width:100%;
        }

        .hp-carousel .carousel-item img{
            height:295px;
        }

        .hp-service-meta{
            flex-direction:column;
            align-items:flex-start;
        }

        .hp-stat-value{
            font-size:1.9rem;
        }
    }
</style>

<section class="hp-hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="hp-pill mb-3">
                    <i class="bi bi-shield-check"></i>
                    Website Resmi Pemerintah Provinsi Sulawesi Utara
                </div>

                <h1 class="hp-title"><?= e($siteName); ?></h1>

                <p class="hp-lead"><?= e($siteTagline); ?></p>
                <p class="hp-lead mt-3"><?= e($siteDesc); ?></p>

                <div class="hp-actions">
                    <a href="<?= $base_url ?>/?page=layanan" class="btn hp-btn hp-btn-light">
                        <i class="bi bi-file-earmark-text me-1"></i> Layanan Online
                    </a>
                    <a href="<?= $base_url ?>/?page=berita" class="btn hp-btn hp-btn-outline">
                        <i class="bi bi-newspaper me-1"></i> Berita Terbaru
                    </a>
                    <a href="<?= $base_url ?>/?page=tracking" class="btn hp-btn hp-btn-outline">
                        <i class="bi bi-search me-1"></i> Cek Status
                    </a>
                </div>

                <div class="hp-chip-row">
                    <span class="hp-chip"><i class="bi bi-person-check"></i> Tanpa akun masyarakat</span>
                    <span class="hp-chip"><i class="bi bi-file-earmark-arrow-up"></i> Upload berkas online</span>
                    <span class="hp-chip"><i class="bi bi-clock-history"></i> Tracking status permohonan</span>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="hp-hero-card p-3 p-lg-4">
                    <?php if ($bannerCount > 1): ?>
                        <div id="homeBanner" class="carousel slide carousel-fade hp-carousel" data-bs-ride="carousel" data-bs-pause="hover">
                            <div class="carousel-inner">
                                <?php $i = 0; while ($b = mysqli_fetch_assoc($banner)): ?>
                                    <?php
                                        $img = !empty($b['image']) ? $base_url . '/uploads/banner/' . $b['image'] : $defaultBanner;
                                    ?>
                                    <div class="carousel-item <?= $i === 0 ? 'active' : ''; ?>">
                                        <img src="<?= e($img); ?>" class="d-block w-100" alt="<?= e($b['title']); ?>" loading="<?= $i === 0 ? 'eager' : 'lazy'; ?>">
                                        <div class="carousel-caption">
                                            <h5><?= e($b['title']); ?></h5>
                                            <?php if (!empty($b['caption'])): ?>
                                                <p class="small"><?= e($b['caption']); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($b['link'])): ?>
                                                <a href="<?= e($b['link']); ?>" class="btn btn-sm btn-warning fw-bold">Selengkapnya</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php $i++; ?>
                                <?php endwhile; ?>
                            </div>

                            <button class="carousel-control-prev" type="button" data-bs-target="#homeBanner" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#homeBanner" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    <?php else: ?>
                        <?php
                            $singleBanner = null;
                            if ($bannerCount === 1) {
                                $singleBanner = mysqli_fetch_assoc($banner);
                            }
                            $singleImg = !empty($singleBanner['image']) ? $base_url . '/uploads/banner/' . $singleBanner['image'] : $defaultBanner;
                        ?>
                        <div class="hp-carousel">
                            <div class="position-relative">
                                <img src="<?= e($singleImg); ?>" class="d-block w-100" alt="<?= e($singleBanner['title'] ?? 'Banner'); ?>" loading="eager" style="height:410px;object-fit:cover;border-radius:24px;">
                                <div class="carousel-caption" style="left:1rem;right:1rem;bottom:1rem;">
                                    <h5><?= e($singleBanner['title'] ?? 'Selamat Datang'); ?></h5>
                                    <p class="small"><?= e($singleBanner['caption'] ?? 'Website resmi Dinas Sosial Daerah Provinsi Sulawesi Utara'); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Baris foto pejabat: Gubernur, Wagub, Kepala Dinas -->
                    <div class="hp-official-row">
                        <div class="hp-official">
                            <img src="<?= e($govPhoto); ?>" alt="<?= e($govName); ?>" loading="lazy">
                            <div>
                                <small>Pimpinan Daerah</small>
                                <strong><?= e($govName); ?></strong>
                            </div>
                        </div>
                        <div class="hp-official">
                            <img src="<?= e($wagovPhoto); ?>" alt="<?= e($wagovName); ?>" loading="lazy">
                            <div>
                                <small>Pimpinan Daerah</small>
                                <strong><?= e($wagovName); ?></strong>
                            </div>
                        </div>
                        <div class="hp-official">
                            <img src="<?= e($headPhoto); ?>" alt="<?= e($headName); ?>" loading="lazy">
                            <div>
                                <small>Kepala Dinas</small>
                                <strong><?= e($headName); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <svg class="hp-wave" viewBox="0 0 1440 180" preserveAspectRatio="none" aria-hidden="true">
        <path fill="#F4F8FD" d="M0,80L80,90C160,100,320,120,480,112C640,104,800,56,960,48C1120,40,1280,72,1360,88L1440,104L1440,180L0,180Z"></path>
    </svg>
</section>

<section class="hp-section hp-section-soft">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="hp-section-title">Menu Cepat</h2>
            <p class="hp-section-subtitle mx-auto">
                Akses bagian utama layanan dan informasi dengan tampilan yang bersih, ringan, dan cepat.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-6 col-lg-3">
                <a href="<?= $base_url ?>/?page=profile" class="text-decoration-none">
                    <div class="hp-menu-card text-center">
                        <div class="hp-menu-icon"><i class="bi bi-building"></i></div>
                        <h5>Profil</h5>
                        <p>Sejarah, visi misi, dan tugas dinas</p>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <a href="<?= $base_url ?>/?page=bidang" class="text-decoration-none">
                    <div class="hp-menu-card text-center">
                        <div class="hp-menu-icon"><i class="bi bi-diagram-3"></i></div>
                        <h5>Bidang</h5>
                        <p>Struktur bidang yang ada di dinas</p>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <a href="<?= $base_url ?>/?page=layanan" class="text-decoration-none">
                    <div class="hp-menu-card text-center">
                        <div class="hp-menu-icon"><i class="bi bi-file-earmark-text"></i></div>
                        <h5>Layanan</h5>
                        <p>Pengajuan online tanpa akun masyarakat</p>
                    </div>
                </a>
            </div>

            <div class="col-6 col-lg-3">
                <a href="<?= $base_url ?>/?page=tracking" class="text-decoration-none">
                    <div class="hp-menu-card text-center">
                        <div class="hp-menu-icon"><i class="bi bi-search"></i></div>
                        <h5>Tracking</h5>
                        <p>Cek status permohonan secara langsung</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="hp-section">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-6">
                <div class="hp-about-box h-100">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="<?= e($logoMain); ?>" alt="Logo" style="width:72px;height:72px;object-fit:contain;">
                        <div>
                            <div class="hp-badge mb-2">
                                <i class="bi bi-stars"></i> Sekilas Dinas
                            </div>
                            <h2 class="hp-section-title mb-0">Melayani dengan hati untuk kesejahteraan masyarakat</h2>
                        </div>
                    </div>

                    <p class="hp-section-subtitle mb-4">
                        <?= e($siteDesc); ?>
                    </p>

                    <div class="hp-feature">
                        <i class="bi bi-shield-check"></i>
                        <div>
                            <h6>Transparan dan terukur</h6>
                            <p>Alur layanan bisa dipantau, berkas dapat diverifikasi, dan status permohonan terlihat jelas.</p>
                        </div>
                    </div>

                    <div class="hp-feature">
                        <i class="bi bi-file-earmark-arrow-up"></i>
                        <div>
                            <h6>Layanan digital tanpa akun</h6>
                            <p>Masyarakat cukup mengisi form, melampirkan berkas, lalu menerima nomor permohonan otomatis.</p>
                        </div>
                    </div>

                    <div class="hp-feature">
                        <i class="bi bi-bell"></i>
                        <div>
                            <h6>Terhubung ke dashboard admin</h6>
                            <p>Setiap permohonan yang masuk langsung tampil sebagai notifikasi di dashboard admin untuk diverifikasi.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <!-- Bagian hp-head-card yang menampilkan foto Kepala Dinas sudah dihapus -->
                <!-- Hanya statistik yang tersisa -->
                <div class="row g-3">
                    <div class="col-6">
                        <div class="hp-stat-card">
                            <div class="hp-stat-label">Bidang Aktif</div>
                            <div class="hp-stat-value"><?= number_format($cntDepartments); ?></div>
                            <p class="hp-stat-desc">Struktur organisasi yang ditampilkan sebagai placeholder.</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="hp-stat-card">
                            <div class="hp-stat-label">Layanan Aktif</div>
                            <div class="hp-stat-value"><?= number_format($cntServices); ?></div>
                            <p class="hp-stat-desc">Layanan online yang siap dipakai masyarakat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="hp-section hp-section-soft">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="hp-section-title">Statistik Singkat</h2>
            <p class="hp-section-subtitle mx-auto">
                Ringkasan data publik yang disusun sederhana agar mudah dibaca dan ringan dimuat.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-xl-3">
                <div class="hp-stat-card">
                    <div class="hp-stat-label">Permohonan Masuk</div>
                    <div class="hp-stat-value"><?= number_format($cntRequests); ?></div>
                    <p class="hp-stat-desc">Total pengajuan masyarakat yang tercatat di sistem.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="hp-stat-card">
                    <div class="hp-stat-label">Layanan Publik</div>
                    <div class="hp-stat-value"><?= number_format($cntServices); ?></div>
                    <p class="hp-stat-desc">Jumlah layanan online yang sedang aktif.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="hp-stat-card">
                    <div class="hp-stat-label">Bidang Dinas</div>
                    <div class="hp-stat-value"><?= number_format($cntDepartments); ?></div>
                    <p class="hp-stat-desc">Bidang yang ditampilkan sebagai placeholder struktur organisasi.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="hp-stat-card">
                    <div class="hp-stat-label">Berita Publik</div>
                    <div class="hp-stat-value"><?= number_format($cntNews); ?></div>
                    <p class="hp-stat-desc">Berita dan pengumuman resmi yang telah dipublikasikan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="bidang" class="hp-section">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="hp-section-title">Bidang</h2>
            <p class="hp-section-subtitle mx-auto">
                Kartu bidang ini bisa diubah kapan saja dari dashboard admin tanpa mengubah layout halaman.
            </p>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($departments) > 0): ?>
                <?php while ($d = mysqli_fetch_assoc($departments)): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="hp-dept-card">
                            <div class="hp-media">
                                <img
                                    src="<?= e(!empty($d['image']) ? $base_url . '/uploads/bidang/' . $d['image'] : $defaultDept); ?>"
                                    alt="<?= e($d['name']); ?>"
                                    loading="lazy"
                                >
                            </div>
                            <div class="hp-body">
                                <span class="hp-badge mb-3">
                                    <i class="bi bi-diagram-3"></i> Bidang
                                </span>
                                <div class="hp-card-title"><?= e($d['name']); ?></div>
                                <div class="hp-card-text">
                                    <?= e(short_text($d['summary'] ?? '', 135)); ?>
                                </div>
                                <div class="hp-actions-row">
                                    <a href="<?= $base_url ?>/?page=bidang&slug=<?= e($d['slug']); ?>" class="btn hp-outline-btn">Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="hp-empty">Data bidang belum tersedia.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="layanan" class="hp-section hp-section-soft">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="hp-section-title">Layanan Online</h2>
            <p class="hp-section-subtitle mx-auto">
                Form layanan dibuat ringan, berkas bisa diunggah langsung, dan status dapat dipantau tanpa login.
            </p>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($services) > 0): ?>
                <?php while ($s = mysqli_fetch_assoc($services)): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="hp-service-card">
                            <div class="hp-media">
                                <img
                                    src="<?= e(!empty($s['banner']) ? $base_url . '/uploads/layanan/' . $s['banner'] : $defaultSrv); ?>"
                                    alt="<?= e($s['name']); ?>"
                                    loading="lazy"
                                >
                            </div>
                            <div class="hp-body">
                                <div class="hp-service-meta">
                                    <span class="hp-service-code"><?= e($s['code']); ?></span>
                                    <span class="hp-service-type"><?= e($s['service_type']); ?></span>
                                </div>
                                <div class="hp-card-title"><?= e($s['name']); ?></div>
                                <div class="hp-card-text">
                                    <?= e(short_text($s['summary'] ?? '', 130)); ?>
                                </div>
                                <div class="hp-actions-row">
                                    <a href="<?= $base_url ?>/?page=detail_layanan&slug=<?= e($s['slug']); ?>" class="btn hp-outline-btn">Detail</a>
                                    <a href="<?= $base_url ?>/?page=form&service=<?= e($s['slug']); ?>" class="btn hp-primary-btn">Ajukan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="hp-empty">Data layanan belum tersedia.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="berita" class="hp-section">
    <div class="container">
        <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <h2 class="hp-section-title mb-0">Berita Terbaru</h2>
                <p class="hp-section-subtitle mb-0">
                    Informasi kegiatan dan pengumuman resmi dari Dinas Sosial Daerah Provinsi Sulawesi Utara.
                </p>
            </div>
            <a href="<?= $base_url ?>/?page=berita" class="btn hp-outline-btn">Lihat Semua</a>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($news) > 0): ?>
                <?php while ($n = mysqli_fetch_assoc($news)): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="hp-news-card">
                            <div class="hp-media position-relative">
                                <span class="hp-news-date">
                                    <i class="bi bi-calendar3"></i>
                                    <?= !empty($n['published_at']) ? date('d M Y', strtotime($n['published_at'])) : date('d M Y', strtotime($n['created_at'])); ?>
                                </span>
                                <img
                                    src="<?= e(!empty($n['image']) ? $base_url . '/uploads/berita/' . $n['image'] : $defaultNews); ?>"
                                    alt="<?= e($n['title']); ?>"
                                    loading="lazy"
                                >
                            </div>
                            <div class="hp-body">
                                <div class="hp-card-title"><?= e($n['title']); ?></div>
                                <div class="hp-card-text">
                                    <?= e(short_text($n['summary'] ?? '', 145)); ?>
                                </div>
                                <div class="hp-actions-row">
                                    <a href="<?= $base_url ?>/?page=detail_berita&slug=<?= e($n['slug']); ?>" class="btn hp-primary-btn">Baca Selengkapnya</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="hp-empty">Belum ada berita yang dipublikasikan.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="tracking" class="hp-section hp-section-soft">
    <div class="container">
        <div class="hp-tracking p-4 p-lg-5">
            <div class="row align-items-center g-4 position-relative">
                <div class="col-lg-6">
                    <div class="hp-badge mb-3">
                        <i class="bi bi-search-heart"></i> Tracking Permohonan
                    </div>
                    <h2 class="hp-section-title text-white mb-3">Cek status pengajuan Anda</h2>
                    <p class="mb-0" style="color:rgba(255,255,255,.92); line-height:1.8;">
                        Masukkan nomor permohonan untuk melihat status terakhir, progres verifikasi, dan catatan tindak lanjut dari admin.
                    </p>

                    <ul class="hp-checklist">
                        <li><i class="bi bi-check-circle-fill"></i> Nomor otomatis setelah submit form</li>
                        <li><i class="bi bi-check-circle-fill"></i> Tanpa login untuk masyarakat</li>
                        <li><i class="bi bi-check-circle-fill"></i> Terhubung ke dashboard admin</li>
                    </ul>
                </div>

                <div class="col-lg-6">
                    <form action="<?= $base_url ?>/?page=tracking" method="get" class="row g-2">
                        <input type="hidden" name="page" value="tracking">
                        <div class="col-12 col-md-8">
                            <input
                                type="text"
                                name="request_no"
                                class="form-control form-control-lg"
                                placeholder="Contoh: DNS-2026-000001"
                                value="<?= e($_GET['request_no'] ?? ''); ?>"
                                required
                            >
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="submit" class="btn btn-warning btn-lg w-100">
                                Cek Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="hp-section">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="hp-section-title">Kontak Kami</h2>
            <p class="hp-section-subtitle mx-auto">
                Informasi alamat kantor, jam operasional, media sosial, dan peta lokasi Dinas Sosial Daerah Provinsi Sulawesi Utara.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="hp-contact-card">
                    <div class="hp-contact-item">
                        <div class="hp-contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <h6>Alamat</h6>
                            <p><?= e($contactAddress); ?></p>
                        </div>
                    </div>

                    <div class="hp-contact-item">
                        <div class="hp-contact-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <h6>Telepon</h6>
                            <p><?= $contactPhone ? e($contactPhone) : '-'; ?></p>
                        </div>
                    </div>

                    <div class="hp-contact-item">
                        <div class="hp-contact-icon"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <h6>Email</h6>
                            <p><?= $contactEmail ? e($contactEmail) : '-'; ?></p>
                        </div>
                    </div>

                    <div class="hp-contact-item">
                        <div class="hp-contact-icon"><i class="bi bi-clock-fill"></i></div>
                        <div>
                            <h6>Jam Operasional</h6>
                            <p><?= e($contactHours); ?></p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <?php if (mysqli_num_rows($socials) > 0): ?>
                            <?php while ($sc = mysqli_fetch_assoc($socials)): ?>
                                <a href="<?= e($sc['url']); ?>" target="_blank" rel="noopener" class="hp-social" title="<?= e($sc['platform']); ?>">
                                    <i class="bi bi-<?= e($sc['icon'] ?: 'link-45deg'); ?>"></i>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="hp-empty">Media sosial belum tersedia.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4">
                        <a
                            href="https://www.google.com/maps/search/?api=1&query=<?= $googleMapsQuery; ?>"
                            target="_blank"
                            rel="noopener"
                            class="btn hp-primary-btn"
                        >
                            <i class="bi bi-map me-1"></i> Buka di Google Maps
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="hp-map">
                    <?php if (!empty($contactMaps)): ?>
                        <?= $contactMaps; ?>
                    <?php else: ?>
                        <iframe
                            src="https://maps.google.com/maps?q=manado&t=&z=13&ie=UTF8&iwloc=&output=embed"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>