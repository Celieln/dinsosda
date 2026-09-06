<?php

$q = trim($_GET['q'] ?? '');

function e($v)
{
    return htmlspecialchars($v,ENT_QUOTES,'UTF-8');
}

?>

<section class="container py-5">


<div class="text-center mb-5">

<h1 class="section-title">

Pencarian


</h1>


<p class="text-muted">

Cari berita, layanan, bidang, maupun informasi lainnya


</p>


</div>



<form method="GET">

<input type="hidden"

name="page"

value="cari">


<div class="row justify-content-center">


<div class="col-md-8">


<div class="input-group shadow">


<input

type="text"

name="q"

class="form-control form-control-lg"

placeholder="Masukkan kata kunci..."

value="<?=e($q);?>"



>



<button

class="btn btn-primary"

>


<i class="bi bi-search"></i>


Cari


</button>


</div>


</div>


</div>


</form>



<?php

if($q!=''):

$search='%'.$q.'%';



$news=mysqli_query(

$conn,

"

SELECT

id,

title,

slug

FROM news

WHERE title LIKE '$search'

OR summary LIKE '$search'

LIMIT 10

"

);




$services=mysqli_query(

$conn,

"

SELECT

id,

name,

slug

FROM services

WHERE name LIKE '$search'

OR summary LIKE '$search'

LIMIT 10

"

);




$departments=mysqli_query(

$conn,

"

SELECT

id,

name,

slug

FROM departments

WHERE name LIKE '$search'

OR summary LIKE '$search'

LIMIT 10

"

);

?>



<hr class="my-5">


<h3 class="mb-4">

Hasil pencarian untuk :

<b>

<?=e($q);?>

</b>


</h3>





<h5 class="mb-3">

Berita


</h5>


<?php if(mysqli_num_rows($news)>0): ?>


<div class="list-group mb-5">


<?php while($n=mysqli_fetch_assoc($news)): ?>


<a

class="list-group-item list-group-item-action"

href="<?=$base_url;?>/?page=detail_berita&slug=<?=$n['slug'];?>"

>


<i class="bi bi-newspaper"></i>


<?=$n['title'];?>


</a>



<?php endwhile; ?>


</div>



<?php else: ?>


<p class="text-muted">

Tidak ada berita ditemukan


</p>



<?php endif; ?>






<h5 class="mb-3">

Layanan


</h5>


<?php if(mysqli_num_rows($services)>0): ?>


<div class="list-group mb-5">


<?php while($s=mysqli_fetch_assoc($services)): ?>


<a

class="list-group-item list-group-item-action"

href="<?=$base_url;?>/?page=detail_layanan&slug=<?=$s['slug'];?>"

>


<i class="bi bi-file-earmark-text"></i>


<?=$s['name'];?>


</a>



<?php endwhile; ?>


</div>



<?php else: ?>


<p class="text-muted">

Tidak ada layanan ditemukan


</p>



<?php endif; ?>








<h5 class="mb-3">

Bidang


</h5>


<?php if(mysqli_num_rows($departments)>0): ?>


<div class="list-group mb-5">


<?php while($d=mysqli_fetch_assoc($departments)): ?>


<a

class="list-group-item list-group-item-action"

href="<?=$base_url;?>/?page=bidang&slug=<?=$d['slug'];?>"

>


<i class="bi bi-diagram-3"></i>


<?=$d['name'];?>


</a>



<?php endwhile; ?>


</div>



<?php else: ?>


<p class="text-muted">

Tidak ada bidang ditemukan


</p>



<?php endif; ?>



<?php endif; ?>



</section>