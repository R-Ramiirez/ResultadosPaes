<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header('Location: index.php');
    exit();
}

require_once('conexion.php');

// Función para formatear el RUT
function formatearRUT($rut)
{
    $rutLimpio = preg_replace('/[^0-9kK]/', '', $rut);
    $dv = substr($rutLimpio, -1);
    $numero = substr($rutLimpio, 0, -1);
    $numero = number_format($numero, 0, "", ".");
    return $numero . '-' . $dv;
}

// Obtener información del estudiante
$rut = $_SESSION['rut'];
$query = "SELECT * FROM user WHERE rut = ?";
$stmt = $conex->prepare($query);
$stmt->bind_param("s", $rut);
$stmt->execute();
$result = $stmt->get_result();
$estudiante = $result->fetch_assoc();

// Obtener resultados del estudiante
$rut = $_SESSION['rut'];
$query2 = "SELECT * FROM datos
            LEFT JOIN puntajes_lenguaje ON datos.correctas_lenguaje = puntajes_lenguaje.correctas_lenguaje 
            WHERE rut = ?";
$stmt = $conex->prepare($query2);
$stmt->bind_param("s", $rut);
$stmt->execute();
$result2 = $stmt->get_result();
$estudiante2 = $result2->fetch_assoc();

// Obtener resultados del estudiante
$rut = $_SESSION['rut'];
$query3 = "SELECT * FROM datos
            LEFT JOIN puntajes_matematicas ON datos.correctas_matematicas = puntajes_matematicas.correctas_matematicas
            WHERE rut = ?";
