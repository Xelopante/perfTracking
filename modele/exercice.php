<?php

class Exercice {
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

	public function getExerciceByMuscle($idMuscle) {

		$sql = "SELECT exercice.id, exercice.nom, exercice.description";
		$sql .= " FROM exercice";
		$sql .= " INNER JOIN travailler ON travailler.id_Exercice = exercice.id";
		$sql .= " WHERE travailler.id = :idMuscle";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':idMuscle', $idMuscle, PDO::PARAM_INT);
		$requete->execute();

		return $requete->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getExerciceById($id) {

		$sql = "SELECT *";
		$sql .= " FROM exercice";
		$sql .= " WHERE id = :id";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':id', $id, PDO::PARAM_INT);
		$requete->execute();

		return $requete->fetch(PDO::FETCH_ASSOC);
	}

	public function getAllExercice() {

		$sql = "SELECT *";
		$sql .= " FROM exercice";

		$requete = $this->pdo->prepare($sql);
		$requete->execute();

		return $requete->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getExerciceSeance($idSeance, $idUser) {

		$sql = "SELECT *";
		$sql .= " FROM exercice";
		$sql .= " INNER JOIN composer ON composer.id_Exercice = exercice.id";
		$sql .= " WHERE composer.id = :idSeance AND id_User = :idUser";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':idSeance', $idSeance, PDO::PARAM_INT);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->execute();

		return $requete->fetchAll(PDO::FETCH_ASSOC);
	}

	public function modifyPoidsExerciceSeance($poids, $idUser, $idExercice, $idSeance) {

		$sql = "UPDATE composer";
		$sql .= " SET poidsSouleve = :poids";
		$sql .= " WHERE id_User = :idUser AND id = :idSeance AND id_Exercice = :idExercice";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':idSeance', $idSeance, PDO::PARAM_INT);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->bindParam(':poids', $poids, PDO::PARAM_INT);
		$requete->bindParam(':idExercice', $idExercice, PDO::PARAM_INT);

		return $requete->execute();
	}

	public function supprimerExercice($idUser, $idSeance, $idExercice) {

		$sql = "DELETE FROM composer";
		$sql .= " WHERE id_User = :idUser AND id = :idSeance AND id_Exercice = :idExercice";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':idSeance', $idSeance, PDO::PARAM_INT);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->bindParam(':idExercice', $idExercice, PDO::PARAM_INT);

		return $requete->execute();
	}

	public function addExerciceSeance($idUser, $idSeance, $idExercice) {

		$sql = " INSERT INTO composer (id_User, id, id_Exercice)";
		$sql .= " VALUES (:idUser, :idSeance, :idExercice)";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':idSeance', $idSeance, PDO::PARAM_INT);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->bindParam(':idExercice', $idExercice, PDO::PARAM_INT);

		return $requete->execute();
	}

    public function getPoidsExerciceIntervalle($dateDebut, $dateFin, $idExercice, $idUser) {

        $sql = "SELECT *";
        $sql .= " FROM composer";
        $sql .= " INNER JOIN seance ON composer.id = seance.id";
        $sql .= " WHERE composer.id_User = :idUser AND composer.id_Exercice = :idExercice AND seance.date BETWEEN :dateDebut AND :dateFin";
        $sql .= " ORDER BY seance.date ASC";

        $requete = $this->pdo->prepare($sql);
        $requete->bindParam(':dateDebut', $dateDebut, PDO::PARAM_STR);
        $requete->bindParam(':dateFin', $dateFin, PDO::PARAM_STR);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->bindParam(':idExercice', $idExercice, PDO::PARAM_INT);

        $requete->execute();

        return  $requete->fetchAll(PDO::FETCH_ASSOC);
    }
}