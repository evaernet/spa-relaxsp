<?php
session_start();
// Evito cache para que “Atrás” no muestre la sesión anterior
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Defino a qué panel apuntan los botones según rol
$panel = 'panel_cliente.php';
if (!empty($_SESSION['rol'])) {
    if ($_SESSION['rol'] === 'admin')    $panel = 'panel_admin.php';
    if ($_SESSION['rol'] === 'empleado') $panel = 'panel_empleado.php';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RelaxSp</title>
  <link rel="stylesheet" href="styles/style.css">
  <link rel="icon" href="image/favicon.ico" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body class="pagina">

  <!-- INICIO + NAV -->
  <section class="inicio" id="inicio">
    <header>
      <div class="logo-container">
        <img src="image/logo.png" alt="Logo RelaxSp">
        <h1>SENTIRSE BIEN</h1>
      </div>

      <nav>
        <ul>
          <li><a href="#inicio">Inicio</a></li>
          <li><a href="#titulo-nosotros">Sobre nosotros</a></li>
          <li><a href="#servicios">Servicios</a></li>
          <?php if (!empty($_SESSION['rol'])): ?>
            <li><a href="<?= $panel ?>">Mi Panel</a></li>
            <li><a href="logout.php">Salir</a></li>
          <?php else: ?>
            <li><a href="login.php">Ingresar</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </header>

    <img src="image/fondo1.jpg" alt="Imagen de fondo del spa" class="fondo">
    <h1>BIENVENIDOS A SPA SENTIRSE BIEN</h1>
    <p>SANANDO TU CUERPO, MENTE Y ESPÍRITU</p>
  </section>

  <main>
    <!-- SOBRE NOSOTROS -->
    <section id="acerca" class="acerca">
      <div id="imagen_titulo">
        <img src="image/sobreNos.png" alt="Imagen de bienvenida">
        <h2 id="titulo-nosotros">Sobre nosotros</h2>
      </div>
      <div class="imagenes">
        <img src="image/imagen1.jpg" alt="Instalaciones">
        <img src="image/imagen2.jpg" alt="Ambiente relajante">
        <img src="image/imagen3.jpg" alt="Servicios profesionales">
      </div>
      <p>
        <strong>Nos dedicamos a ofrecerte una experiencia de relajación y bienestar inigualable.</strong>
        Nuestro oasis de tranquilidad está diseñado para ayudarte a <strong>desconectar del estrés diario</strong> y
        <strong>rejuvenecer tu cuerpo y mente</strong>. Te invitamos a reservar tu cita y descubrir el verdadero
        significado de la <strong>relajación</strong> en <strong>RelaxSp</strong>. Nuestro equipo está aquí para
        asegurarse de que cada visita sea memorable.
      </p>
    </section>

    <!-- SECCIÓN SERVICIOS -->
    <section id="servicios" class="servicios">
      <h2>Servicios</h2>

      <div class="servicio">
        <img src="image/masajes1.jpg" alt="Masajes terapéuticos">
        <h3>Masajes terapéuticos</h3>
        <p class="precio">$50</p>
        <a href="<?= $panel ?>?servicio=<?= urlencode('Masajes terapéuticos') ?>" class="btn-primary">
          Agendar cita
        </a>
      </div>

      <div class="servicio">
        <img src="image/facial1.jpg" alt="Tratamientos faciales">
        <h3>Tratamientos faciales</h3>
        <p class="precio">$40</p>
        <a href="<?= $panel ?>?servicio=<?= urlencode('Tratamientos faciales') ?>" class="btn-primary">
          Agendar cita
        </a>
      </div>

      <div class="servicio">
        <img src="image/sauna.jpg" alt="Sauna y baño de vapor">
        <h3>Sauna y baño de vapor</h3>
        <p class="precio">$30</p>
        <a href="<?= $panel ?>?servicio=<?= urlencode('Sauna y baño de vapor') ?>" class="btn-primary">
          Agendar cita
        </a>
      </div>

      <div class="servicio">
        <img src="image/yoga.jpg" alt="Yoga y meditación">
        <h3>Yoga y meditación</h3>
        <p class="precio">$20</p>
        <a href="<?= $panel ?>?servicio=<?= urlencode('Yoga y meditación') ?>" class="btn-primary">
          Agendar cita
        </a>
      </div>
    </section>

    <!-- Giftcard -->
    <section class="container my-5">
      <div class="card text-center py-5 shadow border-0" style="background-color: #F5EFE6;">
        <div class="card-body">
          <i class="bi bi-gift-fill" style="font-size: 3rem; color: #FF6666;"></i>
          <h2 class="card-title mt-3 fw-bold" style="color: #333;">Regalá bienestar con nuestra Gift Card</h2>
          <p class="card-text mb-4 text-muted">
            Sorprendé a esa persona especial con una experiencia única en RelaxSp.
          </p>
          <button class="btn btn-danger rounded-pill px-4 py-2 shadow-sm"
                  onclick="agendarCita('Gift Card')"
                  style="background-color: #FF6666; border: none;">
            Comprar Gift Card
          </button>
        </div>
      </div>
    </section>

    <!-- Carrusel de reseñas -->
    <section class="carousel-section py-5">
      <div class="text-center mb-4">
        <i class="bi bi-star-fill" style="font-size: 2rem; color: #ff6666;"></i>
        <h2 class="mt-3 fw-bold" style="color: #ff6666;">Reseñas de Nuestros Clientes</h2>
        <p class="text-muted">Lo que opinan quienes han vivido la experiencia RelaxSp</p>
        <div class="section-separator my-3" style="width: 60px; height: 4px; background-color: #ff6666; margin: 0 auto; border-radius: 2px;"></div>
      </div>

      <div id="carouselExample" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-inner">
          <div class="carousel-item active text-center">
            <img src="image/perfil2.webp" alt="" class="slide-img">
            <div class="mt-3">
              <h5>Masaje Descontracturante</h5>
              <p>Alivio inmediato. La tensión en mi espalda desapareció y me sentí mucho más ligero. ¡Excelente servicio.</p>
              <div>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-half" style="color: gold;"></i>
              </div>
            </div>
          </div>
          <div class="carousel-item text-center">
            <img src="image/c1.jpg" alt="" class="slide-img">
            <div class="mt-3">
              <h5>Masaje Descontracturante</h5>
              <p>Alivio inmediato. La tensión en mi espalda desapareció y me sentí mucho más ligero. ¡Excelente servicio.</p>
              <div>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-half" style="color: gold;"></i>
              </div>
            </div>
          </div>
          <div class="carousel-item text-center">
            <img src="image/perfil3.jpeg" alt="" class="slide-img">
            <div class="mt-3">
              <h5>Masaje Descontracturante</h5>
              <p>Alivio inmediato. La tensión en mi espalda desapareció y me sentí mucho más ligero. ¡Excelente servicio.</p>
              <div>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-half" style="color: gold;"></i>
              </div>
            </div>
          </div>
          <div class="carousel-item text-center">
            <img src="image/perfil4.jpg" alt="" class="slide-img">
            <div class="mt-3">
              <h5>Masaje Descontracturante</h5>
              <p>Alivio inmediato. La tensión en mi espalda desapareció y me sentí mucho más ligero. ¡Excelente servicio.</p>
              <div>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-half" style="color: gold;"></i>
              </div>
            </div>
          </div>
          <div class="carousel-item text-center">
            <img src="image/perfil5.jpg" alt="" class="slide-img">
            <div class="mt-3">
              <h5>Masaje Descontracturante</h5>
              <p>Alivio inmediato. La tensión en mi espalda desapareció y me sentí mucho más ligero. ¡Excelente servicio.</p>
              <div>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-fill" style="color: gold;"></i>
                <i class="bi bi-star-half" style="color: gold;"></i>
              </div>
            </div>
          </div>
        </div>
        <a href="#carouselExample" class="carousel-control-prev" role="button" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Atrás</span>
        </a>
        <a href="#carouselExample" class="carousel-control-next" role="button" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Siguiente</span>
        </a>
      </div>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="py-3" style="background: rgba(232,223,244,0.85);">
    <div class="container">
      <div class="row align-items-start g-2">
        <div class="col-md-4 d-flex flex-column align-items-start">
          <div class="d-flex align-items-center mb-1">
            <img src="image/logo.png" height="40" alt="RelaxSp logo">
            <h4 class="ms-2 mb-0 fs-5">RelaxSp</h4>
          </div>
          <p class="text-muted small mb-0">Tu oasis de relajación</p>
        </div>
        <div class="col-md-4 text-center">
          <h6 class="mb-2">Contacto</h6>
          <p class="mb-1 small"><i class="bi bi-telephone-fill me-1"></i>+3624 229947</p>
          <p class="mb-2 small"><i class="bi bi-envelope-fill me-1"></i>contacto@relaxsp.com</p>
          <div class="social-icons">
            <a href="#" class="me-2 text-primary"><i class="bi bi-facebook fs-5"></i></a>
            <a href="#" class="me-2 text-info"><i class="bi bi-twitter fs-5"></i></a>
            <a href="#" class="me-2 text-danger"><i class="bi bi-instagram fs-5"></i></a>
            <a href="#" class="text-primary"><i class="bi bi-linkedin fs-5"></i></a>
          </div>
        </div>
        <div class="col-md-4 text-center text-md-end">
          <h6 class="mb-2">¿Dónde nos encontrás?</h6>
          <div class="mapa-footer mx-auto mx-md-0">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3540.5826243603065!2d-58.98158552535529!3d-27.45111537633037!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94450cf0c80be0d3%3A0xc9f9278c74810912!2sUTN%20-%20Facultad%20Regional%20Resistencia!5e0!3m2!1ses!2sar!4v1746797588425!5m2!1ses!2sar"
              loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
      <hr class="my-3" style="border-color:#ffb3b3;">
      <div class="text-center small">&copy; 2023 RelaxSp. Todos los derechos reservados.</div>
    </div>
  </footer>

  <!-- Modal -->
  <div id="modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Agendar Cita</h2>
      <form id="appointmentForm">
        <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" required>
        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>
        <label for="phone">Teléfono:</label>
        <input type="tel" id="phone" name="phone" required>
        <label for="service">Servicio:</label>
        <input type="text" id="service" name="service" readonly>
        <button type="submit">Confirmar Cita</button>
      </form>
    </div>
  </div>

    <!-- Botón flotante de WhatsApp: fuera de `.pagina`, justo antes de los scripts -->
  <a
    href="https://wa.me/549XXXXXXXXXX"
    class="whatsapp-float"
    target="_blank"
    aria-label="Chatear por WhatsApp"
  >
    <i class="bi bi-whatsapp"></i>
  </a>


  <script src="scripts/script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
