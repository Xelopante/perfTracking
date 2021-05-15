<?php

class Muscle {
    private $pdo;

    //Connexion BDD
    public function __construct() {
		$config = parse_ini_file("config.ini");

		try {
			$this->pdo = new \PDO("mysql:host=".$config["host"].";dbname=".$config["database"].";charset=utf8", $config["user"], $config["password"]);
		} catch(Exception $e) {
			echo $e->getMessage();
		}
    }

    public function getAllMuscles() {

        $sql = "SELECT *";
        $sql .= " FROM muscle";

        $requete = $this->pdo->prepare($sql);
        $requete->execute();

        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}