<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helper.php';
require_once __DIR__ . '/../config/upload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('request_upload_folder_by_label')) {
    function request_upload_folder_by_label(string $label): string
    {
        $label = mb_strtolower(trim($label));

        if (str_contains($label, 'ktp')) return 'ktp';
        if (str_contains($label, 'kartu keluarga') || preg_match('/\bkk\b/u', $label)) return 'kk';
        if (str_contains($label, 'foto')) return 'foto';
        if (str_contains($label, 'surat')) return 'surat';
        if (str_contains($label, 'nikah')) return 'surat';
        if (str_contains($label, 'domisili')) return 'surat';
        if (str_contains($label, 'akta')) return 'surat';
        if (str_contains($label, 'proposal')) return 'surat';
        if (str_contains($label, 'ad/art') || str_contains($label, 'ad art')) return 'surat';
        if (str_contains($label, 'sk ' ) || str_contains($label, ' sk') || str_contains($label, 's k ')) return 'surat';

        return 'lainnya';
    }
}

if (!function_exists('service_type_text')) {
    function service_type_text(string $type): string
    {
        return match ($type) {
            'online' => 'Online',
            'offline' => 'Offline',
            'both' => 'Online / Offline',
            default => strtoupper($type),
        };
    }
}

if (!function_exists('cleanup_uploaded_paths')) {
    function cleanup_uploaded_paths(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_string($path) && $path !== '' && file_exists($path)) {
                @unlink($path);
            }
        }
    }
}

$serviceSlug = trim($_GET['service'] ?? $_POST['service'] ?? '');
$service = null;

if ($serviceSlug !== '') {
    $stmt = mysqli_prepare(
        $conn,
        "
        SELECT
            s.*,
            d.name AS department_name
        FROM services s
        LEFT JOIN departments d ON d.id = s.department_id
        WHERE s.slug = ?
          AND s.is_active = 1
        LIMIT 1
        "
    );
    mysqli_stmt_bind_param($stmt, 's', $serviceSlug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $service = mysqli_fetch_assoc($result);
}

if (!$service) {
    ?>
    <section class="container py-5">
        <div class="alert alert-danger shadow-sm">
            Layanan tidak ditemukan. Silakan kembali ke halaman layanan.
        </div>
        <a href="<?= $base_url ?>/?page=layanan" class="btn btn-primary">Kembali ke Layanan</a>
    </section>
    <?php
    return;
}

$requirements = [];
$qRequirements = mysqli_query(
    $conn,
    "
    SELECT *
    FROM requirements
    WHERE service_id = " . (int)$service['id'] . "
      AND is_active = 1
    ORDER BY sort ASC, id ASC
    "
);

while ($row = mysqli_fetch_assoc($qRequirements)) {
    $requirements[] = $row;
}

$sop = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT *
        FROM sops
        WHERE service_id = " . (int)$service['id'] . "
          AND is_active = 1
        LIMIT 1
        "
    )
);

$sopSteps = [];
if ($sop) {
    $qSteps = mysqli_query(
        $conn,
        "
        SELECT *
        FROM sop_steps
        WHERE sop_id = " . (int)$sop['id'] . "
          AND is_active = 1
        ORDER BY step_no ASC, id ASC
        "
    );

    while ($row = mysqli_fetch_assoc($qSteps)) {
        $sopSteps[] = $row;
    }
}

