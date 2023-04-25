
<?php
session_start();
if (!isset($_SESSION['iduser'])) {
  header('Location: login.php');
  exit;
}
require_once 'connexion.php';

$stmt = $pdo->query('SELECT * FROM Etudiant');
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $apogee = $_POST['apogee'];
  $nom = $_POST['nom'];
  $prenom = $_POST['prenom'];
  $filiere = $_POST['filiere'];
  $datenaissance = $_POST['datenaissance'];
  $statut = 1;

  $stmt = $pdo->prepare('SELECT COUNT(*) FROM Etudiant WHERE apogee = :apogee');
  $stmt->bindParam(':apogee', $apogee);
  $stmt->execute();
  $count = $stmt->fetchColumn();

  if ($count > 0) {
    $message = "L'etudiant déjà existe";
    $messageClass = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
  } else {

    $stmt = $pdo->prepare('INSERT INTO Etudiant (apogee, nom, prenom, filiere, datenaissance ,statut) VALUES (:apogee, :nom, :prenom, :filiere, :datenaissance, :statut)');

    $stmt->bindParam(':apogee', $apogee);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':filiere', $filiere);
    $stmt->bindParam(':datenaissance', $datenaissance);
    $stmt->bindParam(':statut', $statut);

  
    if ($stmt->execute()) {
      $message = "L'etudiant créé avec succès";
      $messageClass = 'alert-success';
      header('Location: gestion.php');
    } else {
      $message = "échec de la création de l'étudiant";
      $messageClass = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">

    <title>etudiants</title>
</head>
<body>
<section class="w-3/4">
        <h2 id="students" class="text-2xl font-bold mb-4">Étudiants</h2>

        <table class="w-full border">
          <thead>
            <tr class="bg-gray-100">
              <th class="py-2 px-4 border">Numéro apogée</th>
              <th class="py-2 px-4 border">Nom</th>
              <th class="py-2 px-4 border">Prénom</th>
              <th class="py-2 px-4 border">Filière</th>
              <th class="py-2 px-4 border">datenaissance</th>
              <th class="py-2 px-4 border">statut</th>
              <th class="py-2 px-4 border">Actions</th>

            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $student): ?>
              <tr>
                <td>
                  <?php echo $student['apogee']; ?>
                </td>
                <td>
                  <?php echo $student['nom']; ?>
                </td>
                <td>
                  <?php echo $student['prenom']; ?>
                </td>
                <td>
                  <?php echo $student['filiere']; ?>
                </td>
                <td>
                  <?php echo $student['datenaissance']; ?>
                </td>
                <td>
                  <?php echo $student['statut']; ?>
                </td>
                <td>
                  <a href="edit_student.php?id=<?php echo $student['idetud']; ?>"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Modifier</a>
                  
                  <form method="POST" action="delete_student.php" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $student['idetud']; ?>">
                    <button type="submit"
                      class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Supprimer</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <h3 class="text-lg font-bold my-4">Ajouter un étudiant</h3>
<form class="w-full max-w-sm" method="POST" onsubmit="return validateForm()">
  <div class="flex flex-wrap -mx-3 mb-4">
    <div class="w-full px-3 mb-4">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="apogee">
        Numéro apogée
      </label>
      <input
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="apogee" name="apogee" type="text" placeholder="123456" required>
      <p class="text-red-500 text-xs italic" id="apogeeError"></p>
    </div>
    <div class="w-full px-3 mb-4">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="nom">
        Nom
      </label>
      <input
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="nom" name="nom" type="text" placeholder="Doe" required>
      <p class="text-red-500 text-xs italic" id="nomError"></p>
    </div>
    <div class="w-full px-3 mb-4">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="prenom">
        Prénom
      </label>
      <input
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="prenom" name="prenom" type="text" placeholder="John" required>
      <p class="text-red-500 text-xs italic" id="prenomError"></p>
    </div>
    <div class="w-full px-3 mb-4">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="filiere">
        Filière
      </label>
      <input
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="filiere" name="filiere" type="text" placeholder="Informatique" required>
      <p class="text-red-500 text-xs italic" id="filiereError"></p>
    </div>
    <div class="w-full px-3 mb-4">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="datenaissance">
        Date de naissance
      </label>
      <input
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="datenaissance" name="datenaissance" type="date" placeholder="jj/mm/aaaa" required>
      <p class="text-red-500 text-xs italic" id="datenaissanceError"></p>
    </div>
  </div>
  <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
    Ajouter
  </button>
</form>
      </section>
</body>
</html>