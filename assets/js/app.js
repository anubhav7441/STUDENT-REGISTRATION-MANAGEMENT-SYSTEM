/**
 * Student Registration Management System Pro
 * Main JavaScript — Validation · Toasts · Modal · Search · Preview
 */

'use strict';

/* TOAST NOTIFICATION SYSTEM*/
const Toast = (() => {
  const container = () => document.getElementById('toast-container');

  function show(message, type = 'info', duration = 4500) {
    const icons = { success: '✅', error: '❌', info: 'ℹ️' };
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.setAttribute('role', 'alert');
    el.innerHTML = `
      <span class="toast-icon" aria-hidden="true">${icons[type] ?? 'ℹ️'}</span>
      <span class="toast-msg">${escapeHtml(message)}</span>
      <button class="toast-close" aria-label="Dismiss">✕</button>
    `;
    el.querySelector('.toast-close').addEventListener('click', () => dismiss(el));
    container().appendChild(el);
    if (duration > 0) setTimeout(() => dismiss(el), duration);
    return el;
  }

  function dismiss(el) {
    if (!el || !el.parentNode) return;
    el.style.animation = 'toastOut .3s cubic-bezier(.4,0,.2,1) both';
    el.addEventListener('animationend', () => el.remove(), { once: true });
  }

  return { show };
})();

/* DELETE CONFIRMATION MODAL */
const DeleteModal = (() => {
  let overlay, idInput, nameEl;

  function init() {
    overlay = document.getElementById('delete-modal');
    idInput = document.getElementById('delete-id');
    nameEl = document.getElementById('delete-name');
    if (!overlay) return;

    overlay.addEventListener('click', e => { if (e.target === overlay) close(); });
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape' && overlay.classList.contains('open')) close();
    });
  }

  function open(id, name) {
    if (!overlay) return;
    idInput.value = id;
    nameEl.textContent = name;
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
    setTimeout(() => overlay.querySelector('.btn-secondary')?.focus(), 60);
  }

  function close() {
    if (!overlay) return;
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  return { init, open, close };
})();

window.confirmDelete = (id, name) => DeleteModal.open(id, name);
window.closeDeleteModal = () => DeleteModal.close();

/* REAL-TIME FORM VALIDATION
   Error messages match PDF requirement exactly.
   */
