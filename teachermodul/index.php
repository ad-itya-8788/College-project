<?php
include '../dbconnect.php';
include '../active.php';


// Fetch teacher details
$user_id = $_SESSION['user_id'];
$result = pg_query_params($conn, "SELECT teacher_id, teacher_name, email, department, phone FROM teachers WHERE teacher_id = $1", array($user_id));
$course_query = "SELECT course_code, course_name FROM course WHERE course_code IN (SELECT course_code FROM course_teacher WHERE teacher_id = $1)";
$res = pg_query_params($conn, $course_query, array($user_id));

if ($teacher = pg_fetch_assoc($result)) 
{
    $teacher_id = htmlspecialchars($teacher['teacher_id']);
    $teacher_name = htmlspecialchars($teacher['teacher_name']);
    $email = htmlspecialchars($teacher['email']);
    $department = htmlspecialchars($teacher['department']);
    $phone = htmlspecialchars($teacher['phone']);

    $courses = [];
    while ($row = pg_fetch_assoc($res))
    {
        $courses[] = $row;
    }
} 
else
 {
    header('Location: ../index.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link href="../bootstrap.css" rel="stylesheet">
    <link rel="icon" href="../photos/modernlogo.ico">
    <style>
        .container-fluid {
            max-width: 100%;
            padding: 0;
        }

        .header {
            background-color: rgb(244, 244, 244);
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px;
            flex-wrap: wrap;
            border-bottom: 3px solid black;
        }

        .header .logo {
            display: flex;
            align-items: center;
        }

        .header .logo img {
            height: 60px;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c82333;
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

        .teacher-data {
            margin: 10px;
            background-color: #fff;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #ced4da;
            box-shadow: 3px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .teacher-data table {
            width: 100%;
            border-collapse: collapse;
        }

        .teacher-data th, .teacher-data td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .teacher-data th {
            background-color: #f1f1f1;
        }

        .task-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: center;
            margin: 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .task-card img {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
            max-width: 100%;
            object-fit: contain;
        }

        .task-card a {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
            margin-bottom: 10px;
        }

        .task-card a:hover {
            text-decoration: underline;
        }

        .task-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }

        @media only screen and (max-width: 768px) {
            .task-card {
                margin: 10px auto;
            }

            .task-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
        }

        @media only screen and (max-width: 480px) {
            .teacher-data table th, .teacher-data table td {
                padding: 8px;
            }

            .teacher-data {
                margin: 20px;
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
            <div>
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <div class="teacher-data">
            <h3>Teacher Information</h3>
            <table>
                <tr>
                    <th>Teacher ID</th>
                    <td><?php echo $teacher_id; ?></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><?php echo $teacher_name; ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo $email; ?></td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td><?php echo $department; ?></td>
                </tr>
                <tr>
                    <th>My Courses</th>
    <!--course names cha jo array hota tyala string mdhe convert krt aahe-->
                    <td><?php echo implode(", ", array_map(function ($course) { return htmlspecialchars($course['course_name']); }, $courses)); ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?php echo $phone; ?></td>
                </tr>
            </table>
        </div>

        <div class="task-grid">
          <?php
if (count($courses) > 0) {
    foreach ($courses as $course) {
        echo '<div class="task-card">';
        echo '<img src="../photos/lab.png" alt="course image">';
        echo '<form action="class.php" method="POST">';
        //class.php la course_code send hoto for get that course_code info of student
        echo '<input type="hidden" name="course_code" value="' . htmlspecialchars($course['course_code']) . '">';
        echo '<h4><button type="submit" style="background: none; border: none; color: #007bff; font-size: 18px; font-weight: bold;">';
        echo htmlspecialchars($course['course_name']);
        echo '</button></h4>';
        echo '<p>Click to view details for this course.</p>';
        echo '</form>';
        echo '</div>';
    }
} else {
echo '<p class="alert alert-danger" style="margin: 10px 50%; width: 90%;">No courses found for this teacher.</p>';

}
?>

        </div>

        <footer>
            <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
        </footer>
    </div>
</body>
</html>