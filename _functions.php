<?php
session_start();
require_once("conexion.php");

if (isset($_POST['accion'])) {
    switch ($_POST['accion']) {
        case 'acceso_user':
            acceso_user();
            break;
        case 'registro_user':
            registro_user();
            break;
    }
}

function generar_contraseña($nombre_completo, $rut) {
    // Dividir el nombre completo en partes
    $partes = explode(' ', $nombre_completo);
    
    // Tomar el primer nombre
    $nombre = strtolower($partes[0]);
    
    // Encontrar los apellidos
    $apellidos = array_slice($partes, -2);
    $apellido1 = isset($apellidos[0]) ? strtolower($apellidos[0]) : '';
    $apellido2 = isset($apellidos[1]) ? strtolower($apellidos[1]) : '';

    // Obtener las primeras dos letras del primer nombre
    $primeras_letras = substr($nombre, 0, 2);

    // Obtener la primera letra de cada apellido
    $letra_apellido1 = $apellido1 ? substr($apellido1, 0, 1) : '';
    $letra_apellido2 = $apellido2 ? substr($apellido2, 0, 1) : '';

    // Obtener los últimos 4 dígitos del RUT sin dígito verificador
    $rut_sin_dv = substr($rut, 0, -1);
    $ultimos_digitos = substr($rut_sin_dv, -4);

    // Combinar todo para formar la contraseña
    return $primeras_letras . $letra_apellido1 . $letra_apellido2 . $ultimos_digitos;
}

function registro_user()
{
    global $conex;
    $rut = mysqli_real_escape_string($conex, $_POST['rut']);
    $nombre_completo = mysqli_real_escape_string($conex, $_POST['nombre_completo']);
    $rol = 2; // Rol por defecto para nuevos usuarios

    // Eliminar puntos y guión del RUT
    $rut_limpio = str_replace(array('.', '-'), '', $rut);

    // Generar la contraseña
    $password = generar_contraseña($nombre_completo, $rut_limpio);

    // Verificar si el usuario ya existe
    $consulta = "SELECT * FROM user WHERE rut = ?";
    $stmt = mysqli_prepare($conex, $consulta);
    mysqli_stmt_bind_param($stmt, "s", $rut_limpio);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        echo json_encode(['error' => 'El RUT ya está registrado']);
        exit();
    }

    $consulta = "INSERT INTO user (rut, nombre_completo, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conex, $consulta);
    mysqli_stmt_bind_param($stmt, "sssi", $rut_limpio, $nombre_completo, $password, $rol);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => 'Usuario registrado correctamente', 'password' => $password, 'redirect' => 'index.php']);
    } else {
        echo json_encode(['error' => 'Error al registrar usuario']);
    }

    exit();
}

function acceso_user()
{
    global $conex;
    $rut = mysqli_real_escape_string($conex, $_POST['rut']);
    $password = $_POST['password'];

    // Eliminar puntos y guión del RUT
    $rut_limpio = str_replace(array('.', '-'), '', $rut);

    $consulta = "SELECT * FROM user WHERE rut = ?";
    $stmt = mysqli_prepare($conex, $consulta);
    mysqli_stmt_bind_param($stmt, "s", $rut_limpio);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);

    if (!$usuario) {
        echo json_encode(['error' => 'No Existe Este Usuario']);
    } else {
        if ($password === $usuario['password']) {
            $_SESSION['rut'] = $rut_limpio;
            $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
            $_SESSION['rol'] = $usuario['rol'];

            if ($usuario['rol'] == 1) {
                error_log("Redirigiendo a informacion.php");
                echo json_encode(['redirect' => 'admin.php']);
            } else {
                error_log("Redirigiendo a admin.php");
                echo json_encode(['redirect' => 'informacion.php']);
            }
        } else {
            echo json_encode(['error' => 'Contraseña incorrecta']);
        }
    }
    exit();
}