events.initialize('lp', ['channel', 'updates'], {
    ts: []
});

let lp = {
	listen: function() {
		let tsa = events.get('lp', 'ts');
		let xhr = ajax.json('/server/lp.listen', {ts: tsa.join('_')}, data => {
			data.each((i, k) => {
				tsa[k] = i[0];
				delete i[0];
				i = i.filter(item => { return item !== undefined });
				if(i.length) window.runEvent('lp', {channel: parseInt(k), updates: i});
			});
			lp.listen();
		});
		xhr.onerror = () => {
			lp.listen();
		};
	}
}
lp.listen();