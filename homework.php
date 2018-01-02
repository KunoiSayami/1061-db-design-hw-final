<?php
	// Variable settings
	$USER_STRING = ""; // Show on user header
	// Database Setting
	$DATABASE_NAME = ''; 
	$TABLE_NAME = '';
	$DATABASE_LOCATION = 'localhost';
	$DATABASE_USER_NAME = '';
	$DATABASE_USER_PASSWORD = '';
	// Button text setting
	// Do not modify unless necessary
	$EDIT_BUTTON = 'Post';
	$POST_NEW_BUTTON = 'Insert';
	// Self File name setting
	// Do not modify unless necessary
	$FILE_NAME = basename($_SERVER['PHP_SELF']);

	class InvalidData extends Exception { }
	$regex = '/^[a-zA-Z0-9]{1,10}$/';
	$regex_email = '/^[a-zA-Z0-9@\.]{1,30}$/';
	//echo $_SERVER['REQUEST_METHOD'];

	function replace_msg($msg){
		//return $msg;
		return str_replace('"','&quot;',str_replace('\'','&apos;',str_replace(' ','&nbsp;',
			str_replace('>','&gt;',str_replace('<','&lt;',str_replace('&amp;','&',$msg))))));
	}

	function sql_replace_msg($msg){
		return str_replace('`','\`',str_replace('\\','\\\\',str_replace('\'','\'\'',$msg)));
	}
	
	function test_func($num,$errmsg){
		if (isset($num))
			if ( ! preg_match('/^\d+$/',$num) )
				throw new InvalidData($errmsg);
	}

	// Data check
	try {
		test_func($_GET['id'],"Invalid `id` in get method");
		test_func($_POST['delete'],"Invalid `id` in post method");
		if ($_POST['submit'] == $EDIT_BUTTON)
			test_func($_POST['id'],"Invalid `id` in post method");
	} catch (InvalidData $e){
		//print_r(get_defined_vars());
		echo "<h1>Caught exception: ", $e->getMessage(),"</hl>";
		http_response_code(400);
		die();
	}

	$db = mysqli_connect($DATABASE_LOCATION,$DATABASE_USER_NAME,$DATABASE_USER_PASSWORD,$DATABASE_NAME);
	mysqli_query($db,'SET NAMES utf8;');
	$method = $POST_NEW_BUTTON;
	if (!isset($_GET['search']))
		if (isset($_GET['id'])){
			if (!mysqli_fetch_array(mysqli_query($db,'SELECT * FROM '.$TABLE_NAME.' WHERE `id` = '.$_GET['id'].' ;')))
			//echo $r;
				header('Location:'.$FILE_NAME.'#data');
				//die();
			$r = mysqli_fetch_array(mysqli_query($db,'SELECT * FROM '.$TABLE_NAME.' WHERE `id` = '.$_GET['id'].' ;'));
			$method = $EDIT_BUTTON;
		} 
		else
		if (isset($_POST['submit'])){
			if (false && $_POST['submit'] == 'Clear Database'){
			/*	mysqli_query($db,'DELETE FROM '.$TABLE_NAME.';');
				mysqli_query($db,'ALTER TABLE '.$TABLE_NAME.' AUTO_INCREMENT=1;');
				header('Location:'.$FILE_NAME);*/
			} 
			else
			// Process post
			if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['msg']))
				try{
					if (!preg_match($regex,$_POST['name']))
						throw new InvalidData('Invalid field `name`');
					if (!preg_match($regex_email,$_POST['email']))
						throw new InvalidData('Invalid field `email`');
					$name = $_POST['name'];
					$email = $_POST['email'];
					$msg = sql_replace_msg($_POST['msg']);
					if ($_POST['submit'] == $POST_NEW_BUTTON)
						mysqli_query($db,'INSERT INTO '.$TABLE_NAME.' (`name`,`email`,`message`,`time`) VALUES (\''.$name.'\',\''.$email.'\',\''.$msg.'\',CURRENT_TIMESTAMP);');
					else if ($_POST['submit'] == $EDIT_BUTTON) 
						mysqli_query($db,'UPDATE '.$TABLE_NAME.' SET `name` = \''.$name.'\', `email` = \''.$email.'\',`message` = \''.$msg.'\',`time` = CURRENT_TIMESTAMP WHERE `id` = '.$_POST['id'].' ;');
					mysqli_commit($db);
					header('Location:'.$FILE_NAME.'#data');
				} catch (InvalidData $e){
					echo "<h1>Caught exception: ", $e->getMessage(),"</hl>";
					mysqli_close($db);
					http_response_code(400);
					//exit(0);
					die();
				}
			}
		else
			if (isset($_POST['delete'])){
				mysqli_query($db,'DELETE FROM '.$TABLE_NAME.' WHERE `id` ='.$_POST['delete'].';');
				header('Location:'.$FILE_NAME.'#data');
			}
