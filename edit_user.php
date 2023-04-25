<?php
include('connexion.php');
session_start();
  if (!isset($_SESSION['login']) && !isset($_SESSION['profil'])) {
    header('Location: login.php');
    exit;
  }
$iduser = $_GET['id'];
$query = $pdo->prepare("SELECT * FROM Utilisateur WHERE iduser = :iduser");
$query->execute(array(':iduser' => $iduser));
$student = $query->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {

  $login = $_POST['login'];
  $password = $_POST['password'];
  $Profil = $_POST['Profil'];
  $statut = $_POST['statut'];

  $query = $pdo->prepare("UPDATE Utilisateur SET login = :login, password = :password, Profil = :Profil, statut = :statut  WHERE iduser = :iduser");
  $query->execute(array(':login' => $login, ':password' => $password, ':Profil' => $Profil, ':statut' => $statut ,':iduser' => $iduser));

  header('Location: deconnexion.php');
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
  <h1 class="text-2xl font-bold mb-4">Modifier Etudiant</h1>
  <form method="POST">
    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="login">
        Identifiant:
      </label>
      <input class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="login" value="<?php echo $student['login']; ?>">
    </div>

    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="password">
        Mot De Passe:
      </label>
      <input class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" type="text" name="password" value="<?php echo $student['password']; ?>">
    </div>
    <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="Profil">
        Profile:
      </label>
      <select class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="Profil">
        <option value="Chef scolarité" <?php if ($student['profil'] == 'Chef scolarité') echo 'selected'; ?>>Chef scolarité</option>
        <option value="agent" <?php if ($student['profil'] == 'agent') echo 'selected'; ?>>agent</option>

      </select>
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