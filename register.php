<?php
session_start();
require_once 'database.php';
class Register extends Database {
    public function registerUser($username, $password, $role, $firstName, $middleName, $lastName, $birthDate) {
        if ($this->getState()) {
            try {
                // Check for duplicate names and birth date
                $checkStmt = $this->getDb()->prepare("SELECT * FROM users WHERE first_name = :first_name AND middle_name = :middle_name AND last_name = :last_name AND birth_date = :birth_date");
                $checkStmt->bindParam(':first_name', $firstName);
                $checkStmt->bindParam(':middle_name', $middleName);
                $checkStmt->bindParam(':last_name', $lastName);
                $checkStmt->bindParam(':birth_date', $birthDate);
                $checkStmt->execute();

                if ($checkStmt->rowCount() > 0) {
                    return "This name and birth date combination is already registered. Please try another name or birth date.";
                }

                // Insert new user data
                $stmt = $this->getDb()->prepare("INSERT INTO users (username, password, role, first_name, middle_name, last_name, birth_date) VALUES (:username, :password, :role, :first_name, :middle_name, :last_name, :birth_date)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password); // Store password as plain text (consider hashing for security)
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':first_name', $firstName);
                $stmt->bindParam(':middle_name', $middleName);
                $stmt->bindParam(':last_name', $lastName);
                $stmt->bindParam(':birth_date', $birthDate);
                $stmt->execute();

                $_SESSION['registration_success'] = "Registration successful! Please log in.";
                header("Location: login.php");
                exit();
            } catch (PDOException $e) {
                if ($e->getCode() === '23000') {
                    return "Duplicate username/password.";
                } else {
                    return "Registration failed: " . $e->getMessage();
                }
            }
        } else {
            return $this->getErrMsg();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get the role from the form
    $firstName = $_POST['first_name'];
    $middleName = $_POST['middle_name'];
    $lastName = $_POST['last_name'];
    $birthDate = $_POST['birth_date'];


    $register = new Register();
    $errorMsg = $register->registerUser($username, $password, $role , $firstName, $middleName, $lastName, $birthDate);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="shortcut icon" href="image/cpclogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Register</h4>

                    <?php if (isset($errorMsg)): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <?php echo htmlspecialchars($errorMsg); ?>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <div class="p-3">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Select type of account:</label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="student">Student</option>
                                    <option value="admin">Teacher</option>
                                </select>
                            </div>

                            <!-- New fields for first, middle, last name, and birth date -->
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter first name" required>
                            </div>
                            <div class="mb-3">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="Enter middle name" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter last name" required>
                            </div>
                            <div class="mb-3">
                                <label for="birth_date" class="form-label">Birth Date</label>
                                <input type="date" name="birth_date" id="birth_date" class="form-control" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login.php">Log in</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
