import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

export default class MapComponent {
    constructor(selector, options = {}) {
        this.selector = selector;
        this.map = L.map(selector).setView(options.center || [ -33.45, -70.66 ], options.zoom || 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(this.map);

        this.layer = L.layerGroup().addTo(this.map);
    }

    async loadVehicles() {
        try {
            const res = await window.axios.get('/api/vehicles/map');
            this.layer.clearLayers();
            res.data.forEach(v => {
                const marker = L.marker([v.latitude, v.longitude]).addTo(this.layer);
                marker.bindPopup(`<strong>${v.plate}</strong><br>${v.brand} ${v.model}<br>Status: ${v.status}`);
                if (v.status === 'inactive') {
                    marker.setOpacity(0.5);
                }
            });
            if (res.data.length) {
                const group = L.featureGroup(this.layer.getLayers());
                this.map.fitBounds(group.getBounds());
            }
        } catch (e) {
            console.error('Failed to load vehicles', e);
        }
    }

    addAdminMenu(containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        const menu = document.createElement('div');
        menu.className = 'absolute top-4 left-4 bg-white p-2 rounded shadow';
        // Ensure the admin menu is above Leaflet panes and accepts clicks
        menu.style.position = 'absolute';
        menu.style.zIndex = '10000';
        menu.style.pointerEvents = 'auto';
        menu.innerHTML = `
            <button id="refreshVehicles" class="px-3 py-1 border rounded">Refresh</button>
            <button id="centerMap" class="px-3 py-1 border rounded ml-2">Center</button>
        `;
        container.appendChild(menu);

        menu.querySelector('#refreshVehicles').addEventListener('click', () => this.loadVehicles());
        menu.querySelector('#centerMap').addEventListener('click', () => this.map.setView([-33.45, -70.66], 12));
    }
}
