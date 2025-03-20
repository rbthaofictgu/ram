function inicialitarTomSelect() {
    var selectElement = document.querySelector('.test-select');

    // Check if Tom Select is already initialized
    if (selectElement.tomselect) {
        selectElement.tomselect.destroy(); // Destroy the existing instance
    }

    
    // Initialize Tom Select
    new TomSelect('.test-select', {
        allowEmptyOption: true,
        placeholder: "Seleccione una opción",
        plugins: {
            clear_button: {
                title: 'Limpie selección' // Tooltip on hover
            }
        }
    });

    // Ensure Bootstrap styles are preserved
    selectElement.classList.add('form-control');
}    