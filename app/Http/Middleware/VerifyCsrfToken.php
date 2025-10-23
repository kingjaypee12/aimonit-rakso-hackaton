<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Exempt Livewire file upload routes
        'livewire/upload-file',
        'livewire/upload-file/*',
        'livewire/*',
        // Exempt Filament file upload routes
        'teacher/livewire/upload-file',
        'teacher/livewire/upload-file/*',
        'teacher/livewire/*',
        // Exempt any other file upload endpoints
        'api/questionnaires',
        'api/*',
        'api/upload/*',
        // Exempt all teacher panel routes for debugging
        'teacher/*',
    ];
}