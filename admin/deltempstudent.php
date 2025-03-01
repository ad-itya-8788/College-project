<?php
include '../active.php';
?>
<?php
/*this file is for delete tempstudnt form tempstudent table*/
include 'dbconnect.php';
$enrollment = pg_escape_string($_GET['enrollment']);
$select_sql = "SELECT sname, course_name, enrollment, semester FROM tempstudent WHERE enrollment='$enrollment'";
$select_result = pg_query($conn, $select_sql);
$student_info = pg_fetch_assoc($select_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Information</title>
    <style>
        body 
		{
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container
		{
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .message 
		{
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .info 
		{
            font-size: 16px;
            color: #555;
        }
        .info div 
		{
            margin-bottom: 10px;
        }
        .info strong 
		{
            color: #000;
        }
        .success 
		{
            color: green;
        }
        .error 
		{
            color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    if ($student_info)
		{
        echo "<div class='message'>Student Information:</div>";
        echo "<div class='info'>";
        echo "<div><strong>Name:</strong> " . htmlspecialchars($student_info['sname']) . "</div>";
        echo "<div><strong>Course Name:</strong> " . htmlspecialchars($student_info['course_name']) . "</div>";
        echo "<div><strong>Enrollment ID:</strong> " . htmlspecialchars($student_info['enrollment']) . "</div>";
        echo "<div><strong>Semester:</strong> " . htmlspecialchars($student_info['semester']) . "</div>";
        echo "</div>";
        $delete_sql = "DELETE FROM tempstudent WHERE enrollment='$enrollment'";
        $delete_result = pg_query($conn, $delete_sql);
        if ($delete_result)
		{
            echo "<div class='message success'>Record Deleted Successfully...</div>";
        }
		else 
		{
            echo "<div class='message error'>Failed to delete record.</div>";
        }
        } 
	else 
	{
        echo "<div class='message error'>No student found with the provided enrollment ID.</div>";
    }

    pg_close($conn);
   ?>
</div>
</body>
</html>
