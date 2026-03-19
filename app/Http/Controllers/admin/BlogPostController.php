<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogPostRequest;
use App\Http\Requests\Admin\UpdateBlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $posts = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.blog-posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.blog-posts.create');
    }

    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = BlogPost::generateSlug($data['title']);
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        if ($request->hasFile('featured_image')) {
            $data['featured_image_path'] = $request->file('featured_image')->store('blog', 'public');
        }

        unset($data['featured_image']);

        if ($data['is_published']) {
            $data['published_at'] = now();
        }

        $post = BlogPost::create($data);

        activity()
            ->performedOn($post)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Blog post created: '.$post->title);

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog post "'.$post->title.'" created successfully.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.edit', compact('blogPost'));
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $request->validated();
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        if ($request->hasFile('featured_image')) {
            if ($blogPost->featured_image_path) {
                Storage::disk('public')->delete($blogPost->featured_image_path);
            }
            $data['featured_image_path'] = $request->file('featured_image')->store('blog', 'public');
        }

        unset($data['featured_image']);

        if ($data['is_published'] && ! $blogPost->published_at) {
            $data['published_at'] = now();
        } elseif (! $data['is_published']) {
            $data['published_at'] = null;
        }

        $blogPost->update($data);

        activity()
            ->performedOn($blogPost)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Blog post updated: '.$blogPost->title);

        return redirect()->route('admin.blog-posts.edit', $blogPost)
            ->with('success', 'Blog post updated successfully.');
    }

    public function toggleVisibility(BlogPost $blogPost): RedirectResponse
    {
        $isPublished = ! $blogPost->is_published;

        $blogPost->update([
            'is_published' => $isPublished,
            'published_at' => $isPublished ? ($blogPost->published_at ?? now()) : null,
        ]);

        activity()
            ->performedOn($blogPost)
            ->causedBy(auth()->guard('admin')->user())
            ->log('Blog post '.($isPublished ? 'published' : 'hidden').': '.$blogPost->title);

        return back()->with('success', '"'.$blogPost->title.'" is now '.($isPublished ? 'visible' : 'hidden').'.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $title = $blogPost->title;

        if ($blogPost->featured_image_path) {
            Storage::disk('public')->delete($blogPost->featured_image_path);
        }

        $blogPost->delete();

        activity()
            ->causedBy(auth()->guard('admin')->user())
            ->log('Blog post deleted: '.$title);

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog post "'.$title.'" deleted.');
    }
}
