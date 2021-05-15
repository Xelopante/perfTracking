<?php

// Test de connexion à la base
$config = parse_ini_file("config.ini");
try {
	$pdo = new \PDO("mysql:host=".$config["host"].";dbname=".$config["database"].";charset=utf8", $config["user"], $config["password"]);
}
catch(Exception $e) {
	echo "<h1>Erreur de connexion à la base de données :</h1>";
	echo $e->getMessage();
	exit;
}

// Chargement des fichiers MVC
require("controleur/controleur.php");
require("vue/vue.php");
require("modele/user.php");
require("modele/seance.php");
require("modele/exercice.php");
require("modele/muscle.php");

//Routes
if(isset($_COOKIE["user"]))
{
    if(isset($_GET["action"]))
    {
        switch($_GET["action"])
        {
            case "deconnexion":
                (new Controleur)->deconnexion();
            break;

            case "seances":
                (new Controleur)->seances();
            break;

            case "seance":
                (new Controleur)->seance();
            break;

            case "ajouterSeance":
                (new Controleur)->ajouterSeance();
            break;

            case "supprimerSeance":
                (new Controleur)->supprimerSeance();
            break;

            case "modifierPoids":
                (new Controleur)->modifierPoids();
            break;

            case "supprimerExercice":
                (new Controleur)->supprimerExercice();
            break;

            case "statistiques":
                (new Controleur)->statistiques();
            break;

            default:
                (new Controleur)->erreur404();
            break;
        }
    }
    else
    {
        (new Controleur)->seances();
    }
}
else
{
    if(isset($_GET["action"]))
    {
        switch($_GET["action"])
        {
            case "connexion":
                (new Controleur)->connexion();
            break;

            case "inscription":
                (new Controleur)->inscription();
            break;

            default:
                (new Controleur)->erreur404();
            break;
        }
    }
    else
    {
        (new Controleur)->connexion();
    }
}