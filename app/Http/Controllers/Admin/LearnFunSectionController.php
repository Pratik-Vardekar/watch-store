<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearnFunSection;
use Illuminate\Http\Request;

class LearnFunSectionController extends Controller
{
    public function edit()
    {
        $section = LearnFunSection::firstOrCreate([], [
            'subtitle' => 'Bringing Words to Life',
            'title' => 'Word Games That Makes',
            'title_highlight' => 'Learning Fun!',
            'content' => 'Boost vocabulary, sharpen thinking skills, and make learning exciting with interactive word challenges designed for curious young minds. From solo play to friendly competitions, every game encourages creativity, problem-solving, and smarter learning in a fun and engaging way. With colorful gameplay, exciting challenges, and age-friendly activities, kids stay entertained while building confidence and language skills every day.',
            'button_text' => 'Read More',
            'button_url' => '#',
            'features' => [
                [
                    'highlight' => 'Enhances',
                    'text' => 'Cognitive Skills',
                    'icon' => 'frontend/assets/images/lf_brain.png',
                    'bg_class' => 'bgPurpleSub',
                    'text_class' => 'txtpurple',
                ],
                [
                    'highlight' => 'Boosts',
                    'text' => 'Vocabulary',
                    'icon' => 'frontend/assets/images/lf_star.png',
                    'bg_class' => 'bgYellowSub',
                    'text_class' => 'txtyellow',
                ],
                [
                    'highlight' => 'Improve',
                    'text' => 'Focus',
                    'icon' => 'frontend/assets/images/lf_dart.png',
                    'bg_class' => 'bgGreenSub',
                    'text_class' => 'txtgreen',
                ],
                [
                    'highlight' => 'Fun for all',
                    'text' => 'Age Groups',
                    'icon' => 'frontend/assets/images/lf_users.png',
                    'bg_class' => 'bgRedSub',
                    'text_class' => 'txtred',
                ],
            ],
        ]);

        return view('admin.learn-fun-section.edit', compact('section'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'subtitle' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'title_highlight' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'button_text' => 'nullable|string|max:100',
            'button_url' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'features' => 'nullable|array',
            'features.*.highlight' => 'nullable|string|max:100',
            'features.*.text' => 'nullable|string|max:255',
            'features.*.bg_class' => 'nullable|string|max:50',
            'features.*.text_class' => 'nullable|string|max:50',
            'features.*.icon' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:2048',
        ]);

        $section = LearnFunSection::firstOrCreate([]);

        $data = $request->only([
            'subtitle',
            'title',
            'title_highlight',
            'content',
            'button_text',
            'button_url',
        ]);

        if ($request->hasFile('image')) {
            $dir = public_path('backend/assets/images/learn-fun');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if ($section->image && file_exists(public_path($section->image))) {
                @unlink(public_path($section->image));
            }

            $file = $request->file('image');
            $name = time() . '_learnfun.' . $file->getClientOriginalExtension();
            $file->move($dir, $name);
            $data['image'] = 'backend/assets/images/learn-fun/' . $name;
        }

        $data['features'] = $this->handleFeatureIcons(
            $request,
            $section->features ?? []
        );

        $section->update($data);

        return back()->with('success', 'Learn Fun section updated successfully.');
    }

    private function handleFeatureIcons(Request $request, array $existing): array
    {
        $rows = $request->input('features', []);
        $dir = public_path('backend/assets/images/learn-fun');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        foreach ($rows as $i => $row) {
            if ($request->hasFile("features.$i.icon")) {
                $file = $request->file("features.$i.icon");
                $name = time() . '_feat' . $i . '.' . $file->getClientOriginalExtension();
                $file->move($dir, $name);
                $rows[$i]['icon'] = 'backend/assets/images/learn-fun/' . $name;
            } else {
                $rows[$i]['icon'] = $existing[$i]['icon'] ?? null;
            }
        }

        return array_values($rows);
    }
}
