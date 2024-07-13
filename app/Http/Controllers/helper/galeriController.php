<?php

namespace App\Http\Controllers\helper;

use App\Http\Controllers\Controller;
use App\Models\image_galeri;
use Illuminate\Http\Request;

class galeriController extends Controller
{
    public function image_create(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
            'private' => 'boolean',
        ]);

        $image = $request->file('image');
        $name = auth()->id() . '_' . now()->timestamp;

        $imageModel = new image_galeri();
        $imageModel->name = $name;
        $imageModel->private = $request->private ?? false;
        $imageModel->image = file_get_contents($image->getRealPath());
        $imageModel->save();

        return response()->json(['link' => route('images.read', $imageModel->id)]);
    }

    // Function to read the image from the database
    public function image_read($id)
    {
        $image = image_galeri::findOrFail($id);
        return response($image->image)->header('Content-Type', 'image/jpeg');
    }

    // Function to update the image in the database
    public function image_update(Request $request, $id)
    {
        $request->validate([
            'image' => 'image',
            'private' => 'boolean',
        ]);

        $image = image_galeri::findOrFail($id);

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $image->image = file_get_contents($imageFile->getRealPath());
        }

        if ($request->has('private')) {
            $image->private = $request->private;
        }

        $image->save();

        return response()->json(['message' => 'Image updated successfully']);
    }

    // Function to delete the image from the database
    public function image_delete($id)
    {
        $image = image_galeri::findOrFail($id);
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }
}
