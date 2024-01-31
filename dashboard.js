addEventListener('load', () => {
  if (document.getElementById('conditions_dialog')) {
    const dialog = document.getElementById('conditions_dialog');
    const conditionsCheckbox = document.querySelector("#conditions_dialog input[type='checkbox']")
    const button = document.querySelector("#conditions_dialog form button")
    const form = document.querySelector("#conditions_dialog form")
    
    dialog.showModal();

    conditionsCheckbox.addEventListener('change', () => {
      if (conditionsCheckbox.checked) {
        button.disabled = false
        button.title = ""
      } else {
        button.disabled = true
        button.title = 'Activa el checkbox para poder avanzar.'
      }
    })

    button.addEventListener('click', () => {
      if (conditionsCheckbox.checked) {
        form.submit();
      } else {
        errorNotification("No puedes avanzar sin aceptar nuestros términos y condicones de uso.")
      }
    })
  }
})