import React, { useEffect, useState } from "react";
import Select from "react-select";
import CreatableSelect from "react-select/creatable";
import { FaEdit, FaTrash } from "react-icons/fa";
import Swal from "sweetalert2";
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
  const getRealId = (id) =>
  typeof id === "string" && id.includes("-") ? id.split("-")[1] : id;
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

  // Load dữ liệu từ API
  useEffect(() => {
    const loadOptions = async () => {
      try {
        if (name === "supervisor") {
          const [superRes, userRes] = await Promise.all([
            fetch(api),
            fetch("/api/users"),
          ]);

          const [supervisors, users] = await Promise.all([
            superRes.json(),
            userRes.json(),
          ]);

          const mappedSupers = supervisors.map((s) => ({
            id: `super-${s.id}`,
            label: s.supervisor_name,
            value: s.supervisor_name,
            avatar: "https://www.w3schools.com/howto/img_avatar.png", // ảnh cố định
          }));

          const mappedUsers = users.map((u) => ({
            id: `user-${u.id}`,
            label: u.name,
            value: u.name,
            avatar: u.avatar ? `/storage/${u.avatar}` : "https://www.w3schools.com/howto/img_avatar.png",
          }));


          const merged = [...mappedUsers];

mappedSupers.forEach((s) => {
  const existsInUsers = mappedUsers.some((u) => u.value === s.value);
  if (!existsInUsers) {
    merged.push(s);
  }
});

setOptions(merged);
        } else {
          const res = await fetch(api);
          const data = await res.json();
          const mapped = data.map((item) => ({
            id: item.id,
            value: item[field],
            label: item[field],
          }));
          setOptions(mapped);
        }
      } catch (err) {
        console.error(`Lỗi khi tải ${label}:`, err);
      }
    };

    loadOptions();
  }, [api, field, label, name]);



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
    const result = await Swal.fire({
      title: "Bạn có chắc chắn muốn xoá?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Xoá",
      cancelButtonText: "Huỷ",
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
    });

    if (!result.isConfirmed) return;

    try {
     const res = await fetch(`${api}/${getRealId(id)}`, {
  method: "DELETE",
  headers: {
    Accept: "application/json",
    "X-CSRF-TOKEN": csrf,
  },
});
      if (!res.ok) throw new Error();

      setOptions((prev) => prev.filter((o) => o.id !== id));
      onChange({ target: { name, value: "" } });

      await Swal.fire({
        icon: "success",
        title: "Đã xoá!",
        text: "Mục đã được xoá thành công.",
        timer: 1500,
        showConfirmButton: false,
      });
    } catch {
      await Swal.fire({
        icon: "error",
        title: "Lỗi!",
        text: "Xoá thất bại!",
      });
    }
  };

  const handleEdit = async (id) => {
    try {
      const res = await fetch(`${api}/${getRealId(id)}`, {
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
              value={value ? options.find(opt => opt.value === value) : null}
              onChange={handleChange}
              onCreateOption={creatable ? handleCreate : undefined}
              options={options}
              placeholder="-- Chọn hoặc nhập mới --"
              isClearable
              styles={{
                menu: (base) => ({ ...base, zIndex: 9999 }),
              }}
              formatOptionLabel={
                name === "supervisor"
                  ? (option) => (
                    <div className="d-flex align-items-center">
                      <img
                        src={option.avatar}
                        alt=""
                        className="rounded-circle me-2"
                        width={24}
                        height={24}
                      />
                      <span>{option.label}</span>
                    </div>
                  )
                  : undefined
              }
              // 👇 Thêm dòng dưới để khung chọn cũng hiển thị ảnh
              formatValueLabel={
                name === "supervisor"
                  ? (option) => (
                    <div className="d-flex align-items-center">
                      <img
                        src={option.avatar}
                        alt=""
                        className="rounded-circle me-2"
                        width={24}
                        height={24}
                      />
                      <span>{option.label}</span>
                    </div>
                  )
                  : undefined
              }
            />

          )}
        </div>

        {/* Nút Sửa/Xoá (chỉ hiển thị khi không đang sửa) */}
     {creatable && editingId === null && (() => {
  const selectedItem = options.find(opt => opt.value === value);
  if (!selectedItem || typeof selectedItem.id !== "string" || !selectedItem.id.startsWith("super-")) return null;

  return (
    <div key={selectedItem.id} className="ms-2 d-flex align-items-center">
      <FaEdit
        role="button"
        className="text-primary me-2"
        onClick={() => {
          setEditingId(selectedItem.id);
          setNewValue(selectedItem.label);
        }}
      />
      <FaTrash
        role="button"
        className="text-danger"
        onClick={() => handleDelete(selectedItem.id)}
      />
    </div>
  );
})()}




      </div>

      {/* Khi bấm sửa, hiển thị ô sửa THAY THẾ dropdown */}
      {editingId !== null && (
       <div className="mt-2 d-flex align-items-center">
  <input
    className="form-control form-control-sm me-2"
    value={newValue}
    onChange={(e) => setNewValue(e.target.value)}
    onKeyDown={(e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        handleEdit(editingId);
      }
    }}
  />
  <button
    type="button"
    className="btn btn-sm btn-primary me-1"
    onClick={() => handleEdit(editingId)}
  >
    Lưu
  </button>
  <button
    type="button"
    className="btn btn-sm btn-secondary"
    onClick={() => setEditingId(null)}
  >
    Huỷ
  </button>
</div>

      )}

    </div>

  );
}
