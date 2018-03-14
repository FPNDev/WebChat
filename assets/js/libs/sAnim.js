var sAnim = {
    init: function init() {
        this.scroll();
        window.bind('scroll', this.scroll, true, 'sAnim');
        window.bind('resize', this.scroll, true, 'sAnim');
    },

    scroll: function scroll(event) {
        var context = document.body;
        if (event && isElement(event.target)) context = event.target;
        sel('[sAnim]', context).each(function (v) {
            if (sAnim.isVisible(v)) {
                sAnim.transform(v);
            }
        });
    },

    transform: function transform(v) {
        if (!ne(v.a('sAnim'))) return;
        var dur = parseFloat(v.a('sDuration')) || parseFloat(v.a('sDur')) || 1;
        var delay = parseFloat(v.a('sDelay')) || 0;
        var animName = v.a('sAnim').toString();
        v.css('animationDelay', delay + 's');
        v.css('animationDuration', dur + 's');
        v.classList.add('animated');
        v.classList.add(animName);
        v.a('sAnim', null);
        v.a('sDuration', null);
        v.a('sDur', null);
        v.a('sDelay', null);
    },

    getParent: function getParent(el) {
        if (!el) return null;
        return this.gpHelper(el.parentNode);
    },

    gpHelper: function gpHelper(el) {
        if (!el) return null;
        var overflowY = window.getComputedStyle(el).overflowY;
        var isScrollable = overflowY !== 'hidden' && overflowY !== 'visible';

        if (isScrollable && el.scrollHeight > el.clientHeight || el == document.body) return el;

        return this.gpHelper(el.parentNode);
    },

    isVisible: function isVisible(el) {
        var elementRect = el.getBoundingClientRect();
        var parentRects = [];
        var parentSearch = el.parentElement;

        while (parentSearch != null) {
            parentRects.push(parentSearch);
            parentSearch = parentSearch.parentElement;
        }

        var visibleInAllParents = parentRects.every(function (parent) {
            if (getComputedStyle(parent)['overflow'] == 'visible') return true;
            var parentRect = parent.getBoundingClientRect();
            var visiblePixelX = Math.min(elementRect.right, parentRect.right) - Math.max(elementRect.left, parentRect.left);
            var visiblePixelY = Math.min(elementRect.bottom, parentRect.bottom) - Math.max(elementRect.top, parentRect.top);
            var visiblePercentageX = visiblePixelX / elementRect.width * 100;
            var visiblePercentageY = visiblePixelY / elementRect.height * 100;
            if (getComputedStyle(parent)['overflowY'] == 'visible') visiblePercentageY = 100;
            if (getComputedStyle(parent)['overflowX'] == 'visible') visiblePercentageX = 100;
            return visiblePercentageX + 0.01 > 20 && visiblePercentageY + 0.01 > 20;
        });
        return visibleInAllParents && window.innerHeight > elementRect.top + parseInt(el.getAttribute('sOffset') ? el.getAttribute('sOffset') : 0) + Math.min(elementRect.height / 10, 20);
    }
};

init.view('', function () {
    sAnim.init();
}, true);