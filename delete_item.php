<?php
// delete_item.php - safe deletion handler
session_start();
require_once __DIR__ . '/connection.php';
include __DIR__ . '/auth_check.php';

// Expect an id via GET (link) or POST (form)
$id = '';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_POST['item_id'])) {
    $id = $_POST['item_id'];
}

if ($id === '') {
    header('Location: item_list.php?msg=' . urlencode('No item id provided.'));
    exit;
}

// Prepare and execute delete
$stmt = $conn->prepare('DELETE FROM item_tbl WHERE item_id = ?');
if ($stmt) {
    $stmt->bind_param('s', $id);
    if ($stmt->execute()) {
        $msg = 'Item deleted successfully.';
    } else {
        $msg = 'Failed to delete item: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $msg = 'Database prepare failed: ' . $conn->error;
}

$conn->close();
header('Location: item_list.php?msg=' . urlencode($msg));
exit;

?>
