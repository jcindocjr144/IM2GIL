<?php
// student_dashboard.php
session_start();
require_once 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

class Student extends Database {
    // Fetch grades for the student based on student_id
    public function getGrades($student_id) {
        $stmt = $this->getDb()->prepare("SELECT s.subject_name, g.grade 
                                         FROM grades g
                                         JOIN subjects s ON g.subject_id = s.id
                                         JOIN students st ON g.student_id = st.id
                                         WHERE g.student_id = :student_id");
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch personal info for the student based on user_id
    public function getPersonalInfo($user_id) {
        $stmt = $this->getDb()->prepare("SELECT id, username, password, first_name, last_name, middle_name, birth_date 
                                         FROM users
                                         WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update personal info for the student
    public function updatePersonalInfo($user_id, $username, $password, $first_name, $last_name, $middle_name, $birth_date) {
        try {
            $this->getDb()->beginTransaction();

            // Update the main user information in the `users` table
            $stmt = $this->getDb()->prepare("UPDATE users SET username = :username, password = :password, 
                                              first_name = :first_name, last_name = :last_name, middle_name = :middle_name, birth_date = :birth_date 
                                              WHERE id = :user_id");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password); // No hashing for password
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':middle_name', $middle_name);
            $stmt->bindParam(':birth_date', $birth_date);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            // Update related tables (e.g., grades)
            $stmt = $this->getDb()->prepare("UPDATE grades SET username = :username WHERE student_id = :user_id");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            $this->getDb()->commit();

            return "Personal information and related records updated successfully!";
        } catch (Exception $e) {
            $this->getDb()->rollBack();
            return "Failed to update information: " . $e->getMessage();
        }
    }
}

// Create an instance of the Student class
$student = new Student(); // Make sure this is not commented out

// Fetch grades and personal information
$student_id = $_SESSION['user_id'];
$grades = $student->getGrades($student_id);
$personalInfo = $student->getPersonalInfo($student_id);

// Handle form submission for updating personal info
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_personal_info'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_name = $_POST['middle_name'];
    $birth_date = $_POST['birth_date'];

    // Update the information in the database
    if ($student->updatePersonalInfo($_SESSION['user_id'], $username, $password, $first_name, $last_name, $middle_name, $birth_date)) {
        $personalInfo = $student->getPersonalInfo($_SESSION['user_id']); // Refresh personal info
        $successMsg = "Information updated successfully!";
    } else {
        $errorMsg = "Failed to update information.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="shortcut icon" href="image/cpclogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="studentstyles.css">    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div id="main">
    <div class="row p-3">
        <div class="col-lg-11 col-11">
            <h1 class="text-warning"> Welcome <span class="text-info"><?php echo htmlspecialchars($_SESSION['username']); ?></span></h1>
        </div>
        <div class="col-lg-1 col-1">
            <span onclick="openNav()"><i class="fa-solid fa-bars p-3 bg-warning text-dark rounded-2"></i></span>
        </div>
    </div>
    <div class="container bg-warning p-2" id="grades" style="display:none;">
    <h3 class="text-center p-2">Grades</h3>
    <table class="table table-bordered border-dark">
        <thead>
            <tr class="bg-black text-warning border-white">
                <th>Subject</th>
                <th>Grade</th>
                <th>Status</th> <!-- New column for status -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grades as $grade): ?>
                <tr>
                    <td><?php echo htmlspecialchars($grade['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                    <td>
                        <?php
                        $gradeValue = $grade['grade'];
                        if ($gradeValue == 0) { 
                            echo "Incomplete"; 
                        } elseif ($gradeValue > 0 && $gradeValue < 1) { 
                            echo "Invalid output"; 
                        } elseif ($gradeValue == 1 || ($gradeValue > 1 && $gradeValue <= 3)) { 
                            echo "Passed"; 
                        } elseif ($gradeValue > 3 && $gradeValue <= 5) { 
                            echo "Failed"; 
                        } else {
                            echo "Invalid output"; 
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


    <!-- Personal Info Section -->
    <div class="container bg-warning p-2" id="info" style="display:none;">
        <h3 class="text-center p-2">Personal Information</h3>

        <?php if (isset($successMsg)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
        <?php elseif (isset($errorMsg)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <table class="table table-bordered bg-dark text-warning p-5">
                <tr>
                    <th>User ID</th>
                    <td><?php echo htmlspecialchars($personalInfo['id']); ?></td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td><input type="text" name="username" value="<?php echo htmlspecialchars($personalInfo['username']); ?>" class="form-control" required></td>
                </tr>
                <tr>
                    <th>Password</th>
                    <td><input type="password" name="password" value="<?php echo htmlspecialchars($personalInfo['password']); ?>" class="form-control" required></td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td><input type="text" name="first_name" value="<?php echo htmlspecialchars($personalInfo['first_name']); ?>" class="form-control" required></td>
                </tr>
                <tr>
                    <th>Middle Name</th>
                    <td><input type="text" name="middle_name" value="<?php echo htmlspecialchars($personalInfo['middle_name']); ?>" class="form-control"></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><input type="text" name="last_name" value="<?php echo htmlspecialchars($personalInfo['last_name']); ?>" class="form-control" required></td>
                </tr>
                <tr>
                    <th>Birth Date</th>
                    <td><input type="date" name="birth_date" value="<?php echo htmlspecialchars($personalInfo['birth_date']); ?>" class="form-control" required></td>
                </tr>
            </table>
            <button type="submit" name="update_personal_info" class="btn btn-primary">Update Information</button>
        </form>
    </div>

    <div class="container-fluid">
        <div id="mySidenav" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="#" onclick="grades()">Show Grades</a>
            <a href="#" onclick="info()">Update Information</a>
            <a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
        </div>
    </div>

<script src="studentscript.js"></script>
</body
