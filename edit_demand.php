<?php
session_start();
if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom']) || !isset($_SESSION['apogee'])) {
    header('Location: index.php');
    exit();
}
include('connexion.php');


$iddemande = $_GET['id'];


if (isset($_POST["submit"])) {
  
    $modules = isset($_POST["modules"]) ? $_POST["modules"] : [];


    $query = "UPDATE demande SET modulesdemandees = :modulesdemandees WHERE idetud = :idetud";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(
        ':idetud' => $_SESSION['idetud'],
        ':modulesdemandees' => implode(", ", $modules),

    ));
    header('Location: myspace.php');
}
$semestres = [
    "S4" => ["M1", "M2", "M3", "M4"],
    "S6" => ["M5", "M6", "M7", "M8"],
];
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Gestion</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>

<div class="container mx-auto py-8">
        <h1 class="hide text-2xl font-bold text-center mb-4">Bonjour
            veuillez sélectionner au maximum 4 modules.
            Merci.
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