<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
<link rel="icon" type="image/png" href="./modernlogo.png">
    <style>
        body{
            
            background-color: #f8f8f8;
            margin: 0;
            padding: 10px;
          font-family: Tahoma, Geneva, Verdana, sans-serif;    

			}
			
        .container 
		{
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1{
            background-color: #4CAF50;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin-top: 0;
           }

      .success-message, .error-message
	  {
            background-color: #e6ffe6;
            color: #009900;
            padding: 10px;
            border: 1px solid #99cc99;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
        }

        .error-message
		{
            background-color: #ffe6e6;
            color: red;
        }

        table
		{
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td
		{
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-size: 18px;
        }

        .message
        {
            font-size: 16px;
            margin-top: 20px;
            text-align: center;
        }
    .admin-message
     {
        background-color: #e0f7ff; 
        color: #005580; 
        padding: 15px;
        border: 2px solid #66b2ff; 
        border-radius: 10px;
        font-size: 18px;
        text-align: center;
        margin-top: 20px;
        box-shadow: 0 0 10px rgba(0, 85, 128, 0.2); 
    }


    

    </style>
</head>
<body>
<div class="container">
    <h1>Registration Form</h1>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function validate_input($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $errors = [];
    $enrollment = validate_input($_POST['enrollment']);
    $password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);
    $email = validate_input($_POST['email']);
    $phone = validate_input($_POST['phone']);
    $fullname = validate_input($_POST['fullname']);
    $course = validate_input($_POST['course']);
    $semester = validate_input($_POST['semester']);
    $dob = validate_input($_POST['dob']);
    $parent_name = validate_input($_POST['parent_name']);
    $gender = validate_input($_POST['gender']);
    $address = validate_input($_POST['address']);
    $acdemicyear = $_POST['acdemicyear'];

    if (!preg_match('/^\d{5,}$/', $enrollment)) $errors[] = "Invalid enrollment number format";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    if ($password == $enrollment) $errors[] = "Password and Enrollment Number must be different";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (!preg_match('/^\d{10}$/', $phone)) $errors[] = "Phone number must be exactly 10 digits long";

    function upload_file($file, $dir) {
        if ($file['error'] == UPLOAD_ERR_OK) {
            $path = __DIR__ . $dir;
            if (!is_dir($path)) mkdir($path, 0775, true);
            $file_path = $path . basename($file['name']);
            move_uploaded_file($file['tmp_name'], $file_path);
            return $file['name'];
        }
        return '';
    }
    
    $file_name = upload_file($_FILES['file'], "/upload_fee/");
    $file_name_photo = upload_file($_FILES['photo'], "/upload_Passport_photo/");
    
    if (!$file_name) $errors[] = "Issue: You have not uploaded the fee receipt correctly.";
    if (!$file_name_photo) $errors[] = "Issue: You have not uploaded the passport photo correctly.";
    
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div class='error-message'>$error</div>";
        }
        exit(); // Stop execution if errors exist
    }
    
    $conn = pg_connect("host=localhost port=5432 dbname=college user=postgres password=1234");
    if (!$conn) exit("<div class='error-message'>Failed to connect to the database</div>");
    
    $dept_code = pg_fetch_result(@pg_query($conn, "SELECT dept_code FROM course WHERE course_name='$course'"), 0, 0);
    $course_code = pg_fetch_result(@pg_query($conn, "SELECT course_code FROM course WHERE course_name='$course'"), 0, 0);
    $dept_name = pg_fetch_result(@pg_query($conn, "SELECT department_name FROM departments WHERE department_code='$dept_code'"), 0, 0);
    
    $sql = "INSERT INTO tempstudent (enrollment, sname, gender, password, email, phone, address, course_name, semester, dob, parent_name, acdemic_year, feerecipt, studentphoto) 
            VALUES ('$enrollment', '$fullname', '$gender', '$password', '$email', '$phone', '$address', '$course', '$semester', '$dob', '$parent_name', '$acdemicyear', '$file_name', '$file_name_photo')";
    
    if (@pg_query($conn, $sql)) {
        echo "<div class='success-message'>Congratulations, your data has been submitted successfully!</div>";
        echo "<div class='success-message'>Note Down Following Data 👇</div>";
    } else {
        echo strpos(pg_last_error($conn), 'duplicate key') !== false ? 
            "<div class='error-message'>Error: The enrollment number already exists.</div>" : 
            "<div class='error-message'>An unexpected error occurred.</div>";
        exit(); // Stop execution on database error
    }
?>
    <!-- Display the submitted data in a table only if there are no errors -->
    <h2 class="message">Your Data Submitted Successfully</h2>
    <table border="1">
        <tr><th>Field</th><th>Value</th></tr>
        <tr><td>Enrollment Number</td><td><?php echo $enrollment; ?></td></tr>
        <tr><td>Password</td><td><?php echo $password; ?></td></tr>
        <tr><td>Full Name</td><td><?php echo $fullname; ?></td></tr>
        <tr><td>Gender</td><td><?php echo $gender; ?></td></tr>
        <tr><td>Photo</td><td><?php echo $file_name_photo; ?></td></tr>
        <tr><td>Email</td><td><?php echo $email; ?></td></tr>
        <tr><td>Phone</td><td><?php echo $phone; ?></td></tr>
        <tr><td>Department</td><td><?php echo $dept_name; ?></td></tr>
        <tr><td>Department Code</td><td><?php echo $dept_code; ?></td></tr>
        <tr><td>Course Code</td><td><?php echo $course_code; ?></td></tr>
        <tr><td>Course Name</td><td><?php echo $course; ?></td></tr>
        <tr><td>Semester</td><td><?php echo $semester; ?></td></tr>
        <tr><td>Academic Year</td><td><?php echo $acdemicyear; ?></td></tr>
        <tr><td>Date of Birth</td><td><?php echo $dob; ?></td></tr>
        <tr><td>Parent's Name</td><td><?php echo $parent_name; ?></td></tr>
        <tr><td>Address</td><td><?php echo $address; ?></td></tr>
        <tr><td>Fee Receipt</td><td><?php echo $file_name; ?></td></tr>
    </table>

    <h2 class="admin-message">
        Your information has been successfully submitted to the Department Admin for verification. You will be notified within 24 hours. Once verified, you will be able to log in using your enrollment number and password. Thank you for your patience!
    </h2>
<?php
}
?>


</body>
</html> 
