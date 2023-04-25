<?php
session_start();
if (!isset($_SESSION['login']) && !isset($_SESSION['profil'])) {
  header('Location: login.php');
  exit;
}

include('connexion.php');


$id = $_POST['id'];

$query = $pdo->prepare("DELETE FROM demande WHERE iddemande = :id");
$query->execute(array(':id' => $id));

header('Location: gestion.php');
exit();
?>