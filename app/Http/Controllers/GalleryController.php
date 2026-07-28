<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    // Página de acceso (pide PIN si el evento tiene uno)
    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)->where('published', true)->firstOrFail();

        // sin PIN -> acceso directo
        if (blank($event->pin) || $this->unlocked($event)) {
            return $this->gallery($event);
        }
        return view('gallery.pin', ['event' => $event, 'error' => null]);
    }

    public function unlock(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->where('published', true)->firstOrFail();
        $pin = (string) $request->input('pin');

        if (blank($event->pin) || hash_equals($event->pin, $pin)) {
            $request->session()->put($this->key($event), true);
            return redirect()->route('gallery.show', $event->slug);
        }
        return view('gallery.pin', ['event' => $event, 'error' => 'PIN incorrecto. Inténtalo de nuevo.']);
    }

    private function gallery(Event $event)
    {
        $event->load(['photos', 'packages']);
        return view('gallery.show', compact('event'));
    }

    private function unlocked(Event $event): bool
    {
        return (bool) session($this->key($event), false);
    }

    private function key(Event $event): string
    {
        return 'gallery_ok_' . $event->id;
    }
}
