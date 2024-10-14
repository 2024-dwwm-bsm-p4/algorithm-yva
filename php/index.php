<?php
// Démarrer la session pour stocker et récupérer les informations utilisateur
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et validation des données
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom = htmlspecialchars($_POST['nom'] );
    $age = isset($_POST['age']) ? (int)$_POST['age'] : null;
    $taille = isset($_POST['taille']) ? (float)$_POST['taille'] : null;
    $sexe = isset($_POST['sexe']) ? htmlspecialchars($_POST['sexe']) : null;
    $date_naissance = $_POST['date_naissance'] ?? null;
    $connaissances = isset($_POST['connaissances']) ? $_POST['connaissances'] : [];
    $couleur_preferee = $_POST['couleur_preferee'] ?? null;
    $image = $_FILES['image']['name'] ?? '';

    // Initialiser $table à partir de $_SESSION['user_data']
    $table = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : [];

    // Rechercher si l'utilisateur existe déjà dans $table
    $existingUserIndex = null;
    foreach ($table as $index => $user) {
        if ($user['prenom'] === $prenom && $user['nom'] === $nom) {
            $existingUserIndex = $index;
            break;
        }
    }

    // Si l'utilisateur existe déjà, mettre à jour ses informations dans $table
    if ($existingUserIndex !== null) {
        // Fusionner les nouvelles données avec les anciennes
        $table[$existingUserIndex] = array_merge($table[$existingUserIndex], [
            'age' => $age ?? $table[$existingUserIndex]['age'],
            'taille' => $taille ?? $table[$existingUserIndex]['taille'],
            'sexe' => $sexe ?? $table[$existingUserIndex]['sexe'],
            'date_naissance' => $date_naissance ?? $table[$existingUserIndex]['date_naissance'],
            'connaissances' => !empty($connaissances) ? $connaissances : $table[$existingUserIndex]['connaissances'],
            'couleur_preferee' => $couleur_preferee ?? $table[$existingUserIndex]['couleur_preferee'],
            'image' => $image ? $image : $table[$existingUserIndex]['image']
        ]);
    } else {
        // Ajouter un nouvel utilisateur dans $table
        $table[] = [
            'prenom' => $prenom,
            'nom' => $nom,
            'age' => $age,
            'taille' => $taille,
            'sexe' => $sexe,
            'date_naissance' => $date_naissance,
            'connaissances' => $connaissances,
            'couleur_preferee' => $couleur_preferee,
            'image' => $image
        ];
    }

    // Synchroniser $table avec $_SESSION['user_data']
    $_SESSION['user_data'] = $table;
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
</head>

