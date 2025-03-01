<?php
include 'dbconnect.php';
include '../active.php';


$teacher = [];
$message = '';

if (isset($_GET['teacher_id'])) {
    $teacher_id = strtoupper($_GET['teacher_id']);
    
    $query = "SELECT teacher_id, teacher_name, email, department, phone, address, time, password, qualification 
              FROM teachers WHERE teacher_id = $1";
              
    $result = pg_query_params($conn, $query, array($teacher_id));

    if ($result && pg_num_rows($result) > 0) {
        $teacher = pg_fetch_assoc($result);
    } else {
        $message = "<div class='alert alert-danger' role='alert'>Teacher not found.</div>";
    }
} else {
    $message = "<div class='alert alert-danger' role='alert'>No teacher ID provided.</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $teacher_name = $_POST['teacher_name'] ?? $teacher['teacher_name'];
        $email = $_POST['email'] ?? $teacher['email'];
        $department = $_POST['department'] ?? $teacher['department'];
        $phone = $_POST['phone'] ?? $teacher['phone'];
        $address = $_POST['address'] ?? $teacher['address'];
        $qualification = $_POST['qualification'] ?? $teacher['qualification'];
        $password = $_POST['password'] ?? $teacher['password'];

        $departmentQuery = "SELECT 1 FROM departments WHERE department_code = $1";
        $departmentResult = pg_query_params($conn, $departmentQuery, array($department));

        if (pg_num_rows($departmentResult) == 0) {
            $message = "<div class='alert alert-danger' role='alert'>Invalid department code. Please recheck.</div>";
        } else {
            $updateQuery = "UPDATE teachers 
                            SET teacher_name = $1, email = $2, department = $3, phone = $4, address = $5, password = $6, qualification = $7 
                            WHERE teacher_id = $8";
            $result = pg_query_params($conn, $updateQuery, array(
                $teacher_name, $email, $department, $phone, $address, $password, $qualification, $teacher_id
            ));

            if ($result) {
                $message = "<div class='alert alert-success' role='alert'>Teacher details updated successfully.</div>";
                echo "<meta http-equiv='refresh' content='1;url=teachers.php'>"; // Redirect after 1 second
            } else {
                $message = "<div class='alert alert-danger' role='alert'>Error updating details.</div>";
            }
        }
    }

    if (isset($_POST['delete'])) {
        pg_query($conn, "BEGIN");

        try {
            $deleteSyUserQuery = "DELETE FROM sy_user WHERE user_id = $1";
            $deleteSyUserResult = pg_query_params($conn, $deleteSyUserQuery, array($teacher_id));
            
            if (!$deleteSyUserResult) throw new Exception("Error deleting sy_user.");

            $deleteTeachersQuery = "DELETE FROM teachers WHERE teacher_id = $1";
            $deleteTeachersResult = pg_query_params($conn, $deleteTeachersQuery, array($teacher_id));

            if (!$deleteTeachersResult) throw new Exception("Error deleting teacher.");

            pg_query($conn, "COMMIT");
            header("Location: teachers.php");
            exit;
        } catch (Exception $e) {
            pg_query($conn, "ROLLBACK");
            $message = "<div class='alert alert-danger'>Error: {$e->getMessage()}</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="bootstrap.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
      display: flex;
      flex-direction: column;
      height: 100vh;
    }

    .container-fluid {
      flex: 1;
      padding: 0;
    }

    .header {
      background-color: rgb(244, 244, 244);
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
      padding: 10px 0;
      width: 100%;
    }

    .header .logo {
      margin: 0;
      padding: 0;
      flex: 1;
    }

    .header .logo img {
      height: 60px;
    }

    footer {
        margin-top:20px;
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

    .container
    {
        border:1px solid gray;
        padding:23px;
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
    </header>

    <!-- Main Content -->
    <div class="container mt-5">
        <?php if (!empty($message)): ?>
            <?php echo $message; ?>
        <?php endif; ?>

        <h2 class="mb-4">Update Teacher Details</h2>
        <form method="POST">
            <div class="form-group mb-3">
                <label for="teacher_name" class="form-label">Teacher Name:</label>
                <input type="text" class="form-control" id="teacher_name" name="teacher_name" value="<?php echo htmlspecialchars($teacher['teacher_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($teacher['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="text" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($teacher['password'] ?? ''); ?>" placeholder="Leave empty to keep current password">
            </div>
            <div class="form-group mb-3">
                <label for="department" class="form-label">Department:</label>
                <input type="text" class="form-control" id="department" name="department" value="<?php echo htmlspecialchars($teacher['department'] ?? ''); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="qualification" class="form-label">Qualification:</label>
                <input type="text" class="form-control" id="qualification" name="qualification" value="<?php echo htmlspecialchars($teacher['qualification'] ?? ''); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="phone" class="form-label">Phone:</label>
                <input type="text" class="form-control" id="phone" maxlength=10 name="phone" value="<?php echo htmlspecialchars($teacher['phone'] ?? ''); ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="address" class="form-label">Address:</label>
                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($teacher['address'] ?? ''); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary" name="update">Update Teacher</button>
            <button type="submit" class="btn btn-danger" name="delete">Delete Teacher</button>
            <a href="teachers.php" class="btn btn-secondary">Back</a>
        </form>
    </div>

    <!-- Footer -->
    <footer>
      <p>For more information, visit <a href="#" target="_blank">Our Team</a>
      </p>
    </footer>
  </div>

</body>

</html>