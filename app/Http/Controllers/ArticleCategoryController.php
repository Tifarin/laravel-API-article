<?php

namespace App\Http\Controllers;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleCategoryController extends Controller
{
    //
    public function index()
    {
        $articleCategories = ArticleCategory::all();
        return response()->json([
            'data' => $articleCategories,
            'message' => 'Success'
        ], 200);
    }

    public function show($id)
    {
        $articleCategory = ArticleCategory::find($id);
        if ($articleCategory) {
            return response()->json([
                'data' => $articleCategory,
                'message' => 'Success'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Article category not found'
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $articleCategory = new ArticleCategory;
        $articleCategory->name = $request['name'];
        $articleCategory->slug = Str::slug($request['name'], '-');
        $articleCategory->save();

        return response()->json([
            'data' => $articleCategory,
            'message' => 'Article category created successfully'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $articleCategory = ArticleCategory::findOrFail($id);

        $articleCategory->name = $request['name'];
        $articleCategory->slug = Str::slug($request['name'], '-');
        $articleCategory->save();

        return response()->json([
            'data' => $articleCategory,
            'message' => 'Article category updated successfully'
        ], 200);
    }


    public function destroy($id)
    {
        $articleCategory = ArticleCategory::findOrFail($id);

        $articleCategory->delete();

        return response()->json([
            'message' => 'Article category deleted successfully'
        ], 204);
    }

}
