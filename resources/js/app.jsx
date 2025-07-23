import './bootstrap';
import { createRoot } from 'react-dom/client';
import Dashboard from './components/Dashboard'
import React from 'react';
// Gắn vào DOM khi đã có phần tử chứa id này
const el = document.getElementById('react-dashboard');

if (el) {
  const props = {
    userName: el.dataset.username,
    taskCount: el.dataset.taskcount,
    dashboardData: JSON.parse(el.dataset.dashboard),
  };

  createRoot(el).render(<Dashboard {...props} />);
}

