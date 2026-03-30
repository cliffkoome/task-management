const API_BASE = '/api';

const Api = {

  async getTasks(status = '') {
    const url = status ? `${API_BASE}/tasks?status=${status}` : `${API_BASE}/tasks`;
    const res = await fetch(url);
    return res.json();
  },

  async createTask(payload) {
    const res = await fetch(`${API_BASE}/tasks`, {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify(payload)
    });
    return { ok: res.ok, data: await res.json() };
  },

  async updateStatus(id, status) {
    const res = await fetch(`${API_BASE}/tasks/${id}/status`, {
      method:  'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({ status })
    });
    return { ok: res.ok, data: await res.json() };
  },

  async deleteTask(id) {
    const res = await fetch(`${API_BASE}/tasks/${id}`, { method: 'DELETE' });
    return { ok: res.ok, data: await res.json() };
  },

  async getReport(date) {
    const res = await fetch(`${API_BASE}/tasks/report?date=${date}`);
    return { ok: res.ok, data: await res.json() };
  }

};