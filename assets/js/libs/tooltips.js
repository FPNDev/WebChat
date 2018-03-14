var tooltips = {

	toggle: function toggle(el, ev) {
		var params = arguments.length <= 2 || arguments[2] === undefined ? {} : arguments[2];
		var accidentFix = arguments.length <= 3 || arguments[3] === undefined ? true : arguments[3];

		if (ev) ev.preventDefault();
		if (tooltips.at == el) tooltips.hide(accidentFix);else tooltips.show(el, ev, params, accidentFix);
		return false;
	},

	show: function show(el, ev) {
		var params = arguments.length <= 2 || arguments[2] === undefined ? {} : arguments[2];
		var accidentFix = arguments.length <= 3 || arguments[3] === undefined ? true : arguments[3];

		if (ev) ev.preventDefault();
		tooltips.aa = true;
		if (this.at === el) return false;
		var dOffsets = {
			top: { x: 0, y: -15 },
			bottom: { x: 0, y: 15 },
			left: { x: -15, y: 0 },
			right: { x: 15, y: 0 }
		};
		var arrowOffset = 30;
		if (tooltips.accidentFixTimer) clearTimeout(tooltips.accidentFixTimer);
		tooltips.accidentFixTimer = setTimeout(function() {
			var p = ['tooltip'];
			var mode, am;
			p.push('tt-' + (mode = el.a('tt_mode') || 'top'));
			nu(el.a('tt_small')) && p.push('tt-small');
			ne(el.a('tt_id')) && p.push('tt-id--'+el.a('tt_id'));
			if (['top', 'bottom', 'left', 'right'].indexOf(mode) == -1) return;
			var arrowPos = el.a('tt_aPos') || 'left';
			p.push('tt-arrow--' + arrowPos);
			var contents, selector, s;
			if ((selector = el.a('tt_selector')) && (s = sel(selector, false))) {
				contents = s.innerHTML;
				ne(s.a('id')) && p.push('tt-template--'+s.a('id'));
			} else {
				contents = ce('div');
				var h = el.a('tt_header') ? ce('div', { className: 'tooltip-title', innerHTML: el.a('tt_header') }) : false,
				    b = el.a('tt_text') ? ce('div', { className: 'tooltip-text', innerHTML: el.a('tt_text') }) : false;
				h && contents.appendChild(h);
				b && contents.appendChild(b);
				contents = contents.innerHTML;
				p.push('tt-default');
			}
			p.push('tt-theme-' + (el.a('tt_theme') || 'white'));
			var offsets = { x: 0, y: 0 };
			offsets.x = dOffsets[mode].x + parseFloat(el.a('tt_offsetX') || 0);
			offsets.y = dOffsets[mode].y + parseFloat(el.a('tt_offsetY') || 0);
			if (tooltips.tt) tooltips.tt.remove();
			var tt = ce('div', Object.assign({ className: p.join(' '), innerHTML: contents }, params));
			el.parentNode.appendChild(tt);
			if (['top', 'bottom'].indexOf(mode) !== -1) {
				switch (arrowPos) {
					case 'left':
						offsets.x += centerX(tt) - arrowOffset;
						break;
					case 'right':
						offsets.x += -centerX(tt) + arrowOffset;
						break;
				}
			}
			tooltips.at = el;
			tooltips.tt = tt;
			tt.onmouseenter = function () {
				tooltips.aa = true;
			};
			tt.onmouseleave = el.onmouseleave;
			var coords = { x: 0, y: 0 };
			switch (mode) {
				case 'top':
					coords.x = el.offset().left + centerX(el) - centerX(tt);
					coords.y = el.offset().top - tt.offsetHeight;
					break;
				case 'bottom':
					coords.x = el.offset().left + centerX(el) - centerX(tt);
					coords.y = el.offset().top + el.offsetHeight;
					break;
				case 'left':
					coords.x = el.offset().left - tt.offsetWidth;
					coords.y = el.offset().top + centerY(el) - centerY(tt);
					break;
				case 'right':
					coords.x = el.offset().left + tt.offsetWidth;
					coords.y = el.offset().top + centerY(el) - centerY(tt);
					break;
				default:
					tooltips.aa = false;
					tooltips.tt = false;
					tooltips.at = false;
					tt.remove();
					break;
			}
			if (!tt) return;
			coords.x += offsets.x;
			coords.y += offsets.y;
			tt.css('left', coords.x + 'px');
			tt.css('top', coords.y + 'px');
			tt.classList.add('shown');
			el.off('mouseleave', 'tooltipAccident');
		}, accidentFix ? 200 : 0);
		el.bind('mouseleave', function () {
			clearInterval(tooltips.accidentFixTimer);
			tooltips.aa = false;
			el.off('mouseleave', 'tooltipAccident');
		}, true, 'tooltipAccident');
		return false;
	},

	hide: function hide() {
		var accidentFix = arguments.length <= 0 || arguments[0] === undefined ? true : arguments[0];

		this.aa = false;
		if (accidentFix) setTimeout(function () {
			!tooltips.aa && tooltips.forceHide();
		}, 200);else this.forceHide();
	},

	forceHide: function forceHide() {
		if (this.tt) {
			this.aa = false;
			this.at = false;
			this.tt.classList.remove('shown');
		}
	},
	aa: false, tt: false, at: false, accidentFixTimer: false

};

var panels = {
	init: function init() {
		document.body.bind('click', function (ev) {
			var p = ev.path || (ev.composedPath && ev.composedPath()) || [];
			if (!p.contains(panels.activePanel) && !p.contains(panels.panelClickable) && p[0] && !p[0].queryParent(this.activePanel)) panels.hide();
		}, true, 'panels');
	},
	toggle: function toggle(el, panel, ev) {
		if (this.activePanel && this.activePanel == panel) return panels.hide();else return panels.show(el, panel, ev);
	},
	show: function show(el, panel, ev) {
		if (ev) ev.preventDefault();
		el.classList.add('active');
		panel.css('display', 'block');
		this.activePanel = panel;
		this.panelClickable = el;
		return true;
	},
	hide: function hide() {
		if (this.activePanel) {
			this.activePanel.css('display', 'none');
			this.panelClickable.classList.remove('active');
			this.activePanel = 0;
			this.panelClickable = 0;
		}
		return true;
	}, activePanel: 0, panelClickable: 0
};

init.view('', function () {
	panels.init();
}, true);