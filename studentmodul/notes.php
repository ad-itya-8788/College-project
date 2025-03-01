<?php
$course = $_POST['ccode'] ?? "";
$year = $_POST['year'] ?? "";

$year_mapping = array('1' => '1st_year', '2' => '2nd_year', '3' => '3rd_year');
$year = $year_mapping[$year] ?? "Unknown"; 
$dir = "../Assignments/" . $course . "/" . $year;

// Check if directory exists
$folders = [];

if (is_dir($dir)) {
    $items = scandir($dir);
    $items = array_diff($items, array('.', '..')); // Remove "." and ".." from the list

    foreach ($items as $item) {
        if (is_dir($dir . "/" . $item)) {
            $folders[] = $item; // Only add folders
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assignment Folders</title>
  <style>
    /* General Styles */
    body {
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
      font-family: 'Arial', sans-serif;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .header {
      background-color: rgb(26, 26, 27);
      border-bottom: 1px solid #ddd;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .header .logo img {
      height: 60px;
    }

    .back-btn {
      background-color: #007bff;
      color: white;
      padding: 8px 16px;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .back-btn:hover {
      background-color: #0056b3;
    }

    .container {
      flex: 1;
      padding: 20px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    /* Grid System for Folders */
    .folder-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
      padding: 10px;
    }

    .folder-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      padding: 15px;
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .folder-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .folder-icon {
      font-size: 30px;
      color: #007bff;
      margin-bottom: 10px;
    }

    .folder-name {
      font-size: 16px;
      font-weight: bold;
      margin-bottom: 10px;
      color: #333;
    }

    .open-btn {
      background: #007bff;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s ease;
    }

    .open-btn:hover {
      background: #0056b3;
    }

    .no-folders {
      text-align: center;
      color: #666;
      font-size: 16px;
    }

    footer {
      background-color: rgb(26, 26, 27);
      color: white;
      text-align: center;
      padding: 15px;
      margin-top: auto;
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
      <a href="index.php" class="back-btn">Back</a>
    </header>

    <!-- Main Content -->
    <div class="container">
      <h2>Assignment Folders for <?php echo htmlspecialchars($course) . " - " . htmlspecialchars($year); ?></h2>
      
      <div class="folder-grid">
        <?php if (!empty($folders)): ?>
          <?php foreach ($folders as $folder): ?>
            <div class="folder-card">
              <div class="folder-icon">📂</div>
              <div class="folder-name"><?php echo htmlspecialchars($folder); ?></div>
              <form action="openfolder.php" method="post">
                <input type="hidden" name="folder" value="<?php echo htmlspecialchars($dir . '/' . $folder); ?>">
                <button type="submit" class="open-btn">Open Folder</button>
              </form>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="no-folders">No folders found in this directory.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank">Aditya Chavan</a></p>
    </footer>
  </div>
</body>
</html>