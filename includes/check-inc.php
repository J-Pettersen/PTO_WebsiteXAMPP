<?php

session_start();

if(isset($_POST['check'])){//script only accesible through correct post value.	

	include_once 'dbh-inc.php';//connect to database

	//variables
	$selecteddate = $_POST['datecheck'];//set varaible to value posted by date picker
	$currentdate = date('Y-m-d');//set varaible as current date.
	
	//Error handlers
	//Check if empty
	if(empty($selecteddate)){
		header("Location: ../check_avail.php?error=emptyfields");
		exit();
	}else{
		//check if shift date selected is after today.
		if($selecteddate < $currentdate){
			//date selected is in past. return error.
			header("Location: ../check_avail.php?error=past");
			exit();
		}else{
			//date selected is accepted.
			//check if shift date exists///
			//get list of dates that match that given by the user.
			//create a template
			$sql_select = "SELECT * FROM shift WHERE the_date=?";
			//create a prepared statement
			$stmt_select = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt_select,$sql_select)){
				header("Location: ../check_avail.php?error=sqlerror");
				exit();
			}else{
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt_select,"s",$selecteddate);
				//Run parameters inside database
				mysqli_stmt_execute($stmt_select);
				$result = mysqli_stmt_get_result($stmt_select);
				//whilst no entries in the shift table match that of the date given by the user...
				while(mysqli_num_rows($result) == 0){ 
					//shift date doesn't yet exist in databse, add date to database with default values.
					//create a template
					$sql_insert = "INSERT INTO shift (the_date) VALUES (?);";
					//create a prepared statement
					$stmt_insert = mysqli_stmt_init($conn);
					//Prepare the prepared statement
					if(!mysqli_stmt_prepare($stmt_insert,$sql_insert)){						
						header("Location: ../check_avail.php?error=sqlerror");
						exit();	
					}else{
						// run prepared statement to add date entry to shift to database.
						mysqli_stmt_bind_param($stmt_insert,"s", $selecteddate);
						mysqli_stmt_execute($stmt_insert);	
						//re run select perameters inside database
						mysqli_stmt_execute($stmt_select);
						//assign result to $result
						$result = mysqli_stmt_get_result($stmt_select);							
					}
				}				
				if($row = mysqli_fetch_assoc($result)){
					//assign data retrieved form databse to session varaibles.					
					//session varaibles will be displayed on web page.
					$_SESSION['the_date'] = $row['the_date'];
					$_SESSION['red_avail'] = $row['red'];
					$_SESSION['blue_avail'] = $row['blue'];
					$_SESSION['night_avail'] = $row['night'];
					$_SESSION['admin_avail'] = $row['admin'];
					header("Location: ../check_avail.php?success=retrieved");
					exit();
				}
				header("Location: ../check_avail.php?error=sqlerror");
				exit();					
			}
		}
	}
				
}else{
	header("Location: ../home.php?error=nodirectaccess");
	exit();
}	