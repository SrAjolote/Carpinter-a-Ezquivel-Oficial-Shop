<?php
include 'includes/header.php';
include 'includes/db.php';

// Verificar conexión a la base de datos
if (!$conexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Consulta optimizada para obtener productos con sus imágenes y stock
$sql = "SELECT p.*, 
        GROUP_CONCAT(
            DISTINCT CONCAT(pi.imagen, '|', pi.orden, '|', pi.es_principal) 
            ORDER BY pi.es_principal DESC, pi.orden ASC 
            SEPARATOR ','
        ) as imagenes_data
        FROM productos p 
        LEFT JOIN producto_imagenes pi ON p.id = pi.producto_id 
        GROUP BY p.id 
        ORDER BY p.id DESC";

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
                // Procesar las imágenes
                $imagenes = [];
                if (!empty($row['imagenes_data'])) {
                    $imagenes_raw = explode(',', $row['imagenes_data']);
                    foreach ($imagenes_raw as $img_data) {
                        $parts = explode('|', $img_data);
                        if (count($parts) >= 3) {
                            $imagenes[] = [
                                'nombre' => $parts[0],
                                'orden' => (int)$parts[1],
                                'es_principal' => (bool)$parts[2]
                            ];
                        }
                    }
                }
                
                // Imagen principal (primera imagen o imagen por defecto)
                $img_principal = 'assets/img/noimg.png';
                if (!empty($imagenes)) {
                    $img_principal = 'assets/img/' . $imagenes[0]['nombre'];
                    if (!file_exists($img_principal)) {
                        $img_principal = 'assets/img/noimg.png';
                    }
                }
                
                // Crear array de todas las imágenes para el carrusel
                $imagenes_carrusel = [];
                foreach ($imagenes as $img) {
                    $ruta_img = 'assets/img/' . $img['nombre'];
                    if (file_exists($ruta_img)) {
                        $imagenes_carrusel[] = $ruta_img;
                    }
                }
                if (empty($imagenes_carrusel)) {
                    $imagenes_carrusel[] = 'assets/img/noimg.png';
                }
            ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                    <div class="card h-100 w-100 shadow producto-card">
                        <div class="ribbon-wrapper">
                            <div class="ribbon">¡Destacado!</div>
                        </div>
                        
                        <!-- Carrusel de imágenes -->
                        <div class="producto-img-container">
                            <?php if (count($imagenes_carrusel) > 1): ?>
                                <div id="carousel-<?php echo $row['id']; ?>" class="carousel slide" data-bs-ride="false">
                                    <div class="carousel-inner">
                                        <?php foreach ($imagenes_carrusel as $index => $imagen): ?>
                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <div class="producto-img-bg">
                                                    <img 
                                                        src="<?php echo $imagen; ?>"
                                                        alt="<?php echo htmlspecialchars($row['nombre']); ?>"
                                                        class="producto-img img-zoom-modal"
                                                        style="cursor: pointer;"
                                                        data-img="<?php echo $imagen; ?>"
                                                        data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>"
                                                        data-precio="<?php echo number_format($row['precio'],2); ?>"
                                                        data-precio-anterior="<?php echo isset($row['precio_anterior']) && $row['precio_anterior'] > $row['precio'] ? number_format($row['precio_anterior'],2) : ''; ?>"
                                                        data-descripcion="<?php echo htmlspecialchars($row['descripcion']); ?>"
                                                        data-imagenes='<?php echo json_encode($imagenes_carrusel); ?>'
                                                        data-producto-id="<?php echo $row['id']; ?>"
                                                    >
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <!-- Controles del carrusel -->
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $row['id']; ?>" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $row['id']; ?>" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                    
                                    <!-- Indicadores -->
                                    <div class="carousel-indicators">
                                        <?php foreach ($imagenes_carrusel as $index => $imagen): ?>
                                            <button type="button" 
                                                    data-bs-target="#carousel-<?php echo $row['id']; ?>" 
                                                    data-bs-slide-to="<?php echo $index; ?>" 
                                                    class="<?php echo $index === 0 ? 'active' : ''; ?>">
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Una sola imagen -->
                                <div class="producto-img-bg">
                                    <img 
                                        src="<?php echo $img_principal; ?>"
                                        alt="<?php echo htmlspecialchars($row['nombre']); ?>"
                                        class="producto-img img-zoom-modal"
                                        style="cursor: pointer;"
                                        data-img="<?php echo $img_principal; ?>"
                                        data-nombre="<?php echo htmlspecialchars($row['nombre']); ?>"
                                        data-precio="<?php echo number_format($row['precio'],2); ?>"
                                        data-precio-anterior="<?php echo isset($row['precio_anterior']) && $row['precio_anterior'] > $row['precio'] ? number_format($row['precio_anterior'],2) : ''; ?>"
                                        data-descripcion="<?php echo htmlspecialchars($row['descripcion']); ?>"
                                        data-imagenes='<?php echo json_encode($imagenes_carrusel); ?>'
                                        data-producto-id="<?php echo $row['id']; ?>"
                                        data-stock="<?php echo $stock; ?>"
                                        data-stock="<?php echo $stock; ?>"
                                    >
                                </div>
                            <?php endif; ?>
                            
                            <!-- Tag de precio -->
                            <div class="precio-tag">
                                <span class="precio-value">$<?php echo number_format($row['precio'], 2); ?></span>
                                <?php if (isset($row['precio_anterior']) && $row['precio_anterior'] > $row['precio']): ?>
                                <span class="precio-anterior">$<?php echo number_format($row['precio_anterior'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Contador de imágenes -->
                            <?php if (count($imagenes_carrusel) > 1): ?>
                                <div class="images-count">
                                    <i class="fa fa-images"></i> <?php echo count($imagenes_carrusel); ?>
                                </div>
                            <?php endif; ?>
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
                            <?php 
                            $stock = $row['stock'] ?? 0;
                            $stock_percentage = min(100, ($stock / 20) * 100); // Asumiendo stock máximo de 20 para el 100%
                            ?>
                            <div class="stock-indicator mb-2">
                                <div class="progress">
                                    <div class="progress-bar <?php echo $stock <= 5 ? 'bg-danger' : ($stock <= 10 ? 'bg-warning' : 'bg-success'); ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $stock_percentage; ?>%"></div>
                                </div>
                                <small class="<?php echo $stock == 0 ? 'text-danger' : ($stock <= 5 ? 'text-warning' : 'text-success'); ?> fw-bold">
                                    <i class="fa fa-<?php echo $stock == 0 ? 'times-circle' : ($stock <= 5 ? 'exclamation-triangle' : 'check-circle'); ?>"></i> 
                                    <?php 
                                    if ($stock == 0) {
                                        echo "¡Agotado!";
                                    } elseif ($stock <= 5) {
                                        echo "¡Quedan solo $stock unidades!";
                                    } else {
                                        echo "Stock disponible ($stock unidades)";
                                    }
                                    ?>
                                </small>
                            </div>
                            <?php if ($stock > 0): ?>
                            <form class="add-to-cart-form d-flex gap-2 mt-2 align-items-center w-100" data-product-id="<?php echo $row['id']; ?>">
                                <input type="hidden" name="add" value="<?php echo $row['id']; ?>">
                                <input type="number" name="cantidad" min="1" max="<?php echo $stock; ?>" value="1" class="form-control text-center quantity-input" required>
                                <button type="submit" class="btn btn-carpinteria flex-grow-1 btn-add-cart">
                                    <i class="fa fa-cart-plus"></i> Añadir al Carrito
                                </button>
                            </form>
                            <?php else: ?>
                            <div class="d-flex gap-2 mt-2 align-items-center w-100">
                                <button type="button" class="btn btn-secondary flex-grow-1" disabled>
                                    <i class="fa fa-times-circle"></i> Agotado
                                </button>
                            </div>
                            <?php endif; ?>
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

<!-- Modal para ver imagen y detalles en grande con galería -->
<div class="modal fade" id="productoZoomModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow rounded-4 modal-beauty">
      <div class="modal-header bg-madera text-white">
        <h5 class="modal-title" id="zoomModalTitle"></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-4">
        <div class="row">
          <!-- Galería de imágenes -->
          <div class="col-md-8">
            <div id="modalCarousel" class="carousel slide" data-bs-ride="false">
              <div class="carousel-inner" id="modalCarouselInner">
                <!-- Las imágenes se cargarán dinámicamente -->
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#modalCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#modalCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
              </button>
            </div>
            
            <!-- Thumbnails -->
            <div class="thumbnails-container mt-3" id="thumbnailsContainer">
              <!-- Los thumbnails se cargarán dinámicamente -->
            </div>
          </div>
          
          <!-- Información del producto -->
          <div class="col-md-4">
            <h4 class="mb-3" id="zoomModalNombre"></h4>
            <div class="mb-3 fs-4">
              <span class="fw-bold text-madera" id="zoomModalPrecio"></span>
              <span class="text-decoration-line-through text-danger ms-2 fs-5" id="zoomModalPrecioAnterior"></span>
            </div>
            <div class="ratings mb-3">
              <i class="fa fa-star text-warning"></i>
              <i class="fa fa-star text-warning"></i>
              <i class="fa fa-star text-warning"></i>
              <i class="fa fa-star text-warning"></i>
              <i class="fa fa-star-half-alt text-warning"></i>
              <small class="text-muted ms-1">(4.5)</small>
            </div>
            <p class="mb-3" id="zoomModalDescripcion"></p>
            <div class="stock-indicator mb-3" id="zoomModalStock">
              <!-- El stock se actualizará dinámicamente -->
            </div>
            <form class="add-to-cart-form-modal d-flex gap-2 mt-3 align-items-center" id="modalCartForm">
              <input type="hidden" name="add" id="modalProductId">
              <input type="number" name="cantidad" min="1" value="1" class="form-control text-center quantity-input" id="modalQuantityInput" required>
              <button type="submit" class="btn btn-madera flex-grow-1" id="modalAddToCartBtn">
                <i class="fa fa-cart-plus"></i> Añadir al Carrito
              </button>
            </form>
          </div>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Manejar modal de zoom con stock
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('img-zoom-modal')) {
        const img = e.target;
        const modal = new bootstrap.Modal(document.getElementById('productoZoomModal'));
        
        // Llenar datos del modal
        document.getElementById('zoomModalTitle').textContent = img.dataset.nombre;
        document.getElementById('zoomModalNombre').textContent = img.dataset.nombre;
        document.getElementById('zoomModalPrecio').textContent = '$' + img.dataset.precio;
        document.getElementById('zoomModalPrecioAnterior').textContent = img.dataset.precioAnterior ? '$' + img.dataset.precioAnterior : '';
        document.getElementById('zoomModalDescripcion').textContent = img.dataset.descripcion;
        document.getElementById('modalProductId').value = img.dataset.productoId;
        
        // Manejar stock
        const stock = parseInt(img.dataset.stock) || 0;
        const stockContainer = document.getElementById('zoomModalStock');
        const quantityInput = document.getElementById('modalQuantityInput');
        const addToCartBtn = document.getElementById('modalAddToCartBtn');
        
        // Actualizar indicador de stock
        const stockPercentage = Math.min(100, (stock / 20) * 100);
        let stockClass = stock <= 5 ? 'bg-danger' : (stock <= 10 ? 'bg-warning' : 'bg-success');
        let textClass = stock == 0 ? 'text-danger' : (stock <= 5 ? 'text-warning' : 'text-success');
        let icon = stock == 0 ? 'times-circle' : (stock <= 5 ? 'exclamation-triangle' : 'check-circle');
        let message = stock == 0 ? '¡Agotado!' : (stock <= 5 ? `¡Quedan solo ${stock} unidades!` : `Stock disponible (${stock} unidades)`);
        
        stockContainer.innerHTML = `
            <div class="progress">
                <div class="progress-bar ${stockClass}" role="progressbar" style="width: ${stockPercentage}%"></div>
            </div>
            <small class="${textClass} fw-bold">
                <i class="fa fa-${icon}"></i> ${message}
            </small>
        `;
        
        // Configurar input de cantidad y botón
        if (stock > 0) {
            quantityInput.max = stock;
            quantityInput.disabled = false;
            addToCartBtn.disabled = false;
            addToCartBtn.innerHTML = '<i class="fa fa-cart-plus"></i> Añadir al Carrito';
            addToCartBtn.className = 'btn btn-madera flex-grow-1';
        } else {
            quantityInput.disabled = true;
            addToCartBtn.disabled = true;
            addToCartBtn.innerHTML = '<i class="fa fa-times-circle"></i> Agotado';
            addToCartBtn.className = 'btn btn-secondary flex-grow-1';
        }
        
        // Cargar imágenes en el carrusel
        const imagenes = JSON.parse(img.dataset.imagenes);
        const carouselInner = document.getElementById('modalCarouselInner');
        const thumbnailsContainer = document.getElementById('thumbnailsContainer');
        
        carouselInner.innerHTML = '';
        thumbnailsContainer.innerHTML = '';
        
        imagenes.forEach((imagen, index) => {
            // Slide del carrusel
            const slide = document.createElement('div');
            slide.className = `carousel-item ${index === 0 ? 'active' : ''}`;
            slide.innerHTML = `<img src="${imagen}" alt="Imagen ${index + 1}" class="d-block w-100">`;
            carouselInner.appendChild(slide);
            
            // Thumbnail
            const thumbnail = document.createElement('div');
            thumbnail.className = `thumbnail-item ${index === 0 ? 'active' : ''}`;
            thumbnail.innerHTML = `<img src="${imagen}" alt="Thumbnail ${index + 1}">`;
            thumbnail.addEventListener('click', () => {
                // Cambiar slide activo
                document.querySelectorAll('#modalCarousel .carousel-item').forEach((item, i) => {
                    item.classList.toggle('active', i === index);
                });
                // Cambiar thumbnail activo
                document.querySelectorAll('.thumbnail-item').forEach((item, i) => {
                    item.classList.toggle('active', i === index);
                });
            });
            thumbnailsContainer.appendChild(thumbnail);
        });
        
        modal.show();
    }
});

