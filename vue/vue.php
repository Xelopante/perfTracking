<?php

class Vue {

    public function entete() {
        echo "
			<!DOCTYPE html>
			<html>
				<head>
					<meta charset='UTF-8'>
					<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">

					<link rel=\"stylesheet\" href=\"css/bootstrap.min.css\">
					<link rel=\"stylesheet\" href=\"css/custom.css\">

					<title>Perf Tracking</title>
				</head>
				<body>
                    <header class=\"header\">
				        <nav class=\"container-fluid\">
							<img class=\"logo\" src=\"images/logo_small.png\">
                            <input class=\"burger-btn\" type=\"checkbox\" id=\"burger-btn\" />
                            <label class=\"burger-icon\" for=\"burger-btn\"><span class=\"navline\"></span></label>
							<ul class=\"menu\">
							";

							if(isset($_COOKIE["user"]))
							{
								echo "
                                	<li><a href=\"index.php?action=seances\">Seances</a></li>
                                	<li><a href=\"index.php?action=statistiques\">Statistiques</a></li>
                                	<li><a href=\"index.php?action=deconnexion\">Déconnexion</a></li>
								";
							}
							else
							{
								echo "
									<li><a href=\"index.php?action=connexion\">Connexion</a></li>
                                	<li><a href=\"index.php?action=inscription\">Inscription</a></li>
								";
							}

							echo "
							</ul>
				        </nav>
                    </header>
			";
    }

    public function fin() {
		echo "
				</body>
			</html>
		";
	}

	public function erreur404() {
		$this->entete();

		echo "
					<div class=\"contenu\">
						<div class=\"erreur404\">
							<h1 class=\"text-center\">Erreur 404</h1>
							<p class=\"text-center\">Ressource introuvable<p>
						</div>
					</div>
		";

		$this->fin();
	}

    public function connexion($message = null) {
        $this->entete();

		echo "
					<div class=\"contenu\">
						<h1 class=\"text-center\">Connexion</h1>
						<form action=\"\" method=\"POST\" name=\"connexionForm\">
							<div class=\"input-group\">
								<div class=\"input-block\">
									<input type=\"text\" class=\"input-custom\" name=\"login\" placeholder=\"Nom d'utilisateur\"/>
								</div>
								<div class=\"input-block\">
									<input type=\"password\" class=\"input-custom\" name=\"password\" placeholder=\"Mot de passe\"/>
								</div>
							</div>
							<div class=\"input-group\">
								<input type=\"submit\" class=\"button-custom\" name=\"OKconnexion\" value=\"Valider\" />
							</div>
						</form>
						<p class=\"inscription-link\"><a href=\"index.php?action=inscription\">Pas encore membre ? Inscrivez-vous ici</a></p>
					</div>
		";

		echo $message;

        $this->fin();
    }

	public function inscription($message = null) {
		$this->entete();

		echo "
					<div class=\"contenu\">
						<h1 class=\"text-center\">Inscription</h1>
						<form action=\"\" method=\"POST\" name=\"inscriptionForm\">
							<div class=\"input-group\">
								<div class=\"input-block\">
									<input type=\"text\" class=\"input-custom\" name=\"nom\" placeholder=\"Nom\"/>
								</div>
								<div class=\"input-block\">
									<input type=\"text\" class=\"input-custom\" name=\"prenom\" placeholder=\"Prénom\"/>
								</div>
								<div class=\"input-block\">
									<input type=\"text\" class=\"input-custom\" name=\"login\" placeholder=\"Nom d'utilisateur\"/>
								</div>
								<div class=\"input-block\">
									<input type=\"password\" class=\"input-custom\" name=\"password\" placeholder=\"Mot de passe\"/>
								</div>
								<div class=\"input-block\">
									<input type=\"password\" class=\"input-custom\" name=\"password-verif\" placeholder=\"Retapez votre mot de passe\"/>
								</div>
							</div>
							<div class=\"input-group\">
								<input type=\"submit\" class=\"button-custom\" name=\"OKinscription\" value=\"Valider\" />
							</div>
						</form>
						<p class=\"inscription-link\"><a href=\"index.php?action=connexion\">Déjà membre ? Connectez-vous</a></p>
					</div>
		";

		echo $message;

		$this->fin();
	}

