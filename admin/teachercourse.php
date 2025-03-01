<?php
include 'dbconnect.php';
include '../active.php';

// Variable to store status messages
$status_message = "";

// Logic to assign a teacher to a course
if (!empty($_POST['course']) && !empty($_POST['teacher'])) {
  $course_id = $_POST['course'];
  $teacher_id = $_POST['teacher'];

  $sql = "INSERT INTO course_teacher (course_code, teacher_id) VALUES ($1, $2)";
  $result = @pg_query_params($conn, $sql, array($course_id, $teacher_id));

  if ($result) {
    $status_message = "Teacher successfully assigned to the course!";
  } else {
    $status_message = "Error: Unable to assign the teacher to the course.";
  }
} else {
  $status_message = "Please select both a course and a teacher.";
}

// Fetching courses and teachers for dropdown options
$course_sql = "SELECT course_code, course_name FROM course";
$course_result = pg_query($conn, $course_sql);

$teacher_sql = "SELECT teacher_id, teacher_name FROM teachers";
$teacher_result = pg_query($conn, $teacher_sql);
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
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background-image: url("bludegrd.jpg");
      background-repeat: no-repeat;
    }

    .container-fluid {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .header {
      background-color: rgb(244, 244, 244);
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
      padding: 10px 0;
      width: 100%;
    }

    .header .logo {
      margin: 0;
      padding: 0;
      flex: 1;
    }

    .header .logo img {
      height: 60px;
    }

    .container {
      width: 100%;
      max-width: 600px;
      padding: 20px;
      background-color: rgba(239, 245, 236, 0.97);
      margin-top: 20px;
      margin-bottom: 20px;
    }

    label {
      font-size: 16px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
    }

    footer {
      background-color: #343a40;
      color: white;
      text-align: center;
      padding: 10px 0;
      margin-top: auto;
      width: 100%;
    }

    footer a {
      color: #ffc107;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }

    .alert {
      color: red;
    }

    .container-fluid {
      padding: 0px;
    }
  </style>
</head>

<body>

  <div class="container-fluid">
    <!-- Header -->
    <header class="header">
      <div class="logo">
        <a href="https://moderncollegepune.edu.in/">
          <img src="../photos/modernlogo.png" alt="Modern College Logo">
        </a>
      </div>
    </header>

    <!-- Content -->
    <form action="" method="POST">
      <div class="container">
        <label for="course">Select Course:</label>
        <select name="course" id="course">
          <option value="">--Select Course--</option>
          <?php
          while ($row = pg_fetch_assoc($course_result)) {
            echo "<option value='" . $row['course_code'] . "'>" . $row['course_name'] . "</option>";
          }
          ?>
        </select>

        <label for="teacher">Select Teacher:</label>
        <select name="teacher" id="teacher">
          <option value="">--Select Teacher--</option>
          <?php
          while ($row = pg_fetch_assoc($teacher_result)) {
            echo "<option value='" . $row['teacher_id'] . "'>" . $row['teacher_name'] . "</option>";
          }
          ?>
        </select>
        <input type="submit" value="Assign Teacher to Course">
        <button onclick="window.location.href='teachers.html';">Go Back</button>

        <?php
        if ($status_message) {
          echo "<div class='alert'>" . $status_message . "</div>";
        }
        ?>
      </div>
    </form>

    <!-- Footer -->
    <footer>
      <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank">Aditya Chavan</a>
      </p>
    </footer>
  </div>

</body>

</html>