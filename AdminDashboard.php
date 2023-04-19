<?php session_start(); 
if(!isset($_SESSION['id'])){
    header('Location: index.php');
}
include_once('connexion.php');
    //Dashboard
    $Reclams = $DB->prepare('select count(*) as nbR from reclamations where statut=?');
    $Reclams->execute(array("pas de réponse"));
    $ReclamsNR = $Reclams->fetch()['nbR'];
    $reclamations = $DB->prepare('select count(*) as totalR from reclamations');
    $reclamations->execute(array());
    $reclamationsT = $reclamations->fetch()['totalR'];
    if($reclamationsT==0){
      $ratio3=1;
    }
    else{
    $ratio3 = $ReclamsNR / $reclamationsT;
    }
    ///
    $facturesIMP = $DB->prepare('select count(*) as nb from factures where status=?');
    $facturesIMP->execute(array("impayé"));
    $impaidInvoices = $facturesIMP->fetch()['nb'];
    $factures = $DB->prepare('select count(*) as total from factures');
    $factures->execute(array());
    $totalInvoices = $factures->fetch()['total'];
    if($totalInvoices==0){
      $ratio=1;
    }
    else{
    $ratio = $impaidInvoices / $totalInvoices;
    }

    $prix = $DB->prepare('select sum(prix_ttc) as prixIMP from factures where status=?');
    $prix->execute(array("impayé"));
    $prixIMP = number_format($prix->fetch()['prixIMP'],2, '.', '');
    $prixT= $DB->prepare('select sum(prix_ttc) as totalP from factures ');
    $prixT->execute(array());
    $prixToatl =number_format($prixT->fetch()['totalP'],2, '.', '') ;
   // echo $prixToatl;
    
    if($prixToatl==0){
      $ratio2=1;
    }
    else{
      $ratio2 = $prixIMP / $prixToatl;
    }
    
    $consom = $DB->prepare('select annee,sum(val_cpt) as somme from consommation_annuelles  group by annee');
    $consom->execute(array());
    $consom2 = $DB->prepare('select zones.id_zone,sum(val_cpt) as somme from consommation_annuelles,zones,client
    where consommation_annuelles.id_client=client.id_client and client.id_zone=zones.id_zone
      group by zones.id_zone');
    $consom2->execute(array());

?>
        
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('header.php'); ?>
</head>
<body>
<?php include_once('style.php'); ?>
    <div class="dash_board mt-2">
        <div class="container">
        <?php include_once('nav.php'); ?>
        <fieldset class="border border-dark p-2 ms-5 mb-0">
            <legend class="float-none w-auto p-2 fw-bolder  main-title">Dashboard  <i class="p-2 fa-solid fa-chart-line fa-lg"></i></legend>
               <div class="row justify-content-between align-items-center">
                <div class="col-5">
                <div class="mb-4">
                 <p class="progress-text ">Factures Impayée :</p>
                 <div class="progress-container ">
                   <div class="progress-bar" style="width: <?php echo $ratio * 100; ?>%;"><?php echo $impaidInvoices . '/' . $totalInvoices; ?></div>
                 </div>
               </div>
               <div class="mb-4">
               <p class="progress-text mb-2">Montant Total : <span class="main-title"><?php echo $prixToatl; ?> DH</span></p>
               <p class="progress-text ">Montant Impayée :</p>
                 <div class="progress-container ">
                   <div class="progress-bar" style="width: <?php echo $ratio2 * 100; ?>%;"><?php echo $prixIMP . ' DH'; ?></div>
                  </div>
               </div>
                </div>
                <div class="col-5">
                <div class="mb-4">
                 <p class="progress-text ">Réclamations non Répondus :</p>
                 <div class="progress-container ">
                   <div class="progress-bar" style="width: <?php echo $ratio3 * 100; ?>%;"><?php echo $ReclamsNR . '/' . $reclamationsT; ?></div>
                 </div>
               </div>
                </div>
               </div>
               <?php if($consom->rowCount()!=0){ ?>
                <div class="row justify-content-between">
               <div class="col-5">
               <table class="table table-dark text-center me-2" id="ttt">
                   <thead  class="td-text">
                       <tr>
                         <th scope="col">Année</th>
                         <th scope="col">Consommation Annuelle</th>
                       </tr>
                  </thead>
                  <tbody>
                  <?php while($row = $consom->fetch()){
                    $annee=$row['annee'];
                    $val_cptS=$row['somme'];
                    $req = $DB->prepare('select sum(val_cpt) as somme from  consommation_annuelles where annee=?');
                    $req->execute(array($annee-1));
                    if($req->rowCount()!=0){
                      $val = $req->fetch()['somme'];
                    }else{ 
                      $val=0;
                    }
                 ?>
               <tr>
               <td><?php echo $annee; ?></td>
               <td> <?php echo $val_cptS-$val; ?> </td>
               </tr>
                <?php } ?>
                  </tbody>
               </table>
               </div>
               <div class="col-5">
                 <!-- //tab2 -->
               <table class="table table-dark text-center me-2 " id="tttt">
                   <thead  class="td-text">
                       <tr>
                         <th scope="col">N° Zone</th>
                         <th scope="col">Consommation Totale</th>
                       </tr>
                  </thead>
                  <tbody>
                  <?php while($row2 = $consom2->fetch()){
                    $id_z=$row2['id_zone'];
                    $val_cptS2=$row2['somme'];
                 ?>
               <tr>
               <td><?php echo $id_z; ?></td>
               <td> <?php echo $val_cptS2; ?> </td>
               </tr>
                <?php } ?>
                <td>Autres zones</td>
                <td>0</td>
                  </tbody>
               </table>
               </div>
              
               </div>
               <?php } ?>

                 
          </fieldset>
        </div> 
    </div>
    <script>
        $(document).ready(function() {
  $('#ttt').DataTable({
    searching: true,  // enable search bar
   lengthMenu: [1],  // set number of lines per page
    pageLength: 1  // set default number of lines per page
  });
});
$(document).ready(function() {
  $('#tttt').DataTable({
    searching: true,  // enable search bar
   lengthMenu: [1],  // set number of lines per page
    pageLength: 1  // set default number of lines per page
  });
});
    </script>
    <script src="js/bootstrap.bundle.min.js"></script>
     <script src="js/all.min.js"></script>
</body>
</html>