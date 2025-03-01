<?php
include '../active.php';
?>

<?php

function renameFolder($oldName, $newName) {
    $oldPath = "uploads/" . $oldName;
    $newPath = "uploads/" . $newName;
    
    if (file_exists($oldPath)) {
        return rename($oldPath, $newPath) ? "Folder renamed successfully" : "Error renaming folder";
    }
    return "Folder does not exist";
}

function deleteFolder($folder) {
    if (!is_dir($folder)) return false;
    foreach (scandir($folder) as $file) {
        if ($file !== '.' && $file !== '..') {
            $filePath = "$folder/$file";
            is_dir($filePath) ? deleteFolder($filePath) : unlink($filePath);
        }
    }
    return rmdir($folder);
}

function removeFolder($folderName) {
    $folder = "uploads/" . $folderName;
    if (file_exists($folder)) {
        return deleteFolder($folder) ? "Folder deleted successfully" : "Error deleting folder";
    }
    return "Folder does not exist";
}

function uploadFile($file, $folder) {
    $targetFolder = "uploads/" . $folder . "/";
    
    if (!is_dir($targetFolder)) {
        mkdir($targetFolder, 0777, true);
    }
    
    $filePath = $targetFolder . basename($file['name']);
    return move_uploaded_file($file['tmp_name'], $filePath) ? "File uploaded successfully" : "Error uploading file";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'rename':
                if (isset($_POST['oldName']) && isset($_POST['newName'])) {
                    echo renameFolder($_POST['oldName'], $_POST['newName']);
                } else {
                    echo "Invalid rename request";
                }
                break;
            case 'delete':
                if (isset($_POST['folderName'])) {
                    echo removeFolder($_POST['folderName']);
                } else {
                    echo "Invalid delete request";
                }
                break;
            case 'upload':
                if (isset($_FILES['file']) && isset($_POST['folder'])) {
                    echo uploadFile($_FILES['file'], $_POST['folder']);
                } else {
                    echo "Invalid upload request";
                }
                break;
            default:
                echo "Invalid action";
        }
    } else {
        echo "No action specified";
    }
} else {
    echo "Invalid request method";
}

?>
