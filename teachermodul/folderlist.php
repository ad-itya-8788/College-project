<?php
$base=__DIR__."/assignments";
$folders=scandir($base);
foreach($folders as $dir)
{
  echo $dir."<br> ";
 
}

?>
