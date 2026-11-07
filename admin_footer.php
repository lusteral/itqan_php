</main>

<footer class="bg-white border-t border-slate-200 text-center py-4 text-xs font-bold text-slate-400">
    نظام إدارة منصة إتقان المحمي © 2026 — جميع العمليات مشفرة ومؤمنة
</footer>
</div>

<script>
function toggleMobileSidebar() {
    const sb = document.getElementById('adminSidebar');
    sb.classList.toggle('hidden');
}

function openAdminTab(tabName) {
    document.querySelectorAll('.tab-panel').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.admin-nav-btn').forEach(el => el.classList.remove('active'));

    const targetPanel = document.getElementById('tab-' + tabName);
    const targetNav = document.getElementById('nav-' + tabName);

    if (targetPanel) targetPanel.classList.add('active');
    if (targetNav) targetNav.classList.add('active');

    if (history.replaceState) {
        history.replaceState(null, null, '?tab=' + tabName);
    }
    document.querySelectorAll('input[name="return_tab"]').forEach(inp => {
        inp.value = tabName;
    });
}

function openModal(modalId) {
    const m = document.getElementById(modalId);
    if (m) m.classList.remove('hidden');
}

function closeModal(modalId) {
    const m = document.getElementById(modalId);
    if (m) m.classList.add('hidden');
}

window.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const tab = params.get('tab') || 'overview';
    openAdminTab(tab);

    const editCourseId = params.get('edit_course');
    if (editCourseId) {
        openAdminTab('courses');
        openModal('edit-course-' + editCourseId);
    }
    const editWorkId = params.get('edit_work');
    if (editWorkId) {
        openAdminTab('works');
        openModal('edit-work-' + editWorkId);
    }
});
</script>
</body>
</html>