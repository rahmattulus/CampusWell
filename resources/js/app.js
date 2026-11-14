import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
Alpine.start();

window.initIcons = () => {
    createIcons({
        icons,
        attrs: {
            'stroke-width': 1.75,
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    window.initIcons();
});
