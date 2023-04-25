<?php
session_start();
  if (!isset($_SESSION['login']) && !isset($_SESSION['profil'])) {
    header('Location: login.php');
    exit;
  }
require_once 'connexion.php';
if ($_SESSION['profil'] == "agent") {
  echo '<style>.chef-only { display: none; }</style>';

}
$stmt = $pdo->query('SELECT * FROM Etudiant');
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->query('SELECT * FROM Utilisateur');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['form_id'] === 'form1') {
   
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
      $message =  "L'etudiant déjà existe";
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
  } else if ($_POST['form_id'] === 'form2') {
  
    $login = $_POST['login'];
    $password = $_POST['Password'];
    $profil = $_POST['Profil'];
    $statut = 1;

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM Utilisateur WHERE login = :Login');
    $stmt->bindParam(':Login', $Login);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
      $message =  "Utlisateur déjà existe";
      $messageClass = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
    } else {
    
      $stmt = $pdo->prepare('INSERT INTO Utilisateur (login, password, profil,statut) VALUES (:login, :password, :profil,:statut)');

      $stmt->bindParam(':login', $login);
      $stmt->bindParam(':password', $password);
      $stmt->bindParam(':profil', $profil);
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


  }else if ($_POST['form_id'] === 'form3') {
    $num_apogee = $_POST['num_apogee'];
    $filiere = $_POST['filiere'];

   
    $sql = "SELECT * FROM Etudiant WHERE 1=1";
    $params = array();
    if (!empty($num_apogee)) {
        $sql .= " AND apogee = ?";
        $params[] = $num_apogee;
    }
    if (!empty($filiere)) {
        $sql .= " AND filiere = ?";
        $params[] = $filiere;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
function get_student_requests() {
  global $pdo;

  $sql = "SELECT d.iddemande, e.apogee, e.nom, e.prenom, d.datedemande, d.modulesdemandees, d.file_releve, d.file_carte, d.reponseadmin, d.datereponse
          FROM Demande d
          INNER JOIN Etudiant e ON d.idetud = e.idetud
          ORDER BY d.datedemande DESC";

  $stmt = $pdo->prepare($sql);
  $stmt->execute();

  $requests = array();

  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $response_message = '';
    switch ($row['reponseadmin']) {
      case 1:
        $response_message = 'En attente';
        break;
      case 2:
        $response_message = 'Approuvé';
        break;
      case 3:
        $response_message = 'Rejeté';
        break;
      default:
        $response_message = 'Inconnu';
        break;
    }

    $request = array(
      'id' => $row['iddemande'],
      'apogee' => $row['apogee'],
      'last_name' => $row['nom'],
      'first_name' => $row['prenom'],
      'submission_date' => $row['datedemande'],
      'modules_requested' => $row['modulesdemandees'],
      'transcript_attachment' => $row['file_releve'],
      'student_card_attachment' => $row['file_carte'],
      'response_sent' => $row['reponseadmin'],
      'response_date' => $row['datereponse'],
      'response_message' => $response_message
    );

    $requests[] = $request;
  }

  return $requests;
}
$requests=get_student_requests();

$total_students_query = "SELECT COUNT(*) FROM Etudiant";
$total_students_stmt = $pdo->query($total_students_query);
$total_students = $total_students_stmt->fetchColumn();

$total_requests_query = "SELECT COUNT(*) FROM Demande";
$total_requests_stmt = $pdo->query($total_requests_query);
$total_requests = $total_requests_stmt->fetchColumn();

  $requests_per_filiere_query = "SELECT e.filiere, COUNT(*) FROM Demande d
                                JOIN Etudiant e ON d.idetud = e.idetud
                                GROUP BY e.filiere";
  $requests_per_filiere_stmt = $pdo->query($requests_per_filiere_query);
  $requests_per_filiere = array();
  while ($row = $requests_per_filiere_stmt->fetch()) {
    $requests_per_filiere[$row['filiere']] = $row['COUNT(*)'];
  }

$unanswered_requests_query = "SELECT COUNT(*) FROM Demande WHERE reponseadmin IS NULL";
$unanswered_requests_stmt = $pdo->query($unanswered_requests_query);
$unanswered_requests = $unanswered_requests_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Gestion</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>

  <header class="bg-gray-800 text-white py-4 flex justify-between">
    <div class="container mx-auto px-4">
      <h1 class="text-2xl font-bold">Gestion</h1>
    </div>
    <div class="max-w-2xl mx-auto ml-auto">
      <form action="deconnexion.php" method="POST">
        <button
          class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
          type="submit" name="submit">
          Deconnexion
        </button>
      </form>
    </div>
  </header>
  <?php if (isset($message)): ?>
    <div class="<?php echo $messageClass; ?>" role="alert">
      <strong class="font-bold">
        <?php echo $message; ?>
      </strong>
    </div>
  <?php endif; ?>

  <main class="container mx-auto px-4 py-8">
    <div class="flex">
     
      <nav class="w-1/6 mr-8">
        <ul class="list-none">
          <li class="mb-4">
            <a href="#" class="chef-only text-blue-500 font-bold" data-target="students">Étudiants</a>
          </li>
          <li class="mb-4">
            <a href="#" class="text-blue-500 font-bold" data-target="Recherche">Rechercher un étudiant</a>
          </li>
          <li class="mb-4">
            <a href="#" class="text-blue-500 font-bold" data-target="requests">Demandes</a>
          </li>
          <li class="mb-4">
            <a href="#" class="chef-only text-blue-500 font-bold" data-target="users">Utilisateurs</a>
          </li>
          <li class="mb-4">
            <a href="#" class="chef-only text-blue-500 font-bold" data-target="stats">statistiques</a>
          </li>
        </ul>
      </nav>
      <div class="container mx-auto px-4 py-8" id="welcome-section">
        <h2 class="text-3xl font-bold mb-4">Bienvenue
          <?php echo $_SESSION['login']; ?> sur la page de gestion (
          <?php echo $_SESSION['profil']; ?>)
        </h2>
      </div>
     
      <section class="chef-only w-3/4 hidden" id="students">
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
  <input type="hidden" name="form_id" value="form1">
  <div class="flex flex-wrap -mx-3 mb-4">
    <div class="w-full px-3 mb-4">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="apogee">
        Numéro apogée
      </label>
      <input
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="apogee" name="apogee" type="text"  >
      <p class="text-red-500 text-xs italic" id="apogeeError"></p>
    </div>
    <div class="w-full px-3 mb-4">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="nom">
        Nom
      </label>
      <input
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="nom" name="nom" type="text"  >
      <p class="text-red-500 text-xs italic" id="nomError"></p>
    </div>
    <div class="w-full px-3 mb-4">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="prenom">
        Prénom
      </label>
      <input
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="prenom" name="prenom" type="text" >
      <p class="text-red-500 text-xs italic" id="prenomError"></p>
    </div>
    <div class="w-full px-3 mb-4">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="filiere">
        Filière
      </label>
      <input
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="filiere" name="filiere" type="text" >
      <p class="text-red-500 text-xs italic" id="filiereError"></p>
    </div>
    <div class="w-full px-3 mb-4">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="datenaissance">
        Date de naissance
      </label>
      <input
        class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="datenaissance" name="datenaissance" type="date" ="jj/mm/aaaa" max="2005-01-01" min="1980-01-01">
      <p class="text-red-500 text-xs italic" id="datenaissanceError"></p>
    </div>
  </div>
  <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
    Ajouter
  </button>
</form>

      </section>



      <section class="chef-only w-3/4 hidden" id="users">
        <h2 id="students" class="text-2xl font-bold mb-4">Utilisateurs</h2>

        <table class="w-full border">
          <thead>
            <tr class="bg-gray-100">
              <th class="py-2 px-4 border">Identifiant</th>
              <th class="py-2 px-4 border">Mot De Passe</th>
              <th class="py-2 px-4 border">Profile</th>
              <th class="py-2 px-4 border">statut</th>
              <th class="py-2 px-4 border">Actions</th>

            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td>
                  <?php echo $user['login']; ?>
                </td>
                <td>
                  <?php echo $user['password']; ?>
                </td>
                <td>
                  <?php echo $user['profil']; ?>
                </td>

                <td>
                  <?php echo $user['statut']; ?>
                </td>
                <td>
                  <a href="edit_user.php?id=<?php echo $_SESSION["iduser"]=$user['iduser']; ?>"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Modifier</a>
                 
                  <form method="POST" action="delete_user.php" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $user['iduser']; ?>">
                    <button type="submit"
                      class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Supprimer</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <h3 class="text-lg font-bold my-4">Ajouter un Utilisateur</h3>
        <form class="w-full max-w-sm" method="POST" onsubmit="return validateForm()">
          <input type="hidden" name="form_id" value="form2">

          <div class="flex flex-wrap -mx-3 mb-4">
            <div class="w-full px-3 mb-4">
              <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="login">
                Identifiant
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                id="login" name="login" type="text"  required>
              <p class="text-red-500 text-xs italic" id="apogeeError"></p>
            </div>
            <div class="w-full px-3 mb-4">
              <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="Password">
                Mot De Passe
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                id="Password" name="Password" type="text"  required>
              <p class="text-red-500 text-xs italic" id="nomError"></p>
            </div>
            <div class="w-full px-3 mb-4">
              <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="Profil">
                Profile
              </label>
              <select
                class="appearance-none block w-full bg-gray-200 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                id="Profil" name="Profil" required>
                <option value="Chef scolarité">Chef scolarité</option>
                <option value="Agent">Agent</option>
              </select>
              <p class="text-red-500 text-xs italic" id="prenomError"></p>
            </div>
          </div>
          <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
            Ajouter
          </button>
        </form>
      </section>

      <section class="w-3/4 hidden" id="Recherche">
  <h2 id="students" class="text-2xl font-bold mb-4">Résultats de recherche</h2>

  <h3 class="text-lg font-bold mt-4 mb-2">Rechercher un étudiant</h3>
  <form class="flex flex-wrap mb-4" method="POST" id="student-search-form">
    <input type="hidden" name="form_id" value="form3">
    <div class="w-full md:w-auto md:flex-grow md:flex-shrink-0 md:pr-4 mb-2">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="num_apogee">
        Numéro Apogée:
      </label>
      <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="num_apogee" name="num_apogee" type="text">
    </div>
    <div class="w-full md:w-auto md:flex-grow md:flex-shrink-0 md:pr-4 mb-2">
      <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="filiere">
        Filière:
      </label>
      <input class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
        id="filiere" name="filiere" type="text">
    </div>
    <button type="submit"
      class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded md:w-auto w-full md:mt-0 mt-2">Rechercher</button>
  </form>

  <?php if (!empty($search_results)): ?>
    <table class="w-full border">
      <thead>
        <tr class="bg-gray-100">
          <th class="py-2 px-4 border">Numéro apogée</th>
          <th class="py-2 px-4 border">Nom</th>
          <th class="py-2 px-4 border">Prénom</th>
          <th class="py-2 px-4 border">Filière</th>
          <th class="py-2 px-4 border">datenaissance</th>
          <th class="py-2 px-4 border">statut</th>
          <th class="chef-only py-2 px-4 border">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($search_results as $student): ?>
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
              class="chef-only bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Modifier</a>
           
            <form class="" method="POST" action="delete_student.php" style="display: inline;">
              <input type="hidden" name="id" value="<?php echo $student['idetud']; ?>">
              <button type="submit"
                class="chef-only bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Supprimer</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>Aucun résultat trouvé.</p>
  <?php endif; ?>
</section>


<section class="w-3/4 hidden" id="requests">
  <h2 class="text-2xl font-bold mb-4">Consultation des demandes d'étudiants</h2>


  <table class="w-full border">
  <thead>
    <tr class="bg-gray-100">
      <th class="py-2 px-4 border">Numéro apogée</th>
      <th class="py-2 px-4 border">Nom</th>
      <th class="py-2 px-4 border">Prénom</th>
      <th class="py-2 px-4 border">Modules demandés</th>
      <th class="py-2 px-4 border">Date de soumission</th>
      <th class="chef-only py-2 px-4 border">Relevé de notes</th>
      <th class="chef-only py-2 px-4 border">Carte étudiante</th>
      <th class="py-2 px-4 border">Date de réponse</th>
      <th class="py-2 px-4 border">Réponse</th>
      <th class="py-2 w-60 px-4 border">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($requests as $request): ?>
    <tr>
      <td>
        <?php echo $request['apogee']; ?>
      </td>
      <td>
        <?php echo $request['last_name']; ?>
      </td>
      <td>
        <?php echo $request['first_name']; ?>
      </td>
      <td>
        <?php echo $request['modules_requested']; ?>
      </td>
      <td>
        <?php echo $request['submission_date']; ?>
      </td>
      <td class="chef-only">
  <?php if ($request['transcript_attachment']): ?>
    <a href="<?php echo $request['transcript_attachment']; ?>" target="_blank"
      class="text-blue-500 hover:text-blue-700 font-bold underline">Télécharger le relevé de notes</a>
  <?php else: ?>
    <span class="text-gray-500">Aucun relevé de notes joint</span>
  <?php endif; ?>
</td>
<td class="chef-only">
  <?php if ($request['student_card_attachment']): ?>
    <a href="<?php echo $request['student_card_attachment']; ?>" target="_blank"
      class="text-blue-500 hover:text-blue-700 font-bold underline">Télécharger la carte étudiante</a>
  <?php else: ?>
    <span class="text-gray-500">Aucune carte étudiante jointe</span>
  <?php endif; ?>
</td>
      <td>
        <?php if (isset($request['response_sent']) && $request['response_sent']): ?>
        <?php echo $request['response_date']; ?>
        <?php else: ?>
        Non répondu
        <?php endif; ?>
      </td>
      <td>
        <?php if (isset($request['response_sent']) && $request['response_sent']): ?>
        <?php if (isset($request['response_message'])): ?>
        <?php echo $request['response_message']; ?>
        <?php else: ?>
        Aucune réponse disponible
        <?php endif; ?>
        <?php else: ?>
        En attente de réponse
        <?php endif; ?>
      </td>
      <td>
        <a href="edit_request.php?id=<?php echo $request['id']; ?>"
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Modifier</a>
        <form method="POST" action="delete_request.php" style="display: inline;">
          <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
          <button type="submit"
            class="chef-only bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Clôturer</button>
         
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</section>
<section class="chef-only w-3/4 hidden mx-auto my-8" id="stats">
  <h2 class="text-2xl font-bold mb-4">Tableau de bord des statistiques</h2>
  <div class="grid grid-cols-2 gap-4">
    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="text-base font-medium text-gray-700">Nombre total d'étudiants</div>
      <div class="text-3xl font-bold text-gray-900"><?php echo $total_students; ?></div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="text-base font-medium text-gray-700">Nombre total de demandes</div>
      <div class="text-3xl font-bold text-gray-900"><?php echo $total_requests; ?></div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="text-base font-medium text-gray-700">Nombre de demandes par filière</div>
      <table>
        <thead>
          <tr>
            <th class="text-left">Filière</th>
            <th class="text-left">Nombre de demandes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($requests_per_filiere as $filiere => $num_requests) { ?>
            <tr>
              <td><?php echo $filiere; ?></td>
              <td><?php echo $num_requests; ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
      <div class="text-base font-medium text-gray-700">Nombre de demandes sans réponse</div>
      <div class="text-3xl font-bold text-gray-900"><?php echo $unanswered_requests; ?></div>
    </div>
  </div>
</section>







    </div>
  </main>

</body>
<script>
  const links = document.querySelectorAll('[data-target]');
  const welcomeSection = document.getElementById('welcome-section');
  links.forEach(link => {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      const target = this.getAttribute('data-target');
      const section = document.getElementById(target);
     
      document.querySelectorAll('section:not(#' + target + ')').forEach(section => {
        section.classList.add('hidden');
      });
      
      section.classList.remove('hidden');
      welcomeSection.classList.add('hidden');
    });
  });
  function validateForm() {
  var apogee = document.getElementById("apogee").value;
  var nom = document.getElementById("nom").value;
  var prenom = document.getElementById("prenom").value;
  var filiere = document.getElementById("filiere").value;
  var datenaissance = document.getElementById("datenaissance").value;
  var apogeeError = document.getElementById("apogeeError");
  var nomError = document.getElementById("nomError");
  var prenomError = document.getElementById("prenomError");
  var filiereError = document.getElementById("filiereError");
  var datenaissanceError = document.getElementById("datenaissanceError");


  if (apogee == "" || nom == "" || prenom == "" || filiere == "" || datenaissance == "") {
    alert("Tous les champs doivent être remplis");
    return false;
  }

 
  if (!/^\d+$/.test(apogee)) {
    apogeeError.innerHTML = "Le numéro apogée doit contenir uniquement des chiffres";
    return false;
  } else {
    apogeeError.innerHTML = "";
  }

  if (!/^[a-zA-Z]+$/.test(nom) || !/^[a-zA-Z]+$/.test(prenom)) {
    nomError.innerHTML = "Le nom et le prénom doivent contenir uniquement des lettres";
    prenomError.innerHTML = "Le nom et le prénom doivent contenir uniquement des lettres";
    return false;
  } else {
    nomError.innerHTML = "";
    prenomError.innerHTML = "";
  }

  if (!/^[a-zA-Z\s]+$/.test(filiere)) {
    filiereError.innerHTML = "La filière doit contenir uniquement des lettres et des espaces";
    return false;
  } else {
    filiereError.innerHTML = "";
  }

  if (!/^\d{4}-\d{2}-\d{2}$/.test(datenaissance)) {
    datenaissanceError.innerHTML = "La date de naissance doit être au format jj/mm/aaaa";
    return false;
  } else {
    datenaissanceError.innerHTML = "";
  }

  return true;
}



</script>

</html>