<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>examen desarrollo</title>
        <link rel="stylesheet" href="estilos.css">
    </head>
<body>
    <div id="container">
        <header class="centertext">
            PRIMER EXAMEN DE DESARROLLO DE SOFTWARE
        </header>
        <br>
        <div id="id2" class="centertext">
          <legend>Seleccione los archivos CSV:</legend>
          <form action="gestorArchivo.php" method="POST" enctype="multipart/form-data">
              <div id="ar" class="centertext">
                <! archivo csv 2021-2">
                Archivo Alumnos Distribuidos el semestre pasado<br>
                <input type="file" name="distribuidos">
                <br>
                <! archivo csv 2022-1 <input type="submit">
                Archivo de matriculados en el presente semestre<br>
                <input type="file" name="matriculados">
                <br>
                <! archivo csv docente>
                Archivo de docentes para el presente semestre <br>
                <input type="file" name="docentes">
                <br>
              </div>
            <legend class="centertext">Seleccione una acción: </legend>
            <div id="lis">
                <input type="radio" name="eleccioN" value="noTutorados" checked>
                <label for="noTutorados">Alumnos no tutorados en semestre 2022-I</label>
                <br>
                <input type="radio" name="eleccioN" value="tutorados" >
                <label for="tutorados"> Mostrar los nuevos alumnos para  tutoria </label>
                <br>
                <input type="radio" name="eleccioN" value="distribucion" >
                <label for="tutorados">  Generar distribución Alumno x Docentes </label>
            </div>
            <p>
              <input type="submit" name="Bbuscar" value="Mostrar">
              <!input type="submit" name="Bdescarga" value="Descargar">
              <! .. comentario  onclick="lexico()" >
            </p>
            </form>
        </div>
    </div>
</body>
</html>
