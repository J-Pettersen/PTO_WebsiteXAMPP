<?php
session_start();
if(isset($_POST['viewallowance'])){//script only accesible through correct post value.	
	//connect to database
	include_once 'dbh-inc.php';
	//varaibles
	$selecteddate = $_POST['datepick'];//set varaible to value posted by date pick
	$currentdate = date('Y-m-d');//set varaible as current date.
	$id = $_SESSION['emp_id'];//get user id from session varaible
	//FUNCTIONS /////////////////////////////////////////////////////////	
	//function that gets the date selected by the user in the argument $given and
	//works out which year the tax year of a given date began, ths is based on 
	// the month and year values of the argument $givenyear
	//it returns this value as an int.
	function getTaxYear($givenyear) : int {	
		//gets month value from date picked
		$month = date("m",strtotime($givenyear));
		//gets year value from date picked.
		$year = date("Y",strtotime($givenyear)); 
		// tax year begins in April, if month selected is before April then the 
		// tax year is the previous year to the date selected.
		if ( $month < 4 ) {
			$year = $year - 1;
		}
		return $year; //returns value as int. 
	}	
	//MAIN //////////////////////////////////////////////////////	
	//Error handlers
	//Check if empty
	if(empty($selecteddate)||empty($id)){
		//one or more values are empty
		header("Location: ../managepto.php?error=emptyfields");
		exit();
	}else{
		//get the tax year of the date from function.
		$selectedTaxYear = getTaxYear($selecteddate); 
		//get the earliest 
		$minimumYear = getTaxYear($currentdate);			
		//check if selected tax year is earlier than current tax year.
		if($selectedTaxYear<$minimumYear){
			//date selected is too early. return error message..
			header("Location: ../managepto.php?error=past");
			exit();
		}else{
			//date selected is accepted.
			//check if shift date exists
			//create a template
			$sql_select = "SELECT * FROM holiday_allowance WHERE employee_id=? AND tax_year_start=?";
			//create a prepared statement
			$stmt_select = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt_select,$sql_select)){
				header("Location: ../managepto.php?error=sqlerror");
				exit();
			}else{
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt_select,"is",$id,$selectedTaxYear);
				//Run parameters inside database
				mysqli_stmt_execute($stmt_select);
				//assign query results to $result
				$result = mysqli_stmt_get_result($stmt_select);
				//if no results are returned from select query.
				if(mysqli_num_rows($result) == 0){
					//shift date doesn't yet exist in databse, add selected date to database.
					$sql_insert = "INSERT INTO holiday_allowance (employee_id,tax_year_start) VALUES (?,?);";
					$stmt_insert = mysqli_stmt_init($conn);						
					if(!mysqli_stmt_prepare($stmt_insert,$sql_insert)){						
						header("Location: ../managepto.php?error=sqlerror");
						exit();	
					}else{
						// run prepared statement to add shift to database.
						mysqli_stmt_bind_param($stmt_insert,"is",$id, $selectedTaxYear);
						mysqli_stmt_execute($stmt_insert);	
						//rerun select parameters inside databas
						mysqli_stmt_execute($stmt_select);
						//assign query results to $result
						$result = mysqli_stmt_get_result($stmt_select);							
					}
				}				
				if($row = mysqli_fetch_assoc($result)){
					//assign data retrieved form databse to session varaibles.
					//session varaibles will be displayed on web page.
					$_SESSION['taxyear'] = $row['tax_year_start'];
					$_SESSION['allowance'] = $row['allowance'];
					$_SESSION['booked'] = $row['booked'];
					$_SESSION['remaining'] = $_SESSION['allowance'] - $_SESSION['booked'];
					header("Location: ../managepto.php?success=retrieved");
					exit();	
				}else{
					header("Location: ../managepto.php?error=sqlerror");
					exit();	
				}
			}							
		}
	}
}else{//redirect improper access
	header("Location: ../home.php?error=nodirectaccess");
	exit();
}