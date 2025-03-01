<?php
include 'dbconnect.php';
require '../active.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Department Data</title>
    <style>
        body {
            font-family: Tahoma;  
            margin: 0;
            padding: 0;
            background-color: rgb(141, 177, 231);
        }

        .container {
            width: 70%;
            margin: 30px auto;
            background-color: #fff;
            padding: 15px;
            border-radius:4px;
            box-shadow:3px 3px 3px black;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius:5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 10px;
        }

        button:hover {
            background-color: rgb(205, 94, 10);
        }

        .header {
            background-color: rgb(244, 244, 244);
            padding: 10px 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid black;
        }

        .header .logo img {
            height: 60px;
        }

        .back-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius:4px;
            margin:30px;
        }

        .back-btn:hover {
            background-color: #c82333;
            color:black;
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

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">
            <a href="https://moderncollegepune.edu.in/">
                <img src="../photos/modernlogo.png" alt="Modern College Logo">
            </a>
        </div>
        <div>
            <a href="department.php" class="back-btn">Back</a>
        </div>
    </div>

    <div class="container">
        <h2>Add Department Data</h2>
        <form action="" method="POST">
            <label for="department_code">Department Code:</label>
            <input type="text" name="department_code" pattern="[A-Za-z0-9]+" title="Department Code must only contain letters (uppercase or lowercase) or numbers" required>
            
            <label for="department_name">Department Name:</label>
            <input type="text" name="department_name" pattern="[a-zA-Z ]+" title="Department name must contain only letters (uppercase or lowercase)" required>
            
            <label for="hod_name">HOD Name:</label>
            <input type="text" name="hod_name" pattern="[a-zA-Z ]+" title="Name must contain only letters (uppercase or lowercase)" required>
            
            <label for="description">Description:</label>
            <textarea name="description" required></textarea>
            
            <label for="location">Location:</label>
            <input type="text" name="location" required>
            
            <label for="contact_address">Contact Address:</label>
            <input type="email" name="contact_address" required placeholder="Email:cexampledept@gmail.com">

            <div class="button-container">
                <button type="submit" name="submit">Add Department</button>
                <button type="reset">Reset</button>
            </div>
        </form>

        <?php
        if (isset($_POST['submit'])) {
            $deptcode = htmlspecialchars($_POST['department_code']);
            $deptname = htmlspecialchars($_POST['department_name']);
            $hod = htmlspecialchars($_POST['hod_name']);
            $desc = htmlspecialchars($_POST['description']);
            $loc = htmlspecialchars($_POST['location']);
            $contact = htmlspecialchars($_POST['contact_address']);

            // Validate input
            if (!preg_match('/^[a-zA-Z0-9]+$/', $deptcode)) {
                echo "<div class='error-message'>Department code must only contain alphabets or digits.</div>";
            } elseif (!preg_match('/^[a-zA-Z ]+$/', $deptname) || !preg_match('/^[a-zA-Z ]+$/', $hod)) {
                echo "<div class='error-message'>Department name and HOD name must contain only alphabets.</div>";
            } else {
                $check_sql = "SELECT * FROM departments WHERE department_code = '$deptcode'";
                $check_result = @pg_query($conn, $check_sql);

                if (pg_num_rows($check_result) > 0) {
                    echo "<div class='error-message'>Department code '$deptcode' already exists. Please choose a different code.</div>";
                } else {
                    $sql = "INSERT INTO departments (department_code, department_name, hod_name, description, location, contact) 
                            VALUES ('$deptcode', '$deptname', '$hod', '$desc', '$loc', '$contact')";
                    $result = @pg_query($conn, $sql);

                    if ($result) {
                        echo "<div class='success-message'>New department data added successfully!</div>";
                    } else {
                        echo "<div class='error-message'>An error occurred. Please try again later.</div>";
                    }
                }
            }
            pg_close($conn);
        }
        ?>
    </div>

    <footer>
        <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
    </footer>

</body>
</html>
