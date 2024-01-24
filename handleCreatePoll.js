document.addEventListener('DOMContentLoaded', function () {
    // Mover la función addOption dentro del evento DOMContentLoaded
    function addOption() {
        const optionsContainer = document.getElementById('optionsContainer');
        if (optionsContainer.childElementCount < 100) {
            const optionsContainer = document.getElementById('optionsContainer');
            const newInput = document.createElement('input');
            newInput.type = 'text';
            newInput.name = 'options[]';
            newInput.placeholder = 'Respuesta';
            newInput.required = true;
            optionsContainer.appendChild(newInput);
        } else {
            errorNotification("No puedes tener más de 100 respuestas.");
        }
    }

    // Asociar la función addOption al evento click de un botón, por ejemplo
    const addButton = document.getElementById('addButton');
    if (addButton) {
        addButton.addEventListener('click', addOption);
    }
});