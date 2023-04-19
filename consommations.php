<?php
session_start();
if(!isset($_SESSION['id']) || !isset($_SESSION['type'])){
    header('Location: index.php');
}
  include_once('connexion.php');
  // Si Client
  if($_SESSION['type']=="Client"){
    $records  = $DB->prepare('select * from consommations where id_client=?');
    $records->execute(array($_SESSION['id']));
  $consommations = $DB->prepare('select * from consommations where id_client=?');
  $consommations->execute(array($_SESSION['id']));
  $req =  $DB->prepare('select date_inscription from client where id_client=?');
  $req->execute(array($_SESSION['id']));
  $year_inscp = $req->fetch()['date_inscription'];
  
  $max_year=  $DB->prepare('select max(annee) as max_a FROM consommations where id_client=?');
  $max_year->execute(array($_SESSION['id']));
  $year = $max_year->fetch()['max_a'];

  $mois_last= $DB->prepare('SELECT max(mois) as max_m FROM consommations WHERE annee=? and id_client=?');
  $mois_last->execute(array($year,$_SESSION['id']));
  $mois = $mois_last->fetch()['max_m'];

  if($year!=""){
    if($mois==12){
     $year++;
     $mois=1;
    }
    else{
      $mois++;
    }
  }
  else{
   // echo "hi";
    $year=date('Y',strtotime($year_inscp));
    $mois=1;
  }

  $records = $records->fetchAll();
  $json_records = json_encode($consommations->fetchAll());
  //echo $consommations->fetchAll();
//echo $json_records;
  }

  // SI Admin
  elseif($_SESSION['type']=="Admin"){
    $records  = $DB->prepare('select * from consommations where is_Facture=?');
    $records->execute(array("non facturé"));
  
  }
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('header.php'); ?>
</head>
<body>
<?php include_once('style.php'); ?>
    <div class="consoms" style="margin-top: 0px;">
    <?php include_once('nav.php'); ?>
        <div class="container">
        <div class="row justify-content-between align-items-center">
            <div class="col-2 mb-3">
              <?php
              if($_SESSION['type']=="Client"){ ?>
                <a class="btn main-btn" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fas fa-plus-circle ps-2"></i> Consommation</a>
               <?php
              }
              ?>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog">
                     <div class="modal-content">
                       <div class="modal-header">
                         <h5 class="modal-title main-title" id="exampleModalLabel">Consommation Mensuelle</h5>
                         <button type="button" class="btn-close main-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <div class="modal-body">
                       <form action="consommationDB.php"  id="form" method="post" enctype="multipart/form-data">
                            <div class="input-group mb-3">
                               <span class="input-group-text" >Année</span>
                               <input class="form-control"  id="year-input" type="text"  readonly value="<?php echo $year; ?>" name="annee">
                             </div>
                             <div class="input-group  mb-3">
                               <span class="input-group-text" >Mois</span>
                               <input class="form-control"  type="text" value="<?php echo $mois; ?>" readonly
                                name="mois">
                             </div>
                             <!-- <select class="form-select f-w-bold mb-3 d-none" id="mois" name="mois">
                             <option selected disabled value="">Mois</option>
                            </select> -->
                             <div class="input-group mb-3">
                               <span class="input-group-text" >Valeur de compteur</span>
                               <input class="form-control"  type="number" step="1" required   name="val_compteur">
                             </div>
                             <div class="mb-3 fw-bolder " >
                                  <label class="main-title" for="formFile">Image de compteur<i class="fas fa-images"></i></label>
                                  <input class="form-control d-none" id="formFile" type="file"  name="image_cpt"  accept="image/*" required>
                              </div>
                         <!-- <span  class="mb-4" id="error" style="color: red; display: none;">Please select a user type.</span> -->
                         <!-- <br>
                              -->
                              <div class="modal-footer">
                                <button type="submit" name="ajouter" class="btn main-btn" >Ajouter</button>
                              </div>
                       </form>
                       </div>
 
                     </div>
                   </div>
                 </div>
                 <!-- fin model -->
            </div>
            </div>
        <table class="table table-dark my-3 text-center" id="tab">
  <thead  class="td-text">
    <tr>
      <th scope="col">N° Consommation</th>
     <?php  if($_SESSION['type']=="Admin"){
      ?>
      <th scope="col">N° Client</th>
      <?php
     }
     ?>
      <th scope="col">Valeur du compteur</th>
      <th scope="col">image du compteur</th>
      <th scope="col">Date Saisie</th>
      <th scope="col">Mois</th>
      <th scope="col">Année</th>
      <?php
      if($_SESSION['type']=="Admin"){ ?>
      <th scope="col">Modifier</th>
               <?php
              }
           ?>
    </tr>
  </thead>
  <tbody>
   <?php
   foreach ($records as $row){
    $mois=$row['mois'];
    $id_consom=$row['id_consom'];
    $id_client = $row['id_client'];
    $val_compteur=$row['val_compteur'];
    $image=$row['photo_cpt'];
    $annee=$row['annee'];
    ?>
    <tr>
       <td><?php  echo $id_consom ;?></td>
       <?php  if($_SESSION['type']=="Admin"){
      ?>
      <td><?php  echo  $id_client;?></td>
      <?php
     }
     ?>
       
       <td><?php  echo $val_compteur ;?></td>
       <td>
<!-- Modal IMG -->
<button type="button" class="main-title bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#img<?php echo $id_consom;?>">
<i class="fas fa-images fa-lg"></i>
</button>
<div class="modal fade" id="img<?php  echo $id_consom;?>" tabindex="-1" aria-labelledby="imgLabel<?php  echo $id_consom;?>" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="btn-close main-btn" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  <div class="container my-2 ">
         <div class="row align-items-center justify-content-center">
            <div class="col-8">
                <p class="fw-bold fz-4 my-2">
                       <img src="<?php echo $image;?>" alt="" class="w-100">
                </p>
            </div>
  </div>
</div>  
</div>
</div>
</div>
</div>
<!-- fin modal IMG -->
</td>
       <td><?php  echo $row['date_saisie'] ;?></td>
       <td><?php  echo $mois ;?></td>
       <td><?php  echo  $annee;?></td>
       <?php
       if($_SESSION['type']=="Admin"){ ?>
             <td> <!-- Modal2 -->
      <button type="button" class="main-title bg-transparent border-0" data-bs-toggle="modal" data-bs-target="#dol<?php echo $id_consom;?>">
      <i class="fas fa-eye fa-lg"></i>
     </button>
  <div class="modal fade" id="dol<?php  echo $id_consom;?>" tabindex="-1" aria-labelledby="dolLabel<?php  echo $id_consom;?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title main-title" id="imgLabel<?php  echo $id_consom;?>">Consommation mensuelle<i class="fas fa-bolt px-3 fa-lg" style="color:#fcc402;;"></i></h5>
        <button type="button" class="btn-close main-btn" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container my-2 ">
               <div class="row align-items-center justify-content-center">
                  <div class="col-8">
                      <p class="fw-bold fz-4 my-2">
                             <img src="<?php echo $image;?>" alt="" class="w-100">
                      </p>
                  </div>
            
            
              <form action="consommationDB.php"  id="form" method="post" enctype="multipart/form-data"> 
                <div class=" row justify-content-center">
                  <div class="input-group mb-3 col-4 w-50">
                   <span class="input-group-text" >Mois</span>
                   <input type="text" class="form-control"  value="<?php echo $mois;?>" name="mois" readonly>
                   </div>
                <div class="input-group mb-3 col-4 w-50">
                   <span class="input-group-text" >Année</span>
                   <input  type="text" class="form-control"  value="<?php echo $annee;?>" name="annee" readonly>
                </div> 
                 <div class="input-group mb-3 col-4 w-60">
                   <span class="input-group-text" >Valeur de compteur</span>
                   <input type="number" class="form-control"  step="1" required value="<?php echo $val_compteur;?>" name="val_compteur">
                   <input  type="hidden"   value="<?php echo $id_consom;?>" name="id_consom">
                   <input  type="hidden"   value="<?php echo $id_client;?>" name="id_client">
                </div>
                <div class="">
                   <button type="submit" name="ajouter" class="btn main-btn" >Valider pour générer la facture</button>
                 </div>
              </div>
          </form>
        </div>
     </div>  
      </div>
    </div>
  </div>
</div>
  <!-- fin modal 2 --></td>
               <?php
              }
           ?>
    </tr>
  
 <?php 
  }  
   ?>
  </tbody>
  <p id="consoms" class="d-none"> <?php
   echo $json_records;
    ?></p>
