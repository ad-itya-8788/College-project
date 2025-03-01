<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <style>
    html, body {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      font-family: Arial, sans-serif;
    }

    body {
      background-color: rgb(145, 178, 211);
    }

    .header {
      background-color: #f4f4f4;
      border-bottom: 2px solid black;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
    }

    .logo img {
      height: 60px;
    }

    .content {
      padding: 20px;
    }

    table {
      width: 100%;
      background-color: white;
      margin-top: 20px;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      text-align: left;
      border: 1px solid #ddd;
    }

    th {
      background-color: rgb(58, 62, 67);
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    tr:hover {
      background: #f5f5;
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

    footer {
      background-color: #343a40;
      color: white;
      text-align: center;
      padding: 10px 0;
    }

    footer a {
      color: #ffc107;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }

    .container-fluid {
      padding: 0;
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
      <div>
        <a href="courses.php"><button class="btn-primary">Back</button></a>
      </div>
    </header>

    <!-- Content -->
    <div class="content">
      <?php
      include 'dbconnect.php';

      // fetch teachers name and course name from tables
      $assign_sql = "
        SELECT course_name, 
               course_code, 
               (SELECT string_agg(teacher_name, ', ') 
                FROM teachers 
                WHERE teacher_id IN 
                    (SELECT teacher_id 
                     FROM course_teacher 
                     WHERE course_code = course.course_code)) AS teacher_names
        FROM course;
      ";

      $assignments_result = pg_query($conn, $assign_sql);
      ?>

      <table>
        <thead>
          <tr>
            <th colspan="3" style="text-align:center; color:green;">Teacher & Course</th>
          </tr>
          <tr>
            <th>Course Name</th>
            <th>Teacher Names</th>
            <th>Manage</th>
          </tr>
        </thead>
        <tbody>
          <?php
          while ($assignment = pg_fetch_assoc($assignments_result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($assignment['course_name']) . "</td>";
            echo "<td>" . htmlspecialchars($assignment['teacher_names']) . "</td>";
            echo "<td><a href='manageteachercourse.php?course_code=" . urlencode($assignment['course_code']) . "' class='btn-primary'>Manage</a></td>";
            echo "</tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <!-- Footer -->
    <footer>
      <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank">Aditya Chavan</a></p>
    </footer>
  </div>
</body>
</html>
