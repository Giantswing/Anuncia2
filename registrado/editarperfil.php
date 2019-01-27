<?php
if(!file_exists("../conf/config.php")){
		header("Location:../instalar/instalar.php");
}
else{
	include_once("../conf/config.php");
}

session_start();
if(!isset($_SESSION['autenticado'])){
	$mensajeBanner = "<div id = 'inicioText'>No has iniciado sesion <a href='../registrado/iniciosesion.php'>INICIAR</a></div>";
}
else {
	$mensajeBanner = "<div id = 'inicioText'>Bienvenido $_SESSION[login] ($_SESSION[rol]) 
	<a href='../registrado/cerrarsesion.php'>(CERRAR SESIÓN)</a></div>";
}



if(isset($_GET['perfil'])){
	if(isset($_SESSION['rol'])){
		if($_SESSION['login'] != $_GET['perfil']){
			header("Location:../index.php");
		}
	} else {
		header("Location:../index.php");
	}
}
else
	header("Location:../index.php");


if(isset($_SESSION['rol'])){
	if($_SESSION['rol'] == "admin")
		$adminButton = "<td><a href='../admin/admin.php'><img class = 'interactiveButton' src='../img/adminIcon.png'></a></td>";
	if($_SESSION['rol'] == "registrado" || $_SESSION['rol'] == "admin"){
		$newMsgButton = "<td><a href='../index.php?newmsg'><img class = 'interactiveButton' src='../img/newMessageIcon.png'></a></td>";
		$editProfileButton = "<td><a href='../registrado/editarperfil.php?perfil=$_SESSION[login]'><img class = 'interactiveButton' src='../img/editIcon.png'></a></td>";
	}
}

$linkDB = mysqli_connect($host, $userDB, $passwordDB, $db, $port);

if(isset($_POST['cambioNombre'])){
	$mensajeUpdate = "UPDATE usuarios SET nombre='$_POST[newName]', apellidos='$_POST[newApellidos]' WHERE login='$_SESSION[login]';";
	mysqli_query($linkDB, $mensajeUpdate);
}

$mensajeSelect = "SELECT * FROM usuarios WHERE login='$_SESSION[login]';";
$mensajeServidor = mysqli_query($linkDB, $mensajeSelect);
$datosUsuario = mysqli_fetch_array($mensajeServidor, MYSQLI_ASSOC);

if(isset($_POST['cambioPass'])){
	$oldHash_mensaje = "SELECT PASSWORD('$_POST[oldPass]');";
	$oldHash_query = mysqli_query($linkDB, $oldHash_mensaje);
	$oldHash = mysqli_fetch_row($oldHash_query) or die();
	
	if($oldHash[0] == $datosUsuario['password']){
		if($_POST['newPass1'] == $_POST['newPass2']){
			if(preg_match('/[A-Z,a-z,0-9,.]{7,32}$/',$_POST['newPass1'])){
				$updateHash_mensaje = "UPDATE usuarios SET password=PASSWORD('$_POST[newPass1]') WHERE login='$_SESSION[login]';";
				mysqli_query($linkDB, $updateHash_mensaje);
				$confirmacionmsj = "<br><div id='confirmacionmsj'>Contraseña cambiada con éxito</div>";
			}
			else 
				$errormsj = "<br><div id='errormsj'>La contraseña ha de tener una longitud entre 7 y 32 caracteres</div>";
		}
		else{
			$errormsj = "<br><div id='errormsj'>Las nuevas contraseñas no coinciden</div>";
		}
	}
	else{
		$errormsj = "<br><div id='errormsj'>La antigua contraseña introducida no es correcta</div>";
	}
}




mysqli_close($linkDB);
?>

<html>
	<head>
		<title>Editar perfil | Anuncia2</title>
		<meta charset = "utf-8"/>
		<link rel="stylesheet" type="text/css" href="../css/estilos.css">
	</head>
	<body>
		<div class = "main">
			<div id = "banner">
					<h1 id = "bannerText"><a href="../index.php">Anuncia2</a></h1>
					<?php if(isset($mensajeBanner)) echo $mensajeBanner; ?>
					<table>
						<tr id = "botonesBarra">
						<?php if(isset($adminButton)) echo $adminButton; ?>
						<?php if(isset($editProfileButton)) echo $editProfileButton; ?>
						<?php if(isset($newMsgButton)) echo $newMsgButton; ?>
						</tr>
					</table>
			</div>
			
			<h1 class = "titulo">Sección de <?php if(isset($datosUsuario)) echo "$datosUsuario[nombre] $datosUsuario[apellidos] ($datosUsuario[login])" ?></h1>
			
			<div class = "content">
				<h2 class = "titulo">Editar perfil</h2>
				<div class = "formularioBack">
					<div id = "formulario">
						<form method='POST'>
							Nombre: <input type="text" name="newName" value='<?php echo "$datosUsuario[nombre]"; ?>'></input>
							Apellidos: <input type="text" name="newApellidos" value='<?php echo "$datosUsuario[apellidos]"; ?>'></input>
							<br><br>
							<input class='interactiveButton' type="submit" name="cambioNombre" value="Cambiar datos personales"></input>
							<br><br>
							<img id="bigProfilePic" src="../img/profilePics/<?php echo "$_SESSION[imagen]";?>"><br>
							Imagen de perfil: <input type="file" name="newImage"></input>
							<br><br>
							<input class='interactiveButton' type="submit" name="cambioImagen" value="Cambiar imagen"></input>
						</form>
					</div>
				</div>
				
				<h2 class = "titulo">Cambiar la contraseña</h2>
				<div class = "formularioBack">
					<div id = "formulario">
						<form method='POST'>
							Antigua contraseña: <input type="password" name="oldPass"></input>
							<br>
							Nueva contraseña: <input type="password" name="newPass1"></input>
							Repita contraseña: <input type="password" name="newPass2"></input>
							<br><br>
							<input class='interactiveButton' type="submit" name="cambioPass" value="Cambiar contraseña"></input>
						</form>
					</div>
					
					<?php if(isset($errormsj)) echo $errormsj; ?>
					<?php if(isset($confirmacionmsj)) echo $confirmacionmsj; ?>
					
				</div>
		</div>
	</body>
</html>