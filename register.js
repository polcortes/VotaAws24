$(function(){

    $('h1').after(`
    <form method="POST">
            
    <label for="register_name">Nombre<span class="required">*</span></label>
    <input type="text" name="register_name" id="register_name" placeholder="María" required>
    `);

    $("body").on('focusout', '#register_name', function(){
        var input = $(this).val();
        validacionNombres(input);
    });

    $("body").on('focusout', '#register_email', function(){
        var input = $(this).val();
        validacionMail(input);
    });
    
    $("body").on('focusout', '#register_pass', function(){
        var input = $(this).val();
        var passcom = validacionPass(input);
        console.log(passcom)
        if(passcom){
            if ($("#register_repeat_pass").prop("disabled") && $("#labelrepeat").hasClass("dis")) {
                $("#register_repeat_pass").prop("disabled", false);
                $("#labelrepeat").removeClass("dis");
            }
            $("body").on('focusout', '#register_repeat_pass', function(){
                var input = $("#register_pass").val();
                passIgual(input);
            });
        }else{
            if (!$("#register_repeat_pass").prop("disabled") && !$("#labelrepeat").hasClass("dis")) {
                $("#register_repeat_pass").prop("disabled", true);
                $("#labelrepeat").addClass("dis");
            }
        }
    });

    $("body").on('change', '#register_pais', function(){
            var selectedOption = $(this).val();
            
            if (selectedOption !== "Selecciona tu país") {
                console.log("La opción seleccionada es: " + selectedOption);
                creacionTel();
                crearPrefix($(this).val());
            } else {
                console.log("Por favor, selecciona tu país");
            }
    });

    $("body").on('input', '#register_tel', function(){
        var phoneNumber = $(this).val();
                
        // Eliminar cualquier carácter que no sea un dígito
        phoneNumber = phoneNumber.replace(/\D/g, '');

        // Aplicar el formato XXX-XXX-XXXX
        phoneNumber = phoneNumber.replace(/(\d{3})(\d{3})(\d{3})/, '$1-$2-$3');

        // Actualizar el valor del campo de entrada
        $(this).val(phoneNumber);
    })

    $("body").on('focusout', '#register_tel', function(){
        var input = $(this).val();
        validacionTel(input);
    })

    $("body").on('focusout', '#register_ciudad', function(){
        var input = $(this).val();
        validacionCiudad(input);
    })

    $("body").on('keydown', 'input', function(event) {
    if (event.keyCode === 13) { 
        event.preventDefault();
        $(this).blur(); 

        var borderBottomColor = $(this).css('border-bottom-color');
        console.log(borderBottomColor)
        // Comprobar si el color es rojo
        if (borderBottomColor != 'rgb(255, 37, 37)') {
            var inputs = $('input');
            var currentIndex = inputs.index(this);
            var nextIndex = (currentIndex + 1) % inputs.length;
            inputs[nextIndex].focus();
        }
    }
    });
});

function creacionMail(){
    if ($("#register_email").length === 0) {
        $("#register_name").after(`
            <label for="register_email">Email<span class="required">*</span></label>
            <input type="email" name="register_email" id="register_email" placeholder="tucorreo@gmail.com" required>
        `);
    }
}

function creacionPassword(){
    
    if ($("#register_pass").length === 0) {
        $("#register_email").after(`
            <div class="divisor passdiv">
            <span>
                <label for="register_pass">Contraseña<span class="required">*</span></label>
                <input type="password" name="register_pass" id="register_pass" placeholder="Min. 8 carácteres" min="8" required>
            </span>
            
            <span>
                <label for="register_repeat_pass" id="labelrepeat" class="dis">Repite la contraseña<span class="required">*</span></label>
                <input type="password" name="register_repeat_pass" id="register_repeat_pass" placeholder="Repite la contraseña" required disabled>
            </span>
        </div>
        `);
    }
}

function creacionPais(){
    if ($("#register_pais").length === 0) {
        $(".passdiv").after(`
        <label for="register_pais">País<span class="required">*</span></label>
        <select name="register_pais" id="register_pais" required>
            <option disabled selected>Selecciona tu país</option>
    `);
    
    var jsonInputValue = $("#jsoncountry").val();
    
    if (jsonInputValue) {
        try {
            var jsonData = JSON.parse(jsonInputValue);
    
            // Iterar sobre los datos y agregar opciones al select
            $.each(jsonData, function(index, item) {
                $("#register_pais").append('<option value="' + item.country_name + '">' + item.country_name + '</option>');
            });
            
        } catch (error) {
            console.error("Error al parsear el JSON:", error);
        }
    }
    
    $(".divisor").after(`
        </select>
    `);
    }
}

