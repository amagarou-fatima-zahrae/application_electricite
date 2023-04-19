     <?php 
      include_once('connexion.php');
       $zones = $DB->prepare('select * from zones');
       $zones->execute(array());
       if(isset($_POST['ajoutZ_A'])){
        echo "ana ";
        print_r($_POST);
        $nom = htmlentities($_POST['nom']);
        $surface = htmlentities($_POST['surface']);
        $nb_cli =0;
        $adresse = htmlentities($_POST['adresse']);
        $email = htmlentities($_POST['email']);
        $tel = htmlentities($_POST['tel']);
        $password = time();
        $login = $email;
        //ajout
        //=>ZONE
        $zone = $DB->prepare('INSERT INTO zones(surface,nom_zone,nb_clients) values(?,?,?)');
        $zone->execute(array($surface,$nom,$nb_cli));
        $id = $DB->prepare('select max(id_zone) as id_zone from zones');
        $id->execute(array());
        $id_zone = $id->fetch()['id_zone'];
        echo "id = ".$id_zone;
        //=>Agent
        $agent = $DB->prepare('INSERT INTO agent(adresse,login,mot_passe,id_zone,email,tel) values(?,?,?,?,?,?)');
        $agent->execute(array($adresse,$login,$password,$id_zone,$email,$tel));
        header('Location: zone.php?id='.$id_zone);
       }

       //
       if($_SESSION['type']=="Admin"){
        $href="AdminDashboard.php";
       }
       elseif($_SESSION['type']=="Agent"){
        $href="zone.php?id=".$_SESSION['id'];
       }
       else{
        $href="client.php?id=".$_SESSION['id'];
       }
     ?>
     <div class="canva my-3" >
               <button class="btn" type="button" style="background-color:#f83858;" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
               <i class="fa-sharp fa-solid fa-bars fa-lg" style="color:#fcc402;"></i>
               </button>

               <div class="offcanvas offcanvas-start" style="width:300px;" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
                 <div class="offcanvas-header">
                   <h5 class="offcanvas-title text-danger fw-bolder fs-2" id="offcanvasExampleLabel">Menu</h5>
                   <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                 </div>
                 <div class="offcanvas-body">
                 <div><a class="btn main-btn rounded-pill f-w-bold mb-4" href="<?php echo $href; ?>"><i class="fa-solid fa-user fa-lg"></i></a></div>
                 <?php if($_SESSION['type']=="Admin"){ ?>
                  <div class="dropdown my-3">
                      <button class="btn main-btn rounded-pill f-w-bold dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                        ZONES
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li> 
                          <button  type="button" class="btn main-btn rounded-pill f-w-bold mx-2 btn-sm" data-bs-toggle="modal" data-bs-target="#k"><i class="fas fa-plus-circle ps-2"></i> Zone avec un Agent</button>
                        </li>
                      <?php 
                         while($row= $zones->fetch()){
                          ?>
                         <li><a class="dropdown-item" href="zone.php?id=<?php echo $row['id_zone'];  ?>">  <?php  echo $row['nom_zone'];  ?></a></li>
                          <?php 
                         }
                         ?>
                      </ul>
                    </div>
                       <?php 
                       }  ?>
                   
                    <div class="my-3">
                       <div><a class="btn main-btn rounded-pill f-w-bold mb-4" href="index.php">Accueil</a></div>
                       <div><a class="btn main-btn rounded-pill f-w-bold mb-4" href="factures.php">Liste des factures</a></div>
                       <?php if(($_SESSION['type']=="Admin") || ($_SESSION['type']=="Agent")){ ?>
                        <div><a class="btn main-btn rounded-pill f-w-bold mb-4" href="clients.php">Liste des clients</a></div>
                       <?php 
                       }  ?>
                       <div><a class="btn main-btn rounded-pill f-w-bold mb-4" href="listeRéclamations.php">liste des réclamations</a></div>
                       <?php if(($_SESSION['type']=="Admin") || ($_SESSION['type']=="Client")){ ?>
                        <div><a class="btn main-btn rounded-pill f-w-bold mb-4" href="consommations.php">liste des consommations</a></div>
                       <?php 
                       }  ?>
                    </div>
                  </div>
                </div>
            </div>

                      <!-- Modal Ajout zone -->
 <div class="modal fade" id="k" tabindex="-1" aria-labelledby="kLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title main-title" id="kLabel">ZONE</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
               <form action=""  id="form" method="post" enctype="multipart/form-data" onsubmit="return validate();">
                        
                            <div class="input-group mb-3">
                               <span class="input-group-text" >Nom Zone</span>
                               <input type="text" class="form-control"  name="nom" />
                            </div>
                             <div class="input-group mb-3">
                               <span class="input-group-text" >Surface</span>
                               <input type="text" class="form-control"  name="surface"/>
                              </div>
                              <div class="input-group mb-3">
                               <span class="input-group-text" >Adresse</span>
                               <input type="text" class="form-control"  name="adresse"  />
                              </div>
                              <div class="input-group mb-3">
                               <span class="input-group-text" >Email</span>
                               <input type="email" class="form-control"  name="email" />
                              </div>
                              <div class="input-group mb-3">
                               <span class="input-group-text" >Tel</span>
                               <input type="text" class="form-control"  name="tel"  />
                              </div>
                              <div class="modal-footer">
                                <button type="submit" name="ajoutZ_A" class="btn main-btn" >Ajouter</button>
                              </div>       
                </form>
            </div>
    </div>
  </div>
</div>
    <!-- fin modal zone -->