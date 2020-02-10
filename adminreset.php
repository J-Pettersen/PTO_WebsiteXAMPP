<?php 
	$active = 'adminreset';//sets active page.
	include_once 'header.php';
	if($_SESSION['emp_position']!='admin'){
		//redirect to home page if user is not admin
		header("Location: home.php?error=unathorisedaccess");
		exit();
	}
?>
<section class = "main-container">
	<div class = "main-wrapper">
		<div class="vertical">
			<ul>
				<li><a  href="admincreate.php">Create/Amend Employee</a></li>
				<li><a href="adminreset.php" class="active">Reset Employees Password</a></li>
				<li><a href="admindelete.php">Delete Employee data.</a></li>
				<li></li>
			</ul>
		</div>			
		<form class="aform" action="includes/adminresetpwd-inc.php" method="POST" >
			<h3>Reset Employees Password</h3>			
			<?php
			//check if an error or success message has been produced and return the relevant message.
			if(isset($_GET['error'])){ // check if error has been produced.
				if($_GET['error'] == 'emptyfields'){
					//echoes this message if an empty field error is produced.
					echo '<p class="formerror">Please fill in all fields.<p>';
				}
				else if($_GET['error'] == 'invalidid'){
					//echoes this message if no match for ID is found in database.
					echo '<p class="formerror">Incorrect ID and Surname combination.<p>';
				}
				else if($_GET['error'] == 'incorrectinput'){
					//data entered into input fields are not valid characters.
					echo '<p class="formerror">Invalid input.<p>';					
				}
				else if($_GET['error'] == 'sqlerror'){
					//echoes this message if an sqlerror message is produced.
					echo '<p class="formerror">Internal error.<p>';
				}
			}else if(isset($_GET['success'])&&($_GET['success'] == 'reset')){
				//echoes this message if a users password is reset successfully.
				echo '<p class="formsuccess">Password successfully reset.<p>';
			}				
			?>
			<!-- input the id of the user to be reset-->
			<input type="number" name="id" placeholder="Employee ID">
			<!-- input the surname of the user to be reset-->
			<input type="text" name="last" placeholder="Surname">
			<!-- button to be clicked to trigger a user's password reset. -->
			<button type="submit" name="reset" onclick="">Reset Password</button>
		</form>
	</div>		
</section>
<?php 
	include_once 'footer.php';
?>