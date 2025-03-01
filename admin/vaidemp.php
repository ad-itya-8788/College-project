<html>
<body>
<form action="" method="POST">
Search:<input type="Search" name='vai'>
<input type="submit">
</form>
</body>
</html>
<?php
$host = 'localhost';
$dbname = 'college';
$port = '5432';
$user = 'postgres';
$password = '1234';
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

$sql="select * from course where course_code=$_POST[vai]";

$result=pg_query($conn,$sql);

$row=pg_fetch_all($result);

echo"<pre>";
print_r($row);
echo"</pre>";


?>