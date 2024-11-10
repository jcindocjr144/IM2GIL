<?php
require_once 'admin_functions.php';

if (isset($_GET['subject_id'])) {
    $subject_id = intval($_GET['subject_id']);
    $admin = new Admin();

    try {
        $students = $admin->getEnrolledStudents($subject_id);
        echo json_encode($students);
    } catch (Exception $e) {
        echo json_encode([]);
    }
}
?>
