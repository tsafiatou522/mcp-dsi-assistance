<?php

namespace App\Http\Controllers;

use App\Models\Bureau;
use Illuminate\Http\Request;

class BureauController extends Controller
{
    public function index()
    {
        return Bureau::with('direction')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'direction_id' => 'required|exists:directions,id',
        ]);

        $bureau = Bureau::create($validated);

        return response()->json($bureau, 201);
    }

    public function show(Bureau $bureau)
    {
        return $bureau->load('direction');
    }

    public function update(Request $request, Bureau $bureau)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'direction_id' => 'sometimes|required|exists:directions,id',
        ]);

        $bureau->update($validated);

        return response()->json($bureau);
    }

    public function destroy(Bureau $bureau)
    {
        $bureau->delete();

        return response()->json(null, 204);
    }
}