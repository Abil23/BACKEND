<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Str;

class GameController extends Controller
{
    /**
     * 1. GET: List Games (Public)
     * URL: /api/v1/games
     */
    public function index(Request $request)
    {
        $sortBy = $request->query('sortBy', 'title'); 
        $sortDir = $request->query('sortDir', 'asc'); 

        $games = Game::with(['createdBy', 'latestVersion'])
                     ->orderBy($sortBy, $sortDir)
                     ->paginate(10); 

        return response()->json([
            'page' => $games->currentPage(),
            'size' => $games->perPage(),
            'totalElements' => $games->total(),
            'totalPages' => $games->lastPage(),
            'content' => $games->items()
        ], 200);
    }

    /**
     * 2. POST: Create Game (Private - Harus Login)
     * URL: /api/v1/games
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|unique:games,title', 
            'description' => 'required|min:10',
        ]);

        $slug = Str::slug($request->title);

        if (Game::where('slug', $slug)->exists()) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Game title already exists (slug conflict).'
            ], 422);
        }

        $game = Game::create([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'created_by' => $request->user()->id, 
        ]);

        return response()->json([
            'status' => 'success',
            'slug' => $game->slug,
            'message' => 'Game created successfully'
        ], 201);
    }

    /**
     * 3. GET: Detail Game (Public)
     * URL: /api/v1/games/{slug}
     */
    public function show($slug)
    {
        $game = Game::with(['createdBy', 'versions'])->where('slug', $slug)->first();

        if (!$game) {
            return response()->json(['status' => 'not_found', 'message' => 'Game not found'], 404);
        }

        return response()->json($game, 200);
    }

    /**
     * 4. PUT: Update Game (Private - Harus Pemilik)
     * URL: /api/v1/games/{slug}
     */
    public function update(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->first();

        if (!$game) {
            return response()->json(['status' => 'not_found', 'message' => 'Game not found'], 404);
        }

        if ($request->user()->id !== $game->created_by) {
            return response()->json(['status' => 'forbidden', 'message' => 'You are not the author'], 403);
        }

        $request->validate([
            'title' => 'min:3|unique:games,title,' . $game->id, 
            'description' => 'min:10',
        ]);

        if ($request->has('title')) {
            $game->title = $request->title;
            $game->slug = Str::slug($request->title);
        }
        
        if ($request->has('description')) {
            $game->description = $request->description;
        }

        $game->save();

        return response()->json([
            'status' => 'success',
            'slug' => $game->slug,
            'message' => 'Game updated successfully'
        ], 200);
    }

    /**
     * 5. DELETE: Hapus Game (Private - Harus Pemilik)
     * URL: /api/v1/games/{slug}
     */
    public function destroy(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->first();

        if (!$game) {
            return response()->json(['status' => 'not_found', 'message' => 'Game not found'], 404);
        }

        if ($request->user()->id !== $game->created_by) {
            return response()->json(['status' => 'forbidden', 'message' => 'You are not the author'], 403);
        }

        $game->delete();

        return response()->json(['status' => 'success', 'message' => 'Game deleted'], 204);
    }

    /**
     * 6. POST: Upload Versi Game (Private - Harus Pemilik)
     * URL: /api/v1/games/{slug}/upload
     */
    public function uploadVersion(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->first();

        if (!$game) {
            return response()->json(['status' => 'not_found', 'message' => 'Game not found'], 404);
        }

        if ($request->user()->id !== $game->created_by) {
            return response()->json(['status' => 'forbidden', 'message' => 'You are not the author'], 403);
        }

        // Validasi File
        $request->validate([
            'version' => 'required', // Contoh: v1.0
            'file' => 'required|file|mimes:zip,rar|max:102400', // Wajib ZIP/RAR, Max 100MB
        ]);

        // Proses Upload
        $path = "";
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Simpan ke storage/app/public/games
            $path = $file->store('games', 'public'); 
        }

        // Simpan ke Database Versions
        $game->versions()->create([
            'version' => $request->version,
            'storage_path' => $path,
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'Game version uploaded successfully'
        ], 201);
    }
}