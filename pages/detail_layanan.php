<?php

$slug = $_GET['slug'] ?? '';

$stmt = mysqli_prepare($conn,"
SELECT

s.*,

d.name bidang

FROM services s

LEFT JOIN departments d

ON d.id=s.department_id

WHERE s.slug=?

LIMIT 1

");

mysqli_stmt_bind_param($stmt,"s",$slug);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

$service=mysqli_fetch_assoc($result);


if(!$service)
{

echo'

<div class="container py-5">

<div class="alert alert-danger">

Layanan tidak ditemukan

</div>

</div>

';

return;

}



$requirements=mysqli_query(

$conn,

"

SELECT *

FROM requirements

WHERE service_id=".$service['id']."

AND is_active=1

ORDER BY sort ASC

"

);



$sop=mysqli_fetch_assoc(

mysqli_query(

$conn,

"

SELECT *

FROM sops

WHERE service_id=".$service['id']."

LIMIT 1

"

)

);



$steps=[];

if($sop)
{

$steps=mysqli_query(

$conn,

"

SELECT *

FROM sop_steps

WHERE sop_id=".$sop['id']."

ORDER BY step_no ASC

"

);

}


?>


<style>


.hero-service{


padding:70px 0;


background:


linear-gradient(

135deg,

#0A4DA2,

#1565C0

);


color:white;


}




.info-box{


background:white;


padding:25px;


border-radius:20px;


box-shadow:


0 5px 20px rgba(0,0,0,.08);


}




.step{


position:relative;


padding-left:60px;


padding-bottom:30px;


}




.step:before{


content:'';


position:absolute;


left:17px;


top:0;


width:4px;


height:100%;


background:#0A4DA2;


}




.circle{


position:absolute;


left:0;


top:0;


width:38px;


height:38px;


border-radius:50%;


background:#0A4DA2;


color:white;


display:flex;


align-items:center;


justify-content:center;


font-weight:bold;


}




.requirement{


padding:15px;


border-bottom:


1px solid #eee;


}




</style>





<section class="hero-service">


<div class="container">


<h1>


<?= $service['name'];?>


</h1>



<p>


<?= $service['summary'];?>


</p>


</div>


</section>






<section class="py-5">


<div class="container">


<div class="row">



<div class="col-lg-8">





<div class="info-box mb-4">


<h3>


Informasi Layanan


</h3>


<hr>



<table class="table">


<tr>

<th width="200">

Kode


</th>


<td>

<?= $service['code'];?>


</td>


</tr>



<tr>

<th>

Jenis


</th>


<td>

<?= strtoupper($service['service_type']);?>


</td>


</tr>




<tr>

<th>


Bidang


</th>


<td>


<?= $service['bidang'] ?: '-';?>


</td>


</tr>



</table>



</div>






<div class="info-box mb-4">


<h3>


Persyaratan


</h3>


<hr>





<?php


if(mysqli_num_rows($requirements)>0):


while($r=mysqli_fetch_assoc($requirements)):


?>



<div class="requirement">


<h5>


<?= $r['name'];?>


</h5>



<p>


<?= $r['detail'];?>


</p>



</div>




<?php endwhile; ?>


<?php endif; ?>




</div>






<div class="info-box">


<h3>


Alur SOP


</h3>


<hr>




<?php


if($steps):



while($s=mysqli_fetch_assoc($steps)):


?>



<div class="step">


<div class="circle">


<?= $s['step_no'];?>


</div>



<h5>


<?= $s['title'];?>


</h5>



<p>


Pelaksana :


<?= $s['actor'];?>


</p>



<p>


Estimasi :


<?= $s['duration_min'];?> menit


</p>




<small>


<?= $s['note'];?>


</small>



</div>




<?php endwhile; ?>


<?php endif; ?>





</div>




</div>







<div class="col-lg-4">



<div class="info-box">



<?php


$image='uploads/layanan/default.jpg';


if($service['banner'])
{

$image='uploads/layanan/'.$service['banner'];

}



?>



<img

src="<?= $base_url.'/'.$image;?>"

class="img-fluid rounded mb-4"

>



<h4>


<?= $service['name'];?>


</h4>



<p>


<?= $service['summary'];?>


</p>



<hr>




<a

href="<?= $base_url;?>/form?service=<?= $service['slug'];?>"

class="btn btn-success w-100"

>


Ajukan Permohonan


</a>




<a

href="<?= $base_url;?>/tracking"

class="btn btn-outline-primary w-100 mt-2"

>


Tracking Permohonan


</a>




</div>



</div>





</div>


</div>


</section>