<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\shared\FilesController;
use App\Http\Controllers\Api\V1\shared\ResponseController;
use App\Http\Controllers\Api\V1\shared\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NewsResource;
use App\Http\Resources\V1\PositionResource;
use App\Http\Resources\V1\ProductResource;
use App\Models\News;
use App\Models\Product;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $name = $request->query('filterText', '');
            $limit = $request->query('limit', 20);

            $products = Product::where(function ($query) use ($name) {
                $query->where('title', 'like', '%' . $name . '%')
                    ->orWhere('description', 'like', '%' . $name . '%');
            })
                ->where('productState_id', '=', 1)
                ->latest()
                ->paginate($limit);

            return ResponseController::success([
                'products' => ProductResource::collection($products),
                'paginate' => [
                    'currentPage' => $products->currentPage(),
                    'totalPage' => $products->lastPage(),
                    'total' => $products->total(),
                    'nextPageUrl' => $products->nextPageUrl(),
                    'prevPageUrl' => $products->previousPageUrl(),
                ]
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            ResponseController::error();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'title' => 'required|string|max:100',
                'description' => 'nullable|string',
                'image' => 'required|string',
                'images' => 'nullable|array',
                'videos' => 'nullable|array',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("No se pudo registrar, revise los datos", 400, $validator);
            }

            $imageName = FilesController::saveFile($request->image, "images");

            $news = News::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'image' => $imageName,
                'user_id' => Auth::user()->id,
            ]);

            if (isset($request->images)) {
                FilesController::saveImages($request->images, $news->id);
            }

            if (isset($request->videos)) {
                FilesController::saveVideos($request->videos, $news->id);
            }

            return ResponseController::success([
                'news' => new NewsResource($news),
            ], "Registro éxitoso", 201);
        } catch (QueryException $e) {
            Log::error("Registrar noticia -> " . $e->getMessage());
            if ($e->getCode() === '23000') {
                return ResponseController::error("Noticia ya registrada", 409);
            } else {
                return ResponseController::error("Error al intentar registrar, revisa los datos", 400);
            }
        } catch (Exception $e) {
            Log::error("Registrar noticia -> " . $e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $news = News::where(array("id" => $id))
                ->first();

            if (!$news) {
                return ResponseController::success("Noticia no encontrada");
            }

            return ResponseController::success(new NewsResource($news));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request['id'] = $id;
            $request['newsState_id'] = $request->newsStateId;
            $rules = [
                'id' => 'required|numeric|exists:news',
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'newsState_id' => 'nullable|numeric|exists:news_states,id',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("La noticia no se pudo actualizar", 400, $validator);
            }

            $news = News::find($id);

            if (isset($request->title)) {
                $news->title = $request->title;
                $news->slug = Str::slug($request->title);
            }

            if (isset($request->description)) {
                $news->description = $request->description;
            }

            if (isset($request->newsState_id)) {
                $news->newsState_id = $request->newsState_id;
            }

            if (isset($request->image)) {
                $imageName = FilesController::saveFile($request->image, "images");
                $news->image = $imageName;
            }

            $news->update();

            return ResponseController::success(new NewsResource($news), "Cargo actualizado", 200);
        } catch (QueryException $e) {
            Log::error("Actualizar noticia -> " . $e->getMessage());
            if ($e->getCode() === '23000') {
                return ResponseController::error("Noticia ya registrada", 409);
            } else {
                return ResponseController::error("Error al intentar actualizar, revisa los datos", 400);
            }
        } catch (Exception $e) {
            Log::error("Registrar noticia -> " . $e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $news = News::find($id);

            if (!$news || $news->newsState_id == 2) { // eliminado
                return ResponseController::success(null, "Noticia no encontrada");
            }

            $news->newsState_id = 2;
            $news->save();

            $message = "Noticia eliminada";
            Log::info($message);
            return ResponseController::success(new NewsResource($news), $message);
        } catch (Exception $e) {
            Log::error("Eliminar noticia -> " . $e->getMessage());
            return ResponseController::error();
        }
    }
}
