<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\File;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
//        $files = File::latest()->paginate(20); // latest() == orderBy('created_at', 'desc')
        $files = File::orderByDesc('id')->paginate(20);
        return view('backend.file.index', compact('files'));
    }
}
