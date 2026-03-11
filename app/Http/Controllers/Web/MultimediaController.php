<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MusicianMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MultimediaController extends Controller
{
    /**
     * Upload an image or video to the musician's profile.
     */
    public function upload(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'musico' || !$user->musicianProfile) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,webp,mp4,mov|max:102400', // max 100MB
            'title' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        
        $isPhoto = in_array($extension, ['jpeg', 'jpg', 'png', 'webp']);
        $type = $isPhoto ? 'photo' : 'video';

        // Check limits: max 15 photos, max 5 videos (example limit, adjust if needed)
        $currentCount = $user->musicianProfile->media()->where('type', $type)->count();
        if ($isPhoto && $currentCount >= 20) {
            return response()->json(['error' => 'Límite de fotos alcanzado (20).'], 422);
        }
        if (!$isPhoto && $currentCount >= 5) {
            return response()->json(['error' => 'Límite de videos alcanzado (5).'], 422);
        }

        $path = $file->store('musician_media', 'public');

        $media = $user->musicianProfile->media()->create([
            'type' => $type,
            'path' => $path,
            'title' => $request->title,
        ]);

        return response()->json([
            'message' => 'Archivo subido exitosamente.',
            'media' => [
                'id' => $media->id,
                'type' => $media->type,
                'url' => $media->url(),
                'title' => $media->title,
                'is_featured' => $media->is_featured,
            ]
        ], 201);
    }

    /**
     * Remove the specified media.
     */
    public function destroy(Request $request, MusicianMedia $media)
    {
        $user = $request->user();
        if ($user->role !== 'musico' || $media->musician_profile_id !== $user->musicianProfile->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if (Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        return response()->json(['message' => 'Eliminado exitosamente.']);
    }

    /**
     * Set a video as featured.
     */
    public function setFeatured(Request $request, MusicianMedia $media)
    {
        $user = $request->user();
        if ($user->role !== 'musico' || $media->musician_profile_id !== $user->musicianProfile->id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if ($media->type !== 'video') {
            return response()->json(['error' => 'Sólo los videos pueden destacarse.'], 422);
        }

        // Unfeature all other videos for this user
        $user->musicianProfile->media()->where('type', 'video')->update(['is_featured' => false]);

        $media->update(['is_featured' => true]);

        return response()->json(['message' => 'Video destacado actualizado.']);
    }
}
