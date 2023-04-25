<?php
session_start();
require_once 'connexion.php';

if (isset($_POST['submit'])) {
    if (isset($_POST['confirme'])) {
        if ($_FILES['releve']['error'] == 0 && $_FILES['carte']['error'] == 0) {

            $allowedTypes = ['application/pdf'];
            $maxSize = 4 * 1024 * 1024;
            if (in_array($_FILES['releve']['type'], $allowedTypes) && $_FILES['releve']['size'] <= $maxSize) {
                $allowedTypes = ['image/jpeg', 'image/png'];
                $maxSize = 3 * 1024 * 1024;
                if (in_array($_FILES['carte']['type'], $allowedTypes) && $_FILES['carte']['size'] <= $maxSize) {
                    $idetud = isset($_SESSION['idetud']) ? $_SESSION['idetud'] : "";
                    $iddemande = isset($_SESSION['iddemande']) ? $_SESSION['iddemande'] : "";
                    $nom_prenom = $idetud;
                    $releve_name .= $nom_prenom . "_releve.pdf";
                    $carte_name .= $nom_prenom . "_carte." . pathinfo($_FILES['carte']['name'], PATHINFO_EXTENSION);
                    $releve_path = "uploads/" . $releve_name;
                    $carte_path = "uploads/" . $carte_name;
                    if (move_uploaded_file($_FILES['releve']['tmp_name'], $releve_path) && move_uploaded_file($_FILES['carte']['tmp_name'], $carte_path)) {
                        $sql = "UPDATE demande SET file_releve=:releve, file_carte=:carte WHERE iddemande=:iddemande";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(":releve", $releve_path);
                        $stmt->bindParam(":carte", $carte_path);
                        $stmt->bindParam(":iddemande", $iddemande);
                        if ($stmt->execute()) {
                            header("Location: myspace.php");
                            exit();
                        } else {
                            header("Location: suivant.php?error=confirm");
                            exit();
                        }
                        
    
                        
                        } else {
                            header("Location: suivant.php?error=upload");
                            exit();
                        }
                        
                     
                        
                        } else {
                            header("Location: suivant.php?error=carte");
                            exit();
                        }
                        
                       
                        
                        } else {
                            header("Location: suivant.php?error=releve");
                            exit();
                        }
                        
                     
                        
                        } else {
                            header("Location: suivant.php?error=files");
                            exit();
                        }
                    }
}
?>