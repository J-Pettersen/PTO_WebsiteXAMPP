<?php
session_start();
if(isset($_POST['cancel'])){	
	include_once 'dbh-inc.php';	
	$selecteddate = $_POST['canceldate'];
	$currentdate = date('Y-m-d');
	$id = $_SESSION['emp_id'];
	//FUNCTIONS ////////////////	
	//function that gets the date selected by the user in the argument $given and
	//works out which year the tax year of a given date began, ths is based on 
	// the month and year values of the argument $selecteddate
	//it returns this value as an int.
	function getTaxYear($selecteddate) : String {			
		$month = date("m",strtotime($selecteddate)); //gets month value from date picked
		$year = date("Y",strtotime($selecteddate)); //gets year value from date picked.
		// tax year begins in April, if month selected is before April then the 
		// tax year is the previous year to the date selected.
		if ( $month < 4 ) {
			$year = $year - 1;
		}
		return $year;
	}	
	//
	//Gets existing number of user holidays booked and reduces it by one.
	//
	function getNewHolidayAllowance($id,$taxYear,$conn) : int{
		//check if holiday date exists
		//create a template
		$sql = "SELECT * FROM holiday_allowance WHERE employee_id=? AND tax_year_start=?";
		//create a prepared statement
		$stmt = mysqli_stmt_init($conn);
		//Prepare the prepared statement
		if(!mysqli_stmt_prepare($stmt,$sql)){
			header("Location: ../managepto.php?error=sqlerror");
			exit();
		}else{							
			mysqli_stmt_bind_param($stmt,"is",$id,$taxYear);
			//Run parameters inside database
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if(mysqli_num_rows($result) == 0){
				//should not be possible to reach this error message if all data is untampered in database.
				session_unset();
				session_destroy();
				header("Location: ../login.php?error=sessionerror");		
				exit();//user is logged out and sent to login page.
			}else{
				if($row = mysqli_fetch_assoc($result)){
					$_SESSION['booked'] = $row['booked'] - 1; //decreases number of booked holidays
					$_SESSION['remaining'] = $row['allowance'] - $_SESSION['booked'];//decreases number of remaining holidays
					return $_SESSION['booked'];	//returns new number of booked holidays to be amended in database			
				}
			}
		}
	}	
	//
	//Gets existing number of user holidays booked and reduces it by one.
	//
	function getShiftAllowance($selecteddate,$conn){
		///check if shift date exists withi shift table
		//create a template
		$sql = "SELECT * FROM shift WHERE the_date=?";
		//create a prepared statement
		$stmt = mysqli_stmt_init($conn);
		//Prepare the prepared statement
		if(!mysqli_stmt_prepare($stmt,$sql)){
			header("Location: ../managepto.php?error=sqlerror");
			exit();
		}else{
			//bind parameters to the placeholder
			mysqli_stmt_bind_param($stmt,"s",$selecteddate);
			//Run parameters inside database
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if(mysqli_num_rows($result) == 0){
				//should not be possible to reach this error message if all data is untampered in database.
				session_unset();
				session_destroy();
				header("Location: ../login.php?error=sessionerror");		
				exit();//user is logged out and sent to login page.				
			}else{
				if($row = mysqli_fetch_assoc($result)){
					$_SESSION['the_date'] = $row['the_date'];
					$_SESSION['red_avail'] = $row['red'];
					$_SESSION['blue_avail'] = $row['blue'];
					$_SESSION['night_avail'] = $row['night'];
					$_SESSION['admin_avail'] = $row['admin'];	
				}else{
					header("Location: ../managepto.php?error=sqlerror");
					exit();
				}
			}	
		}
	}
	//
	// check what shift user belongs to and increase the amount of
	// holidays available for that shift on the selected date.
	//
	function increaseAvailable(){	
		$shiftTeam = $_SESSION['emp_shift'];
		//assigns the amount of holidays available left for users shift to a varaible.
		//and increase the number available by one of the users current shift
		switch ($shiftTeam) {
				case "red": //user is in red shift
					echo $holsAvail = $_SESSION['red_avail'];
					echo $_SESSION['red_avail'] = $_SESSION['red_avail'] +1;
					break;
				case "blue": //user is in blue shift
					echo $holsAvail = $_SESSION['blue_avail'];
					echo $_SESSION['blue_avail'] = $_SESSION['blue_avail'] +1;
					break;
				case "night": //user is in night shift
					echo $holsAvail = $_SESSION['night_avail'];
					echo $_SESSION['night_avail'] = $_SESSION['night_avail'] +1;
					break;
				case "admin": //user is admin
					echo $holsAvail = $_SESSION['admin_avail'];
					echo $_SESSION['admin_avail'] = $_SESSION['admin_avail'] +1;
					break;
		}	
	}	
	//MAIN ////////////////////////////////	
	//Error handlers
	//Check if date picker is empty
	if(empty($selecteddate)||empty($id)){
		//one or more values are emppty
		header("Location: ../managepto.php?error=emptyfields");
		exit();
	}else{
		if($selecteddate <= $currentdate){//is date more than one day ahead?
			//date selected is too soon.
			header("Location: ../managepto.php?error=toosoon");
			exit();
		}else{
			//check if holiday date exists inside employee_holiday table.
			//create a template
			$sql = "SELECT * FROM employee_holiday WHERE employee_id=? AND holiday_date=?";
			//create a prepared statement
			$stmt = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt,$sql)){
				header("Location: ../managepto.php?error=sqlerror");
				exit();
			}else{
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt,"is",$id,$selecteddate);
				//Run parameters inside database
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if(mysqli_num_rows($result) == 0){ //holiday does not exist on selected date.
					header("Location: ../managepto.php?error=invaliddate");
					exit();					
				}else{ // holiday exists
					//check if holiday is denied or accepted.
					$row = mysqli_fetch_assoc($result);
					$accepted = $row['accepted'];
					if($accepted!=1){
						//cannot delete denied holiday requests.
						header("Location: ../managepto.php?error=rejected");
						exit();	
					}else{	
						//Create template to delete selected holiday
						$sql_delete = "DELETE FROM employee_holiday WHERE employee_id=? AND holiday_date=?";
						$stmt_delete = mysqli_stmt_init($conn);
						if(!mysqli_stmt_prepare($stmt_delete,$sql_delete)){						
								header("Location: ../managepto.php?error=sqlerror");
								exit();	
						}else{
							// run prepared statement to add shift to database.
							mysqli_stmt_bind_param($stmt_delete,"is",$id, $selecteddate);
							mysqli_stmt_execute($stmt_delete);	
							// returns selected tax year.
							$taxYear = getTaxYear($selecteddate);
							//returns users number of booked holidays
							$newBooked = getNewHolidayAllowance($id,$taxYear,$conn); 
							
							//Amend holiday_allowance table with new booked value
							//reduce booked number  for user by one is databse.
							$sql = "UPDATE holiday_allowance SET booked=? WHERE employee_id=? AND tax_year_start=?;";
							$stmt = mysqli_stmt_init($conn);
							if(!mysqli_stmt_prepare($stmt,$sql)){
								header("Location: ../managepto.php?error=sqlerror");
								exit();	
							}else{
								//run prepared statement to amend booked value in holiday_allowance table.
								mysqli_stmt_bind_param($stmt,"iis",$newBooked,$id,$taxYear);
								mysqli_stmt_execute($stmt);
								//retrieve availability of shifts on selected date.
								getShiftAllowance($selecteddate,$conn);
								//increase value of holidays available for selected shift and date by one.
								increaseAvailable();
								//update the shift table with new increased holiday availability.							
								$sql = "UPDATE shift SET red=?, blue=?,night=?,admin=? WHERE  the_date=?;";
								$stmt = mysqli_stmt_init($conn);
								if(!mysqli_stmt_prepare($stmt,$sql)){
									header("Location: ../managepto.php?error=sqlerror");
									exit();	
								}else{
									mysqli_stmt_bind_param($stmt,"iiiis",$_SESSION['red_avail'],
										$_SESSION['blue_avail'],$_SESSION['night_avail'],
										$_SESSION['admin_avail'],$selecteddate);
									mysqli_stmt_execute($stmt);
									header("Location: ../managepto.php?success=cancel");
									exit();	
								}
							}
						}
					}
				}
			}
		}
	}
}else{//redirect improper access
	header("Location: ../home.php?error=nodirectaccess");
	exit();
}