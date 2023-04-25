<?php
session_start();
if (!isset($_SESSION['login']) && !isset($_SESSION['profil'])) {
  header('Location: login.php');
  exit;
}

include('connexion.php');

$id = $_POST['id'];

$query = $pdo->prepare("DELETE FROM utilisateur WHERE iduser = :id");
$query->execute(array(':id' => $id));

header('Location: gestion.php');
exit();
?>