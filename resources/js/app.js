import './bootstrap';

import Alpine from 'alpinejs';
import mask from '@alpinejs/mask';

window.Alpine = Alpine;

// Registrar o plugin mask ANTES de iniciar o Alpine
Alpine.plugin(mask);

// Iniciar Alpine.js
Alpine.start();
