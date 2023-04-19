<?php 
session_start();
if(!isset($_SESSION['id'])){
    header('Location: index.php');
}
  include('connexion.php');
  
  if (isset($_POST['answer'])){
    $reponse = htmlentities(trim($_POST['reponse']));
    $date = date('Y-m-d H:i:s',time());
    if(isset($_GET['id_recl']) && !empty($_GET['id_recl'])  && !empty($reponse)){
        $id_reclam =$_GET['id_recl'];
        $statement=$DB->prepare("INSERT INTO reponses(id_reclam,contenu,date) VALUES (?,?,?)");
        $statement->execute(array($id_reclam,$reponse,$date));
        echo "hi";
        $update =$DB->prepare("update reclamations set statut=? where id_reclam=?");
        $update->execute(array("Répondu",$id_reclam));
        header('Location: listeRéclamations.php');
        echo "no";
    }
  }
?>