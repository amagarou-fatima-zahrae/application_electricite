<?php
session_start();
use Dompdf\Dompdf;
use Dompdf\Options;
if(!isset($_SESSION['id'])){
    header('Location: index.php');
}
include_once('connexion.php');
if(isset($_POST['oui'])){
  $modif=$DB->prepare('update factures set status=? where id_fact=?');
  $modif->execute(array("payée",$_POST['fact']));
}
  
  if($_SESSION['type']=="Admin"){
    $factures = $DB->prepare('select * from factures');
    $factures->execute(array());
  }
  elseif($_SESSION['type']=="Client"){
    $factures = $DB->prepare('select * from factures where id_client=?');
    $factures->execute(array($_SESSION['id']));
  }
  else{
    $factures = $DB->prepare('select * from factures where id_client in(select id_client from client where
    id_zone=(select id_zone from agent where id_agent=?))');
    $factures->execute(array($_SESSION['id']));
  }
  // générer PDF
if(isset($_POST['pdf'])){
 // print_r($_POST);
//    print_r(json_decode($_POST['data'], true));
$id_fact=$_POST['id_fact'];
$id_cli = $_POST['id_client'];
$id_cons=$_POST['id_consom'];
$date_fact=$_POST['date_fact'] ;
$date_ech=$_POST['date_echeance'];
$cons=$_POST['consom_mois'];
$ht=$_POST['prix_ht'];
$ttc=$_POST['prix_ttc'];
$mois=$_POST['mois'];
$annee=$_POST['annee'] ;
 $ligne=array_merge(['id_fact'=>$id_fact,'id_client'=>$id_cli,'id_consom'=>$id_cons,'date_fact'=>$date_fact,'date_echeance'=>$date_ech,'mois'=>$mois,'annee'=>$annee,'consom_mois'=>$cons,'prix_ht'=>$ht,'prix_ttc'=>$ttc]); 

   $_SESSION['data']=$ligne;
  //echo  $_SESSION['data']['id_fact'];
//header('Location: templateFacture.php');
    require_once __DIR__ . '/dompdf/autoload.inc.php';
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    ob_start();
    include 'templateFacture.php';
    $html = ob_get_clean();
    
    $dompdf->loadHtml(html_entity_decode($html));
 
    $dompdf->setPaper('A4', 'portrait');
 
    $dompdf->render();
 
     $dompdf->stream('facture.pdf');
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('header.php'); ?>
</head>
<body>
<?php include_once('style.php'); ?>
    <div class="factures" >
    <?php include_once('nav.php'); ?>
        <div class="container" >
        <div class="mb-4"><?php include_once('navbar.php'); ?></div>

    <table class="table table-dark text-center my-3" id="fact">
     <thead  class="td-text">
    <tr>
      <th scope="col">N° Facture</th>
      <?php if(($_SESSION['type']=="Admin") || ($_SESSION['type']=="Agent")){
        ?>
        <th scope="col">N° Client</th>
        <?php
      } ?>
      <th scope="col">N° Consommation</th>
      <th scope="col">Date facturation</th>
      <th scope="col">Date Echéance</th> 
      <th scope="col">Mois</th>
      <th scope="col">Année</th>
      <th scope="col">Consommation de mois</th>
      <th scope="col">Prix HT</th>
      <th scope="col">TTC</th>
      <th scope="col">Statut</th> 
      <?php if($_SESSION['type']=="Admin"){
      ?>
      <th scope="col"></th> 
      <?php } ?>
      <th scope="col"></th> 
    </tr>
  </thead>
  <tbody>
    <?php
   while($row=$factures->fetch()){
  
    $id_fact=$row['id_fact'];
    $id_cli = $row['id_client'];
    $id_cons=$row['id_consom'];
    $date_fact=$row['date_fact'] ;
    $date_ech=$row['date_echeance'];
    $cons=$row['consom_mois'];
    $ht=$row['prix_ht'];
    $ttc=$row['prix_ttc'];
    $req2 = $DB->prepare('select mois,annee from consommations where id_consom=?');
    $req2->execute(array($id_cons));
    $result = $req2->fetch();
    $mois=$result['mois'];
    $annee=$result['annee'] ;
     //print_r(json_encode($ligne));
    // echo '     la ';
    ?>
    <tr>
       <td><?php  echo $id_fact;?></td>
       <?php if(($_SESSION['type']=="Admin") || ($_SESSION['type']=="Agent")){
        ?>
        <td><?php  echo $id_cli ;?></td>
        <?php
      } ?> 
       <td><?php  echo $id_cons;?></td>
       <td><?php  echo $date_fact;?></td>
       <td><?php  echo  $date_ech;?></td>
       <td><?php  echo $mois ;?></td>
       <td><?php  echo $annee;?></td>
       <td><?php  echo $cons;?></td>
       <td><?php  echo  $ht;?></td>
       <td><?php  echo  $ttc;?></td>

       <td><?php  echo $row['status'] ;?></td>
       <?php if($_SESSION['type']=="Admin"){
      ?>
       <td>
        <button type="button" class="main-title bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#dd<?php echo $row['id_fact'];?>">
          <i class="fa-solid fa-money-bill"></i>
        </button>
        <div class="modal fade" id="dd<?php echo  $row['id_fact'];?>" tabindex="-1" aria-labelledby="ddd<?php echo $row['id_fact'];?>" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
       <div class="modal-content">
       <div class="modal-header">
        <h5 class="modal-title py-2 main-title" id="ddd<?php echo  $row['id_fact'];?>">Confirmation </h5>
        <button type="button" class="btn-close main-btn" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
                <div class="modal-body">
                    <?php if($row['status']!=="payée"){
                      ?>
                      <div class="alert alert-success" role="alert">
                    <i class="fas fa-exclamation-triangle fa-lg" style="color:red;"></i> Vous êtes sûre que la facture est payée ?
                    </div>
                    <form action=""  id="form" method="post" enctype="multipart/form-data">    
                              <div class="modal-footer">
                                <input type="hidden" name="fact" value="<?php echo  $row['id_fact'];?>">
                                <button type="submit" name="oui" class="btn main-btn"><i class="fa-solid fa-circle-check fa-lg"></i></button>
                                <button type="submit" name="non" class="btn main-btn"><i class="fa-solid fa-ban"></i></button>
                              </div>
                      </form>
                      <?php
                    }else{
                      ?>
                      <div class="alert alert-success" role="alert">
                      <i class="fas fa-exclamation-triangle fa-lg" style="color:red;"></i> La facture est dèja payée 
                      </div>
                      
                      <?php
                    }
                      ?>
               </div>
    </div>
  </div>
</div>
 <!-- fin modal -->
       </td>
       <?php } ?>
       <td>

        <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_fact" value="<?php echo $id_fact;?>">
        <input type="hidden" name="id_client" value="<?php echo $id_cli;?>">
        <input type="hidden" name="id_consom" value="<?php echo $id_cons;?>">
        <input type="hidden" name="date_fact" value="<?php echo $date_fact;?>">
        <input type="hidden" name="date_echeance" value="<?php echo $date_ech;?>">
        <input type="hidden" name="mois" value="<?php echo $mois;?>">
        <input type="hidden" name="annee" value="<?php echo $annee;?>">
        <input type="hidden" name="consom_mois" value="<?php echo $cons;?>">
        <input type="hidden" name="prix_ht" value="<?php echo $ht;?>">
        <input type="hidden" name="prix_ttc" value="<?php echo $ttc;?>">
        <button type="submit" name="pdf" class="main-btn"><i class="fa-solid fa-download fa-lg px-2"></i></button>
       </form>
     </td>
    </tr> 
 <?php 
  }   ?> 
</tbody>
</table>
  </div>
</div>
<script>
  $(document).ready(function() {
  $('#fact').DataTable({
    searching: true,  // enable search bar
    lengthMenu: [5, 10, 15],  // set number of lines per page
    pageLength: 5  // set default number of lines per page
  });
});
</script>
<?php include_once('footer.php'); ?>
</body>
</html>