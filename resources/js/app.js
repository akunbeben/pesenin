import './bootstrap';

import.meta.glob([
    '../images/**',
]);

import Flickity from 'flickity';
import { Html5QrcodeScanner } from 'html5-qrcode';
import 'flickity/dist/flickity.min.css';

window.Flickity = Flickity;
window.Html5QrcodeScanner = Html5QrcodeScanner;
