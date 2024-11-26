<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".$password."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function login2(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$email."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$password' ";
		$data .= ", type = '$type' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}
	function signup(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", contact = '$contact' ";
		$data .= ", address = '$address' ";
		$data .= ", username = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$data .= ", type = 3";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO users set ".$data);
		if($save){
			$qry = $this->db->query("SELECT * FROM users where username = '".$email."' and password = '".md5($password)."' ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'passwors' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
			}
			return 1;
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/img/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['setting_'.$key] = $value;
		}

			return 1;
				}
	}

	
	function save_category(){
		extract($_POST);
		$data = " name = '$name' ";
		if(!empty($_FILES['img']['tmp_name'])){
			$fname = strtotime(date("Y-m-d H:i"))."_".$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/'.$fname);
			if($move){
				$data .=", img_path = '$fname' ";
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO specialty set ".$data);
		}else{
			$save = $this->db->query("UPDATE specialty set ".$data." where id=".$id);
		}
		if($save)
			return 1;
	}
	function delete_category(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM specialty where id = ".$id);
		if($delete)
			return 1;
	}
	function save_therapists(){
		extract($_POST);
		$data = " name = '$name' ";
		$data .= ", name_pref = '$name_pref' ";
		$data .= ", clinic_address = '$clinic_address' ";
		$data .= ", contact = '$contact' ";
		$data .= ", email = '$email' ";
		if(!empty($_FILES['img']['tmp_name'])){
			$fname = strtotime(date("Y-m-d H:i"))."_".$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'], '../assets/img/'.$fname);
			if($move){
				$data .=", img_path = '$fname' ";
			}
		}
		$data .=" , specialty_ids = '[".implode(",",$specialty_ids)."]' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO therapists_list set ".$data);
			$did= $this->db->insert_id;
		}else{
			$save = $this->db->query("UPDATE therapists_list set ".$data." where id=".$id);
		}
		if($save){
			$data = " username = '$email' ";
			if(!empty($password))
			$data .= ", password = '".$password."' ";
			$data .= ", name = '".$name.', '.$name_pref."' ";
			$data .= ", contact = '$contact' ";
			$data .= ", address = '$clinic_address' ";
			$data .= ", type = 2";
			if(empty($id)){
				$chk = $this->db->query("SELECT * FROM users where username = '$email ")->num_rows;
				if($chk > 0){
					return 2;
					exit;
				}
					$data .= ", therapists_id = '$did'";

					$save = $this->db->query("INSERT INTO users set ".$data);
			}else{
				$chk = $this->db->query("SELECT * FROM users where username = '$email' and therapists_id != ".$id)->num_rows;
				if($chk > 0){
					return 2;
					exit;
				}
					$data .= ", therapists_id = '$id'";
				$chk2 = $this->db->query("SELECT * FROM users where therapists_id = ".$id)->num_rows;
					if($chk2 > 0)
						$save = $this->db->query("UPDATE users set ".$data." where therapists_id = ".$id);
					else
						$save = $this->db->query("INSERT INTO users set ".$data);
					

			}
			return 1;
		}
	}
	function delete_therapists(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM therapists_list where id = ".$id);
		if($delete)
			return 1;
	}

	function save_schedule(){
		extract($_POST);
		foreach($days as $k => $val){
			$data = " therapists_id = '$therapists_id' ";
			$data .= ", day = '$days[$k]' ";
			$data .= ", time_from = '$time_from[$k]' ";
			$data .= ", time_to = '$time_to[$k]' ";
			if(isset($check[$k])){
				if($check[$k]>0)
				$save[] = $this->db->query("UPDATE therapists_schedule set ".$data." where id =".$check[$k]);
			else
				$save[] = $this->db->query("INSERT INTO therapists_schedule set ".$data);
			}
		}

			if(isset($save)){
				return 1;
			}
	}

	function set_appointment() {
		extract($_POST);
	
		// Validate therapist's schedule
		$day = date('l', strtotime($date));
		$time = date('H:i', strtotime($time)) . ":00";
		$schedule = date('Y-m-d', strtotime($date)) . ' ' . $time;
	
		$doc_sched_check = $this->db->query("SELECT * FROM therapists_schedule 
			WHERE therapists_id = $therapists_id AND day = '$day' AND ('$time' BETWEEN time_from AND time_to)");
	
		if ($doc_sched_check->num_rows <= 0) {
			return json_encode(array('status' => 2, 'msg' => "The selected time is not within the therapist's schedule."));
		}
	
		// Check for double booking, excluding the current appointment if updating
		$id_condition = isset($id) ? "AND id != $id" : "";
		$double_booking_check = $this->db->query("SELECT * FROM appointment_list 
			WHERE therapists_id = $therapists_id AND schedule = '$schedule' $id_condition");
	
		if ($double_booking_check->num_rows > 0) {
			return json_encode(array('status' => 3, 'msg' => "The selected time slot is already booked."));
		}
	
		// Save appointment details
		$data = " therapists_id = '$therapists_id' ";
		if (!isset($customer_id)) {
			$data .= ", customer_id = '" . $_SESSION['login_id'] . "' ";
		} else {
			$data .= ", customer_id = '$customer_id' ";
		}
	
		$data .= ", schedule = '$schedule' ";
		$data .= ", address = '$address' ";
		$data .= ", contact_number = '$contact_number' ";
	
		if (isset($status)) {
			$data .= ", status = '$status' ";
		}
	
		if (isset($id) && !empty($id)) {
			$save = $this->db->query("UPDATE appointment_list SET " . $data . " WHERE id = " . $id);
		} else {
			$save = $this->db->query("INSERT INTO appointment_list SET " . $data);
		}
	
		if ($save) {
			return json_encode(array('status' => 1));
		} else {
			return json_encode(array('status' => 0, 'msg' => "Error saving appointment."));
		}
	}
	
	
	function delete_appointment(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM appointment_list where id = ".$id);
		if($delete)
			return 1;
	}
	
	

}