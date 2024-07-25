<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\shared\ResponseController;
use App\Http\Controllers\Api\V1\shared\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $name = $request->query('filterText');
        $limit = $request->query('limit', 20);

        $categories = Category::where('name', 'like', '%' . $name . '%')
            ->where('isDeleted', '=', "N")
            ->latest()
            ->paginate($limit);

        return ResponseController::success([
            'categories' => CategoryResource::collection($categories),
            'paginate' => [
                'currentPage' => $categories->currentPage(),
                'totalPage' => $categories->lastPage(),
                'total' => $categories->total(),
                'nextPageUrl' => $categories->nextPageUrl(),
                'prevPageUrl' => $categories->previousPageUrl(),
            ]
        ]);

        // $category = Category::where("isDeleted", "=", "N")->get();
        // return ResponseController::success(CategoryResource::collection($category));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'name' => 'required|string'
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("La categoria no se pudo registrar", 400, $validator);
            }

            $slug = Str::slug($request->name);

            $productCategory = Category::where("slug", "=", $slug)->first();

            if ($productCategory) {
                return ResponseController::success(null, "La categoria ya existe", 409);
            }

            $category = new Category($request->all());
            $category->slug = $slug;
            $category->isDeleted = "N";
            $category->save();

            return ResponseController::success(new CategoryResource($category), "Categoria registrada correctamente", 201);
        } catch (Exception $e) {
            return ResponseController::error();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $category = Category::where(array("slug" => $slug))
            ->first();

        if ($category) {
            return ResponseController::success(new CategoryResource($category));
        }

        return ResponseController::success(null, "Categoria no encontrada");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request['id'] = $id;
            $rules = [
                'id' => 'required|numeric|exists:categories',
                'name' => 'required|string',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("La categoria no se pudo actualizar", 400, $validator);
            }

            $category = Category::find($id);
            $category->slug = Str::slug($request->name);

            $isRegistred = Category::where("slug", "=", $category->slug)->first();

            if ($isRegistred) {
                return ResponseController::success(null, "La categoria ya existe", 409);
            }

            $category->name = $request->name;
            $category->update();

            return ResponseController::success(new CategoryResource($category), "Categoria actualizada", 200);
        } catch (\Throwable $th) {
            return ResponseController::error();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::find($id);

            if ($category == null) {
                return ResponseController::error("La categoria no existe", 404);
            }

            if ($category->isDeleted == "S") {
                return ResponseController::error("La categoria ya esta elimida", 404);
            }

            $category->isDeleted = "S";
            $category->update();

            return ResponseController::success(null, "Categoria eliminada con éxito");
        } catch (\Throwable $th) {
            return ResponseController::error();
        }
    }
}
