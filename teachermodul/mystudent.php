<?php
require '../active.php';

if (!isset($_POST['course_code']) || !isset($_POST['year'])) 
{
    echo "<div class='alert alert-danger'>❌ Invalid request. Course code and year must be provided.</div>";
    header("Refresh:2; url=index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="../bootstrap.css">
    <script src="jquery.js"></script>
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
            background-color: #f4f4f4;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            width: 100%;
        }

        .header .logo img {
            height: 60px;
        }

        .back-btn {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            margin: 12px;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        .folder-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            padding: 20px;
        }

        .folder-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .folder-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .folder-name {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .folder-options a {
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
            margin: 0 5px;
            font-size: 0.9rem;
        }

        .folder-options a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            width: 100%;
        }

        .folder-header {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f4f4f4;
            border-left: 5px solid #007bff;
            margin: 10px 0;
        }

        .folder-header h3 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #222;
            margin: 0;
        }

        .folder-header button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .folder-header button:hover {
            background-color: #218838;
        }

        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
            margin: 20px auto; 
            padding: 0 15px; 
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: rgb(30, 30, 31) !important;
            color: white;
        }

        .table td {
            vertical-align: middle;
        }

        .alert
        {
          display:flex;
          justify-content:center;
          align-items:center;
          width: 50%;
          margin:10px 200px;
        }
      
    </style>
</head>
<body>
    <div class="container-fluid">
        <header class="header">
            <div class="logo">
                <a href="https://moderncollegepune.edu.in/">
                    <img src="../photos/modernlogo.png" alt="Modern College Logo">
 
                </a>
            </div>
            <a href="class.php" class="back-btn">Back</a>
        </header>

        <?php
        include 'dbconnect.php';

