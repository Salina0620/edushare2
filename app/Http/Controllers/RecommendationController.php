<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RecommendService;

class RecommendationController extends Controller
{
    public function index(Request $request, RecommendService $svc)
    {
        $user = $request->user();
        $notes = $svc->forUser($user->id, 12);
        return view('public.notes.recommended', compact('notes'));
    }
}
