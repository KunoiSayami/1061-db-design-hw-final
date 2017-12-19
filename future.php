<!DOCTYPE html>
<html>
	<?php
	class InvalidData extends Exception { }
	$regex = '/^[a-zA-Z0-9]{1,10}$/';
	$regex_email = '/^[a-zA-Z0-9@\.]{1,30}$/';
	//echo $_SERVER['REQUEST_METHOD'];

	$USER_STRING = "";
	$DATABASE_NAME = '';
	$TABLE_NAME = '';
	$EDIT_BUTTON = 'Post';
	$POST_NEW_BUTTON = 'Insert';

	$db = mysqli_connect('localhost','php','root',$DATABASE_NAME);
	mysqli_query($db,'SET NAMES utf8;');
	$method = $POST_NEW_BUTTON;
	if (!isset($_GET['search']))
		if (isset($_GET['id'])){
			if (!mysqli_fetch_array(mysqli_query($db,'SELECT * FROM '.$TABLE_NAME.' WHERE `id` = '.$_GET['id'].' ;')))
			//echo $r;
				header('Location:future.php');
				//die();
			$r = mysqli_fetch_array(mysqli_query($db,'SELECT * FROM '.$TABLE_NAME.' WHERE `id` = '.$_GET['id'].' ;'));
			$method = $EDIT_BUTTON;
		} 
		else
		if (isset($_POST['submit'])){
			if ($_POST['submit'] == 'Clear Database'){
				mysqli_query($db,'DELETE FROM '.$TABLE_NAME.';');
				mysqli_query($db,'ALTER TABLE '.$TABLE_NAME.' AUTO_INCREMENT=1;');
				header('Location:future.php');
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
					$msg = str_replace('`','\`',str_replace('\\','\\\\',str_replace('\'','\'\'',$_POST['msg'])));
					//echo 'INSERT INTO '.$TABLE_NAME.' (`name`,`email`,`message`) VALUES ('.$name.','.$email.','.$msg.');';
					if ($_POST['submit'] == $POST_NEW_BUTTON)
						mysqli_query($db,'INSERT INTO '.$TABLE_NAME.' (`name`,`email`,`message`,`time`) VALUES (\''.$name.'\',\''.$email.'\',\''.$msg.'\',CURRENT_TIMESTAMP);');
					else if ($_POST['submit'] == $EDIT_BUTTON) 
						mysqli_query($db,'UPDATE '.$TABLE_NAME.' SET `name` = \''.$name.'\', `email` = \''.$email.'\',`message` = \''.$msg.'\',`time` = CURRENT_TIMESTAMP WHERE `id` = '.$_POST['id'].' ;');
					mysqli_commit($db);
					header('Location:future.php');
				} catch (InvalidData $e){
					echo "Caught exception: ", $e->getMessage(),"\n</html>";
					mysqli_close($db);
					http_response_code(400);
					//exit(0);
					die();
				}
			}
		else
			if (isset($_POST['delete'])){
				mysqli_query($db,'DELETE FROM '.$TABLE_NAME.' WHERE `id` ='.$_POST['delete'].';');
				header('Location:future.php');
			}
?>
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
			window.location.assign(window.location.pathname+'?id='+id);
		}
	</script>
	<script>
		function getsearch(str){
			window.location.assign(window.location.pathname+'?search='+str);
		}
	</script>
	<script>
		function reload(){
			window.location.assign(window.location.pathname);
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
<strong><?php echo $USER_STRING; ?></strong><hr>
<?php
	//$s = mysqli_fetch_array($r);
	echo "<form action=\"future.php\" method=\"post\">";
	echo "姓名:  <input name=\"name\" type=\"text\" value=\"$r[1]\"/><br>";
	echo "邮箱:  <input name=\"email\" type=\"text\" value=\"$r[2]\"/><br>";
	echo "讯息:  <input name=\"msg\" type=\"text\" value=\"$r[3]\"/><br>";
	if ($method == $EDIT_BUTTON){
		$id = $_GET['id'];
		echo "<input type=\"hidden\" name=\"id\" value=\"$id\"/>";
	}
	echo "<input type=\"submit\" name=\"submit\" value=\"$method\"/>      ";
?>
	<input type="submit" name="submit" value="Clear Database"><br>
<?php
	if (!isset($_GET['search']))
		$r = mysqli_query($db,'SELECT * FROM '.$TABLE_NAME.' ORDER BY `time` ASC;');
	else
		$r = mysqli_query($db,'SELECT * FROM '.$TABLE_NAME.' WHERE `name` LIKE \'%'.$_GET['search'].'%\' OR `email` LIKE \'%'.$_GET['search'].'%\' OR `message` LIKE \'%'.$_GET['search'].'%\' ORDER BY `time` ASC;');
	mysqli_close($db);
?>
</form>
<hr>
	<?php 
		echo "<form action=\"future.php\" method=\"get\">";
		echo "<table border=\"1\" bordercolor=\"#0000FF\" width=40%>\n";
		echo "<tr><td>姓名</td><td>信箱</td><td>訊息</td><td>時間</td><td>編輯</td></tr>\n";
		while ($a = mysqli_fetch_array($r)){
			echo "<tr>";
			echo "<td>$a[1]</td>";
			echo "<td>$a[2]</td>";
			echo "<td>$a[3]</td>";
			echo "<td>$a[4]</td>";
			echo "<td>";
			echo "<input type=\"button\" value=\"Edit\" onclick=\"gets($a[0])\"> ";
			echo "<input type=\"button\" value=\"Delete\" onclick=\"sendsth($a[0])\"> ";
			echo "</td></tr>\n";
		}
		echo "</table>\n<br>";
	?>
	<form action="future.php" method="get">
		搜寻: <input type="text" name="search">
		<input type="button" value="search" onclick=getsearch(search.value)>
	<input type="button" value="Show All" onclick=reload()>
	</form>
</body>
</html>
