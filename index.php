<?php 
	session_start();
	
	if (isset($_SESSION['user.login'])) {
		include "edit_map.php";
	} else {
		include "login.php";
	}
?>