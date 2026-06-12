<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return view('courses.index', compact('courses'));
    }

    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    public function uploadModule(Request $request, Course $course)
    {
        // 1. Validasi input: wajib ada file, format PDF/MP4, maksimal 20MB
        $request->validate([
            'module_file' => 'required|mimes:pdf,mp4|max:20480',
        ]);

        if ($request->hasFile('module_file')) {
            $file = $request->file('module_file');
            $fileName = time() . '_' . $file->getClientOriginalName();

            try {
                // 2. Kirim file langsung ke Azure Blob Storage
                $path = Storage::disk('azure')->putFileAs(
                    'modules/' . $course->id,
                    $file,
                    $fileName
                );

                // 3. Simpan URL file ke database di kolom module_url
                $course->update([
                    'module_url' => env('AZURE_STORAGE_URL') . '/' . env('AZURE_STORAGE_CONTAINER') . '/' . $path
                ]);

                return back()->with('success', 'Modul berhasil diunggah ke Azure Cloud Storage!');
            } catch (\Exception $e) {
                // Tangkap error jika gagal terhubung ke Azure
                return back()->with('error', 'Gagal mengunggah modul: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Tidak ada file yang dipilih.');
    }
}
