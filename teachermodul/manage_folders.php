<?php
session_start(); // Start session to manage redirects

$base = __DIR__ . "/assignments";  // Path to the folder
$items = scandir($base);  // Get all files and folders in the "assignments" folder

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['rename'])) {
        $oldName = $_POST['old_name'];
        $newName = $_POST['new_name'];
        $oldPath = $base . "/" . $oldName;
        $newPath = $base . "/" . $newName;

        if (rename($oldPath, $newPath)) {
            $_SESSION['message'] = "Item renamed successfully!";
        } else {
            $_SESSION['message'] = "Failed to rename the item.";
        }
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to prevent re-submission
        exit;
    }

    if (isset($_POST['delete'])) {
        $itemToDelete = $_POST['item_to_delete'];
        $itemPath = $base . "/" . $itemToDelete;

        if (is_dir($itemPath)) {
            // Delete folder
            if (rmdir($itemPath)) {
                $_SESSION['message'] = "Folder deleted successfully!";
            } else {
                $_SESSION['message'] = "Failed to delete the folder. Ensure it is empty.";
            }
        } else {
            // Delete file
            if (unlink($itemPath)) {
                $_SESSION['message'] = "File deleted successfully!";
            } else {
                $_SESSION['message'] = "Failed to delete the file.";
            }
        }
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to prevent re-submission
        exit;
    }

    if (isset($_POST['create_folder'])) {
        $folderName = $_POST['folder_name'];
        $folderPath = $base . "/" . $folderName;

        if (!file_exists($folderPath)) {
            if (mkdir($folderPath, 0777, true)) {
                $_SESSION['message'] = "Folder created successfully!";
            } else {
                $_SESSION['message'] = "Failed to create the folder.";
            }
        } else {
            $_SESSION['message'] = "A folder with this name already exists.";
        }
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to prevent re-submission
        exit;
    }
}

// Display session message if set
if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-info'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']); // Clear the message after displaying
}

// Fetch folder contents
if (isset($_GET['folder'])) {
    $folder = $_GET['folder'];
    $folderPath = $base . "/" . $folder;

    if (is_dir($folderPath)) {
        $files = scandir($folderPath);
        echo "<h3>Contents of Folder: $folder</h3>";
        echo "<div class='row'>";
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $filePath = $folderPath . "/" . $file;
                $icon = is_dir($filePath) ? "📁" : "📄";
                echo "<div class='col-md-3 mb-3'>
                        <div class='card'>
                            <div class='card-body'>
                                <h5 class='card-title'>$icon $file</h5>
                            </div>
                        </div>
                      </div>";
            }
        }
        echo "</div>";
        echo "<br><a href='?' class='btn btn-primary'>Back to Folder List</a>";
        exit; // Exit to prevent further processing
    } else {
        echo "<div class='alert alert-danger'>The folder does not exist.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>File and Folder Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      padding: 20px;
    }
    .item-list {
      margin-top: 20px;
    }
    .item {
      margin-bottom: 10px;
      padding: 10px;
      background: white;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .form-container {
      display: none;
      margin-top: 20px;
      padding: 20px;
      background: white;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .add-folder-btn {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1 class="text-center mb-4">File and Folder Manager</h1>

    <!-- Add Folder Button -->
    <button class="btn btn-success add-folder-btn" onclick="showCreateFolderForm()">+ Create New Folder</button>

    <!-- Item List -->
    <div class="item-list">
      <h3>Items:</h3>
      <?php
      foreach ($items as $item) {
          if ($item != "." && $item != "..") {
              $itemPath = $base . "/" . $item;
              $icon = is_dir($itemPath) ? "📁" : "📄";
              echo "<div class='item'>
                      <span>$icon $item</span>
                      <div class='float-end'>
                        <a href='?folder=$item' class='btn btn-sm btn-primary'>Open</a>
                        <button class='btn btn-sm btn-warning' onclick='showRenameForm(\"$item\")'>Rename</button>
                        <button class='btn btn-sm btn-danger' onclick='showDeleteForm(\"$item\")'>Delete</button>
                      </div>
                    </div>";
          }
      }
      ?>
    </div>

    <!-- Create Folder Form -->
    <div id="createFolderForm" class="form-container">
      <form method="POST">
        <h3>Create New Folder</h3>
        <div class="mb-3">
          <label for="folderName" class="form-label">Folder Name:</label>
          <input type="text" class="form-control" name="folder_name" id="folderName" required>
        </div>
        <button type="submit" name="create_folder" class="btn btn-primary">Create Folder</button>
        <button type="button" class="btn btn-secondary" onclick="hideCreateFolderForm()">Cancel</button>
      </form>
    </div>

    <!-- Rename Form -->
    <div id="renameForm" class="form-container">
      <form method="POST">
        <h3>Rename Item</h3>
        <div class="mb-3">
          <label for="oldName" class="form-label">Old Name:</label>
          <input type="text" class="form-control" name="old_name" id="oldName" readonly>
        </div>
        <div class="mb-3">
          <label for="newName" class="form-label">New Name:</label>
          <input type="text" class="form-control" name="new_name" required>
        </div>
        <button type="submit" name="rename" class="btn btn-primary">Rename Item</button>
        <button type="button" class="btn btn-secondary" onclick="hideRenameForm()">Cancel</button>
      </form>
    </div>

    <!-- Delete Form -->
    <div id="deleteForm" class="form-container">
      <form method="POST">
        <h3>Are you sure you want to delete this item?</h3>
        <input type="hidden" name="item_to_delete" id="itemToDelete">
        <button type="submit" name="delete" class="btn btn-danger">Yes, Delete</button>
        <button type="button" class="btn btn-secondary" onclick="hideDeleteForm()">Cancel</button>
      </form>
    </div>
  </div>

  <script>
    // Function to show the create folder form
    function showCreateFolderForm() {
      document.getElementById('createFolderForm').style.display = 'block';
      document.getElementById('renameForm').style.display = 'none';
      document.getElementById('deleteForm').style.display = 'none';
    }

    // Function to hide the create folder form
    function hideCreateFolderForm() {
      document.getElementById('createFolderForm').style.display = 'none';
    }

    // Function to show the rename form and populate the item name
    function showRenameForm(item) {
      document.getElementById('oldName').value = item;
      document.getElementById('renameForm').style.display = 'block';
      document.getElementById('deleteForm').style.display = 'none';
      document.getElementById('createFolderForm').style.display = 'none';
    }

    // Function to show the delete confirmation form and populate the item name
    function showDeleteForm(item) {
      document.getElementById('itemToDelete').value = item;
      document.getElementById('deleteForm').style.display = 'block';
      document.getElementById('renameForm').style.display = 'none';
      document.getElementById('createFolderForm').style.display = 'none';
    }

    // Function to hide the rename form
    function hideRenameForm() {
      document.getElementById('renameForm').style.display = 'none';
    }

    // Function to hide the delete confirmation form
    function hideDeleteForm() {
      document.getElementById('deleteForm').style.display = 'none';
    }
  </script>
</body>
</html>