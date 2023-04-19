<?php 
session_start();
if(!isset($_SESSION['id'])){
    header('Location: index.php');
}
  include_once('connexion.php');
  if(isset($_GET['id']) && !empty($_GET['id'])){
    $id_zone= $_GET['id'];
    $zone = $DB->prepare('select * from zones where id_zone=?');
    $zone->execute(array($id_zone));

    $agent=$DB->prepare("SELECT * FROM agent WHERE id_zone=? ");
    $agent->execute(array($id_zone));
   
    if($zone->rowCount()>0){
        $zone_info=$zone->fetch();
        $agent_info = $agent->fetch();
       $nom = $zone_info['nom_zone'];
       $surface = $zone_info['surface'];
       $nb_clients =$zone_info['nb_clients'];
       // agent
       $id_agent = $agent_info['id_agent'];
       $adresse = $agent_info['adresse'];
       $email = $agent_info['email'];
       $tel = $agent_info['tel'];
       if($_SESSION['type']=="Admin"){
        $fichs = $DB->prepare('select * from  fichiers_consom_annee where id_agent=?');
        $fichs->execute(array($id_agent));
      }
    }
    //Dashboard
    $facturesIMP = $DB->prepare('select count(*) as nb from factures where status=? and id_client in(select 
    id_client from client where id_zone=?)');
    $facturesIMP->execute(array("impayé",$id_zone));
    $impaidInvoices = $facturesIMP->fetch()['nb'];
    $factures = $DB->prepare('select count(*) as total from factures where id_client in(select 
    id_client from client where id_zone=?)');
    $factures->execute(array($id_zone));
    $totalInvoices = $factures->fetch()['total'];
    if($totalInvoices==0){
      $ratio=1;
    }
    else{
    $ratio = $impaidInvoices / $totalInvoices;
    }

    $prix = $DB->prepare('select sum(prix_ttc) as prixIMP from factures where status=? and id_client in(select 
    id_client from client where id_zone=?)');
    $prix->execute(array("impayé",$id_zone));
    $prixIMP = number_format($prix->fetch()['prixIMP'],2, '.', '');
    $prixT= $DB->prepare('select sum(prix_ttc) as totalP from factures where id_client in(select 
    id_client from client where id_zone=?)');
    $prixT->execute(array($id_zone));
    $prixToatl =number_format($prixT->fetch()['totalP'],2, '.', '') ;
   // echo $prixToatl;
    
    if($prixToatl==0){
      $ratio2=1;
    }
    else{
      $ratio2 = $prixIMP / $prixToatl;
    }
    $consom = $DB->prepare('select annee,sum(val_cpt) as somme from consommation_annuelles  where id_client in(select 
    id_client from client where id_zone=?)  group by annee');
    $consom->execute(array($id_zone));
  }
 
  // Modification
  if(isset($_POST['zone'])){

    $nom = htmlentities($_POST['nom']);
    $surface = htmlentities($_POST['surface']);
    $nb_cli = htmlentities($_POST['nb_cli']);

     // modif en BD
     $modif = $DB->prepare("update zones set nom_zone=?,surface=?,nb_clients=? where id_zone=?");
    $modif->execute(array($nom,$surface,$nb_cli,$id_zone));
      //
      header('Location: zone.php?id='.$id_zone);
    
  }
  // Modification
  if(isset($_POST['agent'])){

    $adresse = htmlentities($_POST['adresse']);
    $email = htmlentities($_POST['email']);
    $tel = htmlentities($_POST['tel']);

     // modif en BD
     $modif = $DB->prepare("update agent set adresse=?,email=?,tel=? where id_zone=?");
     $modif->execute(array($adresse,$email,$tel,$id_zone));
     header('Location: zone.php?id='.$id_zone);
  }
   
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('header.php'); ?>
</head>
<body>
<?php include_once('style.php'); ?>
    <div class="zone ">
        <?php include_once('nav.php'); ?>
        <div class="container mx-5">
            <!-- info -->
           <div class="row">
            <div class="col-6">
            <fieldset class="border border-dark p-2 ms-5 mb-0">
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
               <table class="table table-dark  text-center me-2" id="ag">
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
                    $req = $DB->prepare('select sum(val_cpt) as somme from  consommation_annuelles where annee=? and id_client in(select 
                    id_client from client where id_zone=?)');
                    $req->execute(array($annee-1,$id_zone));
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
               <?php } ?>

                 
          </fieldset>
            </div>
            <div class="col-6">
            <fieldset class="border border-dark p-2 mb-4">
                <legend class="float-none w-auto p-2 fw-bolder main-title">ZONE </legend>
                <div >
                   <div class=" row justify-content-around align-items-center ms-4">
                   <p class="fw-bold fz-4 mb-4 col-5   textInfo">
                     Numéro Zone:<span class="fw-bold px-3"style="color:white;" ><?php echo $id_zone ;?></span> 
                      <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
                 </p>
                   <p class="fw-bold fz-4 mb-4 col-7  textInfo">
                     Nom Zone:<span class="fw-bold px-3"style="color:white;" ><?php echo $nom ;?></span> 
                      <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
                 </p>
              <p class="fw-bold fz-4 mb-4 col-5  textInfo">
                 surface: <span class="fw-bold px-3"style="color:white;" ><?php echo $surface;?></span>
                  <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
              </p>
              <p class="fw-bold fz-4 mb-4 col-7  textInfo">
            
              Nombre Clients: <span class="fw-bold px-3"style="color:white;" ><?php echo $nb_clients;?></span> 
               <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
              </p>
             <br>
              <hr class="border-top main-btn border-4 border-dashed w-75 me-3">

              <p class="fw-bold fz-4 mb-4 col-5  textInfo">
                 N° Agent: <span class="fw-bold px-3"style="color:white;" ><?php echo $id_agent;?></span>
                  <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
              </p>
              <p class="fw-bold fz-4 mb-4 col-7  textInfo">
                 Adresse: <span class="fw-bold px-3"style="color:white;" ><?php echo $adresse;?></span>
                  <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 90%; height: 10%; background-color: #f83858;"></span>
              </p>
              <p class="fw-bold fz-4 mb-4 col-5  textInfo">
            
                Tel:<span class="fw-bold px-3"style="color:white;" ><?php echo $tel;?></span> 
                 <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 70%; height: 10%; background-color: #f83858;"></span>
              </p>
              <p class="fw-bold fz-4 mb-4 col-7  textInfo">
                 Email: <span class="fw-bold px-3"style="color:white;" ><?php echo $email;?></span>
                 <span style="position: absolute; top: 0; left: 5px; width: 2px; height: 80%; background-color: #f83858;"></span>
                        <span style="position: absolute; bottom: 0; left: 5px; width: 90%; height: 10%; background-color: #f83858;"></span>
              </p>
              <button  type="button" class="btn main-btn col-4" data-bs-toggle="modal" data-bs-target="#el">Modifier Agent</button>
              <button  type="button" class="btn main-btn col-4" data-bs-toggle="modal" data-bs-target="#e">Modifer Zone</button>
          <!-- Modal zone -->
 <div class="modal fade" id="e" tabindex="-1" aria-labelledby="eLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title main-title" id="eLabel">ZONE</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
               <form action=""  id="form" method="post" enctype="multipart/form-data" onsubmit="return validate();">
                        
                            <div class="input-group mb-3">
                               <span class="input-group-text" >Nom Zone</span>
                               <input type="text" class="form-control"  name="nom" value="<?php echo $nom;?>" />
                            </div>
                             <div class="input-group mb-3">
                               <span class="input-group-text" >Surface</span>
                               <input type="text" class="form-control"  name="surface" value="<?php echo $surface;?>" />
                              </div>
                              <div class="input-group mb-3">
                               <span class="input-group-text" >Nombre Clients</span>
                               <input type="number" class="form-control"  name="nb_cli" value="<?php echo $nb_clients;?>" />
                              </div>
                              <div class="modal-footer">
                                <button type="submit" name="zone" class="btn main-btn" >Modifier</button>
                              </div>       
                </form>
            </div>
    </div>
  </div>
