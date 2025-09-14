<footer class="footer-madera text-white mt-5 p-0 pt-4 pb-2 text-center position-relative">
    <div class="footer-decor"></div>
    <div class="container position-relative z-2">
        <div class="mb-2">
            <img src="/assets/img/logo.jpg" alt="Logo Carpintería Esquivel" class="footer-logo mb-3">
        </div>
        <div class="mb-3">
            <strong>Carpintería Esquivel Oficial</strong><br>
            <span class="footer-slogan">Donde la madera cobra vida</span>
        </div>
        <div class="footer-social mb-3">
            <a href="#" class="footer-social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="footer-social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" class="footer-social-link" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
        </div>
        <small class="footer-copyright">&copy; <?php echo date("Y"); ?> Carpintería Esquivel Oficial. Todos los derechos reservados.</small>
    </div>
</footer>
<style>
.footer-madera {
    background: linear-gradient(90deg, #7a5e3a 0%, #a97436 100%);
    color: #fffbe9;
    box-shadow: 0 -4px 24px #a9743621;
    position: relative;
    overflow: hidden;
    border-top-left-radius: 36px;
    border-top-right-radius: 36px;
}
.footer-decor {
    position: absolute;
    top: -48px;
    left: 0; right: 0;
    height: 70px;
    background: url('/assets/img/wood-footer.svg');
    background-size: cover;
    opacity: 0.28;
    z-index: 1;
}
.footer-logo {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    border: 3px solid #fffbe9;
    background: #fffbe9;
    box-shadow: 0 2px 16px #a9743655;
    object-fit: cover;
    margin-bottom: 8px;
}
.footer-slogan {
    font-size: 1.15rem;
    color: #e7cba0;
    font-family: 'Merriweather', serif;
    letter-spacing: 1.2px;
    display: block;
    margin-top: 2px;
    margin-bottom: 4px;
}
.footer-social {
    margin-bottom: 8px;
}
.footer-social-link {
    color: #fffbe9;
    font-size: 1.5rem;
    margin: 0 10px;
    transition: color 0.18s, transform 0.18s;
    display: inline-block;
}
.footer-social-link:hover {
    color: #b7ffb7;
    transform: scale(1.15) rotate(-5deg);
    text-shadow: 0 2px 12px #fffbe955;
}
.footer-madera small, .footer-madera .footer-copyright {
    color: #fffbe9;
    font-size: 0.95rem;
    opacity: 0.95;
    letter-spacing: 0.5px;
}
@media (max-width: 768px) {
    .footer-logo { width: 44px; height: 44px; }
    .footer-madera { border-top-left-radius: 20px; border-top-right-radius: 20px; }
}
</style>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>