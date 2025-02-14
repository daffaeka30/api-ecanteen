<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\ResponseResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // select all, select paginate, search 
        $categories = Categories::when(request()->search, function ($query) {
            $query->where('name', 'like', '%' . request()->search . '%');
        })->latest()->paginate(10);

        // append search 
        $categories->appends(['search' => request()->search]);

        if ($categories->isEmpty()) {
            return new ResponseResource(true, 'No categories found', null, [
                'code' => 200
            ], 200);
        }

        return new ResponseResource(true,'List of categories', $categories,
            [
                'code'  => 200,
                'total_categories' => $categories->count()
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
        ]);

        try {
            $data['slug'] = Str::slug($data['name']);

            $categories = Categories::create($data);

            $categoriesResponse = [
                'uuid' => $categories->uuid,
                'name' => $categories->name,
                'slug' => $categories->slug,
            ];

            return new ResponseResource(true, 'Category created', $categoriesResponse, [
                'code' => 201
            ]);
        } catch (\Exception $e) {
            return new ResponseResource(false, $e->getMessage(), null, [
                'code' => 500
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        // select all, select paginate, search
        $categories = Categories::where('uuid', $uuid)->first();

        if (!$categories) {
            return new ResponseResource(false, 'Category not found with uuid: '. $uuid . '', null, [
                'code' => 404
            ], 404);
        }

        $categoriesResponse = [
            'uuid' => $categories->uuid,
            'name' => $categories->name,
            'slug' => $categories->slug,
        ]; 
        
        return new ResponseResource(true, 'Category with uuid: '. $uuid . '', $categoriesResponse, [
            'code' => 200,
            'updated_at' => $categories->updated_at
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255|unique:categories,name,'. $uuid . ',uuid',
        ]);

        try {
            $categories = Categories::where('uuid', $uuid)->first();

            if (!$categories) {
                return new ResponseResource(false, 'Categories not found with uuid: ' . $uuid . '', null, [
                    'code' => 404
                ], 404);
            }

            $data['slug'] = Str::slug($data['name']);

            $categories->update($data); //

            $categoriesResponse = [
                'uuid' => $categories->uuid,
                'name' => $categories->name,
                'slug' => $categories->slug
            ];

            return new ResponseResource(true, 'Category updated', $categoriesResponse, [
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            return new ResponseResource(false, $e->getMessage(), null, [
                'code' => 500
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        try {
            $categories = Categories::where('uuid', $uuid)->first();

            if (!$categories) {
                return new ResponseResource(false, 'Categories not found with uuid: ' . $uuid . '', null, [
                    'code' => 404
                ], 404);
            }

            $categories->delete();

            return new ResponseResource(true, 'Category deleted', null, [
                'code' => 200
            ], 200);
        } catch (\Exception $e) {
            return new ResponseResource(false, $e->getMessage(), null, [
                'code' => 500
            ], 500);
        }
    }
}
