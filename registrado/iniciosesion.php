<?php

if(!file_exists("../conf/config.php")){
		header("Location:../instalar/instalar.php");
}
else{
	include_once("../conf/config.php");
}

session_start();

if(!isset($_SESSION['autenticado'])){
	$mensajeBanner = "<div id = 'inicioText'>No has iniciado sesion <a href='./iniciosesion.php'>INICIAR</a></div>";
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


if(isset($_POST['inicio'])){ //si se ha pulsado el botón en el formulario
			//Recibimos las credenciales
			$login = $_POST['login'];
			$passwordUser = $_POST['password'];
			//comprobamos las credenciales en la tabla user de nuestra base de datos
			$linkdb = mysqli_connect($host, $userDB, $passwordDB, $db, $port);
			$consulta = "SELECT * FROM usuarios WHERE login = '$login' AND password = PASSWORD('$passwordUser');";
			$respuesta = mysqli_query( $linkdb, $consulta );
			
			if(mysqli_num_rows($respuesta) == 1){ //la autenticación ha sido positiva
						$datosUsuario = mysqli_fetch_array($respuesta, MYSQLI_ASSOC); //MYSQLI_ASSOC (dame la fila con una array pero 
						//con un array asociativo, esto significa que puedo acceder a los campos con su nombre (login pass rol, etc)
						
						if($datosUsuario['rol'] != 'noactivo'){
							$_SESSION['autenticado'] = true;
							$_SESSION['login'] = $datosUsuario['login'];
							$_SESSION['rol'] = $datosUsuario['rol'];
							//$_SESSION['imagen'] = $datosUsuario['imagenperfil'];
							header("Location:../index.php");
						}
						else {
							$errormsj = "<br><div class='errormsj'>Su cuenta aun no ha sido activada</div>";
						}	

			} else {
				$errormsj = "<br><div class='errormsj'>Error en las credenciales</div>";
			}
}
?>

<!DOCTYPE html>
<head>
		<title>Inicio de sesión | Anuncia2</title>
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
	
    <h1 class = "titulo">Inicio de sesión</h1>
    <div class = "formularioBack">
		<div id = "formulario">
			<form method = 'POST'>
				Login: <input type="text" name="login"></input>
				Password: <input type = "password" name="password"></input>
				<br>
				<input type="submit" name="inicio" value="Iniciar sesión"></input>
			</form>
			
			<?php if(isset($errormsj)) echo $errormsj; ?>
			
			<br>
			¿No registrado? <a href="./registro.php">Regístrate aquí</a>
		</div>
	</div>
</div>

</body>
</html>