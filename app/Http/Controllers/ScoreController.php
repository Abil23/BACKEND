<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Score;

class ScoreController extends Controller
{
    /**
     * 1. GET: Leaderboard (Lihat Skor Tertinggi)
     * URL: /api/v1/games/{slug}/scores
     */
    public function index($slug)
    {
        $game = Game::where('slug', $slug)->first();

        if (!$game) {
            return response()->json(['status' => 'not_found', 'message' => 'Game not found'], 404);
        }

        // Ambil skor, urutkan dari terbesar ke terkecil (Highscore)
        $scores = Score::with('user')
            ->where('game_id', $game->id)
            ->orderBy('score', 'desc')
            ->get();

        // Format datanya biar rapi (Username user ditampilkan)
        $formattedScores = $scores->map(function ($score) {
            return [
                'username' => $score->user->username,
                'score' => $score->score,
                'timestamp' => $score->created_at
            ];
        });

        return response()->json([
            'game' => $game->title,
            'scores' => $formattedScores
        ], 200);
    }

    /**
     * 2. POST: Submit Score (Simpan Skor Baru)
     * URL: /api/v1/games/{slug}/scores
     */
    public function store(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->first();

        if (!$game) {
            return response()->json(['status' => 'not_found', 'message' => 'Game not found'], 404);
        }

        $request->validate([
            'score' => 'required|numeric|min:0'
        ]);

        // Simpan Skor
        Score::create([
            'user_id' => $request->user()->id, // Ambil dari Token
            'game_id' => $game->id,
            'score' => $request->score
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Score submitted successfully'
        ], 201);
    }
}