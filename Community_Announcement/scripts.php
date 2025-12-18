<script>
// --- SIDEBAR LOGIC ---
const burgerBtn = document.getElementById('burgerBtn');
const closeSidebar = document.getElementById('closeSidebar');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const sidebarToggle = document.getElementById('sidebarToggle');

function openSidebarFn() {
    if (overlay) overlay.classList.remove('hidden');
    if (sidebar) sidebar.classList.remove('-translate-x-full');
}

function closeSidebarFn() {
    if (overlay) overlay.classList.add('hidden');
    if (sidebar) sidebar.classList.add('-translate-x-full');
}

if (burgerBtn) {
    burgerBtn.addEventListener('click', openSidebarFn);
}
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', openSidebarFn);
}
if (closeSidebar) {
    closeSidebar.addEventListener('click', closeSidebarFn);
}
if (overlay) {
    overlay.addEventListener('click', closeSidebarFn);
}
// Close sidebar on ESC
document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebarFn(); });

// --- MODAL LOGIC (Handling Image Previews) ---
function openModal(type, action, id = null, title = '', content = '', date = '', loc = '', img = '') {
    let modal, dialog;
    if (type === 'event') {
        modal = document.getElementById('eventModal');
        dialog = modal.querySelector('.modal-dialog');
        document.getElementById('eventAction').value = action;
        document.getElementById('eventId').value = id;
        document.getElementById('mEventTitle').value = title;
        document.getElementById('mEventDate').value = date;
        document.getElementById('mEventLoc').value = loc;
        document.getElementById('mEventDesc').value = content;
    } else {
        modal = document.getElementById('annModal');
        dialog = modal.querySelector('.modal-dialog');
        document.getElementById('annAction').value = action;
        document.getElementById('annId').value = id;
        document.getElementById('mAnnTitle').value = title;
        document.getElementById('mAnnContent').value = content;
        // Handling the previous photo display
        const preview = document.getElementById('annImagePreview');
        if (img && action === 'edit') {
            preview.src = 'uploads/announcements/' + img;
            preview.classList.remove('hidden');
        } else {
            preview.src = '';
            preview.classList.add('hidden');
        }
    }

    // Show modal and animate dialog
    modal.classList.remove('hidden');
    // Small timeout to ensure transition applies
    setTimeout(() => {
        if (dialog) {
            dialog.classList.remove('opacity-0','scale-95');
            dialog.classList.add('opacity-100','scale-100');
        }
    }, 20);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    const dialog = modal.querySelector('.modal-dialog');
    if (dialog) {
        dialog.classList.remove('opacity-100','scale-100');
        dialog.classList.add('opacity-0','scale-95');
        // Wait for transition then hide
        setTimeout(() => modal.classList.add('hidden'), 300);
    } else {
        modal.classList.add('hidden');
    }
}

// --- SEARCH LOGIC (Works for both Cards) ---
document.addEventListener('input', function (e) {
    if (e.target && e.target.id === 'searchInput') {
        const term = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.content-item');

        items.forEach(item => {
            const title = item.getAttribute('data-title').toLowerCase();
            const date = item.getAttribute('data-date').toLowerCase();
            
            if (title.includes(term) || date.includes(term)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }
});
</script>

<!-- AOS library (Animate On Scroll) -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
// Initialize AOS unless user prefers reduced motion
if (!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            once: true,
            duration: 650,
            easing: 'ease-out-cubic',
            offset: 80
        });
    });
}
</script>