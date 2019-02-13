<?php
/** Devuelve la fecha en formato '1 de Enero de 2016'
 * 
 * @param string fecha $fecha
 * @return string fecha con formato largo española dd de Mes de aaaa
 * 
 */
function fechaLarga($fecha)
{
    $fechaaux=  fechaEspanola($fecha);//Nos aseguramos que sea dd-mm-aaaa
    if (strpos($fechaaux,"/") !== FALSE) {
        $aux = explode("/", $fecha);
    }
    if (strpos($fechaaux,"-") !== FALSE) {
        $aux = explode("-", $fecha);
    }

    $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", 
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    
    return "$aux[0] de ".$meses[$aux[1]-1]." de $aux[2]";
}

// Le da la vuelta a la fecha
function cambiaFecha($fecha)
{
    if (strpos($fecha,"/") !== FALSE) {
        $vuelta = explode("/", $fecha);
        return "$vuelta[2]/$vuelta[1]/$vuelta[0]";
    }
    
    if (strpos($fecha,"-") !== FALSE) {
        $vuelta = explode("-", $fecha);
        return "$vuelta[2]-$vuelta[1]-$vuelta[0]";
    }
    
}

/** Devuelve la fecha en formato YYYY-MM-DD
 * 
 * @param type $fecha
 * @return la fecha en formato YYYY-MM-DD
 */
function fechaInglesa($fecha)
{
    if (strpos($fecha,"/") !== FALSE) {
        $arrayFecha = explode("/", $fecha);
    }else {
        $arrayFecha = explode("-", $fecha);
    }

    if(strlen($arrayFecha[0]) == 4) {
        return $fecha;
    }else {
        return cambiaFecha($fecha);
      }
}

/** Devuelve la fecha en formato DD-MM-YYYY
 * 
 * @param type $fecha
 * @return type fecha en formato DD-MM-YYYY
 */
function fechaEspanola($fecha)
{
    return cambiaFecha(fechaInglesa($fecha));
}
function fechaActual(){
    return date("Y-m-d");
}
function compararFechas($primera, $segunda){
    $primera=fechaEspanola($primera);
    $segunda=fechaEspanola($segunda);
    if (strpos($primera,"/") !== FALSE) {
        $c = "/";
    }else {
        $c= "-";
    }
    $valoresPrimera = explode ($c, $primera); 
     if (strpos($segunda,"/") !== FALSE) {
        $c = "/";
    }else {
        $c= "-";
    }
    $valoresSegunda = explode($c, $segunda); 
  $diaPrimera    = $valoresPrimera[0];  
  $mesPrimera  = $valoresPrimera[1];  
  $anyoPrimera   = $valoresPrimera[2]; 
  $diaSegunda   = $valoresSegunda[0];  
  $mesSegunda = $valoresSegunda[1];  
  $anyoSegunda  = $valoresSegunda[2];
  $diasPrimeraJuliano = gregoriantojd($mesPrimera, $diaPrimera, $anyoPrimera);  
  $diasSegundaJuliano = gregoriantojd($mesSegunda, $diaSegunda, $anyoSegunda);     
  if(!checkdate($mesPrimera, $diaPrimera, $anyoPrimera)){
    // "La fecha ".$primera." no es válida";
    return 0;
  }elseif(!checkdate($mesSegunda, $diaSegunda, $anyoSegunda)){
    // "La fecha ".$segunda." no es válida";
    return 0;
  }else{
    return  $diasPrimeraJuliano - $diasSegundaJuliano;
  } 
}
function isadate($fecha){
    //Función que comprueba que el campo sea una fecha dd-mm-aaaa o yyyy-mm-dd
    //comprobar que funciona
    if(preg_match("/^\d{1,2}-\d{1,2}-\d{4}$/", $fecha))
        return true;
    if(preg_match("/^\d{4}-\d{1,2}-\d{1,2}$/", $fecha))
        return true;
    if(preg_match("#^\d{1,2}/\d{1,2}/\d{4}$#", $fecha))
        return true;
    if(preg_match("#^\d{4}/\d{1,2}/\d{1,2}$#", $fecha))
        return true;
    return false;
}

?>