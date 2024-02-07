addEventListener('load', () => {
    const form = document.getElementById("forgot-form")
    const submit = document.querySelector("input[type='submit']")

    const email = document.getElementById('email-forgot')

    submit?.addEventListener('click', (event) => {
        event.preventDefault()
    })

    submit.disabled = true
    var isMailValid = false

    email.addEventListener("focusout", () => {
        isMailValid = validacionMail(email.value, email)

        submit.disabled = !(isMailValid);
    })
})

function validacionMail(mail, el){
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;    
    if (emailRegex.test(mail)) {
        $("#"+el.id).css('border-bottom', '3px solid var(--verde)');
        creacionPassword();
    } else {
        errorNotification('El correo no tiene un formato válido.');
        $("#"+el.id).css('border-bottom', '3px solid var(--rojo)');
    }
}