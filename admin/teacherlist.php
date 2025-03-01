<?php include '../active.php'?>

<?php
include 'dbconnect.php';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT t.teacher_id, t.teacher_name, t.qualification, t.email, t.department, t.phone, t.address, t.time, d.department_name
          FROM teachers t
          LEFT JOIN departments d ON t.department = d.department_code";

if ($searchTerm) {
    $query .= " WHERE t.teacher_name ILIKE '%$searchTerm%' 
                OR t.email ILIKE '%$searchTerm%' 
                OR d.department_name ILIKE '%$searchTerm%'";
}

$result = pg_query($conn, $query);

if ($result) {
    if (pg_num_rows($result) > 0) {
        echo "<div class='table-container'>";
        echo "<table class='teacher-table' >";
        echo "<thead>";
        echo "<tr><th>Teacher ID</th><th>Name</th><th>Qualification</th><th>Email</th><th>Department</th><th>Phone</th><th>Address</th><th>Register Date & Time</th><th>Action</th></tr>";
        echo "</thead>";

        echo "<tbody>";
        while ($row = pg_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['teacher_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['teacher_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['qualification']) . "</td>";
            echo "<td><a href='mailto:" . htmlspecialchars($row['email']) . "'>" . htmlspecialchars($row['email']) . "</a></td>";
            echo "<td>" . htmlspecialchars($row['department_name']) . "</td>";
            echo "<td><a href='tel:" . htmlspecialchars($row['phone']) . "'>" . htmlspecialchars($row['phone']) . "</a></td>";
            echo "<td>" . htmlspecialchars($row['address']) . "</td>";
            echo "<td>" . htmlspecialchars($row['time']) . "</td>";
            echo "<td><a href='updateteacher.php?teacher_id=" . htmlspecialchars($row['teacher_id']) . "' class='btn btn-warning'>Manage</a></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<div class='alert'>No Results Found</div>";
    }
} else {
    echo "<div class='alert'>Query failed: " . pg_last_error($conn) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Teacher List</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f1f1f1;
      font-family:Tahoma;
    }

    .table-container {
      width: 100%;
      padding: 20px;
      box-sizing: border-box;
      font-size: 16px;
      background-color: #f1f1f1;
      overflow-x: auto;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    table {
      width: 100%;
      table-layout: auto;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    tr:hover {
      background-color: #f5f5 !important;
      color: black !important;
    }

    table th, table td {
      padding: 10px 15px;
      text-align: left;
    }

    table th {
      background-color: #e9ecef;
      font-weight: bold;
    }

    @media screen and (max-width: 768px) {
      table {
        display: block;
        width: 100%;
        overflow-x: auto;
       
      }

      table td, table th {
  
        padding: 8px 10px;
      }
    }

    .table-container {
      padding: 0px;
    }
  </style>
</head>
<body>
</body>
</html>
