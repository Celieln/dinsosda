<?php

$limit = 6;

$page_no = isset($_GET['p']) ? (int)$_GET['p'] : 1;

if($page_no < 1)
{
    $page_no = 1;
}

$offset = ($page_no-1)*$limit;


$search='';

$where="status='published'";


if(isset($_GET['q']))
{

$search=mysqli_real_escape_string(

$conn,

$_GET['q']

);

$where.="

AND (

title LIKE '%$search%'

OR

summary LIKE '%$search%'

)

";

}


$total=mysqli_fetch_assoc(

mysqli_query(

$conn,

"

SELECT COUNT(*)

total

FROM news

WHERE $where

"

)

);


$total_page=ceil(

$total['total']

/

$limit

);



$news=mysqli_query(

$conn,

"

SELECT *

FROM news

WHERE $where

ORDER BY

published_at DESC,

id DESC

LIMIT $offset,$limit

"

);

?>




<section class="container py-5">



<div class="row mb-4">


<div class="col-md-6">


<h1 class="section-title">

Berita


</h1>


<p class="text-muted">

Informasi dan pengumuman terbaru


</p>



</div>





<div class="col-md-6">



<form>



<input

type="hidden"

name="page"

value="berita"

>



<div class="input-group">


<input

type="text"

name="q"

class="form-control"

placeholder="Cari berita..."

value="<?= $search;?>"

>



<button

class="btn btn-primary"

>

Cari


</button>


</div>



</form>



</div>



</div>






<div class="row">



<?php


while(

$n=mysqli_fetch_assoc(

$news

)

):




$image=


!empty(

$n['image']

)


?


$base_url.

'/uploads/berita/'.

$n['image']


:


'https://placehold.co/600x400';




?>





<div class="col-lg-4 mb-4">



<div class="card shadow-sm h-100">



<img


src="<?= $image;?>"


class="card-img-top"


style="


height:230px;

object-fit:cover;

"

>



<div class="card-body">



<span

class="badge

bg-primary"

>


<?= date(

'd M Y',

strtotime(

$n['published_at']

)

);?>


</span>




<h5

class="mt-3"

>


<?= $n['title'];?>


</h5>




<p

class="text-muted"

>


<?= mb_substr(

strip_tags(

$n['summary']

),

0,

120

);?>


...


</p>





<a


href="<?= $base_url;?>/?page=detail_berita&slug=<?= $n['slug'];?>"


class="btn btn-primary"

>


Baca Selengkapnya


</a>



</div>



</div>




</div>



<?php endwhile; ?>




</div>








<?php

if(

$total_page>1

):

?>


<nav>


<ul class="pagination justify-content-center">



<?php


for(

$i=1;

$i<=$total_page;

$i++

):

?>


<li

class="page-item

<?=

$i==$page_no

?

'active'

:

''

?>

"


>



<a


class="page-link"


href="<?=

$base_url

?>

/?page=berita&p=<?=

$i

?>

&q=<?=

urlencode(

$search

)

?>

"


>


<?=

$i

?>


</a>



</li>



<?php endfor; ?>



</ul>



</nav>



<?php endif; ?>




</section>