<?php
include 'dbconnect.php';

if (!isset($_POST['folder'])) {
    die("No folder selected!");
}

$folder_path = $_POST['folder'];

if (!is_dir($folder_path)) {
    die("Invalid folder!");
}

// Get contents of folder
$items = array_diff(scandir($folder_path), array('.', '..'));

$folders = [];
$files = [];
$file_details = [];

// Fetch file details from DB
$folder_name = basename($folder_path);
$query = "SELECT file_path, teacher_id, upload_data FROM notes WHERE file_path LIKE $1";
$result = pg_query_params($conn, $query, ["%".$folder_name."%"]);

while ($row = pg_fetch_assoc($result)) {
    $file_name = basename($row['file_path']);
    $file_details[$file_name] = [
        'teacher_id' => $row['teacher_id'],
        'upload_data' => $row['upload_data']
    ];
}

foreach ($items as $item) {
    $full_path = $folder_path . "/" . $item;
    if (is_dir($full_path)) {
        $folders[] = $item;
    } else {
        $files[] = $item;
    }
}

function get_teacher_name($conn, $teacher_id) {
    $query = "SELECT teacher_name FROM teachers WHERE teacher_id = $1";
    $result = pg_query_params($conn, $query, [$teacher_id]);
    if ($result && $row = pg_fetch_assoc($result)) {
        return $row['teacher_name'];
    }
    return 'Unknown';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="bootstrap.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            background-image: url("bludegrd.jpg");
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background-color: rgba(13, 12, 12, 0.9);
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header img {
            height: 50px;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        .logout-btn:hover {
            background: #b02a37;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            margin-bottom: 20px;
            flex-grow: 1;
            overflow-x: auto;
        }
        table {
            width: 100%;
            margin-top: 15px;
        }
        th {
            background: #007bff;
            color: white;
        }
        .btn-sm {
            padding: 5px 10px;
        }
        footer {
            background:rgb(25, 26, 26);
            color: white;
            text-align: center;
            padding: 10px;
            width: 100%;
        }

        table {
    width: 100%;
    margin-top: 15px;
}
    </style>
</head>
<body>
    <header class="header">
        <a href="https://moderncollegepune.edu.in/">
            <img src="../photos/modernlogo.png" alt="Modern College Logo">
        </a>
        <a href="index.php" class="logout-btn">Back</a>
    </header>

    <div class="container">
        <h2 class="text-primary">Contents of <?php echo htmlspecialchars(basename($folder_path)); ?></h2>
        <?php if (!empty($folders) || !empty($files)): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Uploaded By</th>
                        <th>Upload Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($folders as $folder): ?>
                        <tr>
                            <td>📂 <?php echo htmlspecialchars($folder); ?></td>
                            <td>Folder</td>
                            <td>-</td>
                            <td>
                                <form action="openfolder.php" method="post">
                                    <input type="hidden" name="folder" value="<?php echo htmlspecialchars($folder_path . '/' . $folder); ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">Open</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php foreach ($files as $file): 
                        $file_name = basename($file);
                        $teacher_id = $file_details[$file_name]['teacher_id'] ?? 'Unknown';
                        $upload_date = $file_details[$file_name]['upload_data'] ?? 'Unknown';
                        $teacher_name = ($teacher_id !== 'Unknown') ? get_teacher_name($conn, $teacher_id) : 'Unknown';
                    ?>
                        <tr>
                            <td>📄 <?php echo htmlspecialchars($file); ?></td>
                            <td><?php echo htmlspecialchars($teacher_name); ?></td>
                            <td><?php echo htmlspecialchars($upload_date); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($folder_path . '/' . $file); ?>" target="_blank" class="btn btn-success btn-sm">Open</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-muted">No files or folders found.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank" class="text-warning">Aditya Chavan</a></p>
    </footer>
</body>
</html>