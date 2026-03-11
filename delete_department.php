<?php
// delete_department.php - safe deletion handler for departments
session_start();
require_once __DIR__ . '/connection.php';
include __DIR__ . '/auth_check.php';

// Expect an id via GET (link) or POST (form)
$id = '';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} elseif (isset($_POST['dept_id'])) {
    $id = $_POST['dept_id'];
}

if ($id === '') {
    header('Location: department_list.php?msg=' . urlencode('No department id provided.'));
    exit;
}

// Prepare and execute delete
$stmt = $conn->prepare('DELETE FROM department_tbl WHERE dept_id = ?');
if ($stmt) {
    $stmt->bind_param('s', $id);
    if ($stmt->execute()) {
        $msg = 'Department deleted successfully.';
    } else {
        $msg = 'Failed to delete department: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $msg = 'Database prepare failed: ' . $conn->error;
}

$conn->close();
header('Location: department_list.php?msg=' . urlencode($msg));
exit;

?>
