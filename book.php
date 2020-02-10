<?php 	
	$active = 'book';
	include_once 'header.php';
?>		
<section class = "main-container">
	<div class = "main-wrapper">
		<div class="vertical">
			<ul>
				<li><a href="book.php" class="active">Book Paid Time Off</a></li>
				<li><a href="check_avail.php">Check Holiday Availability</a></li>				
				<li></li>
			</ul>
		</div>
		<form class="aform" action="includes/book-inc.php" method="POST" >
			<h2>Book</h5>	
			<?php
			//check if an error or success message has been produced and return the relevant message.
			if(isset($_GET['error'])){ // check if error has been produced.
				if($_GET['error'] == 'emptyfields'){
					//echoes this message if an empty field error is produced.
					echo '<p class="formerror">Please fill in all fields.<p>';
				}else if($_GET['error'] == 'toosoon'){
					//echoes this message if date selected is not two days in advance.
					echo '<p class="formerror">The date you have selected is too soon or in the past.
					Please select a date at least two days from now.<p>';
				}else if($_GET['error'] == 'prebooked'){
					//echoes this message if date has already been booked off by user.
					echo '<p class="formerror">You have already successfully booked this date off.<p>';
				}else if($_GET['error'] == 'userhols'){
					//echoes this message if user has no holidays remaining.
					echo '<p class="formerror">Holiday request denied. User has no holidays remaining for selected tax year.<p>';
				}else if($_GET['error'] == 'shifthols'){
					//echoes this message if user has no holidays remaining.
					echo '<p class="formerror">Holiday request denied. No holidays remaining for selected date.<p>';
				}else if($_GET['error'] == 'sqlerror'){
					//echoes this message if an sqlerror message is produced.
					echo '<p class="formerror">Internal error.<p>';
				}
			}if(isset($_GET['success'])){
				if($_GET['success'] == 'create'){
					//echoes this message if a holiday request is accepted and created.
					echo '<p class="formsuccess">Holiday request accepted.<p>';
				}if($_GET['success'] == 'amend'){
					//echoes this message if a holiday request is amended from denied to accepted.
					echo '<p class="formsuccess">Holiday request accepted.<p>';
				}
			}
			?>
			<!--allows user to select a date to book off.-->
			<label>Select Date to Book :	</label><input type="date" name="datebook" required><br>				
			<button type="submit" name="book">Book</button><br>			
		</form>		
	</div>
</section>
<?php 
	include_once 'footer.php';
?>