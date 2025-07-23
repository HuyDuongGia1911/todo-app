import React, { useEffect, useState } from 'react';
import { BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, Legend } from 'recharts';

export default function ChartTaskByDay() {
  const [data, setData] = useState([]);

  useEffect(() => {
    fetch('/api/dashboard/tasks-by-day')
     .then((res) => res.json())
.then(data => {
  console.log(data);
  setData(data);
})
      .catch(() => alert('Lỗi tải dữ liệu biểu đồ task theo ngày!'));
  }, []);

  return (
    <div className="mt-4">
      <h5 className="mb-3"> Công việc trong tuần</h5>
      <ResponsiveContainer width="100%" height={300}>
        <BarChart data={data}>
          <XAxis dataKey="day" />
          <YAxis allowDecimals={false} />
          <Tooltip
            formatter={(value, name) => [value, 'Số lượng']} // 👈 Đổi nhãn tại đây
          />
          <Legend />

<Bar dataKey="completed" name="Hoàn thành" fill="#198754" radius={[4, 4, 0, 0]} />
<Bar dataKey="overdue" name="Quá hạn" fill="#dc3545" radius={[4, 4, 0, 0]} />
          <Bar dataKey="count" name="Tổng" fill="#0d6efd" radius={[4, 4, 0, 0]} />

        </BarChart>
      </ResponsiveContainer>
    </div>
  );
}
