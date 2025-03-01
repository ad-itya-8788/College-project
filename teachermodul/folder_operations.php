<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../active.php';
include '../dbconnect.php';

// Check if the teacher is logged in
$teacher_id = $_SESSION['user_id'] ?? '';
if (!$teacher_id) 
{
    die("❌ Error: Teacher is not logged in.");
}

// Get action and other inputs from POST
$action = $_POST['action'] ?? '';
$ccode = $_POST['ccode'] ?? '';
$year = $_POST['year'] ?? '';

// Map year to folder name
$yearMapping = ["1" => "1st_year", "2" => "2nd_year", "3" => "3rd_year"];
$yearFolder = $yearMapping[$year] ?? '';

// Validate inputs
if (!$ccode || !$year || !$yearFolder)
{
    die("❌ Error: Course code or year is missing.");
}

// Define base directory
$base = realpath("../Assignments/") . "/$ccode/$yearFolder/";
if (!is_dir($base)) 
{
    die("❌ Error: Invalid course or year.");
}

try {
    // Perform actions based on the request
    switch ($action) 
    {
        case 'create':
            $folderName = $_POST['folderName'] ?? '';
            if (!$folderName) 
            {
                throw new Exception("❌ Error: Folder name is required.");
            }
            $newFolderPath = "$base/$folderName";
            if (file_exists($newFolderPath)) 
            {
                throw new Exception("❌ Error: Folder '$folderName' already exists.");
            }
            if (!mkdir($newFolderPath, 0777, true)) 
            {
                throw new Exception("❌ Error: Failed to create folder '$folderName'.");
            }

            // Insert folder path into the database
            $folderPath = "$ccode/$yearFolder/$folderName/";
            $query = "INSERT INTO notes (course_code, teacher_id, year, file_path) 
                      VALUES ($1, $2, $3, $4)";
            $result = pg_query_params($conn, $query, [$ccode, $teacher_id, $year, $folderPath]);
            if (!$result)
            {
                throw new Exception("❌ Error: Failed to insert into database. " . pg_last_error($conn));
            }

            echo "✅ Folder '$folderName' created successfully and added to database.";
            break;

        case 'rename':
            $oldFolderName = $_POST['oldFolderName'] ?? '';
            $newFolderName = $_POST['newFolderName'] ?? '';
            if (!$oldFolderName || !$newFolderName) 
            {
                throw new Exception("❌ Error: Old and new folder names are required.");
            }
            if (!is_dir("$base/$oldFolderName")) 
            {
                throw new Exception("❌ Error: Folder '$oldFolderName' does not exist.");
            }

            // Rename the folder
            if (rename("$base/$oldFolderName", "$base/$newFolderName"))
             {
                // Update the database with the new folder path
                $oldPath = "$ccode/$yearFolder/$oldFolderName";
                $newPath = "$ccode/$yearFolder/$newFolderName";
                $query = "UPDATE notes SET file_path = REPLACE(file_path, $1, $2) 
                          WHERE course_code = $3 AND year = $4";
                $result = pg_query_params($conn, $query, [$oldPath, $newPath, $ccode, $year]);
                if (!$result) 
                {
                    throw new Exception("❌ Error: Failed to update database. " . pg_last_error($conn));
                }

                echo "✅ Folder renamed to '$newFolderName' successfully and updated in database.";
            }
             else 
            {
                throw new Exception("❌ Error: Failed to rename folder.");
            }
            break;

        case 'delete':
            $folderName = $_POST['folderName'] ?? '';
            if (!$folderName) 
            {
                throw new Exception("❌ Error: Folder name is required.");
            }
            $folderPath = "$base/$folderName";
            if (!is_dir($folderPath)) 
            {
                throw new Exception("❌ Error: Folder '$folderName' does not exist.");
            }

            // Delete all files in the folder
            $files = glob("$folderPath/*");
            foreach ($files as $file)
             {
                if (is_file($file)) 
                {
                    unlink($file);
                }
            }

            // Delete the folder
            if (rmdir($folderPath)) 
            {
                // Delete the folder path from the database
                $filePath = "$ccode/$yearFolder/$folderName";
                $query = "DELETE FROM notes WHERE file_path LIKE $1";
                $result = pg_query_params($conn, $query, ["$filePath%"]);
                if (!$result) 
                {
                    throw new Exception("❌ Error: Failed to delete from database. " . pg_last_error($conn));
                }

                echo "✅ Folder '$folderName' deleted successfully from system and database.";
            } 
            else
            {
                throw new Exception("❌ Error: Failed to delete folder.");
            }
            break;

        case 'upload':
            $folderName = $_POST['folderName'] ?? '';
            if (!$folderName) 
            {
                throw new Exception("❌ Error: Folder name is required.");
            }
            if (!is_dir("$base/$folderName")) 
            {
                throw new Exception("❌ Error: Folder '$folderName' does not exist.");
            }
            if (!isset($_FILES['files'])) 
            {
                throw new Exception("❌ Error: No files selected for upload.");
            }

            // Process each uploaded file
            foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) 
            {
                $file_name = $_FILES['files']['name'][$key];
                $destination = "$base/$folderName/$file_name";
                if (!move_uploaded_file($tmp_name, $destination)) 
                {
                    throw new Exception("❌ Error: Failed to upload '$file_name'.");
                }

                // Insert file path into the database
                $filePath = "$ccode/$yearFolder/$folderName/$file_name";
                $query = "INSERT INTO notes (course_code, teacher_id, year, file_path) 
                          VALUES ($1, $2, $3, $4)";
                $result = pg_query_params($conn, $query, [$ccode, $teacher_id, $year, $filePath]);
                if (!$result) 
                {
                    throw new Exception("❌ Error: Failed to insert into database. " . pg_last_error($conn));
                }
            }

            echo "✅ Files uploaded successfully and stored in database.";
            break;

        default:
            throw new Exception("❌ Error: Invalid action.");
    }
} catch (Exception $e) 
{
    echo $e->getMessage();
}
?>