<body>

    <?php include "./includes/header.inc.html"; ?>

    <div class="parentBtn d-flex  w-100 ">
        <a href="index.php?home" class="w-50 d-flex align-items-center ">
            <button type="button" class="btn btn-lg btn-light w-75">Home</button>
        </a>

        <?php
        // Vérifier si le paramètre 'home' est présent dans l'URL
        if (isset($_GET['home'])) {  
            ?>
            <a href="index.php?add" class="w-100 d-flex align-items-center ms-5">
                <button type="button" class="btn btn-lg btn-primary">Ajoutez des données</button>
            </a>
            <?php
        }

        if (!empty($table) && isset($_GET['home'])) {
            ?>
            <a href="index.php?add-more" class="w-100 d-flex align-items-center ms-5">
                <button type="button" class="btn btn-lg btn-primary">Ajoutez encore des données</button>
            </a>
            <?php  
        }
        ?>
    </div>

    <div class="container-fluid d-flex justify-content-around gap-10 mt-1 w-100 h-50 min-vh-100 p-0">
        <!-- Navigation : affichage conditionnel si la session contient des données -->
        <nav class="d-flex flex-column gap-5 w-25 me-5">
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
        <section class="d-flex flex-column align-items-center justify-content-center flex-wrap h-50 w-75 w-md-75 p-5">
            <?php
            // Vérification du paramètre dans l'URL
            if (isset($_GET)) {
                $action = key($_GET); // Récupère le nom du premier paramètre dans l'URL

                switch ($action) {
                    case 'debogage':
                        echo "<h2>Débogage</h2>";
                        echo "<div class='w-50 h-75 d-flex justify-content-center align-items-center '>";
                        echo "<pre class='p-2'>";
                        print_r($table); // Affiche les données de $table pour débogage
                        echo "</pre>";
                        echo  "</div>";
                        break;

                        case 'concatenation':
                            echo "<h2>Concaténation</h2>";
                        
                            if (!empty($table)) {
                                foreach ($table as $user) {
                                    $prenom = $user['prenom'];
                                    $nom = $user['nom'];
                                    $age = $user['age'];
                                    $taille = $user['taille'] ?? null; // Utiliser null si la clé 'taille' n'existe pas
                        
                                    // Première phrase : afficher le nom et l'âge
                                    echo "<p>Mr $nom $prenom</p>";
                                    echo "<p>J'ai $age ans, je mesure " . ($taille ? "$taille m" : "non spécifiée") . ".</p>"; // Ajouter une gestion si la taille n'est pas spécifiée
                        
                                    // Boucle pour afficher nom et prénom en majuscules avec taille modifiée
                                    foreach ($table as $user) {
                                        $prenom = strtoupper($user['prenom']);
                                        $nom = strtoupper($user['nom']);
                                        $age = $user['age'];
                                        $taille = $user['taille'] ?? null; // Utiliser null si la clé 'taille' n'existe pas
                        
                                        // Remplacer le point par une virgule dans la taille, si elle est disponible
                                        $taille_affichee = $taille ? str_replace('.', ',', $taille) : "non spécifiée";
                        
                                        echo "<p>Bonjour, je suis $nom $prenom et j'ai $age ans, je mesure $taille_affichee m.</p>";
                                    }
                                }
                            } else {
                                echo "<p>Aucune donnée à concaténer. Veuillez ajouter des informations.</p>";
                            }
                            break;
                        

                  
                        case 'boucle':
                        echo "<h2>Boucle sur les données de la session</h2>";
                        
                        if (!empty($table)) {
                            foreach ($table as $index => $user) {
                                $lineNumber = 0;

                                foreach ($user as $key => $value) {
                                    // Vérifier si la clé est un tableau (ex : 'connaissances')
                                    if (is_array($value)) {
                                        $value = implode(', ', $value);  // Convertir le tableau en chaîne de caractères
                                    }
                                    echo "<p>à la ligne n°$lineNumber correspond la clé \"$key\" et contient \"$value\"</p>";
                                    $lineNumber++;
                                }
                                echo "<br>"; // Séparation entre chaque utilisateur
                            }
                        } else {
                            echo "<p>Aucune donnée dans la session.</p>";
                        }
                        break;
                    
                        case 'function':
                            echo "<h2>Appel d'une Fonction</h2>";
                            
                            function readTable($table)
                            {
                                foreach ($table as $index => $user) {
                                    echo "<p>===> Utilisateur $index</p>"; 
                        
                                    $lineNumber = 0;
                                    foreach ($user as $key => $value) {
                                        if (is_array($value)) {
                                            $value = implode(', ', $value);  // Convertir le tableau en chaîne de caractères
                                           print_r($value);
                                        } 
                        
                                        // Vérifier si la clé est "image"
                                        if ($key === 'image' && !empty($value)) {

                                            // Afficher l'image avec une balise <img>
                                            echo "<p>à la ligne n°$lineNumber correspond la clé \"$key\" et contient : <br>";
                                            echo "<img src='./uploaded/$value' alt='Image de l'utilisateur' style='max-width:150px;'><br></p>";
                                        } else {
                                            // Sinon afficher la clé et la valeur normalement
                                            echo "<p>à la ligne n°$lineNumber correspond la clé \"$key\" et contient \"$value\"</p>";
                                        }
                                        
                                        $lineNumber++;
                                    }
                                    echo "<br>"; // Séparation entre chaque utilisateur
                                }
                            }
                            
                            echo "<p>===> J'utilise ma fonction readTable()</p>";
                            readTable($table);
                            break;
                        
                    
                    case 'supprimer':
                        // Vider les données de la session et du tableau temporaire
                        unset($_SESSION['user_data']);
                        $table = []; // Vide le tableau temporaire
                        echo '<div class="alert alert-success w-50" role="alert"> <p>Les données de la session ont été supprimées.</p> </div>';
                        break;

                    default:
                        error_log("Aucune action reconnue");
                        break;
                }
            }
            ?>

            <?php
            if (isset($_GET['add'])) {
                include "./includes/form.inc.html";
            }
            if (isset($_GET['add-more'])) {
                // Si le paramètre est présent, afficher le formulaire
                include_once './includes/form2.inc.php';
            }
            ?>
        </section>
    </div>

    <?php include_once "./includes/footer.html"; ?>
</body>
</html>
