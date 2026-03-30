const UI = {

  today() {
    return new Date().toISOString().split('T')[0];
  },

  toast(msg, type = 'success') {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.className = `toast show ${type}`;
    setTimeout(() => { el.className = 'toast'; }, 3000);
  },

  priorityBadge(p) {
    return `<span class="badge badge-${p}">${p}</span>`;
  },

  statusBadge(s) {
    return `<span class="badge badge-${s}">${s.replace('_', ' ')}</span>`;
  },

  actionButtons(task) {
    const next   = { pending: 'in_progress', in_progress: 'done' };
    const labels = { in_progress: '▶ Start', done: '✔ Complete' };
    let html = '';

    if (next[task.status]) {
      html += `<button class="btn btn-success" onclick="App.updateStatus(${task.id}, '${next[task.status]}')">${labels[next[task.status]]}</button>`;
    }

    if (task.status === 'done') {
      html += `<button class="btn btn-danger" onclick="App.deleteTask(${task.id})">Delete</button>`;
    }

    return html;
  },

  renderTaskItem(task) {
    return `
      <div class="task-item ${task.priority}">
        <div class="task-info">
          <div class="task-title">${task.title}</div>
          <div class="task-meta">
            <span class="task-date"><i class="fa-solid fa-calendar-days"></i> ${task.due_date}</span>
            ${this.priorityBadge(task.priority)}
            ${this.statusBadge(task.status)}
          </div>
        </div>
        <div class="task-actions">
          ${this.actionButtons(task)}
        </div>
      </div>`;
  },

  renderRecentTaskItem(task) {
    return `
      <div class="recent-task-item ${task.priority}">
        <div>
          <div class="recent-task-title">${task.title}</div>
          <div class="recent-task-date">Due: ${task.due_date}</div>
        </div>
        <div class="recent-task-badges">
          ${this.priorityBadge(task.priority)}
          ${this.statusBadge(task.status)}
        </div>
      </div>`;
  },

  emptyState(message = 'No tasks found', sub = 'Create a new task to get started.') {
    return `
      <div class="empty-state">
        <span class="empty-icon">✅</span>
        <h3>${message}</h3>
        <p>${sub}</p>
      </div>`;
  },

  showError(id, msg) {
    const el = document.getElementById(id);
    el.textContent = msg;
    el.classList.add('show');
  },

  hideError(id) {
    document.getElementById(id).classList.remove('show');
  },

  setReportValue(priority, status, value) {
    const el = document.getElementById(`rpt-${priority}-${status}`);
    if (el) el.textContent = value;
  },

  updateStatBar(id, percent) {
    const el = document.getElementById(id);
    if (el) el.style.width = percent + '%';
  }

};