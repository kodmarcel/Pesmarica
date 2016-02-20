<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>LCARS: GZT-project</title>
		<link rel="stylesheet" href="jquery-ui-1.11/jquery-ui.css">
		<script src="jquery.js"></script>
		<script src="jquery-ui-1.11/jquery-ui.js"></script>
		<script src="jquery-colors.js"></script>
		

</head>




<form>
  <input class="target" type="text" value="Field 1">
  <select class="target">
    <option value="option1" selected="selected">Option 1</option>
    <option value="option2">Option 2</option>
  </select>
</form>
<div id="other">
  Trigger the handler
</div>

<script>
$( ".target" ).change(function() {
  alert( "Handler for .change() called." );
});

$( "#other" ).click(function() {
  $( ".target" ).change();
});
</script>
