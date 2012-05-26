<?php
	$link = mysql_connect('localhost', 'root', 'warrior');
	if (!$link) {
	    die('Ошибка соединения: ' . mysql_error());
	}
	
	mysql_select_db( 'mysql', $link );
?>