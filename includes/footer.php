    </main>
    <footer class="main-footer">
        <div class="footer-container">
            <p>&copy; <?= date('Y') ?> Mi Recetario. Todos los derechos reservados.</p>
            <p>Diseñado con ❤️ por un apasionado de la cocina.</p>
        </div>
    </footer>
    <!-- 
    /**
     * Scripts de JavaScript
     *
     * Este bloque final del cuerpo HTML incluye los scripts de JavaScript necesarios
     * para la funcionalidad interactiva de la aplicación.
     *
     * 1. SortableJS: Biblioteca externa que permite la funcionalidad de arrastrar y soltar (drag and drop).
     *    Se utiliza en el dashboard para que los usuarios puedan reordenar sus recetas de forma visual.
     *    - Fuente: CDN de jsDelivr para obtener la última versión.
     *
     * 2. main.js: Archivo de JavaScript principal y personalizado de la aplicación.
     *    Contiene la lógica del frontend, como la inicialización de SortableJS, manejo de eventos,
     *    confirmaciones de borrado, y otras interacciones del usuario.
     *    - Ubicación: /js/main.js
     */
     -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
