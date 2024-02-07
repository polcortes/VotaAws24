addEventListener('load', () => {
    const form = document.getElementById("change-pass-form")
    const submit = document.querySelector("input[type='submit']")

    const oldPass = document.getElementById('actual-pass')
    const newPass = document.getElementById('new-pass')
    const repPass = document.getElementById('repeat-new-pass')

    submit?.addEventListener('click', (event) => {
        event.preventDefault()
        if (newPass.value !== repPass.value) errorNotification("Las contraseñas no coinciden.")
        else if (oldPass.value === newPass.value) errorNotification("La contraseña vieja y la nueva no pueden coincidir.")
        else form.submit();
    })

    submit.disabled = true
    let oldPassOK = false
    let newPassOK = false
    let repeatPassOK = false

    oldPass.addEventListener("focusout", () => {
        if (oldPass.value.trim().length == 0) {
            oldPassOK = false
        } else {
            oldPassOK = true
        }

        submit.disabled = !(oldPassOK && newPassOK && repeatPassOK);
    })

    newPass.addEventListener("focusout", () => {
        if (validacionPass(newPass.value, newPass)) {
            newPassOK = true
        } else {
            newPassOK = false
        }

        submit.disabled = !(oldPassOK && newPassOK && repeatPassOK);
    })

    repPass.addEventListener("focusout", () => {
        if (validacionPass(repPass.value, repPass)) {
            repeatPassOK = true
        } else {
            repeatPassOK = false
        }

        submit.disabled = !(oldPassOK && newPassOK && repeatPassOK);
    })
})

function validacionPass(pass, element) {
    var minLength = 8;
    var hasUppercase = /[A-Z]/.test(pass);
    var hasLowercase = /[a-z]/.test(pass);
    var hasNumber = /\d/.test(pass);
    if (pass.length >= minLength && hasUppercase && hasLowercase && hasNumber) {
        $("#"+element.id).css('border-bottom', '3px solid var(--verde)');
        return true
    } else {
        errorNotification('La contraseña no tiene un formato válido. Debe contener al menos 8 carácteres, al menos 1 letra mínusculas y mayuscula y al menos un número.');
        $("#"+element.id).css('border-bottom', '3px solid var(--rojo)');
        return false
    }
}