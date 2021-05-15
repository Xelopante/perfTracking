<?php

class Controleur {

    //Fonction qui retourne l'IP Véritable de l'appareil.
    public function getIp() {

        if(!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            //IP du proxy.
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            //IP de l'utilisateur véritable.
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function connexion() {

        //Le bouton de connexion doit être coché.
        if(isset($_POST["OKconnexion"]))
        {
            //Les champs doivent être remplis.
            if($_POST["login"] != "" && $_POST["password"] != "")
            {
                // /!\ REMPLACER IP LORS DE MISE EN PLACE SUR SERVEUR DISTANT /!\
                //Récupération de l'IP et création du cookie.
                $IP = $this->getIp();
                $token = bin2hex(random_bytes(30));

                $user = (new User)->connexion($_POST["login"], $_POST["password"], $IP, $token);
                //Si l'authentification réussi, on créé le cookie.
                if($user)
                {
                    //Redirection en header car besoin de recharger la page afin de prendre en compte le cookie.
                    setcookie('user', $token, time()+(60*60*24*30*13));
                    header('Location: index.php?action=seances');
                    exit();
                }
                else
                {
                    $message = "Nom d'utilisateur ou mot de passe incorrect";
                    (new Vue)->connexion($message);
                }
            }
            else
            {
                $message = "Veuillez remplir tout les champs";
                (new Vue)->connexion($message);
            }
        }
        else
        {
            (new Vue)->connexion();
        }
    }

    public function inscription() {

        //Le bouton d'inscription doit être coché.
        if(isset($_POST["OKinscription"]))
        {
            //Les champs doivent être remplis.
            if($_POST["nom"] != "" && $_POST["prenom"] != "" && $_POST["login"] != "" && $_POST["password"] != "" && $_POST["password-verif"] != "")
            {
                //Les 2 mots de passe doivent correspondre.
                if($_POST["password"] == $_POST["password-verif"])
                {
                    $user = (new User)->estDejaInscrit($_POST["login"]);
                    //Si "estDejaInscrit" est faux alors on peut faire l'inscription.
                    if($user == false)
                    {
                        $user = (new User)->inscription($_POST["nom"], $_POST["prenom"], $_POST["login"], $_POST["password"]);
                        if($user)
                        {
                            (new Vue)->connexion();
                        }
                    }
                    else
                    {
                        $message = "Ce nom d'utilisateur est déjà pris";
                        (new Vue)->inscription($message);
                    }
                }
                else
                {
                    $message = "Les mots de passe de correspondent pas";
                    (new Vue)->inscription($message);
                }
            }
            else
            {
                $message = "Veuillez remplir tout les champs";
                (new Vue)->inscription($message);
            }
        }
        else
        {
            (new Vue)->inscription();
        }
    }

    public function deconnexion() {

        $IP = $this->getIp();
        $user = (new User)->deconnexion($_COOKIE["user"], $IP);

        //Si l'authentification est supprimée de la table, on supprime le cookie avec un date d'expiraiton inférieure à la date actuelle.
        if($user)
        {
            //Redirection en header car besoin de recharger la page afin de prendre en compte le cookie.
            setcookie('user', '', time()-(60*60*24*30*13));
            header('Location: index.php?action=connexion');
            exit();
        }
    }

    public function seances($message = null) {

        //Vérification du token et de l'IP.
        $IP = $this->getIp();
        $verification = (new User)->verifyIP($_COOKIE["user"], $IP);

        if($verification["compte"] == 1)
        {
            $seances = null;

            //Récupération de l'ID de l'utilisateur par son token.
            $idUser = (new User)->getIdByToken($_COOKIE["user"]);
            if($idUser != null)
            {
                //récupération des séances par l'ID du user.
                $seances = (new Seance)->getSeanceUser($idUser);
            }

            //Messgae initialisé seulement par l'appel de seances par des "this->seances($message)" dans le contrôleur.
            if(isset($message))
            {
                (new Vue)->seances($seances, $message);
            }
            else
            {
                (new Vue)->seances($seances);
            }
        }
        else
        {
            $idUser = (new User)->getIdByToken($_COOKIE["user"]);
            (new User)->deconnexionForcee($idUser);

            //Redirection en header car besoin de recharger la page afin de prendre en compte le cookie.
            setcookie('user', '', time()-(60*60*24*30*13));
            header('Location: index.php?action=connexion');
            exit();
        }
    }

    public function seance() {

        //Vérification du token et de l'IP.
        $IP = $this->getIp();
        $verification = (new User)->verifyIP($_COOKIE["user"], $IP);

        if($verification["compte"] == 1)
        {
            //Récupération de l'ID de l'utilisateur par son token et de l'ID de la séance dans l'url.
            $idUser = (new User)->getIdByToken($_COOKIE["user"]);
            $idSeance = $_GET["idSeance"];

            if($idUser != null)
            {

                if(isset($_POST["OKDateHeure"]))
                {
                    $seance = (new Seance)->modifySeance($idSeance, $idUser, $_POST["dateSeance"], $_POST["heureDebutSeance"], $_POST["heureFinSeance"]);

                    if(!$seance)
                    {
                        $message = "Une erreur est survenue lors de la modification de la séance.";
                    }
                }

                if(isset($_POST["OKexercice"]))
                {
                    $exercice = (new Exercice)->addExerciceSeance($idUser, $_GET["idSeance"], $_POST["exerciceSearch"]);

                    if(!$exercice)
                    {
                        $message = "Une erreur est survenue lors de l'ajout de l'exercice.";
                    }
                }

                //Récupération des détails de la séance par l'ID du User et son ID (identidiant relatif).
                $seance = (new Seance)->getDetailsSeance($idSeance, $idUser);

                //Si l'ID du muscle recherché est actif, on retourne les exercices concernés par le muscle.
                if(isset($_POST["muscleSearch"]) && isset($_POST["OKmuscle"]))
                {
                    $exercices = (new Exercice)->getExerciceByMuscle($_POST["muscleSearch"]);
                }
                else
                {
                    $exercices = (new Exercice)->getAllExercice();
                }

                $exercicesSeance = (new Exercice)->getExerciceSeance($idSeance, $idUser);
                $muscles = (new Muscle)->getAllMuscles();
            }

            (new Vue)->seance($seance, $exercices, $exercicesSeance, $muscles);
        }
        else
        {
            $idUser = (new User)->getIdByToken($_COOKIE["user"]);
            (new User)->deconnexionForcee($idUser);

            //Redirection en header car besoin de recharger la page afin de prendre en compte le cookie.
            setcookie('user', '', time()-(60*60*24*30*13));
            header('Location: index.php?action=connexion');
            exit();
        }
    }

    public function ajouterSeance() {

        //Vérification du token et de l'IP.
        $IP = $this->getIp();
        $verification = (new User)->verifyIP($_COOKIE["user"], $IP);

        if($verification["compte"] == 1)
        {
            $seance = (new Seance);
            $idUser = (new User)->getIdByToken($_COOKIE["user"]);

            $date = date("Y-m-d");

            if($seance->insertSeance($idUser, $date))
            {
                header('Location: ./index.php');
            }
        }
        else
        {
            $idUser = (new User)->getIdByToken($_COOKIE["user"]);
            (new User)->deconnexionForcee($idUser);

            //Redirection en header car besoin de recharger la page afin de prendre en compte le cookie.
            setcookie('user', '', time()-(60*60*24*30*13));
            header('Location: index.php?action=connexion');
            exit();
        }
    }

    public function supprimerSeance() {

        //Vérification du token et de l'IP.
        $IP = $this->getIp();
        $verification = (new User)->verifyIP($_COOKIE["user"], $IP);

        if($verification["compte"] == 1)
        {
            //Suppression d'une séance par l'ID de l'utilisateur par son token et de l'ID de la séance dans l'url.
            $seance = (new Seance);

            $idUser = (new User)->getIdByToken($_COOKIE["user"]);
            $id = $_GET["idSeance"];

            if($seance->deleteSeance($id, $idUser))
            {
                //Retour dans les séances.
                $this->seances();
            }
            else
            {
                $message = "Une erreur est survenue lors de la suppression de la séance.";
                $this->seances();
            }
        }
        else
        {
            $idUser = (new User)->getIdByToken($_COOKIE["user"]);
            (new User)->deconnexionForcee($idUser);

            //Redirection en header car besoin de recharger la page afin de prendre en compte le cookie.
            setcookie('user', '', time()-(60*60*24*30*13));
            header('Location: index.php?action=connexion');
            exit();
        }
    }

    public function modifierPoids() {

        //Vérification du token et de l'IP.
        $IP = $this->getIp();
        $verification = (new User)->verifyIP($_COOKIE["user"], $IP);

        if($verification["compte"] == 1)
        {
            if(isset($_GET["idSeance"]) && isset($_GET["idExercice"]) && isset($_GET["poids"]))
            {
                if(isset($_POST["OKpoids"]) && isset($_POST["poids"]))
                {
                    $idUser = (new User)->getIdByToken($_COOKIE["user"]);
                    $exercice = (new Exercice)->modifyPoidsExerciceSeance($_POST["poids"], $idUser, $_GET["idExercice"], $_GET["idSeance"]);

                    header('Location: ./index.php?action=seance&idSeance='.$_GET["idSeance"].'');
                }
                else
                {
                    (new Vue)->modifierPoids();
                }
            }
            else
            {
                (new Vue)->seances();
            }
        }
        else
        {
            $idUser = (new User)->getIdByToken($_COOKIE["user"]);
            (new User)->deconnexionForcee($idUser);

            //Redirection en header car besoin de recharger la page afin de prendre en compte le cookie.
            setcookie('user', '', time()-(60*60*24*30*13));
            header('Location: index.php?action=connexion');
            exit();
        }
    }

    public function supprimerExercice() {

        //Vérification du token et de l'IP.
        $IP = $this->getIp();
        $verification = (new User)->verifyIP($_COOKIE["user"], $IP);

        if($verification["compte"] == 1)
        {
            if(isset($_GET["idSeance"]) && isset($_GET["idExercice"]))
            {
                $idUser = (new User)->getIdByToken($_COOKIE["user"]);
                $exercice = (new Exercice)->supprimerExercice($idUser, $_GET["idSeance"], $_GET["idExercice"]);

                $this->seance();
            }
            else
            {
                (new Vue)->erreur404();
            }
        }
        else
        {
            $idUser = (new User)->getIdByToken($_COOKIE["user"]);
            (new User)->deconnexionForcee($idUser);

            //Redirection en header car besoin de recharger la page afin de prendre en compte le cookie.
            setcookie('user', '', time()-(60*60*24*30*13));
            header('Location: index.php?action=connexion');
            exit();
        }
    }

    public function statistiques() {

        //Vérification du token et de l'IP.
        $IP = $this->getIp();
        $verification = (new User)->verifyIP($_COOKIE["user"], $IP);

        if($verification["compte"] == 1)
        {
            $exercices = (new Exercice)->getAllExercice();

            if(isset($_POST["OKstat"]))
            {
                $idUser = (new User)->getIdByToken($_COOKIE["user"]);

                $stats = (new Exercice)->getPoidsExerciceIntervalle($_POST["dateDebut"], $_POST["dateFin"], $_POST["exoSearch"], $idUser);

                (new Vue)->statistiques($exercices, $stats);
            }
            else
            {
                (new Vue)->statistiques($exercices);
            }
        }
        else
        {
            $idUser = (new User)->getIdByToken($_COOKIE["user"]);
            (new User)->deconnexionForcee($idUser);

            //Redirection en header car besoin de recharger la page afin de prendre en compte le cookie.
            setcookie('user', '', time()-(60*60*24*30*13));
            header('Location: index.php?action=connexion');
            exit();
        }
    }

    public function erreur404() {

        (new Vue)->erreur404();
    }
}