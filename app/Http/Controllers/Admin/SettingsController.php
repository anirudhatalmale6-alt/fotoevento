<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit()
    {
        $yape = Setting::yape();
        return view('admin.settings.edit', compact('yape'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'yape_number'  => ['nullable', 'string', 'max:40'],
            'yape_account' => ['nullable', 'string', 'max:120'],
            'yape_qr'      => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:8192'],
        ], [], ['yape_qr' => 'QR de Yape']);

        Setting::put('yape_number', $request->input('yape_number'));
        Setting::put('yape_account', $request->input('yape_account'));

        // El QR se guarda en disco público (se muestra al cliente en la pantalla de pago).
        if ($request->hasFile('yape_qr')) {
            $old = Setting::get('yape_qr_path');
            if ($old) {
                Storage::disk(config('storage.public_disk'))->delete($old);
            }
            $path = $request->file('yape_qr')->store('settings', config('storage.public_disk'));
            Setting::put('yape_qr_path', $path);
        }

        return back()->with('ok', 'Datos de Yape guardados. Ya aparecen en la pantalla de pago de tus clientes.');
    }
}
