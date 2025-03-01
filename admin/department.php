<?php 
include 'dbconnect.php';
require '../active.php';

$search = '';
if (isset($_GET['search'])) {
    $search = pg_escape_string($conn, $_GET['search']);
}
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
            background-color: rgb(148, 180, 211);
            font-family: Tahoma;
        }
        .container-fluid { padding: 0; }
        .header {
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }
        .logo img { height: 60px; }
        .navbar a { margin-left: 10px; }
        .search-box {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            justify-content: flex-end;
        }
        .search-box input {
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 5px;
        }
        .table-container { overflow-x: auto; width: 100%; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th { background-color: #f2f2f2; }
        .btn {
            font-size: 14px;
            padding: 6px 12px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: rgb(188, 31, 42);
            transform: scale(1.1);
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

        .btn {
            font-size: 14px;
            padding: 6px 12px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: rgb(188, 31, 42);
            transform: scale(1.1);
        }
        @media screen and (max-width:768px)
        {
        .btn {
            font-size: 14px;
            padding: 6px 12px;
            margin-top:10px;
            font-size:10px;
            cursor: pointer;
        }   
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <header class="header">
            <div class="logo">
                <a href="https://moderncollegepune.edu.in/">
                    <img src="../photos/modernlogo.png" alt="Modern College Logo">
                </a>
            </div>
            <div class="navbar">
                <a href="deptinsert.php"><button class="btn btn-primary">New Department</button></a>
                <a href="index.php"><button class="btn btn-secondary">Back</button></a>
            </div>
        </header>

        <div class="container mt-4 mb-4">
       

            <?php
            $srno = 0;
            $sql = "SELECT * FROM departments";

            if ($search != '') {
                $sql .= " WHERE department_name ILIKE '%$search%'";
            }

            $result = pg_query($conn, $sql);

            if (!$result) {
                echo "<p class='text-danger'>Query failed. Please try again later!</p>";
            } else {
                echo "<div class='table-container'>";
                echo "<table>";
               echo "<thead>
        <tr>
            <th colspan='8' style='text-align:center; font-size: 24px; font-weight: bold; position: relative;'>
                Department List
                <div class='search-box' style='position: absolute; right: 10px; top: 50%; transform: translateY(-50%);'>
                    <form method='GET' style='display: flex; gap: 5px;'>
                        <input type='text' name='search' value='" . htmlspecialchars($search) . "' placeholder='Search by Department Name' style='padding: 5px;'>
                        <button type='submit' class='btn btn-info'>Search</button>
                    </form>
                </div>
            </th>
        </tr>
        <tr>
            <th>Sr. No</th>
            <th>Department ID</th>
            <th>Department Name</th>
            <th>HOD</th>
            <th>Description</th>
            <th>Location</th>
            <th>Contact Address</th>
            <th>Manage</th>
        </tr>
      </thead>";


                echo "<tbody>";
                while ($row = pg_fetch_assoc($result)) {
                    $srno++;
                    echo "<tr>";
                    echo "<td>{$srno}</td>";
                    echo "<td>" . htmlspecialchars($row['department_code']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['department_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['hod_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                    echo "<td><a href='mailto:" . htmlspecialchars($row['contact']) . "'>" . htmlspecialchars($row['contact']) . "</a></td>";
                    echo "<td><a href='managedept.php?id=" . urlencode($row['department_code']) . "' class='btn btn-success'>Manage</a></td>";
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                pg_free_result($result);
            }

            pg_close($conn);
            ?>
        </div>

        <footer>
            <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
        </footer>
    </div>
</body>
</html>
