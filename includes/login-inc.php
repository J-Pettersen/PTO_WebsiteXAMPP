<?php
session_start();
if(isset($_POST['submit'])){ //script only accesible through correct post value.	
	//connect to database
	include 'dbh-inc.php'; 	
	//variables.		
	$id = $_POST['id'];
	$pwd = mysqli_real_escape_string($conn,$_POST['pwd']);		
	//Log the usrer into the website////////////////
	//Error handlers
	//Check if empty
	if(empty($id)||empty($pwd)){
		header("Location: ../login.php?error=emptyfields");
		exit();
	}else{
		//check if user exists
		//create a template
		$sql = "SELECT * FROM employee WHERE id=?";
		//create a prepared statement
		$stmt = mysqli_stmt_init($conn);
		//Prepare the prepared statement
		if(!mysqli_stmt_prepare($stmt,$sql)){
			header("Location: ../login.php?error=sqlerror");
			exit();
		}else{
			//bind parameters to the placeholder
			mysqli_stmt_bind_param($stmt,"i",$id);
			//Run parameters inside database
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);					
			$resultCheck = mysqli_num_rows($result);
			if($resultCheck == 0){ 
				//user doesn't exist
				header("Location: ../login.php?error=incorrect");
				exit();
			}else{
				if($row = mysqli_fetch_assoc($result)){
					//De-hash password and verify if correct.
					$hashpwdcheck = password_verify($pwd, $row['pwd']);
					if($hashpwdcheck == false){
						//incorrect password
						header("Location: ../login.php?error=incorrect");
						exit();
					}elseif($hashpwdcheck == true){
						//login employee
						//set session variables with users information.
						$_SESSION['emp_id'] = $row['id'];
						$_SESSION['emp_first'] = $row['first'];
						$_SESSION['emp_last'] = $row['last'];
						$_SESSION['emp_position'] = $row['position'];
						$_SESSION['emp_shift'] = $row['shift'];
						$_SESSION['emp_team'] = $row['team'];
						header("Location: ../home.php?login=success");
						exit();
					}
				}
			}
		}				
	}	
}else{//redirect improper access
	header("Location: ../login.php?error=nodirectaccess"); 
	exit();
}