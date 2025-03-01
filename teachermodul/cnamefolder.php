<?php
include 'dbconnect.php';

// Base directory
$basedir = "../Demo/";

// Fetch course names from the database
$sql = "SELECT course_code FROM course";
$result = pg_query($conn, $sql);

if (!$result) {
    die("Error fetching courses: " . pg_last_error($conn));
}

// Year and semester mappings
$yearMapping = ["1" => "1st_year", "2" => "2nd_year", "3" => "3rd_year"];
$semesterMapping = ["1" => "1st_sem", "2" => "2nd_sem"];

// Loop through each course
while ($row = pg_fetch_assoc($result)) {
    $courseName = $row['course_code'];
    $courseDir = $basedir . $courseName;

    // Create the course directory if it doesn't exist
    if (!file_exists($courseDir)) {
        mkdir($courseDir, 0777, true);
    }

    // Create year and semester directories inside the course directory
    foreach ($yearMapping as $yearKey => $yearFolder) {
        $yearDir = $courseDir . "/" . $yearFolder;

        // Create the year directory if it doesn't exist
        if (!file_exists($yearDir)) {
            mkdir($yearDir, 0777, true);
        }

        // Create semester directories inside the year directory
        foreach ($semesterMapping as $semesterKey => $semesterFolder) {
            $semesterDir = $yearDir . "/" . $semesterFolder;

            // Create the semester directory if it doesn't exist
            if (!file_exists($semesterDir)) {
                mkdir($semesterDir, 0777, true);
            }
        }
    }
}

echo "Folder structure created successfully!";
?>