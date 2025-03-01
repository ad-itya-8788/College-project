<?php
/*This is admin dashboard*/
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') 
{
    header('Location: ../index.html');
    exit;
}

$admin_id = $_SESSION['user_id'];
include('dbconnect.php');

$query = "SELECT * FROM admin WHERE admin_id = $1";
$result = pg_query_params($conn, $query, array($admin_id));

$depcount=pg_query($conn,"select count(*) from departments");
$depcount=pg_fetch_result($depcount,0,0);

$scount=pg_fetch_result(pg_query($conn,"select count(*) from student"),0,0);
$ccount=pg_fetch_result(pg_query($conn,"select count(*) from course"),0,0);
$tcount=pg_fetch_result(pg_query($conn,"select count(*) from teachers"),0,0);
$ucount=pg_fetch_result(pg_query($conn,"select count(*) from sy_user"),0,0);

if ($result && pg_num_rows($result) > 0) 
{
    $admin = pg_fetch_assoc($result);
    $adminName = $admin['adm_name'];
    $adminPhone = $admin['contact_info'];
    $adminId = $admin['admin_id'];
} 
else
	{
    $adminName = $adminPhone = $adminId = "Invalid Admin";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="bootstrap.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../photos/modernlogo.png">

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            background:skyblue;
            font-family: Tahoma, Geneva, Verdana, sans-serif;    

        }

        .container-fluid
         {    
            padding: 0;
        }

        .header {
            background-color: #fff;
            border-bottom: 2px solid black;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        .logout-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 2px;
            transition:1s;
        }

        .logout-btn:hover 
        {
            background-color:rgb(50, 21, 156);
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(235px, 1fr));
            gap: 20px;
            padding: 10px;
            margin: 10px;
        }

        .card {
            background-color: #fff;
            text-align: center;
            padding: 20px;
        }

        .card:hover 
        {
            background-color:rgb(237, 247, 238);
            transform: translateY(-10px); 
            transition: transform 0.75s;
        cursor: pointer;
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

        .card-header img {
            height: 30px;
            margin-right: 10px;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <a href="https://moderncollegepune.edu.in/">
                    <img src="../photos/modernlogo.png" alt="Modern College Logo" height="60">
                </a>
            </div>
            <div>
                <a href="../logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <!-- Alert Box for Invalid Admin -->
        <?php if ($adminName === "Invalid Admin") : ?>
            <div class="alert alert-danger text-center my-3">
                Invalid Admin Credentials. Please contact support.
            </div>
        <?php endif; ?>

        <!-- Dashboard Content -->
        <div class="dashboard">
            <!-- Admin Info Card -->
            <div class="card">
                <div class="card-header">
                    <img src="../photos/dept.png" alt="Departments">
                </div>
                <div class="card admin-card">
                    <h5>Admin Information</h5>
                    <p>Name: <?php echo htmlspecialchars($adminName); ?></p>
                    <p>Phone: <?php echo htmlspecialchars($adminPhone); ?></p>
                    <p>Admin ID: <?php echo htmlspecialchars($adminId); ?></p>
                </div>
            </div>

            <!-- Departments Section -->
            <div class="card" onclick="location.href='department.php'">
                <div class="card-header">
                    <a href="department.php">Departments</a>
                </div>
                <img src="../photos/dept.png" alt="Departments">
                <p>Total Departments: <?php echo $depcount; ?></p>
            </div>

            <!-- Courses Section -->
            <div class="card" onclick="location.href='courses.php'">
                <div class="card-header">
                    <a href="courses.php">Courses</a>
                </div>
                <img src="../photos/course.png" alt="Courses">
                <p>Total Courses: <?php echo $ccount; ?></p>
            </div>

            <!-- Students Section -->
            <div class="card" onclick="location.href='student.php'">
                <div class="card-header">
                    <a href="student.php">Students</a>
                </div>
                <img src="../photos/student.png" alt="Students">
                <p>Total Students: <?php echo $scount; ?></p>
            </div>

            <!-- Teachers Section -->
            <div class="card" onclick="location.href='teachers.php'">
                <div class="card-header">
                    <a href="teachers.php">Teachers</a>
                </div>
                <img src="../photos/teacher.png" alt="Teachers">
                <p>Total Teachers: <?php echo $tcount; ?></p>
            </div>




            <!-- Users Section -->
            <div class="card" onclick="location.href='user.php'">
                <div class="card-header">
                    <a href="user.php">Users</a>
                </div>
                <img src="../photos/user.png" alt="Users">
                <p>Total Users: <?php echo $ucount; ?></p>
            </div>

            <div class="card" onclick="location.href='Reports/report.php' ">
                <div class="card-header">
                    <a href="Reports/report.php">Report</a>
                </div>
                <img src="../photos/report.png" alt="Users">
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
    </footer>

</body>

</html>
