<?php include '../active.php'; ?>
<?php include 'dbconnect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Users</title>
  <link href="bootstrap.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      height: 100vh;
background-color:rgb(183, 222, 255);

    }

    .container-fluid {
      flex: 1;
      padding: 0;
    }

    .header {
      background-color: rgb(244, 244, 244);
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 20px;
      width: 100%;
    }

    .back-button {
      margin-left: auto;
    }

    .header .logo img {
      height: 60px;
    }

    .table-container {
      width: 90%;
      margin: auto;
    }

    .search-box {
      display: flex;
      justify-content: right;
      align-items: center;
    }

    .search-box input {
      padding: 6px;
      width: 250px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .search-box button {
      padding: 6px 12px;
      margin-left: 5px;
    }

    .table th, .table td {
      text-align: center;
      vertical-align: middle;
    }

    .table th[colspan="4"] {
      background-color: #343a40;
      color: white;
      font-size: 18px;
    }

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
      <a href="index.php" class="btn btn-primary back-button">Back</a>
    </header>

    <!-- Main Content -->
    <div class="container mt-5">
      <div class="table-container">
        <table class="table table-bordered table-lg mt-4">
       <thead class="table-dark">
  <tr>
    <th colspan="3" style="text-align: left; font-size: 18px;">User Details</th>
    <th style="text-align: right;">
      <div class="search-box">
        <form method="GET">
          <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Search User">
          <button type="submit" class="btn btn-info">Search</button>
        </form>
      </div>
    </th>
  </tr>
  <tr>
    <th>User ID</th>
    <th>User Name</th>
    <th>Password</th>
    <th>User Type</th>
  </tr>
</thead>

          <tbody>
            <?php
            $search = isset($_GET['search']) ? pg_escape_string($conn, $_GET['search']) : '';
            $query = "SELECT * FROM public.sy_user";

            if (!empty($search)) {
              $query .= " WHERE user_id::text ILIKE '%$search%' 
                          OR user_name ILIKE '%$search%' 
                          OR user_type ILIKE '%$search%' ";
            }

            $query .= " ORDER BY user_id";
            $result = pg_query($conn, $query);

            if (!$result) {
                echo "<tr><td colspan='4'>Error fetching data: " . pg_last_error($conn) . "</td></tr>";
            } elseif (pg_num_rows($result) > 0) {
                while ($row = pg_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['user_id']}</td>
                            <td>{$row['user_name']}</td>
                            <td>{$row['password']}</td>
                            <td>{$row['user_type']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>No data found</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <p>For more information, visit <a href="#" target="_blank">our Team</a></p>
    </footer>
  </div>
</body>
</html>