const FormValidator = (() => {

  // Rules for standard input/select/textarea fields
  const rules = {
    full_name: {
      test: v => v.trim().length >= 2,
      msg: 'Please enter your full name',
    },
    email: {
      test: v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()),
      msg: 'Please enter a valid email address',
    },
    phone: {
      test: v => /^\d{10}$/.test(String(v).replace(/\D/g, '')),
      msg: 'Phone number must contain 10 digits',
    },
    date_of_birth: {
      test: v => {
        if (!v) return false;
        const d = new Date(v);
        return !isNaN(d.getTime()) && d < new Date();
      },
      msg: 'Please enter a valid date of birth',
    },
    country: {
      test: v => v !== '' && v !== null,
      msg: 'Country must be selected',
    },
  };

  // ── Helpers ────────────────────────────────────────────────

  function setFieldState(field, valid, msg) {
    const group = field.closest('.form-group');
    if (!group) return;
    const errEl = group.querySelector('.form-error');
    field.classList.toggle('valid', valid);
    field.classList.toggle('invalid', !valid);
    if (errEl) errEl.textContent = valid ? '' : (msg || '');
  }

  function setGroupState(group, valid, msg) {
    if (!group) return;
    const errEl = group.querySelector('.form-error');
    const box = group.querySelector('.radio-group, .checkbox-grid');
    if (box) {
      box.classList.toggle('group-valid', valid);
      box.classList.toggle('group-invalid', !valid);
    }
    if (errEl) errEl.textContent = valid ? '' : (msg || '');
  }

  // ── Validate single standard field ────────────────────────

  function validateField(field) {
    const name = field.name;
    const rule = rules[name];
    if (!rule) return true;

    const value = field.value;

    // Optional field left blank — clear state
    if (!field.hasAttribute('required') && value.trim() === '') {
      field.classList.remove('valid', 'invalid');
      const g = field.closest('.form-group');
      if (g) { const e = g.querySelector('.form-error'); if (e) e.textContent = ''; }
      return true;
    }

    const ok = rule.test(value);
    setFieldState(field, ok, rule.msg);
    return ok;
  }

  // ── Validate radio group (gender) ─────────────────────────

  function validateRadioGroup(form, name) {
    const radios = form.querySelectorAll(`input[name="${name}"]`);
    if (!radios.length) return true;
    const checked = [...radios].some(r => r.checked);
    const group = radios[0].closest('.form-group');
    setGroupState(group, checked, 'Please select your gender');
    return checked;
  }

  // ── Full form validation (on submit) ──────────────────────

  function validateAll(form) {
    let allOk = true;

    // Standard required fields
    form.querySelectorAll('input[required]:not([type="radio"]), select[required], textarea[required]')
      .forEach(f => { if (!validateField(f)) allOk = false; });

    // Radio groups
    const radioGroups = new Set();
    form.querySelectorAll('input[type="radio"][required]')
      .forEach(r => radioGroups.add(r.name));
    radioGroups.forEach(name => {
      if (!validateRadioGroup(form, name)) allOk = false;
    });

    return allOk;
  }

  // ── Attach listeners to form ───────────────────────────────

  function attachTo(form) {
    if (!form) return;

    // Input/select/textarea — validate on blur & live correction
    form.querySelectorAll('input:not([type="radio"]):not([type="checkbox"]):not([type="file"]), select, textarea')
      .forEach(field => {
        field.addEventListener('blur', () => validateField(field));
        field.addEventListener('input', () => {
          if (field.classList.contains('valid') || field.classList.contains('invalid')) {
            validateField(field);
          }
        });
        field.addEventListener('change', () => validateField(field));
      });

    // Radio buttons — validate on change
    form.querySelectorAll('input[type="radio"]').forEach(radio => {
      radio.addEventListener('change', () => validateRadioGroup(form, radio.name));
    });

    // Submit — run all validations
    form.addEventListener('submit', e => {
      const ok = validateAll(form);
      if (!ok) {
        e.preventDefault();
        // Scroll to first invalid element
        const first = form.querySelector('.invalid, .group-invalid');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        Toast.show('Please fix the highlighted errors before submitting.', 'error');
      }
    });
  }

  return { attachTo };
})();

/* IMAGE UPLOAD PREVIEW*/
function initFilePreview() {
  const input = document.getElementById('profile_image');
  const preview = document.getElementById('image-preview');
  const nameLabel = document.getElementById('file-name-label');
  if (!input || !preview) return;

  input.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
      Toast.show('File size must not exceed 2 MB.', 'error');
      this.value = '';
      return;
    }
    const allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!allowed.includes(file.type)) {
      Toast.show('Only JPG, PNG, and WEBP images are allowed.', 'error');
      this.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
    if (nameLabel) nameLabel.textContent = file.name;
  });
}

/* SEARCH — debounced auto-submit */
function initSearch() {
  const input = document.getElementById('search-input');
  if (!input) return;
  let timer;
  input.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => {
      const form = document.getElementById('search-form');
      if (form) form.submit();
    }, 450);
  });
}

/* FLASH TOASTS — from URL query params after redirect */
function initFlashMessages() {
  const p = new URLSearchParams(location.search);
  const s = p.get('success');
  const e = p.get('error');

  if (s === 'added') Toast.show('🎉 Student registered successfully!', 'success');
  if (s === 'updated') Toast.show('✏️ Student record updated successfully.', 'success');
  if (s === 'deleted') Toast.show('🗑️ Student deleted successfully.', 'success');
  if (e === 'notfound') Toast.show('Student record not found.', 'error');

  // Clean URL so toast doesn't re-fire on refresh
  if (s || e) {
    const clean = new URL(location.href);
    clean.searchParams.delete('success');
    clean.searchParams.delete('error');
    clean.searchParams.delete('id');
    history.replaceState(null, '', clean.toString());
  }
}

/* UTILITY */
function escapeHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
    .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/* BOOTSTRAP */
document.addEventListener('DOMContentLoaded', () => {
  DeleteModal.init();
  initFlashMessages();
  initSearch();
  initFilePreview();
  FormValidator.attachTo(document.getElementById('student-form'));
});