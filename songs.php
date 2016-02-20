<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!- UID < 2 ->
<?php
include_once 'functions.php';
session_start();

?>

<script>
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
<div> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum faucibus ipsum id consequat volutpat. Nulla suscipit orci a hendrerit commodo. Donec rutrum leo ac metus convallis, in accumsan lectus venenatis. Phasellus sed neque tellus. Quisque dignissim nec urna vitae convallis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vitae lorem vel turpis efficitur faucibus in vitae massa. Vivamus quis mauris consequat, posuere libero non, tristique felis. Praesent efficitur ornare est hendrerit faucibus. Morbi venenatis mi massa. Maecenas porttitor lacus id lectus cursus dignissim. Praesent vel lobortis lacus, nec dignissim sapien. Aenean efficitur massa ut libero auctor feugiat. In quis eros vitae magna condimentum varius.

Nam lacinia purus ut rhoncus rhoncus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse scelerisque venenatis maximus. Curabitur et sollicitudin ex. Donec nec sodales metus. Ut vitae lobortis nisl. Nunc sollicitudin consectetur tellus, eget egestas risus mollis commodo. Proin sed posuere enim, id placerat magna. Pellentesque vitae risus urna. Sed ac elit ante. Proin et risus non nunc mollis accumsan non ut mi. Phasellus sem dui, commodo non tristique sit amet, convallis quis magna. Pellentesque finibus hendrerit tortor in lobortis. Pellentesque aliquam luctus ante, ut fringilla nulla pretium vel. Quisque sagittis egestas nulla, et scelerisque felis rutrum ac. In quis sem ut velit laoreet maximus sit amet ac massa. </div>

<h3 title="Pregled, generiranje in prenos pesmarice.">3. Prenos</h3>
<div>	<generate title="Generiranje in prenos pesmarice.">Napravi mi pesmarico!</generate></div>

</div>
<div id="DlDialog" title="Generiram...">

</div>
<script>
$( document ).tooltip();
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
	var string ='<div id="progressbar"></div><p>Pripravljam pesmarico...</p>';
	$("#DlDialog").empty().append(string);
	$( "#progressbar" ).progressbar({
  		value: false
	});
	$( "#DlDialog" ).dialog({autoOpen: true, modal: true});
	$.post( "getsongs.php", {action: "generate", songs: names}, 
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
