<?php 
	$active = 'home';
	include_once 'header.php';
?>
<section class = "main-container">
	<div class = "main-wrapper">		
		<div class="area1">
			<br>
			<!-- list of users details deisplayed on home page-->
			<label>Employee ID - </label>
			<label id="id" class="data"><?php echo $_SESSION['emp_id'];?></label><br>
			<label>Full Name - </label>
			<label id="name" class="data"><?php echo $_SESSION['emp_first']," ",$_SESSION['emp_last'];?></label><br>
			<label>Dept - </label>
			<label id="position" class="data"><?php echo $_SESSION['emp_position'];?></label><br>
			<label>Section - </label>
			<label id="shift" class="data"><?php echo $_SESSION['emp_shift'];?></label><br>
			<label>Team - </label>
			<label id="team" class="data"><?php echo $_SESSION['emp_team'];?></label>
		</div>
		<div class="area2">
		<?php
		//check if an error or success message has been produced and return the relevant message.
		if(isset($_GET['error'])){ // check if error has been produced.
			if($_GET['error'] == 'nodirectaccess'){
				//error message if user attempts to directly call actioned files.
				echo '<p class="formerror">No Direct Access.<p>';
			}
		}
		?>
		</div>
	</div>
</section>
<?php 
	include_once 'footer.php';
?>
