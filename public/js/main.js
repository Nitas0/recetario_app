/**
 * main.js
 *
 * Este archivo contiene el código JavaScript principal para la interactividad del frontend
 * de la aplicación "Mi Recetario Online". Se carga en todas las páginas a través del `footer.php`.
 *
 * Lógica principal:
 * Se ejecuta cuando el DOM está completamente cargado (`DOMContentLoaded`).
 *
 * 1.  Lógica del Modal de Confirmación de Eliminación:
 *     - Busca el elemento del modal (`#delete-confirm-modal`) en la página.
 *     - Si existe, configura los siguientes eventos:
 *       a. Asigna un evento `click` a todos los botones con la clase `.btn-delete`.
 *          - Cuando se hace clic en un botón de eliminar, extrae el ID (`data-id`) y el
 *            nombre (`data-name`) de la receta desde los atributos de datos del botón.
 *          - Llama a `openModal()` para mostrar el modal, pasando el ID y el nombre.
 *       b. `openModal()`: Rellena el nombre de la receta en el modal y establece el valor
 *          del campo oculto del formulario de eliminación. Muestra el modal.
 *       c. `closeModal()`: Oculta el modal.
 *       d. Asigna eventos `click` al botón de cerrar (`.close-button`), al botón de cancelar
 *          (`#cancel-delete`) y al fondo del modal para que cualquiera de estas acciones
 *          llame a `closeModal()`.
 *
 * 2.  Lógica de Arrastrar y Soltar (Drag-and-Drop) con SortableJS:
 *     - Busca el contenedor de la cuadrícula de recetas (`#recipes-grid`).
 *     - Si existe, inicializa la biblioteca `SortableJS` en ese elemento.
 *     - `onEnd` (Callback): Esta función se ejecuta automáticamente cuando el usuario
 *       termina de arrastrar y soltar una tarjeta de receta.
 *       a. Obtiene todos los elementos `.recipe-card` en su nuevo orden.
 *       b. Crea un array (`order`) que contiene los `data-id` de las tarjetas en la
 *          secuencia actual.
 *       c. Realiza una llamada `fetch` (AJAX) al endpoint `save_recipe_order.php`.
 *          - Envía el array `order` como un JSON en el cuerpo de una solicitud POST.
 *          - El backend (`save_recipe_order.php`) recibe este orden y actualiza la
 *            base de datos.
 *       d. Procesa la respuesta JSON del servidor. Si hubo un error al guardar, lo
 *          muestra en la consola del navegador.
 */
document.addEventListener('DOMContentLoaded', function() {
    // --- Modal Logic ---
    const modal = document.getElementById('delete-confirm-modal');
    if (modal) {
        const closeModalButton = modal.querySelector('.close-button');
        const cancelDeleteButton = document.getElementById('cancel-delete');
        const modalRecipeName = document.getElementById('modal-recipe-name');
        const modalRecipeId = document.getElementById('modal-recipe-id');

        function openModal(id, name) {
            modalRecipeName.textContent = name;
            modalRecipeId.value = id;
            modal.style.display = 'flex';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                openModal(id, name);
            });
        });

        if (closeModalButton) closeModalButton.addEventListener('click', closeModal);
        if (cancelDeleteButton) cancelDeleteButton.addEventListener('click', closeModal);

        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });
    }

    // --- SortableJS Drag-and-Drop Logic ---
    const grid = document.getElementById('recipes-grid');
    if (grid) {
        new Sortable(grid, {
            animation: 150,
            ghostClass: 'sortable-ghost', // Class name for the drop placeholder
            chosenClass: 'sortable-chosen', // Class name for the chosen item
            dragClass: 'sortable-drag', // Class name for the dragging item
            onEnd: function (evt) {
                const order = [];
                grid.querySelectorAll('.recipe-card').forEach((card, index) => {
                    order.push(card.getAttribute('data-id'));
                });

                // Send the new order to the server
                fetch('save_recipe_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Error saving order:', data.message);
                        // Optionally, revert the order in the UI
                    }
                })
                .catch(error => {
                    console.error('Failed to send order to server:', error);
                });
            }
        });
    }
});