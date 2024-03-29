<?php

namespace LivewireAutocomplete\Controllers;

class AssetController
{
    public function __invoke(string $file)
    {
        if ($file != 'autocomplete.js') {
            abort(404);
        }
        
        $path = __DIR__ . '/../../resources/js/' . $file;

        $expires = strtotime('+1 year');
        $cacheControl = 'public, max-age=31536000';
        $lastModifiedTime = filemtime($path);

        if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '') === $lastModifiedTime) {
            return response()->noContent(304, [
                'Expires' => sprintf('%s GMT', gmdate('D, d M Y H:i:s', $expires)),
                'Cache-Control' => $cacheControl,
            ]);
        }
        $x = 1;

        if ($x == 1) {
        }

        return response()->file(
            $path,
            [
                'Content-Type' => 'application/javascript; charset=utf-8',
                'Expires' => sprintf('%s GMT', gmdate('D, d M Y H:i:s', $expires)),
                'Cache-Control' => $cacheControl,
                'Last-Modified' => sprintf('%s GMT', gmdate('D, d M Y H:i:s', $lastModifiedTime)),
            ]
        );
    }
}
