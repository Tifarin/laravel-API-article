<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function store(Request $request)
    {
        // Validasi file yang diunggah
        $request->validate([
            'files.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $imageUrls = array();
        // Mengunggah setiap file ke direktori penyimpanan dengan nama asli file
        foreach($request->file('files') as $file) {
            $imageName = $file->getClientOriginalName();
            $file->move(public_path('uploads'), $imageName);

            // Menambahkan URL gambar ke array $imageUrls
            $imageUrl = asset('uploads/' . $imageName);
            array_push($imageUrls, $imageUrl);
        }
        
        // Mengembalikan respons dengan URL gambar yang telah diunggah
        $success = "";
        foreach($imageUrls as $imageUrl) {
            $success .= '<img src="' . $imageUrl . '">';
        }
        return response()->json(['success' => $success]);
    }

}
