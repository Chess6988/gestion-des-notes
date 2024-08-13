<?php
session_start();
require_once 'db_connect.php';

// Debugging session variable
error_log("Session userId_etudiant at start: " . ($_SESSION['userId_etudiant'] ?? 'not set'));

// Verify if the student's ID is set in the session
if (!isset($_SESSION['userId_etudiant'])) {
    echo "Student ID is not set in session. Please create an account first.";
    exit();
}

$id_etudiant = $_SESSION['userId_etudiant'];

// Fetch student profile information
$sql_profile = "
SELECT e.id_etudiant, e.firstName_etudiant, e.lastName_etudiant, f.nom_filiere AS filiere, s.nom_semestre AS semestre, a.annee AS annee, n.nom_niveau AS niveau
FROM profile_etudiant pe
JOIN etudiants e ON pe.id_etudiant = e.id_etudiant
JOIN filieres f ON pe.id_filiere = f.id_filiere
JOIN semestres s ON pe.id_semestre = s.id_semestre
JOIN annees a ON pe.id_annee = a.id_annee
JOIN niveaux n ON pe.id_niveau = n.id_niveau
WHERE pe.id_etudiant = ?";

$stmt_profile = $conn->prepare($sql_profile);
if (!$stmt_profile) {
    die("Prepare failed: " . $conn->error);
}
$stmt_profile->bind_param("i", $id_etudiant);
$stmt_profile->execute();
$result_profile = $stmt_profile->get_result();
$profile = $result_profile->fetch_assoc();

if (!$profile) {
    echo "No profile information found for the student ID: " . htmlspecialchars($id_etudiant);
    exit();
}

// Fetch student subjects
$sql_matieres = "
SELECT m.nom_matiere AS matiere, me.id_annee, a.annee
FROM matieres_etudiants me
JOIN matieres m ON me.id_matiere = m.id_matiere
JOIN annees a ON me.id_annee = a.id_annee
WHERE me.id_etudiant = ?";
$stmt_matieres = $conn->prepare($sql_matieres);
if (!$stmt_matieres) {
    die("Prepare failed: " . $conn->error);
}
$stmt_matieres->bind_param("i", $id_etudiant);
$stmt_matieres->execute();
$result_matieres = $stmt_matieres->get_result();

// Fetch common subjects
$sql_matieres_communes = "
SELECT mc.nom_matiere_commune AS matieres_communes, mce.id_annee, a.annee
FROM matieres_communes_etudiants mce
JOIN matieres_communes mc ON mce.id_matiere_commune = mc.id_matiere_commune
JOIN annees a ON mce.id_annee = a.id_annee
WHERE mce.id_etudiant = ?";
$stmt_matieres_communes = $conn->prepare($sql_matieres_communes);
if (!$stmt_matieres_communes) {
    die("Prepare failed: " . $conn->error);
}
$stmt_matieres_communes->bind_param("i", $id_etudiant);
$stmt_matieres_communes->execute();
$result_matieres_communes = $stmt_matieres_communes->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Index - Mentor Bootstrap Template</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <link href="https://www.ubastudent.online/bs/css/style.css" rel="stylesheet">
  <link href="https://www.ubastudent.online/vendors/bower_components/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
  <link href="https://www.ubastudent.online/vendors/bower_components/animate.css/animate.min.css" rel="stylesheet">
  <link href="https://www.ubastudent.online/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css"
        rel="stylesheet">
  <link href="https://www.ubastudent.online/vendors/bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css"
        rel="stylesheet">
  <link href="https://www.ubastudent.online/vendors/bower_components/google-material-color/dist/palette.css" rel="stylesheet">
  <link href="https://www.ubastudent.online/vendors/bower_components/bootstrap-select/dist/css/bootstrap-select.css"
        rel="stylesheet">

  <!-- CSS -->
  <link href="https://www.ubastudent.online/css/template/app.min.1.css" rel="stylesheet">
  <link href="https://www.ubastudent.online/css/template/app.min.2.css" rel="stylesheet">
  <link media="all" type="text/css" rel="stylesheet"
        href="https://www.ubastudent.online/vendors/bower_components/bootstrap-sweetalert/lib/sweet-alert.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body class="index-page">
<style>
  .navbar-nav .nav-link {
    color: black; /* Link text color */
    position: relative; /* Ensure relative positioning for pseudo-element */
    padding-bottom: 0.5rem; /* Space below link for red line */
    transition: color 0.3s; /* Smooth transition for link color change */
}
.navbar-nav .nav-link:hover,
.navbar-nav .nav-link.active {
    color: blue; /* Text color on hover and active */
}
.navbar-nav .nav-link::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 3px; /* Height of red line */
    background-color: red; /* Red line color */
    transform: scaleX(0); /* Initially hidden */
    transition: transform 0.3s ease-out; /* Smooth transition for line appearance */
}
.navbar-nav .nav-link:hover::after,
.navbar-nav .nav-link.active::after {
    transform: scaleX(1); /* Show red line on hover and active */
}
.collapse .nav-item .nav-link {
    font-size: 25px;
    font-family: cursive;
}

