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
            // If data is an array or object, return as JSON
            return response()->json($data);
        }

        // If request is not for API, ensure data has view and compact keys
        if (is_array($data) && isset($data['view'])) {
            return view($data['view'], $data['compact']);
        }

        // Default handling in case no view is provided
        return view('default-view'); // Replace with your default view if needed
    }
}
