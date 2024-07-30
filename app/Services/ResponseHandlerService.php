<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ResponseHandlerService
{
    /**
     * Handle the response based on request type.
     *
     * @param mixed $data
     * @param Request $request
     * @return JsonResponse|View
     */
    public function handleResponse(mixed $data, Request $request): JsonResponse|View
    {
        if ($request->is('api/*')) {
            return response()->json($data);
        }

        return view($data['view'], $data['compact']);
    }
}
