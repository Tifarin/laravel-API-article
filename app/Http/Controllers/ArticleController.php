<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Article;
use App\Models\Media;
use App\Models\ArticleCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class ArticleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $page = $request->query('page', 1);
            $limit = $request->query('limit', 5);
            $categorySlug = $request->query('category');

            $category = null;
            if ($categorySlug) {
                $category = ArticleCategory::where('slug', $categorySlug)->firstOrFail();
            }

            $query = Article::query();
            
            if ($category) {
                $query->where('category_id', $category->id);
            }
            
            // Menambahkan eager loading untuk relasi media
            $articles = $query->with('media')->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'data' => $articles,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve articles',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $article = Article::with('media')->findOrFail($id);

            return response()->json([
                'data' => $article,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Article not found',
            ], 404);
        }
    }


    public function store(Request $request)
    {
        $category = ArticleCategory::find($request['category_id']);
        if (!$category) {
            return response()->json(['error' => 'Invalid category selected'], 422);
        }

        DB::beginTransaction();

        try {
            $article = new Article;
            $article->user_id = auth()->user()->id;
            $article->title = $request['title'];
            $article->body = $request['body'];
            $article->category_id = $request['category_id'];
            $article->slug = Str::slug($request['title']);
            $article->excerpt = substr(strip_tags($request['body']), 0, 200); 

            // cek apakah ada thumbnail yang di-upload
            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                $thumbnailPath = $thumbnail->store('public/thumbnails');
                $article->thumbnail = Storage::url($thumbnailPath);
            }

            $article->save();
            /*
            $html = '<html><body>' . htmlspecialchars($request['body']) . '</body></html>';
            $dom = new \DOMDocument();
            $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            $images = $dom->getElementsByTagName('img');
            
            foreach ($images as $image) {
                $url = $image->getAttribute('src');

                // mengambil nama file gambar
                $filename = pathinfo($url, PATHINFO_BASENAME);

                // membuat direktori untuk menyimpan gambar
                $path = 'public/images/' . $filename;

                // mendownload gambar dari URL ke direktori lokal
                file_put_contents(storage_path('app/' . $path), file_get_contents($url));

                // menyimpan informasi gambar ke database
                $media = new Media;
                $media->article_id = $article->id;
                $media->url = $path; // menyimpan path lokal ke file
                $media->save();
                
            }
            
            */
            // mengambil semua tag img dari body artikel
            preg_match_all('/<img.*src="([^"]+)".*>/iU', $request['body'], $matches);

            // menyimpan setiap gambar ke dalam tabel media
            foreach ($matches[1] as $url) {
                $media = new Media;
                $media->article_id = $article->id;
                $media->url = $url;
                $media->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Article created successfully',
                'article' => $article,
                'media' => $media,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to create article',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $category = ArticleCategory::find($request['category_id']);
        if (!$category) {
            return response()->json(['error' => 'Invalid category selected'], 422);
        }
        DB::beginTransaction();

        try {
            $article = Article::find($id);
            $article->title = $request['title'];
            $article->body = $request['body'];
            $article->category_id = $request['category_id'];
            $article->slug = Str::slug($request['title']);
            $article->excerpt = substr(strip_tags($request['body']), 0, 200);

            // cek apakah ada thumbnail yang di-upload
            if ($request->hasFile('thumbnail')) {
                $thumbnail = $request->file('thumbnail');
                $thumbnailPath = $thumbnail->store('public/thumbnails');
                $article->thumbnail = Storage::url($thumbnailPath);
            }

            $article->save();

            // menghapus semua media sebelumnya
            $media = Media::where('article_id', $id)->get();
            foreach ($media as $m) {
                Storage::delete(str_replace('/storage', 'public', $m->url));
                $m->delete();
            }

            // mengambil semua tag img dari body artikel
            preg_match_all('/<img.*src="([^"]+)".*>/iU', $request['body'], $matches);

            // menyimpan setiap gambar ke dalam tabel media
            foreach ($matches[1] as $url) {
                $media = new Media;
                $media->article_id = $article->id;
                $media->url = $url;
                $media->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Article updated successfully',
                'article' => $article,
                'media' => $media,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to update article',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function Destroy($id)
    {
        DB::beginTransaction();

        try {
            $article = Article::find($id);

            if (!$article) {
                throw new \Exception('Article not found');
            }

            $media = Media::where('article_id', $id)->get();

            foreach ($media as $m) {
                Storage::delete(str_replace('/storage', 'public', $m->url));
                $m->delete();
            }

            $article->delete();

            DB::commit();

            return response()->json([
                'message' => 'Article deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to delete article',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}