<?php
session_start();
if(!isset($_SESSION['id'])){
    header('Location: index.php');
}

  include_once('connexion.php');
  if(isset($_POST['ajout'])){

    if(!empty($_FILES["fich"]["name"])){
        $uploadfile="C://xampp/htdocs/TP3/fichiersAnnees/".$_FILES["fich"]["name"];
        move_uploaded_file($_FILES['fich']['tmp_name'],$uploadfile);
        $fich= "fichiersAnnees/".$_FILES["fich"]["name"];
       }
      if(file_exists($fich) && is_readable($fich)){
        $file = fopen($fich, "r");
        while (($line = fgets($file)) !== false) {
         // Split the line into an array of words
         $info = explode(" ", $line);
         $id_client = $info[0];
         $consom_Annee = $info[1];
         $annee = $info[2];
         $id_zone = $info[3];
         $date_saisie = $info[4];
       //  print_r($info);
         //store consom annee de chaque client
       $req2 = $DB->prepare('INSERT INTO  consommation_annuelles(id_client,annee,val_cpt) values(?,?,?)');
         $req2->execute(array($id_client,$annee,$consom_Annee));
         
     }
     //echo "ok";
     // Close the file
     fclose($file);
     $req = $DB->prepare('INSERT INTO  fichiers_consom_annee(id_agent,fich_consom_annee,annee) values(?,?,?)');
    $req->execute(array($_SESSION['id'],$fich,$annee));
   // echo "bien";
   
     }
    // echo $_SERVER['HTTP_REFERER'];
}
header('Location: '.$_SERVER['HTTP_REFERER']);

//
?>