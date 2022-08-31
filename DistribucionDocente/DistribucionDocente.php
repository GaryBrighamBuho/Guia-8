<?php
include("Funciones.php");
$dir ="files/";
$permitico=array('csv');
$distribuidos=$_FILES["distribuidos"]["name"];
$matriculados=$_FILES["matriculados"]["name"];
$docentes=$_FILES["docentes"]["name"];
$Option=$_POST['eleccioN'];
$BotonB=$_POST['Bbuscar'];
//$BotonDescargar=$_POST['Bdescarga'];

$ruta1=$dir.$distribuidos;
$ruta2=$dir.$matriculados;
$ruta3=$dir.$docentes;
$extencion1=explode(".",$distribuidos);
$extencion2=explode(".",$matriculados);
$extencion3=explode(".",$docentes);
$ex1=strtolower(end($extencion1));
$ex2=strtolower(end($extencion2));
$ex3=strtolower(end($extencion3));

//crea directorio si no existe
if (!file_exists($dir)) {
  mkdir($dir,0777);
}

if (in_array($ex1,$permitico)) {
  if (in_array($ex2,$permitico)) {
    if (in_array($ex3,$permitico)) {
      move_uploaded_file($_FILES["distribuidos"]["tmp_name"],$ruta1);
      move_uploaded_file($_FILES["matriculados"]["tmp_name"],$ruta2);
      move_uploaded_file($_FILES["docentes"]["tmp_name"],$ruta3);

      //recuperando archivos csv a arraglos BiDimencional
      $Matriculados=array();$Docentes=array();$Distribuidos=array();
      $Matriculados=RecuperarcsvToArray($dir.$matriculados);//recupera los matriculados
      array_splice($Matriculados, 0, 1);//elimina cabecera
      $Distribuidos=RecuperarcsvToArray($dir.$distribuidos);//recupera los distribuidos
      $Docentes=RecuperarcsvToArray($dir.$docentes);//recupera los docentes

      if ($BotonB) {
        $AlumnosAnterior=array();$DatosD=array();$DatosT=array();
        $DatosT=MatriculadosAnterrior($dir.$distribuidos);
        $AlumnosAnterior=$DatosT[0];//solo alumnos del Docente del anterior semestre
        $DatosD=$DatosT[1];//solo docentes del anterior semestre

        if ($Option=="distribucion") {
          $Limite=(int)((count($Matriculados))/(count($Docentes)-1));//-1 por las filas cabecera
          $Distribucion_Docente=array();//contruir nueva distribucion
          $Nuevos=array();$NoTutoria=array();
          $Nuevos=NoT_yNuevos($Matriculados,$AlumnosAnterior);//nuevos alumnos por asignar tutor
          //array_splice($Nuevos, 0, 1);//archivo limpio
          $NoTutoria=NoT_yNuevos($AlumnosAnterior,$Matriculados);//Alumnos que no haran tutoria
          //$NrAlAnt=count($AlumnosAnterior);//#alumnos anterior semestre
          //$Manti=$NrAlAnt-count($NoTutoria);
          //printf("#A_anter ".$NrAlAnt.", Se mantienes ".$Manti." NoTuto ".count($NoTutoria)." Tuto ".count($Nuevos)."<br>");
          $PorAsig=(count($Matriculados))-$Limite*(count($Docentes)-1);

          //$Sobran=count($Nuevos)-((count($Matriculados))-$Limite*(count($Docentes)-1));

          //
          $fila_=0;
          for ($i=0; $i <count($Distribuidos) ; $i++) {

            if (strtolower($Distribuidos[$i][0])=="docente") {
              $Distribucion_Docente[$fila_]=$Distribuidos[$i];$i+=1;
              $cont=0;//verifica limite de alumnos
              $AuxAlumnos=array();$filAux=0;
              while (strtolower($Distribuidos[$i][0])!="docente") {
                if (Existe($Distribuidos[$i][0],$NoTutoria)){$i+=1;}//si no hace tutoria se excluye
                else {$AuxAlumnos[$filAux]=$Distribuidos[$i];$i+=1;$cont+=1;$filAux+=1;}
                if ($i ==count($Distribuidos)) {break;}
              }$i-=1;
              //
              $RecuperaAux=array();
              if ($cont<$Limite) {//$Limite
                $RecuperaAux=AsignarNuevosAlumnos($AuxAlumnos,$Nuevos,$Limite,$cont);
                $AuxAlumnos=$RecuperaAux[0];
                $Nuevos=$RecuperaAux[1];

                $RecuperaAux=AumentarAlumno($AuxAlumnos,$Nuevos);
                $AuxAlumnos=$RecuperaAux[0];
                $Nuevos=$RecuperaAux[1];

                //printf("(:: Aux< ".count($AuxAlumnos)." Asig ".count($Distribucion_Docente)." fila ".$fila_."<br>");
                $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
                $fila_=count($Distribucion_Docente);
                //printf("(: Aux ".count($AuxAlumnos)." Nuev ".count($Nuevos)." fila ".$fila_."<br>");
              }
              elseif($cont==$Limite)  {
                $RecuperaAux=AumentarAlumno($AuxAlumnos,$Nuevos);
                $AuxAlumnos=$RecuperaAux[0];
                $Nuevos=$RecuperaAux[1];
                //printf("(:: Aux== ".count($AuxAlumnos)." Asig ".count($Distribucion_Docente)." fila ".$fila_."<br>");
                $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
                $fila_=count($Distribucion_Docente);
                //printf("(: Aux ".count($AuxAlumnos)." Nuev ".count($Nuevos)." fila ".$fila_."<br>");
              }
              else {//else
                $RecuperaAux=DisminuirAlumnos($AuxAlumnos,$Nuevos,$Limite,$cont);
                $AuxAlumnos=$RecuperaAux[0];
                $Nuevos=$RecuperaAux[1];

                $RecuperaAux=AumentarAlumno($AuxAlumnos,$Nuevos);
                $AuxAlumnos=$RecuperaAux[0];
                $Nuevos=$RecuperaAux[1];
                //printf("(:: Aux > ".count($AuxAlumnos)." Asig ".count($Distribucion_Docente)." fila ".$fila_."<br>");
                $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
                $fila_=count($Distribucion_Docente);
                //printf("(: Aux ".count($AuxAlumnos)." Nuev ".count($Nuevos)." fila ".$fila_."<br>");
              }
            }
          }
          //printf("N° Alumnos Distribuidos- ".(count($Distribucion_Docente)-36)." falta asignar ".count($Nuevos)." alumnos<br><br>");

          printf($PorAsig." Docentes tendran mas 1 alumno <br>");
          printf("N° Matriculados ".(count($Matriculados)).", N° Docentes ".(count($Docentes)-1).",  Asignacion a ".$Limite." alumnos"."<br><br>");

          echo "<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>";
          echo "<table>";
          //echo .... cabeceras de la tabla
          for ($i=0; $i < count($Distribucion_Docente); $i++) {
            echo "<tr>";
            foreach ($Distribucion_Docente[$i] as $valor) {
              echo "<td>".$valor."</td>";
            }
            echo "</tr>";
          }
          echo "</table>";
        }
        elseif ($Option=="noTutorados") {
          $Datos=array();
          $Datos=NoT_yNuevos($AlumnosAnterior,$Matriculados);// en datos está todo un distribuidos
          //$Datos=$Matriculados;
          echo "Alumnos no tutorados :-) <br>";
          echo "<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>";
          echo "<table>";
          //echo .... cabeceras de la tabla
          for ($i=0; $i < count($Datos); $i++) {
            echo "<tr>";
            foreach ($Datos[$i] as $valor) {
              echo "<td>".$valor."</td>";
            }
            echo "</tr>";
          }
          echo "</table>";
        }
        else {
          $Datos=array();
          $Datos=NoT_yNuevos($Matriculados,$AlumnosAnterior);// en datos está todo un distribuidos
          //
          echo "<table>";
          //echo .... cabeceras de la tabla
          echo "Alumnos tutorados :-)";
          //printf("#t ".count($Datos)." #AA".count($AlumnosAnterior)." #M ".count($Matriculados)."<br>");
          echo "<tr>"."<td>"."Código"."</td>"."<td>"."Nombres"."</td>"."</tr><br>";
          for ($i=0; $i < count($Datos); $i++) {
            echo "<tr>";
            foreach ($Datos[$i] as $valor) {
              echo "<td>".$valor."</td>";
            }
            //$tabla
            echo "</tr>";
          }
          //$tabla=$Datos;
          echo "</table>";
        }
      }
      elseif (0==1) {
        $AlumnosAnterior=array();$DatosD=array();$DatosT=array();
        $DatosT=MatriculadosAnterrior($dir.$distribuidos);
        $AlumnosAnterior=$DatosT[0];//solo alumnos del Docente del anterior semestre
        $DatosD=$DatosT[1];//solo docentes del anterior semestre
        if (1==1) {
          $Limite=(int)((count($Matriculados))/(count($Docentes)-1));//-1 por las filas cabecera
          $Distribucion_Docente=array();//contruir nueva distribucion
          $Nuevos=array();$NoTutoria=array();
          $Nuevos=NoT_yNuevos($Matriculados,$AlumnosAnterior);//nuevos alumnos por asignar tutor
          //array_splice($Nuevos, 0, 1);//archivo limpio
          $NoTutoria=NoT_yNuevos($AlumnosAnterior,$Matriculados);//Alumnos que no haran tutoria
          $PorAsig=(count($Matriculados))-$Limite*(count($Docentes)-1);

          $fila_=0;
          for ($i=0; $i <count($Distribuidos) ; $i++) {

            if (strtolower($Distribuidos[$i][0])=="docente") {
              $Distribucion_Docente[$fila_]=$Distribuidos[$i];$i+=1;
              $cont=0;//verifica limite de alumnos
              $AuxAlumnos=array();$filAux=0;
              while (strtolower($Distribuidos[$i][0])!="docente") {
                if (Existe($Distribuidos[$i][0],$NoTutoria)){$i+=1;}//si no hace tutoria se excluye
                else {$AuxAlumnos[$filAux]=$Distribuidos[$i];$i+=1;$cont+=1;$filAux+=1;}
                if ($i ==count($Distribuidos)) {break;}
              }$i-=1;
              //
              $RecuperaAux=array();
              if ($cont<$Limite) {//$Limite
                $RecuperaAux=AsignarNuevosAlumnos($AuxAlumnos,$Nuevos,$Limite,$cont);
                $AuxAlumnos=$RecuperaAux[0];
                $Nuevos=$RecuperaAux[1];

                $RecuperaAux=AumentarAlumno($AuxAlumnos,$Nuevos);
                $AuxAlumnos=$RecuperaAux[0];
                $Nuevos=$RecuperaAux[1];

                //printf("(:: Aux< ".count($AuxAlumnos)." Asig ".count($Distribucion_Docente)." fila ".$fila_."<br>");
                $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
                $fila_=count($Distribucion_Docente);
                //printf("(: Aux ".count($AuxAlumnos)." Nuev ".count($Nuevos)." fila ".$fila_."<br>");
              }
              elseif($cont==$Limite)  {
                $RecuperaAux=AumentarAlumno($AuxAlumnos,$Nuevos);
                $AuxAlumnos=$RecuperaAux[0];
                $Nuevos=$RecuperaAux[1];
                //printf("(:: Aux== ".count($AuxAlumnos)." Asig ".count($Distribucion_Docente)." fila ".$fila_."<br>");
                $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
                $fila_=count($Distribucion_Docente);
                //printf("(: Aux ".count($AuxAlumnos)." Nuev ".count($Nuevos)." fila ".$fila_."<br>");
              }
              else {//else
                $RecuperaAux=DisminuirAlumnos($AuxAlumnos,$Nuevos,$Limite,$cont);
                $AuxAlumnos=$RecuperaAux[0];
                $Nuevos=$RecuperaAux[1];

                $RecuperaAux=AumentarAlumno($AuxAlumnos,$Nuevos);
                $AuxAlumnos=$RecuperaAux[0];
                $Nuevos=$RecuperaAux[1];
                //printf("(:: Aux > ".count($AuxAlumnos)." Asig ".count($Distribucion_Docente)." fila ".$fila_."<br>");
                $Distribucion_Docente = array_merge($Distribucion_Docente, $AuxAlumnos);
                $fila_=count($Distribucion_Docente);
                //printf("(: Aux ".count($AuxAlumnos)." Nuev ".count($Nuevos)." fila ".$fila_."<br>");
              }
            }
          }
          //$Distribucion_Docente //se muestra
          conversionYdescarga($Distribucion_Docente);
        }
      }
    }else {echo "para este propósito3, solo se permite archivo de extencion .csv";}
  }else {echo "para este propósito2, solo se permite archivo de extencion .csv";}
}else {echo "para este propósito1, solo se permite archivo de extencion .csv";}
?>

