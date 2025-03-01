<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="bootstrap.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
    }

    .container-fluid {
    
      margin-top: 20px;    
      
  

    }

    .header {
      background-color: #f4f4f4;
      border-bottom: 1px solid #ddd;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
    }

    .logo img {
      height: 60px;
    }

    .header input {
      margin-right: 10px;
      padding: 8px;
      font-size: 16px;
      width: 200px;
    }


    footer {
      background-color: #343a40;
      color: white;
      text-align: center;
      padding: 10px 0;
      margin-top: 20px;
      border-top: 1px solid #ddd;
    }

    footer a {
      color: #ffc107;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }
.header input {
  margin-right: 10px;
  padding: 2px;
  font-size: 16px;
  width:150px;  
}

    @media screen and (max-width: 768px) 
    {
    
      .header input {
        width: 80px;
        margin: 10px;
      }

    
    }


   
  </style>
</head>

<body>

  <header class="header">
    <div class="logo">
      <a href="https://moderncollegepune.edu.in/">
        <img src="../photos/modernlogo.png" alt="Modern College Logo">
      </a>
    </div>
    <form onsubmit="return false;">
      <input type="search" id="searchInput" placeholder="Search Teachers" oninput="searchteacher()">
      <a href="insertteacher.php" class="btn btn-success">Add Teacher</a>
      <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
  </header>

  <div class="container-fluid">
    <div class="teacherlist" id="teacherlist">Data will appear here.</div>
  </div>

  <script>
    window.onload = function () {
      loadTeachers('');
    }

    function searchteacher() {
      const searchTerm = document.getElementById('searchInput').value.trim();
      loadTeachers(searchTerm);
    }

    function loadTeachers(searchTerm) {
      const xhr = new XMLHttpRequest();
      xhr.open('GET', 'teacherlist.php?search=' + encodeURIComponent(searchTerm), true);
      xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
          document.getElementById('teacherlist').innerHTML = xhr.responseText;
        }
      };
      xhr.send();
    }
  </script>

  <footer>
    <p>For more information, visit <a href="#" target="_blank">Our Team</a></p>
  </footer>

</body>

</html>
