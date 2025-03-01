<?php
include 'dbconnect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Get table name from the URL
$table = isset($_GET['table']) ? pg_escape_string($conn, $_GET['table']) : '';

if (!$table) {
    echo json_encode(["success" => false, "message" => "Table name is required"]);
    exit;
}

// Handle different request methods
switch ($method) {
    case 'GET':
        // Read all records from a table
        $query = "SELECT * FROM $table";
        $result = pg_query($conn, $query);
        
        if ($result) {
            $data = pg_fetch_all($result);
            echo json_encode(["success" => true, "data" => $data]);
        } else {
            echo json_encode(["success" => false, "message" => pg_last_error($conn)]);
        }
        break;

    case 'POST':
        // Insert a new record
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
            exit;
        }

        $columns = implode(", ", array_keys($input));
        $values = implode("', '", array_map('pg_escape_string', array_values($input)));

        $query = "INSERT INTO $table ($columns) VALUES ('$values') RETURNING *";
        $result = pg_query($conn, $query);

        if ($result) {
            echo json_encode(["success" => true, "message" => "Record added successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => pg_last_error($conn)]);
        }
        break;

    case 'PUT':
        // Update a record (ID required)
        parse_str(file_get_contents("php://input"), $input);
        if (!isset($input['id'])) {
            echo json_encode(["success" => false, "message" => "ID is required"]);
            exit;
        }

        $id = pg_escape_string($conn, $input['id']);
        unset($input['id']);

        $updates = [];
        foreach ($input as $column => $value) {
            $updates[] = "$column = '" . pg_escape_string($conn, $value) . "'";
        }
        $updateString = implode(", ", $updates);

        $query = "UPDATE $table SET $updateString WHERE id = '$id' RETURNING *";
        $result = pg_query($conn, $query);

        if ($result) {
            echo json_encode(["success" => true, "message" => "Record updated successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => pg_last_error($conn)]);
        }
        break;

    case 'DELETE':
        // Delete a record (ID required)
        parse_str(file_get_contents("php://input"), $input);
        if (!isset($input['id'])) {
            echo json_encode(["success" => false, "message" => "ID is required"]);
            exit;
        }

        $id = pg_escape_string($conn, $input['id']);
        $query = "DELETE FROM $table WHERE id = '$id'";
        $result = pg_query($conn, $query);

        if ($result) {
            echo json_encode(["success" => true, "message" => "Record deleted successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => pg_last_error($conn)]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

pg_close($conn);
?>
