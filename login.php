<?php
session_start(); 
include 'dbconnect.php';

// Sanitize and validate input and remove specialcharacters
$user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$user_type = filter_input(INPUT_POST, 'user_type', FILTER_SANITIZE_STRING);

// Check if required data is received
if (!$user_id || !$password || !$user_type)
{
    die("Please fill in all required fields");
}


// Prepare and execute SQL query
$sql = "SELECT user_id, password, user_type FROM sy_user WHERE user_id = $1";

$result = pg_query_params($conn, $sql, array($user_id));

if (!$result) 
{
    die("Error: " . pg_last_error($conn));
}

// Check if user is present
if (pg_num_rows($result) === 1) {
    $row = pg_fetch_assoc($result);
    if ($password === $row['password'] && $user_type === $row['user_type']) 
    {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_type'] = $user_type;
        switch ($user_type)
         {
            case 'Student':
                header('Location: studentmodul/index.php');
                break;
            case 'Admin':
                header('Location: admin/index.php');
                break;
            case 'Teacher':
                header('Location: teachermodul/index.php');
                break;
            default:
                header('Location:invalide.html');
        }
        exit;
    } else {
        // If wrong ID or password
        header('Location: invalide.html');
    }
} else {
    // If user not present
    header('Location:invalide.html');
}

// Close connection
pg_close($conn);
?>
