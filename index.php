<?php
if(!file_exists("./conf/config.php")){
		header("Location:./instalar/instalar.php");
}
else{
	include_once("./conf/config.php");
}

session_start();
if(!isset($_SESSION['autenticado'])){
	$mensajeBanner = "<div id = 'inicioText'>No has iniciado sesion <a href='./registrado/iniciosesion.php'>INICIAR</a></div>";
}
else {
	$mensajeBanner = "<div id = 'inicioText'>Bienvenido $_SESSION[login] ($_SESSION[rol]) 
	<a href='./registrado/cerrarsesion.php'>(CERRAR SESIÓN)</a></div>";
}

if(isset($_POST['publicar'])){
	
			if(isset($_SESSION['login'])){
			$linkDB = mysqli_connect($host, $userDB, $passwordDB, $db, $port);
			$mensaje = $_POST['textoMensaje'];
		
			$mensajeServidor = "INSERT INTO anuncios VALUES (DEFAULT, CURDATE(), 
			DATE_ADD( CURDATE(), INTERVAL 1 DAY), '$mensaje', '$_SESSION[login]');";
			mysqli_query($linkDB, $mensajeServidor) or die();
			mysqli_close($linkDB);
			header("Location:./index.php");
			}
	
}


if(isset($_SESSION['rol'])){
	if($_SESSION['rol'] == "admin")
		$adminButton = "<td><a href='./admin/admin.php'><img src='./img/adminIcon.png'></a></td>";
	if($_SESSION['rol'] == "registrado" || $_SESSION['rol'] == "admin")
		$newMsgButton = "<td><a href='./index.php?newmsg'><img src='./img/newMessageIcon.png'></a></td>";
}	
?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./css/estilos.css">
	</head>
	<body>
		<div class = "main">
			<div id = "banner">
					<h1 id = "bannerText"><a href="./index.php">ANUNCIA2 AW</a></h1>
					<?php if(isset($mensajeBanner)) echo $mensajeBanner; ?>
					<table>
						<tr id = "botonesBarra">
						<?php if(isset($adminButton)) echo $adminButton; ?>
						<?php if(isset($newMsgButton)) echo $newMsgButton; ?>
						</tr>
					</table>
			</div>
		
			
			<div class = "content">
			<?php 
			include_once("./conf/config.php");
			$linkDB = mysqli_connect($host, $userDB, $passwordDB, $db, $port);
			$consultaMensajes = "SELECT * FROM anuncios;";
			$respuestaMensajes = mysqli_query($linkDB, $consultaMensajes);
			?>
			<table>	
			<?php
			while($fila=mysqli_fetch_array($respuestaMensajes,MYSQLI_ASSOC)){
				echo "<tr><td class = 'mensaje'><div class = 'infoMensaje'><b>$fila[usuario]</b> <i>($fila[fecha])</i></div>$fila[mensaje]</td></tr>";
			}
			?>
			</table>
			</div>
			
			<?php if(isset ($_GET['newmsg'])){ ?>
			<div id="mensajeBack"><a href='./index.php'></a></div>
				<div id = "menuMensaje">
				<form method = 'POST'>
					<textarea id = 'menuTextArea' placeholder = 'Escribe aquí tu anuncio' name="textoMensaje"></textarea>
					<div id = 'cancelarBtn'><a href='./index.php'><img src = './img/cancelIcon.png'></a></div>
					<input id="enviarMensajeBtn" type="submit" value = "PUBLICAR ANUNCIO" name = "publicar"></input>
					
				</form>
				</div>
			<?php } ?>
			


		</div>
	</body>
</html>