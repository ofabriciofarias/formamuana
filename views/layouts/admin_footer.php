    </div>
</main>

<footer>
    <div class="container">
        <p>© <?= date('Y') ?> Forma Muana - Painel Administrativo</p>
    </div>
</footer>

<script>
// Script genérico para menu mobile responsivo
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.getElementById('mobile-toggle');
    const navLinks = document.getElementById('nav-links');

    if (mobileToggle && navLinks) {
        mobileToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            const icon = mobileToggle.querySelector('i');
            if (icon) {
                if(navLinks.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });

        // Ocultar menu ao clicar num link (para navegação em mesma página ou links rápidos)
        const links = navLinks.querySelectorAll('a:not(.dropbtn)');
        links.forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                const icon = mobileToggle.querySelector('i');
                if(icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    }

    // Auto-Responsive Tables (Cards)
    function checkTableResponsive() {
        document.querySelectorAll('.auto-responsive-cards').forEach(container => {
            const table = container.querySelector('table');
            if (!table) return;
            
            // Remove the class temporarily to check natural width
            container.classList.remove('active');
            
            // If table natural width is greater than container width, it overflows
            if (table.scrollWidth > container.clientWidth) {
                container.classList.add('active');
            }
        });
    }

    // Run on load and resize
    checkTableResponsive();
    window.addEventListener('resize', checkTableResponsive);
});
</script>
</body>
</html>
