      body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-image: url("bludegrd.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .container-fluid {
            flex: 1;
            padding: 20px;
        }

        .header {
            background-color: #f4f4f4;
            border-bottom: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
        }

        .header .logo img {
            height: 60px;
        }

        .back-btn {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
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

        .folder-header {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f4f4f4;
            border-left: 5px solid #007bff;
            margin: 10px 0;
        }

        .table-responsive {
            overflow-x: auto;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table {
            min-width: 800px;
            margin: 0;
        }

        .table th {
            white-space: nowrap;
            background-color: #007bff !important;
            color: white;
        }

        .table td {
            vertical-align: middle;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: auto;
        }
		
		
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