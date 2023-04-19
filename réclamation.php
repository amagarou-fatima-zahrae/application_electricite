<?php 
session_start();
if(!isset($_SESSION['id'])){
    header('Location: index.php');
}
  include('connexion.php');

  if(isset($_GET['id']) && !empty($_GET['id'])){
    $reclam=$DB->prepare("SELECT * FROM reclamations WHERE id_reclam=? ");
    $reclam->execute(array($_GET['id']));
    if($reclam->rowCount()>0){
       $reclam_info=$reclam->fetch();
       $sujet = $reclam_info['sujet'];
       $contenu = $reclam_info['contenu'];
       $client=$DB->prepare("SELECT * FROM client WHERE id_client=? ");
       $client->execute(array($_SESSION['id']));
       if($client->rowCount()>0){
       $client_info=$client->fetch();
       $nom_complet = $client_info['nom']. " ".$client_info['prenom'];
       $image = $client_info['image'];
       $adresse =  $client_info['Adresse'];
    }
    }
  }?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réclamation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="reclamation">
        <div class="container">
        <fieldset class="border border-dark p-2 mb-4">
                <legend class="float-none w-auto p-2 fw-bolder">Informations</legend>
                <div class="row justify-content-around align-items-center ">
                   <div class="col-5">
                    <img src="<?php echo $image;?>" alt="" class="w-50">
                   </div>
                   <div class="col-3">
                   <span class="fw-bold px-3" style="color:#0D47A1;" ><?php echo $nom_complet ;?></span> 
                   </div>
                   <div class="col-4">
                    <button  type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dal2">Réponses</button>
                </div>
                </div>
                
            <div class="">
              <p class="fw-bold fz-4 my-2">
              Sujet:<span class="fw-bold px-3 my-2" style="color:#0D47A1;" ><?php echo $sujet ;?></span> 
              </p>

              <p class="fw-bold fz-4 my-2">
              Contenu: <span class="fw-bold px-3 my-3" style="color:#0D47A1;" ><?php echo $contenu;?></span> 
              </p>
                 
            </div>
            <button class="btn btn-success mx-4"  data-bs-toggle="modal" data-bs-target="#3">Répondre</button>
             <!-- Modal 3 -->
             <div class="modal fade" id="3" tabindex="-1" aria-labelledby="33" aria-hidden="true">
                   <div class="modal-dialog">
                     <div class="modal-content">
                       <div class="modal-header">
                         <h5 class="modal-title" id="33"></h5>
                         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body">
                       <form action="reponse.php?id_recl=<?php echo  $row['id_reclam'];?>"  id="form" method="post" enctype="multipart/form-data">
                              <div class="input-group mb-3">
                               <span class="input-group-text" >Réponse</span>
                               <textarea class="form-control"  name="reponse" required ></textarea>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" name="answer" class="btn btn-primary">Répondre</button>
                              </div>
                         </form>
                       </div>
 
                     </div>
                   </div>
                 </div>
                 <!-- fin model 3 -->
                 <!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ok">
  Launch demo modal
</button>

<!-- Modal 3-->
<div class="modal fade" id="ok" tabindex="-1" aria-labelledby="333" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="333">Réclamation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form action="reponse.php?id_recl=<?php echo  $row['id_reclam'];?>"  id="form" method="post" enctype="multipart/form-data">
                              <div class="input-group mb-3">
                               <span class="input-group-text" >Réponse</span>
                               <textarea class="form-control"  name="reponse" required ></textarea>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" name="answer" class="btn btn-primary">Répondre</button>
                              </div>
                         </form>
      </div>
    </div>
  </div>
</div>

<!-- //////// -->
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dal">
  Responses<span class="badge bg-secondary">2</span>
</button>

<!-- Modal2 -->
<div class="modal fade" id="dal" tabindex="-1" aria-labelledby="dalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dalLabel">Modal title</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
  <!-- fin modal 2 -->
    </fieldset>
     
  </div>
 </div>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" ></script>
  </body>
  </html>