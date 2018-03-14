var guest = {
	login: function(el, ev) {
		if(ev) ev.preventDefault();
		var bt = sel('#lb', false);
		bt.classList.add('btn-spinner');
		ajax.form.json(el, function(data) {
			bt.classList.remove('btn-spinner');
			if(data.success) nav.go('/');
		});
	},
	register: function(el, ev, step) {
		if(ev) ev.preventDefault();
		switch(step) {
			case 1:
				var bt = sel('#rsb1', false);
				bt.classList.add('btn-spinner');
				ajax.form.json(el, function(data) {
					bt.classList.remove('btn-spinner');
					if(!data.success) return;
					el.parentNode.css('display', 'none');
					sel('#rs2', false).css('display', '');
					sel('#sid', false).value = data.sid;
				});
				break;
			case 2:
				var bt = sel('#rsb2', false);
				bt.classList.add('btn-spinner');
				ajax.form.json(el, function(data) {
					if(ne(data.error) && nu(data.error.action)) eval(data.error.action);
					if(!data.success) return bt.classList.remove('btn-spinner');
					nav.go('/');
				});
				break;
		}
	},
	regCodeSend: function() {
		ajax.form.json(sel('#rs2f', false), function(data) {
			if(!data.success) return;
			sel('#sid', false).value = data.sid;
		});
	},
	regS1: function() {
		sel('#rs1', false).css('display', '');
		sel('#rs2', false).css('display', 'none');
	}
}