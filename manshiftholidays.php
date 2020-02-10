<?php 
	$active = 'shiftholidays';
	include_once 'header.php';	
	include 'includes/dbh-inc.php';
?>
<section class = "main-container">
	<div class = "main-wrapper">
		<form class="aform" action="includes/shiftholidays-inc.php" method="POST" >
			<h3>Booked Employee Holidays</h3>	
			<?php
			//check if an error or success message has been produced and return the relevant message.
			if(isset($_GET['error'])){ // check if error has been produced.
				if($_GET['error'] == 'emptyfields'){
					//echoes this message if an empty field error is produced.
					echo '<p class="formerror">Please select a date and shift.<p>';
				}
			}if(isset($_GET['success'])&&($_GET['success'] == 'retrieved')){	
				//echoes this message if data is successfully retrieved
				echo '<p class="formsuccess">Data Retrieved succesfully<p>';
			}
			?>
			<label>Select Date	:	</label><br>
			<!-- select date to view booked holidays, passed to select statement to retrieve relevant data-->
			<input type="date" name="date" required><br>
			<label>Select Shift	:	</label><br>
			<!-- drop down menu contains list of shifts, passed to select statement to retrieve relevant data-->
			<select name="shift" required>
				<option value="" disabled selected hidden>Select Shift</option>
				<option value="blue">Blue</option>
				<option value="red">Red</option>
				<option value="night">Night</option>
				<option value="admin">Admin</option>
			</select>
			<label>Date			-	</label>
			<label style ="float: right;">
			<?php echo (isset($_SESSION['the_date'])) ? $_SESSION['the_date'] : '';?>
			</label><br> <!-- show date-->
			<label>Shift		:	</label>
			<label style ="float: right;">
			<?php echo (isset($_SESSION['selected_shift'])) ? $_SESSION['selected_shift'] : '';?>
			</label><br>	<!-- show selected shift-->
			<label>Availability	:	</label>
			<label style ="float: right;">
			<?php echo (isset($_SESSION['shift_avail'])) ? $_SESSION['shift_avail'] : '';?>
			</label><br> <!-- show amount of holidays left for selected shift-->
			<button type="submit" name="viewhols">View Holidays</button><br>	
		</form>
		<!-- table filled with holiday request gotten from databse that match selected date and shift-->
		<table>
			<tr>
				<th>EMPLOYEE ID</th>
				<th>HOLIDAY DATE</th>
				<th>ACCEPTED</th>
				<th>BOOKED ON</th>
			</tr>				
			<?php
			if(!isset($_SESSION['the_date'])){
				//do nothing if the date isn't picked.
			}else{
				if(!isset($_SESSION['selected_shift'])){
				//do nothing if the shift isn't picked.
				}else{					
					$sql = "SELECT * FROM employee_holiday WHERE holiday_date=? AND shift=?";
					//create a prepared statement
					$stmt = mysqli_stmt_init($conn);
					//Prepare the prepared statement
					if(!mysqli_stmt_prepare($stmt,$sql)){
						header("Location: managepto.php?error=sqlerror");
						exit();
					}else{
						//bind parameters to the placeholder
						mysqli_stmt_bind_param($stmt,"ss",$_SESSION['the_date'],$_SESSION['selected_shift']);
						//Run parameters inside database
						mysqli_stmt_execute($stmt);
						$result = mysqli_stmt_get_result($stmt);
						if(mysqli_num_rows($result) > 0){	
							while($row = mysqli_fetch_assoc($result)){
								if($row['accepted'] == 1){ //has holiday request been accepted
									$accepted = "Accepted"; //request accepted
								}else{
									$accepted = "Denied"; //request denied
								}							
								echo "<tr><td>".$row['employee_id']."</td><td>".$row['holiday_date']."</td><td>".$accepted.
								"</td><td>".$row['booked_on']."</td></tr>"; 
								//display holiday request data in table
							}
							echo "</table>"; //close table 
						}else{
							//display this if no holiday requests found for date.
							echo "<tr><td>0 holidays booked for selected date.</td></tr>"; 
						}
					}
				}					
			}
			$conn -> close();
			?>
	</div>
</section>		
<?php 
	include_once 'footer.php';
?>
