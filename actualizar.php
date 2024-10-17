<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header('Location: index.php');
    exit();
}

require_once('conexion.php');

$mensaje = '';
$errores = [];

function validarRUT($rut)
{
    return preg_match('/^[0-9]{7,8}[0-9Kk]{1}$/', $rut);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo_csv"])) {
    $archivo = $_FILES["archivo_csv"];

    if (pathinfo($archivo["name"], PATHINFO_EXTENSION) != "csv") {
        $mensaje = "Error: El archivo debe ser CSV.";
    } else {
        if (($handle = fopen($archivo["tmp_name"], "r")) !== FALSE) {
            fgetcsv($handle, 1000, ";"); // Saltar la primera línea si contiene encabezados

            $conex->begin_transaction();

            try {
                $fila = 2; // Empezamos en 2 porque la primera fila son los encabezados
                while (($datos = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    if (count($datos) != 3) {
                        throw new Exception("La fila $fila no tiene el número correcto de columnas.");
                    }

                    $rut = trim($datos[0]);
                    $correctas_lenguaje = trim($datos[1]);
                    $correctas_matematicas = trim($datos[2]);

                    // Validaciones
                    if (!validarRUT($rut)) {
                        $errores[] = "Fila $fila: El RUT '$rut' no es válido.";
                        $fila++;
                        continue;
                    }
                    if (!is_numeric($correctas_lenguaje) || $correctas_lenguaje < 0) {
                        $errores[] = "Fila $fila: El valor de correctas de lenguaje '$correctas_lenguaje' no es válido.";
                        $fila++;
                        continue;
                    }
                    if (!is_numeric($correctas_matematicas) || $correctas_matematicas < 0) {
                        $errores[] = "Fila $fila: El valor de correctas de matemáticas '$correctas_matematicas' no es válido.";
                        $fila++;
                        continue;
                    }

                    // Insertar o actualizar en la tabla datos
                    $query_datos = "INSERT INTO datos (rut, correctas_lenguaje, correctas_matematicas) 
                                    VALUES (?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE 
                                    correctas_lenguaje = VALUES(correctas_lenguaje), 
                                    correctas_matematicas = VALUES(correctas_matematicas)";

                    $stmt_datos = $conex->prepare($query_datos);
                    $stmt_datos->bind_param("sii", $rut, $correctas_lenguaje, $correctas_matematicas);
                    $stmt_datos->execute();

                    $fila++;
                }

                if (empty($errores)) {
                    $conex->commit();
                    $mensaje = "Datos cargados y/o actualizados exitosamente.";
                } else {
                    $conex->rollback();
                    $mensaje = "Se encontraron errores en algunos datos. No se realizaron cambios en la base de datos.";
                }
            } catch (Exception $e) {
                $conex->rollback();
                $mensaje = "Error al cargar los datos: " . $e->getMessage();
            }

            fclose($handle);
        } else {
            $mensaje = "Error al abrir el archivo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Datos de Estudiantes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="diseño/informa8.css">
</head>

<body>
    <header class="header">
        <nav class="header__menu navbar navbar-expand-lg bg-opacity-10">
            <div class="header__nav-links">
                <a class="header__menu__link" href="admin.php">Inicio</a>
                <a class="header__menu__link" href="actualizar.php">Actualizar Información</a>
                <a class="header__menu__link" href="graficos_puntajes.php">Gráficos Puntajes</a>
                <a class="header__menu__link" href="logout.php">Cerrar Sesión</a>
            </div>
            <div class="header__logo">
                <img src="./img/Imagen5.png" alt="Logo de la empresa">
            </div>
        </nav>
    </header>
    <div class="container mt-5">
        <h2>Subir Archivo CSV de Estudiantes</h2>
        <?php if ($mensaje): ?>
            <div class="alert alert-info" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger" role="alert">
                <h4>Se encontraron los siguientes errores:</h4>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="archivo_csv" class="form-label">Seleccionar archivo CSV (separado por punto y coma, RUT sin guion):</label>
                <input type="file" class="form-control" id="archivo_csv" name="archivo_csv" accept=".csv" required>
            </div>
            <button type="submit" class="btn btn-primary">Subir y Procesar</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>