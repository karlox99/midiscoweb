<?php 
include_once 'config.php';
/* DATOS DE USUARIO
• Identificador ( 5 a 10 caracteres, no debe existir previamente, solo letras y números)
• Contraseña ( 8 a 15 caracteres, debe ser segura)
• Nombre ( Nombre y apellidos del usuario
• Correo electrónico ( Valor válido de dirección correo, no debe existir previamente)
• Tipo de Plan (0-Básico |1-Profesional |2- Premium| 3- Máster)
• Estado: (A-Activo | B-Bloqueado |I-Inactivo )
*/
// Inicializo el modelo 
// Cargo los datos del fichero a la session
function modeloUserInit(){ //OK
    
    /*
    $tusuarios = [ 
         "admin"  => ["12345"      ,"Administrado"   ,"admin@system.com"   ,3,"A"],
         "user01" => ["user01clave","Fernando Pérez" ,"user01@gmailio.com" ,0,"A"],
         "user02" => ["user02clave","Carmen García"  ,"user02@gmailio.com" ,1,"B"],
         "yes33" =>  ["micasa23"   ,"Jesica Rico"    ,"yes33@gmailio.com"  ,2,"I"]
        ];
    */
    if (! isset ($_SESSION['tusuarios'] )){
    $datosjson = @file_get_contents(FILEUSER) or die("ERROR al abrir fichero de usuarios");
    $tusuarios = json_decode($datosjson, true);
    $_SESSION['tusuarios'] = $tusuarios;
   }

      
}

// Comprueba usuario y contraseña (boolean)
function modeloOkUser($user,$clave){
   /* $resu = false;
    if (isset($_SESSION['tusuarios'][$user] ) ){
        $userdat = $_SESSION['tusuarios'][$user];
        $userclave = $userdat[0];
        $resu = ($clave == $userclave);
    }
    return $resu;
    }
    */
    return ($user=='admin') && ($clave =='12345');

 }
 
// Devuelve el plan de usuario (String)
function modeloObtenerTipo($user){ //OK
    //return PLANES[$nplan]; // Máster
    return PLANES[3]; 
}

// Borrar un usuario (boolean)
function modeloUserDel($user){ //OK
    unset($_SESSION['tusuarios'][$user]);
    return true;
    
}
//Comprobamos los requisitos de usuario y contraseña
/*
Datos de Usuario:

1 - Identificador ( 5 a 10 caracteres, no debe existir previamente, solo letras y números) 
2 - Nombre ( Nombre y apellidos 20 caracteres) 
3 - Contraseña ( 8 a 15 caracteres, debe ser segura)
4 - Correo electrónico ( Valor válido de dirección correo, no debe existir previamente)
----------------------------------------------------------------------------------------------
5 y 6 en config.php
5- Tipo de Plan (Básico | Profesional | Premium | Máster)
6- Estado: (Activo | Bloqueado | Inactivo )

*/
function modeloUserComprobar($user, $requisito){
    $login= $user;
    $password = $requisito[0];
    $nombre = $requisito[1];
    $correo = $requisito[2];
    $resu = true;

    if (array_key_exists($login, $_SESSION['tusuarios'])) {
        $resu = false;
    }
    if(strlen($login)<=5 || strlen($login)>=10){
        $resu = false;
    }
    if(strlen($nombre)>20){
        $resu = false;
    }
    if(strlen($password)<=8 || strlen($password)>15){
        $resu =false;
    }
    if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
        $resu = false;
    }
    return $resu;
}
// Añadir un nuevo usuario (boolean)
function modeloUserAdd($userid,$userdat){
    $_SESSION['tusuarios'][$user]=$array;
    return true;
}

// Actualizar un nuevo usuario (boolean)
function modeloUserUpdate ($userid,$userdat){
    $_SESSION['tusuarios'][$user]=$array;
    return true;
}

// Tabla de todos los usuarios para visualizar
function modeloUserGetAll (){
    // Genero lo datos para la vista que no muestra la contraseña ni los códigos de estado o plan
    // sino su traducción a texto
    $tuservista=[];
    foreach ($_SESSION['tusuarios'] as $clave => $datosusuario){
        $tuservista[$clave] = [$datosusuario[1],
                               $datosusuario[2],
                               PLANES[$datosusuario[3]],
                               ESTADOS[$datosusuario[4]]
                               ];
    }
    return $tuservista;
}
// Datos de un usuario para visualizar
function modeloUserGet ($user){
    $datosusuario =$_SESSION['tusuarios'][$user];
    return $datosusuario;
    
}

// Vuelca los datos al fichero
function modeloUserSave(){
    
    $datosjon = json_encode($_SESSION['tusuarios']);
    file_put_contents(FILEUSER, $datosjon) or die ("Error al escribir en el fichero.");
    fclose($fich);
}


/*
 *  Funciones para limpiar la entreda de posibles inyecciones
 */

function limpiarEntrada(string $entrada):string{
    $salida = trim($entrada); // Elimina espacios antes y después de los datos
    $salida = stripslashes($salida); // Elimina backslashes \
    $salida = htmlspecialchars($salida); // Traduce caracteres especiales en entidades HTML
    return $salida;
}
// Función para limpiar todos elementos de un array
function limpiarArrayEntrada(array &$entrada){
 
    foreach ($entrada as $key => $value ) {
        $entrada[$key] = limpiarEntrada($value);
    }
}
