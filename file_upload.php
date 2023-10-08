<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <!-- Include your custom CSS for styling -->
    <link href="./css/style.css" rel="stylesheet">
</head>
<body>


<?php

session_start();


function handleFileUpload($user_id, $uploadDir) {


    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file"])) {

        $file = $_FILES["file"];

        // Check for file upload errors
        if ($file["error"] === UPLOAD_ERR_OK) {
            // Get the uploaded file name
            $filename = basename($file["name"]);

            // Generate a unique filename to prevent overwrites
            $uniqueFilename = uniqid() . "_" . $filename;

            // Set the target file path
            $targetFilePath = $uploadDir . "/" . $uniqueFilename;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
                // File upload successful, now insert data into 'uploads' table
                $upload_timestamp = date('Y-m-d H:i:s');
                $status = "Success";

                // Your database connection code
                $conn = mysqli_connect("localhost", "root", "", "secure_file_upload");

                if ($conn) {
                    // Insert into 'uploads' table
                    $sql = "INSERT INTO `uploads` (`user_id`, `filename`, `upload_timestamp`) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "iss", $user_id, $uniqueFilename, $upload_timestamp);
                        if (mysqli_stmt_execute($stmt)) {
                            // Insert into 'logs' table
                            $ip_address = $_SERVER['REMOTE_ADDR'];
                            $log_status = "Success";

                            $logSql = "INSERT INTO `logs` (`user_id`, `ip_address`, `filename`, `upload_timestamp`, `status`) VALUES (?, ?, ?, ?, ?)";
                            $logStmt = mysqli_prepare($conn, $logSql);

                            if ($logStmt) {
                                mysqli_stmt_bind_param($logStmt, "issss", $user_id, $ip_address, $uniqueFilename, $upload_timestamp, $log_status);
                                mysqli_stmt_execute($logStmt);
                            }
                        }
                        mysqli_stmt_close($stmt);
                    }
                    mysqli_close($conn);
                }
                
                return "File uploaded successfully.";
            } else {
                // File upload failed
                $status = "Rejected";
                $log_status = "Rejected";

                // Log the failed upload into 'logs' table
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $upload_timestamp = date('Y-m-d H:i:s');

                $conn = mysqli_connect("localhost", "username", "password", "database");

                if ($conn) {
                    $logSql = "INSERT INTO `logs` (`user_id`, `ip_address`, `filename`, `upload_timestamp`, `status`) VALUES (?, ?, ?, ?, ?)";
                    $logStmt = mysqli_prepare($conn, $logSql);

                    if ($logStmt) {
                        mysqli_stmt_bind_param($logStmt, "issss", $user_id, $ip_address, $filename, $upload_timestamp, $log_status);
                        mysqli_stmt_execute($logStmt);
                    }
                    mysqli_close($conn);
                }
               
                return "File upload failed.";
            }
        } else {
            return "Error: " . $file["error"];
        }
    }
    return "No file uploaded.";
}




if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
} 

$username = $_SESSION['username'];
$userid = $_SESSION['user_id'];
$userEmail = $_SESSION['email'];


$user_id = $userid; 
$uploadDir = "./uploads"; 
$result = handleFileUpload($user_id, $uploadDir);

if($result == "No file uploaded."){
    $result = "";
}





//echo $result;
?>
<div class="d-flex header-wrap">
<h1>Welcome <?php echo $username ?></h1>
<a href="./logout.php?logout=true" class="btn btn-primary">Logout</a>
</div>

    <div class="container">
        <div class="upload-container">

        <?php
    if (!empty($result)) {
        echo '<div class="d-flex sucess-msg text-success"><p>' . $result . '</p></div>';
    }
    ?>

<?php
    if (!empty($errorMsg)) {
        echo '<div class="d-flex error-msg text-danger"><p>' . $errorMsg . '</p></div>';
    }
    ?>


         
        
            <h2>Upload Your Files</h2>
            <form action="file_upload.php" method="POST" enctype="multipart/form-data">
                <label for="file-upload" class="custom-file-upload">
                    <i class="fas fa-cloud-upload-alt"></i> Choose a File
                </label>
                <input type="file" id="file-upload" name="file" accept=".jpg, .png, .pdf, .docx" required maxlength="5000000">
                <button type="submit" class="upload-button">Upload</button>
            </form>

            <?php
            if (!empty($userEmail == "naseersafri5@gmail.com")) {
                echo '<div class="d-flex dashboard-btn"><a href="./dashboard/index.php" class="btn btn-primary">View Logs</a></div>';
            }
            ?>
            <div id="previewContainer" class="preview-wrap"></div>
        </div>
    </div>

    <script>

        document.getElementById('file-upload').addEventListener('change', function (event) {
            const fileInput = event.target;
            const previewContainer = document.getElementById('previewContainer');
            
            if (fileInput.files && fileInput.files[0]) {
                const file = fileInput.files[0];
                const fileType = file.type;

                previewContainer.innerHTML = '';

                if (fileType.startsWith('image/')) {
                    
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.maxWidth = '100%';
                    previewContainer.appendChild(img);
                } else if (fileType === 'application/pdf') {
                    
                    const iframe = document.createElement('iframe');
                    iframe.src = URL.createObjectURL(file);
                    iframe.style.width = '100%';
                    iframe.style.height = '500px';
                    previewContainer.appendChild(iframe);
                } else {
                   
                    previewContainer.innerHTML = 'Unsupported file type';
                }
            } else {
                
                previewContainer.innerHTML = '';
            }
        });
    </script>
</body>
</html>