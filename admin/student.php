<?php
// Include necessary files and initialize database connection
include '../active.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* General body style */
        body {
            margin: 0;
            padding: 0;
            font-family: Tahoma, Geneva, Verdana, sans-serif;
            background-color:rgba(33, 111, 206, 0.28);
         
        }

        /* Header Styles */
        .header {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #eaeaea;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
        }

        .header .logo img {
            height: 60px;
        }

        .back-btn {
            background-color: rgb(171, 30, 30);
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        .container {
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 280px;
            text-align: center;
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 10px 8px 20px rgba(190, 32, 32, 0.2);
        }

        .card img {
            max-height: 80px;
            margin-bottom: 15px;
        }

        .card h5 {
            margin: 10px 0;
            color: #343a40;
            font-size: 20px;
        }

        .card p {
            color: #6c757d;
            font-size: 14px;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 14px;
        }

        footer a {
            color: #ffc107;
            text-decoration: none;
            transition: color 0.3s;
        }

        footer a:hover {
            color: #e0a800;
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
                <a href="index.php" class="back-btn">Back</a>
            </div>
        </header>

        <!-- Main Content -->
        <div class="container">
            <div class="row">
                <!-- Card: New Student Request -->
                <div class="card" onclick="location.href='newstudentrequest.php'">
                    <img src="../photos/newstudent.png" alt="New Student">
                    <h5>New Student Request</h5>
                    <p>Verify Student Registration</p>
                </div>
                <!-- Card: View Students -->
                <div class="card" onclick="location.href='studentlist.php'">
                    <img src="../photos/viewstudent.png" alt="View Students">
                    <h5>View Students</h5>
                    <p>View all student records And Manage</p>
                </div>
                <!-- Card: Edit Student -->
                <div class="card" onclick="location.href='editstudent.php'">
                    <img src="../photos/edit.png" alt="Edit Student">
                    <h5>Edit Student</h5>
                    <p>Edit an existing student record</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
        </footer>
    </div>
</body>

</html>
