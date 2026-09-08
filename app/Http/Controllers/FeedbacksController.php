<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Log;

class FeedbacksController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            Feedback::create($validatedData);

            return redirect()->to(route('home') . '#hubungi-kami')->with('success', 'Terima kasih! Pesan Anda telah terkirim.');
        } catch (\Exception $e) {
            Log::error('Error saving feedback: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengirim pesan Anda. Silakan coba lagi.');
        }
    }
}
