<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use Illuminate\Http\Request;

class DirectionController extends Controller
{
    public function index()
    {
        return Direction::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $direction = Direction::create($validated);

        return response()->json($direction, 201);
    }

    public function show(Direction $direction)
    {
        return $direction;
    }

    public function update(Request $request, Direction $direction)
    {
        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $direction->update($validated);

        return response()->json($direction);
    }

    public function destroy(Direction $direction)
    {
        $direction->delete();

        return response()->json(null, 204);
    }
}