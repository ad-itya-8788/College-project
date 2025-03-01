<?php
include '../active.php';
include 'dbconnect.php';
/*This file is for edit student record*/
$studentNotFoundMessage = null;

if ( isset($_REQUEST['enrollment'])) {
    $enrollment = $_REQUEST['enrollment'];

    $sql = "SELECT * FROM public.student WHERE enrollment = $1";
    $result = pg_query_params($conn, $sql, array($enrollment));

    if ($result && pg_num_rows($result) > 0) {
        $student = pg_fetch_assoc($result);
    } else {
        $studentNotFoundMessage = "No student found with the provided enrollment number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student Data</title>
    <link href="bootstrap.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
                     background-color:rgb(224, 242, 255);

        }
        .container-fluid { padding: 0; }
        .content-wrapper {
            max-width: 85%;
            margin: 20px auto;
            background-color: #fff;
            padding: 15px;
            border-radius: 7px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 15px; font-size: 18px; }
        .input-group-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .input-group {
            flex: 0 0 calc(48% - 10px);
            margin-bottom: 10px;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 6px;
            margin: 4px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #333;
            color: #fff;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        button:hover { background-color: #555; }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            margin-top:10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        header{ background-color: #fff; padding: 10px; text-align: center; font-size: 12px; }
        footer p { margin: 0; }
        
        .logo img {
            width: 300px; /* Increase logo size */
            display: block;
            margin-left: 0;
            margin-right: auto;
        }

          footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        footer a {
            color: #ffc107;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <header>
        <div class="logo">
            <a href="https://moderncollegepune.edu.in/">
                <img src="../photos/modernlogo.png" alt="Modern College Logo">
           
            </a>
                
        </div>
    </header>

    <section>
        <div class="content-wrapper">
            <h2>Update Student Data</h2>

            <form method="POST">
                <div class="input-group">
                    <label for="enrollment">Enrollment Number:</label>
                    <input type="text" id="enrollment" name="enrollment" required>
                </div>
                <button type="submit">Search</button>
                <button type="button" onclick="window.location.href='index.php'">Back</button>

            </form>

            <?php
                if ($studentNotFoundMessage) {
                    echo "<div class='error-message'>$studentNotFoundMessage</div>";
                }

                if (isset($student) && !$studentNotFoundMessage) {
                    echo "<h3>Student Found: Update Data</h3>";
            ?>
            <form method="POST" action="updatestudent.php">
                <div class="input-group-container">
                    <div class="input-group">
                        <label for="enrollment">Enrollment Number:</label>
                        <input type="text" id="enrollment" name="enrollment" value="<?= htmlspecialchars($student['enrollment']); ?>" readonly required>
                    </div>
                    <div class="input-group">
                        <label for="fullname">Full Name:</label>
                        <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($student['sname']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" value="<?= htmlspecialchars($student['password']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="input-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($student['email']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" maxlength="10" value="<?= htmlspecialchars($student['phone']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="gender">Gender:</label>
                        <select name="gender" required>
                            <option value="Male" <?= $student['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?= $student['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="parent_name">Parent's Name:</label>
                        <input type="text" id="parent_name" name="parent_name" value="<?= htmlspecialchars($student['parent_name']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" value="<?= htmlspecialchars($student['address']); ?>" required>
                    </div>
                 <div class="input-group">
                            <label for="course">Course:</label>
                            <select id="course" name="course" required>
                                <?php
                                $sql = "SELECT course_name FROM public.course";
                                $result = pg_query($conn, $sql);
                                while ($row = pg_fetch_assoc($result)) {
                                    $selected = $student['course_name'] === $row['course_name'] ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($row['course_name']) . "' $selected>" . htmlspecialchars($row['course_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    <div class="input-group">
    <label for="semester">Semester:</label>
    <select id="semester" name="semester" required>
        <?php
        for ($i = 1; $i <= 8; $i++) {
            $semester = "Semester $i";
            $selected = ($student['semester'] === $semester) ? 'selected' : '';
            echo "<option value='$semester' $selected>$semester</option>";
        }
        ?>
    </select>
</div>

                    <div class="input-group">
                        <label for="dob">Date of Birth:</label>
                        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($student['dob']); ?>" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit">Update</button>
                    <button type="reset">Reset</button>
                </div>
            </form>
            <?php
                }
            ?>
        </div>
    </section>

    <footer>
        <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
    </footer>
</div>

</body>
</html>
