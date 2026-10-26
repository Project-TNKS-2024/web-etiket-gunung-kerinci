(() => {
    const map = L.map('monitoring-map').setView([-1.6974, 101.2642], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    const hikers = new Map();
    const sosMarkers = new Map();
    const postLayer = L.layerGroup().addTo(map);
    const sidebar = document.getElementById('hiker-sidebar');
    const status = document.getElementById('monitoring-status');
    const clock = new Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit' });
    let data = JSON.parse(document.getElementById('monitoring-data').textContent);
    let serverTime = Date.parse(data.server_time);
    let syncedAt = performance.now();
    let postsSignature = '';
    let busy = false;
    let stopped = false;

    function node(tag, text, className = '') {
        const element = document.createElement(tag);
        element.textContent = text;
        element.className = className;
        return element;
    }

    function coordinates(position) {
        if (!position) return null;
        const lat = Number(position.latitude);
        const lon = Number(position.longitude);
        return Number.isFinite(lat) && Number.isFinite(lon) && Math.abs(lat) <= 90 && Math.abs(lon) <= 180
            ? [lat, lon] : null;
    }

    function age(position) {
        const recorded = Date.parse(position?.recorded_at);
        return Number.isFinite(recorded) ? Math.max(0, serverTime + performance.now() - syncedAt - recorded) : null;
    }

    function ageLabel(position) {
        const elapsed = age(position);
        if (elapsed === null) return 'Belum ada GPS';
        const minutes = Math.floor(elapsed / 60000);
        if (minutes < 1) return 'Kurang dari 1 menit lalu';
        if (minutes < 60) return `${minutes} menit lalu`;
        if (minutes < 1440) return `${Math.floor(minutes / 60)} jam ${minutes % 60} menit lalu`;
        return `${Math.floor(minutes / 1440)} hari lalu`;
    }

    function hikerIcon(hiker) {
        const stale = age(hiker.position) > 30 * 60000;
        const color = hiker.has_sos ? '#dc3545' : stale ? '#ffc107' : '#198754';
        return L.divIcon({ className: '', iconSize: [14, 14],
            html: `<div style="width:14px;height:14px;border-radius:50%;background:${color};border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,0.3)"></div>` });
    }

    function popup(hiker) {
        const box = node('div', '');
        box.append(node('strong', hiker.name), node('div', ageLabel(hiker.position)));
        if (hiker.position) box.append(node('div', `GPS ${clock.format(new Date(hiker.position.recorded_at))} WIB · Baterai ${hiker.position.battery_level ?? '?'}%`));
        return box;
    }

    function updateAges() {
        for (const hiker of data.hikers) {
            const item = hikers.get(hiker.id);
            if (!item) continue;
            const stale = age(hiker.position) > 30 * 60000;
            item.card.className = `hiker-card w-100 text-start border-0 ${hiker.has_sos ? 'sos' : stale ? 'stale' : 'normal'}`;
            item.detail.textContent = `${hiker.gate} · ${ageLabel(hiker.position)}`;
            if (item.marker) {
                item.marker.setIcon(hikerIcon(hiker));
                item.marker.setPopupContent(popup(hiker));
            }
        }
    }

    function render(next) {
        data = next;
        serverTime = Date.parse(data.server_time);
        syncedAt = performance.now();
        document.getElementById('hiker-count').textContent = `${data.hikers.length} Pendaki Aktif`;
        document.getElementById('sos-count').textContent = `${data.sos.length} SOS`;
        document.getElementById('emergency-count').textContent = `${data.emergency_count} Emergency`;
        sidebar.querySelector('[data-empty]')?.remove();
        const activeIds = new Set(data.hikers.map(hiker => hiker.id));
        for (const [id, item] of hikers) {
            if (!activeIds.has(id)) {
                if (item.marker) map.removeLayer(item.marker);
                item.card.remove();
                hikers.delete(id);
            }
        }
        for (const hiker of data.hikers) {
            let item = hikers.get(hiker.id);
            if (!item) {
                const card = node('button', '');
                card.type = 'button';
                const name = node('div', '', 'fw-semibold');
                const detail = node('small', '', 'text-muted d-block');
                const battery = node('small', '', 'text-muted d-block');
                const badge = node('span', 'SOS', 'badge bg-danger');
                card.append(name, detail, battery, badge);
                card.addEventListener('click', () => {
                    const marker = hikers.get(hiker.id)?.marker;
                    if (marker) { map.setView(marker.getLatLng(), 15); marker.openPopup(); }
                });
                sidebar.append(card);
                item = { card, name, detail, battery, badge, marker: null };
                hikers.set(hiker.id, item);
            }
            item.name.textContent = hiker.name;
            item.badge.hidden = !hiker.has_sos;
            item.battery.hidden = hiker.position?.battery_level == null;
            item.battery.textContent = `Baterai ${hiker.position?.battery_level ?? '?'}%`;
            const point = coordinates(hiker.position);
            if (point) {
                if (item.marker) item.marker.setLatLng(point);
                else item.marker = L.marker(point, { icon: hikerIcon(hiker) }).addTo(map).bindPopup(popup(hiker));
            } else if (item.marker) { map.removeLayer(item.marker); item.marker = null; }
        }
        if (!data.hikers.length) {
            const empty = node('p', 'Tidak ada pendaki aktif', 'text-muted text-center');
            empty.dataset.empty = '';
            sidebar.append(empty);
        }
        const sosIds = new Set(data.sos.map(sos => sos.id));
        for (const [id, marker] of sosMarkers) {
            if (!sosIds.has(id)) { map.removeLayer(marker); sosMarkers.delete(id); }
        }
        for (const sos of data.sos) {
            const point = coordinates(sos);
            if (!point) continue;
            const box = node('div', '');
            box.append(node('strong', `SOS - ${sos.severity}`), node('div', sos.name));
            const link = node('a', 'Detail');
            link.href = sos.url;
            box.append(link);
            let marker = sosMarkers.get(sos.id);
            if (!marker) {
                marker = L.marker(point, { icon: L.divIcon({ className: '', iconSize: [24, 24], html: '<i class="ti ti-urgent" style="font-size:24px;color:#dc3545"></i>' }) }).addTo(map).bindPopup(box);
                sosMarkers.set(sos.id, marker);
            } else marker.setLatLng(point).setPopupContent(box);
        }
        const signature = JSON.stringify(data.posts);
        if (signature !== postsSignature) {
            postLayer.clearLayers();
            for (const post of data.posts) {
                const point = coordinates(post);
                if (!point) continue;
                L.circle(point, { radius: post.radius, color: '#6c757d', fillOpacity: 0.05, weight: 1 }).addTo(postLayer);
                L.marker(point, { icon: L.divIcon({ className: '', iconSize: [16, 16], html: '<i class="ti ti-flag" style="font-size:16px;color:#6c757d"></i>' }) }).addTo(postLayer).bindPopup(node('strong', post.name));
            }
            postsSignature = signature;
        }
        updateAges();
        status.textContent = `Diperbarui ${clock.format(new Date(data.server_time))} WIB · Otomatis setiap 5 detik`;
    }

    async function refresh() {
        if (busy || stopped || document.hidden) return;
        busy = true;
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 10000);
        try {
            const response = await fetch(window.location.href, {
                credentials: 'same-origin', cache: 'no-store', redirect: 'error',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, signal: controller.signal
            });
            if ([401, 403, 404, 419].includes(response.status)) { stopped = true; throw new Error('session'); }
            if (!response.ok) throw new Error('network');
            render(await response.json());
        } catch (error) {
            status.textContent = stopped ? 'Pembaruan terhenti. Muat ulang dan periksa sesi login.'
                : 'Pembaruan gagal. Posisi terakhir tetap ditampilkan; mencoba lagi otomatis…';
        } finally { clearTimeout(timeout); busy = false; }
    }
    render(data);
    setInterval(refresh, 5000);
    setInterval(updateAges, 10000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) { updateAges(); refresh(); } });
})();
