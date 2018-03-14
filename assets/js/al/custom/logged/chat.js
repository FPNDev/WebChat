var chat = {
	send: function(ev) {

		if(!ev) return;
		if(ev.type != 'click' && !(ev.type == 'keydown' && (ev.which || ev.keyCode) == 13 && ev.shiftKey)) return;

		ev.preventDefault();

		var ta = ge('cm');
		if(!ta.innerHTML) return ta.focus();

		var t = ta.innerHTML.replace(/<div>/gi, '\n').replace(/<\/div>/gi, '').replace(/<br>/gi, '\n').replace(/[\n]{2,}/g, '\n\n').trim(),
			msg = chat.tpl({ username: chat.accountData.username, text: t });

		ta.innerHTML = '';

		var ml = ge('ml');
		ml.insertAdjacentHTML('beforeend', msg);
		chat.stb();

		var ms = ml.lastElementChild;
		ms.classList.add('loading');

		ajax.api('chat.send', { sec: cookie('sec'), text: HTML.unescape(t), th: chat.th }, function(data) {
			if(data.error) return ms.remove();

			var nms = ms.cloneNode(true);
			nms.classList.remove('loading');
			nms.id = 'c'+data.success;

			var sc = ml.scrollHeight - ml.offsetHeight - ml.scrollTop,
				scb = !ml.lastElementChild || sc >= ml.lastElementChild.offsetHeight;

			ms.remove();
			ml.appendChild(nms);

			scb && chat.stb();
			chat.of++;
		});
	},
	load: function() {
		if(chat.ended || chat.loading) return;

		chat.loading = true;

		ajax.api('chat.get', { sec: cookie('sec'), of: chat.of }, function(d) {
			if(!d.success) return;
			chat.of += d.success.u.length;
			chat.loading = false;
			chat.ended = d.success.e;

			var ml = ge('ml');
			ml.classList.remove('btn-spinner');
			var sh = ml.scrollHeight;
			d.success.u.forEach(function(e) {
				var msg = chat.tpl({ id: e.id, username: e.username, text: e.text });
				ml.insertAdjacentHTML('afterbegin', msg);
			});
			ml.scrollTop = ml.scrollHeight - sh;
		});
	},
	delete: function(el, ev) {
		if(ev) ev.preventDefault();

		var msg = el.queryParent('.message-wrap');
		if(!msg) return;

		msg.classList.add('loading');

		ajax.api('chat.delete', { sec: cookie('sec'), id: msg.id.substr(1), th: chat.th }, function(data) {
			if(data.error && data.error.error_msg) {
				msg.style.opacity = 1;
				msg.classList.remove('loading');
				return topMsg(data.error.error_msg, 2, 'error');
			}
			chat.of--;
			msg.remove();

			chat.load();
		});
	},
	lp: function(e) {
		if(e.data.th == chat.th) return;
		switch(e.event) {
			case 'add': {
				var msg = chat.tpl({ id: e.id, username: e.data.user.username, text: e.data.text }),
					ml = ge('ml'),
					sc = ml.scrollHeight - ml.offsetHeight - ml.scrollTop,
					scb = !ml.lastElementChild || sc <= ml.lastElementChild.offsetHeight;

				ml.insertAdjacentHTML('beforeend', msg);
				
				scb && chat.stb();
				chat.of++;
				break;
			}

			case 'delete': {
				var msg = ge('c'+e.id);
				msg && msg.remove();
				chat.of--;
			}
		}
	},
	tpl: function(d) {
		var my = d.username == chat.accountData.username,
			html = '';

		d.text = d.text.replace(/\n/g, '<br>');
		html += '<div class="message-wrap" id="c'+(d.id ? d.id : -1)+'">';
		html += 	'<div class="message '+(my ? 'my' : 'other')+'">';
		if(!my)
			html +=		'<div class="author">'+HTML.escape(d.username)+'</div>';
		html +=			'<div class="text">'+d.text+'</div>';
		if(my) html += 	'<a class="link del" onclick="return chat.delete(this,event)">Удалить</a>';
		html +=		'</div>';
		html +=	'</div>';
		return html;
	},
	trim: function(el, ev) {
		el.value = el.value.substr(0, 1500);
	},
	generateTH: function() {
		chat.th = Math.random().toString(36).substring(2, 15);
	},
	stb: function() {
		var ml = ge('ml');
		ml.scrollTop = ml.scrollHeight;
	},
	scroll: function(ev) {
		if(!chat.ended && !chat.loading && ev.target.scrollTop < 20) chat.load();
	},
	th: 0,
	ended: false,
	loading: false,
	of: 0,
	accountData: false
}

chat.generateTH();

init.view('logged/chat', function() {
	chat.ended = false;
	chat.loading = false;
	chat.of = 0;
	chat.load();
	chat.accountData = ad;
	ge('ml').onscroll = chat.scroll;
});

window.on('lp', function(ev) {
	switch(ev.channel) {
		case 0:
			ev.updates.each(chat.lp);
			break;
	}
});