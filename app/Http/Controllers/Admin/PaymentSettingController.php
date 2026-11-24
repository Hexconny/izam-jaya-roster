<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Pengaturan rekening/QRIS yang tampil ke pelanggan + kebijakan ongkir. */
class PaymentSettingController extends Controller
{
    public function edit()
    {
        return view('admin.payment', ['profile' => BusinessProfile::current()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'bank_name'       => ['nullable', 'string', 'max:60'],
            'bank_account'    => ['nullable', 'string', 'max:40'],
            'bank_holder'     => ['nullable', 'string', 'max:100'],
            'shipping_policy' => ['nullable', 'string', 'max:1000'],
            'qris_image'      => ['nullable', 'image', 'max:3072'],
        ]);

        $profile = BusinessProfile::first() ?? new BusinessProfile(['business_name' => 'Izam Jaya Roster']);

        if ($request->boolean('remove_qris') && $profile->qris_image) {
            Storage::disk('public')->delete($profile->qris_image);
            $profile->qris_image = null;
        }

        if ($request->hasFile('qris_image')) {
            if ($profile->qris_image) {
                Storage::disk('public')->delete($profile->qris_image);
            }
            $profile->qris_image = $request->file('qris_image')->store('profile', 'public');
        }

        $profile->fill(collect($data)->except('qris_image')->all())->save();

        return back()->with('success', 'Pengaturan pembayaran dan ongkir disimpan.');
    }
}
