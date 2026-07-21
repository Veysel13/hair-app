<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessHour;
use App\Models\BusinessImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class BusinessSettingsController extends Controller
{
    private function getBusiness(): Business
    {
        $business = Business::where('owner_id', Auth::id())->first();
        if (!$business) {
            if (Auth::user()->role === 'admin') {
                $business = Business::first();
            } else {
                abort(404, 'İşletme bulunamadı.');
            }
        }
        return $business;
    }

    public function edit(): View
    {
        $business = $this->getBusiness();

        // Proactive onboarding: seed business hours if missing
        if ($business->hours()->count() === 0) {
            for ($i = 0; $i < 7; $i++) {
                $business->hours()->create([
                    'day_of_week' => $i,
                    'is_open'     => $i !== 0, // Sunday (0) closed by default
                    'open_time'   => '09:00',
                    'close_time'   => '19:00',
                ]);
            }
        }

        $business->load(['hours', 'images']);

        return view('admin.settings.edit', compact('business'));
    }

    public function update(Request $request): RedirectResponse
    {
        $business = $this->getBusiness();

        $data = $request->validate([
            'name'               => ['required', 'string', 'max:100'],
            'category'           => ['required', 'string', 'in:hair_salon,nail_salon,spa,barber,beauty_center,other'],
            'phone'              => ['required', 'string', 'max:20'],
            'email'              => ['nullable', 'email', 'max:100'],
            'website'            => ['nullable', 'string', 'max:100'],
            'instagram'          => ['nullable', 'string', 'max:100'],
            'description'        => ['nullable', 'string'],
            'address'            => ['required', 'string', 'max:255'],
            'city'               => ['required', 'string', 'max:50'],
            'district'           => ['required', 'string', 'max:50'],
            'deposit_required'   => ['nullable', 'boolean'],
            'deposit_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'cancellation_hours' => ['required', 'integer', 'min:0', 'max:168'],
            'latitude'           => ['nullable', 'numeric'],
            'longitude'          => ['nullable', 'numeric'],
            'logo'               => ['nullable', 'image', 'max:2048'],
            'cover_image'        => ['nullable', 'image', 'max:4096'],
        ]);

        $data['deposit_required'] = $request->has('deposit_required');

        // File upload directories check
        if (!File::exists(public_path('uploads/logos'))) {
            File::makeDirectory(public_path('uploads/logos'), 0755, true);
        }
        if (!File::exists(public_path('uploads/covers'))) {
            File::makeDirectory(public_path('uploads/covers'), 0755, true);
        }

        // Upload Logo
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_logo_' . preg_replace('/[^a-zA-Z0-9_.]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/logos'), $filename);
            $data['logo'] = 'uploads/logos/' . $filename;

            // Delete old file
            if ($business->logo && File::exists(public_path($business->logo))) {
                File::delete(public_path($business->logo));
            }
        }

        // Upload Cover
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = time() . '_cover_' . preg_replace('/[^a-zA-Z0-9_.]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/covers'), $filename);
            $data['cover_image'] = 'uploads/covers/' . $filename;

            // Delete old file
            if ($business->cover_image && File::exists(public_path($business->cover_image))) {
                File::delete(public_path($business->cover_image));
            }
        }

        $business->update($data);

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Salon ayarları başarıyla güncellendi.');
    }

    public function updateHours(Request $request): RedirectResponse
    {
        $business = $this->getBusiness();

        $request->validate([
            'hours' => ['required', 'array', 'size:7'],
            'hours.*.is_open' => ['nullable', 'boolean'],
            'hours.*.open_time' => ['required_if:hours.*.is_open,1', 'nullable', 'date_format:H:i'],
            'hours.*.close_time' => ['required_if:hours.*.is_open,1', 'nullable', 'date_format:H:i'],
        ]);

        foreach ($request->hours as $dayOfWeek => $hourData) {
            BusinessHour::updateOrCreate(
                ['business_id' => $business->id, 'day_of_week' => $dayOfWeek],
                [
                    'is_open' => isset($hourData['is_open']),
                    'open_time' => isset($hourData['is_open']) ? $hourData['open_time'] : null,
                    'close_time' => isset($hourData['is_open']) ? $hourData['close_time'] : null,
                ]
            );
        }

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Çalışma saatleriniz başarıyla güncellendi.');
    }

    public function uploadImage(Request $request): RedirectResponse
    {
        $business = $this->getBusiness();

        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['image', 'max:4096'],
            'caption' => ['nullable', 'string', 'max:100'],
        ]);

        $businessId = $business->id;
        $targetDir = "uploads/gallery/{$businessId}";

        if (!File::exists(public_path($targetDir))) {
            File::makeDirectory(public_path($targetDir), 0755, true);
        }

        $files = $request->file('images');
        $count = $business->images()->count();

        foreach ($files as $file) {
            $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_.]/', '', $file->getClientOriginalName());
            $file->move(public_path($targetDir), $filename);

            BusinessImage::create([
                'business_id' => $businessId,
                'image_path'  => "{$targetDir}/{$filename}",
                'caption'     => $request->caption,
                'sort_order'  => ++$count,
            ]);
        }

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Görseller galeriye başarıyla yüklendi.');
    }

    public function destroyImage(BusinessImage $image): RedirectResponse
    {
        $business = $this->getBusiness();

        // Authorization check
        if ($image->business_id !== $business->id) {
            abort(403, 'Bu görseli silme yetkiniz yok.');
        }

        // Delete physical file
        if (File::exists(public_path($image->image_path))) {
            File::delete(public_path($image->image_path));
        }

        $image->delete();

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Görsel galeriden silindi.');
    }
}
