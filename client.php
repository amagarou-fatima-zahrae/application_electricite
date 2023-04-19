<?php 
session_start();
if(!isset($_SESSION['id'])){
    header('Location: index.php');
}
  include_once('connexion.php');
  if(isset($_GET['id']) && !empty($_GET['id'])){
    $zonesC = $DB->prepare('select * from zones');
    $zonesC->execute(array());
    $client=$DB->prepare("SELECT * FROM client WHERE id_client=? ");
    if($_SESSION['type']=="Client"){
      $client->execute(array($_SESSION['id']));
      $id_cli = $_SESSION['id'];
    }
    elseif(($_SESSION['type']=="Admin")||($_SESSION['type']=="Agent")){
      $client->execute(array($_GET['id']));
      $id_cli=$_GET['id'];
    }
   
    if($client->rowCount()>0){
        $client_info=$client->fetch();
       $nom = $client_info['nom'];
       $prenom = $client_info['prenom'];
       $tel = $client_info['tel'];
       $email =$client_info['email'];
       $num_identite = $client_info['num_identite'];
       $num_cpt_bank = $client_info['compte_bancaire'];
       $num_compteur = $client_info['num_compteur'];
       $image = $client_info['image'];
       $adresse =  $client_info['Adresse'];
       $zone = $client_info['id_zone'];
    }
    $facturesIMP = $DB->prepare('select count(*) as nb from factures where id_client=? and status=?');
    $facturesIMP->execute(array($id_cli,"impayé"));
    $impaidInvoices = $facturesIMP->fetch()['nb'];
    $factures = $DB->prepare('select count(*) as total from factures where id_client=?');
    $factures->execute(array($id_cli));
    $totalInvoices = $factures->fetch()['total'];
    if($totalInvoices==0){
      $ratio=1;
    }
    else{
    $ratio = $impaidInvoices / $totalInvoices;
    }

    $prix = $DB->prepare('select sum(prix_ttc) as prixIMP from factures where id_client=? and status=?');
    $prix->execute(array($id_cli,"impayé"));
    $prixIMP = number_format($prix->fetch()['prixIMP'],2, '.', '');
    $prixT= $DB->prepare('select sum(prix_ttc) as totalP from factures where id_client=?');
    $prixT->execute(array($id_cli));
    $prixToatl =number_format($prixT->fetch()['totalP'],2, '.', '') ;
   // echo $prixToatl;
    
    if($prixToatl==0){
      $ratio2=1;
    }
    else{
      $ratio2 = $prixIMP / $prixToatl;
    }
    $consom = $DB->prepare('select * from consommation_annuelles  where id_client=?');
    $consom->execute(array($id_cli));
  }
  // Modification
  if(isset($_POST['modif'])){
   // print_r($_POST);
   // print_r($_FILES);
    $zone = htmlentities($_POST['zone']);
    $nom = htmlentities($_POST['nom']);
    $prenom = htmlentities($_POST['prenom']);
    $num_compteur = htmlentities($_POST['num_cpt']);
    $num_cpt_bank = htmlentities($_POST['num_cpt_bank']);
    $num_identite=htmlentities($_POST['num_identite']);
    $adresse = htmlentities($_POST['adresse']);
    $email=htmlentities($_POST['email']);
    $tel=htmlentities($_POST['tel']);
    if(!empty($_FILES["image"]["name"])){
      //echo "image";
      $uploadfile="C://xampp/htdocs/TP3/imgs/clients/".$_FILES["image"]["name"];
      move_uploaded_file($_FILES['image']['tmp_name'],$uploadfile);
      $image= "imgs/clients/".$_FILES["image"]["name"];
     }
     else{
      $image= htmlentities($_POST['fich']);
     }
      //  ZONE
      $idZ_Old = $DB->prepare("SELECT id_zone FROM client WHERE id_client=?");
      $idZ_Old->execute(array($id_cli));
      $id_val = $idZ_Old->fetch()['id_zone'];
       // modift en BD
       $modif = $DB->prepare("update client set id_zone=?,nom=?,prenom=?,image=?,Adresse=?,num_compteur=?,compte_bancaire=?,num_identite=?,email=?,tel=? where id_client=?");
       $modif->execute(array($zone,$nom,$prenom,$image,$adresse,$num_compteur,$num_cpt_bank,$num_identite,$email,$tel,$id_cli));
      if($id_val!=$zone){
        $zones = $DB->prepare('select * from zones where id_zone=?');
        $zones->execute(array($id_val));
        $nb=$zones->fetch()['nb_clients'];
        $modif = $DB->prepare("update zones set nb_clients=? where id_zone=?");
        $modif->execute(array($nb-1,$id_val));

        // modif en BD ZONE
        $zones2 = $DB->prepare('select * from zones where id_zone=?');
        $zones2->execute(array($zone));
        $nb2=$zones2->fetch()['nb_clients'];
        $modif2 = $DB->prepare("update zones set nb_clients=? where id_zone=?");
        $modif2->execute(array($nb2+1,$zone));
      }
      
      
      
    
    
      
     // header('Location: clients.php');
    
  }
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once('header.php'); ?>
</head>
<body>
<?php include_once('style.php'); ?>
    <div class="client">
        <div class="row justify-content-between">
        <?php include_once('nav.php'); ?>
          <fieldset class="border border-dark p-2 ms-5  col-4 mb-0">
            <legend class="float-none w-auto p-2 fw-bolder  main-title">Dashboard  <i class="p-2 fa-solid fa-chart-line fa-lg"></i></legend>
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
               <?php if($consom->rowCount()!=0){ ?>
                <div class="">
               <table class="table table-dark text-center me-2" id="t">
                   <thead  class="td-text">
                       <tr>
                         <th scope="col">Année</th>
                         <th scope="col">Consommation Annuelle</th>
                       </tr>
                  </thead>
                  <tbody>
                  <?php while($row = $consom->fetch()){
                    $annee=$row['annee'];
                    $val_cpt=$row['val_cpt'];
                    $req = $DB->prepare('select val_cpt from  consommation_annuelles where annee=? and id_client=?');
                    $req->execute(array($annee-1,$id_cli));
                    if($req->rowCount()!=0){
                      $val = $req->fetch()['val_cpt'];
                    }else{ 
                      $val=0;
                    }
                 ?>
               <tr>
               <td><?php echo $annee; ?></td>
               <td> <?php echo $val_cpt-$val; ?> </td>
               </tr>
                <?php } ?>
                  </tbody>
               </table>
               </div>
               <?php } ?>

                 
          </fieldset>
            <!-- info -->
        <fieldset class="border border-dark p-2  col-7 me-2 mb-0">
                <legend class="float-none w-auto p-2 fw-bolder main-title">Profile</legend>
                <div class="row justify-content-around align-items-center">
                   <div class="col-3">
                    <img src="<?php echo $image;?>" alt="" class="w-100">
                   </div>
                   <div class="col-1"></div>
                  <div class="col-8 row justify-content-between">
                  <p class="fw-bold fz-4 mb-4 col-5 textInfo">
                     N° Client:<span class="fw-bold px-3" style="color:white;" ><?php echo $id_cli ;?></span> 
                        <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 40%; height: 10%; background-color: #f83858;"></span>

                    </p>
                    <p class="fw-bold fz-4 mb-4 col-7 textInfo">
                    N° Zone :<span class="fw-bold px-3" style="color:white;" ><?php echo $zone;?></span> 
                        <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 30%; height: 10%; background-color: #f83858;"></span>

                    </p>
                     <p class="fw-bold fz-4 mb-4 col-5 textInfo">
                     Nom:<span class="fw-bold px-3" style="color:white;" ><?php echo $nom ;?></span> 
                        <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 40%; height: 10%; background-color: #f83858;"></span>

                    </p>
                    <p class="fw-bold fz-4 mb-4 col-7 textInfo">
                      Numéro du compteur: <span class="fw-bold px-3" style="color:white;" ><?php echo $num_compteur;?></span>
                      <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
                   </p>
                  <p class="fw-bold fz-4 mb-4 col-5 textInfo">
            
                   PRENOM: <span class="fw-bold px-3" style="color:white;" ><?php echo $prenom;?></span> 
                   <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 50%; height: 10%; background-color: #f83858;"></span>
                  </p>
              <p class="fw-bold fz-4 mb-4 col-7 textInfo">
                 Email: <span class="fw-bold px-3" style="color:white;" ><?php echo $email;?></span>
                 <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
              </p>
              <p class="fw-bold fz-4 mb-4 col-5 textInfo">
                 Adresse: <span class="fw-bold px-3" style="color:white;" ><?php echo $adresse;?></span>
                 <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
              </p>
              <p class="fw-bold fz-4 mb-4 col-7 textInfo">
                 Numéro carte d'identité: <span class="fw-bold px-3" style="color:white;" ><?php echo $num_identite;?></span>
                 <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
              </p>
              <p class="fw-bold fz-4 mb-4 col-5 textInfo">
            
                Tel:<span class="fw-bold px-3" style="color:white;" ><?php echo $tel;?></span> 
                <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 50%; height: 10%; background-color: #f83858;"></span>
              </p>
              
              <p class="fw-bold fz-4 mb-4 col-7 textInfo">
                 Numéro compte bancaire: <span class="fw-bold px-3" style="color:white;" ><?php echo $num_cpt_bank;?></span>
                 <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
              </p>
            </div>
        </div>
        <?php if($_SESSION['type']=="Admin"){
          ?>
           <!-- Modal -->
           <a class="btn main-btn" data-bs-toggle="modal" data-bs-target="#ex">Modifier</a>
                <div class="modal fade" id="ex" tabindex="-1" aria-labelledby="exLabel" aria-hidden="true">
                   <div class="modal-dialog ">
                     <div class="modal-content" style="width:700px;">
                       <div class="modal-header">
                         <h5 class="modal-title main-title" id="exLabel">Client</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body">
                       <form action=""  id="form" method="post" enctype="multipart/form-data">
                        <div class="row justify-content-between align-items-center">
                        <div class="input-group  mb-3 col-4 w-50">
                               <span class="input-group-text" >Nom</span>
                               <input type="text" class="form-control"  name="nom"  value="<?php echo $nom; ?>"/>
                              </div>
                             <div class="input-group  mb-3 col-4 w-50">
                               <span class="input-group-text" >Prénom</span>
                               <input type="text" class="form-control"  name="prenom" value="<?php echo $prenom; ?>" />
                              </div>
                              <div class="input-group  mb-3 col-4 w-50">
                               <span class="input-group-text" >Email</span>
                               <input type="email" class="form-control"  name="email" value="<?php echo $email; ?>" />
                              </div>
                              <div class="input-group  mb-3 col-4 w-50">
                               <span class="input-group-text" >Tel</span>
                               <input type="text" class="form-control"  name="tel" value="<?php echo $tel; ?>" />
                              </div>
                              <div class="input-group  mb-3 col-4 w-50">
                               <span class="input-group-text" >N° Compteur</span>
                               <input type="text" class="form-control"  name="num_cpt" value="<?php echo $num_compteur ; ?>"/>
                              </div>
                              <div class="input-group  mb-3 col-4 w-50">
                               <span class="input-group-text" >N° compte bancaire</span>
                               <input type="text" class="form-control"  name="num_cpt_bank" value="<?php echo $num_cpt_bank; ?>" />
                              </div>
                              <div class="input-group  mb-3 col-4 w-50">
                               <span class="input-group-text" >N° Identité</span>
                               <input type="text" class="form-control"  name="num_identite" value="<?php echo $num_identite; ?>" />
                              </div>
                              <div class="input-group  mb-3 col-4 w-50">
                               <span class="input-group-text" >Adresse</span>
                               <input type="text" class="form-control"  name="adresse" value="<?php echo $adresse; ?>" />
                              </div>
                              <div class=" mb-3 col-4 w-50 fw-bolder " >
                                  <label for="formFile" class="main-title ml-3">Image <i class="fas fa-images"></i></label>
                                  <input class="form-control d-none" id="formFile" type="file"  name="image"  accept="image/*">
                                  <input type="hidden"  name="fich" value="<?php echo $image; ?>" />  
                                </div>
                              <select class="form-select f-w-bold main-btn w-50 col-4 mb-3"  name="zone" val="<?php echo $zone; ?>">
                                <option selected disabled value="">Zone Géométrique du client</option>
                                <?php  
                                while($row = $zonesC->fetch()){
                                ?>
                                <option value="<?php  echo $row['id_zone'];?>"><?php  echo $row['nom_zone'];?> </option>
                                <?php  }
                                ?>
                              
                               </select>
                            <div class="modal-footer">
                                <button type="submit" name="modif" class="btn main-btn" >Modifier</button>
                            </div>
                        </div>
                   
                       </form>
                       </div>
 
                     </div>
                   </div>
                 </div>
                 <!-- fin model -->
        
        <?php }?>
                
    </fieldset>
  </div>
</div>
<script>
  const selectElement = document.querySelector('select');
  const val = selectElement.getAttribute('val');
  for (const option of selectElement.options) {
  if (option.value == parseInt(val)) {
    option.selected = true;
    break;
  }
}
$(document).ready(function() {
  $('#t').DataTable({
    searching: false,  // enable search bar
   lengthMenu: [1],  // set number of lines per page
    pageLength: 1  // set default number of lines per page
  });
});
</script>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/all.min.js"></script>
</body>
</html>