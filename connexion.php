<?php
// Declaration d'une nouvelle classe
class connexionDB {
    private static $host    = 'localhost';    // nom de l'host
    private static $name    = 'systeme_factures';     // nom de la base de donnee
    private static $user    = 'root';         // utilisateur
    private static $pass    = '';         // mot de passe
    private static $port    = '3306';
    private static $connexion=null;
 
    public static function connect() {
        if(self::$connexion==null){
            try{
                self::$connexion = new PDO('mysql:host=' . self::$host . ';dbname=' . self::$name . ';port=' . self::$port,
                self::$user, self::$pass, 
                array(PDO::MYSQL_ATTR_INIT_COMMAND =>'SET NAMES UTF8',PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
                }catch (PDOException $e){
                    die('Erreur : Impossible de se connecter  a la BDD !' . $e->getMessage());
               }
        }
    return self::$connexion;
  }
  public static function disconnect(){
    self::$connexion=null;
  }
}
 
// Faire une connexion a votre fonction
$DB =connexionDB::connect();
//date
date_default_timezone_set('Africa/Algiers');
?>