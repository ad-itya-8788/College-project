<?php include '../active.php'; ?>
<?php
include 'dbconnect.php';

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teacher_name = trim($_POST['teacher_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $department = $_POST['dept']; 
    $phone = $_POST['phone'];
    $address = trim($_POST['address']);
    $qualification = trim($_POST['qualification']);

    if (!empty($phone) && !is_numeric($phone)) {
        $error_message = "<div class='alert alert-danger mt-3'>Error: Phone number must be numeric.</div>";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[!@#$%^&*(),.?\":{}|<>]).{6,}$/", $password)) {
        $error_message = "<div class='alert alert-danger mt-3'>Error: Password must be at least 6 characters long and include at least one letter, one number, and one special character.</div>";
    } elseif (empty($department)) {
        $error_message = "<div class='alert alert-danger mt-3'>Error: Department must be selected.</div>";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $teacher_name)) {
        $error_message = "<div class='alert alert-danger mt-3'>Error: Teacher name should only contain letters and spaces.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "<div class='alert alert-danger mt-3'>Error: Please enter a valid email address.</div>";
    } else {
        // First, check if the email already exists
        $check_email_query = "SELECT * FROM teachers WHERE email = '$email'";
        $check_email_result = pg_query($conn, $check_email_query);

        if (pg_num_rows($check_email_result) > 0)
         {
            $error_message = "<div class='alert alert-danger mt-3'>Error: This email is already registered.</div>";
        } else
         {
            $name_prefix = strtoupper(substr($teacher_name, 0, 3)); 
            $dept_prefix = strtoupper(substr($department, 0, 3)); 

          
            $sql = "SELECT COUNT(*) AS total_teachers FROM teachers WHERE department = '$department'";
            $result = pg_query($conn, $sql);
            $row = pg_fetch_assoc($result);
            $total_teachers = $row['total_teachers'];

        
            $serial_num = str_pad($total_teachers + 1, 2, '0', STR_PAD_LEFT);

            
            $teacher_id = $name_prefix . $dept_prefix . $serial_num;

            // Prepare and execute the SQL insert query
            $query = "INSERT INTO teachers (teacher_id, teacher_name, email, password, department, phone, address, qualification)
                      VALUES ('$teacher_id', '$teacher_name', '$email', '$password', '$department', '$phone', '$address', '$qualification')";

            $result = pg_query($conn, $query);

            if ($result) {
                $error_message = "<div class='alert alert-success mt-3'>Teacher added successfully!</div>";
                echo "<meta http-equiv='refresh' content='1;url=teachers.php'>";
            } else {
                $error_message = "<div class='alert alert-danger mt-3'>Error: Could not add teacher. Please try again.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Insert Teacher Data</title>
  <link rel="stylesheet" href="bootstrap.css">
  <style>
    body{
      background:#ccff;
    }
    .container-fluid
    {
      padding:0px;
    }
      .header {
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            border-bottom: 2px solid black;
        }

        .btn {
           
        }
  </style>
</head>
<body>
  <div class="container-fluid">
    <!-- Header -->
    <header class="header">
        <div class="logo">
            <a href="https://moderncollegepune.edu.in/"><img src="../photos/modernlogo.png" alt="Modern College Logo" height="60"></a>
        </div>
        <div><a href="teachers.php" class="btn btn-secondary">Back</a></div>
    </header>

  <!-- Main Content -->
<div class="container mt-5">
    <?php if ($error_message != "") echo $error_message; ?>

    <div class="container border p-4 mt-4 bg-white shadow-sm rounded">
        <form method="POST">
            <div class="mb-3">
                <label for="teacher_name" class="form-label">Teacher Name</label>
                <input type="text" class="form-control" id="teacher_name" name="teacher_name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                <select name="dept" class="form-control" required>
                    <option value="">Select Department</option>
                    <?php
                    $query = "SELECT department_code, department_name FROM departments";
                    $result = pg_query($conn, $query);
                    if (pg_num_rows($result) > 0) {
                        while ($row = pg_fetch_assoc($result)) {
                            echo "<option value='" . $row['department_code'] . "'>" . $row['department_name'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" maxlength='10' required>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="qualification" class="form-label">Qualification</label>
                <input type="text" class="form-control" id="qualification" name="qualification" required>
            </div>

            <div >
                <button type="submit" class="btn btn-success">Submit</button>
                <button type="reset" class="btn btn-danger">Reset</button>
            </div>
        </form>
    </div>
</div>

    <!-- Footer -->
    <footer class="mt-auto bg-dark text-white text-center py-3">
      <p>For more information, visit <a href="#" target="_blank" class="text-warning">our Team</a></p>
    </footer>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if ('<?php echo $error_message; ?>' !== '') {
        window.scrollTo(0, document.body.scrollHeight);
      }
    });
  </script>
</body>
</html>
