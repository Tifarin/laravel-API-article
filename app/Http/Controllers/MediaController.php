<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function store(Request $request)
{
    $imageUrls = array();
    
    if ($request->hasFile('files')) {
        // Mengunggah setiap file ke direktori penyimpanan dengan nama asli file
        foreach($request->file('files') as $file) {
            $imageName = $file->getClientOriginalName();
            $file->move(public_path('uploads'), $imageName);

            // Menambahkan URL gambar ke array $imageUrls
            $imageUrl = asset('uploads/' . $imageName);
            array_push($imageUrls, $imageUrl);
        }
    } elseif ($request->hasFile('file')) {
        // Jika hanya satu file yang diunggah, mengunggah file ke direktori penyimpanan dengan nama asli file
        $imageName = $request->file('file')->getClientOriginalName();
        $request->file('file')->move(public_path('uploads'), $imageName);

        // Menambahkan URL gambar ke array $imageUrls
        $imageUrl = asset('uploads/' . $imageName);
        array_push($imageUrls, $imageUrl);
    }
    
    // Mengembalikan respons dengan URL gambar yang telah diunggah
    $success = "";
    foreach($imageUrls as $imageUrl) {
        $success .= '<p><img src="' . $imageUrl . '"></p>';
    }
    return response()->json(['success' => $success]);
}

}
