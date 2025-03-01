<?php
include '../active.php';
include 'dbconnect.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Courses</title>
    <link href="bootstrap.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: rgb(148, 180, 211);
            font-family: Arial, sans-serif;
        }

        .header {
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .logo img {
            height: 60px;
        }

        .btn:hover {
            background-color: rgb(188, 31, 42);
            transform: scale(1.1);
        }

        .table-container {
            overflow-x: auto;
            width: 100%;
            background: white;
            padding: 10px;
            border-radius: 5px;
        }

        footer {
            background-color: #343a40;
            color: white;
            margin-top:50px;
            text-align: center;
            padding: 10px 0;
        }

        .btn {
            margin: 3px;
            font-size: 12px;
        }

        .search-bar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .search-bar input {
            width: 200px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .search-bar button {
            font-size: 12px;
        }

        .container-fluid
        {
          padding:0px;
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
                <a href="courseinsert.php"><button class="btn btn-primary">New Course</button></a>
                <a href="displaycourseteacher.php"><button class="btn btn-info">Teachers & Course</button></a>
                <a href="index.php"><button class="btn btn-secondary">Back</button></a>
            </div>
        </header>

        <div class="container mt-4">
            <?php
            $srno = 0;
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';

         $search = pg_escape_string($conn, $search); 
        $sql = "SELECT c.*, t.teacher_name FROM course c LEFT JOIN teachers t ON c.course_coordinator = t.teacher_id";
        if($search != '') 
       {
           $sql .= " WHERE c.course_name ILIKE '%$search%'";
        }

$result = pg_query($conn, $sql);


            if (!$result) {
                echo "<p class='text-danger'>Query failed. Please try again later!</p>";
            } else {
                echo "<div class='table-container'>";
                echo "<table class='table table-bordered table-striped'>";

                echo "<thead>
                        <tr>
                            <th colspan='2' style='font-size: 24px; font-weight: bold;'>Course List</th>
                            <th colspan='6' style='background-color: #fff;'>
                                <form method='GET' class='search-bar'>
                                    <input type='text' name='search' placeholder='Search course by name' 
                                        value='" . htmlspecialchars($search) . "'>
                                    <button type='submit' class='btn btn-sm btn-primary'>Search</button>
                                </form>
                            </th>
                        </tr>
                        <tr>
                            <th>Sr. No</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Description</th>
                            <th>Duration</th>
                            <th>Department Code</th>
                            <th>Course Coordinator</th>
                            <th>Manage</th>
                        </tr>
                    </thead>";

                echo "<tbody>";
                if (pg_num_rows($result) > 0) {
                    while ($row = pg_fetch_assoc($result)) {
                        $srno++;
                        echo "<tr>
                                <td>{$srno}</td>
                                <td>" . htmlspecialchars($row['course_code']) . "</td>
                                <td>" . htmlspecialchars($row['course_name']) . "</td>
                                <td>" . htmlspecialchars($row['description']) . "</td>
                                <td>" . htmlspecialchars($row['duration']) . "</td>
                                <td>" . htmlspecialchars($row['dept_code']) . "</td>
                                <td>" . htmlspecialchars($row['teacher_name']) . "</td>
                                <td><a href='managecourse.php?id=" . urlencode($row['course_code']) . "' class='btn btn-success'>Manage</a></td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center text-danger'>No courses found</td></tr>";
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
