/* Poll authenticated web routes so updates also work without a WebSocket server. */
(() => {
    const banner = document.getElementById('admin-safety');
    if (!banner) return;
    const label = document.getElementById('admin-safety-status');
    let busy = false;
    let stopped = false;

    async function fetchUpdate(url, accept) {
        const response = await fetch(url, {
            credentials: 'same-origin', cache: 'no-store', redirect: 'error',
            headers: { Accept: accept, 'X-Requested-With': 'XMLHttpRequest' },
            signal: AbortSignal.timeout(10000),
        });
        if ([401, 403, 404, 419].includes(response.status)) {
            stopped = true;
            throw new Error('session');
        }
        if (!response.ok) throw new Error('network');
        return response;
    }

    async function refresh() {
        if (busy || stopped || document.hidden) return;
        busy = true;
        try {
            const summary = await (await fetchUpdate(banner.dataset.summaryUrl, 'application/json')).json();
            label.textContent = `${summary.sos_count} SOS aktif · ${summary.emergency_count} peringatan aktif · ${summary.hiker_messages} pesan pendaki pada SOS aktif. Diperbarui otomatis setiap 5 detik.`;
            banner.classList.toggle('alert-danger', summary.sos_count > 0);
            banner.classList.toggle('alert-info', summary.sos_count === 0);

            if (document.querySelector('[data-admin-live]')) {
                const html = await (await fetchUpdate(window.location.href, 'text/html')).text();
                const fresh = new DOMParser().parseFromString(html, 'text/html');
                document.querySelectorAll('[data-admin-live]').forEach(region => {
                    const next = fresh.querySelector(`[data-admin-live="${region.dataset.adminLive}"]`);
                    // Never discard a draft, focused control, or text being selected.
                    if (!next || region.contains(document.activeElement)
                        || window.getSelection()?.toString()) return;
                    if (region.innerHTML === next.innerHTML) return;
                    const nearBottom = region.scrollHeight - region.scrollTop - region.clientHeight < 40;
                    const scrollTop = region.scrollTop;
                    region.innerHTML = next.innerHTML;
                    region.scrollTop = region.dataset.adminLive === 'chat' && nearBottom
                        ? region.scrollHeight : scrollTop;
                });
            }
        } catch (error) {
            label.textContent = stopped || error instanceof TypeError
                ? 'Pembaruan terputus. Periksa koneksi dan login kembali bila sesi berakhir.'
                : 'Pembaruan belum berhasil. Mencoba lagi secara otomatis…';
        } finally {
            busy = false;
        }
    }
    refresh();
    window.setInterval(refresh, 5000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
})();
