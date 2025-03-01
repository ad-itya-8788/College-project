<?php
include 'dbconnect.php';
include '../active.php';

/* This file is for updating student data after redirection */
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $enrollment = $_POST['enrollment'];
    $fullname = $_POST['fullname'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $parent_name = $_POST['parent_name'];
    $address = $_POST['address'];
    $course = $_POST['course'];
    $semester = $_POST['semester'];
    $dob = $_POST['dob'];

    // Check if passwords match
    if ($password != $confirm_password) {
        echo "Passwords do not match. Please try again.";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'editstudent.php'; 
                }, 2000); // 2-second delay
              </script>";
        exit();
    }

    // Hash the password (for security, you may use password_hash)
    $hashed_password = $password;

    // Fetch course_code, dept_code, and department_name based on the selected course name
    $course_query = "SELECT course_code, dept_code FROM public.course WHERE course_name = '$course'";
    $course_result = pg_query($conn, $course_query);

    if ($course_result && pg_num_rows($course_result) > 0) {
        $course_row = pg_fetch_assoc($course_result);
        $course_code = $course_row['course_code'];
        $dept_code = $course_row['dept_code']; // Get the department code

        // Fetch department name from the departments table based on the department code
        $dept_query = "SELECT department_name FROM public.departments WHERE department_code = '$dept_code'";
        $dept_result = pg_query($conn, $dept_query);

        if ($dept_result && pg_num_rows($dept_result) > 0) 
        {
            $dept_row = pg_fetch_assoc($dept_result);
            $department_name = $dept_row['department_name']; 

            $sql = "UPDATE public.student
                    SET sname = '$fullname', password = '$hashed_password', email = '$email', 
                        phone = '$phone', gender = '$gender', parent_name = '$parent_name',
                        address = '$address', course_name = '$course', course_code = '$course_code', 
                        department_code = '$dept_code', department_name = '$department_name', 
                        semester = '$semester', dob = '$dob' 
                    WHERE enrollment = '$enrollment'";

            $result = pg_query($conn, $sql);

            if ($result) {
                echo "Student data updated successfully!";
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'student.php'; 
                        }, 2000); // 2-second delay
                      </script>";
            } else {
                echo "Error updating student data: " . pg_last_error($conn);
                echo "<script>
                        setTimeout(function() {
                            window.location.href = 'student.php'; 
                        }, 2000); // 2-second delay
                      </script>";
            }
        } else {
            echo "Error fetching department name: " . pg_last_error($conn);
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'editstudent.php'; 
                    }, 2000); // 2-second delay
                  </script>";
        }
    } else {
        echo "Error fetching course_code or dept_code: " . pg_last_error($conn);
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'editstudent.php'; 
                }, 2000); // 2-second delay
              </script>";
    }
}
?>