/* Additional styles to ensure the CSS applies correctly to your HTML structure */
.navmenu ul {
    list-style: none;
    padding-left: 0;
    display: flex;
    justify-content: flex-end;
}
.navmenu ul li {
    margin-right: 20px;
}
.navmenu ul li a {
    color: black;
    position: relative;
    padding-bottom: 0.5rem;
    transition: color 0.3s;
    text-decoration: none;
}
.navmenu ul li a:hover,
.navmenu ul li a.active {
    color: blue;
}
.navmenu ul li a::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 3px;
    background-color: red;
    transform: scaleX(0);
    transition: transform 0.3s ease-out;
}
.navmenu ul li a:hover::after,
.navmenu ul li a.active::after {
    transform: scaleX(1);
}
.navmenu ul li.dropdown:hover > ul {
    display: block;
}
.navmenu ul li.dropdown ul {
    display: none;
    position: absolute;
    list-style: none;
    padding-left: 0;
}
.navmenu ul li.dropdown ul li {
    margin-right: 0;
}
</style>

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.html" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <img src="https://www.ime-school.com/wp-content/uploads/2023/11/logo-ime-p.png" alt="IME School Logo" style="width: 100%; height: auto;">

      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
            <li><a   href="index.html" >Page d'acceuil<br></a></li>
            <li><a class="active">Profils</a></li>
    
            <li><a href="report.php">Vos notes</a></li>
            <li><a href="report_card.php">Relevez des note</a></li>
          
         
          
                <i class="mobile-nav-toggle d-xl-none bi bi-list" style="font-size: 100%; font-weight: bolder;"></i>
    </nav>

    

    </div>
  </header>

  <main class="main">

    <!-- Page Title 
    <div class="page-title"    data-aos="fade" style="background-color: blue;">
      <div class="heading">
        <div class="container">
          <div class="row d-flex justify-content-center text-center"  >
            <div class="col-lg-8">
              <h1>Profil </h1><hr>
              
              
            </div>
          </div>
        </div>
      </div>
      <nav class="breadcrumbs" style="background-color: blue; display: flex; justify-content: center; align-items: center;">
        <div class="container" style="display: flex; justify-content: center; align-items: center;">
          <ol style="display: flex; justify-content: center; align-items: center; list-style: none; padding: 0; margin: 0;">
            
           
         
          </ol>
        </div>
      </nav>
      
    </div> End Page Title -->
    <div    class="page-title" data-aos="fade"  class="page-title" data-aos="fade" style="background-color: blue;">
        <div class="heading">
          <div class="container">
            <div class="row d-flex justify-content-center text-center"  >
              <div class="col-lg-8">
                <h1>Profil</h1><hr>
                
                
              </div>
            </div>
          </div>
        </div>
      
        
      </div>
    
    <section id="main">

      <section id="content">
          <div class="container">
              <div class="card">
                  <br/>
                  <br/>
      
      <div style='margin-right:20%;margin-left: 20%;cursor: pointer' id="msg" onclick="clearMsg(this)"></div>
                  
         
      
          </script>
       <style>
          .tooltip-inner {
            color: black;
            background: white;
            max-width: 350px;
            /* If max-width does not work, try using width instead */
            width: 350px;
          }
          .tooltip.top .tooltip-arrow { border-top-color: white; }
          .tooltip.right .tooltip-arrow { border-right-color:white; }
          .tooltip.bottom .tooltip-arrow { border-bottom-color: white; }
          .tooltip.left .tooltip-arrow { border-left-color: white; }
          </style>
      
      <div style ="margin-left: 10%;margin-right: 10%">
       <style>
             
          </style>
          <div class="card" id="profile-main">
              <div style="overflow: visible;" class="pm-overview c-overflow mCustomScrollbar _mCS_4 mCS-autoHide">
                  <div tabindex="0" id="mCSB_4"
                       class="mCustomScrollBox mCS-minimal-dark mCSB_vertical_horizontal mCSB_outside">
                      <div id="mCSB_4_container" class="mCSB_container mCS_x_hidden mCS_no_scrollbar_x"
                           style="position: relative; top: 0px; left: 0px; width: 100%;" dir="ltr">
                          <div class="pmo-pic">
                              <div class="p-relative">
                                                                                              <a href="">
                                          <img class="img-responsive mCS_img_loaded"
                                               src="https://www.ubastudent.online/images/profile-pics/2.png" alt="">
                                      </a>
                                                          </div>
                              <div class="pmo-stat">
                                  <h4 class="m-0 c-white" style="font-style: italic; font-size: 20px">
                                    Salut comment tu vas 
                                  <?php echo htmlspecialchars($profile['firstName_etudiant']); ?></h4>
      
                              </div>
                          </div>
      
                        
                      </div>
                  </div>
                  <div style="display: block;" id="mCSB_4_scrollbar_vertical"
                       class="mCSB_scrollTools mCSB_4_scrollbar mCS-minimal-dark mCSB_scrollTools_vertical">
                      <div class="mCSB_draggerContainer">
                          <div id="mCSB_4_dragger_vertical" class="mCSB_dragger"
                               style="position: absolute; min-height: 50px; display: block; height: 577px; max-height: 672px;"
                               oncontextmenu="return false;">
                              <div style="line-height: 50px;" class="mCSB_dragger_bar"></div>
                          </div>
                          <div class="mCSB_draggerRail"></div>
                      </div>
                  </div>
                  <div style="display: none;" id="mCSB_4_scrollbar_horizontal"
                       class="mCSB_scrollTools mCSB_4_scrollbar mCS-minimal-dark mCSB_scrollTools_horizontal">
                      <div class="mCSB_draggerContainer">
                          <div id="mCSB_4_dragger_horizontal" class="mCSB_dragger"
                               style="position: absolute; min-width: 50px; width: 0px; left: 0px;"
                               oncontextmenu="return false;">
                              <div class="mCSB_dragger_bar"></div>
                          </div>
                          <div class="mCSB_draggerRail"></div>
                      </div>
                  </div>
              </div>
      
              <div class="pm-body clearfix">
      
                 
      
                  <div class="pmb-block">
                      <div class="pmbb-header">
                          <h2><i class=" m-r-5"></i> Information Personel<hr></h2>
                      </div>
                      <div class="pmbb-body p-l-30">
                          <div class="pmbb-view">
                              <dl class="dl-horizontal">
                                  <dt> ID</dt>
                                  <dd><?php echo htmlspecialchars($profile['id_etudiant']); ?></dd>
                              </dl>

                            
                                
                              <dl class="dl-horizontal">
                                <dt> Nom</dt>
                                <dd> <?php echo htmlspecialchars($profile['firstName_etudiant']); ?></dd>
                            </dl>
                                
                            <dl class="dl-horizontal">
                                <dt> Prenom</dt>
                                <dd> <?php echo htmlspecialchars($profile['lastName_etudiant']); ?></dd>
                            </dl>
