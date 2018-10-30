<?php

session_start();

$dsn = "mysql:host=localhost; dbname=eshop";
$log = "root";
$pwd = "";
$attributes = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

$pdo = new PDO($dsn, $log, $pwd, $attributes);

//> Déclaration des variables
$msg = "";
$contenu = "";
$page = (!empty($page)) ? $page : "Eshop.com";
$seo_description = (!empty($seo_description)) ? $seo_description : "";
# Ici, nous mettons toutes les chances de notre côté pour éviter d'afficher des erreurs. Nous déclarons donc nos variables, mais si $page et $seo sont déclarés avant l'appel de l'init.php, nous faisons en sorte de conserver les valeurs précédemment définies.

//> Déclaration de constantes
define('RACINE', $_SERVER['DOCUMENT_ROOT'] . '/Back/PHP/eshop-master/');
define('URL', "http://localhost/Back/PHP/eshop-master/");
# Je me facilite la vie en déclarant en constante le chemin vers mes fichiers ainsi que l'URL de mon site. Si jamais j'appelle ce chemin en dur ou l'URL en dur dans mon site, je n'aurais plus besoin d'aller dans tous mes fichiers modifier les valeurs. Tout ce travail sera contenu dans les constantes déclarées ci-dessus