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




if(isset($_SESSION['rol'])){
	if($_SESSION['rol'] != "admin"){
		header("Location:../index.php");
	}
} else {
	header("Location:../index.php");
}

if(isset($_GET['user'])){
		echo "<script>alert('Actualizando roles de usuario')</script>";
		
		$linkdb = mysqli_connect($host, $userDB, $passwordDB, $db, $port) or die();
	
		$accion = $_GET['accion'];
		$user = $_GET['user'];
		
		if($accion == 'activar'){
			$mensajeServidor = "UPDATE usuarios SET rol='registrado' WHERE login='$user';";
			mysqli_query($linkdb, $mensajeServidor) or die();
		}
		
		else if($accion == 'desactivar'){
			$mensajeServidor = "UPDATE usuarios SET rol='noactivo' WHERE login='$user';";
			mysqli_query($linkdb, $mensajeServidor) or die();
		}
		
		else if($accion == 'eliminar'){
			$mensajeServidor = "DELETE FROM usuarios WHERE login='$user';";
			mysqli_query($linkdb, $mensajeServidor) or die();
		}
		
		mysqli_close($linkdb);
		
		header("Location:./admin.php");
		
}

if(isset($_SESSION['rol'])){
	if($_SESSION['rol'] == "admin")
		$adminButton = "<td><a href='./admin/admin.php'><img class = 'interactiveButton' src='../img/adminIcon.png'></a></td>";
	if($_SESSION['rol'] == "registrado" || $_SESSION['rol'] == "admin")
		$newMsgButton = "<td><a href='./index.php?newmsg'><img class = 'interactiveButton' src='../img/newMessageIcon.png'></a></td>";
}

?>

<html>
	<head>
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
						<?php if(isset($newMsgButton)) echo $newMsgButton; ?>
						</tr>
					</table>
			</div>
			
			<h1 class = "titulo">Sección del administrador</h1>
			
			<div class = "content">
				<h2 class = "titulo">Gestión de usuarios</h2>
				<table id = "userTable">
				<tr><td>¿Activado?</td><td>Login</td><td>Nombre</td><td>Apellidos</td><td>Acciones</td></tr>
				<?php
				include_once("../conf/config.php");
				$linkDB = mysqli_connect($host, $userDB, $passwordDB, $db, $port);
				$consulta = "SELECT * FROM usuarios;";
				$datos = mysqli_query($linkDB, $consulta);
				
				while($fila=mysqli_fetch_array($datos,MYSQLI_ASSOC)){
					$activo = "N/A";
					$accionActivar = "N/A";
						if($fila['rol'] != "admin"){
						if($fila['rol'] == "registrado"){
							$activo = "Si";
							$accionActivar = "<a href='?user=$fila[login]&accion=desactivar'>DESACTIVAR</a>";
						}
						if($fila['rol'] == "noactivo"){
							$activo = "No";
							$accionActivar = "<a href='?user=$fila[login]&accion=activar'>ACTIVAR</a>";
						}
						
						$accionEliminar = "<a href='?user=$fila[login]&accion=eliminar'>ELIMINAR</a>";

						echo "<tr><td>$activo</td><td>$fila[login]</td><td>$fila[nombre]</td><td>$fila[apellidos]</td><td>$accionActivar  $accionEliminar</td></tr>";
					}
				} 
				mysqli_close($linkDB);
				?>
				</table>
			</div>
		</div>
	</body>
</html>