<!----from here--->
                            <dl class="dl-horizontal">
                                <dt> Filiere</dt>
                                <dd><?php echo htmlspecialchars($profile['filiere']); ?></dd>
                            </dl>

                            <dl class="dl-horizontal">
                                <dt> Semestre choisi</dt>
                                <dd> <?php echo htmlspecialchars($profile['semestre']); ?></dd>
                            </dl>

                            <dl class="dl-horizontal">
                                <dt> Année choisi</dt>
                                <dd> <?php echo htmlspecialchars($profile['annee']); ?></dd>
                            </dl>
                            

                            <dl class="dl-horizontal">
                                <dt> Niveau choisi</dt>
                                <dd> <?php echo htmlspecialchars($profile['niveau']); ?></dd>
                            </dl>


                        
                            
                            
                            <dl class="dl-horizontal">
                                <dt> matiere choisi</dt>
                                <dd> <ul>
                    <?php while ($matiere = $result_matieres->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($matiere['matiere']); ?></li>
                    <?php endwhile; ?>
                    </ul></dd>
                            </dl>


                            <dl class="dl-horizontal">
                                <dt> Les Tronc  commun</dt>
                                <dd>  <ul>
                    <?php while ($matiere_commune = $result_matieres_communes->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($matiere_commune['matieres_communes']); ?></li>
                    <?php endwhile; ?>
                    </ul></dd>
                            </dl>
                            <p>
                    <a href="../edit_profile.php" class="btn btn-primary"><i class="fas fa-edit"></i> Editer votre profil</a>
                    
                </p>
               
                            
                          </div>
                      </div>
                  </div>
      
      
                  
              </div>

              
          </div>
      
      <script src="https://www.ubastudent.online/js/student/notices.js"></script>
      
      </div>
          <!-- Added by Kuete Nkwentamo Valdes fred -->
      
            
      <br/><br/><br/>
      <br/><br/><br/>
      
                  <br/>
                  <br/>
              </div>
      
          </div>
      </section>
      
      <div id ="up"></div>
      <footer>
             
          <ul class="f-menu">
            
              
                  
              
          </ul>
          <div class="row " style="text-align: center;COLOR:black">
          <p class="" >Par Le Developpeur  &nbsp;<a  target="_blank"  style="color: red"> KUETE VALDES</a>&nbsp;&nbsp;<br/> <span>&copy</span>2024</p>
      </div></footer>
      
      </section>



  </main>
  

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>