import Swal from 'sweetalert2';
import iziToast from 'izitoast';

export function showToast(message, type = 'success') {
    iziToast[type]({
        title: type.charAt(0).toUpperCase() + type.slice(1),
        message: message,
        position: 'topRight',
        timeout: 5000
    });
}

export function showAlert(title, message, type = 'info') {
    return Swal.fire({
        title: title,
        text: message,
        icon: type,
        confirmButtonColor: '#3b82f6',
        confirmButtonText: 'OK'
    });
}

export function confirmDelete(title = 'Are you sure?', message = 'This action cannot be undone') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    });
} 