<?php
	include 'conf.php';
	
	if (!empty($_POST['login'])) {
		if (empty($_POST['pwd'])) {
			echo '<span style="color:red;">Не уазкан пароль пользователя!</span>';
		} else {
			
			$login = mysql_real_escape_string($_POST["login"]);
			$pwd = mysql_real_escape_string($_POST["pwd"]);
			
			// TODO сделать филтрацию при залогинивании =) 
			$res = mysql_query("select * from mapuser where login='$login' and password='$pwd'", $link); 
			if (!$res) {
				die('Ошибка соединения: ' . mysql_error());
			}
			// если пользователь сушествует
			if (mysql_num_rows($res) > 0) {
				$_SESSION['user.login'] = $login;
				header('Location: /');
			} else {
				echo "Такого пользователя не существует <a href='login.php'>Назад</a>";	
			}
			
			mysql_close($link);
			return;
		}
	}
	
	
	if (!isset($_SESSION['user.login']) || empty($_SESSION['user.login'])) {
		echo "
			<form action='' method='POST' name='submit'>
				<table>
					<tr><td>Пользователя:</td><td><input name='login' type='text'></td></tr>
			      	<tr><td>Пароль:</td><td><input name='pwd' type='password'></td></tr>
			    </table>
			    <button type='submit'>Войти</button>
		    </form>";
		
		return;			
	}

?>