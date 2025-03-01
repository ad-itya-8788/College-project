<?php
require '../active.php';
include 'dbconnect.php';

// Initialize variables for form data
$course_code = $course_name = $description = $duration = $dept_code = $course_coordinator = "";
$successMessage = $errorMessage = "";

// Sanitize input to prevent SQL injection
function sanitize($input) {
    global $conn;
    return pg_escape_string($conn, trim($input));
}

// Get department options
$departments = pg_query($conn, "SELECT department_code, department_name FROM departments");

// Get teacher options
$teachers = pg_query($conn, "SELECT teacher_id, teacher_name FROM teachers");

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    // Sanitize input
    $course_code = sanitize($_POST['course_code']);
    $course_name = sanitize($_POST['course_name']);
    $description = sanitize($_POST['description']);
    $duration = sanitize($_POST['duration']);
    $dept_code = sanitize($_POST['dept_code']);
    $course_coordinator = sanitize($_POST['course_coordinator']);
    
    // Check if course exists
    $check = pg_query($conn, "SELECT course_code FROM course WHERE course_code = '$course_code'");
    
    if (pg_num_rows($check) > 0) {
        $errorMessage = "Course with this code already exists!";
    } else {
        // Insert new course
        $insert = "INSERT INTO course (course_code, course_name, description, duration, dept_code, course_coordinator) 
                   VALUES ('$course_code', '$course_name', '$description', '$duration', '$dept_code', '$course_coordinator')";
        
        if (pg_query($conn, $insert)) {
            $successMessage = "Course added successfully!";
            header("Location: courses.php");
            exit();
        } else {
            $errorMessage = "Failed to add course.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Course</title>
    <link href="bootstrap.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            font-family: Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-bottom: 2px solid black;
        }

        .back-btn {
            padding: 5px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid black;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 30px;
        }

        footer a {
            color: #ffc107;
        }

        footer a:hover {
            text-decoration: underline;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form input,
        form select,
        form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            display: block;
            width: 100%;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 20px;
        }

        .success {
            color: green;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
  <header class="header">
        <div class="logo">
            <a href="https://moderncollegepune.edu.in/"><img src="../photos/modernlogo.png" alt="Modern College Logo" height="60"></a>
        </div>
        <div><a href="courses.php" class="back-btn">Back</a></div>
    </header>
<div class="container">
  

    <!-- Display success or error messages -->
 <?php 
    echo $successMessage ? "<p class='success'>$successMessage</p>" : ($errorMessage ? "<p class='error'>$errorMessage</p>" : '');
?>


    <form method="POST">
        <h2>Add New Course</h2>
        <label for="course_code">Course Code</label>
        <input type="text" name="course_code" value="<?= $course_code ?>" required>
        
        <label for="course_name">Course Name</label>
        <input type="text" name="course_name" value="<?= $course_name ?>" required>

        <label for="dept_code">Department</label>
        <select name="dept_code" required>
            <option value="">Select Department</option>
            <?php while ($row = pg_fetch_assoc($departments)): ?>
                <option value="<?= $row['department_code'] ?>" <?= $row['department_code'] == $dept_code ? 'selected' : '' ?>>
                    <?= $row['department_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="duration">Duration</label>
        <input type="text" name="duration" value="<?= $duration ?>" required>

        <label for="course_coordinator">Course Coordinator</label>
        <select name="course_coordinator" required>
            <option value="">Select Coordinator</option>
            <?php while ($row = pg_fetch_assoc($teachers)): ?>
                <option value="<?= $row['teacher_id'] ?>" <?= $row['teacher_id'] == $course_coordinator ? 'selected' : '' ?>>
                    <?= $row['teacher_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="description">Description</label>
        <textarea name="description" required><?= $description ?></textarea>

        <button type="submit" name="add">Add Course</button>
    </form>

    
</div>
<footer>
        <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank">Aditya Chavan</a></p>
    </footer>
</body>
</html>
