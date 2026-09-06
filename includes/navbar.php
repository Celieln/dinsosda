<?php

$menus = mysqli_query($conn,"
SELECT *
FROM menus
WHERE is_active=1
ORDER BY sort ASC
");

$departments = mysqli_query($conn,"
SELECT *
FROM departments
WHERE is_active=1
ORDER BY sort ASC
");

$services = mysqli_query($conn,"
SELECT *
FROM services
WHERE is_active=1
ORDER BY sort ASC
");

?>


<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">

<div class="container">


<a class="navbar-brand fw-bold text-primary"

href="<?= $base_url ?>">

DINSOSDA

</a>



<button

class="navbar-toggler"

type="button"

data-bs-toggle="collapse"

data-bs-target="#navbarNav"

>

<span class="navbar-toggler-icon"></span>

</button>




<div

class="collapse navbar-collapse"

id="navbarNav"

>


<ul class="navbar-nav ms-auto">



<?php while($m = mysqli_fetch_assoc($menus)): ?>


<?php if($m['slug']=='bidang'): ?>


<li class="nav-item dropdown">


<a

class="nav-link dropdown-toggle"

href="#"

data-bs-toggle="dropdown"

>

<?= $m['title']; ?>

</a>



<ul class="dropdown-menu">


<?php

mysqli_data_seek($departments,0);

while($d=mysqli_fetch_assoc($departments)):

?>


<li>

<a

class="dropdown-item"

href="<?= $base_url ?>/?page=bidang&slug=<?= $d['slug']; ?>"

>

<?= $d['name']; ?>

</a>

</li>


<?php endwhile; ?>


</ul>


</li>




<?php elseif($m['slug']=='layanan'): ?>


<li class="nav-item dropdown">


<a

class="nav-link dropdown-toggle"

href="#"

data-bs-toggle="dropdown"

>

<?= $m['title']; ?>

</a>



<ul class="dropdown-menu">


<?php

mysqli_data_seek($services,0);

while($s=mysqli_fetch_assoc($services)):

?>


<li>

<a

class="dropdown-item"

href="<?= $base_url ?>/?page=detail_layanan&slug=<?= $s['slug']; ?>"

>

<?= $s['name']; ?>

</a>

</li>


<?php endwhile; ?>


</ul>


</li>




<?php else: ?>


<li class="nav-item">


<a

class="nav-link"

href="<?= $base_url ?>/?page=<?= $m['slug']; ?>"

>

<?= $m['title']; ?>

</a>


</li>



<?php endif; ?>


<?php endwhile; ?>


</ul>


</div>


</div>


</nav>