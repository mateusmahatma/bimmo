<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserGuideController extends Controller
{
    /**
     * Display the user guide index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('user-guide.index', [
            'title' => 'Panduan Pengguna'
        ]);
    }
}
