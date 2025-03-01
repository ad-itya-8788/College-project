<?php
include 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? "";

    if ($action === "get_folders") {
        $ccode = $_POST['course_code'] ?? "BCA";
        $year = $_POST['year'] ?? "1";

        $yearMapping = ["1" => "1st_year", "2" => "2nd_year", "3" => "3rd_year"];
        if (!isset($yearMapping[$year])) exit("❌ Invalid year!");

        $yearFolder = $yearMapping[$year];
        $base = realpath("../Assignments/") . "/$ccode/$yearFolder/";

        if (!is_dir($base)) exit("❌ No folders found for this course and year.");

        $dirs = array_diff(scandir($base), [".", ".."]);
        if (empty($dirs)) exit("🚫 No subfolders found!");

        $output = "";
        foreach ($dirs as $folder) {
            $output .= "<div class='folder-item'>
                            <strong>📂 $folder</strong>
                            <a href='#' class='rename-btn' data-folder='$folder'>✏ Rename</a>
                            <a href='#' class='upload-btn' data-folder='$folder'>📤 Upload File</a>
                            <a href='#' class='new-folder-btn' data-folder='$folder'>📁 New Folder</a>
                        </div>";
        }
        exit($output);
    }

    if ($action === "get_students") {
        $ccode = $_POST['course_code'] ?? "BCA";
        $year = $_POST['year'] ?? "1";

        $sql = "SELECT * FROM student WHERE course_code = $1 AND year = $2";
        $params = array($ccode, $year);
        $result = pg_query_params($conn, $sql, $params);

        if ($result && pg_num_rows($result) > 0) {
            $output = "<table class='table table-bordered'>
                        <thead>
                            <tr><th>Enrollment</th><th>Name</th><th>Semester</th><th>Year</th><th>Course</th><th>Gender</th><th>Parent</th><th>Email</th><th>Phone</th></tr>
                        </thead>
                        <tbody>";

            while ($row = pg_fetch_assoc($result)) {
                $output .= "<tr>
                            <td>{$row['enrollment']}</td>
                            <td>{$row['sname']}</td>
                            <td>{$row['semester']}</td>
                            <td>{$row['year']}</td>
                            <td>{$row['course_name']}</td>
                            <td>{$row['gender']}</td>
                            <td>{$row['parent_name']}</td>
                            <td><a href='mailto:{$row['email']}'>{$row['email']}</a></td>
                            <td><a href='tel:{$row['phone']}'>{$row['phone']}</a></td>
                          </tr>";
            }
            $output .= "</tbody></table>";
            exit($output);
        } else {
            exit("<p class='text-danger'>No students found for this course and year.</p>");
        }
    }
}
?>
