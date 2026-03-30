<?php // Task Management - Frontend ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TaskFlow</title>
  <link rel="stylesheet" href="/task-management/public/css/style.css"/>
  <script src="https://kit.fontawesome.com/8c27d1b0fd.js" crossorigin="anonymous"></script>
</head>
<body>

<!-- ===================== SIDEBAR ===================== -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <h2>TaskFlow</h2>
    <p>Workspace</p>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-item active" onclick="App.navigate('dashboard')">
      <span class="icon"><i class="fa-solid fa-chart-simple"></i></span> Dashboard
    </div>
    <div class="nav-item" onclick="App.navigate('tasks')">
      <span class="icon"><i class="fa-solid fa-clipboard-list"></i></span> Tasks
    </div>
    <div class="nav-item" onclick="App.navigate('reports')">
      <span class="icon"><i class="fa-solid fa-chart-line"></i></span> Reports
    </div>
  </nav>

  <div class="sidebar-footer">
    <button class="btn-new-task" onclick="App.openModal()">
      <span>＋</span> New Task
    </button>
    <div class="nav-item">
      <span class="icon"><i class="fa-solid fa-question"></i></span> Help
    </div>
  </div>
</aside>

<!-- ===================== MAIN ===================== -->
<main class="main">

  <!-- Topbar -->
  <header class="topbar">
    <h1 id="pageTitle">Dashboard</h1>
    <button class="btn btn-primary btn-lg" onclick="App.openModal()">
      + Create New Task
    </button>
  </header>

  <div class="content">

    <!-- ========== DASHBOARD ========== -->
    <div id="page-dashboard" class="page active">
      <div class="page-header">
        <h2>System Health</h2>
        <p>Here's your live task status overview.</p>
      </div>

      <div class="dashboard-grid">

        <!-- Stats Card -->
        <div class="card">
          <div class="flex items-center justify-between">
            <span class="card-title" style="margin:0">Task Overview</span>
            <span class="live-badge"><i class="fa-solid fa-circle"></i> Live</span>
          </div>
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-label">Pending</div>
              <div class="stat-value blue" id="stat-pending">0</div>
              <div class="stat-bar-track"><div class="stat-bar-fill blue" id="bar-pending"></div></div>
            </div>
            <div class="stat-card">
              <div class="stat-label">In Progress</div>
              <div class="stat-value orange" id="stat-inprogress">0</div>
              <div class="stat-bar-track"><div class="stat-bar-fill orange" id="bar-inprogress"></div></div>
            </div>
            <div class="stat-card">
              <div class="stat-label">Done</div>
              <div class="stat-value green" id="stat-done">0</div>
              <div class="stat-bar-track"><div class="stat-bar-fill green" id="bar-done"></div></div>
            </div>
          </div>
        </div>

        <!-- Priority Panel -->
        <div class="priority-panel">
          <div>
            <h3>Priority Breakdown</h3>
            <p>Tasks by priority level</p>
          </div>
          <div class="priority-row">
            <span>🔴 High</span>
            <span id="stat-high">0</span>
          </div>
          <div class="priority-row">
            <span>🟡 Medium</span>
            <span id="stat-medium">0</span>
          </div>
          <div class="priority-row">
            <span>🟢 Low</span>
            <span id="stat-low">0</span>
          </div>
          <div class="priority-panel-deco">✓</div>
        </div>
      </div>

      <!-- Recent Tasks -->
      <div class="card">
        <div class="recent-task-header">
          <span class="card-title" style="margin:0">Recent Tasks</span>
          <button class="btn btn-outline" onclick="App.navigate('tasks')">View All →</button>
        </div>
        <div id="recentTasks" class="task-list mt-md">
          <p class="text-muted" style="text-align:center;padding:2rem">Loading...</p>
        </div>
      </div>
    </div>

    <!-- ========== TASKS ========== -->
    <div id="page-tasks" class="page">
      <div class="page-header">
        <h2>All Tasks</h2>
        <p>Manage and track all your tasks.</p>
      </div>

      <div class="filter-bar">
        <button class="filter-btn active" onclick="App.filterTasks(this, '')">All</button>
        <button class="filter-btn" onclick="App.filterTasks(this, 'pending')">Pending</button>
        <button class="filter-btn" onclick="App.filterTasks(this, 'in_progress')">In Progress</button>
        <button class="filter-btn" onclick="App.filterTasks(this, 'done')">Done</button>
      </div>

      <div id="taskList" class="task-list">
        <p class="text-muted" style="text-align:center;padding:2rem">Loading tasks...</p>
      </div>
    </div>

    <!-- ========== REPORTS ========== -->
    <div id="page-reports" class="page">
      <div class="page-header flex items-center justify-between" style="flex-direction:row;align-items:flex-end">
        <div>
          <h2>Daily Report</h2>
          <p>Monitor task output by priority and status.</p>
        </div>
        <div class="report-controls">
          <input type="date" id="reportDate"/>
          <button class="btn btn-primary btn-lg" onclick="App.loadReport()">
            ↻ Generate
          </button>
        </div>
      </div>

      <div class="report-grid">

        <!-- High -->
        <div class="report-card">
          <div class="report-card-header">
            <span class="report-label high">High Priority</span>
            <span class="report-icon high">⚠</span>
          </div>
          <div class="report-stat">
            <span class="report-stat-label">Pending</span>
            <span class="report-stat-value" id="rpt-high-pending">—</span>
          </div>
          <div class="report-stat">
            <span class="report-stat-label">In Progress</span>
            <span class="report-stat-value" id="rpt-high-in_progress">—</span>
          </div>
          <div class="report-stat">
            <span class="report-stat-label">Done</span>
            <span class="report-stat-value" id="rpt-high-done">—</span>
          </div>
        </div>

        <!-- Medium -->
        <div class="report-card">
          <div class="report-card-header">
            <span class="report-label medium">Medium Priority</span>
            <span class="report-icon medium">≡</span>
          </div>
          <div class="report-stat">
            <span class="report-stat-label">Pending</span>
            <span class="report-stat-value" id="rpt-medium-pending">—</span>
          </div>
          <div class="report-stat">
            <span class="report-stat-label">In Progress</span>
            <span class="report-stat-value" id="rpt-medium-in_progress">—</span>
          </div>
          <div class="report-stat">
            <span class="report-stat-label">Done</span>
            <span class="report-stat-value" id="rpt-medium-done">—</span>
          </div>
        </div>

        <!-- Low -->
        <div class="report-card">
          <div class="report-card-header">
            <span class="report-label low">Low Priority</span>
            <span class="report-icon low">↓</span>
          </div>
          <div class="report-stat">
            <span class="report-stat-label">Pending</span>
            <span class="report-stat-value" id="rpt-low-pending">—</span>
          </div>
          <div class="report-stat">
            <span class="report-stat-label">In Progress</span>
            <span class="report-stat-value" id="rpt-low-in_progress">—</span>
          </div>
          <div class="report-stat">
            <span class="report-stat-label">Done</span>
            <span class="report-stat-value" id="rpt-low-done">—</span>
          </div>
        </div>

      </div>

      <p class="report-msg" id="reportMsg"></p>
    </div>

  </div><!-- end content -->
