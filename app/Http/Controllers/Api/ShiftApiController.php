<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftApiController extends Controller
{
    // Lấy danh sách shifts
    public function index()
    {
        return response()->json(Shift::all());
    }

    // Thêm mới shift
    public function store(Request $request)
    {
        $request->validate([
            'shift_name' => 'required|string|max:255',
        ]);

        $shift = Shift::create([
            'shift_name' => $request->shift_name,
        ]);

        return response()->json($shift, 201);
    }

    // Sửa shift
    public function update(Request $request, $id)
    {
        $request->validate([
            'shift_name' => 'required|string|max:255',
        ]);

        $shift = Shift::findOrFail($id);
        $shift->update([
            'shift_name' => $request->shift_name,
        ]);

        return response()->json($shift);
    }

    // Xoá shift
    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return response()->json(['success' => true]);
    }
}
