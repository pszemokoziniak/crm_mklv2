<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use League\Glide\ServerFactory;

class ImagesController extends Controller
{
    public function show(Filesystem $filesystem, Request $request, $path)
    {
        // Glide 3 bez porzuconego glide-laravel: wynik ląduje w .glide-cache,
        // a my zwracamy ten plik.
        $server = ServerFactory::create([
            'source' => $filesystem->getDriver(),
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => '.glide-cache',
        ]);

        $plik = $server->makeImage($path, $request->all());

        // Trasa jest za logowaniem: tylko cache przeglądarki, nie pośredników.
        return response()->file($filesystem->path($plik), [
            'Content-Type' => $server->getCache()->mimeType($plik),
        ])->setPrivate()->setMaxAge(31536000);
    }
}
