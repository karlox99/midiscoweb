<?php
// --------------------------------------------------------------
// Controlador que realiza la gestión de ficheros de un usuario
// ---------------------------------------------------------------
//Rellenar para v2
include_once 'config.php';
include_once 'modeloUser.php';
//Ver como va el modelodb para acceder a la base de datos


function ctlFileVerFicheros(){
    $arrayficheros = [];
    $nfiles=0;
    $tamañototal = 0;
    $directorio = RUTA_FICHEROS."/".$_SESSION['user']; //app/dat en config.php
    if (is_dir($directorio)){
        if ($dh = opendir($directorio)){
            while (($fichero = readdir($dh)) !== false){
                $rutayfichero = $directorio.'/'.$fichero;
                if ( is_file($rutayfichero)){
                    $arrayficheros[$nfiles]['nombre'] = $fichero;
                    $arrayficheros[$nfiles]['tipo']   = mime_content_type($rutayfichero);
                    $tamaño = filesize($rutayfichero);
                    $arrayficheros[$nfiles]['tamaño'] = $tamaño;
                    $arrayficheros[$nfiles]['fecha']  =  date("d/m/Y",filectime ($rutayfichero));
                    $nfiles++;
                    $tamañototal += $tamaño;
                    
                }
            }
            closedir($dh);
        }
       
    }
    include_once 'plantilla/vertablaficheros.php';
}


function ctlFileNuevo(){
   $msg="";
   $ruta = RUTA_FICHEROS. $_SESSION['user'];
   $archivo = (isset($_FILES['archivo'])) ? $_FILES['archivo'] : null;
 
   $nombreArchivo=$_FILES['archivo']['name'];
   $tmpArchivo=$_FILES['archivo']['tmp_name'];

   
   $numFicheros = modeloDatos($ruta);
   $espacioOcupado = modeloDirectorio($ruta);
   $tamañoFichero = $_FILES['archivo']['size']; 
   
   include_once 'plantilla/fnuevo.php';
}





function ctlFileBorrar(){
    $usuario = $_SESSION['user'];
    $nombre= RUTA_FICHEROS."/".$usuario."/".$_GET["id"];
    unlink($nombre);
    header('Location:index.php?operacion=vertablaficheros');
}



function ctlFileRenombrar(){
    if (isset($_GET['id'])){
        $fichero=$_GET['id'];
        $nuevoNombre=$_GET['nombre'];
        if(modeloFileRenombrar($fichero,$nuevoNombre)){
            $msg="Error al renombrar el fichero";
        }
        $ruta = "app/dat/".$_SESSION['user'];
        $ficheros = modeloUserGetFicheros($ruta);
        $numFicheros = modeloDatos($ruta);
        $espacioOcupado = modeloDirectorio($ruta);
        include_once 'plantilla/vertablaficheros.php';
        }
}


function ctlFileCompartir(){
    $fichero = $_GET['file'];
    $usuario = $_SESSION['user'];
    $rutaArchivo= RUTA_FICHEROS."/".$usuario."/".$fichero;  
    
    // Genero la ruta de descarga
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
        $link = "https";
    }
    else{
            $link = "http";
            $link .= "://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
            $link .="?orden=DescargaDirecta&fdirecto=".urlencode($rutaArchivo);
            echo "<script type='text/javascript'>alert('Fichero [$fichero]:. Enlace de descarga:$link');".
                "document.location.href='index.php?operacion=vertablaficheros';</script>";      
    }
}




function ctlFileDescargar(){
    $fichero = $_GET['file'];
    $usuario = $_SESSION['user'];
    $rutaArchivo= RUTA_FICHEROS."/".$usuario."/".$fichero;
    modeloUserUpdate($fichero, $rutaArchivo);
}