	public function seances($seances, $message = null) {
		$this->entete();

		echo "
					<div class=\"contenu\">
						<div class=\"creer-seance\">
							<a href=\"index.php?action=ajouterSeance\"><button class=\"button-custom-seance\" name=\"ajout-seance\">Ajouter une séance</button></a>
						</div>
			";

			if($seances != null)
			{
				//Réadaptation du format date et heure.
				foreach($seances as $seance)
				{
					if(isset($seance["heureDebut"]))
					{
						$heureDebutSplit = explode(':', $seance["heureDebut"]);
						$heureDebut = $heureDebutSplit[0]."h".$heureDebutSplit[1];
					}
					else
					{
						$heureDebut = "X";
					}

					if(isset($seance["heureFin"]))
					{
						$heureFinSplit = explode(':', $seance["heureFin"]);
						$heureFin = $heureFinSplit[0]."h".$heureFinSplit[1];
					}
					else
					{
						$heureFin = "X";
					}

					$dateSplit = explode('-', $seance["date"]);
					$date = $dateSplit[2]."/".$dateSplit[1]."/".$dateSplit[0];

					echo "
						<div class=\"seance-group\">
							<div class=\"seance-p\"><p>Date : ".$date."</p></div>
							<div class=\"seance-p\"><p>Heure de début : ".$heureDebut."</p></div>
							<div class=\"seance-p\"><p>Heure de fin : ".$heureFin."</p></div>
							<a href=\"index.php?action=seance&idSeance=".$seance["id"]."\"><button class=\"button-custom-tableau\">Consulter</button></a>
							<a href=\"index.php?action=supprimerSeance&idSeance=".$seance["id"]."\"><button class=\"button-custom-tableau\">Supprimer</button></a>
						</div>
					";

				}
			}

			echo "
					</div>
		";

		$this->fin();
	}

	public function seance($seance, $exercices, $exercicesSeance, $muscles) {
		$this->entete();

		echo "
					<div class=\"contenu\">
						<div class=\"seance\">
							<h1>Séance</h1>
						</div>

						<form action=\"\" method=\"POST\" name=\"modifyDateHeureSeance\" class=\"details-seance\">
							<div class=\"input-group-seance\">
								<h2>Date et heure</h2>
								<div class=\"group-date\">
								<label class=\"custom-label\">Date</label>
									<input type=\"date\" class=\"custom-datetime\" name=\"dateSeance\" value=\"".$seance["date"]."\"/>
								</div>
								<div class=\"group-time\">
									<label class=\"custom-label\">Heure de début</label>
									<input type=\"time\" class=\"custom-datetime\" name=\"heureDebutSeance\" value=\"".$seance["heureDebut"]."\"/>
									<label class=\"custom-label\">Heure de fin</label>
									<input type=\"time\" class=\"custom-datetime\" name=\"heureFinSeance\" value=\"".$seance["heureFin"]."\"/>
								</div>
								<div class=\"input-block\">
									<input type=\"submit\" class=\"button-custom-tableau\" name=\"OKDateHeure\" value=\"Modifier\"/>
								</div>
							</div>
                        </form>
                        <form action=\"\" method=\"POST\" name=\"searchMuscle\">
							<div class=\"input-group-seance\">
								<h2>Exercices</h2>
								<div class=\"search-group\">
									<div class=\"input-inline\">
										<label class=\"custom-label\">Muscle à travailler : </label>
										<select class=\"custom-select\" name=\"muscleSearch\">";

										foreach($muscles as $muscle){

											echo "
												<option value=\"".$muscle["id"]."\">".$muscle["nom"]."</option>
											";
										}

										echo "
										</select>
									</div>
									<div class=\"input-inline\">
										<input type=\"submit\" class=\"button-custom-tableau\" name=\"OKmuscle\" value=\"Chercher\">
									</div>
								</div>
								<div class=\"search-group\">
									<div class=\"input-inline\">
										<select class=\"custom-select\" name=\"exerciceSearch\">";
										foreach($exercices as $exercice) {
											echo "
												<option value=\"".$exercice["id"]."\">".$exercice["nom"]."</option>
											";
										}
										echo "
										</select>
									</div>
									<div class=\"input-inline\">
										<input type=\"submit\" class=\"button-custom-tableau\" name=\"OKexercice\" value=\"Ajouter\">
									</div>
								</div>
							</div>
						</form>
						<div class=\"exercice-group\">";
							foreach($exercicesSeance as $exercice) {
								echo "
									<div class=\"exercice-p\"><h3>".$exercice["nom"]."</h3></div>
									<div class=\"exercice-p\"><p>".$exercice["description"]."</p></div>
									<div class=\"input-inline\">
										<label class=\"custom-label\">Poids soulevé : ".$exercice["poidsSouleve"]." kg</label>
									</div>
									<div class=\"input-inline\">
										<a href=\"index.php?action=modifierPoids&idSeance=".$_GET["idSeance"]."&idExercice=".$exercice["id_Exercice"]."&poids=".$exercice["poidsSouleve"]."\"><button class=\"button-custom-tableau\">Modifier</button></a>
									</div>
									<div class=\"input-inline\">
										<a href=\"index.php?action=supprimerExercice&idSeance=".$_GET["idSeance"]."&idExercice=".$exercice["id_Exercice"]."\"><button class=\"button-custom-tableau\">Supprimer</button></a>
									</div>
								";
							}
						echo "
						</div>
					</div>
		";

		$this->fin();
	}

