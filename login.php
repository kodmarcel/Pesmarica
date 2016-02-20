<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<body>
<form id="loginform" action="index.php" method="post">
	<span class="error"> <?php echo $loginErr;?></span>
	Uporabniško ime: <input type="text" name="username" class="textfield" value="<?php echo $username;?>"><span class="error">
	Geslo: <input type="password" name="password" class="textfield" value="">
	Zapomni si me: <input type="checkbox" name="cookie" class="checkbox" value="1" <?php if($cookie){echo "checked";} else{echo "unchecked";}?>>
	<input type="submit" class="button" value="Prijava">
</form>

</body>

