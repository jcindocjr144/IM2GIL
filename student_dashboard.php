<?php
// student_dashboard.php
session_start();
require_once 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
class Student extends Database {
    public function getGrades($user_id) {
        $stmt = $this->getDb()->prepare("SELECT s.subject_name, g.grade 
                                         FROM grades g
                                         JOIN subjects s ON g.subject_id = s.id
                                         WHERE g.student_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function getPersonalInfo($user_id) {
        $stmt = $this->getDb()->prepare("SELECT id, username, password, first_name, last_name, middle_name, birth_date 
                                         FROM users
                                         WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePersonalInfo($user_id, $username, $password, $first_name, $last_name, $middle_name, $birth_date) {
        $stmt = $this->getDb()->prepare("UPDATE users SET username = :username, password = :password, 
                                          first_name = :first_name, last_name = :last_name, middle_name = :middle_name, birth_date = :birth_date 
                                          WHERE id = :user_id");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password); // Consider hashing passwords
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':birth_date', $birth_date);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
}


$student = new Student();
$grades = $student->getGrades($_SESSION['user_id']);
$personalInfo = $student->getPersonalInfo($_SESSION['user_id']);$grades = $student->getGrades($_SESSION['user_id']);
$personalInfo = $student->getPersonalInfo($_SESSION['user_id']);



// Handle form submission for updating personal info
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_personal_info'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_name = $_POST['middle_name'];
    $birth_date = $_POST['birth_date'];

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
                        // Determine status based on the grade
                        $gradeValue = $grade['grade'];
                        if ($gradeValue == 0) { 
                            echo "Incomplete"; // Exactly 0 is Incomplete
                        } elseif ($gradeValue > 0 && $gradeValue < 1) { 
                            echo "Invalid output"; // Greater than 0 but less than 1 is Invalid output
                        } elseif ($gradeValue == 1 || ($gradeValue > 1 && $gradeValue <= 3)) { 
                            echo "Passed"; // Exactly 1 or between 1 and 3 is Passed
                        } elseif ($gradeValue > 3 && $gradeValue <= 5) { 
                            echo "Failed"; // Between 3 and 5 is Failed
                        } else {
                            echo "Invalid output"; // Outside these ranges is Invalid output
                        }
                        
                        
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

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

<script src="studentscript.js"></script>
</body>
</html>