/** Google Maps public search/embed URLs. No Google Maps Platform JavaScript/API key. */
export function googleQuery(address = '', lat = null, lng = null) {
    const value = String(address || '').trim();
    // Prefer manually-entered full address so old GPS coordinates cannot override edits.
    if (value) return value;
    if (lat !== null && lng !== null && lat !== '' && lng !== '' && Number.isFinite(Number(lat)) && Number.isFinite(Number(lng))) {
        return `${Number(lat)},${Number(lng)}`;
    }
    return '';
}

export function renderGoogleMap(element, destination, origin = '') {
    if (!element) return;
    const query = String(destination || '').trim();
    element.replaceChildren();
    let links = element.nextElementSibling;
    if (!links?.classList.contains('greenshop-google-map-links')) {
        links = document.createElement('div');
        links.className = 'greenshop-google-map-links';
        element.insertAdjacentElement('afterend', links);
    }
    links.replaceChildren();
    if (!query) {
        const placeholder = document.createElement('div');
        placeholder.className = 'greenshop-google-map-placeholder';
        placeholder.textContent = 'Nhập địa chỉ thủ công để xem vị trí trên Google Maps.';
        element.appendChild(placeholder);
        return;
    }
    const frame = document.createElement('iframe');
    frame.title = 'Google Maps – bản đồ tham khảo';
    frame.loading = 'lazy';
    frame.referrerPolicy = 'no-referrer-when-downgrade';
    frame.allowFullscreen = true;
    frame.src = `https://maps.google.com/maps?q=${encodeURIComponent(query)}&output=embed&hl=vi`;
    frame.style.cssText = 'width:100%;height:100%;min-height:260px;border:0;border-radius:10px;';
    element.appendChild(frame);
    const search = document.createElement('a');
    search.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(query)}`;
    search.textContent = 'Mở Google Maps';
    const directions = document.createElement('a');
    let directionsUrl = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(query)}`;
    if (origin) directionsUrl += `&origin=${encodeURIComponent(origin)}`;
    directions.href = directionsUrl;
    directions.textContent = 'Chỉ đường';
    for (const link of [search, directions]) {
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        link.style.cssText = 'display:inline-block;margin:8px 10px 4px 0;padding:8px 12px;border:1px solid #b7cbbd;border-radius:8px;color:#246c45;text-decoration:none;font-weight:600;background:white;';
        links.appendChild(link);
    }
}