</div>
    <!-- fin modal zone -->
             <!-- Modal agent -->
 <div class="modal fade" id="el" tabindex="-1" aria-labelledby="elLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title main-title" id="elLabel">Agent</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form action=""  id="form" method="post" enctype="multipart/form-data" onsubmit="return validate();"> 
                        <div class="input-group mb-3">
                           <span class="input-group-text" >Adresse</span>
                           <input type="text" class="form-control"  name="adresse" value="<?php echo $adresse;?>" />
                        </div>
                         <div class="input-group mb-3">
                           <span class="input-group-text" >Email</span>
                           <input type="email" class="form-control"  name="email" value="<?php echo $email;?>" />
                          </div>
                          <div class="input-group mb-3">
                           <span class="input-group-text" >Tel</span>
                           <input type="text" class="form-control"  name="tel" value="<?php echo $tel;?>" />
                          </div>
                          <div class="modal-footer">
                            <button type="submit" name="agent" class="btn main-btn" >Modifier</button>
                          </div>       
            </form>
      </div>
    </div>
  </div>
</div>
    <!-- fin modal agent -->

            </div>
        </div>
    </fieldset>
  <!-- FICHIER -->
    <?php
    if($_SESSION['type']=="Admin"){
       ?>
       <div class="d-flex justify-content-center" id="conteneur">
         <?php if($fichs->rowCount()!=0){
          ?>
            <select class="form-select f-w-bold main-btn w-50 mx-4" id="year">
                                <option selected disabled value="">L'Année du fichier annuelle </option>
                                <?php  
                                while($rows = $fichs->fetch()){
                                ?>
                                <option value="<?php  echo $rows['fich_consom_annee'];?>"><?php  echo $rows['annee'];?> </option>
                                <?php  }
                                ?>
                              
          </select>
       <a class="btn main-btn d-none" id="download-button" download><i class="fa-solid fa-download fa-lg px-2"></i>Fichier Consommation annuelle</a>
          <?php
         }
         ?> 
       </div>
       <?php
    }else{
      ?>
      <div class="my-3 d-flex justify-content-center">
          <a class="btn main-btn" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fa-solid fa-file-import fa-lg px-2"></i>Fichier Consommation annuelle</a>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog">
                     <div class="modal-content">
                       <div class="modal-header">
                         <h5 class="modal-title main-title" id="exampleModalLabel">Consommations annuelles</h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body">
                       <form action="fichierTraitement.php"  id="form" method="post" enctype="multipart/form-data" >
                              <div class="mb-3 fw-bolder main-title" >
                                  <label for="formFile ">Fichier Annuelle<i class="fas fa-file-upload mx-2" style="color:#fcc402;"></i></label>
                                  <input class="form-control" id="formFile" type="file"  name="fich" required>
                              </div>
                            
                            <div class="modal-footer">
                                <button type="submit" name="ajout" class="btn main-btn" >Importer</button>
                            </div>
          
                       </form>
                       </div>
 
                     </div>
                   </div>
                 </div>
                 <!-- fin model -->
          </div>
      <?php
    } ?>
    
          <!-- fichier -->
            </div>
           </div>
  </div>
</div>
<script>
  $(document).ready(function() {
  $('#ag').DataTable({
    searching: true,  // enable search bar
   lengthMenu: [1],  // set number of lines per page
    pageLength: 1  // set default number of lines per page
  });
});
  //
 const downloadButton = document.getElementById('download-button');
 const div = document.getElementById('conteneur');
 //console.log(div);
 const yearS  = document.getElementById('year');
 //console.log(yearS);
yearS.addEventListener('change', () => {
  const selectedYear = yearS.value;
  //console.log(selectedYear);
  if(downloadButton.classList.contains('d-none')){
    downloadButton.classList.remove('d-none');
  }
  downloadButton.href=selectedYear;
});
//

</script>
<?php include_once('footer.php'); ?>
</body>
</html>