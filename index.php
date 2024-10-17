<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/73b3fda649.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Iniciar Sesión</title>
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
            <h2 class="text-center mb-4">Iniciar Sesión</h2>
            <form id="loginForm">
                <div class="form-group mb-3">
                    <input type="text" name="rut" class="form-control" placeholder="RUT (12.345.678-9)" required>
                </div>
                <div class="form-group mb-3">
                    <div class="password-info">
                        <input type="password" name="password" class="form-control" placeholder="abcd1234" required>
                        <span class="ingreso" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus"
                            data-bs-content="&lt;img src='img/img.png' alt='Explicación de contraseña' class='img-fluid'&gt;"
                            data-bs-html="true" data-bs-custom-class="custom-popover">
                            Primeras dos letras del nombre, primera letra del primer apellido, primera letra del segundo apellido y ultimos 4 dígitos del rut sin el dígito verificador.
                            <i class="fas fa-info-circle"></i>
                        </span>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p>¿No tienes una cuenta? <a href="registro.php" class="btn btn-link">Regístrate aquí</a></p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: '_functions.php',
                    type: 'POST',
                    data: $(this).serialize() + '&accion=acceso_user',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                        } else if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    }
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
            var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    html: true,
                    container: 'body',
                    placement: 'auto',
                    trigger: 'hover focus',
                    boundary: 'viewport'
                })
            })
        });
    </script>
</body>

</html>