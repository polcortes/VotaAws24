addEventListener('load', () => {
    const submit = document.getElementById('submit-vote')
    const radios = document.querySelectorAll('input[type="radio"]');
    const form = document.getElementById('vote-form')

    submit.addEventListener('click', (e) => {
        e.preventDefault()
        let isValidToVote = false

        for (const radio of radios) {
            if (radio.checked) {
                isValidToVote = true; 
                break;
            }
        }

        if (isValidToVote) {
            form.submit()
        }
        else {
            errorNotification("Tienes que marcar alguna opción para poder votar.")
        }
    })
})