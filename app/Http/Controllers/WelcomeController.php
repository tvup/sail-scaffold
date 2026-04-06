<?php

namespace App\Http\Controllers;

use App\Models\BoilerplateDockerService;
use App\Models\BoilerplateSailService;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'services' => BoilerplateSailService::query()->orderBy('name')->get(),
            'dockerServices' => BoilerplateDockerService::query()->orderBy('name')->get(),
        ]);
    }
}
