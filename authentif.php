<?php
session_start();
if(isset($_GET['type']) && !empty($_GET['type'])){
  //echo $_GET['type'];
  $_SESSION['type'] = $_GET['type'];
}

if(isset($_POST['login']) && !empty($_POST['login'])){
 // echo "hi";
  $login = htmlspecialchars($_POST['login_name']);
  $password = htmlspecialchars($_POST['mot_passe']);
  if(isset($_SESSION['type']) && !empty($_SESSION['type'])){
    include_once('connexion.php');
    $valid=1;
    //Admin
    if($_SESSION['type']==="Admin"){
      $admin=$DB->prepare("SELECT * FROM fournisseurs WHERE login=? AND mot_passe=? LIMIT 1");
      $admin->execute(array($login,$password));
        if($admin->rowCount()>0){
            $_SESSION['login']=$login;
            $_SESSION['pass']=$password;
            $_SESSION['id']=$admin->fetch()['id_fourn'];
            //echo "    bonne connexion";
             header('Location: AdminDashboard.php'); 
           }else{
            $valid=0; 
           }
       //Client
    }elseif($_SESSION['type']==="Client"){
      $client=$DB->prepare("SELECT * FROM client WHERE login=? AND mot_passe=? LIMIT 1");
      $client->execute(array($login,$password));
        if($client->rowCount()>0){
            $_SESSION['login']=$login;
            $_SESSION['pass']=$password;
            $client_trouve=$client->fetch();
            $_SESSION['id']=$client_trouve['id_client'];
            //echo "    bonne connexion";
           
             header('Location: client.php?id='.$_SESSION['id']); 
           }else{
            $valid=0;
           }
    }else{
      $agent=$DB->prepare("SELECT * FROM agent WHERE login=? AND mot_passe=? LIMIT 1");
      $agent->execute(array($login,$password));
        if($agent->rowCount()>0){
            $_SESSION['login']=$login;
            $_SESSION['pass']=$password;
            $agent_trouve=$agent->fetch();
            $_SESSION['id']=$agent_trouve['id_agent'];
            $id_z = $agent_trouve['id_zone'];
            echo $id_z;
            //echo "    bonne connexion";
             header('Location: zone.php?id='.$id_z); 
           }else{
            $valid=0;
           }
    }
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

<div>
<div class="container">
<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
     <div class="alert alert-danger w-50 mt-3 d-none" style="margin-left: 130px;" role="alert" id="alert" val="<?php echo $valid;?>">
     <i class="fas fa-exclamation-triangle fa-lg" style="color:red;"></i> Informations incorrectes!!!
      </div>
      <div class="modal-header">
        <h5 class="modal-title main-title" id="myModalLabel">Authentification</h5>
      </div>
      <div class="modal-body">
      <div class="formulaire  mt-4 " id="form">
        <form action="" id="form" method="post" enctype="multipart/form-data">
              <div class="input-group mb-3">
                <span class="input-group-text" >Login Name</span>
                <input type="text" class="form-control"  name="login_name"  required>
               </div>
               <div class="input-group mb-3">
                <span class="input-group-text" >Mot de passe</span>
                <input type="password" class="form-control"  name="mot_passe" required >
               </div>
               <div class="modal-footer">
              <div class="mb-3">
                <input type="submit" value="login" class="btn main-btn mx-2" name="login">
              </div>
      </div>
        </form>
      </div>
      </div>
    </div>
  </div>
</div>

</div>
    </div>
</div>
<script>
  var alert = document.getElementById('alert');
  var valid = alert.getAttribute('val');
  if(valid=="0"){
    alert.classList.remove('d-none');
  }
  setTimeout(function() {
    alert.classList.add('d-none');
}, 5000);

  $(document).ready(function() {
    // show the login modal on page load
    $('#myModal').modal('show');
  });
  
</script>
<?php include_once('footer.php'); ?>
</body>
</html>