$errors = [];
$success = false;
$successNo = '';
$form = [
    'full_name'   => trim($_POST['full_name'] ?? ''),
    'nik'         => trim($_POST['nik'] ?? ''),
    'birth_place' => trim($_POST['birth_place'] ?? ''),
    'birth_date'  => trim($_POST['birth_date'] ?? ''),
    'gender'      => trim($_POST['gender'] ?? ''),
    'email'       => trim($_POST['email'] ?? ''),
    'phone'       => trim($_POST['phone'] ?? ''),
    'address'     => trim($_POST['address'] ?? ''),
    'rt'          => trim($_POST['rt'] ?? ''),
    'rw'          => trim($_POST['rw'] ?? ''),
    'village'     => trim($_POST['village'] ?? ''),
    'district'    => trim($_POST['district'] ?? ''),
    'city'        => trim($_POST['city'] ?? ''),
    'province'    => trim($_POST['province'] ?? 'Sulawesi Utara'),
    'notes'       => trim($_POST['notes'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedSlug = trim($_POST['service'] ?? '');

    if ($postedSlug === '' || $postedSlug !== $serviceSlug) {
        $errors[] = 'Layanan tidak valid.';
    }

    if ($form['full_name'] === '' || mb_strlen($form['full_name']) < 3) {
        $errors[] = 'Nama lengkap wajib diisi.';
    }

    $form['nik'] = preg_replace('/\D+/', '', $form['nik']);
    if (!preg_match('/^\d{16}$/', $form['nik'])) {
        $errors[] = 'NIK harus 16 digit angka.';
    }

    if ($form['birth_place'] === '') {
        $errors[] = 'Tempat lahir wajib diisi.';
    }

    if ($form['birth_date'] === '') {
        $errors[] = 'Tanggal lahir wajib diisi.';
    } else {
        $tmpDate = DateTime::createFromFormat('Y-m-d', $form['birth_date']);
        if (!$tmpDate || $tmpDate->format('Y-m-d') !== $form['birth_date']) {
            $errors[] = 'Format tanggal lahir tidak valid.';
        }
    }

    if (!in_array($form['gender'], ['male', 'female'], true)) {
        $errors[] = 'Jenis kelamin wajib dipilih.';
    }

    if ($form['phone'] === '') {
        $errors[] = 'Nomor HP wajib diisi.';
    }

    if ($form['address'] === '') {
        $errors[] = 'Alamat wajib diisi.';
    }

    if ($form['village'] === '') {
        $errors[] = 'Kelurahan / Desa wajib diisi.';
    }

    if ($form['district'] === '') {
        $errors[] = 'Kecamatan wajib diisi.';
    }

    if ($form['city'] === '') {
        $errors[] = 'Kota / Kabupaten wajib diisi.';
    }

    if ($form['province'] === '') {
        $errors[] = 'Provinsi wajib diisi.';
    }

    if ($form['email'] !== '' && !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    $filePlan = [];
    foreach ($requirements as $req) {
        $field = 'req_' . $req['id'];
        $hasFile = isset($_FILES[$field]) && isset($_FILES[$field]['error']) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE;

        if ((int)$req['is_required'] === 1 && !$hasFile) {
            $errors[] = 'Berkas "' . $req['name'] . '" wajib diunggah.';
        }

        if ($hasFile) {
            $check = validateUpload($_FILES[$field]);
            if ($check !== true) {
                $errors[] = 'Berkas "' . $req['name'] . '": ' . $check;
            } else {
                $filePlan[] = $req;
            }
        }
    }

    if (empty($errors)) {
        $savedPaths = [];

        try {
            mysqli_begin_transaction($conn);

            $tempNo = 'TMP-' . date('YmdHis') . '-' . bin2hex(random_bytes(4));

            $stmt = mysqli_prepare(
                $conn,
                "
                INSERT INTO requests
                (
                    service_id,
                    request_no,
                    full_name,
                    nik,
                    birth_place,
                    birth_date,
                    gender,
                    email,
                    phone,
                    address,
                    rt,
                    rw,
                    village,
                    district,
                    city,
                    province,
                    notes,
                    status
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    'new'
                )
                "
            );

            $birthDate = $form['birth_date'];
            mysqli_stmt_bind_param(
                $stmt,
                'issssssssssssssss',
                $service['id'],
                $tempNo,
                $form['full_name'],
                $form['nik'],
                $form['birth_place'],
                $birthDate,
                $form['gender'],
                $form['email'],
                $form['phone'],
                $form['address'],
                $form['rt'],
                $form['rw'],
                $form['village'],
                $form['district'],
                $form['city'],
                $form['province'],
                $form['notes']
            );
            mysqli_stmt_execute($stmt);

            $requestId = (int) mysqli_insert_id($conn);
            if ($requestId <= 0) {
                throw new Exception('Gagal menyimpan permohonan.');
            }

            $finalNo = 'DNS-' . date('Y') . '-' . str_pad((string)$requestId, 6, '0', STR_PAD_LEFT);

            $stmtUpdate = mysqli_prepare(
                $conn,
                "
                UPDATE requests
                SET request_no = ?
                WHERE id = ?
                "
            );
            mysqli_stmt_bind_param($stmtUpdate, 'si', $finalNo, $requestId);
            mysqli_stmt_execute($stmtUpdate);

            foreach ($requirements as $req) {
                $field = 'req_' . $req['id'];

                if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                $folder = request_upload_folder_by_label($req['name']);
                $upload = uploadFile($_FILES[$field], $folder);

                if (empty($upload['status'])) {
                    throw new Exception($upload['message'] ?? 'Gagal upload berkas.');
                }

                $savedPaths[] = $upload['path'];

                $relativePath = 'uploads/permohonan/' . $folder . '/' . $upload['filename'];
                $sizeKb = isset($_FILES[$field]['size']) ? (int) ceil(((int)$_FILES[$field]['size']) / 1024) : null;
                $mime = $_FILES[$field]['type'] ?? null;

                $stmtFile = mysqli_prepare(
                    $conn,
                    "
                    INSERT INTO request_files
                    (
                        request_id,
                        requirement_id,
                        label,
                        file_name,
                        file_path,
                        mime,
                        size_kb
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )
                    "
                );

                $label = $req['name'];
                $fileName = $upload['filename'];
                mysqli_stmt_bind_param(
                    $stmtFile,
                    'iissssi',
                    $requestId,
                    $req['id'],
                    $label,
                    $fileName,
                    $relativePath,
                    $mime,
                    $sizeKb
                );
                mysqli_stmt_execute($stmtFile);
            }

            saveRequestStatus(
                $conn,
                $requestId,
                'new',
                'Permohonan baru diterima dari website.',
                null
            );

            mysqli_commit($conn);

            try {
                notifyAdmin(
                    $conn,
                    'Permohonan baru: ' . $finalNo,
                    'Ada permohonan baru untuk layanan "' . $service['name'] . '" atas nama ' . $form['full_name'] . '.',
                    $base_url . '/admin/permohonan.php?id=' . $requestId
                );
            } catch (Throwable $e) {
                // notifikasi jangan menggagalkan permohonan
            }

            header('Location: ' . $base_url . '/?page=tracking&request_no=' . urlencode($finalNo) . '&submitted=1');
            exit;
        } catch (Throwable $e) {
            mysqli_rollback($conn);
            cleanup_uploaded_paths($savedPaths);
            $errors[] = $e->getMessage();
        }
    }
}

$serviceBadge = match ($service['service_type']) {
    'online' => 'bg-success',
    'offline' => 'bg-danger',
    'both' => 'bg-primary',
    default => 'bg-secondary',
};

$defaultBanner = $base_url . '/uploads/layanan/default.jpg';
$bannerImg = !empty($service['banner'])
    ? $base_url . '/uploads/layanan/' . $service['banner']
    : $defaultBanner;
?>

<style>
    .form-hero{
        background: linear-gradient(135deg, #0A4DA2, #1565C0, #1E88E5);
        color:#fff;
        position:relative;
        overflow:hidden;
        padding:70px 0 88px;
    }
    .form-hero::before{
        content:'';
        position:absolute;
        inset:-120px auto auto -120px;
        width:280px;
        height:280px;
        border-radius:50%;
        background:rgba(255,255,255,.08);
        filter:blur(12px);
    }
    .form-hero::after{
        content:'';
        position:absolute;
        inset:auto -120px -120px auto;
        width:300px;
        height:300px;
        border-radius:50%;
        background:rgba(255,255,255,.08);
        filter:blur(12px);
    }
    .form-card,
    .side-card,
    .info-card{
        border:1px solid #E6EEF8;
        border-radius:24px;
        background:#fff;
        box-shadow:0 16px 38px rgba(15,23,42,.08);
    }
    .form-card .card-header,
    .side-card .card-header,
    .info-card .card-header{
        background:#fff;
        border-bottom:1px solid #EDF3FA;
        border-top-left-radius:24px;
        border-top-right-radius:24px;
        padding:1.1rem 1.25rem;
    }
    .section-title{
        font-weight:800;
        color:#17324D;
        letter-spacing:-.2px;
    }
    .muted{
        color:#64748B;
    }
    .badge-soft{
        background:#EAF3FF;
        color:#0A4DA2;
        border:1px solid #D7E8FF;
        font-weight:700;
    }
    .timeline{
        position:relative;
        padding-left:2.2rem;
    }
    .timeline::before{
        content:'';
        position:absolute;
        left:.85rem;
        top:.25rem;
        bottom:.25rem;
        width:4px;
        border-radius:999px;
        background:linear-gradient(180deg,#0A4DA2,#1E88E5);
    }
    .timeline-item{
        position:relative;
        margin-bottom:1rem;
        padding-bottom:.25rem;
    }
    .timeline-dot{
        position:absolute;
        left:-2.2rem;
        top:.15rem;
        width:1.65rem;
        height:1.65rem;
        border-radius:50%;
        background:#0A4DA2;
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:.85rem;
        box-shadow:0 8px 18px rgba(10,77,162,.18);
    }
    .timeline-item h6{
        margin-bottom:.2rem;
        font-weight:800;
        color:#17324D;
    }
    .timeline-item p{
        margin-bottom:0;
        color:#64748B;
        line-height:1.65;
        font-size:.95rem;
    }
    .req-item{
        border:1px solid #EEF3FA;
        border-radius:18px;
        padding:1rem;
        background:linear-gradient(145deg,#fff,#f8fbff);
        margin-bottom:.85rem;
    }
    .req-item:last-child{ margin-bottom:0; }
    .req-name{
        font-weight:800;
        color:#17324D;
        margin-bottom:.2rem;
    }
    .req-detail{
        color:#64748B;
        margin-bottom:.6rem;
        line-height:1.65;
    }
    .file-hint{
        font-size:.84rem;
        color:#64748B;
    }
    .form-label{
        font-weight:700;
        color:#17324D;
    }
    .form-control,
    .form-select{
        border-radius:14px;
        border-color:#DCE8F5;
        padding:.8rem .95rem;
        box-shadow:none !important;
    }
    .form-control:focus,
    .form-select:focus{
        border-color:#8cb8ea;
        box-shadow:0 0 0 .2rem rgba(13,110,253,.08) !important;
    }
    .submit-btn{
        border-radius:16px;
        padding:.9rem 1.15rem;
        font-weight:800;
        background:linear-gradient(135deg,#0A4DA2,#1E88E5);
        border:0;
        box-shadow:0 14px 26px rgba(33,150,243,.22);
    }
    .submit-btn:hover{
        background:linear-gradient(135deg,#08428a,#1976d2);
    }
    .sticky-side{
        position:sticky;
        top:95px;
    }
    .summary-img{
        width:100%;
        height:230px;
        object-fit:cover;
        border-radius:18px;
        border:1px solid #E6EEF8;
    }
    .summary-list{
        list-style:none;
        padding:0;
        margin:0;
        display:grid;
        gap:.55rem;
    }
    .summary-list li{
        display:flex;
        justify-content:space-between;
        gap:1rem;
        padding:.7rem .85rem;
        border:1px solid #EEF3FA;
        border-radius:14px;
        color:#17324D;
        background:#FBFDFF;
    }
    .summary-list span{
        color:#64748B;
        font-weight:600;
    }
    .alert-soft{
        background:#F0F7FF;
        border:1px solid #D7E8FF;
        color:#0A4DA2;
    }
    .small-note{
        color:#64748B;
        font-size:.88rem;
        line-height:1.65;
    }
    @media (max-width: 991.98px){
        .sticky-side{ position:static; top:auto; }
        .form-hero{ padding:56px 0 76px; }
    }
</style>

<section class="form-hero">
    <div class="container position-relative">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge badge-soft rounded-pill px-3 py-2 mb-3">
                    <?= e($service['code']); ?> · <?= e(service_type_text($service['service_type'])); ?>
                </span>
                <h1 class="display-5 fw-bold mb-3"><?= e($service['name']); ?></h1>
                <p class="lead mb-0" style="max-width:65ch; line-height:1.8; color:rgba(255,255,255,.92);">
                    <?= e($service['summary'] ?: 'Layanan permohonan masyarakat dengan upload berkas dinamis dan tracking status online.'); ?>
                </p>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="#form-pengajuan" class="btn btn-light fw-bold px-4 py-2 rounded-pill">Isi Form</a>
                    <a href="#sop-layanan" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill">Lihat SOP</a>
                    <a href="<?= $base_url ?>/?page=tracking" class="btn btn-warning fw-bold px-4 py-2 rounded-pill">Tracking</a>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="form-card p-3 p-lg-4 bg-white shadow-lg">
                    <img src="<?= e($bannerImg); ?>" alt="<?= e($service['name']); ?>" class="summary-img mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary"><?= e(service_type_text($service['service_type'])); ?></span>
                        <span class="badge bg-dark"><?= e($service['department_name'] ?: 'Bidang terkait'); ?></span>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark"><?= e($service['name']); ?></h5>
                    <p class="small-note mb-0"><?= e($service['summary'] ?: 'Detail layanan akan ditampilkan di halaman ini.'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger shadow-sm">
                        <div class="fw-bold mb-2">Periksa kembali isian Anda:</div>
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?= e($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['submitted']) && $_GET['submitted'] == '1' && !empty($_GET['request_no'])): ?>
                    <div class="alert alert-success shadow-sm">
                        Permohonan berhasil dikirim. Nomor Anda: <strong><?= e($_GET['request_no']); ?></strong>
                    </div>
                <?php endif; ?>

                <div class="card form-card mb-4" id="form-pengajuan">
                    <div class="card-header">
                        <h3 class="section-title mb-1">Form Permohonan</h3>
                        <div class="muted">Masyarakat tidak perlu membuat akun, cukup isi data dan upload berkas.</div>
                    </div>
                    <div class="card-body p-4 p-lg-4">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="service" value="<?= e($serviceSlug); ?>">

                            <div class="row g-3">
                                <div class="col-12">
                                    <h5 class="fw-bold text-dark mb-2">Data Pemohon</h5>
                                    <hr class="mt-0">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="full_name" class="form-control" value="<?= e($form['full_name']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">NIK</label>
                                    <input type="text" name="nik" class="form-control" value="<?= e($form['nik']); ?>" maxlength="16" inputmode="numeric" pattern="\d{16}" required>
                                    <div class="file-hint">16 digit angka sesuai KTP.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="birth_place" class="form-control" value="<?= e($form['birth_place']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="birth_date" class="form-control" value="<?= e($form['birth_date']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Pilih</option>
                                        <option value="male" <?= $form['gender'] === 'male' ? 'selected' : ''; ?>>Laki-laki</option>
                                        <option value="female" <?= $form['gender'] === 'female' ? 'selected' : ''; ?>>Perempuan</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Nomor HP</label>
                                    <input type="text" name="phone" class="form-control" value="<?= e($form['phone']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email (opsional)</label>
                                    <input type="email" name="email" class="form-control" value="<?= e($form['email']); ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea name="address" class="form-control" rows="3" required><?= e($form['address']); ?></textarea>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">RT</label>
                                    <input type="text" name="rt" class="form-control" value="<?= e($form['rt']); ?>">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">RW</label>
                                    <input type="text" name="rw" class="form-control" value="<?= e($form['rw']); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Kelurahan / Desa</label>
                                    <input type="text" name="village" class="form-control" value="<?= e($form['village']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Kecamatan</label>
                                    <input type="text" name="district" class="form-control" value="<?= e($form['district']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Kota / Kabupaten</label>
                                    <input type="text" name="city" class="form-control" value="<?= e($form['city']); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Provinsi</label>
                                    <input type="text" name="province" class="form-control" value="<?= e($form['province'] ?: 'Sulawesi Utara'); ?>" required>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Catatan / Keterangan Tambahan</label>
                                    <textarea name="notes" class="form-control" rows="4" placeholder="Tulis catatan jika ada..."><?= e($form['notes']); ?></textarea>
                                </div>

                                <div class="col-12 mt-4">
                                    <h5 class="fw-bold text-dark mb-2">Upload Berkas Dinamis</h5>
                                    <div class="alert alert-soft mb-3">
                                        Setiap persyaratan yang bertanda wajib harus diunggah. Format file yang diperbolehkan: JPG, JPEG, PNG, PDF. Maksimal 5 MB per file.
                                    </div>
                                    <hr class="mt-0">
                                </div>

                                <?php if (!empty($requirements)): ?>
                                    <?php foreach ($requirements as $req): ?>
                                        <?php
                                            $field = 'req_' . $req['id'];
                                            $requiredAttr = ((int)$req['is_required'] === 1) ? 'required' : '';
                                        ?>
                                        <div class="col-12">
                                            <div class="req-item">
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div>
                                                        <div class="req-name">
                                                            <?= e($req['name']); ?>
                                                            <?php if ((int)$req['is_required'] === 1): ?>
                                                                <span class="badge bg-danger ms-2">Wajib</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary ms-2">Opsional</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="req-detail"><?= e($req['detail'] ?: 'Silakan unggah berkas sesuai persyaratan.'); ?></div>
                                                    </div>
                                                </div>

                                                <input
                                                    type="file"
                                                    name="<?= e($field); ?>"
                                                    class="form-control"
                                                    accept=".jpg,.jpeg,.png,.pdf"
                                                    <?= $requiredAttr; ?>
                                                >
                                                <div class="file-hint mt-2">
                                                    Folder penyimpanan otomatis akan disesuaikan dengan jenis berkas.
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="alert alert-warning">Persyaratan layanan ini belum tersedia.</div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn btn-primary submit-btn w-100">
                                        Kirim Permohonan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card form-card" id="sop-layanan">
                    <div class="card-header">
                        <h3 class="section-title mb-1">Alur SOP</h3>
                        <div class="muted">Tahapan layanan dari awal sampai selesai.</div>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($sopSteps)): ?>
                            <div class="timeline">
                                <?php foreach ($sopSteps as $step): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-dot"><?= (int)$step['step_no']; ?></div>
                                        <h6><?= e($step['title']); ?></h6>
                                        <p>
                                            <strong>Pelaksana:</strong> <?= e($step['actor'] ?: '-'); ?><br>
                                            <strong>Estimasi:</strong> <?= !empty($step['duration_min']) ? (int)$step['duration_min'] . ' menit' : '-'; ?><br>
                                            <strong>Catatan:</strong> <?= e($step['note'] ?: '-'); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mb-0">SOP layanan belum tersedia.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-side">
                    <div class="card side-card mb-4">
                        <div class="card-header">
                            <h4 class="section-title mb-1">Ringkasan Layanan</h4>
                        </div>
                        <div class="card-body p-4">
                            <img src="<?= e($bannerImg); ?>" alt="<?= e($service['name']); ?>" class="summary-img mb-3">
                            <ul class="summary-list">
                                <li><strong>Kode</strong> <span><?= e($service['code']); ?></span></li>
                                <li><strong>Nama</strong> <span><?= e($service['name']); ?></span></li>
                                <li><strong>Jenis</strong> <span><?= e(service_type_text($service['service_type'])); ?></span></li>
                                <li><strong>Bidang</strong> <span><?= e($service['department_name'] ?: '-'); ?></span></li>
                                <li><strong>Persyaratan</strong> <span><?= count($requirements); ?> item</span></li>
                                <li><strong>Tahapan SOP</strong> <span><?= count($sopSteps); ?> tahap</span></li>
                            </ul>
                        </div>
                    </div>

                    <div class="card side-card mb-4">
                        <div class="card-header">
                            <h4 class="section-title mb-1">Persyaratan</h4>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($requirements)): ?>
                                <?php foreach ($requirements as $req): ?>
                                    <div class="req-item">
                                        <div class="req-name"><?= e($req['name']); ?></div>
                                        <div class="req-detail mb-0"><?= e($req['detail'] ?: 'Placeholder persyaratan.'); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-warning mb-0">Data persyaratan belum tersedia.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card side-card">
                        <div class="card-header">
                            <h4 class="section-title mb-1">Informasi</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="small-note">
                                Setelah form dikirim, nomor permohonan akan dibuat otomatis dan status awal masuk ke dashboard admin.
                            </div>
                            <div class="d-grid gap-2 mt-3">
                                <a href="<?= $base_url ?>/?page=tracking" class="btn btn-outline-primary fw-bold rounded-pill">
                                    Tracking Permohonan
                                </a>
                                <a href="<?= $base_url ?>/?page=layanan" class="btn btn-outline-secondary fw-bold rounded-pill">
                                    Kembali ke Layanan
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    const inputs = document.querySelectorAll('input[type="file"]');
    inputs.forEach((input) => {
        input.addEventListener('change', function () {
            const parent = this.closest('.req-item');
            let info = parent.querySelector('.selected-file');
            if (!info) {
                info = document.createElement('div');
                info.className = 'selected-file file-hint mt-2';
                parent.appendChild(info);
            }
            if (this.files && this.files[0]) {
                const file = this.files[0];
                info.textContent = `Dipilih: ${file.name} (${Math.round(file.size / 1024)} KB)`;
            } else {
                info.textContent = '';
            }
        });
    });
})();
</script>