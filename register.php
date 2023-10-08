<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- Include Bootstrap CSS (you should adjust the paths accordingly) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+6OVXsZzSxY4TLnGy/U5bBvuSfIVd1S7iM2DI3z5w5e4PM1I" crossorigin="anonymous">
    <!-- Include your custom CSS for styling -->
    <link href="./css/style.css" rel="stylesheet">
</head>
<body>

<?php

session_start();


$duplicateError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $username = $_POST["name"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    if (registerUser($username, $password, $email)) {

        $_SESSION['errorMsg'] = "";

        header("Location: file_upload.php");
        exit;
    } else {
        //echo "Registration failed.";
        $_SESSION['username'] = "";
        $_SESSION['userid'] = "";
        
        if($_SESSION['errorMsg']) {
            $duplicateError = $_SESSION['errorMsg'];
        }

    }

}





function registerUser($username, $password, $email) {

$dbhostname = "localhost";
$dbusername = "root";
$dbpassword = "";
$database_name = "secure_file_upload";
$conn = mysqli_connect($dbhostname, $dbusername, $dbpassword, $database_name);
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    $sql = "INSERT INTO `user` (`username`, `password`, `email`) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    try {
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashedPassword, $email);
           

    
            if (mysqli_stmt_execute($stmt)) {
                $user_id = mysqli_stmt_insert_id($stmt);
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
    
                // Store user information in the session
               
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
    
                return true;
            } else {
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return false;
            }
        } else {
            mysqli_close($conn);
            return false;
        }
    } catch (Exception $e) {
        // Handle the exception
       // echo "An error occurred: " . $e->getMessage();
         $_SESSION["errorMsg"] = $e->getMessage();
      
       return false;
      // echo $duplicateError;
       
    }
    
}
?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
            <?php
    if (!empty($duplicateError)) {
        echo '<div class="d-flex text-warning error-msg">' . $duplicateError . '</div>';
    }
    ?>
              
                <div class="card">
                    <div class="card-header">User Registration</div>
                    <div class="card-body">
                        <form action="register.php" method="POST">
                            <div class="mb-3 form-group">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3 form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                      
                            <div class="mb-3 form-group">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                          
                            <div class="d-flex form-wrap">
                           
                            <a href="./login.php" class="btn btn-primary">Login</a>
                            <button type="submit" class="btn btn-primary">Register</button>
                           </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/js/bootstrap.min.js" integrity="sha384-fzj/xCBTfIu5Cz5dzzkK9eFq6IHjw6a5ZZf0EK+XnOtEIl9P7p5Bf5w5e4PM1IhF9I" crossorigin="anonymous"></script>
</body>
</html>
