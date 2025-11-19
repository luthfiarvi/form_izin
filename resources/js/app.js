import './bootstrap'
import '../css/app.css'

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Signature canvas setup for pages that include it (register/admin users edit)
document.addEventListener('DOMContentLoaded', () => {
  /**
   * Initialize a signature canvas with drawing + clear + form-submit hookup.
   */
  function initSignature(canvas) {
    if (!canvas.getContext) return;
    if (canvas.dataset.sigInitialized === '1') return;
    const ctx = canvas.getContext('2d');
    let drawing = false;
    let drawn = false;

    // Avoid scroll and selection while drawing
    try { canvas.style.touchAction = 'none'; } catch (e) {}
    try { canvas.style.webkitUserSelect = 'none'; canvas.style.userSelect = 'none'; } catch (e) {}

    // Size and scale for DPR
    function sizeCanvas() {
      const dpr = window.devicePixelRatio || 1;
      const rect = canvas.getBoundingClientRect();
      const targetW = Math.max(1, Math.round(rect.width));
      const targetH = Math.max(1, Math.round(rect.height || canvas.height || 160));

      canvas.width = Math.floor(targetW * dpr);
      canvas.height = Math.floor(targetH * dpr);
      canvas.style.width = targetW + 'px';
      canvas.style.height = targetH + 'px';

      ctx.setTransform(1, 0, 0, 1, 0, 0);
      if (dpr !== 1) ctx.scale(dpr, dpr);

      ctx.lineWidth = 2;
      ctx.lineJoin = 'round';
      ctx.lineCap = 'round';
      ctx.strokeStyle = '#111827';
    }
    sizeCanvas();
    window.addEventListener('resize', sizeCanvas);

    const getPos = (e) => {
      const rect = canvas.getBoundingClientRect();
      if (e.touches && e.touches[0]) {
        return {
          x: e.touches[0].clientX - rect.left,
          y: e.touches[0].clientY - rect.top,
        };
      }
      return { x: e.clientX - rect.left, y: e.clientY - rect.top };
    };

    const start = (e) => {
      drawing = true;
      drawn = true;
      const p = getPos(e);
      ctx.beginPath();
      ctx.moveTo(p.x, p.y);
      if (e.pointerId) {
        try { canvas.setPointerCapture(e.pointerId); } catch (_) {}
      }
      e.preventDefault();
    };
    const move = (e) => {
      if (!drawing) return;
      const p = getPos(e);
      ctx.lineTo(p.x, p.y);
      ctx.stroke();
      e.preventDefault();
    };
    const end = () => { drawing = false; };

    // Use Pointer Events for wider support; fallback to mouse/touch if unavailable
    if ('onpointerdown' in window) {
      canvas.addEventListener('pointerdown', start);
      canvas.addEventListener('pointermove', move);
      window.addEventListener('pointerup', end);
      window.addEventListener('pointercancel', end);
      window.addEventListener('pointerleave', end);
    } else {
      canvas.addEventListener('mousedown', start);
      canvas.addEventListener('mousemove', move);
      window.addEventListener('mouseup', end);
      canvas.addEventListener('touchstart', start, { passive: false });
      canvas.addEventListener('touchmove', move, { passive: false });
      window.addEventListener('touchend', end);
    }

    // Mark as initialized to avoid double binding
    canvas.dataset.sigInitialized = '1';

    // Hook up clear/upload buttons and hidden input within same container/form
    const container = canvas.closest('div') || document;
    const hidden = (container.querySelector('input[name="signature"]')) || document.getElementById('signature');
    const clearBtn = container.querySelector('#clear-signature');
    const uploadBtn = container.querySelector('[data-signature-upload-btn]');
    const fileInput = container.querySelector('input[type="file"][name="signature_file"]') || (canvas.closest('form')?.querySelector('input[type="file"][name="signature_file"]'));
    const fileNameEl = container.querySelector('[data-signature-file-name]');
    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        const w = canvas.width;
        const h = canvas.height;
        ctx.clearRect(0, 0, w, h);
        drawn = false;
        if (hidden) hidden.value = '';
        if (fileInput) {
          try { fileInput.value = ''; } catch (e) { /* no-op */ }
          if (fileNameEl) fileNameEl.textContent = '';
        }
      });
    }

    if (uploadBtn && fileInput) {
      uploadBtn.addEventListener('click', () => fileInput.click());
      fileInput.addEventListener('change', () => {
        const file = fileInput.files && fileInput.files[0];
        if (fileNameEl) fileNameEl.textContent = file ? file.name : '';
        // If a file is chosen, prefer it over canvas
        if (hidden) hidden.value = '';
        // Optional: clear canvas when a file is selected to avoid confusion
        if (file) {
          const w = canvas.width, h = canvas.height;
          ctx.clearRect(0, 0, w, h);
          drawn = false;
        }
      });
    }

    // On submit: prefer file input if provided; else attach canvas data if drawn
    const form = canvas.closest('form');
    if (form) {
      form.addEventListener('submit', () => {
        const fi = form.querySelector('input[name="signature_file"]');
        const hasFile = fi && fi.files && fi.files.length > 0;
        if (!hidden) return;
        if (hasFile) {
          hidden.value = '';
          return;
        }
        if (drawn) {
          try {
            hidden.value = canvas.toDataURL('image/png');
          } catch (e) {
            // no-op
          }
        } else {
          hidden.value = '';
        }
      });
    }
  }

  const canvases = document.querySelectorAll('canvas[data-signature-canvas], canvas#signature-canvas');
  canvases.forEach(initSignature);
});
