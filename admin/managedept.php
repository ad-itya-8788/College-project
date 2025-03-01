<?php
require '../active.php';
include 'dbconnect.php';

// Initialize variables for the form
$dept_code = $dept_name = $hod_name = $description = $location = $contact = "";
$successMessage = $errorMessage = "";

if (isset($_GET['id'])) {
    $dept_code = pg_escape_string($conn, $_GET['id']);

    // Fetch department data based on department code
    $sql = "SELECT * FROM departments WHERE department_code = '$dept_code'";
    $result = pg_query($conn, $sql);

    // Check if any department was found
    if ($result && pg_num_rows($result) > 0) {
        $dept = pg_fetch_assoc($result);
        $dept_name = $dept['department_name'];
        $hod_name = $dept['hod_name'];
        $description = $dept['description'];
        $location = $dept['location'];
        $contact = $dept['contact'];
    } else {
        $errorMessage = "No department found with this code.";
    }
} else {
    $errorMessage = "Invalid department ID!";
}

// Update department logic when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $dept_code = pg_escape_string($conn, $_POST['dept_code']);
        $dept_name = pg_escape_string($conn, $_POST['dept_name']);
        $hod_name = pg_escape_string($conn, $_POST['hod_name']);
        $description = pg_escape_string($conn, $_POST['description']);
        $location = pg_escape_string($conn, $_POST['location']);
        $contact = pg_escape_string($conn, $_POST['contact']);

        // Update department data in the database
        $update_sql = "UPDATE departments SET
                        department_name = '$dept_name',
                        hod_name = '$hod_name',
                        description = '$description',
                        location = '$location',
                        contact = '$contact'
                       WHERE department_code = '$dept_code'";

        if (pg_query($conn, $update_sql)) {
            $successMessage = "Department updated successfully!";
            header("Refresh: 1; url=department.php");
            exit();
        } else {
            $errorMessage = "Error: Could not update department.";
        }
    }

    // Delete department logic when the delete button is clicked
    if (isset($_POST['delete'])) {
        $dept_code = pg_escape_string($conn, $_POST['dept_code']);

        // Delete the department from the database
        $delete_sql = "DELETE FROM departments WHERE department_code = '$dept_code'";

        if (@pg_query($conn, $delete_sql)) {
            $successMessage = "Department deleted successfully!";
            header("Refresh: 1; url=department.php");
            exit();
        } else {
            $errorMessage = "Error: Could not delete department some Student and Teacher is may be assiciate with this .";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Department</title>
    <link href="bootstrap.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color:rgb(183, 205, 226);
            font-family: Arial, sans-serif;
        }

        .header {
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-bottom:2px solid black;
        }

        .back-btn {
            background-color: #007bff;
            color: white;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 5px;
            border: 1px solid black;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;

            border-radius: 4px;
            box-shadow: 10px 0 10px rgba(0, 0, 0, 0.1);
        }

        form input,
        form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        form textarea {
            resize: vertical;
        }

        form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        form button:hover {
            background-color: #0056b3;
        }

        form .delete-btn {
            background-color: #dc3545;
        }

        form .delete-btn:hover {
            background-color: #c82333;
        }

        .message {
            text-align: center;
            margin: 20px 0;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
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

    <header class="header">
        <div class="logo">
            <a href="https://moderncollegepune.edu.in/">
                <img src="../photos/modernlogo.png" alt="Modern College Logo" height="60">
            </a>
        </div>
        <div>
            <a href="department.php" class="back-btn">Back</a>
        </div>
    </header>

    <main class="container mb-4">
        <!-- Display success or error messages -->
        <div class="message">
            <?php if (!empty($successMessage)): ?>
                <p class="success"><?php echo $successMessage; ?></p>
            <?php elseif (!empty($errorMessage)): ?>
                <p class="error"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
        </div>

        <!-- If a department code exists, display the edit form -->
        <?php if (!empty($dept_code)): ?>
            <form action="managedept.php" method="POST">
                <label for="dept_code">Department Code</label>
                <input type="text" readonly name="dept_code" value="<?php echo $dept_code; ?>">

                <label for="dept_name">Department Name</label>
                <input type="text" readonly name="dept_name" value="<?php echo $dept_name; ?>" required>

                <label for="hod_name">HOD Name</label>
                <input type="text" name="hod_name" value="<?php echo $hod_name; ?>" required>

                <label for="description">Description</label>
                <textarea name="description" required><?php echo $description; ?></textarea>

                <label for="location">Location</label>
                <input type="text" name="location" value="<?php echo $location; ?>" required>

                <label for="contact">Contact Address</label>
                <input type="email" name="contact" value="<?php echo $contact; ?>" required>

                <div class="button-container">
                    <button type="submit" name="update">Update Department</button>
                    <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete this department?');">Delete Department</button>
                </div>
            </form>
        <?php else: ?>
            <p class="error">No department found to edit!</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank">Aditya Chavan</a></p>
    </footer>

</body>
</html>
