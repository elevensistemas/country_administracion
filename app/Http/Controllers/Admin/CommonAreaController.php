<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommonArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommonAreaController extends Controller
{
    public function index()
    {
        $commonAreas = CommonArea::orderBy('name')->get();
        return view('admin.common-areas.index', compact('commonAreas'));
    }

    public function create()
    {
        return view('admin.common-areas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'requires_approval' => 'boolean',
            'rules' => 'nullable|string',
            'schedule_start' => 'required|date_format:H:i',
            'schedule_end' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:30',
            'maintenance_blocked_days' => 'nullable|array',
        ]);

        $data['requires_approval'] = $request->has('requires_approval');
        $data['maintenance_blocked_days'] = $request->input('maintenance_blocked_days', null);

        // Handle photos upload
        if ($request->hasFile('photos')) {
            $photosPaths = [];
            foreach ($request->file('photos') as $photo) {
                $photosPaths[] = $photo->store('common_areas', 'public');
            }
            $data['photos'] = $photosPaths;
        } else {
            // Default placeholder images
            $data['photos'] = ['img/common_area_placeholder.jpg'];
        }

        CommonArea::create($data);

        return redirect()->route('admin.common-areas.index')->with('success', 'Zona común creada exitosamente.');
    }

    public function edit(CommonArea $commonArea)
    {
        return view('admin.common-areas.edit', compact('commonArea'));
    }

    public function update(Request $request, CommonArea $commonArea)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'requires_approval' => 'boolean',
            'rules' => 'nullable|string',
            'schedule_start' => 'required|date_format:H:i',
            'schedule_end' => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:30',
            'maintenance_blocked_days' => 'nullable|array',
        ]);

        $data['requires_approval'] = $request->has('requires_approval');
        $data['maintenance_blocked_days'] = $request->input('maintenance_blocked_days', null);

        if ($request->hasFile('photos')) {
            $photosPaths = [];
            foreach ($request->file('photos') as $photo) {
                $photosPaths[] = $photo->store('common_areas', 'public');
            }
            $data['photos'] = $photosPaths;
        }

        $commonArea->update($data);

        return redirect()->route('admin.common-areas.index')->with('success', 'Zona común actualizada exitosamente.');
    }

    public function destroy(CommonArea $commonArea)
    {
        $commonArea->delete();
        return redirect()->route('admin.common-areas.index')->with('success', 'Zona común eliminada exitosamente.');
    }
}
