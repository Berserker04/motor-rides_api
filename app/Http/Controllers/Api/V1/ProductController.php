<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\shared\FilesController;
use App\Http\Controllers\Api\V1\shared\ResponseController;
use App\Http\Controllers\Api\V1\shared\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NewsResource;
use App\Http\Resources\V1\PositionResource;
use App\Http\Resources\V1\ProductResource;
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
                'price' => 'nullable|numeric',
                'subCategoryId' => 'required|numeric',
                'image' => 'required|string',
                'images' => 'nullable|array',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("No se pudo registrar, revise los datos", 400, $validator);
            }

            // $imageName = FilesController::saveFile($request->image, "images");

            $news = Product::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'image' => $request->image,
                'user_id' => Auth::user()->id,
                'sub_category_id' => $request->subCategoryId,
                'productState_id' => 1,
            ]);

            // if (isset($request->images)) {
            //     FilesController::saveImages($request->images, $news->id);
            // }

            // if (isset($request->videos)) {
            //     FilesController::saveVideos($request->videos, $news->id);
            // }

            return ResponseController::success([
                'products' => new ProductResource($news),
            ], "Registro éxitoso", 201);

        } catch (QueryException $e) {
            Log::error("Registrar noticia -> " . $e->getMessage());
            if ($e->getCode() === '23000') {
                return ResponseController::error("Producto ya registrado", 409);
            } else {
                return ResponseController::error("Error al intentar registrar, revisa los datos", 400);
            }
        } catch (Exception $e) {
            Log::error("Error -> registrar producto -> " . $e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::where(array("id" => $id))
                ->first();

            if (!$product) {
                return ResponseController::success("Noticia no encontrada");
            }

            return ResponseController::success(new ProductResource($product));
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
                'id' => 'required|numeric|exists:products',
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                // 'newsState_id' => 'nullable|numeric|exists:news_states,id',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("La noticia no se pudo actualizar", 400, $validator);
            }

            $product = Product::find($id);

            if (isset($request->title)) {
                $product->title = $request->title;
                $product->slug = Str::slug($request->title);
            }

            if (isset($request->description)) {
                $product->description = $request->description;
            }
            if (isset($request->price)) {
                $product->price = $request->price;
            }

            // if (isset($request->newsState_id)) {
            //     $product->newsState_id = $request->newsState_id;
            // }

            // if (isset($request->image)) {
            //     $imageName = FilesController::saveFile($request->image, "images");
            //     $product->image = $imageName;
            // }

            $product->update();

            return ResponseController::success(new ProductResource($product), "Producto actualizado", 200);
        } catch (QueryException $e) {
            Log::error("Actualizar producto -> " . $e->getMessage());
            if ($e->getCode() === '23000') {
                return ResponseController::error("Producto ya registrad0o", 409);
            } else {
                return ResponseController::error("Error al intentar actualizar, revisa los datos", 400);
            }
        } catch (Exception $e) {
            Log::error("Registrar producto -> " . $e->getMessage());
            return ResponseController::error();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $produt = Product::find($id);

            if (!$produt || $produt->productState_id == 2) { // eliminado
                return ResponseController::success(null, "Producto no encontrado");
            }

            $produt->productState_id = 2;
            $produt->save();

            $message = "Producto eliminado";
            Log::info($message);
            return ResponseController::success(new ProductResource($produt), $message);
        } catch (Exception $e) {
            Log::error("Eliminar producto -> " . $e->getMessage());
            return ResponseController::error();
        }
    }
}
