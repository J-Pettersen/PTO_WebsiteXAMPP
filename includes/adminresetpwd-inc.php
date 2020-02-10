<?php
if(isset($_POST['reset'])){//script only accesible through correct post value.		
	//connect to database	
	include_once 'dbh-inc.php';	
	//variables		
	$id = $_POST['id'];
	$last = mysqli_real_escape_string($conn,$_POST['last']);	
	//MAIN//////
	// Reset password of a user to their last name/////////////////////////////
	//Error handlers
	//Check for empty fields
	if(empty($id)||empty($last)){
		header("Location: ../adminreset.php?error=emptyfields");
		exit();//one or both varaibles are empty.
	}else{
		//Check if correct input
		if(!preg_match("/^[a-zA-Z]*$/",$last)){
				header("Location: ../adminreset.php?error=incorrectinput");
				exit(); //incorrect character input
		}else{
			//check if id exists & matches name
			//create a template
			$sql = "SELECT * FROM employee WHERE id=? AND last=?";
			//create a prepared statement
			$stmt = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt,$sql)){
				header("Location: ../adminreset.php?error=sqlerror");
				exit();
			}else{
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt,"is",$id,$last);
				//Run parameters inside database
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				//$resultCheck is assigned the value of how many results are returned from the statement.
				$resultCheck = mysqli_num_rows($result);
				if($resultCheck == 0){	//no results for id and name combination found.
					header("Location: ../adminreset.php?error=invalidid");
					exit();
				}else{	
					//Amend user data in database
					//create a template
					$sql = "UPDATE employee SET pwd=? WHERE id=?";
					//create a prepared statement
					$stmt = mysqli_stmt_init($conn);	
					//Prepare the prepared statement
					if(!mysqli_stmt_prepare($stmt,$sql)){
						header("Location: ../adminreset.php?error=sqlerror");
						exit(); //sql error
					}else{
						//Hash password
						$hashedpassword = password_hash($last, PASSWORD_DEFAULT);
						//reset password to default . (pwd = last name)
						mysqli_stmt_bind_param($stmt,"si",$hashedpassword,$id);
						mysqli_stmt_execute($stmt);
						header("Location: ../adminreset.php?success=reset");
						exit();	//password successfully reset.
					}
				}
			}
		}			
	}
}else{ //redirect improper access
	header("Location: ../home.php?error=nodirectaccess");
	exit();
}