</table>
</div>
</div>
<script>
 $(document).ready(function() {
  $('#tab').DataTable({
    searching: true,  // enable search bar
    lengthMenu: [5, 10, 15],  // set number of lines per page
    pageLength: 5  // set default number of lines per page
  });
});

// year && month code
// var recordsConsom = JSON.parse(document.getElementById('consoms').textContent);
// console.log(recordsConsom);
// var yearInput = document.getElementById('year-input');
// var date_year = yearInput.getAttribute('val');
// var parts = date_year.split('-'); // split the date string into an array of parts
// var year_inscp = new Date(parts[0], parts[1] - 1, parts[2]).getFullYear();
//  // console.log(year_inscp);
// // listen for changes to the year dropdown
//  yearInput.addEventListener('change', function() {
//   // YEAR
//  // console.log(yearInput.value);
//   var year = parseInt(yearInput.value);
//   var selectedYear =yearInput.value;
//   //console.log(selectedYear);
//   var currentYear = new Date().getFullYear();
//   var monthSelect = document.getElementById('mois');
//   if (isNaN(year) || year < year_inscp || year > currentYear) {
//     yearInput.setCustomValidity("Please enter a valid year.");
//     yearInput.value="";
//     if(!monthSelect.classList.contains("d-none")){
//       monthSelect.classList.add('d-none');
//     }
//   } else {
//     yearInput.setCustomValidity("");

//     //mois
    
//  //console.log(selectedYear);
//    var allMonths = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
  
//   // just the records of year selected
//  var selectedYearRecords = recordsConsom.filter(record => record.annee === selectedYear);
//  console.log(selectedYearRecords);
//  //months
//  var existingMonths=selectedYearRecords.map(record => parseInt(record.mois));
 
//  console.log(existingMonths);
//  //   // get the months for which there are no consumption records
//    var availableMonths = allMonths.filter(month => !existingMonths.includes(month));
//    console.log(availableMonths);
//  //   // get the month dropdown element
   
//         monthSelect.classList.remove('d-none');
//  //   // clear the existing options
//  //   monthDropdown.innerHTML = '';
   
//  //   // add an option for each available month
//  var mois_input = document.getElementById('mois_input');
//  console.log(mois_input);
//  mois_input.value = Math.min(...availableMonths);
//    //   availableMonths.forEach(month => {
//    //      var option = document.createElement('option');
//    //      option.text = month;
//    //      option.value=month;
//    //      monthSelect.add(option);
//    // });
//   }

// });
</script>
<?php include_once('footer.php'); ?>
</body>
</html>