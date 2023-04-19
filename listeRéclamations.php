<?php
session_start();
if(!isset($_SESSION['id'])){
    header('Location: index.php');
}

  include_once('connexion.php');
  if($_SESSION['type']=="Admin"){
    $reclamations = $DB->prepare('select * from reclamations');
    $reclamations->execute(array());
  } 
  elseif($_SESSION['type']=="Client"){
    $reclamations = $DB->prepare('select * from reclamations where id_client=?');
    $reclamations->execute(array($_SESSION['id']));
  }
  else{
    $reclamations = $DB->prepare('select * from reclamations where id_client
     in(select id_client from client where id_zone=(select id_zone from agent where id_agent=?))');
    $reclamations->execute(array($_SESSION['id']));
  }
 
  if(isset($_POST['ajout'])){
    if(!empty($_POST['sujet']) && !empty($_POST['contenu']) && !empty($_POST['type'])){
      $date=  $date = date('Y-m-d',time());
      $sujet=htmlentities($_POST['sujet']);
      $contenu=htmlentities($_POST['contenu']);
      $type=htmlentities($_POST['type']);
      $statut="pas de réponse";
      $id_client = $_SESSION['id'];
      $ajout = $DB->prepare("INSERT INTO  reclamations(sujet,contenu,date,statut,id_client,type) values(?,?,?,?,?,?)");
      $ajout->execute(array($sujet,$contenu,$date,$statut,$id_client,$type));
      header('Location: listeRéclamations.php');
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('header.php'); ?>
   
</head>
<body>
<?php include_once('style.php'); ?>
    <div class="recs" style="margin-top: 0px;">
    <?php include_once('nav.php'); ?>
        <div class="container">
          <div class="row justify-content-between align-items-center"> 
        <div class="col-4">
            <?php if($_SESSION['type']=="Client"){
            ?>
              <a class="btn main-btn mb-4" data-bs-toggle="modal" data-bs-target="#exampleModal">Ajouter une réclamation</a>
             <?php
             } ?>                 <!-- Modal 1-->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog">
                     <div class="modal-content">
                       <div class="modal-header">
                         <h5 class="modal-title main-title" id="exampleModalLabel">Réclamation</h5>
                         <button type="button" class="btn-close main-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body">
                       <form action="listeRéclamations.php"  id="form" method="post" enctype="multipart/form-data">
                        <select class="form-select f-w-bold main-btn w-50 col-4 mb-3" id="type" name="type">
                          <option selected disabled value="">Type Réclamation</option>
                          <option value="Facture">Facture</option>
                          <option value="Fuite interne">Fuite interne</option>
                          <option value="Fuite externe">Fuite externe</option>
                          <option value="Autre">Autre</option>
                         </select>
                         <span  class="mb-4" id="error" style="color: red; display: none;">Please select a user type.</span>
                         <br>
                             <div class="input-group mb-3">
                               <span class="input-group-text" >Sujet</span>
                               <input type="text" class="form-control"  name="sujet" required />
                              </div>
                             <div class="input-group mb-3">
                               <span class="input-group-text" >Contenu</span>
                               <textarea class="form-control"  name="contenu" required ></textarea>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" name="ajout" class="btn main-btn" >Ajouter</button>
                              </div>
                       </form>
                       </div>
 
                     </div>
                   </div>
                 </div>
                 <!-- fin model 1-->
            </div>
            </div>
        <table class="table table-dark text-center" id="recl">
  <thead class="td-text">
    <tr>
      <th scope="col">N° Réclamation</th>
      <th scope="col">N° Client</th>
      <th scope="col">Sujet</th>
      <th scope="col">Contenu</th>
      <th scope="col">Date</th>
      <th scope="col">Type</th>
      <th scope="col">statut</th>
      <?php if(($_SESSION['type']=="Admin")||($_SESSION['type']=="Agent")){
        ?>
        <th>répondre</th>
        <?php
      } ?> 
    </tr>
  </thead>
  <tbody>
   <?php
   while($row=$reclamations->fetch()){
    $reponses = $DB->prepare('select * from reponses where id_reclam=?');
    $reponses->execute(array($row['id_reclam']));
    ?>
    <tr>
       <td><?php  echo $row['id_reclam'];?></td>
       <td><?php  echo $row['id_client'] ;?></td>
       <td><?php  echo $row['sujet'] ;?></td>
       <td><?php  echo $row['contenu'] ;?></td>
       <td><?php  echo $row['date'] ;?></td>
       <td><?php  echo $row['type'] ;?></td>
       <?php if($row['statut']=="pas de réponse"){
        ?>
        <td><?php  echo $row['statut'] ;?></td>
        <?php
       }else{
        ?>
        <td>
          <!-- Modal2 -->
      <button type="button" class="main-title bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#dal<?php echo $row['id_reclam'];?>">
        Responses <br><span class="badge main-btn"><?php echo $reponses->rowCount();?></span>
     </button>
  <div class="modal fade" id="dal<?php  echo $row['id_reclam'];?>" tabindex="-1" aria-labelledby="dalLabel<?php  echo $row['id_reclam'];?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title main-title" id="dalLabel<?php  echo $row['id_reclam'];?>">Réponses<i class="ml-2 fa-regular fa-message"></i></h5>
        <button type="button" class="btn-close main-btn" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
                    <?php 
                           while($rows=$reponses->fetch()){
                               ?>
    
                              <div class="p-2 mb-2 text-dark rounded border border-success bg-light">
                               <?PHP echo $rows['contenu']; ?>
                              </div>
                            <?php 
                             }  
                      ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn main-btn" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>
  <!-- fin modal 2 -->
        </td>
        <?php 
      } 
      ?> 
       
       <?php if(($_SESSION['type']=="Admin")||($_SESSION['type']=="Agent")){
        ?>
        <td>
        <button type="button" class="btn main-btn" data-bs-toggle="modal" data-bs-target="#ok<?php echo $row['id_reclam'];?>">
              Répondre
        </button>
        <!-- Modal 3-->
     <div class="modal fade" id="ok<?php echo  $row['id_reclam'];?>" tabindex="-1" aria-labelledby="333<?php echo  $row['id_reclam'];?>" aria-hidden="true">
      <div class="modal-dialog">
       <div class="modal-content">
       <div class="modal-header">
        <h5 class="modal-title py-2 main-title" id="333<?php echo  $row['id_reclam'];?>">Réponse au Réclamation </h5>
        <button type="button" class="btn-close main-btn" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
                <div class="modal-body">
                    <form action="reponse.php?id_recl=<?php echo  $row['id_reclam'];?>"  id="form" method="post" enctype="multipart/form-data">
                              <div class="input-group mb-3">
                               <span class="input-group-text" >Réponse</span>
                               <textarea class="form-control"  name="reponse" required ></textarea>
                              </div>
                              <div class="modal-footer">
                                <button type="submit" name="answer" class="btn main-btn">Répondre</button>
                              </div>
                         </form>
      </div>
    </div>
  </div>
</div>
<!-- fin Modal 3-->
       </td>
        <?php
      } ?>  

    </tr>
  
 <?php 
  }  
   ?>
  </tbody>
</table>
</div>       
</div>
<script>
  $(document).ready(function() {
  $('#recl').DataTable({
    searching: true,  // enable search bar
    lengthMenu: [3],  // set number of lines per page
    pageLength: 3  // set default number of lines per page
  });
});
  var form = document.getElementById("form");
  console.log(form);
  form.addEventListener("submit", function(event) {
    var select = document.getElementById("type");
    var userType = select.options[select.selectedIndex].value;
    console.log(userType);
    if (userType==="") {
      var error = document.getElementById("error");
      error.style.display = "inline";
      event.preventDefault();
      return false;
    }
  });
</script>
<?php include_once('footer.php'); ?>
</body>
</html>