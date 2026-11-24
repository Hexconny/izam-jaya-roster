<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Edit Beranda: judul, kalimat pengantar, gambar banner, dan nomor WhatsApp admin. */
class HomepageController extends Controller
{
    public function edit()
    {
        return view('admin.homepage', [
            'profile'  => BusinessProfile::current(),
            'whatsapp' => optional(Contact::first())->whatsapp,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'hero_title'    => ['required', 'string', 'max:150'],
            'hero_subtitle' => ['nullable', 'string', 'max:300'],
            'whatsapp'      => ['required', 'string', 'max:20', 'regex:/^[0-9+\s-]{8,20}$/'],
            'hero_image'    => ['nullable', 'image', 'max:3072'],
        ]);

        $profile = BusinessProfile::first() ?? new BusinessProfile(['business_name' => 'Izam Jaya Roster']);
        $profile->fill($request->only('hero_title', 'hero_subtitle'));

        if ($request->boolean('remove_hero_image') && $profile->hero_image) {
            Storage::disk('public')->delete($profile->hero_image);
            $profile->hero_image = null;
        }

        if ($request->hasFile('hero_image')) {
            if ($profile->hero_image) {
                Storage::disk('public')->delete($profile->hero_image);
            }
            $profile->hero_image = $request->file('hero_image')->store('profile', 'public');
        }

        $profile->save();

        $contact = Contact::first() ?? new Contact();
        $contact->whatsapp = $data['whatsapp'];
        $contact->save();

        return back()->with('success', 'Beranda berhasil diperbarui.');
    }
}
