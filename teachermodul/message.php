<?php
require 'dbconnect.php';
require '../active.php';

$ccode = $_REQUEST['ccode'] ?? '';
$year = $_REQUEST['year'] ?? '';
$user_id = $_SESSION['user_id'] ?? '';
$user_type = $_SESSION['user_type'] ?? '';
$user_type=strtolower($user_type);
if (!$user_id || !$user_type) {
    header("Location: index.php");
    exit();
}

// Define semesters
$semesters = [
    1 => ['1', '2'],
    2 => ['3', '4'],
    3 => ['5', '6']
];

if (!isset($semesters[$year])) {
    $_SESSION['error'] = "Invalid year selected.";
    header("Location: index.php");
    exit();
}

// Fetch course name
$query = "SELECT course_name FROM course WHERE course_code = $1";
$params = [$ccode];
$course_result = pg_query_params($conn, $query, $params);
$course_name = pg_fetch_result($course_result, 0, 'course_name');

// Fetch existing messages
$query = "SELECT * FROM messages WHERE sender_id = $1 AND course_code = $2 AND semester = ANY($3::text[]) ORDER BY sent_at DESC";
$params = [$user_id, $ccode, '{' . implode(',', $semesters[$year]) . '}'];
$result = pg_query_params($conn, $query, $params);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = trim($_POST['msg']);
    $date = $_POST['date'];
    $semester = $_POST['semester'];
    $target_type = $_POST['target_type'];
    $sent_at = date('Y-m-d H:i:s');
    
    if (empty($msg) || empty($date) || empty($semester) || empty($target_type)) {
        $_SESSION['error'] = "All fields are required.";
    } else {
        $query = empty($_POST['edit_id']) ? 
            "INSERT INTO messages (sender_type, sender_id, course_code, semester, message, message_date, sent_at, target_type) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)" :
            "UPDATE messages SET message = $1, message_date = $2, semester = $3, sent_at = $4, target_type = $5 WHERE msg_id = $6 AND sender_id = $7";
        
        $params = empty($_POST['edit_id']) ? 
            [$user_type, $user_id, $ccode, $semester, $msg, $date, $sent_at, $target_type] :
            [$msg, $date, $semester, $sent_at, $target_type, $_POST['edit_id'], $user_id];

        $result = pg_query_params($conn, $query, $params);

        if ($result) {
            $_SESSION['success'] = "Message " . (empty($_POST['edit_id']) ? "added" : "updated") . " successfully.";
        } else {
            $_SESSION['error'] = "Error processing message: " . pg_last_error($conn);
        }
    }
    header("Location: message.php?year=$year&ccode=$ccode");
    exit();
}

// Handle Delete request
if (isset($_GET['delete_id'])) {
    $delete_query = "DELETE FROM messages WHERE msg_id = $1 AND sender_id = $2";
    $result = pg_query_params($conn, $delete_query, [$_GET['delete_id'], $user_id]);
    $_SESSION['success'] = $result ? "Message deleted successfully." : "Error deleting message: " . pg_last_error($conn);
    header("Location: message.php?year=$year&ccode=$ccode");
    exit();
}

