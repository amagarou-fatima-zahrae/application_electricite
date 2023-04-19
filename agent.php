<?php 
session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;500&display=swap " rel="stylesheet">
    <link rel="stylesheet" href="css/bondi.css">
    <link rel="icon" href="imgs/index.jpg" type="image/x-icon">
</head>
<body>
<?php include_once('style.php'); ?>
   <div class="agent">
    <div class="container">
    <div class="my-3">
          <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">Importer les consommations annuelles </a>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog">
                     <div class="modal-content">
                       <div class="modal-header">
                         <h5 class="modal-title" id="exampleModalLabel">Consommations annuelles</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body">
                       <form action="fichierTraitement.php"  id="form" method="post" enctype="multipart/form-data" >
                              <div class="mb-3 fw-bolder " >
                                  <label for="formFile">Fichier Annuelle<i class="fas fa-file-upload mx-2"></i></label>
                                  <input class="form-control" id="formFile" type="file"  name="fich" required>
                              </div>
                            
                            <div class="modal-footer">
                                <button type="submit" name="ajout" class="btn btn-primary" >Importer</button>
                            </div>
          
                       </form>
                       </div>
 
                     </div>
                   </div>
                 </div>
                 <!-- fin model -->
          </div>
    </div>
   </div>
   <script src="js/bootstrap.bundle.min.js"></script>
     <script src="js/all.min.js"></script>
</body>
</html>