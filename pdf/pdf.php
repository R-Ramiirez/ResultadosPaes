<?php
//Configuracion de documento en PDF
ob_start();
require('formatopdf.php');
$html = ob_get_clean();

$imagePath = __DIR__ . '/../img/Imagen5.png';

if (!file_exists($imagePath)) {
    echo "El archivo de imagen no existe en: $imagePath";
    exit;
}

$imageInfo = getimagesize($imagePath);
if ($imageInfo === false) {
    echo "No se puede obtener información de la imagen";
    exit;
}

$src = 'data:' . $imageInfo['mime'] . ';base64,' . base64_encode(file_get_contents($imagePath));

// Insertar la imagen en el HTML
$html = str_replace(
    '<img src="../img/Imagen5.png" alt="Logo de la empresa" class="logo">', 
    '<img src="' . $src . '" alt="Logo de la empresa" class="logo">', 
    $html
);

require '../vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

// Configura el directorio base para las imágenes
$dompdf->setBasePath($_SERVER['DOCUMENT_ROOT']);

$options = $dompdf->getOptions();
$options->set(array('isRemoteEnabled' => true));
$options->set('margin_left', 0);
$options->set('margin_right', 0);
$options->set('margin_top', 10); // Ajustado para dar espacio al logo
$options->set('margin_bottom', 0);
$dompdf->setOptions($options);

ini_set('memory_limit', '256M');

$dompdf->loadHtml($html);
$dompdf->setPaper('letter');
$dompdf->render();
$dompdf->stream("documento.pdf", array("Attachment" => false));