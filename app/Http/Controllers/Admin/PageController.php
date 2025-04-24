<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the pages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $pages = Page::orderBy('updated_at', 'desc')->get();
            return response()->json($pages);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load pages',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created page in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages',
            'template' => 'nullable|string|max:50',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $page = Page::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'template' => $request->template ?? 'default',
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json($page, 201);
    }

    /**
     * Display the specified page.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $page = Page::findOrFail($id);
            return response()->json($page);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Page not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified page in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $id,
            'template' => 'nullable|string|max:50',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $page->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'template' => $request->template ?? $page->template,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'is_active' => $request->is_active ?? $page->is_active,
        ]);

        return response()->json($page);
    }

    /**
     * Remove the specified page from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return response()->json(null, 204);
    }
} 