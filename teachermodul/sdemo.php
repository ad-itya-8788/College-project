<?php
$base_dir = "assignments/";
if (!file_exists($base_dir)) {
    mkdir($base_dir, 0777, true);
}

function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function is_valid_file($file_name) {
    $allowed_extensions = ['pdf', 'docx', 'txt', 'png', 'jpg'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    return in_array($file_ext, $allowed_extensions);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_folder'])) {
        $folder_name = sanitize_input($_POST['folder_name']);
        $folder_name = basename($folder_name); // Prevent directory traversal
        $dir_path = $base_dir . $folder_name;
        
        if (!empty($folder_name) && !file_exists($dir_path)) {
            mkdir($dir_path, 0777, true);
            $message = "Folder created successfully!";
        } else {
            $message = "Invalid folder name or folder already exists.";
        }
    }

    if (isset($_POST['upload']) && isset($_FILES['file'])) {
        $folder_name = sanitize_input($_POST['folder_name']);
        $folder_name = basename($folder_name);
        $target_dir = $base_dir . $folder_name . "/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES["file"]["name"]);
        $target_file = $target_dir . $file_name;

        if (is_valid_file($file_name)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $message = "Assignment uploaded successfully!";
            } else {
                $message = "Error uploading file.";
            }
        } else {
            $message = "Invalid file type.";
        }
    }

    if (isset($_POST['delete_file'])) {
        $file_path = sanitize_input($_POST['file_path']);
        
        if (file_exists($file_path) && strpos($file_path, $base_dir) === 0) {
            unlink($file_path);
            $message = "Assignment deleted successfully!";
        } else {
            $message = "Error deleting file.";
        }
    }

    if (isset($_POST['delete_folder'])) {
        $folder_path = sanitize_input($_POST['folder_path']);
        
        if (is_dir($folder_path) && strpos($folder_path, $base_dir) === 0) {
            $files = array_diff(scandir($folder_path), ['.', '..']);
            foreach ($files as $file) {
                unlink("$folder_path/$file");
            }
            rmdir($folder_path);
            $message = "Folder deleted successfully!";
        } else {
            $message = "Error deleting folder.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background: #d4edda;
            color: #155724;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }
        .card {
            padding: 15px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .upload-btn {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }
        .upload-btn:hover {
            background: #0056b3;
        }
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Assignment Management</h2>
    
    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>
    
    <form action="" method="post">
        <label>Enter Folder Name:</label>
        <input type="text" name="folder_name" required>
        <button type="submit" name="create_folder" class="upload-btn">Create Folder</button>
    </form>

    <h3>Available Folders and Assignments:</h3>
    <div class="grid">
        <?php
        $folders = scandir($base_dir);
        foreach ($folders as $folder) {
            if ($folder !== '.' && $folder !== '..') {
                $safe_folder = htmlspecialchars($folder, ENT_QUOTES, 'UTF-8');
                echo "<div class='card'>";
                echo "<h3>$safe_folder 
                        <form action='' method='post' style='display:inline;'>
                            <input type='hidden' name='folder_path' value='$base_dir$safe_folder'>
                            <button type='submit' name='delete_folder' class='delete-btn'>Delete</button>
                        </form>
                      </h3>";
                echo "<ul>";

                $dir_path = $base_dir . $folder;
                if (is_dir($dir_path)) {
                    $files = scandir($dir_path);
                    foreach ($files as $file) {
                        if ($file !== '.' && $file !== '..') {
                            $safe_file = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
                            $file_path = $dir_path . '/' . $file;
                            echo "<li>
                                    <a href='$file_path' download>$safe_file</a>
                                    <form action='' method='post' style='display:inline;'>
                                        <input type='hidden' name='file_path' value='$file_path'>
                                        <button type='submit' name='delete_file' class='delete-btn'>Delete</button>
                                    </form>
                                  </li>";
                        }
                    }
                }

                echo "</ul>";

                echo "<form action='' method='post' enctype='multipart/form-data'>
                        <input type='hidden' name='folder_name' value='$safe_folder'>
                        <input type='file' name='file' required>
                        <button type='submit' name='upload' class='upload-btn'>Upload Assignment</button>
                      </form>";

                echo "</div>";
            }
        }
        ?>
    </div>
</div>

</body>
</html>
