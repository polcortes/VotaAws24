addEventListener('load', () => {
    const submit = document.getElementById('submit-vote');
    const radios = document.querySelectorAll('input[type="radio"]');
    const form = document.getElementById('vote-form');
    const pass = document.getElementById('pass-check');

    submit?.addEventListener('click', (e) => {
        e.preventDefault();
        let isOptionMarked = false;
        let isPassWritten = false;

        for (const radio of radios) {
            if (radio.checked) {
                isOptionMarked = true;
                break;
            }
        }

        if (pass != null && pass.value.trim().length !== 0) {
            isPassWritten = true;
        } else if (pass == null) {
            isPassWritten = true;
        }

        if (isOptionMarked && isPassWritten) {
            form.submit();
        } else if (!isOptionMarked) {
            errorNotification("Tienes que marcar alguna opción para poder votar.");
        } else {
            errorNotification("Tienes que rellenar el campo de texto con tu contraseña para verificar que eres tu y enviar tu voto.");
        }
    })
})