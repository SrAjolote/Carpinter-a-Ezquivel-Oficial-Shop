<?php
include '../includes/header.php';
include '../includes/db.php';

// Verificar conexión a la base de datos
if (!$conexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

$sql = "SELECT * FROM productos ORDER BY id DESC";
$result = $conexion->query($sql);
?>

<main class="container py-5">
    <div class="section-header mb-5">
        <h2 class="text-center text-madera display-4 fw-bold">
            <i class="fa fa-gem pulse-icon"></i> Productos Exclusivos
        </h2>
        <p class="text-center subtitle">Artesanía en madera de la más alta calidad</p>
        <div class="decorative-line mx-auto"></div>
    </div>
    
    <div class="row g-4 justify-content-center productos-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): 
                $imgFile = !empty($row['imagen']) && file_exists('assets/img/'.$row['imagen']) ? 'assets/img/'.htmlspecialchars($row['imagen']) : 'assets/img/noimg.png';
            ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                    <div class="card h-100 w-100 shadow producto-card">
                        <div class="ribbon-wrapper">
                            <div class="ribbon">¡Destacado!</div>
                        </div>
                        <div class="producto-img-bg">
                            <img 
                                src="<?php echo $imgFile; ?>"
                                alt="<?php echo htmlspecialchars($row['nombre']); ?>"
                                class="producto-img card-img-top img-zoom-modal"
                                style="cursor: pointer;"
                                data-img="<?php echo $imgFile; ?>"
                                data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>"
                                data-precio="<?php echo number_format($row['precio'],2); ?>"
                                data-precio-anterior="<?php echo isset($row['precio_anterior']) && $row['precio_anterior'] > $row['precio'] ? number_format($row['precio_anterior'],2) : ''; ?>"
                                data-descripcion="<?php echo htmlspecialchars($row['descripcion']); ?>"
                            >
                            <div class="precio-tag">
                                <span class="precio-value">$<?php echo number_format($row['precio'], 2); ?></span>
                                <?php if (isset($row['precio_anterior']) && $row['precio_anterior'] > $row['precio']): ?>
                                <span class="precio-anterior">$<?php echo number_format($row['precio_anterior'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-madera mb-2"><?php echo htmlspecialchars($row['nombre']); ?></h5>
                            <div class="ratings mb-2">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-half-alt"></i>
                                <small class="text-muted ms-1">(4.5)</small>
                            </div>
                            <p class="card-text text-muted mb-3 flex-grow-1"><?php echo htmlspecialchars($row['descripcion']); ?></p>
                            <div class="stock-indicator mb-2">
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 65%"></div>
                                </div>
                                <small class="text-danger fw-bold"><i class="fa fa-exclamation-circle"></i> ¡Quedan pocas unidades!</small>
                            </div>
                            <form class="add-to-cart-form d-flex gap-2 mt-2 align-items-center w-100" data-product-id="<?php echo $row['id']; ?>">
                                <input type="hidden" name="add" value="<?php echo $row['id']; ?>">
                                <input type="number" name="cantidad" min="1" value="1" class="form-control text-center quantity-input" required>
                                <button type="submit" class="btn btn-carpinteria flex-grow-1 btn-add-cart">
                                    <i class="fa fa-cart-plus"></i> Añadir al Carrito
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-custom text-center fs-5 col-12">
                <i class="fa fa-info-circle fa-lg me-2"></i>
                No hay productos disponibles en este momento. ¡Vuelva pronto!
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal mejorado -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4 modal-beauty">
      <div class="modal-header bg-madera text-white">
        <h5 class="modal-title"><i class="fa fa-check-circle me-2"></i>¡Producto añadido!</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center p-4">
        <div class="success-animation mb-3">
          <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
            <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
          </svg>
        </div>
        <p id="confirmMessage" class="fs-5">El producto se añadió correctamente a tu carrito.</p>
        <p class="text-muted">¿Deseas seguir comprando o ir al carrito?</p>
      </div>
      <div class="modal-footer justify-content-center p-3">
        <button type="button" class="btn btn-outline-madera px-4" data-bs-dismiss="modal">Seguir comprando</button>
        <a href="carrito.php" class="btn btn-madera px-4">Ver carrito <i class="fa fa-arrow-right ms-1"></i></a>
      </div>
    </div>
  </div>
</div>

<!-- Modal para ver imagen y detalles en grande -->
<div class="modal fade" id="productoZoomModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow rounded-4 modal-beauty">
      <div class="modal-header bg-madera text-white">
        <h5 class="modal-title" id="zoomModalTitle"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4 d-flex flex-column flex-md-row align-items-center gap-4">
        <img id="zoomModalImg" src="" alt="" class="img-fluid rounded border shadow-sm mb-3 mb-md-0" style="max-width:380px;max-height:380px;object-fit:contain;background:#fff;" />
        <div>
          <h4 class="mb-2" id="zoomModalNombre"></h4>
          <div class="mb-2 fs-5">
            <span class="fw-bold text-madera" id="zoomModalPrecio"></span>
            <span class="text-decoration-line-through text-danger ms-2 fs-6" id="zoomModalPrecioAnterior"></span>
          </div>
          <p class="mb-2" id="zoomModalDescripcion"></p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Incluye Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome para mejores iconos -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>body, html {
  background: #fffbe9;
  font-family: 'Poppins', sans-serif;
  scroll-behavior: smooth;
}

:root {
  --color-primary: #a97436;
  --color-primary-dark: #7a5e3a;
  --color-primary-light: #c9a875;
  --color-accent: #e25822;
  --color-bg: #fffbe9;
  --color-text-dark: #333;
  --color-text-light: #fffbe9;
  --shadow-sm: 0 2px 10px rgba(169, 116, 54, 0.15);
  --shadow-md: 0 5px 20px rgba(169, 116, 54, 0.2);
  --shadow-lg: 0 10px 30px rgba(169, 116, 54, 0.25);
  --radius-sm: 8px;
  --radius-md: 16px;
  --radius-lg: 24px;
  --transition: all 0.3s ease;
}

.productos-bg {
  background-image: url('assets/img/wood-pattern.png'), linear-gradient(135deg, #fffbe9 70%, #e7cba0 100%);
  background-attachment: fixed;
  min-height: 100vh;
}

.text-madera {
  color: var(--color-primary) !important;
  font-family: 'Merriweather', serif;
  letter-spacing: 1px;
}

.display-4 {
  font-size: 2.5rem;
  font-weight: 700;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
}

.subtitle {
  color: #666;
  font-size: 1.1rem;
  margin-top: -5px;
}

.decorative-line {
  width: 80px;
  height: 4px;
  background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
  margin: 1.5rem auto;
  border-radius: 2px;
}

.productos-container {
  position: relative;
  z-index: 1;
}

.producto-card {
  border-radius: var(--radius-md);
  background: #fff;
  transition: transform .28s, box-shadow .28s;
  border: none;
  overflow: hidden;
  position: relative;
  margin: 0 auto 20px;
  box-shadow: var(--shadow-sm);
}

.producto-card:hover {
  transform: translateY(-12px);
  box-shadow: var(--shadow-lg);
  border: none;
}

.producto-card:hover .producto-img {
  transform: scale(1.08);
}

.producto-img-bg {
  background: linear-gradient(120deg, #f5eed3 0%, #fff8e8 100%);
  padding: 1.2rem .8rem;
  border-bottom: 1px solid #f0e8d0;
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 180px;
  overflow: hidden;
}

.producto-img {
  object-fit: contain;
  border-radius: var(--radius-sm);
  box-shadow: var(--shadow-sm);
  background: #fffbe9;
  margin-bottom: .5rem;
  max-width: 150px !important;
  max-height: 150px !important;
  transition: transform .4s ease-out, box-shadow .3s;
}

.ribbon-wrapper {
  width: 85px;
  height: 88px;
  overflow: hidden;
  position: absolute;
  top: -3px;
  left: -3px;
  z-index: 5;
  display: block;
}

.ribbon {
  width: 120px;
  height: 24px;
  font-size: 0.8rem;
  text-align: center;
  color: #fff;
  font-weight: 600;
  box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.25);
  background: var(--color-accent);
  transform: rotate(-45deg);
  position: absolute;
  top: 20px;
  left: -30px;
  z-index: 5;
}

.precio-tag {
  position: absolute;
  bottom: 10px;
  right: 10px;
  background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);
  color: #fff;
  font-size: 1.15rem;
  border-radius: var(--radius-lg);
  padding: 7px 15px;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  transform: translateY(0);
  animation: float 3s ease-in-out infinite;
}

.precio-value {
  line-height: 1.2;
}

.precio-anterior {
  font-size: 0.8rem;
  text-decoration: line-through;
  opacity: 0.8;
}

.card-body {
  padding: 1.5rem;
  background: #fff;
}

.card-title {
  font-size: 1.2rem;
  margin-bottom: 0.6rem;
  font-weight: 600;
  line-height: 1.3;
  color: var(--color-primary-dark);
}

.card-text {
  font-size: 0.95rem;
  line-height: 1.5;
  color: #555;
}

.ratings {
  color: #ffb100;
}

.stock-indicator {
  margin-top: 10px;
}

.stock-indicator .progress {
  height: 7px;
  border-radius: 10px;
  background: #e9ecef;
}

.stock-indicator .progress-bar {
  border-radius: 10px;
  background: linear-gradient(90deg, #28a745, #80c590);
}

.quantity-input {
  max-width: 70px;
  height: 46px;
  border-radius: var(--radius-sm);
  border: 2px solid #ddd;
  font-weight: 600;
}

.btn-carpinteria, .btn-madera {
  background: linear-gradient(90deg, var(--color-primary) 60%, var(--color-primary-dark) 100%) !important;
  color: var(--color-text-light) !important;
  font-weight: 600;
  border-radius: var(--radius-lg) !important;
  border: none !important;
  text-transform: uppercase;
  font-size: 0.95rem;
  letter-spacing: 1px;
  box-shadow: 0 4px 10px rgba(169, 116, 54, 0.25);
  padding: 12px 20px;
  transition: all .2s;
}

.btn-carpinteria:hover, .btn-madera:hover {
  background: linear-gradient(90deg, var(--color-primary-dark) 0%, var(--color-primary) 100%) !important;
  color: #fff !important;
  transform: translateY(-3px);
  box-shadow: 0 7px 14px rgba(169, 116, 54, 0.3);
}

.btn-carpinteria:active, .btn-madera:active {
  transform: translateY(1px);
  box-shadow: 0 3px 8px rgba(169, 116, 54, 0.2);
}

.btn-outline-madera {
  color: var(--color-primary) !important;
  border: 2px solid var(--color-primary) !important;
  background: transparent !important;
  border-radius: var(--radius-lg) !important;
  font-weight: 500;
  padding: 12px 20px;
  transition: all .2s;
}

.btn-outline-madera:hover {
  background: rgba(169, 116, 54, 0.1) !important;
  transform: translateY(-3px);
}

.btn-add-cart {
  position: relative;
  overflow: hidden;
}

.btn-add-cart:after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 5px;
  height: 5px;
  background: rgba(255, 255, 255, 0.5);
  opacity: 0;
  border-radius: 100%;
  transform: scale(1, 1) translate(-50%);
  transform-origin: 50% 50%;
  transition: all 0.3s;
}

.btn-add-cart:hover:after {
  transform: scale(50, 50) translate(-50%);
  opacity: 0.3;
}

.alert-custom {
  background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
  border-left: 5px solid #dc3545;
  color: #721c24;
  padding: 1rem;
  border-radius: var(--radius-sm);
}

.modal-beauty {
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.bg-madera {
  background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-primary-dark) 100%) !important;
  color: #fff !important;
  border: none;
}

.success-animation {
  width: 80px;
  height: 80px;
  margin: 0 auto;
}

.checkmark {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: block;
  stroke-width: 3;
  stroke: var(--color-primary);
  stroke-miterlimit: 10;
  box-shadow: inset 0px 0px 0px var(--color-primary);
  animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
}

.checkmark__circle {
  stroke-dasharray: 166;
  stroke-dashoffset: 166;
  stroke-width: 3;
  stroke-miterlimit: 10;
  stroke: var(--color-primary);
  fill: none;
  animation: stroke .6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark__check {
  transform-origin: 50% 50%;
  stroke-dasharray: 48;
  stroke-dashoffset: 48;
  animation: stroke .3s cubic-bezier(0.65, 0, 0.45, 1) .8s forwards;
}

@keyframes stroke {
  100% {
    stroke-dashoffset: 0;
  }
}

@keyframes scale {
  0%, 100% {
    transform: none;
  }
  50% {
    transform: scale3d(1.1, 1.1, 1);
  }
}

@keyframes fill {
  100% {
    box-shadow: inset 0px 0px 0px 30px white;
  }
}

.testimonials-section {
  background: linear-gradient(135deg, #f9f5e9 0%, #f0e8d0 100%);
  padding: 2rem;
  border-radius: var(--radius-lg);
  margin-top: 3rem;
}

.testimonial-card {
  background: #fff;
  border-radius: var(--radius-md);
  padding: 1.5rem;
  box-shadow: var(--shadow-sm);
  position: relative;
  text-align: center;
  transition: transform .2s, box-shadow .2s;
}

.testimonial-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-md);
}

.testimonial-avatar {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  overflow: hidden;
  margin: -50px auto 15px;
  border: 4px solid #fff;
  box-shadow: var(--shadow-sm);
}

.testimonial-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.testimonial-text {
  font-style: italic;
  color: #555;
  line-height: 1.6;
  margin-bottom: 0.5rem;
}

.testimonial-author {
  font-weight: 600;
  color: var(--color-primary);
}

.testimonial-stars {
  color: #ffb100;
  margin-top: 0.5rem;
}

.pulse-icon {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes float {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-6px);
  }
  100% {
    transform: translateY(0px);
  }
}

@media (max-width: 575.98px) {
  .producto-img {
    max-width: 100px !important;
    max-height: 100px !important;
  }
  .producto-card {
    min-width: 96vw !important;
  }
  .producto-img-bg {
    min-height: 140px;
  }
  .display-4 {
    font-size: 1.8rem;
  }
}

@media (min-width: 576px) and (max-width: 991.98px) {
  .producto-img {
    max-width: 120px !important;
    max-height: 120px !important;
  }
  .producto-card {
    min-width: 98% !important;
  }
  .display-4 {
    font-size: 2.2rem;
  }
}

@media (min-width: 992px) {
  .producto-img {
    max-width: 150px !important;
    max-height: 150px !important;
  }
  .producto-card {
    min-width: 100%;
  }
}</style>
<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Selecciona todos los formularios de añadir al carrito
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Obtener datos del formulario
            const productId = form.getAttribute('data-product-id');
            const cantidad = form.querySelector('input[name="cantidad"]').value;
            
            // Validar cantidad
            if (cantidad < 1) {
                alert('Por favor, seleccione una cantidad válida');
                return;
            }
            
            // Preparar datos para enviar
            const formData = new FormData();
            formData.append('add', productId);
            formData.append('cantidad', cantidad);
            
            try {
                // Mostrar indicador de carga
                const btnSubmit = form.querySelector('button[type="submit"]');
                const originalText = btnSubmit.innerHTML;
                btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Agregando...';
                btnSubmit.disabled = true;
                
                // Realizar solicitud AJAX
                const response = await fetch('carrito_ajax.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                });
                
                // Verificar si la respuesta es JSON
                let result;
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    result = await response.json();
                } else {
                    // Si no es JSON, manejamos el error
                    const text = await response.text();
                    console.error('Respuesta no JSON:', text);
                    throw new Error('Formato de respuesta no válido');
                }
                
                // Restaurar el botón
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
                
                // Actualizar mensaje en el modal
                const confirmMessage = document.getElementById('confirmMessage');
                if (result.success) {
                    confirmMessage.textContent = '¡Producto añadido correctamente a tu carrito!';
                    confirmMessage.className = 'fs-5 text-success';
                    
                    // Actualizamos el contador del carrito si existe
                    if (document.querySelector('.cart-counter')) {
                        const cartCounter = document.querySelector('.cart-counter');
                        const currentCount = parseInt(cartCounter.textContent || '0');
                        cartCounter.textContent = currentCount + parseInt(cantidad);
                        cartCounter.classList.add('cart-updated');
                        setTimeout(() => cartCounter.classList.remove('cart-updated'), 1000);
                    }
                } else {
                    confirmMessage.textContent = 'Error: ' + (result.message || 'No se pudo añadir el producto.');
                    confirmMessage.className = 'fs-5 text-danger';
                }
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                modal.show();
                
            } catch (error) {
                console.error('Error en la solicitud:', error);
                alert('Error al procesar tu solicitud. Por favor, inténtalo de nuevo.');
                
                // Restaurar botón en caso de error
                const btnSubmit = form.querySelector('button[type="submit"]');
                btnSubmit.innerHTML = '<i class="fa fa-cart-plus"></i> Añadir al Carrito';
                btnSubmit.disabled = false;
            }
        });
    });

    // Animación para botones agregar al carrito
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.classList.add('pulse');
            setTimeout(() => {
                this.classList.remove('pulse');
            }, 1000);
        });
    });
    
    // Modal para mostrar imagen y detalles en grande
    document.querySelectorAll('.img-zoom-modal').forEach(img => {
        img.addEventListener('click', function() {
            document.getElementById('zoomModalImg').src = this.getAttribute('data-img');
            document.getElementById('zoomModalImg').alt = this.getAttribute('data-nombre');
            document.getElementById('zoomModalNombre').textContent = this.getAttribute('data-nombre');
            document.getElementById('zoomModalTitle').textContent = this.getAttribute('data-nombre');
            document.getElementById('zoomModalDescripcion').textContent = this.getAttribute('data-descripcion');
            document.getElementById('zoomModalPrecio').textContent = "$" + this.getAttribute('data-precio');
            let precioAnterior = this.getAttribute('data-precio-anterior');
            document.getElementById('zoomModalPrecioAnterior').textContent = precioAnterior !== '' ? "$" + precioAnterior : '';
            let modalZoom = new bootstrap.Modal(document.getElementById('productoZoomModal'));
            modalZoom.show();
        });
    });
    
    // Depuración - Verificar si el backend responde correctamente
    console.log('Script de carrito cargado correctamente');
});

// Protecciones (bloqueos)
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('selectstart', e => e.preventDefault());
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('cut', e => e.preventDefault());
document.addEventListener('paste', e => e.preventDefault());
document.addEventListener('keydown', function(e) {
    if (e.keyCode === 123) e.preventDefault();
    if (e.ctrlKey && e.shiftKey && ['I','J','C','K','L','i','j','c','k','l'].includes(e.key)) e.preventDefault();
    if (e.ctrlKey && (e.key === 'u' || e.key === 'U')) e.preventDefault();
    if (e.ctrlKey && (e.key === 's' || e.key === 'S')) e.preventDefault();
    if (e.ctrlKey && (e.key === 'p' || e.key === 'P')) e.preventDefault();
    if (e.ctrlKey && ['+', '-', '=', '_'].includes(e.key)) e.preventDefault();
});
window.addEventListener('wheel', function(e) {
    if (e.ctrlKey) e.preventDefault();
}, { passive: false });
document.addEventListener('dragstart', e => e.preventDefault());
</script>
<?php include 'includes/footer.php'; ?>