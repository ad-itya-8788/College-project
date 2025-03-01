<?php
include 'dbconnect.php';

// Get the selected year and course code from the AJAX request
$year = $_POST['year'] ?? '';
$cd = $_POST['course_code'] ?? '';

// Query to fetch students based on selected year and course_code
if ($year != '') {
  $sql = "SELECT * FROM student WHERE course_code = $1 AND year = $2";
  $params = [$cd, $year];
} else {
  $sql = "SELECT * FROM student WHERE course_code = $1";
  $params = [$cd];
}

$result = pg_query_params($conn, $sql, $params);

if ($result && pg_num_rows($result) > 0) {
  $students = [];
  while ($row = pg_fetch_assoc($result)) {
    $students[] = $row;
  }
  echo json_encode($students);
} else {
  echo json_encode([]);
}

pg_close($conn);
?>
