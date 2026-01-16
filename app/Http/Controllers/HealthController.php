<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class HealthController extends Controller
{
    /**
     * Health check endpoint for Docker/Coolify
     */
    public function check(): Response
    {
        return response('healthy', 200);
    }
}
