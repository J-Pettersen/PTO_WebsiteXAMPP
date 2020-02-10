<?php
session_start();
if(isset($_POST['viewhols'])){
	include_once 'dbh-inc.php'; //connect to database
	$selecteddate = $_POST['date']; //set date selected to variable
	$selectedshift = $_POST['shift'];	//set shift selected to variable
	//Error handlers
	//Check if empty fields
	if(empty($selecteddate)||empty($selectedshift)){
		header("Location: ../check_avail.php?error=emptyfields");
		exit();
	}else{				
		//check if shift date exists
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
			while(mysqli_num_rows($result) == 0){
				//shift date doesn't yet exist in databse, add date to database with default values.
				$sql_insert = "INSERT INTO shift (the_date) VALUES (?);";
				$stmt_insert = mysqli_stmt_init($conn);
				if(!mysqli_stmt_prepare($stmt_insert,$sql_insert)){						
					header("Location: ../check_avail.php?error=sqlerror");
					echo "SQL error!";
					exit();	
				}else{
					// run prepared statement to add shift to database.
					mysqli_stmt_bind_param($stmt_insert,"s", $selecteddate);
					mysqli_stmt_execute($stmt_insert);	
					mysqli_stmt_execute($stmt_select);
					$result = mysqli_stmt_get_result($stmt_select);							
				}
			}
			if($row = mysqli_fetch_assoc($result)){
				$_SESSION['the_date'] = $row['the_date']; //sets date as one selected				
				$_SESSION['selected_shift'] = $selectedshift; //sets as selected shift e.g. red						
				$_SESSION['shift_avail'] = $row[$selectedshift]; 
				header("Location: ../manshiftholidays.php?success=retrieved");
				exit();
			}	
		}		
	}
				
}else{
	header("Location: ../home.php?error=nodirectaccess");
	exit();
}	