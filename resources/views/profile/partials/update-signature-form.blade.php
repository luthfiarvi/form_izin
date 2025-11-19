<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Tanda Tangan Digital (TTD)
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Unggah file PNG/JPG atau gambar langsung pada kanvas.
        </p>
    </header>

    <form method="post" action="{{ route('profile.signature.update', [], false) }}" class="mt-6 space-y-4" enctype="multipart/form-data">
        @csrf
        @method('patch')

        @php $sig = $user->signature_path; @endphp
        @if ($sig)
            @php $sigName = basename($sig); @endphp
            <div class="flex items-center gap-3">
                <div class="h-16 w-40 bg-white border rounded bg-no-repeat bg-contain bg-left pointer-events-none select-none"
                     style="background-image: url('{{ asset('storage/'.$sig) }}');"
                     aria-label="TTD saat ini"></div>
                <span class="text-xs text-gray-600">TTD saat ini</span>
            </div>
        @endif

        <div class="border rounded p-3">
            <canvas id="profile-signature-canvas" width="600" height="180" class="w-full max-w-xl border bg-white rounded touch-none pointer-events-auto"></canvas>
            <div class="mt-2 flex items-center gap-2">
                <button type="button" id="profile-clear-signature" class="px-3 py-1 bg-gray-200 rounded pointer-events-auto">Bersihkan</button>
                <label for="profile_signature_file" class="px-3 py-1 bg-blue-600 text-white rounded cursor-pointer pointer-events-auto">Unggah</label>
                <span class="text-xs text-gray-600" data-signature-file-name></span>
            </div>
            <input id="profile_signature_file" type="file" name="signature_file" accept="image/*" class="hidden pointer-events-auto">
            <input type="hidden" id="profile_signature" name="signature" value="">
            <x-input-error :messages="$errors->get('signature_file')" class="mt-2" />
            <x-input-error :messages="$errors->get('signature')" class="mt-1" />
        </div>

        <div>
            <x-primary-button>Simpan TTD</x-primary-button>
        </div>
    </form>

    @push('scripts')
    <script>
    (function(){
      const canvas = document.getElementById('profile-signature-canvas');
      if(!canvas || canvas.dataset.sigInitialized==='1' || !canvas.getContext) return;
      const ctx = canvas.getContext('2d'); let draw=false;
      function pos(e){ const r=canvas.getBoundingClientRect(); const t=e.touches?e.touches[0]:e; return {x:t.clientX-r.left,y:t.clientY-r.top}; }
      function start(e){ draw=true; const p=pos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); e.preventDefault(); }
      function move(e){ if(!draw) return; const p=pos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); e.preventDefault(); }
      function end(){ draw=false; }
      canvas.style.touchAction='none';
      canvas.addEventListener('pointerdown',start); canvas.addEventListener('pointermove',move); window.addEventListener('pointerup',end);
      canvas.addEventListener('touchstart',start,{passive:false}); canvas.addEventListener('touchmove',move,{passive:false}); window.addEventListener('touchend',end);
      const clearBtn=document.getElementById('profile-clear-signature'); if(clearBtn){ clearBtn.addEventListener('click',()=>{ ctx.clearRect(0,0,canvas.width,canvas.height); }); }
      const form=canvas.closest('form'); const hidden=document.getElementById('profile_signature'); if(form&&hidden){ form.addEventListener('submit',()=>{ const fi=document.getElementById('profile_signature_file'); const hasFile=fi&&fi.files&&fi.files.length>0; if(!hasFile){ try{ hidden.value=canvas.toDataURL('image/png'); }catch(_){} } else { hidden.value=''; } }); }
      const fi=document.getElementById('profile_signature_file'); const nameEl=document.querySelector('#profile-signature-canvas').parentElement.querySelector('[data-signature-file-name]'); if(fi&&nameEl){ fi.addEventListener('change',()=>{ nameEl.textContent=fi.files&&fi.files[0]?fi.files[0].name:''; }); }
      canvas.dataset.sigInitialized='1';
    })();
    </script>
    @endpush
</section>
