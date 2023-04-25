<?php
session_start();
include('connexion.php');
if (isset($_SESSION['login']) && isset($_SESSION['profil'])) {
  header('Location: gestion.php');
  exit;
}
if (isset($_POST['submit'])) {
  $login = $_POST['login'];
  $password = $_POST['password'];

  $query = "SELECT * FROM Utilisateur WHERE login = :login AND password = :password";
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':login', $login);
  $stmt->bindParam(':password', $password);
  $stmt->execute();
  $result = $stmt->fetch();

  if ($result) {
    $_SESSION['login'] = $result['login'];
    $_SESSION['profil'] = $result['profil'];
    header('Location: gestion.php');
  } else {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Informations de connexion incorrectes</strong>
          </div>';
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Identifiant</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.7/tailwind.min.css">
</head>

<body>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white p-8 rounded-md shadow-md">
      <h1 class="text-center font-bold text-2xl mb-5">Connexion</h1>
      <form action="" method="POST">
        <div class="mb-4">
          <label class="block text-gray-700 font-bold mb-2" for="login">
            Identifiant
          </label>
          <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="login" name="login" type="text" placeholder="Identifiant" required>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-bold mb-2" for="password">
            Mot de passe
          </label>
          <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" placeholder="Mot de passe" required>
        </div>
        <div class="flex items-center justify-between">
          <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" name="submit">
            Se connecter
          </button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>