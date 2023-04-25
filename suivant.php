<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom']) || !isset($_SESSION['apogee']) ) {
    header('Location: index.php');
    exit();
}
if (!isset($_SESSION['iddemande']) ) {
    header('Location: myspace.php');
    exit();
}
$error = $_GET["error"] ?? "";
if ($error === "") {
    echo '<style>.hide { display: none; }</style>';
}elseif ($error === "confirm") {
    $message = "Veuillez confirmer que vous acceptez nos termes et conditions.";
} elseif ($error === "upload") {
    $message = "Erreur lors du téléversement des fichiers.";
} elseif ($error === "carte") {
    $message = "Type de fichier invalide ou taille de fichier non prise en charge pour la carte d'étudiant.";
} elseif ($error === "releve") {
    $message = "Type de fichier invalide ou taille de fichier non prise en charge pour le relevé de notes.";
} elseif ($error === "files") {
    $message = "Veuillez téléverser à la fois le relevé de notes et la carte d'étudiant.";
} else {
    $message = "";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Espace Etudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.7/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
    <div class="hide bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Erreur :</strong>
        <span class="block sm:inline"></span>
    </div>

    <div class="flex justify-center container mx-auto py-8">
        <form action="upload.php" method="post" enctype="multipart/form-data" class="w-full md:w-1/2 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="releve">Relevé de notes (S1, S2 et S3) :</label>
                <input class="form-input w-full" type="file" name="releve" id="releve" accept=".pdf" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="carte">Carte étudiant :</label>
                <input class="form-input w-full" type="file" name="carte" id="carte" accept=".jpg,.png,.jpeg" required>
            </div>
            <div class="mb-4">
                <input class="form-checkbox h-5 w-5 text-blue-600" type="checkbox" name="confirme" id="confirme" required>
                <label class="ml-2 text-gray-700" for="confirme">J'accepte ma demande</label>
            </div>
            <div class="flex items-center justify-between">
                <a href="deconnexion1.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Déconnexion</a>
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4" type="submit" name="submit">Envoyer ma demande</button>            </div>
        </form>
    </div>

    <script>
        var error = "<?php echo $error; ?>";
        var message = "<?php echo $message; ?>";
        var alertBox = document.querySelector('.hide');
        var messageBox = alertBox.querySelector('span');

        if (error !== "") {
            alertBox.classList.remove('hide');
            messageBox.textContent = message;
        }

        var checkbox = document.getElementById("confirme");
        var bouton = document.querySelector("[name='submit']");

        checkbox.addEventListener("change", function() {
            if (checkbox.checked == true) {
                bouton.removeAttribute("disabled");
            } else {
                bouton.setAttribute("disabled", "disabled");
            }
        });
    </script>
</body>
</html>