<?php
include '../active.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
      color: #333;
    }

    a {
      text-decoration: none;
    }

    .header {
      background-color: #fff;
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .container {
      max-width: 1200px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h3 {
      margin-top: 0;
    }

    .table-container {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table th, table td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }

    table th {
      background-color: #f1f1f1;
    }

    tr:hover {
      background-color: #f5f5;
            transition: background-color 0.90s ease;

    }

    .assignment-actions {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-top: 20px;
    }

    .assignment-actions form label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    .assignment-actions button {
      background-color: #007bff;
      color: white;
      font-size: 16px;
      border: none;
      cursor: pointer;
    }

    .assignment-actions button:hover {
      background-color: #0056b3;
    }

    footer {
      background-color: #343a40;
      color: white;
      text-align: center;
      padding: 15px 0;
      margin-top: 20px;
    }

    footer a {
      color: #ffc107;
    }

    footer a:hover {
      text-decoration: underline;
    }

    #ass {
      height: 73px;
      width: 134px;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    img {
      height: 64px;
    }
  </style>
</head>

<body>
  <header class="header">
    <a href="https://moderncollegepune.edu.in/">
      <img src="../photos/modernlogo.png" alt="Modern College Logo">
    </a>
  </header>

  <div class="container">
    <h3>Student of Course 👇</h3>

    <!-- Year-wise Filter -->
    <form action="" method="POST">
      <label for="year">Filter by Year:</label>
      <select name="year" id="year">
        <option value="">Select Year</option>
        <option value="1" <?php if(isset($_POST['year']) && $_POST['year'] == '1') echo 'selected'; ?>>Year 1</option>
        <option value="2" <?php if(isset($_POST['year']) && $_POST['year'] == '2') echo 'selected'; ?>>Year 2</option>
        <option value="3" <?php if(isset($_POST['year']) && $_POST['year'] == '3') echo 'selected'; ?>>Year 3</option>
      </select>
      <label for="course_code">Course Code:</label>
      <input type="text" name="course_code" id="course_code" value="<?php echo isset($_POST['course_code']) ? htmlspecialchars($_POST['course_code']) : ''; ?>" placeholder="Enter Course Code">
      <button type="submit">Filter</button>
    </form>

    <?php
    include 'dbconnect.php';

    // Get the selected year and course code from the filter form
    $year = $_POST['year'] ?? '';
    $cd = $_POST['course_code'] ?? '';

    // Query to fetch students based on selected year and course_code
    if ($year != '') {
      // Query for the selected year and course code
      $sql = "SELECT * FROM student WHERE course_code = $1 AND year = $2";
      $params = [$cd, $year];
    } else {
      // If no year is selected, fetch all students for the course
      $sql = "SELECT * FROM student WHERE course_code = $1";
      $params = [$cd];
    }

    $result = pg_query_params($conn, $sql, $params);

    if ($result && pg_num_rows($result) > 0) {
      $course_name = htmlspecialchars($cd);
      echo "<div class='table-container'>
              <table>
                <tr><th colspan='10'>Students of the course: $course_name</th></tr>
                <tr>
                  <th>Enrollment</th>
                  <th>Name</th>
                  <th>Semester</th>
                  <th>Year</th>
                  <th>Course</th>
                  <th>Gender</th>
                  <th>Parent Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Date of Birth</th>
                </tr>";

      while ($row = pg_fetch_assoc($result)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['enrollment']) . "</td>
                <td>" . htmlspecialchars($row['sname']) . "</td>
                <td>" . htmlspecialchars($row['semester']) . "</td>
                <td>" . htmlspecialchars($row['year']) . "</td>
                <td>" . htmlspecialchars($row['course_name']) . "</td>
                <td>" . htmlspecialchars($row['gender']) . "</td>
                <td>" . htmlspecialchars($row['parent_name']) . "</td>
                <td><a href='mailto:" . htmlspecialchars($row['email']) . "'>" . htmlspecialchars($row['email']) . "</a></td>
                <td>" . htmlspecialchars($row['phone']) . "</td>
                <td>" . htmlspecialchars($row['dob']) . "</td>
              </tr>";
      }
      echo "</table>
            </div>";
    } else {
      echo "<p>No student records found for this course and year.</p>";
    }
    ?>
  </div>

  <footer>
    <p>&copy; 2024 Modern College. Developed by Aditya Chavan</p>
    <a href="https://moderncollegepune.edu.in">Visit Us</a>
  </footer>
</body>

</html>
