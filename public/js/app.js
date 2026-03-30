const App = {

  currentFilter: '',
  currentPage:   'dashboard',

  init() {
    // Set default dates
    document.getElementById('m-due-date').value = UI.today();
    document.getElementById('reportDate').value = UI.today();

    // Modal backdrop close
    document.getElementById('taskModal').addEventListener('click', function(e) {
      if (e.target === this) App.closeModal();
    });

    // Load initial page
    this.loadDashboard();
  },

  // ===================== NAVIGATION =====================
  navigate(page) {
    // Hide all pages
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));

    // Deactivate all nav items
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));

    // Show selected page
    document.getElementById('page-' + page).classList.add('active');

    // Activate nav item
    const navMap = { dashboard: 0, tasks: 1, reports: 2 };
    document.querySelectorAll('.nav-item')[navMap[page]].classList.add('active');

    // Update topbar title
    const titles = { dashboard: 'Dashboard', tasks: 'Tasks', reports: 'Daily Report' };
    document.getElementById('pageTitle').textContent = titles[page];

    this.currentPage = page;

    if (page === 'dashboard') this.loadDashboard();
    if (page === 'tasks')     this.loadTasks(this.currentFilter);
    if (page === 'reports')   document.getElementById('reportDate').value = UI.today();
  },

  // ===================== MODAL =====================
  openModal() {
    document.getElementById('taskModal').classList.add('open');
    document.getElementById('m-title').focus();
    UI.hideError('modalError');
  },

  closeModal() {
    document.getElementById('taskModal').classList.remove('open');
    document.getElementById('m-title').value = '';
    document.getElementById('m-priority').value = 'medium';
    document.getElementById('m-due-date').value = UI.today();
    UI.hideError('modalError');
  },

  // ===================== DASHBOARD =====================
  async loadDashboard() {
    const data  = await Api.getTasks();
    const tasks = data.data || [];

    const counts = { pending: 0, in_progress: 0, done: 0, high: 0, medium: 0, low: 0 };
    tasks.forEach(t => { counts[t.status]++; counts[t.priority]++; });

    const total = tasks.length || 1;

    document.getElementById('stat-pending').textContent    = counts.pending;
    document.getElementById('stat-inprogress').textContent = counts.in_progress;
    document.getElementById('stat-done').textContent       = counts.done;
    document.getElementById('stat-high').textContent       = counts.high;
    document.getElementById('stat-medium').textContent     = counts.medium;
    document.getElementById('stat-low').textContent        = counts.low;

    UI.updateStatBar('bar-pending',    Math.round((counts.pending     / total) * 100));
    UI.updateStatBar('bar-inprogress', Math.round((counts.in_progress / total) * 100));
    UI.updateStatBar('bar-done',       Math.round((counts.done        / total) * 100));

    const recent = document.getElementById('recentTasks');
    recent.innerHTML = tasks.length === 0
      ? UI.emptyState('No tasks yet', 'Create your first task!')
      : tasks.slice(0, 5).map(t => UI.renderRecentTaskItem(t)).join('');
  },

  // ===================== TASKS =====================
  async loadTasks(status = '') {
    this.currentFilter = status;
    const data  = await Api.getTasks(status);
    const tasks = data.data || [];
    const list  = document.getElementById('taskList');

    list.innerHTML = tasks.length === 0
      ? UI.emptyState()
      : tasks.map(t => UI.renderTaskItem(t)).join('');
  },

  filterTasks(btn, status) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    this.loadTasks(status);
  },

  // ===================== CREATE TASK =====================
  async createTask() {
    const title    = document.getElementById('m-title').value.trim();
    const due_date = document.getElementById('m-due-date').value;
    const priority = document.getElementById('m-priority').value;

    if (!title) {
      UI.showError('modalError', 'Task title is required.');
      return;
    }

    UI.hideError('modalError');

    const { ok, data } = await Api.createTask({ title, due_date, priority });

    if (ok) {
      this.closeModal();
      UI.toast('Task created successfully!');
      this.loadDashboard();
      if (this.currentPage === 'tasks') this.loadTasks(this.currentFilter);
    } else {
      UI.showError('modalError', data.error);
    }
  },

  // ===================== UPDATE STATUS =====================
  async updateStatus(id, status) {
    const { ok, data } = await Api.updateStatus(id, status);

    if (ok) {
      UI.toast('Status updated!');
      this.loadTasks(this.currentFilter);
      this.loadDashboard();
    } else {
      UI.toast(data.error, 'error');
    }
  },

  // ===================== DELETE TASK =====================
  async deleteTask(id) {
    if (!confirm('Delete this task? This cannot be undone.')) return;

    const { ok, data } = await Api.deleteTask(id);

    if (ok) {
      UI.toast('Task deleted.');
      this.loadTasks(this.currentFilter);
      this.loadDashboard();
    } else {
      UI.toast(data.error, 'error');
    }
  },

  // ===================== REPORT =====================
  async loadReport() {
    const date = document.getElementById('reportDate').value;

    if (!date) { UI.toast('Please select a date.', 'error'); return; }

    const { ok, data } = await Api.getReport(date);

    if (!ok) { UI.toast(data.error, 'error'); return; }

    const { summary } = data;
    ['high', 'medium', 'low'].forEach(p => {
      ['pending', 'in_progress', 'done'].forEach(s => {
        UI.setReportValue(p, s, summary[p][s]);
      });
    });

    document.getElementById('reportMsg').textContent = `Showing results for ${date}`;
    UI.toast('Report generated!');
  }

};

// Boot
document.addEventListener('DOMContentLoaded', () => App.init());