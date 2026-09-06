<?php
$data = null;

if (isset($_GET['request_no'])) {
    $request_no = mysqli_real_escape_string($conn, $_GET['request_no']);

    $q = mysqli_query($conn, "
        SELECT
            r.*,
            s.name layanan
        FROM requests r
        LEFT JOIN services s ON s.id = r.service_id
        WHERE r.request_no = '$request_no'
        LIMIT 1
    ");

    $data = mysqli_fetch_assoc($q);
}
?>

<section class="container py-5">
    <div class="text-center mb-5">
        <h1 class="section-title">Tracking Permohonan</h1>
        <p class="text-muted">Lacak status permohonan masyarakat</p>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-4">
            <form method="GET">
                <input type="hidden" name="page" value="tracking">
                <div class="row">
                    <div class="col-md-10">
                        <input type="text" name="request_no" class="form-control form-control-lg"
                               placeholder="DNS-2026-000001" required
                               value="<?= htmlspecialchars($_GET['request_no'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-lg w-100">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($_GET['request_no'])): ?>
        <?php if ($data): ?>
            <?php
            // Tambahkan status 'new' ke array
            $status = [
                'new'      => 'warning',
                'pending'  => 'warning',
                'verified' => 'primary',
                'process'  => 'info',
                'completed'=> 'success',
                'rejected' => 'danger'
            ];

            // Untuk progress, tentukan persentase dan class
            $progressClass = '';
            $progressWidth = 20;

            // Ambil status, fallback ke 'new' jika tidak ada
            $currentStatus = $data['status'] ?? 'new';

            switch ($currentStatus) {
                case 'new':
                case 'pending':
                    $progressClass = 'bg-warning';
                    $progressWidth = 20;
                    break;
                case 'verified':
                    $progressClass = 'bg-primary';
                    $progressWidth = 40;
                    break;
                case 'process':
                    $progressClass = 'bg-info';
                    $progressWidth = 70;
                    break;
                case 'completed':
                    $progressClass = 'bg-success';
                    $progressWidth = 100;
                    break;
                case 'rejected':
                    $progressClass = 'bg-danger';
                    $progressWidth = 100;
                    break;
                default:
                    $progressClass = 'bg-secondary';
                    $progressWidth = 20;
            }

            // Ambil nilai dengan penanganan null
            $fullName   = $data['full_name'] ?? '-';
            $layanan    = $data['layanan'] ?? '-';
            $requestNo  = $data['request_no'] ?? '-';
            $notes      = $data['notes'] ?? '-';

            // Cek beberapa kemungkinan nama kolom untuk tanggal
            $createdAt = $data['created_at'] ?? $data['submitted_at'] ?? $data['request_date'] ?? null;
            $tanggal = ($createdAt) ? date('d F Y', strtotime($createdAt)) : '-';
            ?>

            <div class="card shadow mt-4 border-0">
                <div class="card-body p-4">
                    <h4>Data Permohonan</h4>
                    <hr>
                    <table class="table">
                        <tr>
                            <th width="220">Nomor Permohonan</th>
                            <td><?= htmlspecialchars($requestNo) ?></td>
                        </tr>
                        <tr>
                            <th>Nama Pemohon</th>
                            <td><?= htmlspecialchars($fullName) ?></td>
                        </tr>
                        <tr>
                            <th>Layanan</th>
                            <td><?= htmlspecialchars($layanan) ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td><?= htmlspecialchars($tanggal) ?></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-<?= $status[$currentStatus] ?? 'secondary' ?>">
                                    <?= ucfirst($currentStatus) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td><?= htmlspecialchars($notes) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow border-0 mt-4">
                <div class="card-body">
                    <h4>Progress</h4>
                    <hr>
                    <div class="progress" style="height:25px;">
                        <div class="progress-bar <?= $progressClass ?>" style="width: <?= $progressWidth ?>%;">
                            <?= ucfirst($currentStatus) ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-danger mt-4">Nomor permohonan tidak ditemukan</div>
        <?php endif; ?>
    <?php endif; ?>
</section>