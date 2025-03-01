<?php
include 'dbconnect.php';
include '../active.php';

$course_code = isset($_GET['course_code']) ? pg_escape_string($conn, $_GET['course_code']) : '';

if (empty($course_code)) {
    echo "Course code is missing!";
    exit;
}

// Fetch course details and assigned teachers
$sql = "
    SELECT 
        course_name, 
        (SELECT string_agg(teacher_name, ', ') 
         FROM teachers 
         WHERE teacher_id IN (
             SELECT teacher_id 
             FROM course_teacher 
             WHERE course_code = course.course_code
         )) AS teacher_names
    FROM course 
    WHERE course_code = '$course_code';
";

$result = pg_query($conn, $sql);
$course = pg_fetch_assoc($result);

if (!$course) {
    echo "No course found!";
    exit;
}

$course_name = htmlspecialchars($course['course_name']);
$teacher_names = htmlspecialchars($course['teacher_names'] ?? "None");

// Handle teacher assignment/removal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_teacher'])) {
        $new_teacher_id = pg_escape_string($conn, $_POST['new_teacher_id']);
        $sql_add_teacher = "INSERT INTO course_teacher (course_code, teacher_id) VALUES ('$course_code', '$new_teacher_id')";
        pg_query($conn, $sql_add_teacher);
    }

    if (isset($_POST['remove_teacher'])) {
        $teacher_id_to_remove = pg_escape_string($conn, $_POST['teacher_id']);
        $sql_remove_teacher = "DELETE FROM course_teacher WHERE course_code = '$course_code' AND teacher_id = '$teacher_id_to_remove'";
        pg_query($conn, $sql_remove_teacher);
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?course_code=$course_code");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <style>
     body {
    font-family: Arial, sans-serif;
    background-color: rgb(152, 225, 214);
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 2px;
}

h2, h3 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

th, td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #f4f4f4;
}

select, input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
}

input[type="submit"] {
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #0056b3;
}



.logo img {
    height: 60px;
}

footer {
    background-color: #343a40;
    color: white;
    text-align: center;
    padding: 10px 0;
    width: 100%;
}

footer a {
    color: #ffc107;
    text-decoration: none;
}

footer a:hover {
    text-decoration: underline;
}

.header {
    background-color: #f4f4f4;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    border-bottom:2px solid black;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 3px;
}

.btn-primary:hover {
    background-color: rgb(224, 32, 19);
}

    </style>
</head>
<body>

<header class="header">
    <div class="logo">
        <a href="https://moderncollegepune.edu.in/">
            <img src="../photos/modernlogo.png" alt="Modern College Logo">
        </a>
    </div>
    <div>
        <a href="courses.php"><button class="btn-primary">Back</button></a>
    </div>
</header>

<div class="container">
    <h2>Manage Teachers for: <?php echo $course_name; ?> (<?php echo htmlspecialchars($course_code); ?>)</h2>
    <h3>Assigned Teachers</h3>
    <table>
        <tr>
            <th>Teacher Name</th>
            <th>Action</th>
        </tr>
        <?php
        $assigned_teachers_query = "
            SELECT teacher_id, teacher_name 
            FROM teachers 
            WHERE teacher_id IN (
                SELECT teacher_id FROM course_teacher WHERE course_code = '$course_code'
            )
        ";
        $assigned_teachers_result = pg_query($conn, $assigned_teachers_query);

        if (pg_num_rows($assigned_teachers_result) > 0) {
            while ($assigned_teacher = pg_fetch_assoc($assigned_teachers_result)) {
                echo "<tr>
                    <td>" . htmlspecialchars($assigned_teacher['teacher_name']) . "</td>
                    <td>
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='teacher_id' value='" . $assigned_teacher['teacher_id'] . "'>
                            <input type='submit' name='remove_teacher' value='Remove'>
                        </form>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No teachers assigned</td></tr>";
        }
        ?>
    </table>

    <h3>Assign a New Teacher</h3>
    <form method="POST">
        <select name="new_teacher_id">
            <?php
            $teachers_query = "SELECT teacher_id, teacher_name FROM teachers";
            $teachers_result = pg_query($conn, $teachers_query);
            while ($teacher = pg_fetch_assoc($teachers_result)) {
                echo "<option value='" . $teacher['teacher_id'] . "'>" . htmlspecialchars($teacher['teacher_name']) . "</option>";
            }
            ?>
        </select>
        <input type="submit" name="add_teacher" value="Assign Teacher">
    </form>
</div>

<footer>
    <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank">Aditya Chavan</a></p>
</footer>

</body>
</html>
