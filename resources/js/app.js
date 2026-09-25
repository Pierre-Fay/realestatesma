import Alpine from 'alpinejs';
import { registerBlatUI } from './blatui-core.js';

window.Alpine = Alpine;

registerBlatUI(Alpine);

Alpine.start();
