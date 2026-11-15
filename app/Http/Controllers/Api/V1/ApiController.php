<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->successResponse(
            'AmarHisab API v1 is working fine.',
            [
                'app' => 'AmarHisab',
                'version' => 'v1',
                'status' => 'active',
            ]
        );
    }
}