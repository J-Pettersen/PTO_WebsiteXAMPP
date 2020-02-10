<?php
	session_start();
	//check if logged in to allow access to page.
	if(!isset($_SESSION['emp_id'])){
		//redirect to login.php if not logged in.
		header("Location: login.php?error=unauthorisedaccess");
		exit();
	}
?>

<!DOCTYPE html>
<html>
<head>    
	<link rel="stylesheet" type="text/css" href="theme.css">		
    <title>BOOKING SYSTEM</title>  
</head>
<body>
	<header>
		<nav>
			<div class="main-wrapper">			
				<h1>Company Name</h1>
				<!--Navigation bar changed depending on users job position.-->
				<ul class="navbar">
					<li class="<?php if($active =="home"){echo "active";}?>" >
					<a href="home.php">Home</a></li> <!--nav link to home page-->
					<?php 
						if($_SESSION['emp_position']== 'employee'||$_SESSION['emp_position']== 'admin'){
							//if user is admin or employee then show these links in nav bar.
							//nav link to book pto page
							echo '<li class="';							
							if($active =="book"){ //is user on book pto page?
								echo "active"; 
							}
							echo'">
							<a href="book.php">Book PTO</a></li>
							<li class="';
							//nav link to manage pto page
							if($active =="managepto"){ //is user on manage pto page?
								echo "active";
							}
							echo'">
							<a href="managepto.php">Manage PTO</a></li>';
						}
						if($_SESSION['emp_position']== 'admin'){
							//only admin may see these nav links.
							echo '<li class="';
							// nav link to admin page			
							if($active =="admincreate"||$active =="adminreset"){//is user on admin page?
								echo "active";
							}
							echo	'">
									<a href="admincreate.php">Admin</a></li>';
						}else if($_SESSION['emp_position']== 'manager'){
							//only managers may see these nav links
							echo '<li class="';
							//nav link to shift holidays page.
							if($active =="shiftholidays"){//is user on shift holidays page?
								echo "active";
							}
							echo	'">
									<a href="manshiftholidays.php">View Shift Holidays</a></li>';
						}							
					?>	
					<!-- nav link to password change page -->
					<li class="<?php if($active =="passchange"){echo "active";}?>" >
					<!-- visible to all users -->
					<a href="passchange.php">Change Password</a></li>
					<li class ="sign">
						<form action="includes/logout-inc.php" method="POST">				
							<ul>			
								<!-- Click button to sign user out-->
								<li><button name="signout">Sign Out</button></li><br>								
							</ul>
						</form>
					</li>					
				</ul>		
				<!--displays users full name -->
				<label class="namelabel"><?php echo $_SESSION['emp_first']," ",$_SESSION['emp_last']; ?></label>		
			</div>
		</nav>
	</header>