import './bootstrap';
import { createRoot } from 'react-dom/client';
import React from 'react';
import Dashboard from './pages/Dashboard';
import TaskIndex from './pages/TaskIndex';

// Mount Dashboard
const elDashboard = document.getElementById('react-dashboard');
if (elDashboard) {
  const props = {
    userName: elDashboard.dataset.username,
    taskCount: elDashboard.dataset.taskcount,
    dashboardData: JSON.parse(elDashboard.dataset.dashboard),
  };
  createRoot(elDashboard).render(<Dashboard {...props} />);
}

// Mount TaskIndex
const elTaskList = document.getElementById('react-task-list');
if (elTaskList) {
  const tasks = JSON.parse(elTaskList.dataset.tasks);
  createRoot(elTaskList).render(<TaskIndex tasks={tasks} />);
}
