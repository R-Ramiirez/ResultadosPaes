<?php
include("../conexion.php");

// Capturamos el RUT ingresado
$rut = $_GET['rut'];

// Realizamos la consulta a la base de datos
$conex = mysqli_connect("$servername", "$username", "$password", "$database");
$consulta = "SELECT * FROM user WHERE rut = $rut";
$result = mysqli_query($conex, $consulta);

// Verificamos si hay resultados
if ($result->num_rows > 0) {
    $estudiante = $result->fetch_assoc();
}

// Segunda consulta para obtener datos adicionales
$consulta2 = "SELECT * FROM datos
        LEFT JOIN puntajes_lenguaje ON datos.correctas_lenguaje = puntajes_lenguaje.correctas_lenguaje 
        WHERE rut = $rut";
$result2 = mysqli_query($conex, $consulta2);

// Verificamos si hay resultados
if ($result2->num_rows > 0) {
    $estudiante2 = $result2->fetch_assoc();
}

// Tercera consulta para obtener datos adicionales
$consulta3 = "SELECT * FROM datos
        LEFT JOIN puntajes_matematicas ON datos.correctas_matematicas = puntajes_matematicas.correctas_matematicas
        WHERE rut = $rut";
$result3 = mysqli_query($conex, $consulta3);

// Verificamos si hay resultados
if ($result3->num_rows > 0) {
    $estudiante3 = $result3->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Estudiante</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .header {
            position: relative;
            height: 60px; /* Reducido de 100px */
        }
        .logo {
            position: absolute;
            right: 20px;
            max-height: 40px; /* Reducido de 80px */
            width: auto;
            /* Asegura que la imagen mantenga sus proporciones */
            object-fit: contain;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .student-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .student-info h1 {
            color: #000000;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .student-name, .student-rut {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .results-container {
            margin-bottom: 30px;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .results-table th, .results-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .results-table th {
            background-color: #f2f2f2;
        }
        .subject-header {
            font-size: 18px;
            font-weight: bold;
        }
        .score-header {
            font-size: 16px;
            font-weight: bold;
            border-left: 1px solid #ddd;
        }
        .score {
            font-size: 24px;
            font-weight: bold;
            vertical-align: middle;
        }
        .score-1 { color: #007bff; }
        .score-2 { color: #28a745; }
        .data-row td:first-child {
            text-align: left;
        }
        .data-row p {
            display: flex;
            align-items: center;
            margin: 5px 0;
        }
        .data-label {
            font-weight: bold;
            width: 240px;
            text-align: right;
            margin-right: 10px;
            flex-shrink: 0;
        }
        .data-value {
            flex-grow: 1;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="../img/Imagen5.png" alt="Logo de la empresa" class="logo">
    </div>
    <div class="container">
        <div class="student-info">
            <h1>Resultados del Estudiante</h1>
            <p class="student-name"><strong>Nombre:</strong> <?php echo htmlspecialchars($estudiante['nombre_completo']); ?></p>
        </div>
        <div class="results-container">
            <!-- Lenguaje -->
            <table class="results-table">
                <thead>
                    <tr>
                        <th class="subject-header">Lenguaje</th>
                        <th class="score-header">Puntaje</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td>
                            <p><span class="data-label">Puntos Obtenidos:</span><span class="data-value"><?php echo isset($estudiante2['correctas_lenguaje']) ? htmlspecialchars($estudiante2['correctas_lenguaje']) : 'Sin datos'; ?></span></p>
                            <p><span class="data-label">Puntos Posibles:</span><span class="data-value">65</span></p>
                            <p><span class="data-label">Porcentaje de Correctas:</span><span class="data-value"><?php echo number_format(($estudiante2['correctas_lenguaje'] * 100) / 65, 1); ?>%</span></p>
                        </td>
                        <td class="score score-1">
                            <?php echo isset($estudiante2['puntajes_lenguaje']) ? htmlspecialchars($estudiante2['puntajes_lenguaje']) : 'Sin datos'; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- Matemáticas -->
            <table class="results-table">
                <thead>
                    <tr>
                        <th class="subject-header">Matemáticas</th>
                        <th class="score-header">Puntaje</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="data-row">
                        <td>
                            <p><span class="data-label">Puntos Obtenidos:</span><span class="data-value"><?php echo isset($estudiante3['correctas_matematicas']) ? htmlspecialchars($estudiante3['correctas_matematicas']) : 'Sin datos'; ?></span></p>
                            <p><span class="data-label">Puntos Posibles:</span><span class="data-value">65</span></p>
                            <p><span class="data-label">Porcentaje de Correctas:</span><span class="data-value"><?php echo number_format(($estudiante3['correctas_matematicas'] * 100) / 65, 1); ?>%</span></p>
                        </td>
                        <td class="score score-2">
                            <?php echo isset($estudiante3['puntajes_matematicas']) ? htmlspecialchars($estudiante3['puntajes_matematicas']) : 'Sin datos'; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>