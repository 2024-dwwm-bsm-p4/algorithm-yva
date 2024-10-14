<?php
// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// On récupère les données existantes dans la session ou on initialise un tableau vide
$table = isset($_SESSION['user_data']) ? $_SESSION['user_data'] : [];

// Vérifier si le formulaire est soumis (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom = htmlspecialchars($_POST['nom']);
    $age = (int)$_POST['age'];
    $date_naissance = $_POST['date_naissance'] ?? null;  // Nouveau champ pour la date
    $connaissances = isset($_POST['connaissances']) ? $_POST['connaissances'] : [];
    $couleur_preferee = $_POST['couleur_preferee'] ?? null;  // Nouveau champ pour la couleur
    $image = $_FILES['image']['name'] ?? '';
    $taille = isset($_POST['taille']) ? (float)$_POST['taille'] : null;
    $sexe = isset($_POST['sexe']) ? htmlspecialchars($_POST['sexe']) : null;

    // Vérification simple
    if (empty($prenom) || empty($nom) || $age < 18 || $age > 70) {
        echo "<p>Des informations sont incorrectes ou manquantes.</p>";
    } else {
        // Étape 3 : Gestion de l'upload de l'image
        if (!empty($_FILES['image']['name'])) {
            $imageName = $_FILES['image']['name'];
            $imageTmpName = $_FILES['image']['tmp_name'];
            $imageSize = $_FILES['image']['size'];
            $imageError = $_FILES['image']['error'];
            $imageType = $_FILES['image']['type'];

            // Extensions autorisées
            $allowed = ['jpg', 'jpeg', 'png'];

            // Récupérer l'extension du fichier
            $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

            // Vérifier si l'extension est autorisée
            if (in_array($imageExt, $allowed)) {
                if ($imageError === 0 && $imageSize < 2000000) { // Limite de taille à 2MB
                    $newImageName = uniqid('', true) . "." . $imageExt;  // Générer un nom unique
                    $imageDestination = 'uploaded/' . $newImageName;

                    // Vérifier si le dossier 'uploaded' existe, sinon le créer
                    if (!is_dir('uploaded')) {
                        mkdir('uploaded', 0755, true); // Créer le dossier avec les permissions 755
                    }

                    // Déplacer le fichier dans le dossier 'uploaded'
                    if (move_uploaded_file($imageTmpName, $imageDestination)) {
                        // Enregistrer le chemin de l'image dans la session
                        $image = $newImageName;
                    } else {
                        echo "<p>Erreur lors du téléchargement de l'image.</p>";
                    }
                } else {
                    echo "<p>Erreur lors du téléchargement de l'image ou taille trop grande.</p>";
                }
            } else {
                echo "<p>Format d'image non autorisé. Seuls JPG, JPEG et PNG sont autorisés.</p>";
            }
        }

        // Étape 2 : Fusionner les données si l'utilisateur existe déjà
        $existingUserIndex = null;

        // Vérifier si l'utilisateur existe déjà dans la session
        foreach ($table as $index => $user) {
            if ($user['prenom'] === $prenom && $user['nom'] === $nom) {
                $existingUserIndex = $index;
                break;
            }
        }

        // Si l'utilisateur existe déjà, on met à jour ses données avec les nouvelles valeurs du formulaire
        if ($existingUserIndex !== null) {
            // On met à jour uniquement les nouveaux champs disponibles
            $table[$existingUserIndex]['age'] = $age;
            if ($date_naissance) $table[$existingUserIndex]['date_naissance'] = $date_naissance;
            if ($connaissances) $table[$existingUserIndex]['connaissances'] = $connaissances;
            if ($couleur_preferee) $table[$existingUserIndex]['couleur_preferee'] = $couleur_preferee;
            if ($image) $table[$existingUserIndex]['image'] = $image;
        } else {
            // Ajouter un nouvel utilisateur si pas déjà dans la session
            $table[] = [
                'prenom' => $prenom,
                'nom' => $nom,
                'age' => $age,
                'date_naissance' => $date_naissance,
                'connaissances' => $connaissances,
                'couleur_preferee' => $couleur_preferee,
                'image' => $image
            ];
        }

        // Stocker le tableau mis à jour dans la session
        $_SESSION['user_data'] = $table;

        // Étape 1 : Afficher le message de succès et masquer le formulaire
        echo '<div class="alert alert-success w-75 mx-auto text-center" role="alert">Données envoyées avec succès !</div>';
    }
} else {
    // Afficher le formulaire si aucune soumission n'a été faite
    ?>
    <!-- Formulaire HTML pour ajouter encore des données -->
    <div class="row w-100">
        <!-- Bloc Prénom et Nom -->
        <div class="card col-md-7 mx-auto my-1">
            <div class="card-body">
                <h5 class="card-title">Prénom</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="age" class="form-label">Âge (18 à 70 ans)</label>
                        <input type="number" class="form-control" id="age" name="age" placeholder="Renseignez votre âge" min="18" max="70" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance">
                    </div>
                    <!-- Le reste des champs -->
                    <div class="card-body">
                        <h5 class="card-title">Connaissances</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="html" name="connaissances[]" value="HTML">
                            <label class="form-check-label" for="html">HTML</label>
                        </div>
                        <!-- Ajoute les autres options ici -->
                        <div class="mb-3">
                            <label for="couleur_preferee" class="form-label">Couleur préférée</label>
                            <input type="color" class="form-control form-control-color" id="couleur_preferee" name="couleur_preferee" value="#000000">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Joindre une image (jpg ou png)</h5>
                            <div class="mb-3">
                                <input class="form-control" type="file" id="image" name="image" accept="image/png, image/jpeg">
                            </div>
                        </div>
                    </div>

                    <!-- Bouton soumettre -->
                    <div class="row">
                        <div class="col-11 mx-auto">
                            <button type="submit" class="btn btn-primary w-100">Enregistrer les données</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}
?>
