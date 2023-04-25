<?php
include('connexion.php');
session_start();
  if (!isset($_SESSION['login']) && !isset($_SESSION['profil'])) {
    header('Location: login.php');
    exit;
  }

$demande_id = $_GET['id'];

$query = "SELECT * FROM demande WHERE iddemande = :demande_id";
$statement = $pdo->prepare($query);
$statement->bindValue(':demande_id', $demande_id);
$statement->execute();
$demande = $statement->fetch(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $reponseadmin = $_POST['reponseadmin'];
  $query = "UPDATE demande SET reponseadmin = :reponseadmin, iduser = :iduser ,	datereponse =:datereponse WHERE iddemande = :demande_id";
  $statement = $pdo->prepare($query);
  $statement->bindValue(':reponseadmin', $reponseadmin);
  $statement->bindValue(':demande_id', $demande_id);
  $statement->bindValue(':datereponse', date('Y-m-d H:i:s', strtotime('now')));
  $statement->bindValue(':iduser', $_SESSION["iduser"]);
  $statement->execute();
  
  $query = "INSERT INTO eventuser (idevent, iduser, dateevent, ipadress) VALUES (:idevent, :iduser, :dateevent, :ipadress)";
  $statement = $pdo->prepare($query);
  $statement->bindValue(':idevent', $demande_id);
  $statement->bindValue(':iduser', $_SESSION["iduser"]);
  $statement->bindValue(':dateevent', date('Y-m-d H:i:s', strtotime('now')));
  $statement->bindValue(':ipadress', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);
  $statement->execute();
  


  header("Location: gestion.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Modifier la réponse admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.7/tailwind.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="max-w-lg w-full bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <h1 class="text-lg font-bold mb-4">Modifier la réponse admin</h1>

    <form method="POST" class="mb-4">
      <div class="mb-4">
        <label for="reponseadmin" class="block text-gray-700 font-bold mb-2">Réponse admin:</label>
        <select id="reponseadmin" name="reponseadmin" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          <option value="2" <?php if ($demande['reponseadmin'] == 2) { echo 'selected'; } ?>>Approuvé</option>
          <option value="3" <?php if ($demande['reponseadmin'] == 3) { echo 'selected'; } ?>>Rejeté</option>
        </select>
      </div>
      <div class="flex items-center justify-between">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Enregistrer</button>
        <a href="gestion.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Annuler</a>
      </div>
    </form>
  </div>

  <div class="text-center text-gray-500 absolute bottom-0 w-full py-4">
    <p>&copy; 2023 My Website. All rights reserved.</p>
  </div>
</body>
</html>