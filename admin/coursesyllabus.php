<?php
include 'dbconnect.php';

$successMessage = $errorMessage = "";

$course_codes = pg_query($conn, "SELECT course_code, course_name FROM course");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) 
{
    $course_code = $_POST['course_code'];
    $files = $_FILES['syllabus_pdf'];

    // Check if files are selected
    if (!empty($files['name'])) {
        $upload_dir = 'D:\xampp\htdocs\College project\coursesyllabus\\';

        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create directory recursively
        }

        foreach ($files['name'] as $index => $file_name) {
            $file_temp = $files['tmp_name'][$index];

            if ($files['error'][$index] === 0) {
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Ensure only PDF files are processed
                if ($file_ext === 'pdf') {
                    $unique_name = uniqid('', true) . '.' . $file_ext;
                    $file_path = $upload_dir . $unique_name;

                    if (move_uploaded_file($file_temp, $file_path)) {
                        $escaped_path = pg_escape_literal($conn, $file_path);
                        $escaped_course_code = pg_escape_literal($conn, $course_code);
                        $insert_sql = "INSERT INTO course_syllabus (course_code, syllabus_pdf_path) VALUES ($escaped_course_code, $escaped_path)";
                        if (pg_query($conn, $insert_sql)) {
                            $successMessage = "Syllabus uploaded successfully!";
                        } else {
                            $errorMessage = "Failed to save the syllabus in the database.";
                        }
                    } else {
                        $errorMessage .= "Failed to upload the file: $file_name<br>";
                    }
                } else {
                    $errorMessage .= "$file_name is not a valid PDF file.<br>";
                }
            } else {
                $errorMessage .= "Error uploading file: $file_name.<br>";
            }
        }
    } else {
        $errorMessage = "No file selected!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Syllabus for Course</title>
    <link href="bootstrap.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2>Upload Syllabus for Course</h2>

    <!-- Display success or error messages -->
    <?php 
    if ($successMessage) {
        echo "<p class='success'>$successMessage</p>";
    } elseif ($errorMessage) {
        echo "<p class='error'>$errorMessage</p>";
    }
    ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="course_code">Select Course</label>
        <select name="course_code" required>
            <option value="">Select Course</option>
            <?php while ($row = pg_fetch_assoc($course_codes)): ?>
                <option value="<?= $row['course_code'] ?>"><?= $row['course_code'] . ' - ' . $row['course_name'] ?></option>
            <?php endwhile; ?>
        </select>
        
        <label for="syllabus_pdf">Upload Syllabus (Multiple PDFs allowed)</label>
        <input type="file" name="syllabus_pdf[]" multiple required>

        <button type="submit" name="submit">Upload</button>
    </form>
</div>
</body>
</html>
