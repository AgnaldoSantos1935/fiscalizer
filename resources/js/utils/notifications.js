// ====================================================
// 🔔 notifications.js — Toastr + SweetAlert2 centralizados
// ====================================================
import toastr from 'toastr';
import Swal from 'sweetalert2';
import 'toastr/build/toastr.min.css';

export function setupToastrDefaults() {
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-bottom-right',
    timeOut: 4000,
  };
}

export function showToast(type, message, title = '') {
  toastr[type](message, title);
}

export function confirmarAcao(msg, onConfirm, options = {}) {
  Swal.fire({
    title: options.title || 'Confirmação',
    text: msg,
    icon: options.icon || 'warning',
    showCancelButton: true,
    confirmButtonText: options.confirmText || 'Sim',
    cancelButtonText: options.cancelText || 'Cancelar',
  }).then((result) => {
    if (result.isConfirmed && typeof onConfirm === 'function') onConfirm();
  });
}
