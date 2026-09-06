<?php

$q = mysqli_query($conn,"
SELECT *
FROM pages
WHERE slug='profil'
LIMIT 1
");

$data = mysqli_fetch_assoc($q);

?>

<section class="container py-5">

<div class="text-center mb-5">

<h1 class="section-title">

Profil Dinas Sosial Daerah

</h1>

<p class="text-muted">

Provinsi Sulawesi Utara

</p>

</div>


<div class="card shadow border-0">

<div class="card-body p-5">

<?php if($data): ?>


<?= $data['content']; ?>


<?php else: ?>


<h3>Sejarah</h3>

<p>

Placeholder sejarah Dinas Sosial Daerah
Provinsi Sulawesi Utara.

</p>


<hr>


<h3>Visi</h3>

<p>

Placeholder visi.

</p>


<hr>


<h3>Misi</h3>

<p>

Placeholder misi.

</p>


<hr>


<h3>Tugas Pokok dan Fungsi</h3>

<p>

Placeholder tupoksi.

</p>


<?php endif; ?>


</div>

</div>

</section>