<?php include '../active.php';
     include 'dbconnect.php';
     /*This file display data of studnt from student table and provide serch and delte option */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Singnup Students</title>
    <style>
       
        body {
            font-family: Tahoma;
            color: #333;
                        background-color:rgb(224, 242, 255);

            margin: 0;
            padding: 0;
        }
        
        
        .container {
            width: 90%;
            max-width: 1200px;

            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        form {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 10px;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button, input[type="button"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }

        button:hover, input[type="button"]:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border:1px solid black;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #333;
            color: white;
            border:1px solid black;
        }

        tr:hover {
            background-color: #e6ffe6;
            color:red;

        }

        p {
            margin: 10px 0;
            font-size: 18px;

        }


        .table-responsive {
            overflow: auto;
        }
         .container-fluid {
            flex: 1;
            padding: 0;
        }

        /* Header Styles */
        .header {
            background-color:rgb(245, 245, 245);
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
            height: 70px;
        }

        /* Footer Styles */
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
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <a href="https://moderncollegepune.edu.in/">
                    <img src="../photos/modernlogo.png" alt="Modern College Logo">
                </a>
            </div>
    </header>
            <hr style="height:2px; background:red;">

<div class="container">
    <form class="filter-form" method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="course">Select Course:</label>
                <select name="course" id="course">
                    <option value="">All Courses</option>
                    <?php
                     {
                        $sql = "SELECT course_name FROM course";
                        $result = pg_query($conn, $sql);
                        if ($result) 
                          {
                            while ($row = pg_fetch_assoc($result)) {
                                $course_name = $row['course_name'];
                                echo "<option value='$course_name'>$course_name</option>";
                            }
                            pg_free_result($result);
                        } 
                        else
                         {
                            echo "Query failed!";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="gender">Select Gender:</label>
                <select name="gender" id="gender">
                    <option value="">All Genders</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div class="form-group">
                <label for="semester">Select Semester:</label>
                <select name="semester" id="semester">
                    <option value="">All Semesters</option>
                    <option value="Semester 1">Semester 1</option>
                    <option value="Semester 2">Semester 2</option>
                    <option value="Semester 3">Semester 3</option>
                    <option value="Semester 4">Semester 4</option>
                    <option value="Semester 5">Semester 5</option>
                    <option value="Semester 6">Semester 6</option>
                    <!-- Add more options for other semesters -->
                </select>
            </div>

            <div class="form-group">
                <label for="search">Search by Name:</label>
                <input type="text" name="search" id="search" placeholder="Enter student name">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="academic_year">Select Academic Year:</label>
                <select name="academic_year" id="academic_year">
                    <option value="">All Years</option>
                    <option value="2025-2026">2025-2026</option>
<option value="2026-2027">2026-2027</option>
<option value="2027-2028">2027-2028</option>
<option value="2028-2029">2028-2029</option>
<option value="2029-2030">2029-2030</option>

                </select>
            </div>

            <div class="form-group">
                <label for="enrollment">Search by Enrollment Number:</label>
                <input type="text" name="enrollment" id="enrollment" placeholder="Enter enrollment number">
            </div>
        </div>

        <button type="submit">Filter</button>
        <input type="button" onclick="location.href='student.php'" value="Go Back">
    </form>

    <hr>
    <?php
{
    $filter_course = isset($_POST['course']) ? $_POST['course'] : '';
    $filter_gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $filter_semester = isset($_POST['semester']) ? $_POST['semester'] : '';
    $filter_name = isset($_POST['search']) ? $_POST['search'] : '';
    $filter_academic_year = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';
    $filter_enrollment = isset($_POST['enrollment']) ? $_POST['enrollment'] : '';

    // Construct SQL query with filters
    $sql = "SELECT * FROM student WHERE 1=1";
    if (!empty($filter_course)) {
        $sql .= " AND course_name = '$filter_course'";
    }
    if (!empty($filter_gender)) {
        $sql .= " AND gender = '$filter_gender'";
    }
    if (!empty($filter_semester)) {
        $sql .= " AND semester = '$filter_semester'";
    }
    if (!empty($filter_name)) {
        $sql .= " AND sname LIKE '%$filter_name%'";
    }
    if (!empty($filter_academic_year)) {
        $sql .= " AND acdemic_year = '$filter_academic_year'";
    }
    if (!empty($filter_enrollment)) {
        $sql .= " AND enrollment = '$filter_enrollment'";
    }

    // Execute SQL query
    $result = pg_query($conn, $sql);
    if (!$result) {
        echo "Query failed: " . pg_last_error($conn);
    } else {
        // Display total number of students
        $total_students = pg_num_rows($result);
        echo "<p>Total Students: $total_students</p>";

        // Display applied filters
        if (!empty($filter_course) || !empty($filter_gender) || !empty($filter_semester) || !empty($filter_name) || !empty($filter_academic_year) || !empty($filter_enrollment)) {
            echo "<p>Filters Applied:</p>";
            echo "<ul>";
            if (!empty($filter_course)) {
                echo "<li>Course: $filter_course</li>";
            }
            if (!empty($filter_gender)) {
                echo "<li>Gender: $filter_gender</li>";
            }
            if (!empty($filter_semester)) {
                echo "<li>Semester: $filter_semester</li>";
            }
            if (!empty($filter_name)) {
                echo "<li>Name: $filter_name</li>";
            }
            if (!empty($filter_academic_year)) {
                echo "<li>Academic Year: $filter_academic_year</li>";
            }
            if (!empty($filter_enrollment)) {
                echo "<li>Enrollment Number: $filter_enrollment</li>";
            }
            echo "</ul>";
        }
// Display table
echo "<div class='table-responsive'>";
echo "<table>";
echo "<thead>";
echo "<tr>
        <th>Sr. No</th>
        <th>Enrollment Number</th>
        <th>Student Name</th>
        <th>Student Photo</th> 
       <th>Study Year</th>
        <th>Semester</th>

        <th>Gender</th>
        <th>Academic Year</th>

        <th>Password</th>
        <th>Parent Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Course Name</th>
        <th>Date Of Birth</th>
        <th>Course Code</th>
        <th>Department Code</th>
        <th>Department Name</th>
        <th>Registration Date</th>
        <th>Fee Receipt</th>
        <th>Manage</th> 
      </tr>";
echo "</thead>";
echo "<tbody>";

$srno = 0;
while ($row = pg_fetch_assoc($result)) {
    $srno++;
    echo "<tr>";
    echo "<td>$srno</td>";
    echo "<td>{$row['enrollment']}</td>";
    echo "<td>{$row['sname']}</td>";
// Display student photo
    $student_photo_path = $row['studentphoto'];
    if ($student_photo_path && file_exists("D:/xampp/htdocs/College Project/upload_Passport_photo/$student_photo_path")) {
        echo "<td><img src='../upload_passport_photo/$student_photo_path' style='max-width: 90px; max-height: 90px; border:1px solid gray;' /></td>";
    } else {
        echo "<td>No Photo Available</td>";
    }
    echo"<td>{$row['year']} Year</td>";
    echo "<td>{$row['semester']}</td>";

    echo "<td>{$row['gender']}</td>";
    
    

        echo "<td>{$row['acdemic_year']}</td>";

    echo "<td>{$row['password']}</td>";
    echo "<td>{$row['parent_name']}</td>";
    echo "<td><a href='mailto:{$row['email']}'>{$row['email']}</a></td>";
    echo "<td>{$row['phone']}</td>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['course_name']}</td>";
    echo "<td>{$row['dob']}</td>";
    echo "<td>{$row['course_code']}</td>";
    echo "<td>{$row['department_code']}</td>";
    echo "<td>{$row['department_name']}</td>";
    echo "<td>{$row['time']}</td>";

    // Display Fee Receipt
    // Display Fee Receipt
    $fee_receipt_path = $row['feerecipt'];
    if ($fee_receipt_path && file_exists("D:/xampp/htdocs/College Project/upload_fee/$fee_receipt_path")) {
        echo "<td><a href='../upload_fee/$fee_receipt_path' target='_blank'>View Receipt</a></td>";
    } else {
        echo "<td>No Receipt Available</td>";
    }

// Manage column with Approve/Delete buttons
echo "<td>
        <button style='margin: 4px; background-color: green;' onclick='window.location.href=\"editstudent.php?enrollment={$row['enrollment']}\"'>Edit</button>

        <button style='margin: 4px; background-color: red;' onclick='deleteStudent(\"{$row['enrollment']}\")'>Delete</button>
      </td>";
echo "</tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>";

pg_free_result($result);

    }
    pg_close($conn);
}
?>
<script>
   
    // JavaScript function to handle student deletion
    function deleteStudent(enrollment) {
        if (confirm("Are you sure you want to delete this student?")) {
            window.location.href = 'deletestudentpermanant.php?enrollment=' + enrollment;
        }
    }
</script>
</div>
<hr style="height: 2px; background-color:rgb(45, 54, 46);">
 <footer>
            <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank">Aditya Chavan</a></p>
        </footer>
   
</body>
</html>
