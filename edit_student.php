<?php

include('connexion.php');
session_start();
  if (!isset($_SESSION['login']) && !isset($_SESSION['profil'])) {
    header('Location: login.php');
    exit;
  }

$idetud = $_GET['id'];

$query = $pdo->prepare("SELECT * FROM Etudiant WHERE idetud = :idetud");
$query->execute(array(':idetud' => $idetud));
$student = $query->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
  
  $apogee = $_POST['apogee'];
  $nom = $_POST['nom'];
  $prenom = $_POST['prenom'];
  $filiere = $_POST['filiere'];
  $datenaissance = $_POST['datenaissance'];
  $statut = $_POST['statut'];

  $query = $pdo->prepare("UPDATE Etudiant SET apogee = :apogee, nom = :nom, prenom = :prenom, filiere = :filiere, datenaissance = :datenaissance, statut = :statut  WHERE idetud = :idetud");
  $query->execute(array(':apogee' => $apogee, ':nom' => $nom, ':prenom' => $prenom, ':filiere' => $filiere, ':datenaissance' => $datenaissance, ':statut' => $statut ,':idetud' => $idetud));

  header('Location: gestion.php');
  exit();
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Gestion</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>

<div class="max-w-2xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Modifier Etudiant </h1>
  <form method="POST">
    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="apogee">
        Numéro Apogée:
      </label>
      <input class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="apogee" value="<?php echo $student['apogee']; ?>">
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="nom">
        Nom:
      </label>
      <input class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="nom" value="<?php echo $student['nom']; ?>">
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="prenom">
        Prénom:
      </label>
      <input class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="prenom" value="<?php echo $student['prenom']; ?>">
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="filiere">
        Filière:
      </label>
      <input class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="filiere" value="<?php echo $student['filiere']; ?>">
    </div>
    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="datenaissance">
        Date de Naissance:
      </label>
      <input class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="date" name="datenaissance" value="<?php echo $student['datenaissance']; ?>">
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="statut">
        Statut:
      </label>
      <select class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="statut">
        <option value="1" <?php if ($student['statut'] == '1') echo 'selected'; ?>>1</option>
        <option value="0" <?php if ($student['statut'] == '0') echo 'selected'; ?>>0</option>

      </select>
    </div>
    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" name="submit">Save Changes</button>
  </form>
</div>
</body>

</html>