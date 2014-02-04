<?php

$myAppointmentClass	=	new drh_appointment();
$myacount			=	$_POST['whatshouldido'];

// what function to use
if	($myacount	==	'saveiguess') 	{ 	$myAppointmentClass->save_appointment(); }
if	($myacount	==	'lemmesee')		{  	$myAppointmentClass->view_appointment(); }

class drh_appointment {

	function view_appointment() {
		$response			=	array(	'success' => false, 
										'first_name' => '',
										'phone' => '',
										'email' => '',
										'start_location' => '',
										'end_location' => '',
										'departure_date' => '',
										'departure_time' => '',
										'status' => ''	);	

		$the_conf_num			=	$_POST['the_conf_num'];
		$the_phone_status		=	$_POST['the_phone_status'];
		while(!is_file('wp-config.php')){
		  if(is_dir('../')) chdir('../');
		  else die('Could not find WordPress.');

		}
		include( 'wp-config.php' );
		global $wpdb; // this is how you get access to the database
		$table					=	"drh_appointments";					
		
		$sql					=	"SELECT first_name,phone,email,start_location,end_location,departure_time,departure_date,status from $table where id=$the_conf_num and phone='$the_phone_status'";
		$sql					=	strip_tags ( htmlentities ( trim ( $sql ) , ENT_NOQUOTES ) );

		$row 					=	$wpdb->get_row($sql);
 
		if ($wpdb->num_rows == 1)
		{
			$response['success']	=	true;
			$response['first_name'] = $row->first_name."<br>";;
			$response['phone'] = $row->phone."<br>";;
			$response['email'] = $row->email."<br>";;
			$response['start_location'] = $row->start_location."<br>";;
			$response['end_location'] = $row->end_location."<br>";;
			$response['departure_date'] = $row->departure_date."<br>";;
			$response['departure_time'] = $row->departure_time."<br>";;
			$response['status'] = $row->status."<br>";
		} 
		else
		{
			$response['success']	=	false;
		}	
		echo json_encode($response);
	
		
		

		
		die();
	}
	
	function save_appointment() {
		// get the posted data
		$response			=	array('success' => false, 'last_id' => 1);
		$your_first_name	=	$_POST['your_first_name'];
		$startLocation		=	$_POST['startLocation'];
		$endLocation		=	$_POST['endLocation'];
		$phoneNumber		=	$_POST['phoneNumber'];
		$email				=	$_POST['youremail'];
		$departureTime		=	$_POST['departureTime'];
		$departureDate		=	$_POST['departureDate'];
		
		switch( true ){
			case ( 	!empty($your_first_name) 
				&& 	!empty($startLocation) 
				&&	!empty($endLocation)
				&&	!empty($phoneNumber)
				&&	!empty($email)
				&&	!empty($departureTime) 
				&&	!empty($departureDate) ):
			{
				$response['success'] = true;
				break;
			}		
			default:{
				break;
			}
		}
		while(!is_file('wp-config.php')){
		  if(is_dir('../')) chdir('../');
		  else die('Could not find WordPress.');

		}

		include( 'wp-config.php' );
		global $wpdb; // this is how you get access to the database
			
		$table		=	"drh_appointments";
	
		$sql		=	"INSERT INTO $table ".
		 "(first_name,phone,email,start_location,end_location,departure_time,departure_date,status) ".
		 "VALUES ".
		 "('$your_first_name','$phoneNumber','$email','$startLocation','$endLocation','$departureTime',
		 '$departureDate','booked')";
		$sql		=	strip_tags ( htmlentities ( trim ( $sql ) , ENT_NOQUOTES ) );
		$result		=	$wpdb->query($sql);
		$response['success'] = true;
		$response['last_id'] = $wpdb->insert_id;
		echo json_encode($response);
		$rows = $wpdb->get_results( "SELECT first_name,phone,email,start_location,end_location,departure_time,departure_date,status FROM $table where id=$lastid");
		// foreach ($rows as $row) {
			// echo $row->first_name."<br>";
			// echo $row->phone."<br>";
			// echo $row->email."<br>";
			// echo $row->start_location."<br>";
			// echo $row->end_location."<br>";
			// echo $row->departure_time."<br>";
			// echo $row->status."<br>";
			// echo "<br>";
		// }		
		die();
	} // end function save_appointment	
} // end class drh_appointment

?>