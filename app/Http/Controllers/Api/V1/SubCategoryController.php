<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\shared\ResponseController;
use App\Http\Controllers\Api\V1\shared\ValidateController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SubCategoryResource;
use App\Models\SubCategory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
        // $travelRoute = SubCategory::where("isDeleted", "=", "N")->get();
        // return ResponseController::success(SubCategoryResource::collection($travelRoute));

        $name = $request->query('filterText');
        $limit = $request->query('limit', 20);

        $subCategories = SubCategory::where('name', 'like', '%' . $name . '%')
            ->where('isDeleted', '=', "N")
            ->latest()
            ->paginate($limit);

        return ResponseController::success([
            'subCategories' => SubCategoryResource::collection($subCategories),
            'paginate' => [
                'currentPage' => $subCategories->currentPage(),
                'totalPage' => $subCategories->lastPage(),
                'total' => $subCategories->total(),
                'nextPageUrl' => $subCategories->nextPageUrl(),
                'prevPageUrl' => $subCategories->previousPageUrl(),
            ]
        ]);
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
                return ResponseController::error("La subcategoria turistica no se pudo registrar", 400, $validator);
            }

            $slug = Str::slug($request->name);

            $productCategory = SubCategory::where("slug", "=", $slug)->first();

            if ($productCategory) {
                return ResponseController::success(null, "La subcategoria turistica ya existe", 409);
            }

            $travelRoute = new SubCategory($request->all());
            $travelRoute->slug = $slug;
            $travelRoute->isDeleted = "N";
            $travelRoute->save();

            return ResponseController::success(new SubCategoryResource($travelRoute), "Subcategoria registrada correctamente", 201);
        } catch (Exception $e) {
            return ResponseController::error();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $travelRoute = SubCategory::where(array("slug" => $slug))
            ->first();

        if ($travelRoute) {
            return ResponseController::success(new SubCategoryResource($travelRoute));
        }

        return ResponseController::success(null, "Subcategoria no encontrada");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request['id'] = $id;
            $rules = [
                'id' => 'required|numeric|exists:sub_categories',
                'name' => 'required|string',
            ];

            $validator = ValidateController::validate($request, $rules);

            if ($validator != null) {
                return ResponseController::error("La subcategoria no se pudo actualizar", 400, $validator);
            }

            $travelRoute = SubCategory::find($id);
            $travelRoute->slug = Str::slug($request->name);

            $isRegistred = SubCategory::where("slug", "=", $travelRoute->slug)->first();

            if ($isRegistred) {
                return ResponseController::success(null, "La subcategoria ya existe", 409);
            }

            $travelRoute->name = $request->name;
            $travelRoute->update();

            return ResponseController::success(new SubCategoryResource($travelRoute), "Subcategoria actualizada", 200);
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
            $travelRoute = SubCategory::find($id);

            if ($travelRoute == null) {
                return ResponseController::error("La subcategoria no existe", 404);
            }

            if ($travelRoute->isDeleted == "S") {
                return ResponseController::error("La categoria ya esta elimida", 404);
            }

            $travelRoute->isDeleted = "S";
            $travelRoute->update();

            return ResponseController::success(null, "Subcategoria eliminada con éxito");
        } catch (\Throwable $th) {
            return ResponseController::error();
        }
    }
}
