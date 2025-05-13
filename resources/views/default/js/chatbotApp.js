// import './bootstrap';
import {Alpine, Livewire} from '~vendor/livewire/livewire/dist/livewire.esm';
import ajax from '~nodeModules/@imacrayon/alpine-ajax';
import {fetchEventSource} from '@microsoft/fetch-event-source';
// import modal from "./components/modal";
import clipboard from "./components/clipboard";
import '../scss/chatbot-embed.scss';

window.fetchEventSource = fetchEventSource;
window.Alpine = Alpine;

Alpine.plugin(ajax);
console.log('chatbotApp yüklendi');

document.addEventListener('alpine:init', () => {
    Alpine.data('clipboard', (data) => clipboard(data));
});

Livewire.start();

document.querySelectorAll('[magic-load]').forEach(function (element) {
    element.removeAttribute('magic-load');
});
