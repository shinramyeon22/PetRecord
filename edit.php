<?php
include('databases.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_method'] ?? '') === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM pets WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
    header('Location: index.php');
    exit;
}
?>