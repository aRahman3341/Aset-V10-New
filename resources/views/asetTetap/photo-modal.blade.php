{{-- resources/views/asetTetap/photo-modal.blade.php --}}
{{-- Modal Lightbox Foto Aset — include di index.blade.php --}}

<!-- ══ Modal Foto ══════════════════════════════════════════ -->
<div class="modal fade" id="modalFotoAset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:860px;">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;background:#111827;">

            {{-- Header --}}
            <div class="modal-header" style="background:#1e2d3d;border:none;padding:14px 20px;">
                <div class="d-flex align-items-center gap-3 w-100">
                    <div style="width:36px;height:36px;background:rgba(65,84,241,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-images" style="color:#a5b4fc;font-size:1rem;"></i>
                    </div>
                    <div class="flex-1" style="min-width:0;">
                        <div id="modalFotoTitle" style="color:#f1f5f9;font-size:.9rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Foto Aset</div>
                        <div id="modalFotoSubtitle" style="color:#64748b;font-size:.72rem;margin-top:1px;">Memuat...</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto flex-shrink-0" data-bs-dismiss="modal"></button>
                </div>
            </div>

            {{-- Body --}}
            <div class="modal-body p-0" style="background:#111827;">

                {{-- Loading state --}}
                <div id="fotoLoading" style="padding:60px;text-align:center;">
                    <div style="width:40px;height:40px;border:3px solid rgba(255,255,255,.1);border-top-color:#4154f1;border-radius:50%;animation:spin .7s linear infinite;margin:0 auto 14px;"></div>
                    <div style="color:#64748b;font-size:.82rem;">Memuat foto...</div>
                </div>

                {{-- Error state --}}
                <div id="fotoError" style="display:none;padding:60px;text-align:center;">
                    <i class="bi bi-exclamation-circle" style="font-size:2.5rem;color:#ef4444;display:block;margin-bottom:10px;"></i>
                    <div style="color:#94a3b8;font-size:.85rem;">Gagal memuat foto. Coba lagi.</div>
                </div>

                {{-- Empty state --}}
                <div id="fotoEmpty" style="display:none;padding:60px;text-align:center;">
                    <i class="bi bi-image" style="font-size:2.5rem;color:#374151;display:block;margin-bottom:10px;"></i>
                    <div style="color:#6b7280;font-size:.85rem;">Belum ada foto untuk aset ini.</div>
                </div>

                {{-- Foto utama (lightbox) --}}
                <div id="fotoContent" style="display:none;">
                    {{-- Main image --}}
                    <div style="position:relative;background:#000;display:flex;align-items:center;justify-content:center;min-height:420px;max-height:520px;overflow:hidden;">
                        <img id="fotoMainImg"
                             src=""
                             alt="Foto Aset"
                             style="max-width:100%;max-height:520px;object-fit:contain;display:block;transition:opacity .25s ease;">

                        {{-- Nav prev --}}
                        <button id="fotoPrev" onclick="navigatePhoto(-1)" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:40px;height:40px;background:rgba(0,0,0,.6);border:1px solid rgba(255,255,255,.15);border-radius:50%;color:#fff;font-size:1rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;backdrop-filter:blur(4px);">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        {{-- Nav next --}}
                        <button id="fotoNext" onclick="navigatePhoto(1)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:40px;height:40px;background:rgba(0,0,0,.6);border:1px solid rgba(255,255,255,.15);border-radius:50%;color:#fff;font-size:1rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .15s;backdrop-filter:blur(4px);">
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        {{-- Counter --}}
                        <div id="fotoCounter" style="position:absolute;top:12px;right:12px;background:rgba(0,0,0,.6);color:#fff;font-size:.72rem;font-weight:700;padding:4px 10px;border-radius:20px;backdrop-filter:blur(4px);"></div>

                        {{-- Nama file --}}
                        <div id="fotoFileName" style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,.7));color:#cbd5e1;font-size:.72rem;padding:20px 14px 10px;text-align:center;"></div>
                    </div>

                    {{-- Thumbnail strip --}}
                    <div id="fotoThumbs" style="display:flex;gap:8px;padding:12px 16px;overflow-x:auto;background:#1e2d3d;">
                        {{-- Filled by JS --}}
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }

