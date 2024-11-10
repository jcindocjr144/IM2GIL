<?php
session_start();
require_once 'admin_functions.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin = new Admin();
$message = "";

// Handle adding, updating, and deleting subjects and grades
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if (isset($_POST['add_subject'])) {
            $subject_name = htmlspecialchars($_POST['subject_name']);
            $room_assignment = htmlspecialchars($_POST['room_assignment']);
            $start_date = htmlspecialchars($_POST['start_date']);  // Get start date
            $end_date = htmlspecialchars($_POST['end_date']);      // Get end date
            $schedule = htmlspecialchars($_POST['schedule']);      // Get schedule

            // Check if the subject already exists
            if ($admin->subjectExists($subject_name)) {
                $message = "Error: Subject name '$subject_name' already exists.";
            } else {
                // Pass schedule parameter to addSubject method
                $message = $admin->addSubject($subject_name, $room_assignment, $start_date, $end_date, $schedule);
            }
        } 
        elseif (isset($_POST['update_subject'])) {
            $subject_id = htmlspecialchars($_POST['subject_id']);
            $subject_name = htmlspecialchars($_POST['subject_name']);
            $room_assignment = htmlspecialchars($_POST['room_assignment']);
            $start_date = htmlspecialchars($_POST['start_date']); // Get start date
            $end_date = htmlspecialchars($_POST['end_date']);     // Get end date
            $schedule = htmlspecialchars($_POST['schedule']);      // Get schedule

            error_log("Updating subject ID: $subject_id with name: $subject_name, room: $room_assignment, start date: $start_date, end date: $end_date, schedule: $schedule");
            // Pass schedule parameter to updateSubject method
            $message = $admin->updateSubject($subject_id, $subject_name, $room_assignment, $start_date, $end_date, $schedule);
        } elseif (isset($_POST['delete_subject'])) {
            $subject_id = htmlspecialchars($_POST['subject_id']);
            $message = $admin->deleteSubject($subject_id);
        } elseif (isset($_POST['add_or_update_grade'])) {
            $student_id = htmlspecialchars($_POST['student_id']);
            $subject_id = htmlspecialchars($_POST['subject_id']);
            $grade = htmlspecialchars($_POST['grade']);
            $message = $admin->addOrUpdateGrade($student_id, $subject_id, $grade);
        } elseif (isset($_POST['delete_grade'])) {
            $grade_id = htmlspecialchars($_POST['grade_id']);
            $message = $admin->deleteGrade($grade_id);
        } elseif (isset($_POST['delete_student'])) {
            $student_id = htmlspecialchars($_POST['student_id']);
            $message = $admin->deleteStudent($student_id); // Ensure this method is implemented in Admin class
        } elseif (isset($_POST['update_grade'])) {
            $grade_id = htmlspecialchars($_POST['grade_id']);
            $new_grade = htmlspecialchars($_POST['grade']);

            // Validate the input
            if (is_numeric($new_grade) && $new_grade >= 1 && $new_grade <= 5) {
                // Prepare the SQL statement to update the grade
                $stmt = $admin->getConnection()->prepare("UPDATE grades SET grade = ? WHERE id = ?");
                $stmt->bindParam(1, $new_grade); // Bind new grade
                $stmt->bindParam(2, $grade_id);   // Bind grade ID

                if ($stmt->execute()) {
                    $message = "Grade updated successfully!";
                } else {
                    $message = "Failed to update grade.";
                }
            } else {
                $message = "Invalid grade. Please enter a number between 1 and 5.";
            }
        }

        // After processing the request, redirect to the same page to avoid duplicate submission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit; // Make sure to exit after redirect
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Retrieve subjects and grades
$subjects = $admin->getSubjects();
$grades = $admin->getAllGrades();
$students = $admin->getAllStudents(); 

// Include the HTML view
include 'admindashboard_html.php';
?>


<?php if ($message): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: 'Message',
                text: <?php echo json_encode($message); ?>,
                icon: '<?php echo strpos($message, "Error") !== false ? "error" : "success"; ?>',
                confirmButtonText: 'OK'
            });
        });
    </script>
<?php endif; ?>
