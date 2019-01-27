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
		
			$mensajeServidor = "INSERT INTO anuncios VALUES (DEFAULT, NOW(), 
			DATE_ADD( NOW(), INTERVAL 1 DAY), '$mensaje', '$_SESSION[login]');";
			mysqli_query($linkDB, $mensajeServidor) or die();
			mysqli_close($linkDB);
			header("Location:./index.php");
			}	
}

if(isset($_GET['borrarMsg'])){
	if(isset($_SESSION['rol'])){
			if($_SESSION['rol'] == 'admin' || ($_SESSION['rol'] == 'registrado' && $_GET['autor']==$_SESSION['login'])){
				$idMsg = $_GET['borrarMsg'];
				$linkDB = mysqli_connect($host, $userDB, $passwordDB, $db, $port);
				$mensajeServidor = "DELETE FROM anuncios WHERE id=$idMsg;";
				mysqli_query($linkDB, $mensajeServidor) or die();
				mysqli_close($linkDB);
				header("Location:./index.php");
			}
	}
	else
		header("Location:./index.php");
}


if(isset($_SESSION['rol'])){
	if($_SESSION['rol'] == "admin")
		$adminButton = "<td><a href='./admin/admin.php'><img class = 'interactiveButton' src='./img/adminIcon.png'></a></td>";
	if($_SESSION['rol'] == "registrado" || $_SESSION['rol'] == "admin"){
		$newMsgButton = "<td><a href='./index.php?newmsg'><img class = 'interactiveButton' src='./img/newMessageIcon.png'></a></td>";
		$editProfileButton = "<td><a href='./registrado/editarperfil.php?perfil=$_SESSION[login]'><img class = 'interactiveButton' src='./img/editIcon.png'></a></td>";
	}
}	
?>

<html>
	<head>
		<title>Inicio | Anuncia2</title>
		<meta charset = "utf-8"/>
		<link rel="stylesheet" type="text/css" href="./css/estilos.css"/>
	</head>
	<body>
		<div class = "main">
			<div id = "banner">
					<h1 id = "bannerText"><a href="./index.php">Anuncia2</a></h1>
					<?php if(isset($mensajeBanner)) echo $mensajeBanner; ?>
					<table>
						<tr id = "botonesBarra">
						<?php if(isset($adminButton)) echo $adminButton; ?>
						<?php if(isset($editProfileButton)) echo $editProfileButton; ?>
						<?php if(isset($newMsgButton)) echo $newMsgButton; ?>
						</tr>
					</table>
			</div>
		
			
			<div class = "content">
			<?php 
			include_once("./conf/config.php");
			$linkDB = mysqli_connect($host, $userDB, $passwordDB, $db, $port);
			$consultaMensajes = "SELECT * FROM anuncios ORDER BY fecha DESC;";
			$respuestaMensajes = mysqli_query($linkDB, $consultaMensajes);
			?>
			<table class = "msjHolder">	
			<?php
			
			
			while($fila=mysqli_fetch_array($respuestaMensajes,MYSQLI_ASSOC)){
				
				$horasPasadasMsj = "SELECT TIMESTAMPDIFF(HOUR, '$fila[fecha]', NOW());";
				$horasPasadasSelect = mysqli_query($linkDB, $horasPasadasMsj) or die();
				$horasPasadas = mysqli_fetch_row($horasPasadasSelect) or die();
				
				//$minutosPasadosMsj = "MOD(SELECT TIMESTAMPDIFF(MINUTE, '$fila[fecha]', NOW()), 60);";
				$minutosPasadosMsj = "SELECT MOD(TIMESTAMPDIFF(MINUTE, '$fila[fecha]', NOW()), 60);";
				$minutosPasadosSelect = mysqli_query($linkDB, $minutosPasadosMsj) or die();
				$minutosPasados = mysqli_fetch_row($minutosPasadosSelect) or die();
				
				$tiempoMensaje = "hace $horasPasadas[0] hora/s y $minutosPasados[0] minuto/s";
				if($horasPasadas[0] == 0 && $minutosPasados[0] == 0){
						$tiempoMensaje = "ahora mismo";
				}
				else if($horasPasadas[0] == 0 && $minutosPasados[0] != 0){
						$tiempoMensaje = "hace $minutosPasados[0] minuto/s";
				}
				
				
				//echo "<script>alert('$horasPasadas[0]');</script>";
				if(file_exists('./img/profilePics/'.$fila['usuario'].'_profilepic.jpg')){
					echo "<tr><td class = 'mensaje'><img class='imagenMensaje' src='./img/profilePics/$fila[usuario]_profilepic.jpg'><div class='textoMensaje'>";
				}
				else{
					echo "<tr><td class = 'mensaje'><img class='imagenMensaje' src='./img/profilePics/user_profilepic.jpg'><div class='textoMensaje'>";
				}
				
				if(isset($_SESSION['rol'])){
					if($_SESSION['rol'] == 'admin'){
						echo "<div class = 'infoMensaje'><b>$fila[usuario]</b> <i>($fila[fecha]), $tiempoMensaje</i>:</div><a class = 'borrarMsgBtn' 
						href = './index.php?borrarMsg=$fila[id]&autor=$fila[usuario]'><img class = 'interactiveButton' src = './img/deleteIcon.png'></a><br>$fila[mensaje]";
					}
					else{
						if($fila['usuario'] == $_SESSION['login']){
							echo "<div class = 'infoMensaje'><b>$fila[usuario]</b> <i>($fila[fecha]), $tiempoMensaje</i>:</div><a class = 'borrarMsgBtn' 
							href = './index.php?borrarMsg=$fila[id]&autor=$fila[usuario]'><img class = 'interactiveButton' src = './img/deleteIcon.png'></a><br>$fila[mensaje]";
						}
						else
							echo "<div class = 'infoMensaje'><b>$fila[usuario]</b> <i>($fila[fecha]), $tiempoMensaje</i>:</div><br>$fila[mensaje]";
					}
				}
				else
					echo "<div class = 'infoMensaje'><b>$fila[usuario]</b> <i>($fila[fecha]), $tiempoMensaje</i>:</div><br>$fila[mensaje]";
				
				echo "</div></td></tr>";
			}
			
			?>
			</table>
			</div>
			
			<?php if(isset($_GET['newmsg']) && (isset($_SESSION['rol'])) && $_SESSION['rol'] != 'noactivo'){ ?>
			<div id="mensajeBack"><a href='./index.php'></a></div>
				<div id = "menuMensaje">
				<form method = 'POST'>
					<textarea id = 'menuTextArea' placeholder = 'Escribe aquí tu anuncio' name="textoMensaje"></textarea>
					<div id = 'cancelarBtn'><a href='./index.php'><img class = 'interactiveButton' src = './img/cancelIcon.png'></a></div>
					<input class = 'interactiveButton' id="enviarMensajeBtn" type="submit" value = "PUBLICAR ANUNCIO" name = "publicar"></input>
					
				</form>
				</div>
			<?php } ?>
			
			<script> 
				document.getElementById("menuTextArea").focus();
			</script>

		</div>
	</body>
</html>