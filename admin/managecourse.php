<?php
require '../active.php';
include 'dbconnect.php';

// Initialize variables for the form and messages
$course_code = $course_name = $description = $duration = $dept_code = $course_coordinator = "";
$successMessage = $errorMessage = "";

// Function to safely escape input values
function sanitizeInput($input) 
{
    global $conn;
    return pg_escape_string($conn, trim($input));
}

if (isset($_GET['id'])) 
{
    $course_code = sanitizeInput($_GET['id']);

    // Fetch course data based on course code
    $sql = "SELECT * FROM course WHERE course_code = $1";
    $result = pg_query_params($conn, $sql, array($course_code));

    if ($result && pg_num_rows($result) > 0) 
    {
        $course = pg_fetch_assoc($result);
        $course_name = $course['course_name'];
        $description = $course['description'];
        $duration = $course['duration'];
        $dept_code = $course['dept_code'];
        $course_coordinator = $course['course_coordinator']; 
    }
    else 
    {
        $errorMessage = "No course found with this code.";
    }
} 
else
{
    $errorMessage = "Invalid course ID!";
}

// Fetch the list of teachers name to assign co-ordinator
$teachers_sql = "SELECT teacher_id, teacher_name FROM teachers";
$teachers_result = pg_query($conn, $teachers_sql);
$teachers = [];
while ($row = pg_fetch_assoc($teachers_result)) {
    $teachers[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $course_code = sanitizeInput($_POST['course_code']);
        $course_name = sanitizeInput($_POST['course_name']);
        $description = sanitizeInput($_POST['description']);
        $duration = sanitizeInput($_POST['duration']);
        $dept_code = sanitizeInput($_POST['dept_code']);
        $course_coordinator = sanitizeInput($_POST['course_coordinator']);

        $update_sql = "UPDATE course SET
                        course_name = $1,
                        description = $2,
                        duration = $3,
                        dept_code = $4,
                        course_coordinator = $5
                       WHERE course_code = $6";

        if (pg_query_params($conn, $update_sql, array($course_name, $description, $duration, $dept_code, $course_coordinator, $course_code))) {
            $successMessage = "Course updated successfully!";
            header("Refresh: 1; url=courses.php");
            exit();
        } else {
            $errorMessage = "Error: Could not update course.";
        }
    }

    if (isset($_POST['delete'])) {
        $course_code = sanitizeInput($_POST['course_code']);

        $delete_sql = "DELETE FROM course WHERE course_code = $1";

        if (pg_query_params($conn, $delete_sql, array($course_code))) {
            $successMessage = "Course deleted successfully!";
            header("Refresh: 1; url=courses.php");
            exit();
        } else {
            $errorMessage = "Error: Could not delete course.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Course</title>
    <link href="bootstrap.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
        }

        .container-fluid {
            padding: 20px;
        }

        .header {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .back-btn {
            background-color: #007bff;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        footer a {
            color: #ffc107;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin: 0 auto;
            padding: 20px;
        }

        form input,
        form textarea,
        form select {
            width: 100%;
            padding: 4px;
            margin: 10px 0;
        }

        form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        form button:hover {
            background-color: #0056b3;
        }

        form .delete-btn {
            background-color: #dc3545;
        }

        form .delete-btn:hover {
            background-color: #c82333;
        }

        .error {
            color: red;
            text-align: center;
        }

        .success {
            color: green;
            text-align: center;
        }

        .container-fluid {
            padding: 0px;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <header class="header">
            <div class="logo">
                <a href="https://moderncollegepune.edu.in/">
                    <img src="../photos/modernlogo.png" alt="Modern College Logo" height="60">
                </a>
            </div>
            <div>
                <a href="courses.php" class="back-btn">Back</a>
            </div>
        </header>

        <!-- Display success or error messages -->
        <?php if (!empty($successMessage)): ?>
            <p class="success"><?php echo $successMessage; ?></p>
        <?php elseif (!empty($errorMessage)): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <!-- Course editing form -->
        <?php if (!empty($course_code)): ?>
            <form action="managecourse.php" method="POST">
                <label for="course_code">Course Code</label>
                <input type="text" name="course_code" value="<?php echo htmlspecialchars($course_code); ?>" readonly /><br>

                <label for="course_name">Course Name</label>
                <input type="text" name="course_name" value="<?php echo htmlspecialchars($course_name); ?>" required /><br>

                <label for="dept_code">Department Code</label>
                <input type="text" name="dept_code" value="<?php echo htmlspecialchars($dept_code); ?>" readonly required /><br>

                <label for="duration">Duration</label>
                <input type="text" name="duration" value="<?php echo htmlspecialchars($duration); ?>" required /><br>

                <label for="course_coordinator">Course Coordinator</label>
                <select name="course_coordinator" required>
                    <option value="">Select Course Coordinator</option>
                    <?php foreach ($teachers as $teacher): ?>
                        <option value="<?php echo htmlspecialchars($teacher['teacher_id']); ?>" <?php echo ($teacher['teacher_id'] == $course_coordinator) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($teacher['teacher_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="description">Description</label>
                <textarea name="description" required><?php echo htmlspecialchars($description); ?></textarea><br>

                <div class="button-container">
                    <button type="submit" name="update">Update Course</button>
                    <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete this course?');">Delete Course</button>
                </div>
            </form>
        <?php else: ?>
            <p class="error">No course found to edit!</p>
        <?php endif; ?>

        <footer>
            <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank">Aditya Chavan</a></p>
        </footer>

    </div>

</body>

</html>
