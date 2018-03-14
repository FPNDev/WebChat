var list = {
	init: function init(apiMethod, pageSize, parentNode, tpl) {
		if (!tpl || !parentNode || !pageSize || !apiMethod) return false;
		this.lists.push({
			parentNode: parentNode,
			pageSize: pageSize,
			apiMethod: apiMethod,
			tpl: tpl,
			offset: 0,
			ended: 0,
			loading: false,
			sortType: '',
			sortDirection: 'DESC',
			search: {}
		});
		list.load(this.lists.length - 1);
		var s = sel('[data-sort]', parentNode);
		s.each(function(ss) {
			ss.a('data-id', list.lists.length - 1);
			ss.onclick = function(event) {
				return list.sort(ss, event);
			}
		});
		var se = sel('[data-search]', parentNode);
		se.each(function(ss) {
			ss.a('data-id', list.lists.length - 1);
			ss.bind('inputend', function(event) {
				return list.search(ss);
			});
		});
		if (!this.evListens) {
			window.bind('scroll', function (ev) {
				list.lists.each(function (l, k) {
					list.check(l, k);
				});
			}, true, 'lists');
			this.evListens = true;
		}
	},
	check: function check(l, k) {
		if (l.loading) return;
		var br = l.parentNode.getBoundingClientRect();
		if ((-1 * br.top + window.innerHeight) / l.parentNode.offsetHeight * 100 >= 100) list.load(k);
	},
	load: function load(id) {
		if (!ne(this.lists[id])) return false;
		var l = this.lists[id];
		if (l.ended) return true;
		if (l.loading) return true;
		l.loading = true;
		var reqObj = {
			pageSize: l.pageSize,
			offset: l.offset,
			sortType: l.sortType,
			sortDirection: l.sortDirection,
		};
		for(var k in l.search) {
			reqObj['search['+k+']'] = l.search[k];
		}
		ajax.json('/method/' + l.apiMethod + '.php', reqObj, function (data) {
			if (ne(data.error) && nu(data.error.error_msg)) return notifier.message(2, data.error.error_msg);
			if (ne(data.warning) && nu(data.warning.warning_msg)) return notifier.message(2, data.warning.warning_msg);
			if (data.success) {
				var e = data.entries;
				l.offset += e.length;
				l.ended = data.ended;
				l.loading = false;
				e.each(function (el) {
					el = ce('div', { innerHTML: fpntpl(l.tpl, el) }).firstElementChild;
					el.a('data-list', true);
					l.parentNode.appendChild(el);
				});
				if(nu(sAnim)) sAnim.scroll();
				list.check(l, id);
			}
		});

		return true;
	},
	sort: function(el, ev) {
		if(ev) ev.preventDefault();
		var id = parseInt(el.a('data-id')),
			l = this.lists[id],
			field = el.a('data-field');
		l.ended = false;
		l.offset =  0;
		if(l.sortType == field) l.sortDirection = l.sortDirection == 'ASC' ? 'DESC' : 'ASC';
		else {
			l.sortType = field;
			l.sortDirection = 'DESC';
		}
		list.arrow(el, l, ev);
		sel('[data-list="true"]', l.parentNode).each(function(v) {
			v.remove()
		});
		list.check(l, id);
		return true;
	},
	search: function(el) {
		var id = parseInt(el.a('data-id')),
			l = this.lists[id],
			field = el.a('name');
		l.search[field] = el.value;
		l.ended = false;
		l.offset =  0;
		sel('[data-list="true"]', l.parentNode).each(function(v) {
			v.remove()
		});
		list.check(l, id);
	},
	arrow: function(el, l, ev) {
		sel('[data-sort]', l.parentNode).each(function(v) {
			var s = sel('i[data-arrow]', v, false);
			s && s.remove();
		});
		arEl = ce('i', { className: 'material-icons' });
		arEl.a('data-arrow', 'true');
		arEl.innerHTML = l.sortDirection == 'ASC' ? 'arrow_drop_up' : 'arrow_drop_down';
		el.appendChild(arEl);
	},
	lists: [],
	evListens: false
};