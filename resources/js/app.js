import './bootstrap';
import '../css/app.css';
import './realtime-dashboard';
import './components/action-buttons.js';
import './lazy-loading';

// Import local packages (no Bootstrap)
import $ from 'jquery';
window.$ = window.jQuery = $;

import 'select2';
import 'datatables.net';
import 'datatables.net-dt'; // Changed from bootstrap-5 to default theme
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

// TailwindCSS-compatible tooltip/popover alternative
function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
        });
        
        element.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.tooltip');
            if (tooltip) tooltip.remove();
        });
    });
}

// Initialize common functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize custom tooltips (TailwindCSS compatible)
    initializeTooltips();
    
    // Initialize DataTables with TailwindCSS styling
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 25,
            dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"<"mb-2 sm:mb-0"l><"mb-2 sm:mb-0"f>>rtip',
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
            },
            // TailwindCSS classes for DataTables
            initComplete: function() {
                // Style DataTable elements with TailwindCSS
                $('.dataTables_length select').addClass('block w-auto px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500');
                $('.dataTables_filter input').addClass('block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500');
                $('.dataTables_paginate .paginate_button').addClass('px-3 py-2 ml-1 text-sm text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50');
                $('.dataTables_paginate .paginate_button.current').addClass('bg-indigo-600 text-white hover:bg-indigo-700');
            }
        });
    }
    
    // Initialize Select2 without Bootstrap theme
    if ($.fn.select2) {
        $('.select2').select2({
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
    
    // Initialize mobile menu toggle (TailwindCSS compatible)
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Initialize dropdown menus (TailwindCSS compatible)
    const dropdowns = document.querySelectorAll('[data-dropdown-toggle]');
    dropdowns.forEach(button => {
        const targetId = button.getAttribute('data-dropdown-toggle');
        const dropdown = document.getElementById(targetId);
        
        if (dropdown) {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                dropdown.classList.add('hidden');
            });
            
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
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

import { createApp } from 'vue';
import SvgIcon from './components/SvgIcon.vue';

const app = createApp({});
app.component('SvgIcon', SvgIcon);

app.mount('#app');