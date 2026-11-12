<?php
session_start(); // Démarrage de la session
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// TODO: Implémenter la classe d'authentification myAuthClass
// En attendant, on autorise tous les accès
$authorized = true;

// Décommenter ces lignes quand myAuthClass.php sera créé:
// require_once(dirname(__FILE__) . '/../class/myAuthClass.php');
// $authorized = myAuthClass::is_auth($_SESSION);
// if (!$authorized) {
//     include dirname(__FILE__).'/../login.php';
//     exit(1);
// }
