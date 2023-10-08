<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link href="./css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+6OVXsZzSxY4TLnGy/U5bBvuSfIVd1S7iM2DI3z5w5e4PM1I" crossorigin="anonymous">
</head>
<body>

<?php

session_start();

$duplicateError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve form data
    $email = $_POST["email"];
    $password = $_POST["password"];
   
if (login($email, $password)) {
    header("Location: file_upload.php");
    $_SESSION['errorMsg'] = "";
    $duplicateError = "";
} else {
        $_SESSION['username'] = "";
        $_SESSION['userid'] = "";
        $duplicateError = "Invalid credentials.";
}

}



function login($email, $password) {
    
    $conn = mysqli_connect("localhost", "root", "", "secure_file_upload");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $email = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT * FROM `user` WHERE `email` = '$email'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        mysqli_close($conn);
        return false;
    }

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        
        if (password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];

            mysqli_close($conn);
            return true;
        }
    }
    mysqli_close($conn);
    return false;
}


?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
            <?php
    if (!empty($duplicateError)) {
        echo '<div class="d-flex text-warning error-msg">' . $duplicateError . '</div>';
    }
    ?>
                <div class="card">
                    <div class="card-header">Please Login</div>
                    <div class="card-body">
                        <form action="login.php" method="POST">
                        <div class="mb-3 form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3 form-group">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-flex form-wrap">
                            
                            <a href="./register.php" class="btn btn-primary">Register</a>
                            <button type="submit" class="btn btn-primary">Login</button>
                           </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/js/bootstrap.min.js" integrity="sha384-fzj/xCBTfIu5Cz5dzzkK9eFq6IHjw6a5ZZf0EK+XnOtEIl9P7p5Bf5w5e4PM1IhF9I" crossorigin="anonymous"></script>
</body>
</html>
