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
				<li><a href="adminreset.php">Reset Employees Password</a></li>
				<li><a href="admindelete.php" class="active">Delete Employee Data</a></li>
				<li></li>
			</ul>
		</div>			
		<form class="aform" action="includes/admindelete-inc.php" method="POST" >
			<h3>Delete Employee Data From System</h3>			
			<?php
			//check if an error or success message has been produced and return the relevant message.
			if(isset($_GET['error'])){ // check if error has been produced.
				if($_GET['error'] == 'emptyfields'){
					//returns this error if an empty field is detected.
					echo '<p class="formerror">Please fill in all fields.<p>';
				}else 
					if($_GET['error'] == 'self'){
					//returns this error if the user tries to delete themselves from the databse.
					echo '<p class="formerror">Cannot delete own account from system.<p>';
				}else 
					if($_GET['error'] == 'incorrectpwd'){
					//returns this error if the password input by the user is incorrect
					echo '<p class="formerror">Password entered is incorrect.<p>';
				}else 
					if($_GET['error'] == 'invaliduser'){
					//returns this error if the id & surname combination return no matches in the system.
					echo '<p class="formerror">Invalid ID & Surname combination.<p>';					
				}else 
					if($_GET['error'] == 'sqlerror'){
					//echoes this message if an sqlerror message is produced.
					echo '<p class="formerror">Internal error.<p>';
				}
			}else if(isset($_GET['success'])&&($_GET['success'] == 'delete')){
				//echoes this message if a users is successfully removed from the system.
				echo '<p class="formsuccess">Employee records successfully deleted.<p>';
			}
				
			?>
			<p>Be warned, this function will delete an employee's data from the holiday
			booking system and it will not be able to be retrieved.<p>
			<label>Please the employee ID and surname of the user to be deleted.:</label>
			<!-- input the id of the user to be deleted-->
			<input type="number" name="id" placeholder="Employee ID" required>
			<!-- input the surname of the user to be deleted-->
			<input type="text" name="last" placeholder="Surname" required>
			<label>Please enter your password:</label>
			<!-- password required for security purposes-->			
			<input type="password" placeholder="Enter Password" name="pwd" required>
			<!-- button to be clicked to delete user -->
			<button type="submit" name="delete">Delete Employee</button>
		</form>
	</div>		
</section>
<?php 
	include_once 'footer.php';
?>