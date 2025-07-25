import React, { useEffect, useState } from "react";
import Swal from "sweetalert2";

export default function KpiTab() {
  const [kpis, setKpis] = useState([]);
  const [form, setForm] = useState({ name: "", target: "", progress: "" });
  const [editing, setEditing] = useState(null);

  const csrf = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

  // Load danh sách KPI
  useEffect(() => {
    fetch("/management/kpis", { headers: { Accept: "application/json" } })
      .then((res) => res.json())
      .then((data) => setKpis(Array.isArray(data) ? data : []))
      .catch(() => Swal.fire("Lỗi", "Không tải được danh sách KPI", "error"));
  }, []);

  const resetForm = () => {
    setForm({ name: "", target: "", progress: "" });
    setEditing(null);
  };

  // Lưu KPI
  const handleSave = async () => {
    const method = editing ? "PUT" : "POST";
    const url = editing ? `/management/kpis/${editing.id}` : "/management/kpis";

    try {
      const res = await fetch(url, {
        method,
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": csrf,
        },
        body: JSON.stringify(form),
      });

      if (!res.ok) throw new Error();
      const kpi = await res.json();

      if (editing) {
        setKpis((prev) => prev.map((k) => (k.id === kpi.id ? kpi : k)));
        Swal.fire("Thành công", "Đã cập nhật KPI", "success");
      } else {
        setKpis((prev) => [kpi, ...prev]);
        Swal.fire("Thành công", "Đã thêm KPI mới", "success");
      }

      resetForm();
    } catch {
      Swal.fire("Lỗi", "Không thể lưu KPI", "error");
    }
  };

  // Chỉnh sửa KPI
  const handleEdit = (kpi) => {
    setForm({
      name: kpi.name,
      target: kpi.target,
      progress: kpi.progress,
    });
    setEditing(kpi);
  };

  // Xóa KPI
  const handleDelete = async (id) => {
    const confirm = await Swal.fire({
      title: "Bạn có chắc chắn muốn xóa KPI này?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Xóa",
    });
    if (!confirm.isConfirmed) return;

    try {
      const res = await fetch(`/management/kpis/${id}`, {
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": csrf },
      });
      if (!res.ok) throw new Error();
      setKpis((prev) => prev.filter((k) => k.id !== id));
      Swal.fire("Thành công", "Đã xóa KPI", "success");
    } catch {
      Swal.fire("Lỗi", "Không thể xóa KPI", "error");
    }
  };

  return (
    <div className="p-3">
      <h3>Quản lý KPI</h3>

      {/* Form thêm/sửa */}
      <div className="card p-3 mb-4">
        <div className="row g-2">
          <div className="col-md-3">
            <input
              type="text"
              className="form-control"
              placeholder="Tên KPI"
              value={form.name}
              onChange={(e) => setForm({ ...form, name: e.target.value })}
            />
          </div>
          <div className="col-md-3">
            <input
              type="number"
              className="form-control"
              placeholder="Mục tiêu"
              value={form.target}
              onChange={(e) => setForm({ ...form, target: e.target.value })}
            />
          </div>
          <div className="col-md-3">
            <input
              type="number"
              className="form-control"
              placeholder="Tiến độ (%)"
              value={form.progress}
              onChange={(e) => setForm({ ...form, progress: e.target.value })}
            />
          </div>
          <div className="col-md-3">
            <button className="btn btn-primary w-100" onClick={handleSave}>
              {editing ? "Cập nhật" : "Thêm"}
            </button>
            {editing && (
              <button
                className="btn btn-secondary mt-2 w-100"
                onClick={resetForm}
              >
                Hủy
              </button>
            )}
          </div>
        </div>
      </div>

      {/* Bảng KPI */}
      <table className="table table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Tên KPI</th>
            <th>Mục tiêu</th>
            <th>Tiến độ</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          {Array.isArray(kpis) && kpis.length > 0 ? (
            kpis.map((k, i) => (
              <tr key={k.id}>
                <td>{i + 1}</td>
                <td>{k.name}</td>
                <td>{k.target}</td>
                <td>{k.progress}%</td>
                <td>
                  <button
                    className="btn btn-warning btn-sm me-2"
                    onClick={() => handleEdit(k)}
                  >
                    Sửa
                  </button>
                  <button
                    className="btn btn-danger btn-sm"
                    onClick={() => handleDelete(k.id)}
                  >
                    Xóa
                  </button>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="5" className="text-center text-muted">
                Không có KPI nào
              </td>
            </tr>
          )}
        </tbody>
      </table>
    </div>
  );
}
