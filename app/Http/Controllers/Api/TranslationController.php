<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Translation;

class TranslationController extends Controller {

    /**
 * @OA\Get(
 *     path="/api/translations",
 *     tags={"Translations"},
 *     summary="List all translations (with optional filters)",
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="tag",
 *         in="query",
 *         description="Filter by tag (e.g. mobile, web)",
 *         required=false,
 *         @OA\Schema(type="string", example="mobile")
 *     ),
 *     @OA\Parameter(
 *         name="key",
 *         in="query",
 *         description="Filter by translation key",
 *         required=false,
 *         @OA\Schema(type="string", example="greeting")
 *     ),
 *     @OA\Parameter(
 *         name="content",
 *         in="query",
 *         description="Filter by translated content",
 *         required=false,
 *         @OA\Schema(type="string", example="Welcome")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of translations",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="key", type="string", example="greeting"),
 *                 @OA\Property(property="translations", type="object",
 *                     @OA\Property(property="en", type="string", example="Hello"),
 *                     @OA\Property(property="fr", type="string", example="Bonjour")
 *                 ),
 *                 @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"mobile", "web"})
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */
    public function index(Request $request) {
        $query = Translation::query();

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        if ($request->filled('key')) {
            $query->where('key', 'like', "%{$request->key}%");
        }

        if ($request->filled('content')) {
            $query->where('translations', 'like', "%{$request->content}%");
        }

        return response()->json($query->paginate(50));
    }

/**
 * @OA\Post(
 *     path="/api/translations",
 *     tags={"Translations"},
 *     summary="Create a new translation",
 *     security={{"sanctum": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"key", "translations"},
 *             @OA\Property(property="key", type="string", example="welcome"),
 *             @OA\Property(property="translations", type="object",
 *                 @OA\Property(property="en", type="string", example="Welcome"),
 *                 @OA\Property(property="fr", type="string", example="Bienvenue")
 *             ),
 *             @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"web", "mobile"})
 *         )
 *     ),
 *     @OA\Response(response=201, description="Translation created"),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=422, description="Validation failed")
 * )
 */
    public function store(Request $request) {
        $validated = $request->validate([
            'key' => 'required|string|unique:translations,key',
            'translations' => 'required|array',
            'tags' => 'nullable|array',
        ]);

        $translation = Translation::create($validated);
        return response()->json($translation, 201);
    }

    /**
 * @OA\Put(
 *     path="/api/translations/{id}",
 *     tags={"Translations"},
 *     summary="Update an existing translation",
 *     security={{"sanctum": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Translation ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="key", type="string", example="greeting"),
 *             @OA\Property(property="translations", type="object",
 *                 @OA\Property(property="en", type="string", example="Hello"),
 *                 @OA\Property(property="fr", type="string", example="Bonjour")
 *             ),
 *             @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"mobile", "web"})
 *         )
 *     ),
 *     @OA\Response(response=200, description="Translation updated"),
 *     @OA\Response(response=404, description="Translation not found"),
 *     @OA\Response(response=401, description="Unauthenticated"),
 *     @OA\Response(response=422, description="Validation failed")
 * )
 */
    public function update(Request $request, $id) {
        $translation = Translation::findOrFail($id);

        $validated = $request->validate([
            'key' => 'required|string|unique:translations,key,' . $id,
            'translations' => 'required|array',
            'tags' => 'nullable|array',
        ]);

        $translation->update($validated);
        return response()->json($translation);
    }
/**
 * @OA\Get(
 *     path="/api/translations/export",
 *     tags={"Translations"},
 *     summary="Export all translations in JSON format",
 *     security={{"sanctum": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Translations exported successfully",
 *         @OA\JsonContent(
 *             example={
 *                 "en": {
 *                     "welcome": "Welcome",
 *                     "goodbye": "Goodbye"
 *                 },
 *                 "fr": {
 *                     "welcome": "Bienvenue",
 *                     "goodbye": "Au revoir"
 *                 }
 *             }
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated")
 * )
 */
    public function export() {
        $translations = Translation::select('key', 'translations')->get();

        $data = [];
        foreach ($translations as $item) {
            foreach ($item->translations as $locale => $text) {
                $data[$locale][$item->key] = $text;
            }
        }

        return response()->json($data);
    }
}