<?php
session_start();

require_once 'connexion.php';
$stmt = $pdo->prepare('SELECT iddemande FROM Demande WHERE idetud = :idetud');
$stmt->execute(array(':idetud' => $_SESSION['idetud']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $iddemande = $row['iddemande'];
    $demande_exist = true;
} else {
    $demande_exist = false;
}
;

if ($demande_exist) {
    echo '<style>.hide { display: none !important; }</style>';
} else {
    echo '<style>.hidee { display: none !important; }</style>';
}
if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom']) || !isset($_SESSION['apogee'])) {
    header('Location: index.php');
    exit();
}

$semestres = [
    "S4" => ["M1", "M2", "M3", "M4"],
    "S6" => ["M5", "M6", "M7", "M8"],
];

$iddemande = null;

if (isset($_POST["submit"])) {
    $modules = isset($_POST["modules"]) ? $_POST["modules"] : [];

    $stmt = $pdo->prepare('INSERT INTO Demande (idetud, datedemande, modulesdemandees,iduser) VALUES (:idetud, :datedemande, :modulesdemandees,:iduser)');
    $stmt->execute(
        array(
            ':idetud' => $_SESSION['idetud'],
            ':datedemande' => date('Y-m-d'),
            ':modulesdemandees' => implode(", ", $modules),
            ':iduser' => null
        )
    );

    $iddemande = $pdo->lastInsertId();

    
    $_SESSION['iddemande'] = $iddemande;

    echo "<p class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative' role='alert'>Vos choix de modules ont été enregistrés avec succès.</p>";
    header("Location:suivant.php");
}
function get_student_requests()
{
    global $pdo;

    $sql = "SELECT d.iddemande, e.apogee, e.nom, e.prenom, d.datedemande, d.modulesdemandees, d.file_releve, d.file_carte, d.reponseadmin, d.datereponse
          FROM Demande d 
          INNER JOIN Etudiant e ON d.idetud = e.idetud
          WHERE e.idetud = :idetud
          ORDER BY d.datedemande DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idetud', $_SESSION["idetud"]);
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
$requests = get_student_requests();

?>
<!DOCTYPE html>
<html>

<head>
    <title>Espace Etudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-8">
        <h1 class="hide text-2xl font-bold text-center mb-4">Bonjour
            <?php echo $_SESSION['nom'] . ' ' . $_SESSION['prenom'] ?>, veuillez sélectionner au maximum 4 modules.
            Merci.
        </h1>
        <h1 class="hidee text-2xl font-bold text-center mb-4">Bonjour
            <?php echo $_SESSION['nom'] . ' ' . $_SESSION['prenom'] ?>, tu peux modifier ta demande. Merci.
        </h1>
        <div class="hide flex justify-center">
            <form action="" method="POST" class="max-w-lg bg-white p-6 rounded-lg shadow-lg">

                <div class="mb-4">
                    <h2 class="text-xl font-bold mb-2">S4</h2>
                    <?php foreach ($semestres["S4"] as $module): ?>
                        <label class="inline-flex items-center">
                            <input type="checkbox" class="form-checkbox h-5 w-5 text-gray-600" name="modules[]"
                                value="<?php echo $module ?>">
                            <span class="ml-2 text-gray-700">
                                <?php echo $module ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="mb-4">
                    <h2 class="text-xl font-bold mb-2">S6</h2>
                    <?php foreach ($semestres["S6"] as $module): ?>
                        <label class="inline-flex items-center">
                            <input type="checkbox" class="form-checkbox h-5 w-5 text-gray-600" name="modules[]"
                                value="<?php echo $module ?>">
                            <span class="ml-2 text-gray-700">
                                <?php echo $module ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="mb-4 flex justify-between">
                    <a href="deconnexion1.php"
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Déconnexion</a>
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit"
                        name="submit">Suivant</button>
                </div>
            </form>
        </div>
        <div class="hidee flex justify-center mt-8">
            <div class="w-full flex justify-center overflow-x-auto">
                <table class="w-full bg-white shadow-md rounded-lg" style="width:50%">
                    <thead>
                        <tr class="bg-gray-100 border-b-2 border-gray-300">
                            <th class="px-6 py-4 text-gray-600 font-bold uppercase border">Numéro apogée</th>
                            <th class="px-6 py-4 text-gray-600 font-bold uppercase border">Nom</th>
                            <th class="px-6 py-4 text-gray-600 font-bold uppercase border">Prénom</th>
                            <th class="px-6 py-4 text-gray-600 font-bold uppercase border">Modules demandés</th>
                            <th class="px-6 py-4 text-gray-600 font-bold uppercase border">Date de soumission</th>
                            <th class="px-6 py-4 text-gray-600 font-bold uppercase border">Date de réponse</th>
                            <th class="px-6 py-4 text-gray-600 font-bold uppercase border">Réponse</th>
                            <th class="px-6 py-4 text-gray-600 font-bold uppercase border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                            <tr class="border-b border-gray-300">
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <?php echo $request['apogee']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <?php echo $request['last_name']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <?php echo $request['first_name']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <?php echo $request['modules_requested']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <?php echo $request['submission_date']; ?>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <?php if (isset($request['response_sent']) && $request['response_sent']): ?>
                                        <?php echo $request['response_date']; ?>
                                    <?php else: ?>
                                        Non répondu
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <?php if (isset($request['response_sent']) && $request['response_sent']): ?>
                                        <?php echo $request['response_message']; ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-center">
                                    
  
                                        <a href="edit_demand.php?id=<?php echo $request['id']; ?>"
                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Modifier</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <form method="post" action="deconnexion1.php" class="hidee text-center mt-4">
            <button type="submit" class="hidee bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Déconnexion
            </button>
        </form>
    </div>
</body>
<script>
    let all_check_boxs = document.querySelectorAll("input[type='checkbox']");
    console.log(all_check_boxs);

    let num = 0;
    let max = 4;

    all_check_boxs.forEach(element => {
        element.addEventListener("change", function () {

            if (element.checked) {
                num++;
            }
            else {
                num--;
            }
            if (num >= max) {
                all_check_boxs.forEach(element => {
                    if (!element.checked) {
                        element.disabled = true;
                    }
                });
            }
            else {
                all_check_boxs.forEach(element => {
                    element.disabled = false;
                });
            }

        });

    });
</script>

</html>