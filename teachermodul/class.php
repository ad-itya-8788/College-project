<?php
require '../active.php';
if(isset($_POST['course_code']))
{
  $coursecode = $_POST['course_code'];
}
else
{
  header("Location:index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../bootstrap.css">
  <title>Admin Dashboard</title>
  <style>
  body {
      margin: 0;
      font-family:Tahoma;
      background-color: #f8f9fa;
      color: #333;
    }

    .header {
      background-color: rgb(244, 244, 244);
      border-bottom: 1px solid #ddd;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px;
      flex-wrap: wrap;
      border-bottom: 3px solid black;
    }

    .header .logo {
      display: flex;
      align-items: center;
    }

    img {
      height: 60px;
    }

    .back-btn {
      background-color: #007bff;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .back-btn:hover {
      background-color: #0056b3;
    }


    .card-container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      padding: 20px;
      gap: 20px;
    }

    .card {
      width: 18rem;
      border: none;
      border-radius: 10px;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .card-img-top {
      height: 150px;
      width: 100%;
      object-fit: contain;
      padding: 20px;
      background-color: #f1f1f1;
    }

    .card-body {
      text-align: center;
      padding: 20px;
    }

    .card-title {
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .card-text {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 20px;
    }

    .btn-primary {
      background-color: #007bff;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 1rem;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #0056b3;
    }

    footer {
      background-color: #343a40;
      color: white;
      text-align: center;
      padding: 15px 0;
      margin-top: 40px;
    }

    footer a {
      color: #ffc107;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }  </style>
</head>

<body>
  <header class="header">
    <a href="https://moderncollegepune.edu.in/">
      <img src="../photos/modernlogo.png" alt="Modern College Logo">
    </a>
    <a href="index.php" class="back-btn">Back</a>
  </header>

  <!-- Cards Container -->
  <div class="card-container">
    <!-- FY Year Card -->

  <!--Here also pasing course_code and year like 1 2 3 to to access in mystudent.php in that they display student list of that year and this folders-->
    <div class="card">
      <img class="card-img-top" src="../Photos/viewstudent.png" alt="FY Card image">
      <div class="card-body">
        <h5 class="card-title">FY</h5>
        <p class="card-text">Content for FY students.</p>
  <!--Class -->
        <form method="POST" action="mystudent.php">
          <input type="hidden" name="course_code" value="<?php echo $coursecode; ?>">
          <input type="hidden" name="year" value="1">
          <button type="submit" class="btn btn-primary">Go to FY Section</button>
        </form>
      </div>
    </div>

    <!-- SY Year Card -->
    <div class="card">
      <img class="card-img-top" src="../Photos/viewstudent.png" alt="SY Card image">
      <div class="card-body">
        <h5 class="card-title">SY</h5>
        <p class="card-text">Content for SY students.</p>
        <form method="POST" action="mystudent.php">
          <input type="hidden" name="course_code" value="<?php echo $coursecode; ?>">
          <input type="hidden" name="year" value="2">
          <button type="submit" class="btn btn-primary">Go to SY Section</button>
        </form>
      </div>
    </div>

    <!-- TY Year Card -->
    <div class="card">
      <img class="card-img-top" src="../Photos/viewstudent.png" alt="TY Card image">
      <div class="card-body">
        <h5 class="card-title">TY</h5>
        <p class="card-text">Content for TY students.</p>
        <form method="POST" action="mystudent.php">
          <input type="hidden" name="course_code" value="<?php echo $coursecode; ?>">
          <input type="hidden" name="year" value="3">
          <button type="submit" class="btn btn-primary">Go to TY Section</button>
        </form>
      </div>
    </div>
  </div>
 <footer>
            <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
        </footer>
</body>

</html>
