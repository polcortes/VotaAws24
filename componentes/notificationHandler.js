const SVGS = {
    error: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#FF2525" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>`,
    successful: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#21FF2A" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" /><path d="M9 12l2 2l4 -4" /></svg>`,
    alert: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#FFCD1D" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
    close: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>`
}

$(function() {
    $("#notification__list > li button.close_notification").click(function() {
        $(this).closest("li").remove();
    });
});

function errorNotification(text) {
    const notifList = $('#notification__list');
    notifList.append(`<li><span>${SVGS.error}<span>${text}</span></span><button class="close_notification">${SVGS.close}</button></li>`); // Está sin probar ni estilizar.
    $("#notification__list > li button.close_notification").click(function() {
        $(this).closest("li").remove();
    })
}

function successfulNotification(text) {
    const notifList = $('#notification__list');
    notifList.append(`<li><span>${SVGS.successful}<span>${text}</span></span><button class="close_notification">${SVGS.close}</button></li>`); // Está sin probar ni estilizar.
    $("#notification__list > li button.close_notification").click(function() {
        $(this).closest("li").remove();
    })
}