<?php
  session_start();
  if(!isset($_SESSION['id'])){
    header('Location: index.php');
  }
  include_once('connexion.php');
  if(($_SESSION['type']=="Admin")){
    $clients = $DB->prepare('select * from client');
    $clients->execute(array());
  }
  elseif(($_SESSION['type']=="Agent")){
    $clients = $DB->prepare('select * from client where id_client in(select id_client from client where
    id_zone=(select id_zone from agent where id_agent=?))');
    $clients->execute(array($_SESSION['id']));
  }
  $zonesListe = $DB->prepare('select * from zones');
  $zonesListe->execute(array());
  $email=false;
  if(isset($_POST['ajout'])){
      $id_zone = htmlentities($_POST['zone']);
      $nom = htmlentities($_POST['nom']);
      $prenom = htmlentities($_POST['prenom']);
      $date_inscrp = date('Y') . '-01-01';
      $num_cpt = htmlentities($_POST['num_cpt']);
      $num_cpt_bank = htmlentities($_POST['num_cpt_bank']);
      $num_identite=htmlentities($_POST['num_identite']);
      $adresse = htmlentities($_POST['adresse']);
      $time = time();
      $password =$time;
      $login = $nom."@".$prenom;
      //$hashed_password = password_hash($password, PASSWORD_BCRYPT);
      $email=htmlentities($_POST['email']);
      $tel=htmlentities($_POST['tel']);
      $image= "imgs/clients/default.jpeg";
      if(!empty($_FILES["image"]["name"])){
        $uploadfile="C://xampp/htdocs/TP3/imgs/clients/".$_FILES["image"]["name"];
        move_uploaded_file($_FILES['image']['tmp_name'],$uploadfile);
        $image= "imgs/clients/".$_FILES["image"]["name"];
       }
       //ajout en BD
      $ajout = $DB->prepare("INSERT INTO  client(id_zone,nom,prenom,image,login,mot_passe,Adresse,num_compteur,compte_bancaire,date_inscription,num_identite,email,tel) values(?,?,?,?,?,?,?,?,?,?,?,?,?)");
      $ajout->execute(array($id_zone,$nom,$prenom,$image,$login,$password,$adresse,$num_cpt,$num_cpt_bank,$date_inscrp,$num_identite,$email,$tel));
      
    //  modif en BD ZONE
     $zones = $DB->prepare('select * from zones where id_zone=?');
     $zones->execute(array($id_zone));
     $nb=$zones->fetch()['nb_clients'];
     $modif = $DB->prepare("update zones set nb_clients=? where id_zone=?");
     $modif->execute(array($nb+1,$id_zone));
      //email
     require('email.php');
     $mail->addAddress($email, $nom." ".$prenom);
     $mail->Subject = 'Mot de passe et login';
     $mail->Body = "Bonjour cher(e) client \n vous trouverez ci-dessous votre mot de passe et le login pour accéder à l'application\n";
     $mail->Body .=" mot de passe : ".$password;
     $mail->Body .=" \nlogin name : ".$login;
     $mail->send();
     $email=true;
        //
       // header('Location: clients.php');
      
    }
  
