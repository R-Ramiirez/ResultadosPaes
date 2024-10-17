<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header('Location: index.php');
    exit();
}

require_once('conexion.php');

// Función para formatear el RUT
function formatearRUT($rut): string
{
    $rutLimpio = preg_replace('/[^0-9kK]/', '', $rut);
    $dv = substr($rutLimpio, -1);
    $numero = substr($rutLimpio, 0, -1);
    $numero = number_format($numero, 0, "", ".");
    return $numero . '-' . $dv;
}

// Obtener resultados de estudiantes con puntajes
$query = "SELECT 
    u.id,
    u.rut,
    u.nombre_completo,
    d.correctas_lenguaje,
    d.correctas_matematicas,
    (SELECT puntajes_lenguaje FROM puntajes_lenguaje 
     WHERE correctas_lenguaje = d.correctas_lenguaje
     LIMIT 1) AS puntaje_lenguaje,
    (SELECT puntajes_matematicas FROM puntajes_matematicas 
     WHERE correctas_matematicas = d.correctas_matematicas
     LIMIT 1) AS puntaje_matematicas
FROM 
    user u
INNER JOIN datos d ON u.rut = d.rut
WHERE 
    d.correctas_lenguaje IS NOT NULL 
    OR d.correctas_matematicas IS NOT NULL
ORDER BY u.nombre_completo";

$stmt = $conex->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

// Obtener el nombre del usuario actual
$query_user = "SELECT nombre_completo FROM user WHERE rut = ?";
$stmt_user = $conex->prepare($query_user);
$stmt_user->bind_param("s", $_SESSION['rut']);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$usuario_actual = $result_user->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Estudiantes</title>
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
    <main class="main-content">
        <div class="container">
            <div class="student-info text-center mb-4">
                <h4 class="student-name">Bienvenido <?= htmlspecialchars($usuario_actual['nombre_completo']) ?></h4>
            </div>
            <div class="results-container">
                <div class="results-column">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center">
                                <input class="form-control light-table-filter w-100" data-table="table_id" type="text" placeholder="Buscador de estudiantes">
                            </div>
                        </div>
                    </div>
                    <br>
                    <table class="results-table table_id">
                        <thead>
                            <tr>
                                <th scope="col">RUT</th>
                                <th scope="col">NOMBRE</th>
                                <th scope="col">CORRECTAS LENGUAJE</th>
                                <th scope="col">PUNTAJE LENGUAJE</th>
                                <th scope="col">CORRECTAS MATEMÁTICAS</th>
                                <th scope="col">PUNTAJE MATEMÁTICAS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows >= 1): ?>
                                <?php while ($estudiante = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(formatearRUT($estudiante['rut'])) ?></td>
                                        <td><?= htmlspecialchars($estudiante['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($estudiante['correctas_lenguaje'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($estudiante['puntaje_lenguaje'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($estudiante['correctas_matematicas'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($estudiante['puntaje_matematicas'] ?? 'N/A') ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr class="text-center">
                                    <td colspan="6">No existen registros de estudiantes con resultados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script src="buscador.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>