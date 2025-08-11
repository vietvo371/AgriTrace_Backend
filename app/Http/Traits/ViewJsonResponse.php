<?php

namespace App\Http\Traits;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

trait ViewJsonResponse
{

    /**
     * Response error
     *
     * @param array $error error
     *
     * @return response
     */
    public function responseError(array $error, $reload = false, $link = '')
    {
        return response()->json([
            'errors' => [
                'message' => [$error],
            ],
            'reload'    => $reload,
            'link'      => $link,
        ], '400');
    }

    /**
     * Response success
     *
     * @param string $message message
     * @param string $subType subType
     * @param string $reload  reload
     * @param string $link    link
     *
     * @return response
     */
    public function responseSuccess($message, $subType = 'success', $reload = false, $link = '', $data = null)
    {
        return response()->json([
            'type'      => 'toastr',
            'sub_type'  => $subType,
            'message'   => $message,
            'reload'    => $reload,
            'link'      => $link,
            'data'      => $data
        ], 200);
    }

    /**
     * Response data
     *
     * @param array $data Data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseData($data = [])
    {
        return response()->json([
            'data' => $data
        ], 200);
    }
}
