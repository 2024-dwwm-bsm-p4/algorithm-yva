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
    $date_naissance = $_POST['date_naissance'];  // Nouveau champ pour la date
    $connaissances = isset($_POST['connaissances']) ? $_POST['connaissances'] : [];
    $couleur_preferee = $_POST['couleur_preferee'];  // Nouveau champ pour la couleur
    $image = $_FILES['image']['name'] ?? '';
    $taille = isset($_POST['taille']) ? (float)$_POST['taille'] : null;
    $sexe = isset($_POST['sexe']) ? htmlspecialchars($_POST['sexe']) : null;
    
    // Validation simple
    if (empty($prenom) || empty($nom) || $age < 18 || $age > 70 || empty($date_naissance) || empty($couleur_preferee)) {
        echo "<p>Des informations sont incorrectes ou manquantes.</p>";
    } else {
        // Ajouter les nouvelles données au tableau $table
        $table[] = [
            'prenom' => $prenom,
            'nom' => $nom,
            'age' => $age,
            'date_naissance' => $date_naissance,  // Ajout de la date de naissance
            'connaissances' => $connaissances,
            'couleur_preferee' => $couleur_preferee,  // Ajout de la couleur préférée
            'image' => $image
            
        ];

        // Stocker le tableau mis à jour dans la session
        $_SESSION['user_data'] = $table;

        // Message de succès
        echo "<p>Données ajoutées avec succès !</p>";
    }
}
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
            <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
          </div>
      </div>
    </div>

    <!-- Bloc Connaissances -->
    <div class="card col-md-4 mx-auto my-1 w-25 ">
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
          <input class="form-check-input" type="checkbox" id="javascript" name="connaissances[]" value="JavaScript">
          <label class="form-check-label" for="javascript">JavaScript</label>
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
          <input type="color" class="form-control form-control-color" id="couleur_preferee" name="couleur_preferee" value="#000000" required>
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

  <!-- Bouton soumettre -->
  <div class="row">
    <div class="col-11 mx-auto">
      <button type="submit" class="btn btn-primary w-100">Enregistrer les données</button>
    </div>
  </div>
  </form>