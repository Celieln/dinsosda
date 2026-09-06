<?php

require_once __DIR__.'/../config/database.php';

$setting = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT * FROM settings LIMIT 1"
    )
);

?>

<!doctype html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"

content="width=device-width, initial-scale=1">


<title>

<?= $setting['site_name']; ?>

</title>


<link rel="icon"

href="<?= $base_url ?>/assets/favicon.ico">


<link rel="preconnect"

href="https://fonts.googleapis.com">


<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"

rel="stylesheet">


<link rel="stylesheet"

href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">


<link rel="stylesheet"

href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


<link rel="stylesheet"

href="https://unpkg.com/aos@2.3.4/dist/aos.css">



<style>

*{

margin:0;

padding:0;

box-sizing:border-box;

font-family:'Poppins',sans-serif;

}



body{

background:#F3F7FC;

overflow-x:hidden;

}




a{

text-decoration:none;

}





/*===========================

HEADER

===========================*/


.top-header{


background:


linear-gradient(

135deg,

#0A4DA2,

#1565C0,

#2196F3

);



padding:15px 0;



border-bottom:

4px solid #FFD54F;



color:white;


}




.logo{


width:90px;


height:90px;


object-fit:contain;


}





.gov{


width:75px;


height:95px;


object-fit:cover;


border-radius:10px;


border:3px solid rgba(255,255,255,.2);


box-shadow:


0 5px 15px rgba(0,0,0,.25);



}





.header-title{


font-size:28px;


font-weight:700;


line-height:1.2;


margin-bottom:6px;



}





.header-sub{


font-size:15px;


font-weight:500;


opacity:.95;



}





.gov-name{


font-size:12px;


font-weight:500;


display:block;


margin-top:5px;



}





.info-bar{


background:#FFD54F;


padding:8px;


font-size:14px;


font-weight:500;


color:#0A4DA2;



}





/*===========================

NAVBAR

===========================*/



.navbar{


background:white;


box-shadow:


0 3px 12px rgba(0,0,0,.08);



}





.nav-link{


font-weight:600;


margin-left:15px;


color:#0A4DA2;



}




.nav-link:hover{


color:#2196F3;



}





/*===========================

SECTION

===========================*/


.section-title{


font-size:35px;


font-weight:700;


color:#0A4DA2;



}





.card{


border:none;


border-radius:18px;


transition:.3s;



}




.card:hover{


transform:


translateY(-5px);



}





.footer{


background:#0A4DA2;


padding:50px;


color:white;



}





.social{


width:45px;


height:45px;


border-radius:50%;


display:inline-flex;


align-items:center;


justify-content:center;


background:rgba(255,255,255,.15);


font-size:22px;


margin-right:8px;


color:white;



}





.social:hover{


background:white;


color:#1565C0;



}





@media(max-width:768px){



.logo{


width:70px;


height:70px;


}




.gov{


width:60px;


height:75px;


}




.header-title{


font-size:18px;


}




.header-sub{


font-size:13px;


}




}



</style>


</head>



<body>





<div class="top-header">


<div class="container">


<div class="row align-items-center">



<div class="col-lg-2 text-center">


<img

src="<?= $base_url ?>/uploads/profile/<?= $setting['logo_main']; ?>"

class="logo">


</div>






<div class="col-lg-7 text-center">



<h1 class="header-title">


<?= $setting['site_name']; ?>


</h1>




<p class="header-sub">


<?= $setting['site_tagline']; ?>


</p>




<p class="mb-0">


<?= date('d F Y'); ?>


</p>




</div>







<div class="col-lg-3">



<div

class="d-flex

justify-content-center

gap-3"

>





<div class="text-center">



<img


src="<?= $base_url ?>/uploads/profile/<?= $setting['photo_gov']; ?>"


class="gov"

>



<span class="gov-name">


<?= $setting['gov_name']; ?>


</span>



</div>






<div class="text-center">



<img


src="<?= $base_url ?>/uploads/profile/<?= $setting['photo_wagov']; ?>"


class="gov"

>



<span class="gov-name">


<?= $setting['wagov_name']; ?>


</span>



</div>





</div>



</div>




</div>



</div>



</div>






<div class="info-bar">


<marquee>


Selamat datang di Website Resmi


<?= $setting['site_name']; ?>


•

Pelayanan Online

•

Informasi Bantuan Sosial

•

Pengaduan Masyarakat

•

Berita dan Pengumuman


</marquee>



</div>