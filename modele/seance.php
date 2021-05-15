<?php

class Seance {
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

	public function getIdCreatedSeance($idUser) {

		$sql = "SELECT id";
		$sql .= " FROM seance";
		$sql .= " WHERE id_User = :idUser";
		$sql .= " ORDER BY id DESC";
		$sql .= " LIMIT 1";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->execute();

		return $requete->fetch(PDO::FETCH_ASSOC);
	}

	public function getSeanceUser($idUser) {

		$sql = "SELECT *";
		$sql .= " FROM seance";
		$sql .= " WHERE id_User = :idUser";
		$sql .= " ORDER BY date DESC, heureDebut DESC";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->execute();

		return $requete->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getDetailsSeance($id, $idUser) {

		$sql = "SELECT *";
		$sql .= " FROM seance";
		$sql .= " WHERE id_User = :idUser AND id = :id";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->bindParam(':id', $id, PDO::PARAM_INT);
		$requete->execute();

		return $requete->fetch(PDO::FETCH_ASSOC);
	}

	public function insertSeance($idUser, $date) {

		$sql = "SELECT id";
		$sql .= " FROM seance";
		$sql .= " WHERE id_User = :idUser";
		$sql .= " ORDER BY id DESC";
		$sql .= " LIMIT 1";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->execute();

		$id = $requete->fetch(PDO::FETCH_ASSOC);

		if($id != null)
		{
			$id = $id["id"] + 1;
		}
		else
		{
			$id = 1;
		}

		$sql = "INSERT INTO seance (id, id_User, date)";
		$sql .= " VALUES (:id, :idUser, :date)";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':id', $id, PDO::PARAM_INT);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->bindParam(':date', $date, PDO::PARAM_STR);

		return $requete->execute();
	}

	public function deleteSeance($id, $idUser) {

		$sql = "DELETE FROM composer";
		$sql .= " WHERE id = :id AND id_User = :idUser;";
		$sql .= " DELETE FROM seance";
		$sql .= " WHERE id = :id AND id_User = :idUser";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':id', $id, PDO::PARAM_INT);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);

		return $requete->execute();
	}

	public function modifySeance($id, $idUser, $date, $heureDebut, $heureFin) {

		$sql = "UPDATE seance";
		$sql .= " SET date = :date, heureDebut = :heureDebut, heureFin = :heureFin";
		$sql .= " WHERE id = :id AND id_User = :idUser";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':id', $id, PDO::PARAM_INT);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);
		$requete->bindParam(':date', $date, PDO::PARAM_STR);
		$requete->bindParam(':heureDebut', $heureDebut, PDO::PARAM_STR);
		$requete->bindParam(':heureFin', $heureFin, PDO::PARAM_STR);

		return $requete->execute();
	}
}