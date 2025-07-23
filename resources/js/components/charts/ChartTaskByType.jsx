import React, { useEffect, useState } from 'react';
import { PieChart, Pie, Cell, Tooltip, Legend, ResponsiveContainer } from 'recharts';

const COLORS = ['#0d6efd', '#dc3545', '#ffc107', '#20c997', '#6f42c1'];

export default function ChartTaskByType() {
  const [data, setData] = useState([]);

  useEffect(() => {
    fetch('/api/dashboard/tasks-by-type')
      .then(res => res.json())
      .then(setData)
      .catch(() => alert('Lỗi tải dữ liệu loại task!'));
  }, []);

  return (
    <div className="mt-4">
      <h5 className="mb-3"> Phân loại công việc</h5>
      <ResponsiveContainer width="100%" height={300}>
        <PieChart>
          <Pie
            data={data}
            dataKey="count"
            nameKey="type"
            cx="50%"
            cy="50%"
            outerRadius={100}
            label
          >
            {data.map((entry, index) => (
              <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
            ))}
          </Pie>
          <Tooltip formatter={(value) => [`${value}`, 'Số lượng']} />
          <Legend />
        </PieChart>
      </ResponsiveContainer>
    </div>
  );
}
