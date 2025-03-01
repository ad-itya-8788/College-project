<?php

require '../active.php';
require 'dbconnect.php';



$eno = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'] ?? 'Student';

// Fetch student details from the database
$query = "SELECT enrollment, sname, year, course_code, email, course_name, semester FROM student WHERE enrollment = $1";
pg_prepare($conn, "qry", $query);
$result = pg_execute($conn, "qry", array($eno));

if (!$result) {
    die("An error occurred while executing the query: " . pg_last_error($conn));
}

$row = pg_fetch_assoc($result);

// Set variables with fetched data or default values
$sname = $row['sname'] ?? 'Not found';
$email = $row['email'] ?? 'Not found';
$ccode = $row['course_code'] ?? 'Not found';
$year = $row['year'] ?? 'Not found';
$course = $row['course_name'] ?? 'Not found';
$semester = $row['semester'] ?? 'Not found';
$semester = preg_replace('/\D/', '', $semester);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        header {
            background-color: rgb(36, 38, 39);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header img {
            height: auto;
            width: 150px;
        }

        .right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .st {
            margin: 0;
            font-size: 18px;
            color: white;
        }

        .btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #c0392b;
        }

        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background-color: rgb(36, 36, 37);
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .card-body {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        table td:first-child {
            font-weight: bold;
            width: 30%;
            color: #555;
        }

        footer {
            background-color:rgb(39, 38, 38);
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 20px;
        }

        footer a {
            color: #3498db;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .quick-updates-card .message-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s ease;
            border-radius: 5px;
        }

        @keyframes colorChange {
            0% { color: red; }
            50% { color: green; }
            100% { color: black; }
        }

        .message-item {
            animation: colorChange 1s infinite alternate;
        }

        .notes-card {
            text-align: center;
        }

        .notes-card img {
            max-height: 100px;
            margin-bottom: 15px;
        }

        .notes-card h5 {
            margin: 10px 0;
            font-size: 18px;
            color: rgb(255, 255, 255);
        }

        .notes-card p {
            color: #666;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<header>
    <img src="../photos/modernlogo.png" alt="College Logo">
    <div class="right">
        <h1 class="st">Sign In <?php echo htmlspecialchars($user_type); ?></h1>
        <button class="btn" onclick="handleLogout()">Logout</button>
    </div>
</header>

<div class="container">
    <div class="card-grid">
        <!-- Student Information Card -->
        <div class="card">
            <div class="card-header">Student Information</div>
            <div class="card-body">
                <table>
                    <tr><td>Student ID:</td><td><?php echo htmlspecialchars($eno); ?></td></tr>
                    <tr><td>Name:</td><td><?php echo htmlspecialchars($sname); ?></td></tr>
                    <tr><td>Course:</td><td><?php echo htmlspecialchars($course); ?></td></tr>
                    <tr><td>Year:</td><td><?php echo htmlspecialchars($year); ?></td></tr>
                    <tr><td>Semester:</td><td><?php echo htmlspecialchars($semester); ?></td></tr>
                    <tr><td>Email:</td><td><?php echo htmlspecialchars($email); ?></td></tr>
                </table>
            </div>
        </div>

        <!-- Quick Updates Card -->
        <div class="card quick-updates-card">
            <div class="card-header">Quick Updates</div>
            <div class="card-body">
                <?php
                $query = "SELECT message, message_date, sender_id, target_type 
                          FROM messages 
                          WHERE course_code = $1 
                            AND semester = $2 
                            AND message_date >= CURRENT_DATE - INTERVAL '3 days'
                          ORDER BY message_date DESC";

                $params = [$ccode, $semester];
                $res = pg_query_params($conn, $query, $params);

                if (!$res) {
                    echo "<p style='color: red;'>Error fetching messages: " . pg_last_error($conn) . "</p>";
                } else {
                    $messages = pg_fetch_all($res);

                    if ($messages) {
                        foreach ($messages as $message) {
                            echo "<div class='message-item'>";
                            echo "<p style='margin: 0; font-weight: bold;'>" . htmlspecialchars($message['message']) . "</p>";
                            echo "<small style='color: #666;'>Date: " . htmlspecialchars($message['message_date']) . " | Type: " . htmlspecialchars($message['target_type']) . "</small>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p style='color: #666;'>No recent messages found.</p>";
                    }
                }
                ?>
            </div>
        </div>

        <!-- Notes Section Card -->
        <div class="card notes-card">
            <div class="card-header">Notes Section</div>
            <div class="card-body">
                <img src="../photos/notes.png" alt="Notes">
                <h5>Access Your Notes</h5>
                <p>View and download your study materials here.</p>
                <form id="notesForm" action="notes.php" method="post" style="display: none;">
                    <input type="hidden" name="ccode" value="<?php echo htmlspecialchars($ccode); ?>">
                    <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
                </form>
                <button class="btn" onclick="event.preventDefault(); document.getElementById('notesForm').submit();">Access Notes</button>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
</footer>

<script>
    function handleLogout() {
        if (confirm('Are you sure you want to log out?')) {
            window.location.href = '../logout.php';
        }
    }
</script>

</body>
</html>