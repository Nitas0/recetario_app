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