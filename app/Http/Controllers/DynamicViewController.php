<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DynamicViewController
{
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function index($view)
    {
        $files = array_filter(glob(resource_path().'/**/*.blade.php'), fn ($path) => Str::contains($path, [$view.'.blade.php']));

        if (! Arr::first($files)) {
            abort(404, 'Blade '.$view.'.blade.php not found');
        }

        $path = str_replace('/', '.', str_replace('.blade.php', '', trim(str_replace(resource_path('views'), '', Arr::first($files)), '/')));

        return view($path);
    }
}
