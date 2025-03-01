<?php
// Include necessary files and initialize database connection
include '../active.php';
include 'dbconnect.php';
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
            padding: 0;
            font-family:Tahoma, Verdana, sans-serif;
        }

  

        .header {
            background-color: rgb(244, 244, 244);
            border-bottom: 1px solid black;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .header .logo img {
            height: 60px;
        }

        .back-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color:rgb(246, 10, 34);
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

        .message {
            margin: 20px;
            padding: 10px;
            border-radius: 5px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .student-data {
            border-collapse: collapse;
            margin: 20px auto; 
            width: 50%;
            text-align: center;
        }

        .student-data td,
        .student-data th {
            border: 1px solid black;
            padding: 8px;
        }

        .student-data th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <a href="https://moderncollegepune.edu.in/">
                    <img src="../photos/modernlogo.png" alt="Modern College Logo">
                </a>
            </div>
            <div>
                <a href="newstudentrequest.php" class="back-btn">Back</a>
            </div>
        </header>

        <?php
        // Fetch student data securely
        function fetchTempStudentData($conn, $enrollment)
        {
            $query = "SELECT * FROM tempstudent WHERE enrollment = $1";
            $result = pg_query_params($conn, $query, array($enrollment));
            return pg_fetch_assoc($result);
        }

        // Insert student data securely
        function insertStudentData($conn, $studentData)
        {
            $query = "INSERT INTO student (
                enrollment, sname, acdemic_year, gender, studentphoto, password,
                parent_name, email, phone, address, course_name, semester, dob,
                course_code, department_code, department_name, feerecipt, time
            ) VALUES (
                $1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17, $18
            );";

            $params = array(
                $studentData['enrollment'], $studentData['sname'], $studentData['acdemic_year'],
                $studentData['gender'], $studentData['studentphoto'],$studentData['password'],
                $studentData['parent_name'], $studentData['email'], $studentData['phone'],
                $studentData['address'], $studentData['course_name'], $studentData['semester'],
                $studentData['dob'], $studentData['course_code'], $studentData['department_code'],
                $studentData['department_name'], $studentData['feerecipt'], $studentData['time']
            );

            return pg_query_params($conn, $query, $params);
        }

        // Delete temporary student data
        function deleteTempStudentData($conn, $enrollment)
        {
            $query = "DELETE FROM tempstudent WHERE enrollment = $1";
            return pg_query_params($conn, $query, array($enrollment));
        }

        // Display student data
        function displayStudentData($studentData)
        {
            echo "<table class='student-data'>";
            foreach ($studentData as $key => $value) {
                echo "<tr><td>" . ucfirst(str_replace('_', ' ', $key)) . ":</td><td>";
                if ($key === 'studentphoto' && $value && file_exists("../upload_passport_photo/$value")) {
                    echo "<img src='../upload_passport_photo/$value' style='max-width: 90px; max-height: 90px; border:1px solid gray;' />";
                } elseif ($key === 'email') {
                    echo "<a href='mailto:$value'>$value</a>";
                } else {
                    echo htmlspecialchars($value);
                }
                echo "</td></tr>";
            }
            echo "</table>";
        }

        // Process enrollment data
        $enrollment = $_GET['enrollment'] ?? '';
        if ($enrollment) {
            $studentData = fetchTempStudentData($conn, $enrollment);
            if ($studentData) {
                $insertResult = @insertStudentData($conn, $studentData);
                if ($insertResult) {
                    deleteTempStudentData($conn, $enrollment);
                    echo "<div class='message success-message'>Student information inserted successfully into the student table and deleted from tempstudent.</div>";
                } else {
                    $error = pg_last_error($conn);
                    if (strpos($error, 'duplicate key value violates unique constraint') !== false) {
                        echo "<div class='message error-message'>A student with enrollment number $enrollment already exists...</div>";
                    } else {
                        echo "<div class='message error-message'>Error inserting data: " . htmlspecialchars($error) . "</div>";
                    }
                }
                displayStudentData($studentData);
            } else {
                echo "<div class='message error-message'>No student found with the provided enrollment.</div>";
            }
        } else {
            echo "<div class='message error-message'>Invalid enrollment number provided.</div>";
        }

        pg_close($conn);
        ?>

        <footer>
            <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
        </footer>
    </div>
</body>

</html>
