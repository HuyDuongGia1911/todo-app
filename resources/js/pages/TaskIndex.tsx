import React, { useState, useEffect } from 'react';
import Select, { SingleValue } from 'react-select';
import SidebarEditTask from '../components/SidebarEditTask';
import { Sun, Sunrise, Moon } from 'lucide-react';
// Định nghĩa kiểu option cho react-select
type OptionType = { value: string; label: string };

interface Task {
  id: number;
  title: string;
  task_date: string;
  created_at: string;
  shift?: string;
  type?: string;
  supervisor?: string;
  priority?: string;
  progress?: number;
  detail?: string;
  file_link?: string;
  status: 'Đã hoàn thành' | 'Chưa hoàn thành';
}

interface Props {
  tasks: Task[];
}

export default function TaskIndex({ tasks }: Props) {
  const [taskList, setTaskList] = useState<Task[]>(tasks);
  const [editingTask, setEditingTask] = useState<Task | null>(null);
  const [newTaskTitle, setNewTaskTitle] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [tab, setTab] = useState<'all' | 'done' | 'pending' | 'overdue'>('all');
  const [priorityFilter, setPriorityFilter] = useState<SingleValue<OptionType>>(null);
  const [taskDateStart, setTaskDateStart] = useState('');
  const [taskDateEnd, setTaskDateEnd] = useState('');
  const [createdStart, setCreatedStart] = useState('');
  const [createdEnd, setCreatedEnd] = useState('');
  const itemsPerPage = 10;

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  useEffect(() => {
    setCurrentPage(1);
  }, [tab, priorityFilter, taskDateStart, taskDateEnd, createdStart, createdEnd]);
  const getCurrentDayInfo = () => {
  const today = new Date();
  const days = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
  const weekday = days[today.getDay()];
  const date = today.toLocaleDateString('vi-VN');

  const hour = today.getHours();
  let session = '';
  let Icon = Sun; // default

  if (hour < 12) {
    session = 'Sáng';
    Icon = Sunrise;
  } else if (hour < 18) {
    session = 'Chiều';
    Icon = Sun;
  } else {
    session = 'Tối';
    Icon = Moon;
  }

  return { weekday, date, session, Icon };
};

  const applyFilters = (): Task[] => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return taskList.filter(task => {
      if (tab === 'done' && task.status !== 'Đã hoàn thành') return false;
      if (tab === 'pending' && task.status !== 'Chưa hoàn thành') return false;
      if (tab === 'overdue') {
        const taskDate = new Date(task.task_date);
        taskDate.setHours(0, 0, 0, 0);
        if (task.status === 'Đã hoàn thành' || taskDate >= today) return false;
      }
      if (priorityFilter && task.priority !== priorityFilter.value) return false;
      if (taskDateStart && new Date(task.task_date) < new Date(taskDateStart)) return false;
      if (taskDateEnd && new Date(task.task_date) > new Date(taskDateEnd)) return false;

      return true;
    });
  };

  const filteredTasks = applyFilters();
  const totalPages = Math.ceil(filteredTasks.length / itemsPerPage);
  const startIdx = (currentPage - 1) * itemsPerPage;
  const currentTasks = filteredTasks.slice(startIdx, startIdx + itemsPerPage);
  const { weekday, date, session, Icon } = getCurrentDayInfo();

  const buildExportUrl = (type: 'all' | 'filtered' = 'filtered') => {
    const params = new URLSearchParams();
    params.append('type', type);
    if (tab !== 'all') params.append('status_tab', tab);
    if (priorityFilter) params.append('priority', priorityFilter.value);
    if (taskDateStart) params.append('task_date_start', taskDateStart);
    if (taskDateEnd) params.append('task_date_end', taskDateEnd);

    return `/tasks/export?${params.toString()}`;
  };

  const resetFilters = () => {
    setTab('all');
    setPriorityFilter(null);
    setTaskDateStart('');
    setTaskDateEnd('');
    setCreatedStart('');
    setCreatedEnd('');
  };


  const handleToggle = async (task: Task) => {
    const newStatus = task.status === 'Đã hoàn thành' ? 'Chưa hoàn thành' : 'Đã hoàn thành';
    try {
      const res = await fetch(`/tasks/${task.id}/status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({ status: newStatus }),
      });

      if (!res.ok) throw new Error('Status update failed');
      setTaskList(prev => prev.map(t => (t.id === task.id ? { ...t, status: newStatus } : t)));
    } catch (err) {
      alert('Lỗi khi cập nhật trạng thái!');
      console.error(err);
    }
  };

  const handleSave = (updatedTask: Task) => {
    setTaskList(prev => prev.map(t => (t.id === updatedTask.id ? updatedTask : t)));
    setEditingTask(null);
  };

  const handleDelete = async (taskId: number) => {
    if (!confirm('Bạn có chắc chắn muốn xoá công việc này?')) return;
    try {
      const res = await fetch(`/tasks/${taskId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({ _method: 'DELETE' }),
      });

      if (!res.ok) throw new Error('Delete failed');
      setTaskList(prev => prev.filter(t => t.id !== taskId));
    } catch (err) {
      alert('Lỗi khi xoá công việc!');
      console.error(err);
    }
  };

  const handleQuickAdd = async () => {
    if (!newTaskTitle.trim()) return;
    try {
      const res = await fetch('/tasks/quick-add', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({ title: newTaskTitle.trim() }),
      });

      const result = await res.json();
      if (result.exists) {
        const confirmAdd = confirm("Hôm nay đã có task này, bạn vẫn muốn thêm chứ?");
        if (!confirmAdd) return;

        const forceRes = await fetch('/tasks', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
          },
          body: JSON.stringify({
            title: newTaskTitle.trim(),
            task_date: new Date().toISOString().split('T')[0],
            status: 'Chưa hoàn thành'
          }),
        });

        const newTask = await forceRes.json();
        setTaskList(prev => [newTask, ...prev]);
        setNewTaskTitle('');
        setCurrentPage(1);
        return;
      }

      if (result.task) {
        setTaskList(prev => [result.task, ...prev]);
        setNewTaskTitle('');
        setCurrentPage(1);
      }
    } catch (err) {
      alert('Lỗi khi thêm công việc!');
      console.error(err);
    }
  };

  return (
<div className="position-relative" style={{ minHeight: '100vh' }}>
      {editingTask && (
        <SidebarEditTask
          task={editingTask}
          onClose={() => setEditingTask(null)}
          onSave={handleSave}
        />
      )}

      <div className="main-content-wrapper" style={{ marginRight: editingTask ? '360px' : '0', transition: 'margin-right 0.3s ease',  overflowX: 'hidden' }}>
        <style>{`
          .switch {
            position: relative; height: 1.5rem; width: 3rem; cursor: pointer;
            appearance: none; border-radius: 9999px;
            background-color: rgba(100, 116, 139, 0.377); transition: all .3s ease;
          }
          .switch:checked { background-color: rgba(236, 72, 153, 1); }
          .switch::before {
            position: absolute; content: ""; left: calc(1.5rem - 1.6rem); top: calc(1.5rem - 1.6rem);
            height: 1.6rem; width: 1.6rem; border: 1px solid rgba(100, 116, 139, 0.527);
            border-radius: 9999px; background-color: white;
            box-shadow: 0 3px 10px rgba(100, 116, 139, 0.327); transition: all .3s ease;
          }
          .switch:checked::before {
            transform: translateX(100%);
            border-color: rgba(236, 72, 153, 1);
          }
        `}</style>



       <div className="mb-3 p-3 bg-white rounded shadow">
  {/* Tab lọc trạng thái */}
  <div className="d-flex flex-wrap mb-3">
    {['all', 'done', 'pending', 'overdue'].map(t => (
      <button
        key={t}
        className={`btn me-2 mb-2 ${tab === t ? 'btn-primary' : 'btn-outline-primary'} btn-sm`}
        onClick={() => setTab(t as 'all' | 'done' | 'pending' | 'overdue')}
      >
        {{ all: 'Tất cả', done: 'Đã hoàn thành', pending: 'Chưa hoàn thành', overdue: 'Quá hạn' }[t]}
      </button>
    ))}
  </div>

  {/* Bộ lọc nâng cao: độ ưu tiên + khoảng ngày */}
  <div className="row g-3 mb-3">
    <div className="col-md-3">
      <label className="form-label">Độ ưu tiên</label>
      <Select
        isClearable
        value={priorityFilter}
        onChange={setPriorityFilter}
        options={['Khẩn cấp', 'Cao', 'Trung bình', 'Thấp'].map(p => ({ value: p, label: p }))}
        placeholder="Chọn độ ưu tiên"
      />
    </div>

    <div className="col-md-3">
      <label className="form-label">Ngày công việc (Từ)</label>
      <input
        type="date"
        className="form-control"
        value={taskDateStart}
        onChange={e => setTaskDateStart(e.target.value)}
      />
    </div>

    <div className="col-md-3">
      <label className="form-label">Ngày công việc (Đến)</label>
      <input
        type="date"
        className="form-control"
        value={taskDateEnd}
        onChange={e => setTaskDateEnd(e.target.value)}
      />
    </div>
  </div>

  {/* Nút xuất và reset */}
  <div className="d-flex flex-wrap justify-content-between align-items-center mt-3">
      {/* SVG + thời gian */}
      <div className="d-flex align-items-center text-muted mb-2">
        <Icon size={20} className="me-2" />
        <span className="fw-semibold">{session}, {weekday} {date}</span>
      </div>

      {/* Nút xuất/reset */}
      <div className="d-flex flex-wrap justify-content-end">
        <a
          href="/tasks/export?type=all"
          className="btn btn-outline-dark btn-sm me-2 mb-2"
        >
          Xuất tất cả
        </a>
        <a
          href={buildExportUrl('filtered')}
          className="btn btn-success btn-sm me-2 mb-2"
          download
          target="_blank"
          rel="noopener noreferrer"
        >
          Xuất bảng hiện tại
        </a>
        <button
          className="btn btn-outline-secondary btn-sm mb-2"
          onClick={resetFilters}
        >
          Reset bộ lọc
        </button>
      </div>
  </div>
</div>





         
         <div className="d-flex mb-3">
          <input type="text" className="form-control me-2" placeholder="Thêm công việc mới..."
            value={newTaskTitle} onChange={(e) => setNewTaskTitle(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && handleQuickAdd()} />
          <button className="btn btn-primary" onClick={handleQuickAdd}>Thêm</button>
        </div>
        <div className="p-4 bg-white rounded shadow">
          <h2 className="h4 mb-4 fw-bold">Danh sách công việc</h2>
          <table className="table">
            <thead>
              <tr>
                <th>Ngày</th><th>Ca</th><th>Loại</th><th>Tên task</th><th>Người phụ trách</th>
                <th>Ưu tiên</th><th>Tiến độ</th><th>Chi tiết</th><th>File</th><th>Hành động</th><th>Trạng thái</th>
              </tr>
            </thead>
            <tbody>
              {currentTasks.map(task => (
                <tr key={task.id} className={task.status === 'Đã hoàn thành' ? 'opacity-50' : ''}>
                  <td>{task.task_date}</td>
                  <td>{task.shift}</td>
                  <td>{task.type}</td>
                  <td>{task.title}</td>
                  <td>{task.supervisor}</td>
                  <td>{task.priority}</td>
                  <td>{task.progress}</td>
                  <td>{task.detail}</td>
                  <td>
                    {task.file_link ? task.file_link.split(',').map((link, i) => (
                      <a key={i} href={link.trim()} target="_blank" rel="noopener noreferrer">
                        Link {i + 1}<br />
                      </a>
                    )) : '-'}
                  </td>
                  <td>
                    <button className="btn btn-warning btn-sm me-1" onClick={() => setEditingTask(task)}>Sửa</button>
                    <button className="btn btn-danger btn-sm" disabled={task.status === 'Đã hoàn thành'} onClick={() => handleDelete(task.id)}>Xoá</button>
                  </td>
                  <td>
                    <input className="switch" type="checkbox" checked={task.status === 'Đã hoàn thành'} onChange={() => handleToggle(task)} />
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          <div className="d-flex justify-content-between align-items-center mt-3">
            <span>Trang {currentPage} / {totalPages}</span>
            <div>
              <button className="btn btn-sm btn-outline-primary me-2" disabled={currentPage === 1} onClick={() => setCurrentPage(p => p - 1)}>&laquo; Trước</button>
              <button className="btn btn-sm btn-outline-primary" disabled={currentPage === totalPages} onClick={() => setCurrentPage(p => p + 1)}>Sau &raquo;</button>
            </div>
          </div>
        </div>
      </div>
  </div>
  );
}
