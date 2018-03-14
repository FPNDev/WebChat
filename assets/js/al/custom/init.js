init.view('', function () {
    window.onresize = onBodyResize;
    scrollElement.onscroll = onBodyScroll;
}, true);

init.view('', function () {
    tplinit();
    sel('#modal_wrap', false).bind('click', function (ev) {
        var p = ev.path || (ev.composedPath && ev.composedPath()) || [];
        if (p[0] == this) modal.hide();
    }, 'modal');
}, true);

function onBodyResize() {
    if (modal.active) {
        // modal resize
        if (sel('#modal_wrap > *', false).offsetHeight > window.innerHeight) {
            sel('#modal_wrap', false).css('align-items', 'flex-start');
        } else sel('#modal_wrap', false).css('align-items', '');

        if (sel('#modal_wrap > *', false).offsetWidth > window.innerWidth) {
            sel('#modal_wrap', false).css('justify-content', 'flex-start');
        } else sel('#modal_wrap', false).css('justify-content', '');
    }
}

function onBodyScroll() {}