<?php 
	$active = 'admincreate';//sets active page.
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
				<li><a href="admincreate.php" class="active">Create/Amend Employee</a></li>
				<li><a href="adminreset.php">Reset Employees Password</a></li>
				<li><a href="admindelete.php">Delete Employee Data</a></li>
				<li></li>
			</ul>
		</div>
		<form class="aform" action="includes/createamend-inc.php" method="post" >
			<!--admin team can enter data in order to either create a new employee or 
				amend an existing employees data.-->
			<h3>Create/Amend Employee</h3>
			<?php
			//check if an error or success message has been produced and return the relevant message.
			if(isset($_GET['error'])){ // check if error has been produced.
				if($_GET['error'] == 'emptyfields'){
					//echoes this message if an empty field error is produced.
					echo '<p class="formerror">Please fill in all fields.<p>';
				}else if($_GET['error'] == 'incorrectinput'){
					//echoes this message if incorrect characters are entered
					echo '<p class="formerror">Please only use letters for the name fields.<p>';
				}else if($_GET['error'] == 'idtaken'){
					//echoes this message if user id already exists in system when trying to create new entry.
					echo '<p class="formerror">Employee record cannot be created, user ID already exists.<p>';
				}else if($_GET['error'] == 'invalidid'){
					//echoes this message if user id does not exists in system when trying to amend an entry.
					echo '<p class="formerror">Employee ID not found in system.<p>';
				}else if($_GET['error'] == 'sqlerror'){
					//echoes this message if an sqlerror message is produced.
					echo '<p class="formerror">Internal error.<p>';
				}
			}else if(isset($_GET['success'])){
				if($_GET['success'] == 'create'){
					//echoes this message if new entry is successfully entered into employee table.
					echo '<p class="formsuccess">Employee data successsfully entered.<p>';
				}else if($_GET['success'] == 'amend'){
					//echoes this message if an entry is successfully amended in the employee table.
					echo '<p class="formsuccess">Employee data successsfully amended.<p>';
				}
			}			
			?>
			<!-- form to enter employee data into that will be stored in the database-->
			<!-- employee id number-->
			<input type="number" name="id" placeholder="Employee ID" required>
			<!-- employee's first name-->
			<input type="text" name="first" placeholder="First Name" required>
			<!-- employee's surname-->
			<input type="text" name="last" placeholder="Surname" required>
			<!-- employee's position held within the company-->
			<select name="position" required>
				<option value="" disabled selected hidden>Select Position</option>
				<option value="employee">Warehouse Operative</option>
				<option value="admin">Admin Dept</option>
				<option value="manager">Management Team</option>
			</select>
			<!-- employee's shift team-->
			<select name="shift" required>
				<option value="" disabled selected hidden>Select Shift</option>
				<option value="red">Red</option>
				<option value="blue">Blue</option>
				<option value="night">Nights</option>
				<option value="night">Admin</option>
			</select>
			<!-- employee's team number-->
			<input type="number" name="team" min="1" max="10" placeholder="Team" required>
			<!-- click to create a new entry for an employee's data within the database-->
			<button type="submit" name="create" onclick="">Create</button>
			<!-- click to amend and existing entry for an employee's data within the database-->
			<button type="submit" name="amend" onclick="">Amend</button>
		</form>			
	</div>		
</section>
<?php 
	include_once 'footer.php';
?>