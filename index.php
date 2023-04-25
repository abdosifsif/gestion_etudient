<?php
session_start();
include('connexion.php');
if (isset($_SESSION['nom']) || isset($_SESSION['prenom']) || isset($_SESSION['apogee'])) {
  header('Location: myspace.php');
  exit();
}
if (isset($_POST['submit'])) {
  $apogee = $_POST['apogee'];
  $datenaissance = $_POST['datenaissance'];

  $query = "SELECT * FROM Etudiant WHERE apogee = :apogee AND datenaissance = :datenaissance";
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':apogee', $apogee);
  $stmt->bindParam(':datenaissance', $datenaissance);
  $stmt->execute();
  $result = $stmt->fetch();

  if ($result) {
    $_SESSION['apogee'] = $apogee;
    $_SESSION['nom'] = $result['nom'];
    $_SESSION['prenom'] = $result['prenom'];
    $_SESSION['idetud'] = $result['idetud'];
    header('Location: myspace.php');
  } else {
    $error = "Informations de connexion incorrectes";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Page de connexion pour les étudiants</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.17/tailwind.min.css" integrity="sha512-yXagpXH0ulYCN8G/Wl7GK+XIpdnkh5fGHM5rOzG8Kb9Is5Ua8nZWRx5/RaKypcbSHc56mQe0GBG0HQIGTmd8bw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-100">
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl text-center font-bold mb-8">Page de connexion pour les étudiants</h1>
    <form method="post" class="max-w-lg mx-auto">
      <div class="mb-4">
        <label for="apogee" class="block font-bold mb-2">Apogée:</label>
        <input type="text" id="apogee" name="apogee" required class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      </div>

      <div class="mb-4">
        <label for="datenaissance" class="block font-bold mb-2">Date de naissance (format AAAA-MM-JJ):</label>
        <input type="text" id="datenaissance" name="datenaissance" required class="w-full px-3 py-2 border rounded-md outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        <?php if (isset($error)): ?>
          <p class="text-red-500 mt-2"><?php echo $error; ?></p>
        <?php endif; ?>
      </div>

      <div class="text-center">
        <input type="submit" name="submit" value="Se connecter" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
      </div>
    </form>
  </div>
</body>
</html>