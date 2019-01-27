<?php
if(!file_exists("../conf/config.php")){
		header("Location:../instalar/instalar.php");
}
else{
	include_once("../conf/config.php");
}

session_start();

if(!isset($_SESSION['autenticado'])){
	$mensajeBanner = "<div id = 'inicioText'>No has iniciado sesion <a href='./iniciosesion.php'>NICIAR</a></div>";
}
else {
	$mensajeBanner = "<div id = 'inicioText'>Bienvenido $_SESSION[login] ($_SESSION[rol]) 
	<a href='./cerrarsesion.php'>(CERRAR SESIÓN)</a></div>";
}

if(isset($_SESSION['rol'])){
	if($_SESSION['rol'] == "admin")
		$adminButton = "<td><a href='../admin/admin.php'><img class = 'interactiveButton' src='../img/adminIcon.png'></a></td>";
	if($_SESSION['rol'] == "registrado" || $_SESSION['rol'] == "admin"){
		$newMsgButton = "<td><a href='../index.php?newmsg'><img class = 'interactiveButton' src='../img/newMessageIcon.png'></a></td>";
		$editProfileButton = "<td><a href='../registrado/editarperfil.php?perfil=$_SESSION[login]'><img class = 'interactiveButton' src='../img/editIcon.png'></a></td>";
	}
}

if(isset($_POST['registro'])){
		$funciona = true;
		$linkDB = mysqli_connect($host, $userDB, $passwordDB, $db, $port);
		$loginNewUser = $_POST['loginNewUser'];
		$passwordNewUser = $_POST['passwordNewUser'];
		$passwordNewUser2 = $_POST['passwordNewUser2'];
		$nombreNewUser = $_POST['nombreNewUser'];
		$apellidosNewUser = $_POST['apellidosNewUser'];

		

		if($passwordNewUser != $passwordNewUser2){
			$errormsj = "<br><div id='errormsj'>Las contraseñas no coinciden</div>";
			$funciona = false;
		}
		
		$mensajeServidor = "SELECT * FROM usuarios WHERE login = '$loginNewUser'";
		$consultaSelect = mysqli_query($linkDB, $mensajeServidor);
		if(mysqli_num_rows($consultaSelect) == 1){
			$errormsj = "<br><div id='errormsj'>El nombre de usuario ya está en uso</div>";
			$funciona = false;
		}
		
		if(!preg_match('/[A-Z,a-z,0-9,.]{7,32}$/', $passwordNewUser)){
				$funciona = false;
				$errormsj = "<br><div id='errormsj'>La contraseña ha de tener una longitud entre 7 y 32 caracteres</div>";
		}
		
		if($funciona){
			$consultaInsert = "INSERT INTO usuarios VALUES ( '$loginNewUser', PASSWORD('$passwordNewUser'), 'noactivo', '$nombreNewUser', '$apellidosNewUser');";
			mysqli_query( $linkDB, $consultaInsert);
			$confirmacionmsj = "<br><div id='confirmacionmsj'>Usuario creado, debe esperar a que el administrador active su cuenta</div>";
		}		
		
		mysqli_close( $linkDB );
}
?>

<!DOCTYPE html>
<head>
    <title>Registro</title>
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
	
    <h1 class = "titulo">Registro nuevo usuario</h1>
    <div class = "formularioBack">
		<div id = "formulario">
			<form method='POST'>
				Login: <input type="text" name="loginNewUser"></input>
				Password: <input type = "password" name="passwordNewUser"></input>
				Repita la password: <input type = "password" name="passwordNewUser2"></input>
				<br>
				Nombre: <input type="text" name="nombreNewUser"></input>
				Apellidos: <input type="text" name="apellidosNewUser"></input>
				<br>
				<input type="submit" name="registro" value="Registrarse"></input>
			</form>
		</div>
		
		<?php if(isset($errormsj)) echo $errormsj; ?>
		<?php if(isset($confirmacionmsj)) echo $confirmacionmsj; ?>
	</div>
</div>

</body>
</html>