// Manejar formularios de agregar al carrito
document.addEventListener('submit', function(e) {
    if (e.target.classList.contains('add-to-cart-form') || e.target.classList.contains('add-to-cart-form-modal')) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Mostrar loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Agregando...';
        
        // Enviar petición AJAX
        fetch('carrito_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar modal de éxito
                const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                document.getElementById('confirmMessage').textContent = 
                    `${data.product_name} agregado al carrito (${data.quantity_added} unidad${data.quantity_added > 1 ? 'es' : ''})`;
                modal.show();
                
                // Actualizar contador del carrito si existe
                const cartCounter = document.querySelector('.cart-counter');
                if (cartCounter) {
                    cartCounter.textContent = data.cart_count;
                }
                
                // Cerrar modal de producto si está abierto
                const productModal = bootstrap.Modal.getInstance(document.getElementById('productoZoomModal'));
                if (productModal) {
                    productModal.hide();
                }
                
            } else {
                // Mostrar error
                alert(data.message || 'Error al agregar producto al carrito');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión. Por favor, inténtalo de nuevo.');
        })
        .finally(() => {
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    }
});
</script>

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

.producto-img-container {
  position: relative;
  overflow: hidden;
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

.images-count {
  position: absolute;
  bottom: 10px;
  left: 10px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  font-size: 0.8rem;
  padding: 4px 8px;
  border-radius: 12px;
  font-weight: 500;
  z-index: 3;
}

/* Estilos para el carrusel en las tarjetas */
.carousel-control-prev,
.carousel-control-next {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.5);
  top: 50%;
  transform: translateY(-50%);
  border: none;
  opacity: 0;
  transition: opacity 0.3s;
}

.producto-card:hover .carousel-control-prev,
.producto-card:hover .carousel-control-next {
  opacity: 1;
}

.carousel-control-prev {
  left: 10px;
}

.carousel-control-next {
  right: 10px;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  width: 15px;
  height: 15px;
}

.carousel-indicators {
  bottom: 5px;
  margin-bottom: 0;
}

.carousel-indicators button {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  margin: 0 2px;
  opacity: 0.5;
}

.carousel-indicators button.active {
  opacity: 1;
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

/* Estilos para el modal de zoom */
#modalCarousel .carousel-inner img {
  width: 100%;
  height: 400px;
  object-fit: contain;
  background: #f8f9fa;
  border-radius: 12px;
}

.thumbnails-container {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding: 10px 0;
}

.thumbnail-item {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.3s;
  flex-shrink: 0;
}

.thumbnail-item:hover,
.thumbnail-item.active {
  border-color: var(--color-primary);
  transform: scale(1.05);
}

.thumbnail-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 6px;
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
  
  #modalCarousel .carousel-inner img {
    height: 250px;
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
  
  #modalCarousel .carousel-inner img {
    height: 300px;
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