import React, { useState } from 'react';
import ExportModal from '../components/ExportModal';

const getCsrfToken = () => {
  const el = document.head.querySelector('meta[name="csrf-token"]');
  if (!el) {
    console.error("⛔ CSRF token meta tag không tồn tại!");
    return "";
  }
  return el.content;
};

const KpiPage = ({ initialKpis, filters }) => {
  const [kpis, setKpis] = useState(initialKpis);
  const csrfToken = getCsrfToken();

  const updateStatus = async (id, newStatus) => {
    try {
      const response = await fetch(`/kpis/${id}/status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ status: newStatus }),
      });

      if (!response.ok) throw new Error();

      setKpis(kpis.map(k => k.id === id ? { ...k, status: newStatus } : k));
    } catch {
      alert('Lỗi khi cập nhật trạng thái KPI!');
    }
  };

  return (
    <div>
      <h2>Danh sách KPI</h2>

      {/* Bộ lọc */}
      <form method="GET" className="row g-2 mb-3">
        <div className="col-auto">
          <input type="date" name="start_date" className="form-control" defaultValue={filters.start_date} />
        </div>
        <div className="col-auto">
          <input type="date" name="end_date" className="form-control" defaultValue={filters.end_date} />
        </div>
        <div className="col-auto">
          <button className="btn btn-primary">Lọc</button>
          <a href="/kpis" className="btn btn-secondary">Xoá lọc</a>
        </div>
      </form>

      <div className="mb-3">
        <a href="/kpis/create" className="btn btn-success">Thêm KPI</a>
        <button className="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exportKPIModal">Xuất Excel</button>
      </div>

      <table className="table">
        <thead>
          <tr>
            <th>Ngày bắt đầu</th>
            <th>Ngày đến hạn</th>
            <th>Tên Deadline</th>
            <th>Tiến độ</th>
            <th>Chi tiết</th>
            <th>Hành động</th>
            <th>Trạng thái</th>
          </tr>
        </thead>
        <tbody>
          {kpis.map(kpi => (
            <tr key={kpi.id} className={kpi.status === 'Đã hoàn thành' ? 'opacity-50' : ''}>
              <td>{kpi.start_date}</td>
              <td>{kpi.end_date}</td>
              <td>{kpi.name}</td>
              <td>{kpi.calculated_progress}%</td>
              <td>
                <a href={`/kpis/${kpi.id}`} className="btn btn-primary btn-sm">Chi tiết</a>
              </td>
              <td>
                <a
                  href={`/kpis/${kpi.id}/edit`}
                  className="btn btn-warning btn-sm me-1"
                  disabled={kpi.status === 'Đã hoàn thành'}
                >
                  Sửa
                </a>
                <form action={`/kpis/${kpi.id}`} method="POST" style={{ display: 'inline' }}>
                  <input type="hidden" name="_method" value="DELETE" />
                  <input type="hidden" name="_token" value={csrfToken} />
                  <button
                    className="btn btn-danger btn-sm"
                    disabled={kpi.status === 'Đã hoàn thành'}
                    onClick={(e) => {
                      if (!confirm('Xoá deadline này?')) e.preventDefault();
                    }}
                  >
                    Xoá
                  </button>
                </form>
              </td>
              <td>
                <div className="toggler">
                  <input
                    id={`kpi-toggle-${kpi.id}`}
                    type="checkbox"
                    onChange={(e) =>
                      updateStatus(kpi.id, e.target.checked ? 'Đã hoàn thành' : 'Chưa hoàn thành')
                    }
                    checked={kpi.status === 'Đã hoàn thành'}
                  />
                  <label htmlFor={`kpi-toggle-${kpi.id}`}>
                    <svg className="toggler-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                      <polyline className="path check" points="100.2,40.2 51.5,88.8 29.8,67.5" />
                    </svg>
                    <svg className="toggler-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                      <line className="path line" x1="34.4" y1="34.4" x2="95.8" y2="95.8" />
                      <line className="path line" x1="95.8" y1="34.4" x2="34.4" y2="95.8" />
                    </svg>
                  </label>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      <ExportModal
        modalId="exportKPIModal"
        allUrl="/kpis/export?type=all"
        filteredUrl={`/kpis/export?type=filtered&start_date=${filters.start_date}&end_date=${filters.end_date}`}
      />
    </div>
  );
};

export default KpiPage;
