<?php
include_once 'functions.php';


$txt_file    = file_get_contents('');
$rows        = explode("\n", $txt_file);
$title="";
$author="";
$song="";
$insong=0;

//echo $txt_file;

foreach($rows as $row)
{
    //echo $row."<br>";
    if (substr($row,0,9)=="\section{") {
    	$title = substr($row,9,-1);
    	echo "FOUND TITLE: ".$title."<br>";
    	$insong=0;
    } elseif (substr($row,0,13)=="\subsection*{") {
    	$author = substr($row,13,-1);
    	echo "FOUND AUTHOR: ".$author."<br>";
    	$insong=0;
    } elseif (substr($row,0,14)=="\begin{guitar}") {
    	echo "FOUND SONG START!"."<br>";
    	$insong=1;
    } elseif (substr($row,1,12)=="end{guitar}") {
    	echo "FOUND SONG END! <br> IMPORTING..."."<br>";
    	$insong=0;
    	//echo $title;
	//echo "<br>";
	//echo $author;
	//echo "<br>";
	//echo $song;
	//echo "*********************************************************************************************************************<br>";
	//echo "write status: ".file_put_contents("songs/".$author."_".$title.".txt", $title."\n\n".$author."\n\n".$song)."<br>";
	$con = dbconnect();
	$query = 'INSERT INTO `songs` (`title`, `author`, `song`, `remarks`) VALUES ("'.$title.'", "'.$author.'", "'.$song.'", "Imported with AutoParser");';
	mysqli_query($con,$query);
	mysqli_commit($con);
	mysqli_close($con);
	$song="";
    } else {
    	if ($insong==1){
    		$song = $song.$row."\n";
    	}
    }
}
//echo $title;
//echo "<br>";
//echo $author;
//echo "<br>";
//echo $song;


?>
