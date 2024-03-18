import Flickity from 'flickity';
import { Html5QrcodeScanner } from 'html5-qrcode';
import 'flickity/dist/flickity.min.css';

import.meta.glob([
    '../images/**',
]);

window.Flickity = Flickity;
window.Html5QrcodeScanner = Html5QrcodeScanner;
