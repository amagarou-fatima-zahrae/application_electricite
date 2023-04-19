<?php
session_start();
if(!isset($_SESSION['id'])){
    header('Location: index.php');
}
try{
  echo "fourn";
include_once('connexion.php');
  echo "oui";
  if (isset($_POST['ajouter'])){
    echo "da5el";
    print_r($_POST);
    $annee = htmlentities(trim($_POST['annee']));
    $mois = htmlentities(trim($_POST['mois']));
    $val_compteur = htmlentities(trim($_POST['val_compteur']));
    $date_saisie = date('Y-m-d');
        // cas modification par admin
        $valid = 0;
        if(isset($_POST['id_client'])){
          echo "kifax";
            $id_client = $_POST['id_client'];
            $id_consom = $_POST['id_consom'];
            $valid = 1;
        }
        // cas ajout par client
        else{
          echo "client";
         $id_client = $_SESSION['id'];
         if(!empty($_FILES["image_cpt"]["name"])){
             $uploadfile="C://xampp/htdocs/TP3/imgs/compteurs/".$_FILES["image_cpt"]["name"];
             move_uploaded_file($_FILES['image_cpt']['tmp_name'],$uploadfile);
             $image_cpt= "imgs/compteurs/".$_FILES["image_cpt"]["name"];
         }
        }
   
    if($mois!=1){
   echo 'mois diff de 1';
    $record_mois_precedent = $DB->prepare('select val_compteur from consommations where id_client=? and mois=? and 
    annee=?');
    $record_mois_precedent->execute(array($id_client,$mois-1,$annee));
   
    if($record_mois_precedent->rowCount()!=0){
        $val_compteur_moisAvant = $record_mois_precedent->fetch()['val_compteur'];
    }
    else{
        $val_compteur_moisAvant = 0;
    }
    //echo $val_compteur_moisAvant;
     $consom_mois = $val_compteur-$val_compteur_moisAvant;
     //echo $consom_mois;
     // CAS consommation NORMAL => facturé
     if(($consom_mois>=50 && $consom_mois<=400) || ($valid==1)){
       //echo 'hi';
        if(!isset($_POST['id_client'])){
             //consommation
          $req = $DB->prepare('INSERT INTO consommations(id_client,val_compteur,photo_cpt,date_saisie,mois,annee,is_Facture)
           values(?,?,?,?,?,?,?)');
         $req->execute(array($_SESSION['id'],$val_compteur,$image_cpt,$date_saisie,$mois,$annee,"facturé"));
         // id consommation
         $req2 = $DB->prepare('select max(id_consom) as id from consommations');
         $req2->execute(array());
         $id_consom = $req2->fetch()['id'];
        }else{
            // update consom
            $req = $DB->prepare('update consommations set val_compteur=?,is_Facture=? where id_consom=?');
            $req->execute(array($val_compteur,"facturé",$id_consom));
        }
       $date_fact = $date_saisie;
       $date_echeance = date('Y-m-d', strtotime($date_fact . ' +1 week'));

       if($consom_mois<=100){
         $prix_ht = $consom_mois*0.91;
       }
       elseif($consom_mois>=101 && $consom_mois>=200){
        $prix_ht = $consom_mois*1.01;
       }
       else{
        $prix_ht=$consom_mois*1.12;
       }
       $prix_ttc = $prix_ht*(1+0.14);
       $status = "impayé";
       // INSERT FACTURE
         $req3= $DB->prepare('INSERT INTO factures(id_client,id_consom,date_fact,date_echeance,consom_mois,prix_ht,
          prix_ttc,status) values(?,?,?,?,?,?,?,?)');
         $req3->execute(array($id_client,$id_consom,$date_fact,$date_echeance,$consom_mois,$prix_ht,$prix_ttc,$status));
     }
     // CAS consommation NON NORMAL =>NON facturé
     else{
      echo "him";
          if(!isset($_POST['id_client'])){
            echo "iwa";
            //consommation
            $req = $DB->prepare('INSERT INTO consommations(id_client,val_compteur,photo_cpt,date_saisie,mois,annee,is_Facture)
            values(?,?,?,?,?,?,?)');
            $req->execute(array($_SESSION['id'],$val_compteur,$image_cpt,$date_saisie,$mois,$annee,"non facturé"));
       } 
     }
         
    }
    // début d'année
    else{
        echo 'mois 1';
         /////////////////////////////////////
        $req4 = $DB->prepare('select val_compteur from consommations where id_client=? and annee=? and mois=?');
       $req4->execute(array($id_client,$annee-1,12)); 
      
       if( $req4->rowCount()!=0){
        $val1 =  $req4->fetch()['val_compteur'];
       }
      else{
        $val1 = 0;
       }
       //echo $val1;
      // $req5 = $DB->prepare('select val_compteur from consommations where id_client=? and annee=? and mois=?');
      // $req5->execute(array($id_client,$annee-2,12));
       //if( $req5->rowCount()!=0){
      //  $val2 =  $req5->fetch()['val_compteur'];
     //  }
    ///  else{
    //    $val2 = 0;
    //   } 
      //  echo $val2;
      //  $consom_annuelle = $val1-$val2;
      //  echo $consom_annuelle;
       $req5 = $DB->prepare('select val_cpt from consommation_annuelles  where id_client=? and annee=?');
       $req5->execute(array($id_client,$annee-1)); 
       if( $req5->rowCount()!=0){
        $val_reelle = $req5->fetch()['val_cpt'];
       }
      else{
        $val_reelle = 0;
       } 
      // echo 'consom_annuelle_reelle '.$val_reelle;
       $difference = $val_reelle-$val1;
       echo 'diff '.$difference;
       $consom_mois = $val_compteur-$val_reelle;
       echo 'consom mois '.$consom_mois;
         // CAS consommation NORMAL => facturé
         if(($consom_mois>=50 && $consom_mois<=400) || ($valid==1)){
            if($difference>0 && $difference>100){
                $consom_mois+=$difference;
            }elseif($difference<0){
                $consom_mois-=(-$difference);
            }
            if(!isset($_POST['id_client'])){
                 //consommation
              $req = $DB->prepare('INSERT INTO consommations(id_client,val_compteur,photo_cpt,date_saisie,mois,annee,is_Facture)
               values(?,?,?,?,?,?,?)');
             $req->execute(array($_SESSION['id'],$val_compteur,$image_cpt,$date_saisie,$mois,$annee,"facturé"));
             // id consommation
             $req2 = $DB->prepare('select max(id_consom) as id from consommations');
             $req2->execute(array());
             $id_consom = $req2->fetch()['id'];
            }else{
                // update consom
                $req = $DB->prepare('update consommations set val_compteur=?,is_Facture=? where id_consom=?');
                $req->execute(array($val_compteur,"facturé",$id_consom));
            }
           $date_fact = $date_saisie;
           $date_echeance = date('Y-m-d', strtotime($date_fact . ' +1 week'));
    
           if($consom_mois<=100){
             $prix_ht = $consom_mois*0.91;
           }
           elseif($consom_mois>=101 && $consom_mois>=200){
            $prix_ht = $consom_mois*1.01;
           }
           else{
            $prix_ht=$consom_mois*1.12;
           }
           $prix_ttc = $prix_ht*(1+0.14);
           $status = "impayé";
           // INSERT FACTURE
             $req3= $DB->prepare('INSERT INTO factures(id_client,id_consom,date_fact,date_echeance,consom_mois,prix_ht,
              prix_ttc,status) values(?,?,?,?,?,?,?,?)');
             $req3->execute(array($id_client,$id_consom,$date_fact,$date_echeance,$consom_mois,$prix_ht,$prix_ttc,$status));
         }
         // CAS consommation NON NORMAL =>NON facturé
         else{
              if(!isset($_POST['id_client'])){
                //consommation
                $req = $DB->prepare('INSERT INTO consommations(id_client,val_compteur,photo_cpt,date_saisie,mois,annee,is_Facture)
                values(?,?,?,?,?,?,?)');
                $req->execute(array($_SESSION['id'],$val_compteur,$image_cpt,$date_saisie,$mois,$annee,"non facturé"));
           } 
         }
        
    }
    
  }
  header('Location: consommations.php');
}catch(PDOException $e){
  die('Erreur : ' . $e->getMessage());
}
 
?>