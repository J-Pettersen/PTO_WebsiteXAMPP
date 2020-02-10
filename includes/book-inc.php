<?php

session_start();

if(isset($_POST['book'])){

	include_once 'dbh-inc.php';

	$selecteddate = $_POST['datebook']; //date given from date picker on web page.		
	$currentdate = date('Y-m-d');	//todays date.
	$tomorrow = strtotime("+1 day", strtotime($currentdate)); //returns the date of tommorrow
	$date = date("Y-m-d", $tomorrow);
	$taxYear = date('Y');		// tax year in formal 'YYYY'					
	$shiftTeam = $_SESSION['emp_shift']; //current users shift.
	$id = $_SESSION['emp_id']; //current users id.
	$existrejected = 0; //initialise variable to false.
	$nohols=0;
	
	// FUNCTIONS //////////////////////////////////////////////////////////
		
	//
	// check what shift user belongs to and if there are any 
	// holidays available for that shift on the selected date.
	//
	function holsAvailable($shiftTeam) : int{			
		//assigns the amount of holidays available left for users shift to a varaible.
		//then reduces the number available by one of the users current shift selected.
		switch ($shiftTeam) {
				case "red":
					echo $holsAvail = $_SESSION['red_avail'];
					echo $_SESSION['red_avail'] = $_SESSION['red_avail'] -1;
					break;
				case "blue":
					echo $holsAvail = $_SESSION['blue_avail'];
					echo $_SESSION['blue_avail'] = $_SESSION['blue_avail'] -1;
					break;
				case "night":
					echo $holsAvail = $_SESSION['night_avail'];
					echo $_SESSION['night_avail'] = $_SESSION['night_avail'] -1;
					break;
				case "admin":
					echo $holsAvail = $_SESSION['admin_avail'];
					echo $_SESSION['admin_avail'] = $_SESSION['admin_avail'] -1;
					break;
		}
		return $holsAvail;	
	}
	//
	// CREATE BOOKING WITHING employee_holiday TABLE
	//
	function bookHoliday($id, $selecteddate,$selectedTaxYear, $currentdate, $shiftTeam, $isaccepted,$conn){	
		//create a template
		if($isaccepted==1){//was the booking accepted?
			//booking accepted.
			//create a template
			$sql = "INSERT INTO employee_holiday 
				(employee_id,holiday_date,tax_year_start,booked_on,shift,accepted) 
				VALUES (?,?,?,?,?,?)";
			//create a prepared statement	
			$stmt = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt,$sql)){						
				header("Location: ../book.php?error=sqlerror");
				exit();	
			}else{
				// run prepared statement to create new booking within employee_holiday table in the database.
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt,"issssi", $id, $selecteddate,$selectedTaxYear, $currentdate,$shiftTeam, $isaccepted);
				//Run parameters inside database
				mysqli_stmt_execute($stmt);				
			}
		}else{
			//booking denied.
			//create a templat
			$sql = "INSERT INTO employee_holiday 
				(employee_id,holiday_date,tax_year_start,booked_on,shift) 
				VALUES (?,?,?,?,?)";
			//create a prepared statement	
			$stmt = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt,$sql)){						
				header("Location: ../book.php?error=sqlerror");
				exit();	
			}else{
				// run prepared statement to create new booking within employee_holiday table in the database.
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt,"issss", $id, $selecteddate,$selectedTaxYear, $currentdate,$shiftTeam);
				//Run parameters inside database
				mysqli_stmt_execute($stmt);				
			}
		}
	}			
	
	
	//
	// AMEND BOOKING WITHING employee_holiday TABLE 
	// change from rejected to accepted
	//
	function amendHoliday($id, $selecteddate, $currentdate, $isaccepted,$conn){	
		//create a template
		$sql = "UPDATE employee_holiday SET booked_on=?,accepted=? WHERE employee_id=? AND holiday_date=?";
		//create a prepared statement
		$stmt = mysqli_stmt_init($conn);
		//Prepare the prepared statement
		if(!mysqli_stmt_prepare($stmt,$sql)){						
			header("Location: ../book.php?error=sqlerror");
			exit();	
		}else{
			// run prepared statement to amend existing booking within employee_holiday table in the database.
			//bind parameters to the placeholder
			mysqli_stmt_bind_param($stmt,"siis", $currentdate, $isaccepted,$id, $selecteddate);
			//Run parameters inside database
			mysqli_stmt_execute($stmt);				
			//booking accepted changed from denied to accepted.
			//date of accepted changed to current date.
		}	
	}
	
	//function checkHolidays(
	
	//
	// This function checks which tax year the user wishes to book time off within.
	//
	function getTaxYear($selecteddate) : String {			
		$month = date("m",strtotime($selecteddate)); //gets month value from date picked
		$year = date("Y",strtotime($selecteddate)); //gets year value from date picked.
		// tax year begins in April, if month selected is before April then the 
		// tax year is the previous year to the date selected.
		if ( $month < 4 ) {
			$year = $year - 1;
		}
		return $year; //return selected tax year.
	}				
	
	// MAIN //////////////////////////////////////////////////////////////////////////
	
	if(empty($id)||empty($selecteddate)){ //are input fields empty and is user logged in?
		header("Location: ../book.php?error=emptyfields");
		exit();
	}else{			
		if($selecteddate <= $date){//is date more than one day ahead?
			//date selected is too soon.
			header("Location: ../book.php?error=toosoon");
			exit();
		}else{
			//CHECK THAT USER HAS NOT ALREADY BOOKED A HOLIDAY ON THIS DATE///
		
			//create a template
			$sql = "SELECT * FROM employee_holiday WHERE employee_id=? AND holiday_date=?";
			//create a prepared statement
			$stmt = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt,$sql)){
				header("Location: ../book.php?error=sqlerror");
				exit();
			}else{
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt,"is",$id,$selecteddate);
				//Run parameters inside database
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if(mysqli_num_rows($result) != 0){ //does a user request on this date already exist?
					if($row = mysqli_fetch_assoc($result)){
						$alreadyaccepted = $row['accepted'];//has previous request been accepted?							
						if($alreadyaccepted == 1){
							//user has already booked this date off
							header("Location: ../book.php?error=prebooked");
							exit();
						}elseif($alreadyaccepted==0){
							//holiday request exists but was rejected, 
							//if accepted this time it needs to be amended instead of creating a new entry.
							$existrejected = 1;
						}								
					}
				}		
									
				//CHECK THAT USER HAS ENOUGH HOLIDAYS REMAINING ////
				//calls function to get the tax year of the selected date and assign it to the $selectedTaxYear variable.
				$selectedTaxYear = getTaxYear($selecteddate);
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
					$result = mysqli_stmt_get_result($stmt_select);
					if(mysqli_num_rows($result) == 0){
						//shift date doesn't yet exist in databse, add date to database with default values.
						$sql_insert = "INSERT INTO holiday_allowance (employee_id,tax_year_start) VALUES (?,?);";
						$stmt_insert = mysqli_stmt_init($conn);						
						if(!mysqli_stmt_prepare($stmt_insert,$sql_insert)){						
							header("Location: ../managepto.php?error=sqlerror");
							exit();	
						}else{
							// run prepared statement to add shift to database.
							mysqli_stmt_bind_param($stmt_insert,"is",$id, $selectedTaxYear);
							mysqli_stmt_execute($stmt_insert);	
							//re-run select paramentets inside database after new entry has been inserted.
							mysqli_stmt_execute($stmt_select);
							//assign new entry just inserted into database to $result varaible
							$result = mysqli_stmt_get_result($stmt_select);//assign new entry just inserted into database to $result varaible					
						}
					}
					if($row = mysqli_fetch_assoc($result)){
						if($row['booked']>=$row['allowance']){ //does user have holidays remaining?
							//user has no holidays remaining for selected tax year.							
							if($existrejected!=1){ //does request exist in databse?
								//when holiday request doesn't exist in database.
								//variable set to 1 when user has no holidays remaining.
								$nohols=1;
							}else{
								//rejected holiday request already exists for this user,
								//nothing in the databse neeeds to be edited.
								//return request denied message.
								header("Location: ../book.php?error=userhols");
								exit();
							}
						}							
					}
					
					$newBooked = ($row['booked'])+1; //new amount of holidays booked if all checks verify						
					///check if shift date exists in database yet///
					//create a template
					$sql_select_shift = "SELECT * FROM shift WHERE the_date=?";
					//create a prepared statement
					$stmt_select_shift = mysqli_stmt_init($conn);
					//Prepare the prepared statement
					if(!mysqli_stmt_prepare($stmt_select_shift,$sql_select_shift)){
						header("Location: ../book.php?error=sqlerror");
						exit();
					}else{
						//bind parameters to the placeholder
						mysqli_stmt_bind_param($stmt_select_shift,"s",$selecteddate);
						//Run parameters inside database
						mysqli_stmt_execute($stmt_select_shift);
						$result_shift = mysqli_stmt_get_result($stmt_select_shift);
						while(mysqli_num_rows($result_shift) == 0){//run until shift  exists
							//shift date doesn't yet exist in databse, add date to database with default values.
							$sql_insert_shift = "INSERT INTO shift (the_date) VALUES (?);";
							$stmt_insert_shift = mysqli_stmt_init($conn);
							if(!mysqli_stmt_prepare($stmt_insert_shift,$sql_insert_shift)){						
								header("Location: ../book.php?error=sqlerror");
								exit();	
							}else{
								// run prepared statement to add shift to database.
								mysqli_stmt_bind_param($stmt_insert_shift,"s", $selecteddate);
								mysqli_stmt_execute($stmt_insert_shift);	
								mysqli_stmt_execute($stmt_select_shift);
								$result_shift = mysqli_stmt_get_result($stmt_select_shift);							
							}
						}
						if($row = mysqli_fetch_assoc($result_shift)){
							//assign shift availability to varaibles
							$_SESSION['the_date'] = $row['the_date'];
							$_SESSION['red_avail'] = $row['red'];
							$_SESSION['blue_avail'] = $row['blue'];
							$_SESSION['night_avail'] = $row['night'];
							$_SESSION['admin_avail'] = $row['admin'];	
						}else{
							header("Location: ../book.php?error=sqlerror");
							exit();
						}
						if((holsAvailable($shiftTeam) > 0)&&($nohols!=1)){//does shift and user have holidays available?		
							// holidays are available for selected shift on given date.
							$isaccepted = 1; //holiday booking accepted.										
							//Amend user holiday_allowance data in database
							//create a template
							$sql = "UPDATE holiday_allowance 
							SET booked=? WHERE employee_id=? AND tax_year_start=?;";
							//create a prepared statement
							$stmt = mysqli_stmt_init($conn);
							//Prepare the prepared statement
							if(!mysqli_stmt_prepare($stmt,$sql)){
								header("Location: ../book.php?error=sqlerror");
								exit();	
							}else{
								//bind parameters to the placeholder
								mysqli_stmt_bind_param($stmt,"iis",$newBooked,$id,$selectedTaxYear);
								//Run parameters inside database
								mysqli_stmt_execute($stmt);										
								//Amend shift holiday availability in shift table./////
								//create a template										
								$sql = "UPDATE shift SET red=?, blue=?,night=?,admin=? 
										WHERE  the_date=?;";
								//create a prepared statement
								$stmt = mysqli_stmt_init($conn);
								if(!mysqli_stmt_prepare($stmt,$sql)){
									header("Location: ../book.php?error=sqlerror");
									exit();	
								}else{
									//bind parameters to the placeholder
									mysqli_stmt_bind_param($stmt,"iiiis",$_SESSION['red_avail'],
									$_SESSION['blue_avail'],$_SESSION['night_avail'],
									$_SESSION['admin_avail'],$selecteddate);
									//Run parameters inside database
									mysqli_stmt_execute($stmt);
									if($existrejected!=1){//does booking need to be created or amended?						
										bookHoliday($id, $selecteddate,$selectedTaxYear, $currentdate, $shiftTeam, $isaccepted,$conn);	
										header("Location: ../book.php?success=create");
										exit(); //new booking successfully created within database.
									}elseif($existrejected==1){
										amendHoliday($id, $selecteddate, $currentdate, $isaccepted,$conn);
										header("Location: ../book.php?success=amend");
										exit();//new booking successfully amended within database.
									}
									
								}
							}											
						}else{
							//no holidays available for selected shift on given date.
							if($existrejected!=1){//does denied booking already exist for thia date?
								//booking does not exist and needs to be created.
								bookHoliday($id, $selecteddate,$selectedTaxYear, $currentdate, $shiftTeam, $isaccepted,$conn);
								if($nohols==1){
									header("Location: ../book.php?error=userhols");
									exit();
								}else{
									header("Location: ../book.php?error=shifthols");
									exit(); //new booking successfully created within database, saved as denied.
								}
							}else{
								//booking already exists.
								header("Location: ../book.php?error=shifthols");
								exit();
							}
						}									
					}							
				}			
			}
		}	
	}
}else{//no improper access
	header("Location: ../home.php?error=nodirectaccess");
	exit();
}