#fotoThumbs::-webkit-scrollbar { height: 4px; }
#fotoThumbs::-webkit-scrollbar-track { background: transparent; }
#fotoThumbs::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 4px; }

.foto-thumb-strip {
    width: 70px;
    height: 70px;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all .18s;
    flex-shrink: 0;
    opacity: .65;
}
.foto-thumb-strip:hover { opacity: 1; border-color: rgba(255,255,255,.4); }
.foto-thumb-strip.active { border-color: #4154f1; opacity: 1; box-shadow: 0 0 0 3px rgba(65,84,241,.3); }

#fotoPrev:hover, #fotoNext:hover { background: rgba(65,84,241,.7); border-color: #4154f1; }
</style>

<script>
(function () {
    'use strict';

    let currentPhotos  = [];
    let currentIndex   = 0;

    window.openPhotoModal = function (materialId, namaBarang) {
        // Reset state
        currentPhotos = [];
        currentIndex  = 0;
        showFotoState('loading');
        document.getElementById('modalFotoTitle').textContent    = namaBarang || 'Foto Aset';
        document.getElementById('modalFotoSubtitle').textContent = 'Memuat...';

        // Tampilkan modal
        const modal = new bootstrap.Modal(document.getElementById('modalFotoAset'));
        modal.show();

        // Fetch foto
        fetch(`/asetTetap/${materialId}/photos`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(data => {
            const photos = data.photos || [];
            if (photos.length === 0) {
                showFotoState('empty');
                return;
            }
            currentPhotos = photos;
            renderFoto(0);
            showFotoState('content');
        })
        .catch(() => showFotoState('error'));
    };

    function showFotoState(state) {
        ['fotoLoading','fotoError','fotoEmpty','fotoContent'].forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
        const map = { loading:'fotoLoading', error:'fotoError', empty:'fotoEmpty', content:'fotoContent' };
        document.getElementById(map[state]).style.display = 'block';
    }

    function renderFoto(idx) {
        if (!currentPhotos.length) return;
        idx = Math.max(0, Math.min(idx, currentPhotos.length - 1));
        currentIndex = idx;

        const photo = currentPhotos[idx];
        const img   = document.getElementById('fotoMainImg');

        // Fade transition
        img.style.opacity = '0';
        setTimeout(() => {
            img.src = photo.url;
            img.onload = () => { img.style.opacity = '1'; };
            img.alt = photo.original_name;
        }, 120);

        // Counter & filename
        document.getElementById('fotoCounter').textContent = (idx + 1) + ' / ' + currentPhotos.length;
        document.getElementById('fotoFileName').textContent = photo.original_name;
        document.getElementById('modalFotoSubtitle').textContent = currentPhotos.length + ' foto';

        // Nav visibility
        document.getElementById('fotoPrev').style.display = idx > 0 ? 'flex' : 'none';
        document.getElementById('fotoNext').style.display = idx < currentPhotos.length - 1 ? 'flex' : 'none';

        // Thumbnail strip
        const thumbsEl = document.getElementById('fotoThumbs');
        thumbsEl.innerHTML = '';
        currentPhotos.forEach((p, i) => {
            const t = document.createElement('img');
            t.src       = p.url;
            t.alt       = p.original_name;
            t.className = 'foto-thumb-strip' + (i === idx ? ' active' : '');
            t.title     = p.original_name;
            t.addEventListener('click', () => renderFoto(i));
            thumbsEl.appendChild(t);
        });

        // Scroll active thumb into view
        const activeThumb = thumbsEl.querySelector('.active');
        if (activeThumb) activeThumb.scrollIntoView({ inline: 'center', behavior: 'smooth' });
    }

    window.navigatePhoto = function (dir) {
        renderFoto(currentIndex + dir);
    };

    // Keyboard navigation
    document.getElementById('modalFotoAset').addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft')  navigatePhoto(-1);
        if (e.key === 'ArrowRight') navigatePhoto(1);
    });
})();
</script>