$image_path = "../photos/modernlogo.png";
$real_path = realpath($image_path);
//echo "Real Path: " . $real_path;

        // Sanitize inputs
        $ccode = htmlspecialchars($_POST['course_code']);
        $year = htmlspecialchars($_POST['year']);

        function getYearFolders($ccode, $year)
         {
            $yearMapping = ["1" => "1st_year", "2" => "2nd_year", "3" => "3rd_year"];
            if (!isset($yearMapping[$year])) return "<div class='alert alert-danger'>❌ Invalid year!</div>";

            $yearFolder = $yearMapping[$year];
            $base = realpath("../Assignments/") . "/$ccode/$yearFolder/";

            if (!is_dir($base)) return "<div class='alert alert-danger'>❌ No folders found for this course and year.</div>";

            $dirs = array_diff(scandir($base), [".", ".."]);

            if (empty($dirs)) return "<div class='alert alert-danger'>❌ No subfolders found!</div>";

            $output = "<div class='folder-grid'>";
            foreach ($dirs as $folder) {
                $output .= "<div class='folder-card' data-folder='$folder'>
                                <div class='folder-name'>$folder</div>
                                <div class='folder-options'>
                                    <a href='#' class='rename-btn' data-folder='$folder'>Rename</a>
                                    <a href='#' class='delete-btn' data-folder='$folder'>Delete</a>
                                    <a href='#' class='upload-btn' data-folder='$folder'>Upload</a>
                                </div>
                            </div>";
            }
            $output .= "</div>";
            return $output;
        }

     echo "<div class='folder-header'>
        <h3>$ccode - Year $year</h3>
        <button id='createFolderBtn'>Create New Folder</button>
        <form action='message.php' method='POST' style='display:inline;'>
            <input type='hidden' name='ccode' value='$ccode'>
            <input type='hidden' name='year' value='$year'>
            <button type='submit'>Add Notice For Student</button>
        </form>
      </div>";

              
        echo getYearFolders($ccode, $year);

        $sql = "SELECT * FROM student WHERE course_code = $1 AND year = $2";
        $params = array($ccode, $year);
        $result = pg_query_params($conn, $sql, $params);

        if ($result && pg_num_rows($result) > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered table-hover">';
            echo '<thead><tr>
                    <th>Enrollment</th>
                    <th>Name</th>
                    <th>Semester</th>
                    <th>Year</th>
                    <th>Course</th>
                    <th>Gender</th>
                    <th>Parent</th>
                    <th>Email</th>
                    <th>Phone</th>
                  </tr></thead><tbody>';

            while ($row = pg_fetch_assoc($result)) {
                echo "<tr>
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
            echo '</tbody></table></div>';
        } else {
            echo "<div class='alert alert-info mt-3'>No students found</div>";
        }
        ?>
<footer>
            <p>For more information, visit <a href="" target="_blank">Our Team</a></p>
        </footer>
    </div>

    <script>
        $(document).ready(function() {
            // Create Folder
            $('#createFolderBtn').click(function()
             {
                let folderName = prompt("Enter the name of the new folder:");
                if (folderName && folderName.trim() !== "") {
                    $.post('folder_operations.php', { action: 'create', folderName: folderName.trim(), ccode: '<?php echo $ccode; ?>', year: '<?php echo $year; ?>' }, function(response) {
                        alert(response);
                        location.reload();
                    });
                } else {
                    alert("Folder name cannot be empty!");
                }
            });

            // Rename Folder
            $('.rename-btn').click(function(e) {
                e.stopPropagation();
                let oldFolderName = $(this).data('folder');
                let newFolderName = prompt("Enter the new name for the folder:", oldFolderName);
                if (newFolderName && newFolderName.trim() !== "") {
                    $.post('folder_operations.php', { action: 'rename', oldFolderName: oldFolderName, newFolderName: newFolderName.trim(), ccode: '<?php echo $ccode; ?>', year: '<?php echo $year; ?>' }, function(response) {
                        alert(response);
                        location.reload();
                    });
                } else {
                    alert("Folder name cannot be empty!");
                }
            });

            // Delete Folder
            $('.delete-btn').click(function(e) {
                e.stopPropagation();
                let folderName = $(this).data('folder');  //data is the key and data-folder foler contain folder value
                if (confirm(`Are you sure you want to delete the folder "${folderName}"?`)) {
                    $.post('folder_operations.php', { action: 'delete', folderName: folderName, ccode: '<?php echo $ccode; ?>', year: '<?php echo $year; ?>' }, function(response) {
                        alert(response);
                        location.reload();
                    });
                }
            });

            // Upload Files
            $('.upload-btn').click(function(e) {
                e.stopPropagation();
                let folderName = $(this).data('folder');
                let fileInput = $('<input type="file" multiple>');
                fileInput.on('change', function() {
                    let formData = new FormData();
                    $.each(this.files, function(i, file) {
                        formData.append('files[]', file);
                    });
                    formData.append('action', 'upload');
                    formData.append('folderName', folderName);
                    formData.append('ccode', '<?php echo $ccode; ?>');
                    formData.append('year', '<?php echo $year; ?>');

                    $.ajax({
                        url: 'folder_operations.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            alert(response);
                            location.reload();
                        }
                    });
                });
                fileInput.click();
            });

            // Open Folder
            $('.folder-card').click(function() {
                let folderName = $(this).data('folder');
                let ccode = '<?php echo $ccode; ?>';
                let year = '<?php echo $year; ?>';

                // Create a hidden form
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = 'folder_contents.php';

                // Add hidden inputs for the data
                let inputCcode = document.createElement('input');
                inputCcode.type = 'hidden';
                inputCcode.name = 'ccode';
                inputCcode.value = ccode;

                let inputYear = document.createElement('input');
                inputYear.type = 'hidden';
                inputYear.name = 'year';
                inputYear.value = year;

                let inputFolder = document.createElement('input');
                inputFolder.type = 'hidden';
                inputFolder.name = 'folder';
                inputFolder.value = folderName;

                // Append inputs to the form
                form.appendChild(inputCcode);
                form.appendChild(inputYear);
                form.appendChild(inputFolder);

                // Append the form to the body and submit it
                document.body.appendChild(form);
                form.submit();
            });
        });
    </script>
</body>
</html>