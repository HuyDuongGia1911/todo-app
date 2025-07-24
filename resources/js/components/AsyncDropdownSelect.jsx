import React, { useEffect, useState } from "react";
import Select from "react-select";
import CreatableSelect from "react-select/creatable";
import { FaEdit, FaTrash } from "react-icons/fa";

export default function AsyncDropdownSelect({
  label,
  name,
  field,
  api,
  value,
  onChange,
  creatable = false,
}) {
  const [options, setOptions] = useState([]);
  const [editingId, setEditingId] = useState(null);
  const [newValue, setNewValue] = useState("");

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

  // Load dữ liệu từ API
  useEffect(() => {
    fetch(api)
      .then((res) => res.json())
      .then((data) => {
        const mapped = data.map((item) => ({
          value: item[field],
          label: item[field],
          id: item.id,
        }));
        setOptions(mapped);
      })
      .catch((err) => console.error(`Lỗi khi tải ${label}:`, err));
  }, [api, field, label]);

  const handleChange = (selected) => {
    onChange({
      target: {
        name,
        value: selected?.value || "",
      },
    });
  };

  const handleCreate = async (inputValue) => {
    try {
      const res = await fetch(api, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": csrf,
        },
        body: JSON.stringify({ [field]: inputValue }),
      });

      if (!res.ok) throw new Error("Tạo mới thất bại");

      const item = await res.json();
      const newOption = {
        value: item[field],
        label: item[field],
        id: item.id,
      };

      setOptions((prev) => [...prev, newOption]);
      handleChange({ value: newOption.value });
    } catch (err) {
      console.error("Lỗi khi tạo mới:", err);
      alert("Tạo mới thất bại!");
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm("Bạn chắc chắn xoá?")) return;

    try {
      const res = await fetch(`${api}/${id}`, {
        method: "DELETE",
        headers: {
          Accept: "application/json",
          "X-CSRF-TOKEN": csrf,
        },
      });

      if (!res.ok) throw new Error("Xoá thất bại");

      setOptions((prev) => prev.filter((o) => o.id !== id));
       onChange({
      target: {
        name,
        value: "",
      },
    });
    } catch (err) {
      console.error("Error:", err);
      alert("Xoá thất bại");
    }
  };

  const handleEdit = async (id) => {
  try {
    const res = await fetch(`${api}/${id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": csrf,
      },
      body: JSON.stringify({ [field]: newValue }),
    });

    if (!res.ok) throw new Error("Sửa thất bại");

    // Cập nhật danh sách + cập nhật value
    setOptions((prev) =>
      prev.map((o) => (o.id === id ? { ...o, label: newValue, value: newValue } : o))
    );

    onChange({
      target: {
        name,
        value: newValue, // 🔥 Cập nhật luôn giá trị đang chọn
      },
    });

    setEditingId(null);
    setNewValue("");
  } catch (err) {
    console.error("Lỗi:", err);
    alert("Sửa thất bại");
  }
};


  const SelectComponent = creatable ? CreatableSelect : Select;

  return (
    <div className="mb-2">
  <label className="form-label d-block">{label}</label>

  <div className="d-flex align-items-center">
    <div style={{ flex: 1 }}>
      {/* CHỈ hiển thị dropdown khi KHÔNG trong trạng thái sửa */}
      {editingId === null && (
        <SelectComponent
          value={value ? { value, label: value } : null}
          onChange={handleChange}
          onCreateOption={creatable ? handleCreate : undefined}
          options={options}
          placeholder="-- Chọn hoặc nhập mới --"
          isClearable
          styles={{
            menu: (base) => ({ ...base, zIndex: 9999 }),
          }}
        />
      )}
    </div>

    {/* Nút Sửa/Xoá (chỉ hiển thị khi không đang sửa) */}
    {creatable && options.length > 0 && editingId === null &&
      options.map(item =>
        value === item.value && (
          <div key={item.id} className="ms-2 d-flex align-items-center">
            <FaEdit
              role="button"
              className="text-primary me-2"
              onClick={() => {
                setEditingId(item.id);
                setNewValue(item.label);
              }}
            />
            <FaTrash
              role="button"
              className="text-danger"
              onClick={() => handleDelete(item.id)}
            />
          </div>
        )
      )}
  </div>

  {/* Khi bấm sửa, hiển thị ô sửa THAY THẾ dropdown */}
 {editingId !== null && (
  <form
    onSubmit={(e) => {
      e.preventDefault(); // ✅ tránh reload
      handleEdit(editingId);
    }}
    className="mt-2 d-flex align-items-center"
  >
    <input
      className="form-control form-control-sm me-2"
      value={newValue}
      onChange={(e) => setNewValue(e.target.value)}
    />
    <button type="submit" className="btn btn-sm btn-primary me-1">Lưu</button>
    <button type="button" className="btn btn-sm btn-secondary" onClick={() => setEditingId(null)}>Huỷ</button>
  </form>
)}

</div>

  );
}
