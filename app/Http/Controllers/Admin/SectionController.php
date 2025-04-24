<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of the sections for a specific page.
     *
     * @param  int  $pageId
     * @return \Illuminate\Http\Response
     */
    public function index($pageId)
    {
        $page = Page::findOrFail($pageId);
        $sections = $page->sections()->orderBy('order')->get();
        
        return response()->json($sections);
    }

    /**
     * Store a newly created section in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $pageId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $pageId)
    {
        $page = Page::findOrFail($pageId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'is_active' => 'boolean',
            'order' => 'required|string|in:start,end',
        ]);

        $position = $request->order;
        
        if ($position === 'start') {
            // If adding at the start, shift all existing sections down
            $page->sections()->increment('order');
            $order = 1;
        } else {
            // If adding at the end, place after the last section
            $order = $page->sections()->max('order') + 1;
        }

        $section = Section::create([
            'page_id' => $pageId,
            'name' => $request->name,
            'type' => $request->type,
            'order' => $order,
            'is_active' => $request->is_active ?? true,
            'content' => $request->content ?? [],
        ]);

        return response()->json($section, 201);
    }

    /**
     * Display the specified section.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $section = Section::with('page')->findOrFail($id);
        return response()->json($section);
    }

    /**
     * Update the specified section in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);

        $section->update([
            'name' => $request->name,
            'type' => $request->type,
            'is_active' => $request->is_active ?? $section->is_active,
        ]);

        return response()->json($section);
    }

    /**
     * Update the content of a section.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateContent(Request $request, $id)
    {
        $section = Section::findOrFail($id);
        
        $request->validate([
            'content' => 'required|array',
        ]);

        $section->update([
            'content' => $request->content,
        ]);

        return response()->json($section);
    }

    /**
     * Remove the specified section from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        $pageId = $section->page_id;
        $sectionOrder = $section->order;
        
        $section->delete();
        
        // Reorder remaining sections
        Section::where('page_id', $pageId)
            ->where('order', '>', $sectionOrder)
            ->decrement('order');

        return response()->json(null, 204);
    }

    /**
     * Update the order of a section (move up or down).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOrder(Request $request, $id)
    {
        $request->validate([
            'direction' => 'required|string|in:up,down',
        ]);

        $section = Section::findOrFail($id);
        $direction = $request->direction;
        
        if ($direction === 'up' && $section->order > 1) {
            // Get the section above this one
            $otherSection = Section::where('page_id', $section->page_id)
                ->where('order', $section->order - 1)
                ->first();
                
            if ($otherSection) {
                $otherSection->order = $section->order;
                $otherSection->save();
                
                $section->order = $section->order - 1;
                $section->save();
            }
        } elseif ($direction === 'down') {
            // Get the section below this one
            $otherSection = Section::where('page_id', $section->page_id)
                ->where('order', $section->order + 1)
                ->first();
                
            if ($otherSection) {
                $otherSection->order = $section->order;
                $otherSection->save();
                
                $section->order = $section->order + 1;
                $section->save();
            }
        }

        return response()->json($section);
    }
} 