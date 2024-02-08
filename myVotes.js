$(function() {
    const buttons = $(".mostrar-ocultar-respuesta")
    const options = $(".option")

    options.slideUp(0)

    buttons.click(function() {
        $(this).siblings(".option").slideToggle()
        $(this).text(($(this).text() === "Mostrar respuesta" ? "Esconder respuesta" : "Mostrar respuesta"))
    })
})