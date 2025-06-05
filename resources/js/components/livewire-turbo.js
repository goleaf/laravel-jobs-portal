// livewire-turbo Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // livewire-turbo Component JavaScript
// Enhanced with Context7 patterns

let livewireToken ="{{ csrf_token() }}"

// livewire-turbo Component JavaScript
// Enhanced with Context7 patterns

window.livewire = new Livewire();window.Livewire = window.livewire;window.livewire_app_url = '';window.livewire_token = livewireToken ;window.deferLoadingAlpine = function (callback) {window.addEventListener('livewire:load', function () {callback();});};let started = false;window.addEventListener('alpine:initializing', function () {if (! started) {window.livewire.start();started = true;}});document.addEventListener("DOMContentLoaded", function () {if (! started) {window.livewire.start();started = true;}});


    } catch (error) {
        console.error('Error in livewire-turbo component:', error);
    }
});