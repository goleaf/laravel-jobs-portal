import './app';

// Admin-specific libraries
import 'datatables.net';
import 'datatables.net-dt/css/jquery.dataTables.min.css';
import 'select2';
import 'select2/dist/css/select2.min.css';
import Chart from 'chart.js/auto';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import Swal from 'sweetalert2';
import 'summernote/dist/summernote-lite.min.js';
import 'summernote/dist/summernote-lite.min.css';

// Make globally available
window.Chart = Chart;
window.flatpickr = flatpickr;
window.Swal = Swal;

// Initialize admin components
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }

    // Initialize Select2
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            width: '100%',
            placeholder: 'Select an option'
        });
    }

    // Initialize Flatpickr
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.flatpickr', {
            dateFormat: 'Y-m-d',
            allowInput: true
        });
    }

    // Initialize Summernote
    if (typeof $.fn.summernote !== 'undefined') {
        $('.summernote').summernote({
            height: 200,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    }

    console.log('Admin components initialized');
});