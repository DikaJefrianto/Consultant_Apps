<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Feedback; // Impor model Feedback
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Menampilkan daftar semua feedback.
     */
    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['feedback.view']);
        $feedbacks = Feedback::latest()->paginate(10); // Ambil feedback terbaru, dengan paginasi
        return view('backend.pages.feedbacks.index', compact('feedbacks')); // Buat view ini nanti
    }

    /**
     * Menampilkan detail feedback tertentu.
     */
    public function show(Feedback $feedback)
    {
        return view('backend.pages.feedbacks.show', compact('feedback')); // Buat view ini nanti
    }

    /**
     * Menghapus feedback.
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        return redirect()->route('admin.feedbacks.index')->with('success', 'Feedback berhasil dihapus.');
    }
}