?>
<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script>
		function sendsth(paramater){
			var form = $('<form></form>');
			form.attr("method","post");
			form.attr("action",window.location.pathname);
			var field = $('<input></input>');
			field.attr("type","hidden");
			field.attr("name",'delete');
			field.attr("value",paramater);
			form.append(field);
			$(document.body).append(form);
			form.submit();
			return;
		}
	</script>
	<script>
		function gets(id){
			window.location.assign(window.location.pathname+'?id='+id+'#postform');
		}
	</script>
	<script>
		function getsearch(str){
			window.location.assign(window.location.pathname+'?search='+str+'#data');
		}
	</script>
	<script>
		function reload(){
			window.location.assign(window.location.pathname+'#data');
		}
	</script>
	<meta charset="utf-8" />
	<title>Final Homework</title>
	<!--META HTTP-EQUIV="Pragma" CONTENT="no-cache"-->
	<style>
		td {
			text-align:center; 
			color:red;
			text-align: center;
		}
		strong {
			font-weight: bold;
			color:blue;
		}
	</style>
</head>
<body>
<center><strong><?php echo $USER_STRING; ?></strong><hr>
<?php
	//$s = mysqli_fetch_array($r);
	echo "<form id=\"postform\" action=\"",$FILE_NAME,"\" method=\"post\">";
	echo "姓名:  <input name=\"name\" type=\"text\" value=\"$r[1]\"><br>";
	echo "信箱:  <input name=\"email\" type=\"text\" value=\"$r[2]\"><br>";
	echo "訊息:  <input name=\"msg\" type=\"text\" value=\"",replace_msg($r[3]),"\"><br>";
	if ($method == $EDIT_BUTTON){
		$id = $_GET['id'];
		echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";
	}
	echo "<input type=\"submit\" name=\"submit\" value=\"$method\">      ";
?>
	<!--input type="submit" name="submit" value="Clear Database"--><br>
<?php
	if (!isset($_GET['search']))
		$r = mysqli_query($db,'SELECT * FROM '.$TABLE_NAME.' ORDER BY `time` ASC;');
	else {
		$search_str = sql_replace_msg($_GET['search']);
		$r = mysqli_query($db,'SELECT * FROM '.$TABLE_NAME.' WHERE `name` LIKE \'%'.$search_str.'%\' OR `email` LIKE \'%'.$search_str.'%\' OR `message` LIKE \'%'.$search_str.'%\' ORDER BY `time` ASC;');
	}
	mysqli_close($db);
?>
</form>
<hr>
	<form action=<?php echo "\"",$FILE_NAME,"\""?> method="get">
		搜尋: <input type="text" name="search">
		<input type="button" value="search" onclick=getsearch(search.value)>
	<input type="button" value="Show All" onclick=reload()>
	</form><br>
	<?php 
		echo "<form action=\"",$FILE_NAME,"\" method=\"get\">";
		echo "<table id=\"data\" border=\"1\" bordercolor=\"#0000FF\">\n";
		echo "<tr><td>姓名</td><td>信箱</td><td>訊息</td><td>時間</td><td>編輯</td></tr>\n";
		while ($a = mysqli_fetch_array($r)){
			echo "<tr>";
			echo "<td>$a[1]</td>";
			echo "<td>$a[2]</td>";
			echo "<td>".replace_msg($a[3])."</td>";
			echo "<td>$a[4]</td>";
			echo "<td>";
			echo "<input type=\"button\" value=\"Edit\" onclick=\"gets($a[0])\"> ";
			echo "<input type=\"button\" value=\"Delete\" onclick=\"sendsth($a[0])\"> ";
			echo "</td></tr>\n";
		}
		echo "</table></form>\n<br>";
	?>
	</center>
</body>
</html>
