<?php

$contact = mysqli_fetch_assoc(

mysqli_query(

$conn,

"SELECT * FROM contacts LIMIT 1"

)

);



$socials = mysqli_query(

$conn,

"

SELECT *

FROM socials

WHERE is_active=1

ORDER BY sort

"

);

?>


<footer class="footer">


<div class="container">


<div class="row gy-4">





<div class="col-lg-4">


<img

src="<?= $base_url ?>/uploads/profile/<?= $setting['logo_main'];?>"

width="70"

class="mb-3"

>



<h5 class="fw-bold">

<?= $setting['site_name']; ?>

</h5>



<p>

<?= $setting['site_tagline']; ?>

</p>



</div>






<div class="col-lg-2">


<h5>

Menu


</h5>


<ul class="list-unstyled">



<li>

<a href="<?= $base_url ?>">Home</a>

</li>



<li>

<a href="#">Profil</a>

</li>



<li>

<a href="#">Bidang</a>

</li>



<li>

<a href="#">Layanan</a>

</li>



<li>

<a href="#">Berita</a>

</li>



<li>

<a href="#">Kontak</a>

</li>



</ul>


</div>







<div class="col-lg-3">


<h5>

Kontak


</h5>



<p>


<i class="bi bi-geo-alt-fill"></i>


<?= $contact['address']; ?>


</p>




<?php if(!empty($contact['phone'])): ?>


<p>


<i class="bi bi-telephone-fill"></i>


<?= $contact['phone']; ?>


</p>


<?php endif; ?>






<?php if(!empty($contact['email'])): ?>


<p>


<i class="bi bi-envelope-fill"></i>


<?= $contact['email']; ?>


</p>


<?php endif; ?>





<p>


<i class="bi bi-clock-fill"></i>


<?= $contact['open_hours']; ?>


</p>



</div>







<div class="col-lg-3">


<h5>

Media Sosial


</h5>



<?php


while(

$s=mysqli_fetch_assoc(

$socials

)

){


?>



<a


href="<?= $s['url'];?>"


target="_blank"


class="social"

>


<i

class="bi bi-<?= strtolower($s['icon']); ?>"

></i>



</a>




<?php


}


?>




</div>



</div>




<hr>




<div class="row">


<div class="col-lg-6">


© <?= date('Y');?>


<?= $setting['site_name']; ?>



</div>




<div class="col-lg-6 text-end">


Developed by


DINSOSDA IT TEAM


</div>



</div>



</div>


</footer>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>


<script>


AOS.init({


duration:800


});


</script>



</body>

</html>