function creacionTel(){
    if ($("#register_tel").length === 0) {
        $("#register_pais").after(`
        <label for="register_tel">Teléfono<span class="required">*</span></label>
        <div id="tel">
           <input type="text" name="register_prefix" readonly id="prefixtel" value="">
           <input type="tel" name="register_tel" id="register_tel" placeholder="639122561" required>
       </div>
        `)
}
};

function crearPrefix(countrysel){
    var jsonInputValue = $("#jsoncountry").val();
    if (jsonInputValue) {
        try {
            var jsonData = JSON.parse(jsonInputValue);

            // Buscar en el JSON el objeto con el nombre seleccionado
            var objetoEncontrado = jsonData.find(function(item) {
                return item.country_name === countrysel;
            });

            if (objetoEncontrado) {
                $("#prefixtel").val(objetoEncontrado.tel_prefix);

            } else {
                console.log("Nombre no encontrado en el JSON");
            }
        } catch (error) {
            console.error("Error al parsear el JSON:", error);
        }
    } else {
        console.log("La cadena JSON está vacía o no definida.");
    }
}


function creacionCiudad(){
    if ($(".divciudad").length === 0) {
    $("#tel").after(`
    <div class="divisor divciudad">
    <span>
    <label for="register_ciudad">Ciudad<span class="required">*</span></label>
    <input type="text" name="register_ciudad" id="register_ciudad" placeholder="Sídney" required>
    </span>
    
    <span>
    <label for="register_cp">Código postal<span class="required">*</span></label>
    <input type="number" name="register_cp" id="register_cp" placeholder="20852" value="" required readonly>
    </span>
    </div>
    `
    )}
}

function crearSubmit(){
    if ($("#submit").length === 0) {
        $(".divciudad").after(`
        <input type="submit" id="submit" value="Crear cuenta">
        </form>
        `
        )} 
}

function validacionNombres(nombre){
    var regex = /^[a-zA-Z\s]+$/;
    if (nombre.trim() !== '' && regex.test(nombre)) {
        $("#register_name").css('border-bottom', '3px solid var(--verde)');
        creacionMail();
    } else {
        errorNotification('El nombre solo puede contener letras mayúsculas y minúsculas.');
        $("#register_name").css('border-bottom', '3px solid var(--rojo)');
    }
}

function validacionMail(mail){
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;    
    if (emailRegex.test(mail)) {
        $("#register_email").css('border-bottom', '3px solid var(--verde)');
        creacionPassword();
    } else {
        errorNotification('El correo no tiene un formato válido.');
        $("#register_email").css('border-bottom', '3px solid var(--rojo)');
    }
}

function validacionPass(pass){
    var minLength = 8;
    var hasUppercase = /[A-Z]/.test(pass);
    var hasLowercase = /[a-z]/.test(pass);
    var hasNumber = /\d/.test(pass);
    if (pass.length >= minLength && hasUppercase && hasLowercase && hasNumber) {
        $("#register_pass").css('border-bottom', '3px solid var(--verde)');
        return true
    } else {
        errorNotification('La contraseña no tiene un formato válido. Debe contener al menos 8 carácteres, al menos 1 letra mínusculas y mayuscula y al menos un número.');
        $("#register_pass").css('border-bottom', '3px solid var(--rojo)');
        return false
    }
}

function passIgual(pass){
    if (pass == $("#register_repeat_pass").val()){
        
        $("#register_repeat_pass").css('border-bottom', '3px solid var(--verde)');
        creacionPais();
    }else{
        errorNotification("Las contraseñas no coinciden.");
        $("#register_repeat_pass").css('border-bottom', '3px solid var(--rojo)');
    }
}

function validacionTel(tel){
    // if (("" + tel).length !== 9) errorNotification("El telefono no es válido");
    var telefonofin = tel.split('-').join('');
    var regex = /^\d{1,9}$/;

    if (regex.test(telefonofin) && telefonofin.length === 9) {
        $("#register_tel").css('border-bottom', '3px solid var(--verde)');
        creacionCiudad();
    }else{
        errorNotification("El teléfono no es válido.");
        $("#register_tel").css('border-bottom', '3px solid var(--rojo)');
    }
}

function validacionNumeros(){
    var numero = Math.floor(Math.random() * (99999 - 10000 + 1)) + 10000;
    $("#register_cp").val(numero)
}

function validacionCiudad(ciudad){
    var regex = /^[a-zA-Z\s]+$/;
    if (ciudad.trim() !== '' && regex.test(ciudad)) {
        $("#register_ciudad").css('border-bottom', '3px solid var(--verde)');

        validacionNumeros();
        crearSubmit();
    } else {
        errorNotification("La ciudad que has proporcionado no parece válida.");
        $("#register_ciudad").css('border-bottom', '3px solid var(--rojo)');
    }
}

