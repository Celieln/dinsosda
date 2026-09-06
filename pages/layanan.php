<?php

$keyword = trim($_GET['q'] ?? '');

$sql = "
SELECT
services.*,

(
SELECT COUNT(*)
FROM requirements
WHERE requirements.service_id=services.id
AND is_active=1
) total_syarat,


(
SELECT COUNT(*)
FROM sops
LEFT JOIN sop_steps
ON sops.id=sop_steps.sop_id
WHERE sops.service_id=services.id
AND sops.is_active=1
) total_sop


FROM services

WHERE is_active=1
";


if($keyword!='')
{

$sql.=" AND (

name LIKE '%$keyword%'

OR

summary LIKE '%$keyword%'

)

";

}



$sql.="

ORDER BY sort ASC

";


$services=mysqli_query($conn,$sql);

?>


<style>

.hero-service{

background:linear-gradient(135deg,#0A4DA2,#1565C0);

padding:70px 0;

color:white;

}



.service-card{

border:none;

border-radius:20px;

overflow:hidden;

transition:.3s;

box-shadow:0 5px 20px rgba(0,0,0,.08);

}



.service-card:hover{

transform:translateY(-6px);

}



.service-banner{


height:220px;

object-fit:cover;

width:100%;

}



.badge-online{


background:#198754;

}



.badge-offline{


background:#dc3545;

}



.badge-both{


background:#0d6efd;

}



</style>



<section class="hero-service">


<div class="container text-center">


<h1 class="fw-bold">

Layanan Publik


</h1>


<p>

Daftar layanan yang tersedia pada Dinas Sosial Daerah Provinsi Sulawesi Utara


</p>




<form method="GET">


<input

type="hidden"

name="page"

value="layanan"

>



<div class="row justify-content-center">


<div class="col-md-6">


<div class="input-group">


<input

type="text"

name="q"

class="form-control"

placeholder="Cari layanan..."

value="<?= $keyword;?>"

>


<button

class="btn btn-warning"

>


Cari


</button>



</div>



</div>



</div>



</form>



</div>


</section>





<section class="py-5">


<div class="container">



<div class="row">



<?php


while($s=mysqli_fetch_assoc($services)):


$image='uploads/layanan/default.jpg';


if($s['banner'])
{

$image='uploads/layanan/'.$s['banner'];

}



?>



<div class="col-lg-4 mb-4">



<div class="card service-card h-100">



<img

src="<?= $base_url.'/'.$image;?>"

class="service-banner"

>




<div class="card-body">



<div class="mb-3">



<?php


if($s['service_type']=='online')
{

echo'<span class="badge badge-online">ONLINE</span>';

}


elseif($s['service_type']=='offline')
{

echo'<span class="badge badge-offline">OFFLINE</span>';

}


else
{

echo'<span class="badge badge-both">ONLINE / OFFLINE</span>';

}


?>



</div>




<h4>


<?= $s['name'];?>


</h4>




<p class="text-muted">


<?= $s['summary'];?>


</p>




<hr>




<div class="row">



<div class="col-6 text-center">


<h4>

<?= $s['total_syarat'];?>


</h4>


<small>

Persyaratan


</small>


</div>



<div class="col-6 text-center">


<h4>


<?= $s['total_sop'];?>


</h4>


<small>


Tahapan SOP


</small>


</div>



</div>





<div class="mt-4 d-grid gap-2">



<a

href="<?= $base_url;?>/detail_layanan?slug=<?= $s['slug'];?>"

class="btn btn-primary"

>


Detail


</a>




<a

href="<?= $base_url;?>/form?service=<?= $s['slug'];?>"

class="btn btn-success"

>


Ajukan Permohonan


</a>



</div>



</div>



</div>



</div>



<?php endwhile;?>


</div>


</div>


</section>x