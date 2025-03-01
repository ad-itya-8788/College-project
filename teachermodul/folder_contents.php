<?php
include '../active.php';
include '../dbconnect.php';

// Set default timezone
date_default_timezone_set('Asia/Kolkata');

// Validate required POST parameters
if (!isset($_POST['ccode']) || !isset($_POST['year']))
 {
    die("<div class='alert alert-danger text-center' id='alert-message'>❌ Invalid request. Course code and year must be provided.</div>");
}

// Sanitize inputs
$ccode = htmlspecialchars($_POST['ccode']);
$year = htmlspecialchars($_POST['year']);
$folder = htmlspecialchars($_POST['folder'] ?? '');
$teacher_id = $_SESSION['user_id'];

// Map year to folder name
$yearMapping = ["1" => "1st_year", "2" => "2nd_year", "3" => "3rd_year"];
$yearFolder = $yearMapping[$year] ?? '';

// Define base directory
$base = realpath("../Assignments/") . "/$ccode/$yearFolder/$folder/";

// Ensure the folder exists
if (!is_dir($base)) 
{
    die("<div class='alert alert-danger text-center' id='alert-message'>❌ Folder not found.</div>");
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']))
 {
    $fileToDelete = basename($_POST['delete']);
    $filePath = "$base/$fileToDelete";

    if (file_exists($filePath)) 
    {
        // Delete the file from the server
        if (unlink($filePath))
         {
            // Delete the file record from the database
            $query = "DELETE FROM notes WHERE file_path = $1";
            $result = pg_query_params($conn, $query, ["$ccode/$yearFolder/$folder/$fileToDelete"]);

            if ($result)
             {
                echo "<div class='alert alert-success text-center' id='alert-message'>✅ File '$fileToDelete' deleted successfully.</div>";
            }
             else 
             {
                echo "<div class='alert alert-danger text-center' id='alert-message'>❌ File '$fileToDelete' was deleted from the server, but the database record could not be deleted. Error: " . pg_last_error($conn) . "</div>";
            }
        } 
        else
         {
            echo "<div class='alert alert-danger text-center' id='alert-message'>❌ Failed to delete file '$fileToDelete' from the server.</div>";
        }
    } 
    else
    {
        echo "<div class='alert alert-danger text-center' id='alert-message'>❌ File '$fileToDelete' does not exist.</div>";
    }
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadedFile = $_FILES['file'];
    $fileName = basename($uploadedFile['name']);
    $filePath = "$base/$fileName";

    // Validate file size (e.g., 100MB limit)
    if ($uploadedFile['size'] > 100 * 1024 * 1024) {
        die("<div class='alert alert-danger text-center' id='alert-message'>❌ File size exceeds 100MB limit.</div>");
    }

    // Move uploaded file to the target directory
    if (move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
        // Insert file record into the database
        $folderPath = "$ccode/$yearFolder/$folder/$fileName";
        $query = "INSERT INTO notes (course_code, teacher_id, year, file_path) VALUES ($1, $2, $3, $4)";
        $result = pg_query_params($conn, $query, [$ccode, $teacher_id, $year, $folderPath]);

        if ($result) {
            echo "<div class='alert alert-success text-center' id='alert-message'>✅ File '$fileName' uploaded and recorded successfully.</div>";
        } else {
            echo "<div class='alert alert-danger text-center' id='alert-message'>❌ Failed to record file '$fileName' in the database. Error: " . pg_last_error($conn) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger text-center' id='alert-message'>❌ Failed to upload file '$fileName'.</div>";
    }
}


// Get list of files in the folder
$files = array_diff(scandir($base), [".", ".."]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folder Contents</title>
    <link href="../bootstrap.css" rel="stylesheet">
    <style>
       body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .header {
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .folder-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #343a40;
        }
        .upload-card {
            max-width: 600px;
            margin: 20px auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .table-responsive {
            margin: 20px auto;
            max-width: 1000px;
        }
        .btn-view {
            background: #17a2b8;
            color: white;
        }
        .btn-delete {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <header class="header py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="#">
                <img src="../photos/modernlogo.png" alt="Modern College Logo" height="60">
            </a>
            <a href="class.php" class="btn btn-primary">Back</a>
        </div>
    </header>

    <div class="container my-5">
        <div class="folder-title text-center my-4">Files in Folder: <?php echo $folder; ?></div>

        <div class="upload-card card shadow">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <input type="hidden" name="ccode" value="<?php echo $ccode; ?>">
                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                    <input type="hidden" name="folder" value="<?php echo $folder; ?>">
                    <button type="submit" class="btn btn-success w-100">Upload File</button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Open</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($files as $file) {
                        $filePath = "$base/$file";
                        $fileDate = date("d-m-Y H:i:s", filemtime($filePath));

                        echo "<tr>
                                <td>$file</td>
                                <td>$fileDate</td>
                                <td><a href='../Assignments/$ccode/$yearFolder/$folder/$file' class='btn btn-view' target='_blank'>Open</a></td>
                                <td>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='delete' value='$file'>
                                        <input type='hidden' name='ccode' value='$ccode'>
                                        <input type='hidden' name='year' value='$year'> 
                                        <input type='hidden' name='folder' value='$folder'>
                                        <button type='submit' class='btn btn-delete' onclick=\"return confirm('Are you sure you want to delete this file?');\">Delete</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3">
        <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank" class="text-white">Aditya Chavan</a></p>
    </footer>

    <script src="boot.js"></script>
    <script>
        // Automatically hide the alert message after 3 seconds
        setTimeout(function() {
            const alertMessage = document.getElementById('alert-message');
            if (alertMessage) {
                alertMessage.style.display = 'none';
            }
        }, 3000); 
    </script>
</body>
</html>