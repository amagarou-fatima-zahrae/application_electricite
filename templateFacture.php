<?php
//session_start();
if(isset($_SESSION['data'])){
   ////echo 'how';
// print_r($_SESSION['data']);
    $num_fact = htmlentities($_SESSION['data']['id_fact']);
    $id_cli = htmlentities($_SESSION['data']['id_client']);
    $id_consom = htmlentities($_SESSION['data']['id_consom']);
    $date_fact = htmlentities($_SESSION['data']['date_fact']);
    $date_ech = htmlentities($_SESSION['data']['date_echeance']);
    $mois = htmlentities($_SESSION['data']['mois']);
    $annee = htmlentities($_SESSION['data']['annee']);
    $consom_m = htmlentities($_SESSION['data']['consom_mois']);
    $ht = htmlentities($_SESSION['data']['prix_ht']);
    $ttc = htmlentities($_SESSION['data']['prix_ttc']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>formulaire</title>
</head>
<body>
    <div class="formulaire">
        <div class="container">
         <fieldset class="border border-dark p-2 mb-4">
                <legend class="float-none w-auto p-2 fw-bolder">Facture</legend>
                <div class="row justify-content-around align-items-center">
                  
                   <div class="col-8">
                   <p class="fw-bold fz-4 my-2">
              
              Numéro de la facture:<span class="fw-bold px-3" style="color:#0D47A1;" ><?php echo $num_fact ;?></span> 
              </p>
              <p class="fw-bold fz-4 my-2">
            
              Numéro de la consommation: <span class="fw-bold px-3" style="color:#0D47A1;" ><?php echo $id_consom;?></span> 
              </p>
              <p class="fw-bold fz-4 my-2">
            
              Numéro du client:<span class="fw-bold px-3" style="color:#0D47A1;" ><?php echo $id_cli;?></span>  
              </p>
              <p class="fw-bold fz-4 my-2">
            
                Consommation mensuelle:<span class="fw-bold px-3" style="color:#0D47A1;" ><?php echo $consom_m;?></span> 
              </p>
              <p class="fw-bold fz-4 my-2">
                 Mois: <span class="fw-bold px-3" style="color:#0D47A1;" ><?php echo $mois;?></span>
              </p>
               <p class="fw-bold fz-4 my-2">
            
                 Année: <span class="fw-bold px-3" style="color:#0D47A1;" ><?php echo $annee;?></span>
              </p>
              <p class="fw-bold fz-4 my-2">
                 Date de facturation: <span class="fw-bold px-3" style="color:#0D47A1;" ><?php echo $date_fact;?></span>
              </p>
              <p class="fw-bold fz-4 my-2">
                 Date d'échéance: <span class="fw-bold px-3" style="color:#0D47A1;" ><?php echo $date_ech;?></span>
              </p>
                   </div>
                </div>
          </fieldset>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" ></script>
</body>
</html>