<?php
	
//logs user out of website.
if(isset($_POST['signout'])){
	session_start();
	session_unset();
	session_destroy();
	header("Location: ../login.php?success=loggedout");		
	exit();
}