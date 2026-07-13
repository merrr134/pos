import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart; // dipakai grafik Dashboard Admin (FR-010) & Laporan (FR-011) — offline-safe via Vite

Alpine.start();
