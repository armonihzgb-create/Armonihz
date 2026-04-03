<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MusicianMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process; // <-- IMPORTANTE: Agregar esto
use Illuminate\Support\Facades\Log; // <-- IMPORTANTE: Para registrar errores si FFmpeg falla

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

        // 1. Guardar el archivo en storage
        $path = $file->store('musician_media', 'public');
        
        // --- INICIO DE OPTIMIZACIÓN DE VIDEO (FAST START) ---
        if (!$isPhoto) {
            // Obtener la ruta absoluta del archivo en el servidor
            $fullPath = Storage::disk('public')->path($path);
            $tempPath = $fullPath . '.tmp.mp4';

            // Ejecutamos FFmpeg usando un array por seguridad en los nombres de archivo.
            // -c copy: No recodifica (super rápido).
            // -movflags +faststart: Mueve el moov atom al principio.
            $result = Process::run([
                'ffmpeg', 
                '-y', // Sobrescribir si el temporal existe
                '-i', $fullPath, 
                '-c', 'copy', 
                '-movflags', '+faststart', 
                $tempPath
            ]);

            // Si el comando fue exitoso y el archivo temporal se creó
            if ($result->successful() && file_exists($tempPath)) {
                // Reemplazamos el archivo original "roto" por el optimizado
                rename($tempPath, $fullPath);
            } else {
                // Si FFmpeg falla (ej. no está instalado en el servidor), 
                // borramos el temporal y dejamos el video original para que al menos funcione.
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
                Log::warning("No se pudo aplicar FastStart al video: " . $result->errorOutput());
            }
        }
        // --- FIN DE OPTIMIZACIÓN DE VIDEO ---

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