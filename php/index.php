<?php
// Démarrer la session pour stocker et récupérer les informations utilisateur
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et validation des données
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom = htmlspecialchars($_POST['nom']);
    $age = (int)$_POST['age'];
    $taille = (float)$_POST['taille'];
    $sexe = htmlspecialchars($_POST['sexe']);

    // Vérifications simples
    if (empty($prenom) || empty($nom) || $age < 18 || $age > 70 || $taille < 1.26 || $taille > 3 || !in_array($sexe, ['femme', 'homme'])) {
        echo "<p>Des informations sont incorrectes ou manquantes.</p>";
    } else {
        // Ajouter les nouvelles données à la session
        $_SESSION['user_data'][] = [
            'prenom' => $prenom,
            'nom' => $nom,
            'age' => $age,
            'taille' => $taille,
            'sexe' => $sexe
        ];

        // Copier les données de la session dans un tableau temporaire
        $table = $_SESSION['user_data'];

        // // Affichage du récapitulatif des données soumises
        // echo "<h2>Récapitulatif des données soumises :</h2>";
        // echo "<p>Prénom : $prenom</p>";
        // echo "<p>Nom : $nom</p>";
        // echo "<p>Âge : $age ans</p>";
        // echo "<p>Taille : $taille m</p>";
        // echo "<p>Sexe : " . ($sexe == 'femme' ? 'Femme' : 'Homme') . "</p>";
    }
} else {
    // Si aucune donnée n'a été soumise, charger la session dans $table pour les traitements ultérieurs
    $table = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : [];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include "./includes/head.inc.html"; ?>

    <title>Gestion des sessions en PHP</title>
    <style>
        *{
            outline:2px solid black;
        }
    </style>
</head>

<body>

    <?php include "./includes/header.inc.html"; ?>

    <div class="parentBtn d-flex  w-100 ms-1">
        <a href="index.php?home" class="w-50 d-flex align-items-center">
            <button type="button" class="btn btn-lg btn-light w-75">Home</button>
        </a>

        <?php
        // Vérifier si le paramètre 'add' est présent dans l'URL
        if (!isset($_GET['home'])) {  // Si 'add' n'est pas présent dans l'URL, afficher le bouton
            ?>
            <a href="index.php?add" class="w-100 d-flex align-items-center ms-5">
                <button type="button" class="btn btn-lg btn-primary">Ajoutez des données</button>
            </a>
            <?php
        }
        ?>

    </div>

    <div class="container-fluid d-flex gap-10 mt-1 h-50 h-50 min-vh-100 p-0">
        <!-- Navigation : affichage conditionnel si la session contient des données -->
        <nav class="d-flex flex-column gap-5 w-50">
            <?php
            // Afficher uniquement si le tableau temporaire contient des données
            if (!empty($_SESSION)) {
                include "./includes/ul.inc.php"; // Inclure la liste si la session contient des données
            } else {
                error_log("session est vide");
            }
            ?>
        </nav>

        <!-- Section principale : switch en fonction des paramètres de l'URL -->
        <section class="d-flex flex-column align-items-center justify-content-center flex-wrap h-50 w-50 w-md-75">
            <?php
            // Vérification du paramètre dans l'URL
            if (isset($_GET)) {
                $action = key($_GET); // Récupère le nom du premier paramètre dans l'URL (ex: debogage, concatenation)

                switch ($action) {
                    case 'debogage':
                        echo "<h2>Débogage Activé</h2>";
                        echo "<div class='w-50 d-flex justify-content-center'>";
                        echo "<pre class='p-2'>";
                        print_r($table); // Affiche les données de $table pour débogage
                        echo "</pre>";
                        echo  "</div>";
                        break;

                    case 'concatenation':
                        echo "<h2>Concaténation</h2>";

                        // Vérification si des données existent dans la session
                        if (!empty($table)) {
                            foreach ($table as $user) {
                                $prenom = $user['prenom'];
                                $nom = $user['nom'];
                                $age = $user['age'];
                                $taille = $user['taille'];

                                // Première phrase : afficher le nom et l'âge en format normal
                                echo "<p>Mr $nom $prenom</p>";
                                echo "<p>J'ai $age ans, je mesure $taille m.</p>";

                                // Boucle pour afficher la phrase avec nom et prénom en majuscules et taille avec une virgule
                                foreach ($table as $user) {
                                    $prenom = strtoupper($user['prenom']); // Convertir le prénom en majuscules
                                    $nom = strtoupper($user['nom']);       // Convertir le nom en majuscules
                                    $age = $user['age'];

                                    // Remplacer le point décimal par une virgule pour la taille
                                    $taille = str_replace('.', ',', $user['taille']);

                                    // Afficher un message avec nom/prénom en majuscules et taille modifiée
                                    echo "<p>Bonjour, je suis $nom $prenom et j'ai $age ans, je mesure $taille m.</p>";
                                }
                            }
                        } else {
                            echo "<p>Aucune donnée à concaténer. Veuillez ajouter des informations dans la session.</p>";
                        }
                        break;

                        case 'boucle':
                            echo "<h2>Boucle sur les données de la session</h2>";
                            
                            if (!empty($table)) {
                                // Parcourir chaque utilisateur dans le tableau
                                foreach ($table as $index => $user) {
                                    // Initialisation du numéro de ligne
                                    $lineNumber = 0;
                        
                                    // Parcourir chaque clé et valeur de l'utilisateur
                                    foreach ($user as $key => $value) {
                                        echo "<p>à la ligne n°$lineNumber correspond la clé \"$key\" et contient \"$value\"</p>";
                                        $lineNumber++;
                                    }
                                    echo "<br>"; // Ajouter une séparation entre chaque utilisateur
                                }
                            } else {
                                echo "<p>Aucune donnée dans la session.</p>";
                            }
                            break;
                        

                            case 'function':
                                echo "<h2>Appel d'une Fonction</h2>";
                            
                                // Définition de la fonction readTable()
                                function readTable($table)
                                {
                                    // Boucle pour chaque utilisateur dans le tableau
                                    foreach ($table as $index => $user) {
                                        echo "<p>===> Utilisateur $index</p>"; // Afficher l'utilisateur
                            
                                        // Initialisation du compteur de lignes pour chaque utilisateur
                                        $lineNumber = 0;
                            
                                        // Boucle foreach pour parcourir chaque clé et valeur de l'utilisateur
                                        foreach ($user as $key => $value) {
                                            echo "<p>à la ligne n°$lineNumber correspond la clé \"$key\" et contient \"$value\"</p>";
                                            $lineNumber++;
                                        }
                            
                                        echo "<br>"; // Séparation entre chaque utilisateur
                                    }
                                }
                            
                                // Appel de la fonction readTable() avec le tableau $table (qui existe déjà)
                                echo "<p>===> J'utilise ma fonction readTable()</p>";
                                readTable($table);
                                break;
                            

                    case 'supprimer':
                        // echo "<h2>Suppression des données</h2>";
                        // Vider les données de la session et du tableau temporaire
                        unset($_SESSION['user_data']);
                        $table = []; // Vide le tableau temporaire

                        echo '<div class="alert alert-success w-50" role="alert"> <p>Les données de la session ont été supprimées.</p> </div>';
                        break;

                    default:
                        error_log("Aucune action reconnue") ;
                        break;
                }
            }
            ?>
            <?php
            if (isset($_GET['add'])) {
                include "./includes/form.inc.html";
            }
            ?>
        </section>

        <!-- Afficher le formulaire si ?add est dans l'URL -->

    </div>
    <?
    include_once "./includes/footer.html"
    ?>
</body>

</html>