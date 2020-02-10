<?php 	
	$active = 'passchange';
	include_once 'header.php';
?>		
<section class = "main-container">
	<div class = "main-wrapper">
		<form class="aform" action="includes/changepass-inc.php" method="POST">
			<h3>Change Password</h3>
			<?php
			if(isset($_GET['error'])){ // check if error has been produced.
				if($_GET['error'] == 'emptyfields'){
					//echoes this message if an empty field error is produced.
					echo '<p class="formerror">Please fill in all fields.<p>';
				}if($_GET['error'] == 'passmatch'){
					//echoes this message if 'newpwd' & 'confirmpwd' input fields do not match.
					echo '<p class="formerror">Both new password fields do not match..<p>';
				}if($_GET['error'] == 'invalidchar'){
					//echoes this message if invalid character is used.
					echo '<p class="formerror">Please only use number and letters.<p>';
				}else if($_GET['error'] == 'incorrect'){
					//echoes this message if user enters their existing password incorrectly.
					echo '<p class="formerror">Existing password entered is incorrect.<p>';
				}else if($_GET['error'] == 'sqlerror'){
					//echoes this message if an sqlerror message is produced.
					echo '<p class="formerror">Internal error.<p>';
				}
			}if(isset($_GET['success'])){	
				if($_GET['success'] == 'changed'){
					//password has successfully been changed.
					echo '<p class="formsuccess">Password changed successfully.<p>';
				}
			}
			?>
			<p>Please use letters, numbers only, with no spaces, minimum 6, maximum 32 chracters.</p>
			<!-- enter existing password here -->
			<input type="password" name="pwd" placeholder="Enter Existing Password">
			<!-- enter new pasword-->
			<input type="password" name="newpwd" placeholder="Enter New Password"  minlength=6 maxlength=32>
			<!-- confirm new password 'newpd' must equal 'confirmpwd'--> 
			<input type="password" name="confirmpwd" placeholder="Confirm New Password" minlength=6 maxlength=32>
			<!-- submit form-->
			<button type="submit" name="change">Change Password</button><br>		
		</form>
	</div>
</section>
<?php 
	include_once 'footer.php';
?>
