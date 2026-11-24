<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Edit Tentang: profil usaha, alamat, jam buka, dan titik peta. */
class AboutController extends Controller
{
    public function edit()
    {
        return view('admin.about', ['profile' => BusinessProfile::current()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string', 'max:3000'],
            'address'       => ['nullable', 'string', 'max:500'],
            'open_hours'    => ['nullable', 'string', 'max:100'],
            'instagram'     => ['nullable', 'string', 'max:100'],
            'map_query'     => ['nullable', 'string', 'max:255'],
            'image'         => ['nullable', 'image', 'max:2048'],
            'logo'          => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ]);

        $profile = BusinessProfile::first() ?? new BusinessProfile();

        if ($request->hasFile('image')) {
            if ($profile->image) {
                Storage::disk('public')->delete($profile->image);
            }
            $data['image'] = $request->file('image')->store('profile', 'public');
        } else {
            unset($data['image']);
        }

        // Logo: upload baru menggantikan yang lama; centang hapus = kembali ke logo bawaan
        if ($request->boolean('remove_logo') && $profile->logo) {
            Storage::disk('public')->delete($profile->logo);
            $profile->logo = null;
        }

        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $profile->logo = $request->file('logo')->store('profile', 'public');
        }

        $profile->fill(collect($data)->except(['image', 'logo'])->all())->save();

        return back()->with('success', 'Informasi usaha berhasil diperbarui.');
    }
}
