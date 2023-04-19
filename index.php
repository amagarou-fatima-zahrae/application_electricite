<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('header.php'); ?>  
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
          <a class="navbar-brand" href="">
          <span class=" text-uppercase mx-2 py-3 fw-bolder fs-3 merriweather" style="color:#f83858;">ELECTRIK</span>
          <br>
            <img src="imgs/logo3.png" alt="">
          </a>
          <button class="navbar-toggler"
           type="button" 
           data-bs-toggle="collapse" 
           data-bs-target="#main"
           aria-controls="main"
            aria-expanded="false"
             aria-label="Toggle navigation">
             <i class="fa-solid fa-bars"></i>
          </button>
          <div class="collapse navbar-collapse" id="main">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active p-2 p-lg-3 " aria-current="page" href="#H" >Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link  p-2 p-lg-3 " href="#A">About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link  p-2 p-lg-3" href="#C">Contact</a>
              </li>
            </ul>
            <!-- <div class="search ps-3 pe-3 d-none d-lg-block">
              <i class="fa-solid fa-magnifying-glass"></i>
            </div>  -->
            <select class=" btn main-btn rounded-pill f-w-bold" id="type">
              <option selected disabled>Login</option>
              <option value="Admin">Admin</option>
              <option value="Agent">Agent</option>
              <option value="Client">Client</option>
            </select>
            <!-- <a class="btn main-btn rounded-pill f-w-bold" href="#" >Login</a> -->
          </div>
        </div>
      </nav>
      <!-- landing -->
      <div class="landing d-flex justify-content-center align-items-center" id="H">
        <div class="text-center text-light ">
          <h1>Bienvenue chez <span  style="color:#f83858;">ELECTRIK</span></h1>
          <p class=" fs-6 text-white-50 mb-4"> Suivez facilement votre consommation et vos factures d'énergie mensuelles.</p>
          
        </div>
      </div>
      <!-- About -->
      <div class="landing d-flex justify-content-center align-items-center" id="A">
        <div class="text-center text-light ">
          <h1><span  style="color:#f83858;">ELECTRIK</span></h1>
          <p class=" fs-6 text-white-50 mb-4">Notre site Web  d'électricité conçu pour que les clients puissent gérer leurs comptes et accéder à diverses fonctionnalités d'une manière informatif et sécurisé.
          </p>
          <p class=" fs-6 text-white-50 mb-4">
         Vous premet de visualiser votre consommation d'électricité au fil du temps et suivre leurs tendances de consommation d'énergie,ainsi que vos factures

          </p>
          
        </div>
      </div>
      
          <!-- footer -->
          <div class="footer py-5 text-center text-md-start  text-white-50" id="C">
            <div class="container">
              <div class="row">
                <div class="col-lg-4 col-md-6 ">
                  <div class=" info mb-5">
                    <img class="mb-4" src="imgs/logo.png" alt="">
                    <p class="mb-5">Electrik </p>
                    <div class="copyright">
                        <div>
                          &copy; 2023 - <span>ElectriK</span>
                        </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-2 col-md-6 ">
                  <div class=" links fs-5 text-light pb-3">Links</div>
                  <ul class="list-unstyled lh-lg">
                    <li>Home</li>
                    <li>Our Services</li>
                    <li>Portfolio</li>
                    <li>Support</li>
                    <li>Terms and Condition</li>
                  </ul>
                </div>
                <div class="col-lg-2 col-md-6 ">
                  <div class=" links fs-5 text-light pb-3">Links</div>
                  <ul class="list-unstyled lh-lg">
                    <li>Sign In</li>
                    <li>Register</li>
                    <li>About Us</li>
                    <li>Contact Us</li>
                  </ul>
                </div>
                <div class="col-lg-4 col-md-6  ">
                  <div class="contact ">
                    <h5 class="text-light">Contact Us</h5>
                    <p class="mb-5 mt-3 mb-5">Get in touch with us via mail phone.We are waiting for your call or message</p>
                    <a class="btn main-btn rounded-pill w-100" href="#" >Electrik@gmail.com</a>
                    <ul class="links d-flex list-unstyled mt-5 gap-3">
                      <li> <a class="d-block text-light" href="#"><i class="fa-brands fa-facebook fa-lg facebook rounded-circle p-2" ></i></a></li>
                      <li> <a class="d-block text-light" href="#"><i class="fa-brands fa-twitter fa-lg  twitter rounded-circle p-2" ></i> </a></li>
                      <li> <a class="d-block text-light" href="#"><i class="fa-brands fa-linkedin fa-lg  linkedin rounded-circle p-2"></i></a> </li>
                      <li> <a class="d-block text-light" href="#"><i class="fa-brands fa-youtube fa-lg youtube rounded-circle p-2"> </i> </a></li>
                   </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>


  <script>
  var select = document.getElementById("type");
  select.addEventListener("change", function() {
    var userType = select.options[select.selectedIndex].value;
    if (userType) {
      window.location.href = "authentif.php?type=" + userType;
    }
  });
</script>
    <script src="js/bootstrap.bundle.min.js"></script>
     <script src="js/all.min.js"></script>
</body>
</html>