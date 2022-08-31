<?php
function Balancear0($array,$nuevos){
  	for($i=0;$i<count($array);$i++){
  	  // Comparar codigos
  	  for($j=$i+1;$j<count($array);$j++){
  	    // Si Son del mismo codigo
  	    if(substr($array[$i][0],0,2)==substr($array[$j][0],0,2)){
  	      // Buscar un codigo distinto en la lista de nuevos
          for($k=0;$k<count($nuevos);$k++){
  	        if(substr($nuevos[$k][0],0,2)!=substr($array[$j][0],0,2)){
  	          $aux = $nuevos[$k];
  	          $nuevos[$k] = $array[$j];
  	          $array[$j] = $aux;
  	        }
  	      }
  	    }
  	  }
  	}
	}
//
function conversionYdescarga($datos, $filename = "resultados.csv", $delimiter=",") {
      $f = fopen('php://memory', 'w');
      foreach ($datos as $valor) {
          fputcsv($f, $valor, $delimiter);
      }
      fseek($f, 0);
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="'.$filename.'",');
      fpassthru($f);
  }
//
function AumentarAlumno($AuxAlumnos,$Nuevos)
{
  $dado = [1,1, 0, 1, 1, 1,1,1, 1,1];
  $indice_aleatorio = mt_rand(0, count($dado) - 1);
  $asignar = $dado[$indice_aleatorio];
  $fila=count($AuxAlumnos);
  if ($asignar==1) {
    if (count($Nuevos)>0) {//si hay elementos, agrega
      $AuxAlumnos[$fila]=$Nuevos[0];//agrega al final
      array_splice($Nuevos, 0, 1);//elimia el primero que a sido agregado
    }
    return array($AuxAlumnos,$Nuevos);
  }
  else {return array($AuxAlumnos,$Nuevos);}
}
//Alumnos antiguos por docentesfun
function AsignarNuevosAlumnos($AuxAlumnos,$Nuevos,$Limite,$cont)
{
  $fila=count($AuxAlumnos);
  $a=$cont;
  while ($a<$Limite) {//$Limite
    if (count($Nuevos)>0) {
      $AuxAlumnos[$fila]=$Nuevos[0];$a+=1;$fila+=1;
      array_splice($Nuevos, 0, 1);//elimia el primero que a sido agregado
    }
    else {break;}
  }
  return array($AuxAlumnos,$Nuevos);
}
function DisminuirAlumnos($AuxAlumnos,$Nuevos,$Limite,$cont)
{
  $a=$cont;
  while ($a>$Limite) {//$Limite
    $b=count($Nuevos);
    $fila=count($AuxAlumnos)-1;//posicion del ultimo elemento
    $Nuevos[$b]=$AuxAlumnos[$fila];//agrega al final el alumno excdente
    array_splice($AuxAlumnos, $fila, 1);//elimia el ultimo porque excedentes
    $a-=1;
  }
  return array($AuxAlumnos,$Nuevos);
}
//verifica existencia de elemento en un arreglo
function Existe($elemento,$array)
  {
    for ($i=0; $i < count($array); $i++) {
      if ($elemento==$array[$i][0]) {return true;}
    }
    return false;
  }
//recuperamos csv para la manipulación de datos
function RecuperarcsvToArray($archivo)
{
  $fila=0;//contador para recuperar datos
  $archcsv=fopen($archivo,"r");
  while (($registro= fgetcsv($archcsv,1024,","))== true) {//mientras haya comas
    $num=count($registro);//numero de campos de cada linea
    for ($i=0; $i <$num ; $i++) {
      $datos[$fila][$i]=$registro[$i];//recupera cada atributo
    }
    $fila++;
  }
  fclose($archcsv);
  return $datos;//matriz con todos los elementos del archivo
}

function listarAlumnos($archivo,$archivo1){
  $fila=0;//contador para recuperar datos
  $archcsv=fopen($archivo,"r");
  while (($registro= fgetcsv($archcsv,1024,","))== true) {//mientras haya comas
    $archcsv1=fopen($archivo1,"r");
    $existe=false;
    while (($registro1= fgetcsv($archcsv1,1024,","))== true){//mientras haya elementos
      if ($registro[0]==$registro1[0]) { $existe=true; break;   }// existe alumno en las 2 tablas
    }
    if (!$existe) {//si no existe guardamos (no hace tutoría o es nuevo)
      $num=count($registro);//numero de campos de cada linea
      for ($i=0; $i <$num ; $i++) {
        $datos[$fila][$i]=$registro[$i];//recupera cada atributo
      }
      $fila++;
    }
    fclose($archcsv1);
  }
  fclose($archcsv);
  return $datos;//matriz con todos los elementos del archivo
}

//no tutorados y tutorados
function NoT_yNuevos($archivo,$archivo1){
  $fila=0;
  for ($i=0; $i < count($archivo); $i++) {
    $existe=false;
    for ($j=0; $j < count($archivo1); $j++) {
      if ($archivo[$i][0]==$archivo1[$j][0]) { $existe=true; break;   }// existe alumno en las 2 tablas
    }
    if (!$existe) {//si no existe guardamos (no hace tutoría o es nuevo)
      $num=count($archivo[$i]);//numero de campos de cada linea
      for ($k=0; $k <$num ; $k++) {
        $datos[$fila][$k]=$archivo[$i][$k];//recupera cada atributo
      }$fila+=1;
    }
  }
  return $datos;//matriz con todos los elementos del archivo
}
#alumnos matriculados el anterior semestre, a partir de la distribución de alumnosxDocente
function MatriculadosAnterior($archivo){
  $fila=0;//contador para recuperar datos
  $fila1=0;//contador para recuperar docentes
  $archcsv=fopen($archivo,"r");
  while (($registro= fgetcsv($archcsv,1024,","))== true) {//mientras haya comas
    $str=strtolower($registro[0]);
    if ($str!="docente") {//si no existe guardamos (no hace tutoría o es nuevo)
      $num=count($registro);//numero de campos de cada linea
      for ($i=0; $i <$num ; $i++) {
        $datos[$fila][$i]=$registro[$i];//recupera cada atributo
      }
      $fila++;
    }
    else{
      $num=count($registro);//numero de campos de cada linea
      for ($i=0; $i <$num ; $i++) {
        $datosD[$fila1][$i]=$registro[$i];//recupera cada atributo
      }
      $fila1++;
    }
  }
  fclose($archcsv);
  //en datos esta solos alumnos y en datosD solo docentes
  return array($datos,$datosD);//matriz con todos los elementos del archivo
}
?>