</main>

<!-- ===================== MODAL ===================== -->
<div id="taskModal" class="modal-overlay">
  <div class="modal">
    <div class="modal-header">
      <div>
        <h2>Create New Task</h2>
        <p>Add a new task to your workspace.</p>
      </div>
      <button class="modal-close" onclick="App.closeModal()">✕</button>
    </div>

    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Task Title</label>
        <input id="m-title" type="text" class="form-input" placeholder="e.g., Fix login bug"/>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Due Date</label>
          <input id="m-due-date" type="date" class="form-input"/>
        </div>
        <div class="form-group">
          <label class="form-label">Priority</label>
          <select id="m-priority" class="form-select">
            <option value="high">High</option>
            <option value="medium" selected>Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
      </div>

      <div id="modalError" class="form-error"></div>

      <div class="modal-actions">
        <button class="btn btn-outline btn-lg" onclick="App.closeModal()">Cancel</button>
        <button class="btn btn-primary btn-lg" onclick="App.createTask()">Create Task</button>
      </div>
    </div>

    <div class="modal-footer">
      <div class="modal-footer-bar"></div>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="toast"></div>

<!-- Scripts — order matters: api → ui → app -->
<script src="/task-management/public/js/api.js"></script>
<script src="/task-management/public/js/ui.js"></script>
<script src="/task-management/public/js/app.js"></script>

</body>
</html>