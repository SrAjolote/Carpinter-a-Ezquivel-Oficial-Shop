<?php
session_start();
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // Cambia estos datos según tu preferencia
    $admin_usuario = "EzquivelAdmin";
    $admin_clave = "Ezquivel2415691611+";

    if ($usuario === $admin_usuario && $clave === $admin_clave) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
include '../includes/header.php';
?>
<style>
.bg-admin-login {
    min-height: 100vh;
    background: linear-gradient(120deg, #e7cba0 0%, #b4845c 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}
.admin-login-card {
    background: #fffbeedb;
    border-radius: 22px;
    box-shadow: 0 8px 32px #a9743635;
    padding: 2.5rem 2rem 2rem 2rem;
    margin-top: 40px;
    margin-bottom: 40px;
    animation: adminfadein .9s cubic-bezier(.28,.84,.42,1);
}
@keyframes adminfadein {
    0% { opacity: 0; transform: scale(.95) translateY(40px);}
    100% { opacity: 1; transform: scale(1) translateY(0);}
}
.admin-login-title {
    color: #a97436;
    font-family: 'Merriweather', serif;
    text-align: center;
    font-weight: bold;
    font-size: 2rem;
    margin-bottom: 10px;
    text-shadow: 0 1px 8px #e7cba070;
}
.admin-login-icon {
    font-size: 2.2rem;
    color: #7a5e3a;
    margin-bottom: 8px;
    text-shadow: 0 2px 10px #a9743637;
}
.btn-admin-login {
    background: linear-gradient(90deg, #a97436 60%, #7a5e3a 100%);
    color: #fffbe9;
    border: none;
    font-size: 1.12rem;
    padding: 14px 0;
    border-radius: 24px;
    box-shadow: 0 4px 16px #a9743630;
    font-weight: bold;
    letter-spacing: 1px;
    transition: background .16s, transform .16s;
    text-transform: uppercase;
}
.btn-admin-login:hover {
    background: linear-gradient(90deg, #b89b64 60%, #a97436 100%);
    color: #fffbe9;
    transform: translateY(-2px) scale(1.03);
}
.form-label {
    color: #7a5e3a;
    font-weight: 600;
    font-size: 1.04rem;
}
input.form-control {
    border-radius: 16px;
    border: 1.5px solid #e7cba0;
    background: #fffbe9;
}
.alert-danger {
    border-radius: 14px;
    font-size: 1rem;
    margin-bottom: 18px;
    background: linear-gradient(90deg, #de7979 60%, #e7cba0 100%);
    color: #7a5e3a;
    border: none;
    box-shadow: 0 2px 12px #a9743637;
    text-align: center;
}

/* Estilos para el campo de contraseña con toggle */
.password-field-container {
    position: relative;
}
.password-toggle-btn {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #a97436;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s ease;
}
.password-toggle-btn:hover {
    color: #7a5e3a;
}
.password-toggle-btn:focus {
    outline: none;
    color: #7a5e3a;
}

@media (max-width: 600px) {
    .admin-login-card { padding: 1.5rem 0.6rem 1.2rem 0.6rem; }
    .admin-login-title { font-size: 1.3rem; }
}
</style>
<div class="bg-admin-login">
    <div class="admin-login-card col-11 col-sm-8 col-md-5 col-lg-4">
        <div class="admin-login-title"><span class="admin-login-icon"><i class="fa fa-user-shield"></i></span><br>Acceso Administrador</div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" name="usuario" id="usuario" class="form-control" required autocomplete="username">
            </div>
            <div class="mb-3">
                <label for="clave" class="form-label">Contraseña</label>
                <div class="password-field-container">
                    <input type="password" name="clave" id="clave" class="form-control" required autocomplete="current-password">
                    <button type="button" class="password-toggle-btn" onclick="togglePassword()" aria-label="Mostrar/Ocultar contraseña">
                        <i class="fa fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-admin-login w-100 mt-2"><i class="fa fa-sign-in-alt"></i> Ingresar</button>
        </form>
    </div>
</div>
<script>
// Función para mostrar/ocultar contraseña
function togglePassword() {
    const passwordField = document.getElementById('clave');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.className = 'fa fa-eye-slash';
    } else {
        passwordField.type = 'password';
        toggleIcon.className = 'fa fa-eye';
    }
}

// Bloquear clic derecho y selección de texto
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('selectstart', e => e.preventDefault());

// Bloquear copiar, cortar y pegar
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('cut', e => e.preventDefault());
document.addEventListener('paste', e => e.preventDefault());

// Bloquear teclas para devtools, código fuente, guardar, imprimir, terminal y zoom
document.addEventListener('keydown', function(e) {
    // F12 (DevTools)
    if (e.keyCode === 123) e.preventDefault();

    // Ctrl+Shift+I/J/C/K/L (DevTools/terminal/inspeccionar)
    if (e.ctrlKey && e.shiftKey && ['I','J','C','K','L','i','j','c','k','l'].includes(e.key)) e.preventDefault();

    // Ctrl+U (Ver código fuente)
    if (e.ctrlKey && (e.key === 'u' || e.key === 'U')) e.preventDefault();

    // Ctrl+S (Guardar)
    if (e.ctrlKey && (e.key === 's' || e.key === 'S')) e.preventDefault();

    // Ctrl+P (Imprimir)
    if (e.ctrlKey && (e.key === 'p' || e.key === 'P')) e.preventDefault();

    // Ctrl+(+/-/=) (Zoom in/out/reset)
    if (e.ctrlKey && ['+', '-', '=', '_'].includes(e.key)) e.preventDefault();
});

// Bloquear zoom con scroll del mouse (Ctrl + rueda)
window.addEventListener('wheel', function(e) {
    if (e.ctrlKey) e.preventDefault();
}, { passive: false });

// Prevenir arrastrar elementos (por ejemplo imágenes)
document.addEventListener('dragstart', e => e.preventDefault());
</script>
<!-- Sin footer aquí -->