<?php

$contact = mysqli_fetch_assoc(

mysqli_query(

$conn,

"

SELECT *

FROM contacts

LIMIT 1

"

)

);



$socials = mysqli_query(

$conn,

"

SELECT *

FROM socials

WHERE is_active=1

ORDER BY sort ASC

"

);

?>


<section class="container py-5">


<div class="text-center mb-5">


<h1 class="section-title">

Kontak Kami


</h1>


<p class="text-muted">


Hubungi Dinas Sosial Daerah


Provinsi Sulawesi Utara


</p>


</div>





<div class="row g-4">




<div class="col-lg-5">



<div class="card shadow border-0 h-100">


<div class="card-body p-4">




<h4 class="mb-4">

Informasi Kantor


</h4>




<div class="mb-4">


<h6>


<i class="bi bi-geo-alt-fill text-primary"></i>

Alamat


</h6>


<p>


<?= $contact['address']; ?>


</p>


</div>






<div class="mb-4">


<h6>


<i class="bi bi-telephone-fill text-primary"></i>

Telepon


</h6>


<p>


<?= $contact['phone']; ?>


</p>


</div>







<div class="mb-4">


<h6>


<i class="bi bi-envelope-fill text-primary"></i>

Email


</h6>


<p>


<?= $contact['email']; ?>


</p>


</div>







<div class="mb-4">


<h6>


<i class="bi bi-clock-fill text-primary"></i>

Jam Operasional


</h6>


<p>


<?= $contact['open_hours']; ?>


</p>


</div>








<h5>

Media Sosial


</h5>



<div class="mt-3">



<?php


while(

$s = mysqli_fetch_assoc(

$socials

)

):

?>


<a


href="<?= $s['url'];?>"


target="_blank"


class="btn btn-primary me-2 mb-2"

>


<i

class="bi bi-<?= strtolower($s['icon']); ?>"

></i>


<?= $s['platform'];?>


</a>



<?php endwhile; ?>



</div>





</div>


</div>



</div>








<div class="col-lg-7">



<div class="card shadow border-0">


<div class="card-body p-3">



<div

class="ratio ratio-16x9"

>


<?php


if(

!empty(

$contact['maps_embed']

)

):




echo

$contact['maps_embed'];




else:

?>




<iframe


src="https://maps.google.com/maps?q=manado&t=&z=13&ie=UTF8&iwloc=&output=embed"


style="border:0;"


allowfullscreen


loading="lazy"


>


</iframe>



<?php endif; ?>




</div>



</div>


</div>




</div>



</div>




</section>