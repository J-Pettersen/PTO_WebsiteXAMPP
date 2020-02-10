<?php	
if(isset($_POST['create'])){//script only accesible through correct post value.	
	//connect to database
	include_once 'dbh-inc.php';	
	//variables
	$id = intval($_POST['id']);
	$first = mysqli_real_escape_string($conn,$_POST['first']);
	$last = mysqli_real_escape_string($conn,$_POST['last']);
	$position = mysqli_real_escape_string($conn,$_POST['position']);
	$shift = mysqli_real_escape_string($conn,$_POST['shift']);
	$team = intval($_POST['team']);	
	//Create new entry in employee table.////////////////////		
	//Error handlers
	//Check for empty fields
	if(empty($id)||empty($first)||empty($last)||empty($shift)||empty($team)||empty($position)){
		header("Location: ../admincreate.php?error=emptyfields");
		//empty field found, return error message.
		exit();
	}else{			
		//Check if correct input
		if(!preg_match("/^[a-zA-Z]*$/",$first)||!preg_match("/^[a-zA-Z]*$/",$last)||!is_numeric($team)||!is_numeric($id)){
			header("Location: ../admincreate.php?error=incorrectinput");
			//improper input found, return error message.
			exit();
		}else{
			//Check emplpoyee id is unique.
			//create a template
			$sql = "SELECT * FROM employee WHERE id=?";
			//create a prepared statement
			$stmt = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt,$sql)){
				header("Location: ../admincreate.php?error=sqlerror");
				//sql error, query wont run in database.
				exit();
			}else{
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt,"i",$id);
				//Run parameters inside database
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if(mysqli_num_rows($result) == 1){
					//id already exists, return error message.
					header("Location: ../admincreate.php?error=idtaken");
					exit();
				}else{
					//id doesn't already exists...
					//Add user to database
					//Hash password
					$hashedpassword = password_hash($last, PASSWORD_DEFAULT);
					//create a template
					$sql = "INSERT INTO employee (id, first, last, position, shift, team, pwd) VALUES (?,?,?,?,?,?,?);";
					//create a prepared statement
					$stmt = mysqli_stmt_init($conn);
					if(!mysqli_stmt_prepare($stmt,$sql)){						
						header("Location: ../admincreate.php?error=sqlerror");
						//sql error, query wont run in database
						exit();	
					}else{
						//bind parameters to the placeholder
						mysqli_stmt_bind_param($stmt,"issssis", $id,$first,$last,$position,$shift,$team,$hashedpassword);
						//Run parameters inside database
						mysqli_stmt_execute($stmt);
						//entry inserted successfully, return success message.
						header("Location: ../admincreate.php?success=create");
						exit();	
					}									
				}
			}
		}			
	}
}else if(isset($_POST['amend'])){//script only accesible through correct post value.		
	//connect to database
	include_once 'dbh-inc.php';	
	//variables.
	$id = intval($_POST['id']);
	$first = mysqli_real_escape_string($conn,$_POST['first']);
	$last = mysqli_real_escape_string($conn,$_POST['last']);
	$position = mysqli_real_escape_string($conn,$_POST['position']);
	$shift = mysqli_real_escape_string($conn,$_POST['shift']);
	$team = intval($_POST['team']);	
	//Amend existing user/////////////////////////////////////
	//Error handlers
	//Check for empty fields
	if(empty($id)||empty($first)||empty($last)||empty($shift)||empty($team)||empty($position)){
		header("Location: ../admincreate.php?error=emptyfields");
		exit();
	}else{
		//Check if correct input
		if(!preg_match("/^[a-zA-Z]*$/",$first)||!preg_match("/^[a-zA-Z]*$/",$last)||!is_numeric($team)||!is_numeric($id)){
				header("Location: ../admincreate.php?error=incorrectinput");
				exit();
		}else{
			//check if id exists
			//create a template
			$sql = "SELECT * FROM employee WHERE id=?";
			//create a prepared statement
			$stmt = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt,$sql)){
				//sql error, query wont run in database
				header("Location: ../admincreate.php?error=sqlerror");
				exit();
			}else{
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt,"i",$id);
				//Run parameters inside database
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if(mysqli_num_rows($result) == 0){
					//id does not exist within table, return error message.
					header("Location: ../admincreate.php?error=invalidid");
					exit();
				}else{					
					//Amend user data in database
					$sql = "UPDATE employee SET first=?, last=?, position=?, shift=?, team=? WHERE id=?;";
					$stmt = mysqli_stmt_init($conn);
					if(!mysqli_stmt_prepare($stmt,$sql)){
						//sql error, query wont run in database
						header("Location: ../admincreate.php?error=sqlerror");
						exit();	
					}else{
						//bind parameters to the placeholder
						mysqli_stmt_bind_param($stmt,"ssssii",$first,$last,$position,$shift,$team,$id);
						//Run parameters inside database
						mysqli_stmt_execute($stmt);
						//entry amended successfully, return success message.
						header("Location: ../admincreate.php?success=amend");
						exit();	
					}
				}
			}
		}			
	}
}else{//redirect improper access
	header("Location: ../home.php?error=nodirectaccess");
	exit();
}