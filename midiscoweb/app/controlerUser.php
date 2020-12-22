<?php
// ------------------------------------------------
// Controlador que realiza la gestión de usuarios
// ------------------------------------------------
include_once 'config.php';
include_once 'modeloUser.php';

/*
 * Inicio Muestra o procesa el formulario (POST)
 */

function  ctlUserInicio(){
    $msg = "";
    $user ="";
    $clave ="";
    if ( $_SERVER['REQUEST_METHOD'] == "POST"){
        if (isset($_POST['user']) && isset($_POST['clave'])){
            $user =$_POST['user'];
            $clave=$_POST['clave'];
            if ( modeloOkUser($user,$clave)){
                $_SESSION['user'] = $user;
                $_SESSION['tipouser'] = modeloObtenerTipo($user);
                if ( $_SESSION['tipouser'] == "Máster"){
                    $_SESSION['modo'] = GESTIONUSUARIOS;
                    header('Location:index.php?orden=VerUsuarios');
                }
                else {
                  // Usuario normal;
                  // PRIMERA VERSIÓN SOLO USUARIOS ADMISTRADORES
                  $msg="Error: Acceso solo permitido a usuarios Administradores.";
                  // $_SESSION['modo'] = GESTIONFICHEROS;
                  // Cambio de modo y redireccion a verficheros
                }
            }
            else {
                $msg="Error: usuario y contraseña no válidos.";
           }  
        }
    }
    
    include_once 'plantilla/facceso.php';
}
//Subir usuarios, ver datos de usuario, estructura variables arriba (user,clave.....)
/* DATOS
1 - Identificador ( 5 a 10 caracteres, no debe existir previamente, solo letras y números) 
2 - Nombre ( Nombre y apellidos 20 caracteres) 
3 - Contraseña ( 8 a 15 caracteres, debe ser segura)
4 - Correo electrónico ( Valor válido de dirección correo, no debe existir previamente)
----------------------------------------------------------------------------------------------
5 y 6 en config.php
5- Tipo de Plan (Básico | Profesional | Premium | Máster)
6- Estado: (Activo | Bloqueado | Inactivo )
*/
/*
function ctlUserAlta(){
    
    
    $user = "";
    $nombre = "";
    $clave = "";
    $correo = "";
    $tipo = "";
    $estado = "";
    
    $msg = "";

    if ( $_SERVER['REQUEST_METHOD'] == "POST"){
        if (isset($_POST['user']) && (isset($_POST['nombre']) && isset($_POST['clave']) && isset($_POST['correo']) && isset($_POST['tipo']) && isset($_POST['estado'])){
            $user =$_POST['user'];
            $nombre =$_POST['nombre'];
            $clave=$_POST['clave'];
            $correo =$_POST['correo'];
            $tipo =$_POST['tipo'];
            $estado =$_POST['estado'];
        }
            //$user aparte
            $requisito = [
                $nombre,
                $clave,
                $correo,
                $tipo,
                $estado
            ]
        
           
                modeloUserGetAll();
                modeloUserAdd($user, $nuevo);
                modeloUserSave();
                $msg = "OK en el alta";
                header('Location:index.php?orden=VerUsuarios');
            } else {
                $msg = "Error en el alta";
            }
            
}
*/
//Borrar usuarios
function ctlUserBorrar(){
    if (isset($_GET['id'])){
         $user = $_GET['id'];
         modeloUserDel($user);
         modeloUserSave();
         header('Location:index.php?orden=VerUsuarios');
    }
}

//Modificar usuarios
function ctlUserModificar(){
    $clave=$_GET['id'];
    $usuariomod =$_SESSION['tusuarios'][$clave];
    
    $newuser=$clave;
    $newclave=$usuariomod[0]; 
    $newnombre=$usuariomod[1];
    $newcorreo=$usuariomod[2];
    $newtipo="";
    $newestado="";
    if( $_SERVER['REQUEST_METHOD'] == "POST"){
      
            $newuser = $_POST['user'];
            $newclave = $_POST['clave'];
            $newnombre = $_POST['nombre'];
            $newcorreo = $_POST['correo'];
            $newtipo = $_POST['tipo'];
            $newestado = $_POST['estado'];
            
            $modificado = [ $newclave, $newnombre, $newcorreo, $newtipo, $newestado];
            
                modeloUserUpdate($newuser, $modificado);
                modeloUserSave();
                if(modeloUserUpdate($newuser, $modificado)){
                    header('Location:index.php?orden=VerUsuarios');
                }
         
    } else{
        include_once 'plantilla/fmodificar.php';
    }
}

//Detalles del usuario
function ctlUserDetalles(){
    $clave=$_GET['id'];
    $detalles = modeloUserGet($clave);
    $nombre=$detalles[1];
    $correo=$detalles[2];
    $tipo=$detalles[3];
    $plan=PLANES[$tipo];
    include_once 'plantilla/fdetalles.php';  
}

// Cierra la sesión y vuelva los datos
function ctlUserCerrar(){
    session_destroy();
    modeloUserSave();
    header('Location:index.php');
}

// Muestro la tabla con los usuario 
function ctlUserVerUsuarios (){
    // Obtengo los datos del modelo
    $usuarios = modeloUserGetAll(); 
    // Invoco la vista 
    include_once 'plantilla/verusuariosp.php';
   
}
