<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>    
	<link rel="stylesheet" type="text/css" href="theme.css">		
    <title>Login Page</title>
</head>
<body>
	<section>
		<div class="main-container">
			<header class="navbar">
				<h1>Company Name</h1>
			</header>
			<div class="aform">
				<!--login form--> 
				<form action="includes/login-inc.php" method="POST">
					<?php
					//check if an error or success message has been produced and return the relevant message.
					if(isset($_GET['error'])){ // check if error has been produced.
						if($_GET['error'] == 'emptyfields'){
							//echoes this message if an empty field error is produced.
							echo '<p class="formerror">Please fill in all fields.<p>';
						}else if($_GET['error'] == 'incorrect'){
							//echoes this message if an incorrect username or password error is produced.
							echo '<p class="formerror">Incorrect ID or password.<p>';
						}else if($_GET['error'] == 'sessioerror'){
							//this message occurs if and unknown internal error arises and kicks the user out.
							echo '<p class="formerror">Please Log back in.<p>';
						}else if($_GET['error'] == 'unauthorisedaccess'){
							//echoes this message if user attempts to access webiste without being logged in.
							echo '<p class="formerror">Please log in.<p>';
						}else if($_GET['error'] == 'sqlerror'){
							//echoes this message if an sqlerror message is produced.
							echo '<p class="formerror">Internal error.<p>';
						}
					}if(isset($_GET['success'])&&($_GET['success'] == 'loggedout')){	
						//echoes this message if user is successfully logged out.
						echo '<p class="formsuccess">Logged out successfully.<p>';
					}
					?>
					<!--Enter user id here-->
					<input type="number" placeholder="Enter Employee ID" name="id" required>
					<!--Enter user passowrd here-->
					<input type="password" placeholder="Enter Password" name="pwd" required>
					<button type="submit" name="submit">Login</button>
				</form>
			</div>
		</div>
	</section?>

<?php 
	include_once 'footer.php';
?>


























