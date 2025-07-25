import React, { useState, useMemo } from 'react';
import Modal from '../Modal';

export default function SummaryDetailModal({ summary, onClose, onSaveContent, onRegenerate }) {
  const [editing, setEditing] = useState(false);
  const [content, setContent] = useState(summary.content || '');

 const handleExportExcel = () => {
  // Xuất theo ID của summary hiện tại
  window.open(`/summaries/${summary.id}/export`, '_blank');
};

  return (
    <Modal
      title={`Chi tiết tháng ${summary.month}`}
      onClose={onClose}
      footer={
        <>
          {editing ? (
            <>
              <button
                className="btn btn-success"
                onClick={() => onSaveContent(summary.id, content)}
              >
                Lưu
              </button>
              <button className="btn btn-secondary" onClick={() => setEditing(false)}>
                Hủy
              </button>
            </>
          ) : (
            !summary.locked_at && (
              <button className="btn btn-primary" onClick={() => setEditing(true)}>
                Sửa
              </button>
            )
          )}
          <button className="btn btn-outline-info" onClick={() => onRegenerate(summary.id)}>
            Tính lại thống kê
          </button>
           <button className="btn btn-success" onClick={handleExportExcel}>
        Xuất Excel
      </button>
        </>
      }
    >
      <h6 className="fw-bold">Nội dung tổng kết</h6>
      {editing ? (
        <textarea
          className="form-control mb-3"
          rows={6}
          value={content}
          onChange={(e) => setContent(e.target.value)}
        />
      ) : (
        <div className="mb-3" style={{ whiteSpace: 'pre-wrap' }}>
          {summary.content || 'Chưa có nội dung'}
        </div>
      )}

      <h6 className="fw-bold">Danh sách công việc trong tháng</h6>
      {summary.tasks_cache?.length ? (
        <ul className="list-group mb-3">
          {summary.tasks_cache.map((task, i) => (
            <li key={i} className="list-group-item">
              <strong>{task.title}</strong> (Tiến độ: {task.progress})<br />
              <small>Ngày: {task.dates?.join(', ')}</small>
            </li>
          ))}
        </ul>
      ) : (
        <p>Không có task nào.</p>
      )}

      {summary.stats && (
        <div className="alert alert-secondary">
          <strong>Thống kê:</strong><br />
          Tổng số task: {summary.stats.total || 0}<br />
          Đã hoàn thành: {summary.stats.done || 0}<br />
          Chưa hoàn thành: {summary.stats.pending || 0}<br />
          Quá hạn: {summary.stats.overdue || 0}
        </div>
      )}
    </Modal>
  );
}