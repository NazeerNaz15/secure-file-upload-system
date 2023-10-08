<?php
session_start();
// Your database connection code here
$conn = mysqli_connect("localhost", "root", "", "secure_file_upload");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch data from the logs table
$sql = "SELECT * FROM `logs`";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

if (!isset($_SESSION['email'])) {

    if($_SESSION['email'] != "naseersafri5@gmail.com")
    header("Location: login.php");
    exit;
} 


//print_r($_SESSION)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/style.css" rel="stylesheet">
    <title>Logs Table</title>
    <style>
        
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<div class="d-flex header-wrap">
<h1>Logs Table</h1>
<a href="../file_upload.php" class="btn btn-primary">Back</a>
</div>
    
    <table>
        <thead>
            <tr>
                <th>Log ID</th>
                <th>User ID</th>
                <th>IP Address</th>
                <th>File Name</th>
                <th>Upload Timestamp</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop through the query results and display them in the table
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['user_id'] . "</td>";
                echo "<td>" . $row['ip_address'] . "</td>";
                echo "<td>" . $row['filename'] . "</td>";
                echo "<td>" . $row['upload_timestamp'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "</tr>";
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        </tbody>
    </table>
</body>
</html>
