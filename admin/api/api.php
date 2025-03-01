<?php
include '../dbconnect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$table = $_GET['table'] ?? null;

if (!$table) {
    echo json_encode(["success" => false, "message" => "Table name is required"]);
    exit;
}

// Query to fetch all records
$query = "SELECT * FROM $table";
$result = pg_query($conn, $query);

if (!$result) {
    echo json_encode(["success" => false, "message" => pg_last_error($conn)]);
    exit;
}

$data = pg_fetch_all($result) ?: [];

$count_query = "SELECT COUNT(*) AS total FROM $table";
$count_result = pg_query($conn, $count_query);

$total_count = 0;
if ($count_result) {
    $count_row = pg_fetch_assoc($count_result);
    $total_count = $count_row['total'];
}

echo json_encode([
    "success" => true,
    "total" => (int) $total_count,
    "data" => $data
]);

pg_close($conn);
?>
