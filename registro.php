<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/73b3fda649.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Registro</title>
    <link rel="stylesheet" type="text/css" href="diseño/inicio5.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include("conexion.php"); ?>
    <div class="container-fluid d-flex justify-content-center align-items-center">
        <div class="login-container">
            <div class="text-center mb-2">
                <img src="img/paes.png" class="rounded-circle" alt="...">
            </div>
            <h2 class="text-center mb-4">Registro</h2>
            <form id="registerForm">
                <div class="mb-3">
                    <input type="text" name="rut" class="form-control" placeholder="RUT (sin puntos ni guión)" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="nombre_completo" class="form-control" placeholder="Nombre Completo" required>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p>¿Ya tienes una cuenta? <a href="index.php" class="btn btn-link">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar la contraseña -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Tu contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tu contraseña es: <strong id="generatedPassword"></strong></p>
                    <p>Por favor, guárdala en un lugar seguro.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="copyPassword">Copiar Contraseña</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#registerForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '_functions.php',
                    type: 'POST',
                    data: $(this).serialize() + '&accion=registro_user',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                        } else if (response.success) {
                            $('#generatedPassword').text(response.password);
                            var modal = new bootstrap.Modal(document.getElementById('passwordModal'));
                            modal.show();
                        }
                    }
                });
            });

            $('#copyPassword').click(function() {
                var password = $('#generatedPassword').text();
                navigator.clipboard.writeText(password).then(function() {
                    alert('Contraseña copiada al portapapeles');
                }, function(err) {
                    console.error('No se pudo copiar la contraseña: ', err);
                });
            });

            $('#passwordModal').on('hidden.bs.modal', function () {
                window.location.href = 'index.php';
            });
        });
    </script>
</body>

</html>