<?php
session_start();
if(isset($_POST['delete'])){ //script only accesible through correct post value.	
	//connect to database
	include 'dbh-inc.php'; 	
	//variables.		
	$adminid = $_SESSION['emp_id']; //logged in admin's id.
	$pwd = mysqli_real_escape_string($conn,$_POST['pwd']);//logged in admin's password.
	$id = $_POST['id']; //id of user to be delted
	$last = $_POST['last']; //surname of user to be deleted	
	//FUNCTIONS
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
			header("Location: ../admindelete.php?error=sqlerror");
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
					header("Location: ../admindelete.php?error=sqlerror");
					exit();
				}
			}	
		}
	}
	//
	// check what shift employee to be deleted belongs to and increase the amount of
	// holidays available for that shift on the selected date.
	//
	function increaseAvailable($employeeshift){	
		$shiftTeam = $employeeshift;
		//assigns the amount of holidays available left for selected shift to a varaible.
		//and increase the number available by one of the users shift.
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
	//MAIN///////////////	
	//CHECK Admin's passsword is correct.
	//Error handlers
	//Check if empty
	if(empty($id)||empty($pwd)||empty($adminid)||empty($pwd)){
		//one or more fields are empty
		header("Location: ../admindelete.php?error=emptyfields");
		exit();
	}else{
		if($adminid==$id){
			//cannot delete self.
			header("Location: ../admindelete.php?error=self");
			exit();
		}else{
			//CHECK IF USER EXISTS
			//create a template
			$sql = "SELECT * FROM employee WHERE id=?";
			//create a prepared statement
			$stmt = mysqli_stmt_init($conn);
			//Prepare the prepared statement
			if(!mysqli_stmt_prepare($stmt,$sql)){
				header("Location: ../admindelete.php?error=sqlerror");
				exit();
			}else{
				//bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt,"i",$adminid);
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
							header("Location: ../admindelete.php?error=incorrectpwd");
							exit();
						}elseif($hashpwdcheck == true){
							//password verified.
							
							//STAGE TWO - VERIFY EMPLOYEE EXISTS
							
							//check if id exists & matches name
							//create a template
							$sql = "SELECT * FROM employee WHERE id=? AND last=?";
							//create a prepared statement
							$stmt = mysqli_stmt_init($conn);
							//Prepare the prepared statement
							if(!mysqli_stmt_prepare($stmt,$sql)){
								header("Location: ../admindelete.php?error=sqlerror");
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
									header("Location: ../admindelete.php?error=invaliduser");
									exit();
								}else{
									//employee exists
									//get shift of employee to be deleted.
									$row = mysqli_fetch_assoc($result);
									$employeeshift = $row['shift'];
									//CHECK IF EMPLOYEE HAS HOLIDAYS BOOKED.
									
									//check if holiday date exists inside employee_holiday table.
									//create a template
									$sql = "SELECT * FROM employee_holiday 
										WHERE employee_id=? AND accepted=?";
									//create a prepared statement
									$stmt = mysqli_stmt_init($conn);
									//Prepare the prepared statement
									if(!mysqli_stmt_prepare($stmt,$sql)){
										header("Location: ../admindelete.php?error=sqlerror");
										exit();
									}else{
										$accepted=1; //check for holidays that have been accepted.
										//bind parameters to the placeholder
										mysqli_stmt_bind_param($stmt,"is",$id,$accepted);
										//Run parameters inside database
										mysqli_stmt_execute($stmt);
										$result = mysqli_stmt_get_result($stmt);
										//does user have holidats booked?
										if(mysqli_num_rows($result) > 0){ 
											//user has holidays booked.
											//for each holiday booked that is accepted....
											while($row=mysqli_fetch_assoc($result)){
												//DELTE ALL BOOKED EMPLOYEE HOLIDAYS
												//set varaible as currently selected booked holiday.
												$selecteddate=$row['holiday_date'];	
												$sql_delete = "DELETE FROM employee_holiday WHERE employee_id=? AND holiday_date=?";
												$stmt_delete = mysqli_stmt_init($conn);
												if(!mysqli_stmt_prepare($stmt_delete,$sql_delete)){						
														header("Location: ../admindelete.php?error=sqlerror");
														exit();	
												}else{
													// run prepared statement to add shift to database.
													mysqli_stmt_bind_param($stmt_delete,"is",$id, $selecteddate);
													mysqli_stmt_execute($stmt_delete);
													//INCREASE SHIFT HOLIDAY AVAILABILITY BY ONE																								
													//retrieve availability of shifts on selected date.
													getShiftAllowance($selecteddate,$conn);
													//increase value of holidays available for selected shift and date by one.
													increaseAvailable($employeeshift);
													//update the shift table with new increased holiday availability.							
													$sql = "UPDATE shift SET red=?, blue=?,night=?,admin=? WHERE  the_date=?;";
													$stmt = mysqli_stmt_init($conn);
													if(!mysqli_stmt_prepare($stmt,$sql)){
														header("Location: ../admindelete.php?error=sqlerror");
														exit();	
													}else{
														mysqli_stmt_bind_param($stmt,"iiiis",$_SESSION['red_avail'],
															$_SESSION['blue_avail'],$_SESSION['night_avail'],
															$_SESSION['admin_avail'],$selecteddate);
														mysqli_stmt_execute($stmt);
													}
												}
											}
										}//user now has no holidats booked in database.
										//Create template to delete employee from database.
										$sql_delete = "DELETE FROM employee WHERE id=? AND last=?";
										$stmt_delete = mysqli_stmt_init($conn);
										if(!mysqli_stmt_prepare($stmt_delete,$sql_delete)){						
												header("Location: ../admindelete.php?error=sqlerror");
												exit();	
										}else{
											// run prepared statement to add shift to database.
											mysqli_stmt_bind_param($stmt_delete,"is",$id, $last);
											mysqli_stmt_execute($stmt_delete);	
											header("Location: ../admindelete.php?success=delete");
											exit();	
										}
									}
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