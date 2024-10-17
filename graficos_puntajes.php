<?php
session_start();
if (!isset($_SESSION['rut'])) {
    header('Location: index.php');
    exit();
}

require_once('conexion.php');

// Consulta para obtener los puntajes promedio
$query = "SELECT 
    AVG(pl.puntajes_lenguaje) as promedio_lenguaje,
    AVG(pm.puntajes_matematicas) as promedio_matematicas
FROM 
    datos d
LEFT JOIN puntajes_lenguaje pl ON d.correctas_lenguaje = pl.correctas_lenguaje
LEFT JOIN puntajes_matematicas pm ON d.correctas_matematicas = pm.correctas_matematicas";

$result = $conex->query($query);
$promedios = $result->fetch_assoc();

// Obtener el nombre del usuario actual
$query_user = "SELECT nombre_completo, rol FROM user WHERE rut = ?";
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
    <title>Gráfico de Puntajes Promedio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="diseño/informa8.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <header class="header">
        <nav class="header__menu navbar navbar-expand-lg bg-opacity-10">
            <div class="header__nav-links">
                <?php if ($usuario_actual['rol'] == 1): ?>
                    <!-- Header para admin -->
                    <a class="header__menu__link" href="admin.php">Inicio</a>
                    <a class="header__menu__link" href="actualizar.php">Actualizar Información</a>
                    <a class="header__menu__link" href="graficos_puntajes.php">Gráficos Puntajes</a>
                    <a class="header__menu__link" href="logout.php">Cerrar Sesión</a>
                <?php else: ?>
                    <!-- Header para estudiante -->
                    <a class="header__menu__link" href="informacion.php">Inicio</a>
                    <a class="header__menu__link" href="graficos_puntajes.php">Gráficos Puntajes</a>
                    <a class="header__menu__link" href="logout.php">Cerrar Sesión</a>
                <?php endif; ?>
            </div>
            <div class="header__logo">
                <img src="./img/Imagen5.png" alt="Logo de la empresa">
            </div>
        </nav>
    </header>
    <main class="main-content">
        <div class="container">
            <div class="chart-container">
                <canvas id="graficoBarra"></canvas>
            </div>
        </div>
    </main>
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
    <script>
        // Datos para el gráfico
        var promedioLenguaje = <?= $promedios['promedio_lenguaje'] ?? 0 ?>;
        var promedioMatematicas = <?= $promedios['promedio_matematicas'] ?? 0 ?>;

        // Gráfico de barras
        var ctxBar = document.getElementById('graficoBarra').getContext('2d');
        var graficoBarra = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Lenguaje', 'Matemáticas'],
                datasets: [{
                    label: 'Puntaje Promedio',
                    data: [promedioLenguaje, promedioMatematicas],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 100,
                        max: 1000,
                        ticks: {
                            stepSize: 100,
                            callback: function(value, index, values) {
                                return value.toFixed(0);
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Puntajes Promedio por Asignatura'
                    },
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/73b3fda649.js" crossorigin="anonymous"></script>
</body>

</html>