<?php 	
	$active = 'book';
	include_once 'header.php';
?>		
<section class = "main-container">
	<div class = "main-wrapper">
		<div class="vertical">
			<ul>
				<li><a href="book.php">Book Paid Time Off</a></li>
				<li><a href="check_avail.php" class="active">Check Holiday Availability</a></li>				
				<li></li>
			</ul>
		</div>
		<form class="aform" action="includes/check-inc.php" method="POST" >
			<h2>Check Availability</h2>	
			<?php
			//check if an error or success message has been produced and return the relevant message.
			if(isset($_GET['error'])){ // check if error has been produced.
				if($_GET['error'] == 'emptyfields'){
					//echoes this message if an empty field error is produced.
					echo '<p class="formerror">Please pick a date.<p>';
				}else
					if($_GET['error'] == 'past'){
					//shows this error if the date selected is prior to current date.
					echo '<p class="formerror">Date selected cannot be prior to today.<p>';
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
			<!--allows user to check if the selected has holidays available-->
			<label>Select Date	:	</label>
			<input type="date" name="datecheck" required><br>				
			<button type="submit" name="check">Check Availability</button><br>
			<h3>Holidays Available</h3>
			<label>Date		-	</label>
			<label style ="float: right;">
			<?php echo (isset($_SESSION['the_date'])) ? $_SESSION['the_date'] : '';?>
			</label><br><!--  date of selected shift-->
			<label>Red	:	</label>
			<label style ="float: right;">
			<?php echo (isset($_SESSION['red_avail'])) ? $_SESSION['red_avail'] : '';?>
			</label><br><!-- Holidays available on red shift for selected date.-->
			<label>Blue	:	</label>
			<label style ="float: right;">
			<?php echo (isset($_SESSION['blue_avail'])) ? $_SESSION['blue_avail'] : '';?>
			</label><br><!-- Holidays available on blue shift for selected date.-->
			<label>Nights	:	</label>
			<label style ="float: right;">
			<?php echo (isset($_SESSION['night_avail'])) ? $_SESSION['night_avail'] : '';?>
			</label><br><!-- Holidays available on night shift for selected date.-->
			<label>Admin	:	</label>
			<label style ="float: right;">
			<?php echo (isset($_SESSION['admin_avail'])) ? $_SESSION['admin_avail'] : '';?>
			</label><br><!-- Holidays available for admin staff for selected date.-->
		</form>			
	</div>
</section>
<?php 
	include_once 'footer.php';
?>
