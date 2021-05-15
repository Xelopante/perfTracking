<?php

class User {
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

	public function getIdByToken($token) {

		$sql = "SELECT idUser";
		$sql .= " FROM authentification";
		$sql .= " WHERE token = :token";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':token', $token, PDO::PARAM_STR);
		$requete->execute();

		return $requete->fetch(PDO::FETCH_ASSOC);
	}

	public function verifyIP($token, $IP) {

		$sql = "SELECT COUNT(*) AS compte";
		$sql .= " FROM authentification";
		$sql .= " WHERE token = :token AND ip = :ip";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':ip', $IP, PDO::PARAM_STR);
		$requete->bindParam(':token', $token, PDO::PARAM_STR);
		$requete->execute();

		return $requete->fetch(PDO::FETCH_ASSOC);
	}

	public function connexion($login, $mdp, $IP, $token) {

		$sql = "SELECT id, mdp";
		$sql .= " FROM user";
		$sql .= " WHERE login = :login";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':login', $login, PDO::PARAM_STR);
		$requete->execute();

		$resultat = $requete->fetch(PDO::FETCH_ASSOC);

		if($resultat != null)
		{
			if(password_verify($mdp, $resultat["mdp"]))
			{
				//Suppression de l'ancienne authentification avec le même IP et même ID.
				$sql = "DELETE FROM authentification";
				$sql .= " WHERE idUser = :id AND ip = :ip";

				$requete = $this->pdo->prepare($sql);
				$requete->bindParam(':id', $resultat["id"], PDO::PARAM_INT);
				$requete->bindParam(':ip', $IP, PDO::PARAM_STR);

				if($requete->execute() == true)
				{
					//Insertion de la nouvelle authentification.
					$sql = "INSERT INTO authentification (idUser, ip, token)";
					$sql .= " VALUES (:id, :ip, :token)";

					$requete = $this->pdo->prepare($sql);
					$requete->bindParam(':id', $resultat["id"], PDO::PARAM_INT);
					$requete->bindParam(':ip', $IP, PDO::PARAM_STR);
					$requete->bindParam(':token', $token, PDO::PARAM_STR);

					return $requete->execute();
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	public function inscription($nom, $prenom, $login, $mdp) {

		$sql = "INSERT INTO user (nom, prenom, login, mdp)";
		$sql .= " VALUES (:nom, :prenom, :login, :mdp)";

		$password = password_hash($mdp, PASSWORD_DEFAULT);

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':nom', $nom, PDO::PARAM_STR);
		$requete->bindParam(':prenom', $prenom, PDO::PARAM_STR);
		$requete->bindParam(':login', $login, PDO::PARAM_STR);
		$requete->bindParam(':mdp', $password, PDO::PARAM_STR);

		return $requete->execute();
	}

	public function estDejaInscrit($login) {

        $sql = "SELECT COUNT(*) AS nombre";
		$sql .= " FROM user";
		$sql .= " WHERE login = :login";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':login', $login, PDO::PARAM_STR);
		$requete->execute();

		$resultat = $requete->fetch(PDO::FETCH_ASSOC);

		if($resultat["nombre"] == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
    }

	public function deconnexion($token, $IP) {

		$sql = "DELETE FROM authentification";
		$sql .= " WHERE token = :token AND ip = :ip";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':token', $token, PDO::PARAM_STR);
		$requete->bindParam(':ip', $IP, PDO::PARAM_STR);

		return $requete->execute();
	}

	public function deconnexionForcee($idUser) {

		$sql = "DELETE FROM authentification";
		$sql .= " WHERE id = :idUser";

		$requete = $this->pdo->prepare($sql);
		$requete->bindParam(':idUser', $idUser, PDO::PARAM_INT);

		return $requete->execute();
	}
}