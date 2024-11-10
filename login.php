<?php
// login.php
session_start();
require_once 'Login.php';
require_once 'database.php'; 

class Login extends Database {
    public function loginUser($username, $password) {
        try {
            // Prepare statement to check for user credentials
            $stmt = $this->getDb()->prepare("SELECT id, username, role FROM users WHERE username = :username AND password = :password");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Login failed: " . $e->getMessage();
            return false;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="shortcut icon" href="image/cpclogo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="login.css">  
</head>
<body>

<div class="container">
    <div class="row d-flex justify-content-center mt-5">
    <div class="containers col-md-5 position-relative">
    <img src="image/cpclogo.png" alt="" class="bg-white rounded-circle" style="width:100%;">
    <h2 class="centered text-center bg-dark bg-opacity-50 w-100 p-3 position-absolute top-50 start-50 translate-middle">
        <span class="text-warning">Welcome to</span> 
        <span class="effects text-info"><br>Cordova Public College</span>
    </h2>
</div>
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body bg-warning rounded-2 border border-3 border-dark">
                    <h4 class="card-title text-center mb-4">Login</h4>

                    <form action="login.php" method="POST">
                        <div class="p-3">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control"  required>
                        </div>
                        

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control"  required>
                        </div>

                        
                    <?php if (isset($errorMsg)): ?>
                        <div class="alert alert-danger text-center" role="alert">
                            <?php echo htmlspecialchars($errorMsg); ?>
                        </div>
                    <?php endif; ?>
                        <div class="text-center">
    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login = new Login();
    $user = $login->loginUser($username, $password);

    if ($user) {
        // Set session variables
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['user_id'] = $user['id'];

        // Redirect to the appropriate dashboard
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit();
    } else {
        echo "<p style='color:red'>Invalid username or password.</p>";
    }
}?>
                        </div>
                        <div class="d-grid text-center">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                        </div>
                        <div class="text-center mt-3">
                            <p>Don't have an account? <a href="student_registration.php">Register</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="login.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-Qe+4xDdD5KL/JstcJxUHzPzxTZrgt4GZT2CCylz7b2G9rVu9p8Gnsr93cF/TLh4M" crossorigin="anonymous"></script>
</body>
</html>
