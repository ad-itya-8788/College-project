<?php
$arr=["departments","course","teachers","course","admin","student"];
?>
<form method="POST">
<select name="sname">
<?php
foreach($arr as $name)
{
  echo "<option value='$name'> $name </option>";
}
?>
<input type="submit" value="get Report">
</select>
</form>

<?php
include '../dbconnect.php';
if(isset($_POST['sname']))
{
  $name=$_POST['sname'];
  $qry="select * from $name";
  $result=pg_query($conn,$qry);
  $rows=pg_num_rows($result);

  if($rows>0)
  {
  echo"<h1>Report Table </h1>";
  echo"Number of rows:".$rows;
  $row=pg_fetch_all($result);
 echo"<table border='1'>";

 echo"<tr>";
  foreach(array_keys($row[0]) as $col)
  {
    echo"<th>". htmlspecialchars($col) ."</th>";
  }
 
  foreach($row as $value)
  {
    echo"<tr>";
    foreach($value as $rvalue)
    {
      echo"<td>".htmlspecialchars($rvalue)."</td>";
    }
    echo"</tr>";
  }
 echo"</tr>";
 echo"</table>";

}

else
{
  echo"<h1> Table is empty</h1>";
}

}

?>