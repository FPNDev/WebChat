events.initialize('mousepressed', ['clientX', 'clientY', 'offsetX', 'offsetY', 'pageX', 'pageY', 'path'], {
    pressed: []
});

document.on('mousedown', function (ev) {
    events.set('mousepressed', 'pressed', []);
    var el = ev.target;
    while (el) {
        events.get('mousepressed', 'pressed').push(el);
        el = el.parentNode;
    }
}, true);

document.on('mousemove', function (ev) {
    events.get('mousepressed', 'pressed').forEach(function (v) {
        v.runEvent('mousepressed', ev);
    });
});

document.on('mouseup', function (ev) {
    events.get('mousepressed', 'pressed').forEach(function (v) {
        v.runEvent('mousepressed', ev);
    });
    events.set('mousepressed', 'pressed', []);
}, true);

events.initialize('inputend', []);

document.addEventListener('input', function (ev) {
    var path = ev.path || (ev.composedPath && ev.composedPath());
    if (!path) return;
    if (typeof path[0].input  != 'undefined' && path[0].input) clearTimeout(path[0].input);
    path[0].input = setTimeout(function () {
        path[0].runEvent('inputend', ev);
    }, 500);
}, true);

if (browser.mobile) {
    events.initialize('touchpressed', ['touches', 'direction', 'path', 't'], {
        touchesPos: []
    });

    document.on('touchstart', function (ev) {
        events.set('touchpressed', 'touchesPos', Array.prototype.map.call(ev.touches, function (v) {
            return {
                x: v.screenX,
                y: v.screenY
            };
        }));

        var direction = [];
        Array.prototype.forEach.call(events.get('touchpressed', 'touchesPos'), function (v, k) {
            direction[k] = {
                x: 0,
                y: 0
            };
        });
        ev.direction = direction;
        ev.target.runEvent('touchpressed', ev);
    }, true);

    document.on('touchmove', function (ev) {
        var tPoses = events.get('touchpressed', 'touchesPos');
        var direction = [];
        Array.prototype.forEach.call(ev.touches, function (v, k) {
            direction[k] = {
                x: v.screenX - tPoses[k].x,
                y: v.screenY - tPoses[k].y
            };
        });
        ev.direction = direction;
        ev.target.runEvent('touchpressed', ev);
        events.set('touchpressed', 'touchesPos', Array.prototype.map.call(ev.touches, function (v) {
            return {
                x: v.screenX,
                y: v.screenY
            };
        }));
    });

    document.on('touchend', function (ev) {
        events.set('touchpressed', 'touchesPos', Array.prototype.map.call(ev.touches, function (v) {
            return {
                x: v.screenX,
                y: v.screenY
            };
        }));
    }, true);

    events.initialize('tap', ['touches', 'path'], {
        tapping: []
    });

    document.on('touchstart', function (ev) {
        if (ev.touches.length > 1) return;
        events.get('tap', 'tapping').push([this, Date.now()]);
    }, true);

    document.on('touchmove', function (ev) {
        if (ev.touches.length > 1) return;
        events.get('tap', 'tapping').forEach(function (v, k) {
            if (v[0] == this) {
                delete events.get('tap', 'tapping')[k];
            }
        });
    }, true);

    document.on('touchend', function (ev) {
        if (ev.touches.length > 1) return;
        events.get('tap', 'tapping').forEach(function (v, k) {
            if (Date.now() - v[1] < 800 && v[0] == ev.target) {
                v[0].runEvent('tap', ev);
                delete events.get('tap', 'tapping')[k];
            }
        });
    }, true);
}