<?php
session_start();
include_once('connexion.php');
session_unset();
session_destroy();
connexionDB::disconnect();
header('Location: index.php');
?>