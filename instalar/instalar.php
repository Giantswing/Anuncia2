<?php
if(isset($_POST['instala'])){
		$host = $_POST['host'];
		$port = $_POST['port'];
		$db = $_POST['db'];
		$userDB = $_POST['userDB'];
		$passwordDB = $_POST['passwordDB'];		
		
		$EnlaceAlSGBD = mysqli_connect($host , $userDB, $passwordDB, $db, $port) or die();
		
		if($EnlaceAlSGBD == null){
			//ha habido un problema al conectar
			$mensaje = "<br><div align='center'><i>Error en las credenciales, comprueba los datos.</i></div>";
		}	else {
			//continuamos con la instalación
			//1: guardamos las credenciales en el fichero config.php
			
			$fp = fopen("../conf/config.php", "w"); //lo guardamos en una variable "file pointer"
			//CONCATENAR EN PHP SE HACE CON UN PUNTO NO CON EL +, USAMOS COMILLAS SIMPLES PARA NO INTERPRETAR LAS VARIABLES!!!!!
			$texto = "<?php\n".'$host='."'$host';\n".'$port='."$port;\n".'$userDB='."'$userDB';\n".'$passwordDB='."'$passwordDB';\n".'$db='."'$db';\n?>";
			fwrite($fp, $texto);
			fclose($fp);
			
			//Crear tablas: crear sentencias sql como texto
			//TIPO DE DATO ENUM: solo puedes ser una opción, si puede ser varias opciones es un campo SET
			$sql[]="CREATE TABLE IF NOT EXISTS usuarios(
					login VARCHAR(40) NOT NULL PRIMARY KEY,
					password VARCHAR(512) NOT NULL,
					rol ENUM('admin','registrado','noactivo') NOT NULL,
					nombre VARCHAR(512),
					apellidos VARCHAR(512)
					);";
					
			$sql[]="CREATE TABLE IF NOT EXISTS anuncios(
					id INT(10) PRIMARY KEY AUTO_INCREMENT,
					fecha DATETIME NOT NULL,
					fecha_validez DATETIME,
					mensaje VARCHAR(2048),
					usuario VARCHAR(40) NOT NULL, 
					FOREIGN KEY(usuario) REFERENCES USUARIOS(login)
					ON DELETE CASCADE
					ON UPDATE CASCADE
					);";
			
			foreach($sql as $consulta){
				mysqli_query($EnlaceAlSGBD, $consulta) or die("Error en consulta $consulta ".mysqli_error($EnlaceAlSGBD));
			}
			
			//Ya hemos creado la infraestructura de tablas y vamos a pasar a la creación del administrador
			//La cabecera no la podemos reenviar una vez que hemos empezado a enviar HTML
			
			mysqli_close($EnlaceAlSGBD);
			header("Location:instalar.php?paso=2");
		}
}		

//si el botón está definido
if(isset($_POST['admin'])){
	//TENEMOS QUE VOLVER A ESTABLECER LA CONEXIÓN CON LA BASE DE DATOS!
	include_once("../conf/config.php");
	$linkDB = mysqli_connect($host, $userDB, $passwordDB, $db, $port);
	
	//Insertamos en la tabla de usuarios el primer usuario administrador
	$loginAdmin = mysqli_real_escape_string($linkDB, $_POST['loginAdmin']);
	$passwordAdmin = mysqli_real_escape_string($linkDB, $_POST['passwordAdmin']);
				
	$consultaInsert = "INSERT INTO USUARIOS VALUES('$loginAdmin', PASSWORD('$passwordAdmin'), 'admin', 'Admin', 'Admin');";
				
	mysqli_query($linkDB, $consultaInsert);
	mysqli_close($linkDB);
				
	$mensaje = "Usuario administrador '$loginAdmin' creado con éxito, borre la carpeta 'Instalar' y empiece a usar la AW";
}
?>


<!DOCTYPE html>
<head>
	<link rel = "stylesheet" href = "../css/estilos.css">
	<title>Instalación de Anuncia2</title>
	<meta charset = "utf-8"/>
</head>
<body>
	<div id = "banner">
		<h1 id = "bannerText"><a href="../index.php">Anuncia2</a></h1>
	</div>
		
	
	<div class = "content">Estas en el proceso de instalación e implantación de la aplicación web <b>Anuncia2</b>.
	Necesitamos cierta información para continuar.
	
	<ol>
	<li>Crea una base de datos en tu SGBD.</li>
	<li>Crea un usuario con privilegios sobre esa base de datos.</li>
	<li>Rellena el formulario siguiente con esas credenciales de acceso al SGBD</li>
	</ol>	

	
	<?php if(!isset($_GET['paso'])){ ?>
	
	    <div class = "formularioBack">
		<div id = "formulario">
			<form method = 'POST'>
				HOST: <input type="text" name="host"></input>
				Puerto: <input type = "text" name="port"></input>
				Nombre BD: <input type="text" name = "db" />
				Usuario SGBD: <input type="text" name="userDB" />
				Password SGBD: <input type = "password" name = "passwordDB" />
				<br>
				<input type="submit" name = "instala" value="INSTALAR (PASO 1)" />
			</form>
		</div>
		
	<!-- elseif lo podemos usar en vez de else if para hacerlo todo en una sola sentencia -->
	
	<?php } elseif ($_GET['paso']==2) { ?>
	
	<p>Ahora vamos a crear al usuario <b>Administrador</b> de la aplicación:</p>
	<div class = "formularioBack">
	<div id = "formulario">
		<form method = "POST" >
			Login Admin <input type="text" name = "loginAdmin" />
			Contraseña Admin <input type="password" name = "passwordAdmin" />
			<br>
			<input type="submit" name = "admin" value="INSTALAR (PASO 2)" />
		</form>
	</div>
	</div>
	
	<?php } ?>
	
	<?php if(isset($mensaje)) echo $mensaje; ?>
	
	</div>
	</div>
	
</body>
</html>