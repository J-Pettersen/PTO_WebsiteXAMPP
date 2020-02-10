<?php 
	$active = 'managepto';
	include_once 'header.php';
	include 'includes/dbh-inc.php';
?>
<section class = "main-container">
	<div class = "main-wrapper">
		<div class ="area2">
			<form class="aform" action="includes/viewallowance-inc.php" method="POST" >
				<!--shows employees holiday allowance for selected tax year-->
				<h3>Holiday Allowance</h3>				
				<?php
				//check if an error or success message has been produced and return the relevant message.
				if(isset($_GET['error'])){ // check if error has been produced.
					if($_GET['error'] == 'emptyfields'){
						//echoes this message if an empty field error is produced.
						echo '<p class="formerror">Please select a date.<p>';
					}else
						if($_GET['error'] == 'past'){
						//shows this error if the date selected is prior to current tax year.
						echo '<p class="formerror">Selected date cannot be prior to current tax year.<p>';
					}else
						if($_GET['error'] == 'sqlerror'){
						//echoes this message if an sqlerror message is produced.
						echo '<p class="formerror">Internal error.<p>';
					}
				}if(isset($_GET['success'])){ //check if success message as been produced.
					if($_GET['success'] == 'retrieved'){
						//success message shows if data is successfully retrieved.
						echo '<p class="formsuccess">Data successfully retrieved.<p>';
					}
				}
				?>				
				<label>Tax Year	:	</label>
				<label style ="float: right;">
				<?php echo (isset($_SESSION['taxyear'])) ? $_SESSION['taxyear'] : '';?>
				</label><br> <!-- displays selected tax year value-->
				<label>Allowance	:	</label>
				<label style ="float: right;">
				<?php echo (isset($_SESSION['allowance'])) ? $_SESSION['allowance'] : '';?>
				</label><br>	<!-- displays amount of holidays allocated to user foe selected tax year -->
				<label>Booked		:	</label>
				<label style ="float: right;">
				<?php echo (isset($_SESSION['booked'])) ? $_SESSION['booked'] : '';?>
				</label><br> <!-- displays amount of holidays booked  by user for selected tax year -->
				<label>Remaining	:	</label>
				<label style ="float: right;">
				<?php echo (isset($_SESSION['remaining'])) ? $_SESSION['remaining'] : '';?>
				</label><br> <!-- displays amount of holidays remaining for the user within the selected tax year. -->
				<br>
				<p> Please pick a date within the tax year that you wish to view your holiday allowance or holiday list for. 
				A tax year runs between 1st April and the 31st March.</p>
				<input type="date" name="datepick" required><!-- use this to pick date for selected tax year-->
				<!-- submit form to retrieve data about users holiday allowance for selected tax year.-->
				<button type="submit" name="viewallowance">View Holidays</button><br> 				
			</form>
		</div>
		<div class="area2">		
			<form class="aform" action="includes/delete-inc.php" method="POST" >
				<h3>Cancal Booked Holiday</h3>
				<?php
				//check if an error or success message has been produced and return the relevant message.
				if(isset($_GET['error'])){ // check if error has been produced.
					if($_GET['error'] == 'emptyfields'){
						//echoes this message if an empty field error is produced.
						echo '<p class="formerror">Please select a date.<p>';
					}else
						if($_GET['error'] == 'toosoon'){
						//shows this error if the date selected is before tommorow.
						echo '<p class="formerror">Selected date must be at least one day in advance.<p>';
					}else
						if($_GET['error'] == 'invaliddate'){
						//shows error if user selects a date they have not already successfully booked off.
						echo '<p class="formerror">You have no holidays booked off on this date.<p>';
					}else
						if($_GET['error'] == 'rejected'){
						//shows error if user tried to delete a holiday request that has been rejected.
						echo '<p class="formerror">You may not cancel a holiday request that has been denied.<p>';
					}else
						if($_GET['error'] == 'sqlerror'){
						//echoes this message if an sqlerror message is produced.
						echo '<p class="formerror">Internal error.<p>';
					}
				}if(isset($_GET['success'])){ //check if success message as been produced.
					if($_GET['success'] == 'cancel'){
						//success message shows if holiday request is successfully cancelled.
						echo '<p class="formsuccess">Holiday successfully cancelled.<p>';
					}
				}
				?>	
				<!--date can be selected to cancel a holiday on, must exist to be successful-->				
				<p>Use the date picker to select the date of a holiday you wish to cancel.</p>
				<label>Select Date	:	</label><input type="date" name="canceldate" required><br>			
				<button type="submit" name="cancel" >Cancel PTO</button><br>	
			</form>
		</div>
		<!-- Table showing all the users holiday requests from the selected tax year -->
		<table>
			<tr>
				<th>HOLIDAY DATE</th>
				<th>ACCEPTED</th>
				<th>SHIFT</th>
				<th>BOOKED ON</th>
			</tr>
		<?php
		if(!isset($_SESSION['taxyear'])){				
			//do nothing if the tax year isn't picked.
		}else{
			$sql = "SELECT * FROM employee_holiday WHERE employee_id=? AND tax_year_start=?";
			//create a prepared statement
			$stmt = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt,$sql)){
				header("Location: managepto.php?error=sqlerror");
				exit();
			}else{
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt,"is",$_SESSION['emp_id'],$_SESSION['taxyear']);
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
						echo "<tr><td>".$row['holiday_date']."</td><td>".$accepted.
						"</td><td>".$row['shift']."</td><td>".$row['booked_on']."</td></tr>"; 
						//display holiday request data in table
					}
					echo "</table>"; //close table 
				}else{
					 //display this if no holiday requests found for tax year.
					echo "<tr><td>0 holidays booked for selected tax year.</td></tr>";
				}
			}				
		}
		?>
		</table>
		
	</div>
</section>		
<?php 
	include_once 'footer.php';
?>