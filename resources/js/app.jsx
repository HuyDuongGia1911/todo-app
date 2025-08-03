import './bootstrap';
import { createRoot } from 'react-dom/client';
import React from 'react';
import Dashboard from './pages/Dashboard';
import TaskIndex from './pages/TaskIndex';
import ManagementIndex from './pages/ManagementIndex'; // thêm dòng này đầu file
import ProfilePage from './pages/ProfilePage';
import SummaryIndex from './pages/SummaryIndex';
import KpiPage from './pages/KpiPage';
import '/PHP/todo-app/resources/css/app.css'
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
const elManagement = document.getElementById('management-app');
if (elManagement) {
  createRoot(elManagement).render(<ManagementIndex />);
}
// Mount Profile
const elProfile = document.getElementById('profile-app');
if (elProfile) {
  createRoot(elProfile).render(<ProfilePage />);
}
// ---- Mount vào #summary-app ----
const elSummary = document.getElementById('summary-app');
if (elSummary) {
  const root = createRoot(elSummary);
  root.render(<SummaryIndex />);
}
// Mount KPI
const elKpi = document.getElementById('kpi-app');
if (elKpi) {
  const props = {
    initialKpis: JSON.parse(elKpi.dataset.kpis),
    filters: JSON.parse(elKpi.dataset.filters),
  };
  createRoot(elKpi).render(<KpiPage {...props} />);
}