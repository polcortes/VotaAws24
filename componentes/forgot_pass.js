$(document).ready(function() {
    $('#forgot-form > input[type="submit"]').on('click', function(e) {
        var email = $('#email-forgot').val();

        // Expresión regular para validar el correo electrónico
        var emailRegex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;

        if (!emailRegex.test(email)) {
            alertNotification('No hemos encontrado ningún usuario con ese token. Por favor, inténtalo de nuevo.');
            e.preventDefault();
            return false;
        }

        return true;
    });

    $('#change-password-form > input[type="submit"]').on('click', function(e) {
        var password = $('#pass').val();
        var passwordConfirm = $('#pass-confirm').val();

        // Expresión regular para validar la contraseña
        // Debe contener al menos una letra minúscula, una letra mayúscula, un número y un carácter especial
        // y debe tener al menos 8 caracteres de longitud
        var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!passwordRegex.test(password)) {
            alertNotification('La contraseña debe tener al menos 8 caracteres, incluyendo una letra minúscula, una letra mayúscula, un número y un carácter especial.');
            e.preventDefault();
            return false;
        }

        if (password !== passwordConfirm) {
            alertNotification('Las contraseñas no coinciden.');
            e.preventDefault();
            return false;
        }

        return true;
    });
});