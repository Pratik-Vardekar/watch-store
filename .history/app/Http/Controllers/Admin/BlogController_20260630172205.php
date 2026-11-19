<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
// use App\Models\BlogCategory;    // Category code commented

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  // ->orWhere('category_id', 'like', '%' . $request->search . '%') // category code commented
                  // ->orWhere('author', 'like', '%' . $request->search . '%') // author code commented
                  ->orWhere('short_description', 'like', '%' . $request->search . '%');
            });
        }

        // Category Filter
        // Category Filter - Using category_id
        // if ($request->filled('category')) {
        //     $query->where('category_id', $request->category); // Use category_id
        // }

        // Status Filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Author Filter
        // if ($request->filled('author')) {
        //     $query->where('author', 'like', '%' . $request->author . '%');
        // }

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $blogs = $query->latest()->paginate(10);
        // $categories = BlogCategory::where('status', 1)->get(); // Category code commented

        // return view('admin.blog.index', compact('blogs', 'categories'));
        return view('admin.blog.index', compact('blogs'));
    }

    public function create()
    {
        // $categories = BlogCategory::where('status', 1)->get(); // Category code commented
        // return view('admin.blog.create', compact('categories'));
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'             => 'required',
            'slug'              => 'required|unique:blogs,slug',
            // 'category_id'       => 'nullable',              // category code commented
            'trending' => 'nullable|boolean',
            // 'author'            => 'nullable',              // author code commented
            'blog_date'         => 'nullable|date',
            'short_description' => 'nullable',
            'description'       => 'nullable',
            // 'tags'              => 'nullable|string',        // tags code commented
            'image'             => 'required|image|mimes:jpg,jpeg,png,webp',
            'status'            => 'required',
        ]);

        $image = '';

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(
                public_path('backend/assets/images/blog'),
                $imageName
            );
            $image = $imageName;
        }

        Blog::create([
            'title'             => $request->title,
            'slug'              => Str::slug($request->slug),
            // 'category_id'       => $request->category_id,     // category code commented
            'trending' => $request->trending ?? 0,
            // 'author'            => $request->author ?? auth()->user()->name ?? 'Admin', // author code commented
            'blog_date'         => $request->blog_date ?? now(),
            'short_description' => $request->short_description,
            'description'       => $request->description,
            // 'tags'              => $request->tags,            // tags code commented
            'image'             => $image,
            'status'            => $request->status,
        ]);

        return redirect()
            ->route('blog.index')
            ->with('success', 'Blog Added Successfully');
    }

    public function show(Blog $blog)
    {
        return view('admin.blog.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        // $categories = BlogCategory::where('status', 1)->get(); // Category code commented
        // return view('admin.blog.edit', compact('blog', 'categories'));
        return view('admin.blog.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title'             => 'required',
            'slug'              => 'required|unique:blogs,slug,' . $blog->id,
            // 'category_id'       => 'nullable',                 // category code commented
            'trending' => 'nullable|boolean',
            // 'author'            => 'nullable',                 // author code commented
            'blog_date'         => 'nullable|date',
            'short_description' => 'nullable',
            'description'       => 'nullable',
            // 'tags'              => 'nullable|string',           // tags code commented
            'image'             => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'status'            => 'required',
        ]);

        $image = $blog->image;

        if ($request->hasFile('image')) {
            $oldPath = public_path('backend/assets/images/blog/' . $blog->image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(
                public_path('backend/assets/images/blog'),
                $imageName
            );
            $image = $imageName;
        }

        $blog->update([
            'title'             => $request->title,
            'slug'              => Str::slug($request->slug),
            // 'category_id'       => $request->category_id,     // category code commented
            'trending' => $request->trending ?? 0,
            // 'author'            => $request->author,          // author code commented
            'blog_date'         => $request->blog_date,
            'short_description' => $request->short_description,
            'description'       => $request->description,
            // 'tags'              => $request->tags,            // tags code commented
            'image'             => $image,
            'status'            => $request->status,
        ]);

        return redirect()
            ->route('blog.index')
            ->with('success', 'Blog Updated Successfully');
    }

    public function destroy(Blog $blog)
    {
        $path = public_path('backend/assets/images/blog/' . $blog->image);
        if (file_exists($path)) {
            unlink($path);
        }

        $blog->delete();

        return redirect()
            ->route('blog.index')
            ->with('success', 'Blog Deleted Successfully');
    }

    public function status(Blog $blog)
    {
        $blog->status = $blog->status == 1 ? 0 : 1;
        $blog->save();

        return redirect()
            ->back()
            ->with('success', 'Blog status updated successfully');
    }

    public function export(Request $request)
    {
        $query = Blog::query();

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    // ->orWhere('category_id', 'like', '%' . $request->search . '%') // category code commented
                    // ->orWhere('author', 'like', '%' . $request->search . '%');     // author code commented
                ;
            });
        }

        // Category Filter
        // if ($request->filled('category_id')) {
        //     $query->where('category_id', $request->category_id); // category code commented
        // }

        // Status Filter
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Author Filter
        // if ($request->filled('author')) {
        //     $query->where('author', 'like', '%' . $request->author . '%'); // author code commented
        // }

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $blogs = $query->latest()->get();

        $fileName = 'blogs_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () use ($blogs) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");
            
            fputcsv($file, [
                'ID',
                'Title',
                'Slug',
                // 'Category', // category code commented
                // 'Author',   // author code commented
                'Blog Date',
                'Short Description',
                // 'Tags',     // tags code commented
                'Status',
                'Created At',
                'Updated At'
            ]);

            foreach ($blogs as $blog) {
                $row = [
                    $blog->id,
                    $blog->title,
                    $blog->slug,
                    // $blog->category_id ?? '-', // category code commented
                    // $blog->author ?? '-',      // author code commented
                    $blog->blog_date ? date('d-m-Y', strtotime($blog->blog_date)) : '-',
                    strip_tags($blog->short_description ?? '-'),
                    // $blog->tags ?? '-',        // tags code commented
                    $blog->status == 1 ? 'Active' : 'Inactive',
                    $blog->created_at->format('d-m-Y H:i:s'),
                    $blog->updated_at->format('d-m-Y H:i:s'),
                ];
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}