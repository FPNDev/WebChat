var tabs = {
    switch: function(el, ev) {
        if(ev) ev.preventDefault();
        if(!el) return;

        let ac = tabs.ac,
            pw = tabs.pw,
            ap = false;
        ac && !ac.classList.remove('active');
        pw && !pw.classList.remove('active');

        if(tabs.ac === el) {
            tabs.ac = 0;
            tabs.pw = 0;
            return;
        }

        pw = sel('.popup-window', false, el.parentNode);
        pw && !pw.classList.add('active');
        el.classList.add('active');
        tabs.ac = el;
        tabs.pw = pw;
    },
    ac: 0,
    pw: 0
}