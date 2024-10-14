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
                  
                  // Remplacer les espaces dans le prénom et nom pour éviter les erreurs dans le nom du fichier
                  $prenomClean = str_replace(' ', '_', strtolower($prenom));
                  $nomClean = str_replace(' ', '_', strtolower($nom));
  
                  // Générer un nouveau nom d'image incluant le prénom, le nom et un ID unique
                  $newImageName = $prenomClean . "_" . $nomClean . "_" . uniqid('upload_', true) . "." . $imageExt;
  
                  $imageDestination = 'uploaded/' . $newImageName;
  
                  // Vérifier si le dossier 'uploaded' existe, sinon le créer
                  if (!is_dir('uploaded')) {
                      mkdir('uploaded', 0755, true); // Créer le dossier avec les permissions 755
                  }
  
                  // Déplacer le fichier dans le dossier 'uploaded'
                  if (move_uploaded_file($imageTmpName, $imageDestination)) {
                      // Enregistrer le chemin de l'image dans la session
                      $image = $newImageName;
                      echo " <div class='w-100  p-5  bg-success'>
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
  }
  
} else {
    // Afficher le formulaire si aucune soumission n'a été faite
    ?>
    <div class="container mt-4">
        <form method="POST" class='border border-dark p-2' enctype="multipart/form-data">
            <div class="row w-100">
                <!-- Bloc Prénom/Nom/Âge/Taille/Genre -->
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
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="css" name="connaissances[]" value="CSS">
                            <label class="form-check-label" for="css">CSS</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="js" name="connaissances[]" value="JavaScript">
                            <label class="form-check-label" for="js">JavaScript</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="php" name="connaissances[]" value="PHP">
                            <label class="form-check-label" for="php">PHP</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mysql" name="connaissances[]" value="MySQL">
                            <label class="form-check-label" for="mysql">MySQL</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="bootstrap" name="connaissances[]" value="Bootstrap">
                            <label class="form-check-label" for="bootstrap">Bootstrap</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="symfony" name="connaissances[]" value="Symfony">
                            <label class="form-check-label" for="symfony">Symfony</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="react" name="connaissances[]" value="React">
                            <label class="form-check-label" for="react">React</label>
                        </div>

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
