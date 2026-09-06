<?php

$slug = $_GET['slug'] ?? '';

$stmt = mysqli_prepare($conn,"
SELECT

n.*,
c.name kategori,
u.name penulis

FROM news n

LEFT JOIN categories c
ON c.id=n.category_id

LEFT JOIN users u
ON u.id=n.user_id

WHERE n.slug=?
AND n.status='published'

LIMIT 1

");

mysqli_stmt_bind_param($stmt,"s",$slug);

mysqli_stmt_execute($stmt);

$result=mysqli_stmt_get_result($stmt);

$berita=mysqli_fetch_assoc($result);


if(!$berita)
{

?>

<div class="container py-5">

<div class="alert alert-danger">

Berita tidak ditemukan

</div>

</div>

<?php

return;

}


mysqli_query(

$conn,

"UPDATE news
SET views=views+1
WHERE id=".$berita['id']

);



$galeri=mysqli_query(

$conn,

"

SELECT *

FROM news_images

WHERE news_id=".$berita['id']."

ORDER BY sort ASC

"

);



$related=mysqli_query(

$conn,

"

SELECT

id,
title,
slug,
image

FROM news

WHERE category_id=".$berita['category_id']."

AND id!=".$berita['id']."

AND status='published'

ORDER BY published_at DESC

LIMIT 5

"

);


?>


<style>


.hero-news{

padding:80px 0;

background:

linear-gradient(

135deg,

#0A4DA2,

#1565C0

);

color:white;

}



.news-card{

background:white;

padding:30px;

border-radius:20px;

box-shadow:

0 5px 20px rgba(0,0,0,.08);

}



.related{

padding:15px;

border-bottom:

1px solid #eee;

}



.related img{

width:70px;

height:70px;

object-fit:cover;

border-radius:10px;

}



.gallery{

height:180px;

object-fit:cover;

width:100%;

border-radius:10px;

}


</style>





<section class="hero-news">


<div class="container">



<span class="badge bg-warning">

<?= $berita['kategori'];?>

</span>



<h1 class="mt-3">

<?= $berita['title'];?>


</h1>



<p>


<i class="bi bi-person"></i>

<?= $berita['penulis'];?>


&nbsp;


<i class="bi bi-calendar"></i>


<?= date('d F Y',

strtotime(

$berita['published_at']

)

);?>


&nbsp;


<i class="bi bi-eye"></i>


<?= $berita['views'];?> Views



</p>



</div>


</section>







<section class="py-5">


<div class="container">


<div class="row">



<div class="col-lg-8">



<div class="news-card">





<?php


if($berita['image']):


?>


<img

src="<?= $base_url;?>/uploads/berita/<?= $berita['image'];?>"

class="img-fluid rounded mb-4"

>



<?php endif; ?>





<div>


<?= $berita['body'];?>


</div>





<?php


if(mysqli_num_rows($galeri)>0):

?>


<hr>


<h4>

Galeri Foto


</h4>




<div class="row">



<?php


while($g=mysqli_fetch_assoc($galeri)):


?>


<div class="col-md-4 mb-3">


<img

src="<?= $base_url;?>/uploads/berita/<?= $g['image'];?>"

class="gallery"

>


</div>



<?php endwhile; ?>



</div>




<?php endif; ?>






</div>



</div>







<div class="col-lg-4">



<div class="news-card">



<h4>


Berita Terkait


</h4>



<hr>




<?php


while($r=mysqli_fetch_assoc($related)):


?>


<div class="related">



<div class="d-flex gap-3">



<?php


if($r['image'])
{


?>


<img

src="<?= $base_url;?>/uploads/berita/<?= $r['image'];?>"

>


<?php


}


?>



<div>



<a

href="<?= $base_url;?>/detail_berita?slug=<?= $r['slug'];?>"

>


<?= $r['title'];?>


</a>



</div>



</div>



</div>




<?php endwhile; ?>





<hr>




<a

href="<?= $base_url;?>/berita"

class="btn btn-primary w-100"

>


Kembali Ke Berita


</a>




</div>



</div>






</div>


</div>


</section>