$stmt = $conex->prepare($query3);
$stmt->bind_param("s", $rut);
$stmt->execute();
$result3 = $stmt->get_result();
$estudiante3 = $result3->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Estudiante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="diseño/informa8.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <header class="header">
        <nav class="header__menu navbar navbar-expand-lg bg-opacity-10">
            <div class="header__nav-links">
                <a class="header__menu__link" href="informacion.php">Inicio</a>
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
                <h1>Resultados del Estudiante</h1>
                <p class="student-name"><strong>Nombre:</strong> <?php echo htmlspecialchars($estudiante['nombre_completo']); ?></p>
                <p class="student-name"><strong>Rut:</strong> <?php echo htmlspecialchars(formatearRUT($estudiante['rut'])); ?></p>
            </div>
            <div class="results-container">
                <div class="results-column">
                    <table class="results-table">
                        <tr>
                            <th>Puntos Obtenidos</th>
                            <td class="text-center">
                                <?php
                                if (isset($estudiante2['correctas_lenguaje']) > 0) {
                                ?>
                                    <?php echo htmlspecialchars($estudiante2['correctas_lenguaje']); ?>
                                <?php
                                } else {
                                ?>
                                    Sin datos disponibles
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Puntos Posibles</th>
                            <td class="text-center">60</td>
                        </tr>
                        <tr>
                            <th>Porcentaje de Correctas</th>
                            <td class="text-center">
                                <?php
                                if (isset($estudiante2['correctas_lenguaje']) && $estudiante2['correctas_lenguaje'] > 0) {
                                    $porcentaje1 = number_format((($estudiante2['correctas_lenguaje'] * 100) / 60), 1);
                                    $int_cast1 = (float)$porcentaje1;
                                ?>
                                    <div class="progress bg-light" style="height: 35px;">
                                        <div id="barra-progreso" class="progress-bar bg-success"
                                            style="width: <?php echo $int_cast1; ?>%;"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="<?php echo $int_cast1; ?>%">
                                            <?php if ($int_cast1 > 34) echo $int_cast1 . '%'; ?>
                                        </div>
                                    </div>
                                    <script>
                                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                                        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                                            return new bootstrap.Tooltip(tooltipTriggerEl)
                                        })
                                    </script>
                                <?php
                                } else {
                                    echo "Sin datos disponibles";
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                    <div class="card text-center">
                        <div class="card-header">
                            <h2>Lenguaje</h2>
                        </div>
                        <div class="card-body">
                            <?php
                            if (isset($estudiante2['correctas_lenguaje']) > 0) {
                            ?>
                                <p class="score score-1"><?php echo htmlspecialchars($estudiante2['puntajes_lenguaje']); ?></p>
                            <?php
                            } else {
                            ?>
                                <p class="score score-1">Sin datos disponibles</p>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="results-column">
                    <table class="results-table">
                        <tr>
                            <th>Puntos Obtenidos</th>
                            <td class="text-center">
                                <?php
                                if (isset($estudiante2['correctas_matematicas']) > 0) {
                                ?>
                                    <?php echo htmlspecialchars($estudiante2['correctas_matematicas']); ?>
                                <?php
                                } else {
                                ?>
                                    Sin datos disponibles
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Puntos Posibles</th>
                            <td class="text-center">60</td>
                        </tr>
                        <tr>
                            <th>Porcentaje de Correctas</th>
                            <td class="text-center">
                                <?php
                                if (isset($estudiante2['correctas_matematicas']) && $estudiante2['correctas_matematicas'] > 0) {
                                    $porcentaje1 = number_format((($estudiante2['correctas_matematicas'] * 100) / 60), 1);
                                    $int_cast1 = (float)$porcentaje1;
                                ?>
                                    <div class="progress bg-light" style="height: 35px;">
                                        <div id="barra-progreso" class="progress-bar bg-success"
                                            style="width: <?php echo $int_cast1; ?>%;"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="<?php echo $int_cast1; ?>%">
                                            <?php if ($int_cast1 > 34) echo $int_cast1 . '%'; ?>
                                        </div>
                                    </div>
                                    <script>
                                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                                        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                                            return new bootstrap.Tooltip(tooltipTriggerEl)
                                        })
                                    </script>
                                <?php
                                } else {
                                    echo "Sin datos disponibles";
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                    <div class="card text-center">
                        <div class="card-header">
                            <h2>Matematicas</h2>
                        </div>
                        <div class="card-body">
                            <?php
                            if (isset($estudiante3['puntajes_matematicas']) > 0) {
                                echo "<p class='score score-2'>" . htmlspecialchars($estudiante3['puntajes_matematicas']) . "</p>";
                            } else {
                                echo "<p class='score score-2'>Sin datos disponibles</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mb-5 mt-2">
                <input type="hidden" name="rut" id="rut" value="<?php echo htmlspecialchars($estudiante['rut']); ?>">
                <a class="btn btn-success" target="_blank" href="./pdf/pdf.php?rut=<?php echo htmlspecialchars($estudiante['rut']); ?>">Descargar PDF</a>
            </div>
            <div class="text-center mb-3 mt-2">
                <h5>Para más información respecto a la prueba, contactate con nosotros al correo <a href="mailto:prepara.umag@umag.cl">prepara.umag@umag.cl</a></h5>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h5>Ubícanos en:</h5>
                <p>Jose Nogueira #1332, Punta Arenas, Chile</p>
                <p>Contacto: (+569) 74997771</p>
            </div>
            <div class="footer-section footer-logo">
                <a href="https://admision.umag.cl" target="_blank">
                    <img src="./img/umag.jpeg" class="rounded-circle" alt="Logo de la empresa">
                </a>
            </div>
            <div class="footer-section">
                <h5 class="text-center">¡Síguenos en nuestras redes sociales!</h5>
                <div class="social-icons">
                    <a href="https://web.facebook.com/admision.umag?locale=es_LA"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.tiktok.com/@admision_umag?lang=es" target="_blank"><i class="fab fa-tiktok"></i></a>
                    <a href="https://www.instagram.com/admision_umag/" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="mailto:prepara.umag@umag.cl" target="_blank"><i class="fa-solid fa-envelope"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

</html>