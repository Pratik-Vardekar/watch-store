<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUsSection;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function index()
    {
        $about = AboutUsSection::firstOrCreate(
            ['id' => 1],
            [
                'title' => 'About Us',
                'content' => '',
            ]
        );
    
        return view('admin.about-us.index', compact('about'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'title'   => 'nullable|string|max:255',
            'content' => 'nullable|string',
        ]);
    
        $about = AboutUsSection::firstOrCreate(['id' => 1]);
    
        $about->update([
            'title'   => $request->title,
            'content' => $request->content,
        ]);
    
        return redirect()
            ->route('about-us.index')
            ->with('success', 'About page updated successfully.');
    }

    private function handleRepeaterFiles(Request $request, string $key, array $existing, array $fileFields): array
    {
        $rows = $request->input($key, []);
        $dir = public_path('backend/assets/images/about');

        foreach ($rows as $i => $row) {
            foreach ($fileFields as $field) {
                if ($request->hasFile("$key.$i.$field")) {
                    $file = $request->file("$key.$i.$field");
                    $name = time() . '_' . $key . $i . '_' . $field . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $name);
                    $rows[$i][$field] = 'backend/assets/images/about/' . $name;
                } else {
                    $rows[$i][$field] = $existing[$i][$field] ?? null;
                }
            }
        }

        return array_values($rows);
    }
}
