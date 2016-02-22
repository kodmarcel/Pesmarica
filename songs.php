<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!- UID < 2 ->
<?php
include_once 'functions.php';
session_start();

?>

<script>
var frontpageTitle = "default";
$(document).ready(function() {
	refreshsongs();
	})
	
	//*************************** REFRESHERS ************************
	function refreshsongs(column){
		$.post( "getsongs.php", {action: "getsongs", SortBy: column}, 
					function(data){
			//alert(data);
			$("#songs_table").empty().append(data);
			$('#SelAuthor').change();
		});;		
		//e.preventDefault();
	};
	
	//************************** Image uploader *******************
	$(document).on('click', '#uploadFrontpage', function()  {
		$("#DlDialog").attr('title', 'Nalagam...');
		var string ='<div id="progressbar"></div><p>Nalagam naslovnico...</p>';
		$("#DlDialog").empty().append(string);
		$( "#progressbar" ).progressbar({
  			value: false
		});
		$( "#DlDialog" ).dialog({autoOpen: true, modal: true});
    		var file_data = $("#fileToUpload").prop("files")[0];   
		var form_data = new FormData();                  
		form_data.append("file", file_data);
		//alert(form_data);                             
		//$.post( "upload.php", {form_data}, 
		//			function(data){
		//	alert(data);
		//});;
		$.ajax({
                	url: 'upload.php', // point to server-side PHP script 
	                dataType: 'text',  // what to expect back from the PHP script, if anything
	                cache: false,
	                contentType: false,
	                processData: false,
	                data: form_data,                         
	                type: 'post',
	                success: function(php_script_response){
	                    if (php_script_response.substring(0, 2)=="OK"){
				$( "#DlDialog" ).dialog( "close" );
				frontpageTitle = php_script_response.substring(3, php_script_response.length-2);
				$("#gallery").prepend('<div class="galleryItem"><img src="uploads/'+frontpageTitle+'" alt="'+frontpageTitle+'" style="height:350px;"></div>');
	                    }
	                    else{
	                    	var string = "<h2>PUJS!</h2><br>"+php_script_response+"";
				$("#DlDialog").empty().append(string);
				setTimeout(function(){$( "#DlDialog" ).dialog( "close" )}, 3000);
	                    }
	                }
		});
	});


$(document).on('click', '.SortBy', function()  {
	var column = $(this).attr('id');
	refreshsongs(column);
});
	

</script>
<div id="header">
<?php
if(isset($_SESSION["user"])){
echo "Dobrodošel, ".$_SESSION["user"]."! <a href='index.php?p=logout' title='Klikni tukaj za odjavo.'>Odjava</a>";}
if(!isset($_SESSION["user"])){include "login.php";}
?>
<span id="fpname">
</div>
<div id="accordion">
<h3 title="Izbor pesmi">1. Pesmi</h3>
<div id="songs">
<div id="menu">
<div id="filter">
	<select multiple="multiple" id="SelAuthor" name="SelAuthor[]">
     		<?php
		$con = dbconnect();
		$query = "SELECT distinct `author` FROM `songs` ORDER BY `author` ASC";
		$result =  mysqli_query($con,$query);
		$initial = "-1";
		while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
			if (substr($row["author"],0,1)!=$initial){
				if($initial!="-1") echo '</optgroup>';
				$initial=substr($row["author"],0,1);
				echo '<optgroup label='.$initial.'>';
			}
			echo '<option value="'.$row["author"].'">'.$row["author"].'</option>';
		}
		echo '</optgroup>';
		?>
	</select>
	
	<SelAllVisible title="Izberi vse trenutno prikazane pesmi.">Označi vse vidne!</SelAllVisible>
	<UnselAllVisible title="Odstrani trenutno prikazane pesmi iz izbora.">Odznači vse vidne!</UnselAllVisible>

	<?php
	if(isset($_SESSION["user"])){
	echo "
	<AddSong title='Dodajanje nove pesmi'>Dodaj pesem</AddSong>
	<div id='editor'></div>
	<script>
	$( 'AddSong' ).button();
	$('AddSong').click(function(e)  {
		$.post( 'getsongs.php', {action: 'getNew'}, 
		function(data){
			$('#editor').empty().append(data);
		});
		$( '#editor' ).dialog({autoOpen: true, modal: true, dialogClass: 'no-close',
  		buttons: [{
			text: 'Ok',
			click: function() {
				//alert($('#formtitle').val()+$('#formauthor').val()+$('textarea#formsong').val()+$('#formlanguage').val()+$('#formgenre').val()+$('#formremarks').val());
				$.post( 'getsongs.php', {action: 'setNew', title: $('#formtitle').val(), author: $('#formauthor').val(), song:$('textarea#formsong').val(), language: $('#formlanguage').val(), genre: $('#formgenre').val(), remarks: $('#formremarks').val()});
				refreshsongs();
				$( this ).dialog( 'close' );
			}
		}, 
		{
			text: 'Cancel',
			click: function() {
				$( this ).dialog( 'close' );
			}
		}
		]
	});
		e.preventDefault();
	});
	$(document).on('click', 'td:not(.selector)', function()  {
		var id = $(this).parent().attr('id');
		$.post( 'getsongs.php', {action: 'getEdit', id: id}, 
		function(data){
			$('#editor').empty().append(data);
		});
			
		
		$( '#editor' ).dialog({autoOpen: true, modal: true, dialogClass: 'no-close',
  		buttons: [{
			text: 'Ok',
			click: function() {
				$.post( 'getsongs.php', {action: 'setEdit', title: $('#formtitle').val(), author: $('#formauthor').val(), song:$('textarea#formsong').val(), language: $('#formlanguage').val(), genre: $('#formgenre').val(), remarks: $('#formremarks').val()});
				refreshsongs();
				$( this ).dialog( 'close' );
			}
		}, 
		{
			text: 'Cancel',
			click: function() {
				$( this ).dialog( 'close' );
			}
		}
		]
	});
	});
	</script>";
	}
	?>
	