?><!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('header.php'); ?>
</head>
<body>
<?php include_once('style.php'); ?>
    <div class="clients">
    <?php include_once('nav.php'); ?>
        <div class="container">
        <div class="mb-4"><?php include_once('navbar.php'); ?></div>
        <div class="alert alert-danger w-50 mt-3 d-none" style="margin-left: 130px;" role="alert" id="alert" val="<?php echo $email;?>">
        <i class="fa-sharp fa-solid fa-envelope fa-lg" style="color:red;"></i> Email Envoyé au client avec succés<i class="fa-solid fa-thumbs-up main-title mx-2 fa-lg"></i>
        </div>
          <div class="my-3">
            <?php if($_SESSION['type']=="Admin"){
              ?>
          <a class="btn main-btn" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fas fa-user-plus fa-lg"></i></a>
              <?php
            }
            ?>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog modal-dialog-centered">
                     <div class="modal-content">
                       <div class="modal-header">
                         <h5 class="modal-title main-title" id="exampleModalLabel">Client</h5>
                         <button type="button" class="btn-close main-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body">
                       <form action=""  method="post" enctype="multipart/form-data" >
                          <div class="row justify-content-between align-items-center">
                              <div class="input-group mb-3  col-4 w-50">
                               <span class="input-group-text" >Nom</span>
                               <input type="text" class="form-control"  name="nom" required />
                              </div>
                             <div class="input-group mb-3  col-4 w-50 ">
                               <span class="input-group-text" >Prénom</span>
                               <input type="text" class="form-control"  name="prenom" required />
                              </div>
                              <div class="input-group mb-3  col-4 w-50">
                               <span class="input-group-text" >Email</span>
                               <input type="email" class="form-control"  name="email" required />
                              </div>
                              <div class="input-group mb-3  col-4 w-50">
                               <span class="input-group-text" >Tel</span>
                               <input type="text" class="form-control"  name="tel" required />
                              </div>
                              <div class="input-group mb-3  col-4 w-50">
                               <span class="input-group-text" >N° Compteur</span>
                               <input type="text" class="form-control"  name="num_cpt" required />
                              </div>
                              <div class="input-group mb-3  col-4 w-50">
                               <span class="input-group-text" >N° compte bancaire</span>
                               <input type="text" class="form-control"  name="num_cpt_bank" required />
                              </div>
                              <div class="input-group mb-3  col-4 w-50">
                               <span class="input-group-text" >N° Identité</span>
                               <input type="text" class="form-control"  name="num_identite" required />
                              </div>
                              <div class="input-group mb-3  col-4 w-50">
                               <span class="input-group-text" >Adresse</span>
                               <input type="text" class="form-control"  name="adresse" required />
                              </div>
                              <div class="mb-3 fw-bolder col-4" >
                                  <label for="formFile" class="main-title ml-3">Image <i class="fas fa-images fa-lg"></i></label>
                                  <input class="form-control d-none" id="formFile" type="file"  name="image"  accept="image/*">
                              </div>
                              <select class="form-select f-w-bold main-btn w-50 col-4 mb-3"  name="zone">
                                <option selected disabled value="">Zone Géométrique du client</option>
                                <?php  
                                while($rows = $zonesListe->fetch()){
                                ?>
                                <option value="<?php  echo $rows['id_zone'];?>"><?php  echo $rows['nom_zone'];?> </option>
                                <?php  }
                                ?>
                              
                               </select>
                            <div class="modal-footer">
                                <button type="submit" name="ajout" class="btn main-btn" >Ajouter</button>
                            </div>
                           </div>
                       </form>
                       </div>
 
                     </div>
                   </div>
                 </div>
                 <!-- fin model -->
          </div>
        <table class="table table-dark my-3 text-center" id="tabl">
  <thead class="td-text">
    <tr>
      <th scope="col">N° Client</th>
      <th scope="col">Nom</th>
      <th scope="col">Prénom</th>
      <th scope="col">N° Zone</th>
      <th scope="col">Adresse</th>
      <th scope="col">Email</th>
      <th scope="col">Tel</th>
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
    <?php
   while($row=$clients->fetch()){
    ?>
    <tr>
       <td><?php  echo $row['id_client'];?></td>
       <td><?php  echo $row['nom'] ;?></td>
       <td><?php  echo $row['prenom'] ;?></td>
       <td><?php  echo $row['id_zone'] ;?></td>
       <td><?php  echo $row['Adresse'] ;?></td>
       <td><?php  echo $row['email'] ;?></td>
       <td><?php  echo $row['tel'] ;?></td>
       <td> <a class="btn main-btn " href="client.php?id=<?php echo $row['id_client'] ;?>"><i class="fas fa-eye"></i></a></td>

    </tr>
 <?php 
  }   ?> 
</tbody>
</table>
    </div>
</div>
<script>
   var alert = document.getElementById('alert');
  var valid = alert.getAttribute('val');
  if(valid){
    alert.classList.remove('d-none');
  }
  setTimeout(function() {
    alert.classList.add('d-none');
}, 5000);
  //
   $(document).ready(function() {
  $('#tabl').DataTable({
    searching: true,  // enable search bar
    lengthMenu: [5, 10, 15],  // set number of lines per page
    pageLength: 5  // set default number of lines per page
  });
});
</script>
<?php include_once('footer.php'); ?>
</body>
</html>