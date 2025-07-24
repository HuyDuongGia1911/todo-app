// components/AsyncDropdownSelect.js
import React, { useEffect, useState } from 'react';
import Select from 'react-select';

export default function AsyncDropdownSelect({ label, name, field, api, value, onChange }) {
  const [options, setOptions] = useState([]);

  useEffect(() => {
    fetch(api)
      .then(res => res.json())
      .then(data => {
        const mapped = data.map(item => ({
          value: item[field],
          label: item[field]
        }));
        setOptions(mapped);
      })
      .catch(err => console.error(`Lỗi khi tải ${label}:`, err));
  }, [api]);

  const handleChange = (selected) => {
    onChange({
      target: {
        name,
        value: selected?.value || ''
      }
    });
  };

  return (
    <div className="mb-2">
      <label className="form-label">{label}</label>
      <Select
        value={value ? { value, label: value } : null}
        onChange={handleChange}
        options={options}
        placeholder="-- Chọn --"
        isClearable
        styles={{
          menu: (base) => ({
            ...base,
            zIndex: 9999,
          }),
        }}
      />
    </div>
  );
}