</div>
</div>


		<div id="songs_table" class="tableContainer">
			
		</div>
</div>
<h3 title="Nalaganje naslovnice ter izbira nekaterih slogovnih nastavitev">2. Naslovnica in oblika</h3>
<div> 
	<div id="gallery">
		<?php
			$con = dbconnect();
			$query = "SELECT * FROM `songbooks` ORDER BY `id` DESC";
			$result =  mysqli_query($con,$query);
			while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
				echo'<div class="galleryItem" title="'.$row["title"].' by '.$row["uid"].'"><img src="songbooks/frontpages/'.$row["frontpage"].'" alt="'.$row["frontpage"].'" style="height:350px;"></div>';
			}
		?>
	</div>
	<div id="uploadForm">
		Select image to upload:
		<input type="file" name="fileToUpload" id="fileToUpload">
		<input type="submit" value="Upload Image" id="uploadFrontpage">
	</div>
	<div>	<generate title="Generiranje in prenos pesmarice.">Napravi mi pesmarico!</generate></div>
</div>





</div>
<div id="DlDialog" title="Generiram...">

</div>
<script>
//$( document ).tooltip();
$( "#accordion" ).accordion();
$( "SelAllVisible" ).button();
$( "UnselAllVisible" ).button();
$( "generate" ).button();
$( "SelAllVisible" ).click(function(){
	$('tr[style="display: table-row;"]').children(".selector").children(".CheckedSongId").prop("checked", true);
});
$( "UnselAllVisible" ).click(function(){
	$('tr[style="display: table-row;"]').children(".selector").children(".CheckedSongId").prop("checked", false);
});

$( "generate" ).click(function(){
	var selected = [];
	//$('tr[style="display: table-row;"]').children(".selector").children(".CheckedSongId").children("input:checked").map(function() {
	var names = [];
        $('#songs_table input:checked').each(function() {
            names.push(this.value);
        });
	//alert(names);
	$("#DlDialog").attr('title', 'Generiram...');
	var string ='<div id="progressbar"></div><p>Pripravljam pesmarico...</p>';
	$("#DlDialog").empty().append(string);
	$( "#progressbar" ).progressbar({
  		value: false
	});
	$( "#DlDialog" ).dialog({autoOpen: true, modal: true});
	$.post( "getsongs.php", {action: "generate", songs: names, frontpage: frontpageTitle}, 
		function(data){
			//window.location.href = data;
			//alert(data);
			var string = "<h2>Tvoja pesmarica je pripravljena! <a href="+data+" id='DlDialogCloser'>Prenos</a></h2>";
			$("#DlDialog").empty().append(string);
		});
});

$(document).on('click', '#DlDialogCloser', function()  {
	$( "#DlDialog" ).dialog( "close" );
});


$('#SelAuthor').multiSelect({ selectableOptgroup: true, selectableHeader: "<input type='text' class='search-input' autocomplete='on'>",
  selectionHeader: "<input type='text' class='search-input' autocomplete='on'>",
  afterInit: function(ms){
    var that = this,
        $selectableSearch = that.$selectableUl.prev(),
        $selectionSearch = that.$selectionUl.prev(),
        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
    .on('keydown', function(e){
      if (e.which === 40){
        that.$selectableUl.focus();
        return false;
      }
    });

    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
    .on('keydown', function(e){
      if (e.which == 40){
        that.$selectionUl.focus();
        return false;
      }
    });
  },
  afterSelect: function(){
    this.qs1.cache();
    this.qs2.cache();
  },
  afterDeselect: function(){
    this.qs1.cache();
    this.qs2.cache();
  }
});

$("#SelAuthor").change(function(){
		var str = "";
		$( "select option:selected" ).each(function() {
			if (str==""){
				str += "tr[author='"+$( this ).text() + "']";
			}
			else{
				str += ", tr[author='"+$( this ).text() + "']";
			}	
		});
		//alert(str);
		if (str==""){
			$("tr").show();
			//alert("bu");
		}
		else {
			//$(str).css("background-color", "yellow")
			$("tr.normalrow, tr.alternaterow").hide()
			$(str).show();
		}
		$(".scrollContent").children('tr[style="display: table-row;"]').each(function(i){
			if (i%2){
				$(this).attr('class', 'alternaterow');
			}else{
				$(this).attr('class', 'normalrow');
			}
		}); 
 	});
</script>
