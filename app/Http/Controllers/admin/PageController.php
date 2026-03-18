<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        $keyOrder = array_keys(Page::PAGES);
        $pages = Page::all()->sortBy(fn ($page) => array_search($page->key, $keyOrder))->values();

        return view('admin.pages.index', compact('pages'));
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        $page->update($data);

        activity()
            ->performedOn($page)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Page updated: '.$page->title);

        return redirect()->route('admin.pages.edit', $page)
            ->with('success', 'Page "'.$page->title.'" updated successfully.');
    }
}
