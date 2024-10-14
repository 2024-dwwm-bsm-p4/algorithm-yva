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

  // Recherche de l'utilisateur existant dans $table
  $existingUserIndex = null;
  foreach ($table as $index => $user) {
      if ($user['prenom'] === $prenom && $user['nom'] === $nom) {
          $existingUserIndex = $index;
          break;
      }
  }

  // Vérification et gestion de l'upload de l'image
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
            
            // Nettoyer les noms (comme tu l'as fait)
            $prenomClean = str_replace(' ', '_', strtolower($prenom));
            $nomClean = str_replace(' ', '_', strtolower($nom));

            // Générer un nouveau nom unique
            $newImageName = $prenomClean . "_" . $nomClean . "_" . uniqid('upload_', true) . "." . $imageExt;

            $imageDestination = 'uploaded/' . $newImageName;

            // Vérifier si le dossier 'uploaded' existe, sinon le créer
            if (!is_dir('uploaded')) {
                mkdir('uploaded', 0755, true);
            }

            // Déplacer l'image téléchargée vers le dossier 'uploaded'
            if (move_uploaded_file($imageTmpName, $imageDestination)) {
                // Mettre à jour le tableau avec le NOUVEAU nom de l'image
                if ($existingUserIndex !== null) {
                    $table[$existingUserIndex]['image'] = $newImageName;
                } else {
                    // Si l'utilisateur n'existe pas encore, ajouter un nouvel utilisateur avec l'image
                    $table[] = [
                        'prenom' => $prenom,
                        'nom' => $nom,
                        'age' => $age,
                        'taille' => $taille,
                        'sexe' => $sexe,
                        'date_naissance' => $date_naissance,
                        'connaissances' => $connaissances,
                        'couleur_preferee' => $couleur_preferee,
                        'image' => $newImageName // Utiliser le nouveau nom d'image
                    ];
                }

                echo "<div class='w-100 p-5 bg-success'>
                    <p class='text-light'>Image téléchargée avec succès sous le nom : $newImageName</p>
                </div>";
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

  // Si l'utilisateur existe déjà, fusionner ses données
  if ($existingUserIndex !== null) {
    $table[$existingUserIndex] = array_merge($table[$existingUserIndex], [
        'age' => $age ?? $table[$existingUserIndex]['age'],
        'taille' => $taille ?? $table[$existingUserIndex]['taille'],
        'sexe' => $sexe ?? $table[$existingUserIndex]['sexe'],
        'date_naissance' => $date_naissance ?? $table[$existingUserIndex]['date_naissance'],
        'connaissances' => !empty($connaissances) ? $connaissances : $table[$existingUserIndex]['connaissances'],
        'couleur_preferee' => $couleur_preferee ?? $table[$existingUserIndex]['couleur_preferee'],
        // Ne pas modifier l'image si elle n'a pas été uploadée
    ]);
  }

  // Synchroniser $table avec $_SESSION['user_data']
  $_SESSION['user_data'] = $table;

} else {
  // Afficher le formulaire si aucune soumission n'a été faite
?>
  <div class="container mt-4">
    <form method="POST" class='border border-dark p-2' enctype="multipart/form-data">
      <!-- Bloc Prénom/Nom/Âge/Taille/Genre -->
      <div class="row w-100">
        <div class="card col-md-7 mx-auto my-1">
          <div class="card-body">
            <h5 class="card-title">Prénom</h5>
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
            <div class="mb-3 d-flex align-items-center">
              <label for="taille" class="form-label me-2">Taille (1,26m à 3m)</label>
              <input type="number" class="form-control w-25 me-2" id="taille" name="taille" min="1.26" max="3" step="0.01" placeholder="1,70" required>
              <span>m</span>
            </div>
            <div class="mb-3">
              <label class="form-label d-block">Genre</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="genre" id="femme" value="Femme" required>
                <label class="form-check-label" for="femme">Femme</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="genre" id="homme" value="Homme" required>
                <label class="form-check-label" for="homme">Homme</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Bloc Connaissances/Couleur préférée/Date de naissance -->
        <div class="card col-md-4 mx-auto my-1">
          <div class="card-body">
            <h5 class="card-title">Connaissances</h5>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="html" name="connaissances[]" value="HTML">
              <label class="form-check-label" for="html">HTML</label>
            </div>
            <!-- Autres options de compétences (omises pour la brièveté) -->
            <div class="mb-3">
              <label for="couleur_preferee" class="form-label">Couleur préférée</label>
              <input type="color" class="form-control form-control-color" id="couleur_preferee" name="couleur_preferee" value="#000000">
            </div>
            <div class="mb-3">
              <label for="date_naissance" class="form-label">Date de naissance</label>
              <input type="date" class="form-control" id="date_naissance" name="date_naissance">
            </div>
          </div>
        </div>
      </div>

      <!-- Bloc Joindre une image -->
      <div class="row w-100">
        <div class="card col-11 mx-auto my-1">
          <div class="card-body">
            <h5 class="card-title">Joindre une image (jpg ou png)</h5>
            <div class="mb-3">
              <input class="form-control" type="file" id="image" name="image" accept="image/png, image/jpeg">
            </div>
          </div>
        </div>
      </div>

      <!-- Bouton Enregistrer les données -->
      <div class="row">
        <div class="col-11 mx-auto">
          <button type="submit" class="btn btn-primary w-100">Enregistrer les données</button>
        </div>
      </div>
    </form>
  </div>
<?php
}
?>
