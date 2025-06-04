import './app';

// Frontend-specific libraries
import 'slick-carousel';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';
import iziToast from 'izitoast';
import 'izitoast/dist/css/iziToast.min.css';
import Swal from 'sweetalert2';

// Make globally available
window.iziToast = iziToast;
window.Swal = Swal;

// Frontend functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sliders
    if (typeof $.fn.slick !== 'undefined') {
        $('.slider').slick({
            dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 1,
            adaptiveHeight: true,
            autoplay: true,
            autoplaySpeed: 5000
        });

        $('.testimonial-slider').slick({
            dots: true,
            infinite: true,
            speed: 500,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    }

    // Job search functionality
    initializeJobSearch();
    
    // Filter functionality
    initializeFilters();

    console.log('Frontend components initialized');
});

function initializeJobSearch() {
    const searchForm = document.getElementById('job-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Implement job search logic
            console.log('Job search submitted');
        });
    }
}

function initializeFilters() {
    const filterSelects = document.querySelectorAll('.filter-select');
    filterSelects.forEach(select => {
        if (typeof $.fn.select2 !== 'undefined') {
            $(select).select2({
                width: '100%',
                placeholder: 'All'
            });
        }
    });
}