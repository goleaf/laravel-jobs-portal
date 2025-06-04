// Core Libraries
import Alpine from 'alpinejs';
import $ from 'jquery';
window.$ = window.jQuery = $;

// Alpine.js Setup
window.Alpine = Alpine;
Alpine.start();

// Chart.js
import Chart from 'chart.js/auto';
window.Chart = Chart;

// Moment.js
import moment from 'moment';
window.moment = moment;

// Select2
import 'select2';

// Flatpickr Date Picker
import flatpickr from 'flatpickr';
window.flatpickr = flatpickr;

// SweetAlert2
import Swal from 'sweetalert2';
window.Swal = Swal;

// Toastr
import toastr from 'toastr';
window.toastr = toastr;

// IziToast
import iziToast from 'izitoast';
window.iziToast = iziToast;

// Slick Carousel
import 'slick-carousel';

// Ion Range Slider
import 'ion-rangeslider';

// International Telephone Input
import intlTelInput from 'intl-tel-input';
window.intlTelInput = intlTelInput;

// Summernote
import 'summernote';

// AutoNumeric
import AutoNumeric from 'autonumeric';
window.AutoNumeric = AutoNumeric;

// Date Range Picker
import 'bootstrap-daterangepicker';

// DataTables
import 'datatables.net';

// Quill Editor
import Quill from 'quill';
window.Quill = Quill;

// CKEditor
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
window.ClassicEditor = ClassicEditor;

// Handlebars
import Handlebars from 'handlebars/runtime';
window.Handlebars = Handlebars;

// JSRender
import jsrender from 'jsrender';
window.jsrender = jsrender;

// Lodash
import _ from 'lodash';
window._ = _;

// Axios
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Livewire Turbolinks (local alternative)
import { Turbo } from '@hotwired/turbo';
window.Turbo = Turbo; 