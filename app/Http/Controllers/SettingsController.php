<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(): Response
    {
        $settings = SettingService::all();

        return Inertia::render('Settings', [
            'settings' => $settings,
            'defaults' => SettingService::defaults(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_address' => ['required', 'string', 'max:500'],
            'store_phone' => ['required', 'string', 'max:20'],
            'store_tax_id' => ['required', 'string', 'max:50'],
            'pb1_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'receipt_header' => ['required', 'string', 'max:255'],
            'receipt_footer' => ['required', 'string', 'max:255'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'enable_notifications' => ['boolean'],
        ]);

        // Update settings in database
        foreach ($validated as $key => $value) {
            $type = match ($key) {
                'pb1_rate', 'low_stock_threshold' => 'number',
                'enable_notifications' => 'boolean',
                default => 'string',
            };

            SettingService::set($key, $value, $type);
        }

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil diperbarui');
    }

    public function reset(): RedirectResponse
    {
        SettingService::initialize();

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan direset ke nilai default');
    }
}
