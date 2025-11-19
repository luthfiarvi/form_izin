export function setupSignatureFromDom(root = document) {
    const canvas = root.querySelector('#signature-canvas');
    const clearBtn = root.querySelector('#clear-signature');
    const hidden = root.querySelector('#signature');
    if (!canvas || !hidden) return;

    const ctx = canvas.getContext('2d');
    let drawing = false;
    let drawn = false;

    ctx.strokeStyle = '#111827';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';

    const getPos = (e) => {
        const rect = canvas.getBoundingClientRect();
        if (e.touches && e.touches[0]) {
            return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
        }
        return { x: e.clientX - rect.left, y: e.clientY - rect.top };
    };

    const start = (e) => { drawing = true; drawn = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); };
    const move = (e) => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); };
    const end = () => { drawing = false; };

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', end);

    canvas.addEventListener('touchstart', (e) => { e.preventDefault(); start(e); }, { passive: false });
    canvas.addEventListener('touchmove', (e) => { e.preventDefault(); move(e); }, { passive: false });
    window.addEventListener('touchend', end);

    if (clearBtn) {
        clearBtn.addEventListener('click', () => { ctx.clearRect(0, 0, canvas.width, canvas.height); drawn = false; hidden.value = ''; });
    }

    const form = canvas.closest('form');
    if (form) {
        form.addEventListener('submit', () => {
            if (drawn) hidden.value = canvas.toDataURL('image/png');
        });
    }
}

document.addEventListener('DOMContentLoaded', () => setupSignatureFromDom());

