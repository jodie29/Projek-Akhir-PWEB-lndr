<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price_per_kg' => 'required|numeric|min:0',
            'active' => 'sometimes|boolean',
        ]);

        $data['active'] = $request->has('active') ? (bool) $request->active : true;

        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dibuat.');
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.edit', compact('service'));
    }

    public function show($id)
    {
        // Untuk kenyamanan, langsung redirect ke halaman edit jika ingin melihat detail
        return redirect()->route('admin.services.edit', $id);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price_per_kg' => 'required|numeric|min:0',
            'active' => 'sometimes|boolean',
        ]);

        $data['active'] = $request->has('active') ? (bool) $request->active : false;

        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus.');
    }
}
