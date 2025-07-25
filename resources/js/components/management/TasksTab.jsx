import React, { useEffect, useState } from "react";
import Swal from "sweetalert2";

const PRIORITY_OPTIONS = ["Low", "Normal", "High"];
const STATUS_OPTIONS = ["Chưa hoàn thành", "Đã hoàn thành"];

export default function TasksTab() {
  const [tasks, setTasks] = useState([]);
  const [form, setForm] = useState({
    title: "",
    task_date: "",
    priority: "Normal",
    status: "Chưa hoàn thành",
    progress: 0,
  });
  const [editing, setEditing] = useState(null);

  const csrf =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ||
    "";

  // Load tất cả task
  useEffect(() => {
    fetch("/management/tasks", { headers: { Accept: "application/json" } })
      .then((res) => res.json())
      .then(setTasks)
      .catch(() => Swal.fire("Lỗi", "Không tải được danh sách task", "error"));
  }, []);

  const resetForm = () => {
    setForm({
      title: "",
      task_date: "",
      priority: "Normal",
      status: "Chưa hoàn thành",
      progress: 0,
    });
    setEditing(null);
  };

  const handleSave = async () => {
    // dùng POST + _method PUT khi sửa để tránh 405
    const url = editing
      ? `/management/tasks/${editing.id}`
      : "/management/tasks";

    const payload = editing
      ? { ...form, _method: "PUT" }
      : { ...form };

    try {
      const res = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrf,
          Accept: "application/json",
        },
        body: JSON.stringify(payload),
      });

      if (!res.ok) {
        const msg = await res.text();
        throw new Error(msg);
      }

      const task = await res.json();

      if (editing) {
        setTasks((prev) => prev.map((t) => (t.id === task.id ? task : t)));
        Swal.fire("Thành công", "Đã cập nhật task", "success");
      } else {
        setTasks((prev) => [task, ...prev]);
        Swal.fire("Thành công", "Đã tạo task", "success");
      }

      resetForm();
    } catch (err) {
      Swal.fire("Lỗi", "Không thể lưu task", "error");
    }
  };

  const handleEdit = (task) => {
    setEditing(task);
    setForm({
      title: task.title,
      task_date: task.task_date,
      priority: task.priority,
      status: task.status,
      progress: task.progress ?? 0,
    });
  };

  const handleDelete = async (id) => {
    const confirm = await Swal.fire({
      title: "Bạn chắc chắn xoá?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Xoá",
    });
    if (!confirm.isConfirmed) return;

    try {
      const res = await fetch(`/management/tasks/${id}`, {
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": csrf },
      });
      if (!res.ok) throw new Error();
      setTasks((prev) => prev.filter((t) => t.id !== id));
      Swal.fire("Thành công", "Đã xoá task!", "success");
    } catch {
      Swal.fire("Lỗi", "Không thể xoá task", "error");
    }
  };

  return (
    <div className="p-3">
      <h3>Quản lý công việc</h3>

      {/* FORM */}
      <div className="card p-3 mb-4">
        <div className="row g-2">
          <div className="col-md-3">
          <input
              type="date"
              className="form-control"
              placeholder="Ngày"
              value={form.task_date}
              onChange={(e) => setForm({ ...form, task_date: e.target.value })}
            />
          </div>

          <div className="col-md-3">
            <input
              type="text"
              className="form-control"
              placeholder="Tên công việc"
              value={form.title}
              onChange={(e) => setForm({ ...form, title: e.target.value })}
            />
          </div>

          <div className="col-md-2">
            <select
              className="form-select"
              value={form.priority}
              onChange={(e) => setForm({ ...form, priority: e.target.value })}
            >
              {PRIORITY_OPTIONS.map((p) => (
                <option key={p} value={p}>
                  {p}
                </option>
              ))}
            </select>
          </div>

          <div className="col-md-2">
            <select
              className="form-select"
              value={form.status}
              onChange={(e) => setForm({ ...form, status: e.target.value })}
            >
              {STATUS_OPTIONS.map((s) => (
                <option key={s} value={s}>
                  {s}
                </option>
              ))}
            </select>
          </div>

          <div className="col-md-1">
            <input
              type="number"
              className="form-control"
              placeholder="%"
              min={0}
              max={100}
              value={form.progress}
              onChange={(e) =>
                setForm({ ...form, progress: Number(e.target.value) })
              }
            />
          </div>

          <div className="col-md-1 d-grid">
            <button className="btn btn-primary" onClick={handleSave}>
              {editing ? "Cập nhật" : "Thêm"}
            </button>
            {editing && (
              <button className="btn btn-secondary mt-2" onClick={resetForm}>
                Huỷ
              </button>
            )}
          </div>
        </div>
      </div>

      {/* TABLE */}
      <table className="table table-bordered table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Ngày</th>
            <th>Tên task</th>
            <th>Ưu tiên</th>
            <th>Trạng thái</th>
            <th>Tiến độ</th>
            <th width="140">Hành động</th>
          </tr>
        </thead>
        <tbody>
          {tasks.map((t, i) => (
            <tr key={t.id}>
              <td>{i + 1}</td>
              <td>{t.task_date}</td>
              <td>{t.title}</td>
              <td>{t.priority}</td>
              <td>{t.status}</td>
              <td>{t.progress ?? 0}%</td>
              <td>
                <button
                  className="btn btn-warning btn-sm me-2"
                  onClick={() => handleEdit(t)}
                >
                  Sửa
                </button>
                <button
                  className="btn btn-danger btn-sm"
                  onClick={() => handleDelete(t.id)}
                >
                  Xoá
                </button>
              </td>
            </tr>
          ))}

          {tasks.length === 0 && (
            <tr>
              <td colSpan="7" className="text-center">
                Không có công việc nào
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}
