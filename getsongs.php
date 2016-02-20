<?php
include_once 'functions.php';
session_start();
if($_POST["action"]=='getsongs'){
	//echo'BU';
		$con = dbconnect();
		if (!isset($_POST["author"])){
			if (isset($_POST["SortBy"])){
				$query = "SELECT * FROM `songs` ORDER BY `".$_POST["SortBy"]."` ASC";
			} else {
				$query = "SELECT * FROM `songs` ORDER BY `title` ASC";
			}
		}
		else{
			if (isset($_POST["SortBy"])){
				$query = "SELECT * FROM `songs` WHERE `author`='".$_POST["author"]."' ORDER BY `".$_POST["SortBy"]."` ASC ";
			} else {
				$query = "SELECT * FROM `songs` WHERE `author`='".$_POST["author"]."' ORDER BY `title` ASC";
			}
		}
		$result =  mysqli_query($con,$query);
		if (mysqli_num_rows($result) > 0) {
			echo '
			<table class="scrollTable" width="100%" cellspacing="0" cellpadding="0" border="0">
			<thead class="fixedHeader">
			<tr class="header">
			<th>
			Izberi
			</th>
			<th>
			<a href="#" class="SortBy" id="author">Izvajalec</a>
			</th>
			<th>
			<a href="#" class="SortBy" id="title">Naslov</a>
			</th>
			<th>
			<a href="#" class="SortBy" id="genre">Zvrst</a>
			</th>
			<th>
			<a href="#" class="SortBy" id="added_by">Prispeval</a>
			</th>
			<th>
			<a href="#" class="SortBy" id="remarks">Opombe</a>
			</th>
			</tr>
			</thead>
			<tbody title="Klikni za urejanje pesmi" class="scrollContent">';
			// output data of each row
			$a = 0;
			while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
				if (!isset($_SESSION["user"])){
		        	if($a){echo '<tr class="alternaterow" author="'.$row["author"].'" style="display: table-row;"><td class="selector"><input type="checkbox" class="CheckedSongId" value="'.$row["id"].'"></input></td><td>' . $row["author"]. '</td><td>' . $row["title"]. '</td><td>' . $row["genre"]. '</td><td>' . $row["added_by"]. '</td><td>' . $row["remarks"]. '</td></tr>';$a=0;}
		        	else{echo '<tr class="normalrow" author="'.$row["author"].'" style="display: table-row;"><td class="selector"><input type="checkbox" class="CheckedSongId" value="'.$row["id"].'"></input></td><td>' . $row["author"]. '</td><td>' . $row["title"]. '</td><td>' . $row["genre"]. '</td><td>' . $row["added_by"]. '</td><td>' . $row["remarks"]. '</td></tr>';$a=1;}
		        	}
		        	if (isset($_SESSION["user"])){
		        	if($a){echo '<tr id="'.$row["id"].'" class="alternaterow" author="'.$row["author"].'" style="display: table-row;"><td class="selector"><input type="checkbox" class="CheckedSongId" value="'.$row["id"].'"></input></td><td>' . $row["author"]. '</td><td>' . $row["title"]. '</td><td>' . $row["genre"]. '</td><td>' . $row["added_by"]. '</td><td>' . $row["remarks"]. '</td></tr>';$a=0;}
		        	else{echo '<tr id="'.$row["id"].'" class="normalrow" author="'.$row["author"].'" style="display: table-row;"><td class="selector"><input type="checkbox" class="CheckedSongId" value="'.$row["id"].'"></input></td><td>' . $row["author"]. '</td><td>' . $row["title"]. '</td><td>' . $row["genre"]. '</td><td>' . $row["added_by"]. '</td><td>' . $row["remarks"]. '</td></tr>';$a=1;}
		        	}
			}
			echo '</tbody></table>';
		}
		else {
			echo "0 results";
		}
		mysqli_commit($con);
		mysqli_close($con);
}
elseif ($_POST["action"]=='generate'){
	$con = dbconnect();
	date_default_timezone_set('Europe/Ljubljana');
	$title=date("Y_m_d-H_i_s");
	$header = file_get_contents('tex/header.tex');
	file_put_contents("songbooks/".$title.".tex", $header, FILE_APPEND | LOCK_EX);
	$chords = file_get_contents('tex/chords.tex');
	file_put_contents("songbooks/".$title.".tex", $chords, FILE_APPEND | LOCK_EX);
	foreach ($_POST["songs"] as $value){
		//echo $value;
		$query = "SELECT * FROM `songs` WHERE `id`='".$value."'";
		$result =  mysqli_query($con,$query);
		$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
		//echo $row["title"].$row["author"];
		$song = "\section{".$row["title"]."}\n";
		$song = $song."\subsection*{".$row["author"]."}\n";
		$song = $song."\begin{guitar}\n";
		$song = $song.$row["song"];
		$song = $song."\n";
		$song = $song."\\end{guitar}\n";
		file_put_contents("songbooks/".$title.".tex", $song, FILE_APPEND | LOCK_EX);
		//echo $row["song"];
	}
	$footer = file_get_contents('tex/footer.tex');
	file_put_contents("songbooks/".$title.".tex", $footer, FILE_APPEND | LOCK_EX); 
	shell_exec("sh ".$config['site_root']."/generate.sh ".$config['site_root']."/songbooks ".$title);
	mysqli_close($con);
	echo "songbooks/".$title.".pdf";
}
elseif ($_POST["action"]=='getEdit'){
	$con = dbconnect();
	$query = "SELECT * FROM `songs` WHERE `id` = ".$_POST["id"];
	$result =  mysqli_query($con,$query);
	$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
	echo '<form>
		Naslov: 	<input type="text"  id="formtitle" class="textfield" value="'.$row['title'].'"><br>
		Avtor: 		<input type="text"  id="formauthor" class="textfield" value="'.$row['author'].'"><br>
		Besedilo: 	<textarea rows="15" cols="30" id="formsong" name="Pomoč prihaja..." class="textfield">'.$row['song'].'</textarea><br>
		Jezik: 		<input type="text" id="formlanguage" class="textfield" value="'.$row['language'].'"><br>
		Zvrst: 		<input type="text" id="formgenre" class="textfield" value="'.$row['genre'].'"><br>
		Opombe: 	<input type="text" id="formremarks" class="textfield" value="'.$row['remarks'].'"><br>
		</form>';
}
elseif ($_POST["action"]=='setEdit'){
	$con = dbconnect();
	$query = 'INSERT INTO `songs` (`title`, `author`, `song`, `language`, `genre`, `remarks`, `added_by`) VALUES ("'.$_POST["title"].'", "'.$_POST["author"].'", "'.$_POST["song"].'", "'.$_POST["language"].'", "'.$_POST["genre"].'", "'.$_POST["remarks"].'", "'.$_SESSION["user"].'");';
	echo $query;
	mysqli_query($con,$query);
	mysqli_commit($con);
	mysqli_close($con);
}
elseif ($_POST["action"]=='getNew'){
	echo '<form>
		Naslov: 	<input type="text" id="formtitle" class="textfield" value=""><br>
		Avtor: 		<input type="text" id="formauthor" class="textfield" value=""><br>
		Besedilo: 	<textarea rows="15" cols="30" id="formsong" name="Pomoč prihaja..." class="textfield"></textarea><br>
		Jezik: 		<input type="text" id="formlanguage" class="textfield" value=""><br>
		Zvrst: 		<input type="text" id="formgenre" class="textfield" value=""><br>
		Opombe: 	<input type="text" id="formremarks" class="textfield" value=""><br>
		</form>';
}
elseif ($_POST["action"]=='setNew'){
	$con = dbconnect();
	$query = 'INSERT INTO `songs` (`title`, `author`, `song`, `language`, `genre`, `remarks`, `added_by`) VALUES ("'.$_POST["title"].'", "'.$_POST["author"].'", "'.$_POST["song"].'", "'.$_POST["language"].'", "'.$_POST["genre"].'", "'.$_POST["remarks"].'", "'.$_SESSION["user"].'");';
	echo $query;
	mysqli_query($con,$query);
	mysqli_commit($con);
	mysqli_close($con);
}

?>	
