<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUsSection;
use Illuminate\Http\Request;

class AboutUsController extends Controller
{
    public function index()
    {
        $about = AboutUsSection::firstOrCreate(['id' => 1], ['title' => 'About Us']);

        return view('admin.about-us.index', compact('about'));
    }

    public function update(Request $request)
    {
        $about = AboutUsSection::firstOrCreate(['id' => 1], ['title' => 'About Us']);

        $data = $request->only([
            'content',
        ]);

        $data['stats'] = array_values($request->input('stats', []));
        $data['features'] = array_values(array_filter(
            $request->input('features', []),
            fn ($v) => filled($v)
        ));

        $about->update($data);

        return redirect()
            ->route('about-us.index')
            ->with('success', 'About page content updated successfully.');
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
