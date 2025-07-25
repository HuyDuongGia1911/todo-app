<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use Illuminate\Http\Request;

class KpiAdminController extends Controller
{
    public function index()
    {
        return Kpi::orderBy('id', 'desc')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'target'   => 'required|numeric|min:0',
            'progress' => 'nullable|numeric|min:0|max:100',
        ]);

        $kpi = Kpi::create($data);
        return response()->json($kpi);
    }

    public function update(Request $request, Kpi $kpi)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'target'   => 'required|numeric|min:0',
            'progress' => 'nullable|numeric|min:0|max:100',
        ]);

        $kpi->update($data);
        return response()->json($kpi);
    }

    public function destroy(Kpi $kpi)
    {
        $kpi->delete();
        return response()->json(['success' => true]);
    }
}
