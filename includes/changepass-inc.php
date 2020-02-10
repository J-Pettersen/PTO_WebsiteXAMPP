<?php
session_start();	
if(isset($_POST['change'])){	
	include_once 'dbh-inc.php';	
	//variables.		
	$id = $_SESSION['emp_id'];
	$pwd = mysqli_real_escape_string($conn,$_POST['pwd']); //set variable as password the user enters as their curent one.	
	$newpwd = mysqli_real_escape_string($conn,$_POST['newpwd']); //set variable as new entered passowrd
	$confirmpwd = mysqli_real_escape_string($conn,$_POST['confirmpwd']); //set variable as new password confirmation.	
	//check inputs aren't empty
	if(empty($id)||empty($pwd)||empty($newpwd)||empty($confirmpwd)){
		//one or more fields are empty. return error message.
		header("Location: ../passchange.php?error=emptyfields");
		exit();
	}else{
		//no empty fields found.
		//do both new passwords entered match?
		if($newpwd!=$confirmpwd){
			//new passwords do not match. return error message.
			header("Location: ../passchange.php?error=passmatch");
			exit();
		}else{
			//passwords match.
			//does newpassword contain prohibited characters?
			if(!preg_match("/^[a-zA-Z\-0-9]*$/",$newpwd)||!preg_match("/^[a-zA-Z\-0-9]*$/",$confirmpwd)){
				//new password contains prohibited characters. return error message.
				header("Location: ../passchange.php?error=invalidchar");
				exit();
			}else{
				//new password does not contain prohibited characters.
				//check if user exists
				//create a template
				$sql = "SELECT * FROM employee WHERE id=?";
				//create a prepared statement
				$stmt = mysqli_stmt_init($conn);
				//Prepare the prepared statement
				if(!mysqli_stmt_prepare($stmt,$sql)){
					header("Location: ../passchange.php?error=sqlerror");
					exit();
				}else{
					//bind parameters to the placeholder
					mysqli_stmt_bind_param($stmt,"i",$id);
					//Run parameters inside database
					mysqli_stmt_execute($stmt);
					$result = mysqli_stmt_get_result($stmt);					
					$resultCheck = mysqli_num_rows($result);
					if($resultCheck == 0){ 
						//user with same id as current user has not been found in databse.
						//either mysql error or session data is incorrec.
						//usser should log back in.
						session_unset();
						session_destroy();
						header("Location: ../login.php?error=sessionerror");		
						exit();//user is logged out and sent to login page.
					}else{
						if($row = mysqli_fetch_assoc($result)){
							//De-hash password and verify if correct.
							$hashpwdcheck = password_verify($pwd, $row['pwd']);
							if($hashpwdcheck == false){
								//incorrect password
								header("Location: ../passchange.php?error=incorrect");
								exit();
							}elseif($hashpwdcheck == true){
								//correct password.
								//Amend user data in database
								//create a template
								$sql = "UPDATE employee SET pwd=? WHERE id=?";
								//create a prepared statement
								$stmt = mysqli_stmt_init($conn);	
								//Prepare the prepared statement
								if(!mysqli_stmt_prepare($stmt,$sql)){
									header("Location: ../passchange.php?error=sqlerror");
									exit(); //sql error
								}else{
									//Hash password
									$hashedpassword = password_hash($newpwd, PASSWORD_DEFAULT);
									//set password to new password entered by user.
									mysqli_stmt_bind_param($stmt,"si",$hashedpassword,$id);
									mysqli_stmt_execute($stmt);
									header("Location: ../passchange.php?success=changed");
									exit();	//password successfully changed.
								}
							}						
						}else{
							header("Location: ../passchange.php?error=sqlerror");
							exit();
						}
					}
				}
			}
		}
	}
}else{//redirect improper access
	header("Location: ../login.php?error=nodirectaccess"); 
	exit();
}	