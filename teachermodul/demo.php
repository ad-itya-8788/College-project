<?php
$arr = ["<b>Aditya</b>", "ram", "Sham"];

$newar = array_map('htmlspecialchars', $arr);

echo "<pre>";
print_r($newar);
echo "</pre>";
?>
