import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Swal from 'sweetalert2';
import SummaryDetailModal from '../components/summaries/SummaryDetailModal';

export default function SummaryIndex() {
  const [summaries, setSummaries] = useState([]);
  const [form, setForm] = useState({ month: '', title: '', content: '' });
  const [viewing, setViewing] = useState(null);

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  useEffect(() => {
    fetch('/summaries', { headers: { Accept: 'application/json' } })
      .then(res => res.json())
      .then(setSummaries)
      .catch(() => Swal.fire('Lỗi', 'Không tải được dữ liệu!', 'error'));
  }, []);

 const handleSave = async () => {
  if (!form.month || !/^\d{4}-\d{2}$/.test(form.month)) {
    Swal.fire('Thiếu dữ liệu', 'Vui lòng chọn tháng hợp lệ (YYYY-MM)!', 'warning');
    return;
  }

  try {
    const res = await fetch('/summaries', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
      },
      body: JSON.stringify(form),
    });

    if (!res.ok) {
      const error = await res.json();
      console.error('Lỗi khi thêm báo cáo:', error);
      throw new Error(error.message || 'Lỗi không xác định');
    }

    const newSummary = await res.json();
    setSummaries(prev => [newSummary, ...prev]);
    Swal.fire('Thành công', 'Đã thêm báo cáo!', 'success');
    setForm({ month: '', title: '', content: '' });
  } catch (err) {
    Swal.fire('Lỗi', err.message || 'Không thể thêm báo cáo!', 'error');
  }
};



  const handleDelete = async (id) => {
    const confirm = await Swal.fire({
      title: 'Bạn chắc chắn xoá?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Xoá',
    });
    if (!confirm.isConfirmed) return;

    try {
      const res = await fetch(`/summaries/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf },
      });
      if (!res.ok) throw new Error();
      setSummaries(prev => prev.filter(s => s.id !== id));
      Swal.fire('Đã xoá', 'Báo cáo đã được xoá!', 'success');
    } catch {
      Swal.fire('Lỗi', 'Không thể xoá!', 'error');
    }
  };

  const handleLock = async (id) => {
    const confirm = await Swal.fire({
      title: 'Chốt báo cáo?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Chốt',
    });
    if (!confirm.isConfirmed) return;

    try {
      const res = await fetch(`/summaries/${id}/lock`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf },
      });
      if (!res.ok) throw new Error();
      const result = await res.json();
      setSummaries(prev =>
        prev.map(s => (s.id === id ? { ...s, locked_at: result.locked_at } : s))
      );
      Swal.fire('Thành công', 'Báo cáo đã được chốt!', 'success');
    } catch {
      Swal.fire('Lỗi', 'Không thể chốt!', 'error');
    }
  };

  const handleUpdateContent = async (id, content) => {
  try {
    const res = await fetch(`/summaries/${id}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
      },
      body: JSON.stringify({
        _method: 'PUT',
        content,
      }),
    });

    if (!res.ok) throw new Error();

    // ✅ Fetch lại dữ liệu đầy đủ từ show API
    const resShow = await fetch(`/summaries/${id}`);
    const updated = await resShow.json();

    setSummaries(prev => prev.map(s => (s.id === id ? updated : s)));
    setViewing(updated);
    Swal.fire('Thành công', 'Nội dung đã được cập nhật!', 'success');
  } catch {
    Swal.fire('Lỗi', 'Không thể cập nhật nội dung!', 'error');
  }
};

const handleRegenerate = async (id) => {
  try {
    const res = await fetch(`/summaries/${id}/regenerate`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf },
    });
    if (!res.ok) throw new Error();

    // ✅ Fetch lại dữ liệu đầy đủ từ show API
    const resShow = await fetch(`/summaries/${id}`);
    const updated = await resShow.json();

    setSummaries(prev => prev.map(s => (s.id === id ? updated : s)));
    setViewing(updated);
    Swal.fire('Thành công', 'Đã tính lại thống kê!', 'success');
  } catch {
    Swal.fire('Lỗi', 'Không thể tính lại thống kê!', 'error');
  }
};

const handleOpenSummary = async (id) => {
  try {
    await fetch(`/summaries/${id}/regenerate`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf },
    });

    const res = await fetch(`/summaries/${id}`);
    const data = await res.json();

    console.log('Chi tiết summary:', data); // ✅ thêm dòng này

    setViewing(data);
  } catch (err) {
    console.error(err); // ✅ log lỗi chi tiết
    Swal.fire('Lỗi', 'Không thể mở chi tiết báo cáo!', 'error');
  }
};

  return (
    <div className="p-4">
      <h2 className="mb-3">Tổng kết tháng</h2>

      <div className="mb-3 d-flex">
        <input
          type="month"
          className="form-control me-2"
          value={form.month}
          onChange={(e) => setForm({ ...form, month: e.target.value })}
        />
        <input
          type="text"
          className="form-control me-2"
          placeholder="Tiêu đề"
          value={form.title}
          onChange={(e) => setForm({ ...form, title: e.target.value })}
        />
        <button className="btn btn-primary" onClick={handleSave}>Thêm</button>
      </div>

    

      <table className="table table-bordered">
        <thead>
          <tr>
            <th>Tháng</th><th>Tiêu đề</th><th>Chốt</th><th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          {summaries.map(s => (
            <tr key={s.id}>
              <td>{s.month}</td>
              <td>{s.title}</td>
              <td>{s.locked_at || 'Chưa chốt'}</td>
              <td>
                <button
                  className="btn btn-info btn-sm me-2"
                  onClick={() => handleOpenSummary(s.id)}

                >
                  Chi tiết
                </button>
                {!s.locked_at && (
                  <>
                    <button className="btn btn-danger btn-sm me-2" onClick={() => handleDelete(s.id)}>Xoá</button>
                    <button className="btn btn-warning btn-sm" onClick={() => handleLock(s.id)}>Chốt</button>
                  </>
                )}
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {viewing && (
       <SummaryDetailModal
  isOpen={!!viewing} // ✅ Bắt buộc truyền prop này
  summary={viewing}
  onClose={() => setViewing(null)}
  onSaveContent={handleUpdateContent}
  onRegenerate={handleRegenerate}
/>
      )}
    </div>
  );
}


