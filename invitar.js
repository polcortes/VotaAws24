addEventListener('load', () => {
    if (document.getElementById('invitar')) {
        const invitarButt = document.getElementById('invitar');
        const modal = document.getElementById('modal-invitar')

        invitarButt.addEventListener('click', (event) => {
            event.preventDefault();
            modal.showModal();
        })
    }
})