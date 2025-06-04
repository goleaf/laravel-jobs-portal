import './bootstrap';
import '../css/app.css';
import './realtime-dashboard';
import './components/action-buttons.js';

// Import local packages
import 'bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;

import 'select2';
import 'datatables.net';
import 'datatables.net-bs5';
import Swal from 'sweetalert2';
window.Swal = Swal;

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import moment from 'moment';
window.moment = moment;

import _ from 'lodash';
window._ = _;

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Chart libraries
import Chart from 'chart.js/auto';
window.Chart = Chart;

import ApexCharts from 'apexcharts';
window.ApexCharts = ApexCharts;

// Date picker
import flatpickr from 'flatpickr';
window.flatpickr = flatpickr;

// File upload
import { Dropzone } from 'dropzone';
window.Dropzone = Dropzone;

// Sliders
import { Swiper } from 'swiper/bundle';
window.Swiper = Swiper;

// Initialize common functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Initialize DataTables
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 25,
            language: {
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Previous'
                }
            }
        });
    }
    
    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
    
    // Initialize date pickers
    if (window.flatpickr) {
        flatpickr('.datepicker', {
            dateFormat: 'Y-m-d',
            allowInput: true
        });
        
        flatpickr('.datetimepicker', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            allowInput: true
        });
    }
});

// Global error handling
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
});

// CSRF token setup
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}