    public function modifierPoids() {
        $this->entete();

        echo "
                    <div class=\"contenu\">
                        <h1 class=\"text-center\">Modifier le poids soulevé</h1>
                        <form action=\"\" method=\"POST\" name=\"modifPoids\">
                            <div class=\"input-group\">
                                <div class=\"input-inline\">
									<label class=\"custom-label\">Poids soulevé : </label>
                                    <input type=\"number\" class=\"custom-number\" name=\"poids\" value=\"".$_GET["poids"]."\">
								</div>
                                <div class=\"input-inline\">
                                    <input type=\"submit\" class=\"button-custom-tableau\" name=\"OKpoids\" value=\"Valider\" />
                                </div>
                            </div>
                        </form>
                    </div>
        ";

        $this->fin();
    }

	public function statistiques($exercices, $stats = null) {
		$this->entete();

		echo "
					<div class=\"contenu\">
						<div class=\"recherche\">
                            <div class=\"div-center\">
                                <form action=\"\" method=\"POST\" name=\"searchMuscleStat\">

                                    <div class=\"input-block\">
                                        <label class=\"custom-label\">Muscle recherché : </label>
                                        <select class=\"custom-select\" name=\"exoSearch\">";
                                        foreach($exercices as $exo){
                                            echo "
                                                <option value=\"".$exo["id"]."\">".$exo["nom"]."</option>
                                            ";
                                        }
                                        echo "
                                        </select>
                                    </div>

                                    <div class=\"input-block\">
                                        <div class=\"group-date\">
                                            <label class=\"custom-label\">Date de début</label>
                                            <input type=\"date\" class=\"custom-datetime\" name=\"dateDebut\" value=\"";  if(isset($_POST["dateDebut"])){echo $_POST["dateDebut"];}else{echo "2021-05-14";} echo "\"/>
                                            <label class=\"custom-label\">Date de fin</label>
                                            <input type=\"date\" class=\"custom-datetime\" name=\"dateFin\" value=\"";  if(isset($_POST["dateFin"])){echo $_POST["dateFin"];}else{echo date('Y-m-d');} echo "\"/>
                                        </div>
                                    </div>
                                    <input type=\"submit\" name=\"OKstat\" class=\"button-custom-tableau\" value=\"rechercher\" />
                                </form>
                            </div>
                        </div>
                        <div class=\"pourcentage\">";
                            if($stats != null) {
                                $resultat = 0;
                                $premierResultat = 0;
                                $dernierResultat = 0;
                                $resultatPlus = 0;
                                $resultatMoins = 0;
                                $compteur = 0;

                                foreach($stats as $poids) {
                                    if($compteur == 0) {
                                        $premierResultat = $poids["poidsSouleve"];
                                        $resultatMoins = $poids["poidsSouleve"];
                                        $dernierResultat = $poids["poidsSouleve"];
                                        $resultatPlus = $poids["poidsSouleve"];
                                    }
                                    else {
                                        if($resultatMoins > $poids["poidsSouleve"]) {
                                            $resultatMoins = $poids["poidsSouleve"];
                                        }
                                        if($resultatPlus < $poids["poidsSouleve"]) {
                                            $resultatPlus = $poids["poidsSouleve"];
                                        }

                                        $dernierResultat = $poids["poidsSouleve"];
                                    }

                                    $compteur++;
                                }

                                $resultat = $dernierResultat - $premierResultat;

                                if($premierResultat > $dernierResultat) {
                                    $resultat = $resultat / $dernierResultat;
                                }
                                else{
                                    $resultat = $resultat / $premierResultat;
                                }

                                $resultat = $resultat * 100;
                                $resultat = round($resultat, 1);

                                echo "
                                    <div class=\"div-center\">";

                                    if($resultat < 0) {
                                        echo "<h1 class=\"pourcent-stats-down\">".$resultat."%</h1>";
                                    }
                                    elseif($resultat == 0) {
                                        echo "<h1 class=\"pourcent-stats-neutral\">+".$resultat."%</h1>";
                                    }
                                    else {
                                        echo "<h1 class=\"pourcent-stats-up\">+".$resultat."%</h1>";
                                    }
                                        echo "
                                    </div>
                                    <div class=\"input-block\">
                                        <p>Première stat : <b>".$premierResultat." kg</b></p>
                                    </div>
                                    <div class=\"input-block\">
                                        <p>Dernière stat : <b>".$dernierResultat." kg</b></p>
                                    </div>
                                    <div class=\"input-block\">
                                        <p>Stat + : <b>".$resultatPlus." kg</b></p>
                                    </div>
                                    <div class=\"input-block\">
                                        <p>Stat - : <b>".$resultatMoins." kg</b></p>
                                    </div>";
                            }

                        echo "
                        </div>
					</div>
		";

		$this->fin();
	}
}