// Fetch message for editing
$edit_message = null;
if (isset($_GET['edit_id'])) {
    $edit_query = "SELECT * FROM messages WHERE msg_id = $1 AND sender_id = $2";
    $edit_result = pg_query_params($conn, $edit_query, [$_GET['edit_id'], $user_id]);
    $edit_message = pg_fetch_assoc($edit_result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Sending Module</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
body {
    background: linear-gradient(to bottom right,rgba(215, 255, 216, 0.25),rgba(163, 163, 163, 0.81)); 
    margin: 0;
    padding: 0;
  font-family: Tahoma, sans-serif;}



          .header {
      background-color: rgb(255, 255, 255);
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
      padding: 10px 0;
      width: 100%;

    }

    .header .logo {
      margin: 0;
      padding: 0;
      flex: 1;
    }

    .header .logo img {
      height: 60px;
    }

    .logout-btn {
      background-color: #dc3545;
      color: white;
      padding: 5px 15px;
      margin-right:10px;
      text-decoration: none;
      border-radius: 5px;
    }

    .logout-btn:hover {
      background-color: #c82333;
    }

    /* Footer Styles */
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
        .message-form {
            background-color:rgb(255, 255, 255);
            padding: 30px;
          
            border-radius:5px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 10px;
        }
        .form-group label {
            font-weight: bold;
            color: #495057;
        }
        .btn-send {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            padding: 10px 30px;
            font-size: 18px;
        }
        .sent-messages {
            background-color: #ffffff;
            padding: 30px;
            margin-bottom: 10px;
            border-radius:5px;

            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .table th {
            font-weight: bold;
        }
        .alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
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
      <div>
        <a href="index.php" class="logout-btn">Back</a>
      </div>
    </header>
    <div class="container mt-5">
        <h2 class="text-center mb-4 font-weight-bold"><?= htmlspecialchars($course_name) ?> - 📝Message </h2>
        
        <!-- Alert Container -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success'] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="message-form">
            <form action="" method="POST" id="messageForm">
                <input type="hidden" name="ccode" value="<?= htmlspecialchars($ccode) ?>">
                <input type="hidden" name="year" value="<?= htmlspecialchars($year) ?>">
                <?php if ($edit_message): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_message['msg_id'] ?>">
                <?php endif; ?>
                
                <div class="form-row">
                   <div class="form-group col-md-8">
    <label for="msg">Message:</label>
    <input type="text" class="form-control" id="msg" name="msg" value="<?= htmlspecialchars($edit_message['message'] ?? '') ?>" required>
</div>

                    <div class="form-group col-md-4">
                        <label for="date">Date:</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?= htmlspecialchars($edit_message['message_date'] ?? '') ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="semester">Semester:</label>
                        <select class="form-control" id="semester" name="semester" required>
                            <?php foreach ($semesters[$year] as $sem): ?>
                                <option value="<?= $sem ?>" <?= ($edit_message['semester'] ?? '') == $sem ? 'selected' : '' ?>><?= $sem ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="target_type">Message Type:</label>
                        <select class="form-control" id="target_type" name="target_type" required>
                            <option value="General" <?= ($edit_message['target_type'] ?? '') == 'General' ? 'selected' : '' ?>>General</option>
                            <option value="Targeted" <?= ($edit_message['target_type'] ?? '') == 'Targeted' ? 'selected' : '' ?>>Targeted</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group text-center mt-4">
                    <button type="submit" class="btn btn-send"><?= $edit_message ? 'Update' : 'Send' ?> Notice</button>
                    <?php if ($edit_message): ?>
                        <a href="message.php?year=<?= $year ?>&ccode=<?= $ccode ?>" class="btn btn-secondary ml-2">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="sent-messages">
            <h3 class="text-center mb-4 font-weight-bold">Sent Messages</h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Message</th>
                            <th>Semester</th>
                            <th>Date</th>
                            <th>Sent At</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = pg_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['message']) ?></td>
                                <td><?= htmlspecialchars($row['semester']) ?></td>
                                <td><?= htmlspecialchars($row['message_date']) ?></td>
                                <td><?= htmlspecialchars($row['sent_at']) ?></td>
                                <td><?= htmlspecialchars($row['target_type']) ?></td>
                                <td>
                                    <a href="?edit_id=<?= $row['msg_id'] ?>&year=<?= $year ?>&ccode=<?= $ccode ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="?delete_id=<?= $row['msg_id'] ?>&year=<?= $year ?>&ccode=<?= $ccode ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 <footer>
      <p>For more information, visit <a href="https://adityachavan8788.netlify.app/" target="_blank">Aditya Chavan</a>
      </p>
    </footer>
    <script>
        document.querySelectorAll('.alert .close').forEach(function(button) {
            button.addEventListener('click', function() {
                this.closest('.alert').style.display = 'none';
            });
        });
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
