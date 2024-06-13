$(document).ready(function() {
    menu = `<nav id="nav-mobile">
                <button id="close">
                    <svg xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg>
                </button>
                <ul>
                </ul>
            </nav>`;

    if ($('#nav-mobile').length == 0) {
        $('body').append(menu)
        $('.navbar > ul:first-of-type > li').clone().appendTo('#nav-mobile ul');
    }
    
    $('#toggle-menu').click(function() {
        $('#nav-mobile').css({visibility: 'visible'})
        $('#nav-mobile').addClass('active');
    });

    $('#nav-mobile #close').click(function() {
        $('#nav-mobile').removeClass('active');
        setTimeout(() => {
            $('#nav-mobile').css({visibility: 'hidden'})
        }, 300);
    });
})