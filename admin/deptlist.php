
<?php include '../active.php'?>
<!DOCTYPE html>
<html lang="en">
<!--This file dispaly Deprartment List normal list-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments Table</title>
    <link href="bootstrap.css" rel="stylesheet">
    <style>
        h1 {
            text-align: center;
            font-size: 3rem; 
            font-weight: bold;
            color: #333; 
            margin-bottom: 50px; 
            text-transform: uppercase;
            letter-spacing: 1.5px; 
        }

        table 
        {
            width: 100%;
            border-collapse: collapse; 
        }

        thead 
        {
            background-color: #007bff;
            color: white;
        }

        th, td {
            padding: 15px; 
            text-align: left;
            border-bottom: 1px solid #ddd; 
        }

        tbody tr:hover {
            background-color: #f5f5f5;
            cursor: pointer; 
        }

        tbody tr:focus, tbody tr:active 
        {
            background-color: #e0e0e0; 
        }

        .btn {
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn:hover 
        {
            background-color: #28a745;
            transform: scale(1.1);
        }

        
        tbody tr:nth-child(odd) {
            background-color: #f9f9f9; 
        }

        tbody tr:nth-child(even) {
            background-color: #ffffff; 
        }
    </style>
</head>

<body>
    <h1>Department Lists</h1>

    <div class="container mt-4">
        <?php
        include 'dbconnect.php';
        $srno = 0;
        $sql = "SELECT * FROM departments";
        $result = pg_query($conn, $sql);
        if (!$result) {
            echo "<p class='text-danger'>Query failed!</p>";
        } else {
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead>
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
                echo "<td>{$row['department_code']}</td>";
                echo "<td>{$row['department_name']}</td>";
                echo "<td>{$row['hod_name']}</td>";
                echo "<td>{$row['description']}</td>";
                echo "<td>{$row['location']}</td>";
                echo "<td>{$row['contact']}</td>";
                echo "<td><a href='managedept.php?id={$row['department_code']}' class='btn btn-success'>Manage</a></td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            pg_free_result($result);
        }
        pg_close($conn);
        ?>
    </div>
</body>

</html>
