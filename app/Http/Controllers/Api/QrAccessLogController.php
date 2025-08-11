<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QrAccessLogResource;
use App\Models\QrAccessLog;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QrAccessLogController extends Controller
{
    /**
     * Display a listing of the QR access logs.
     */
    public function index(): AnonymousResourceCollection
    {
        $logs = QrAccessLog::with('batch')
            ->latest()
            ->paginate();

        return QrAccessLogResource::collection($logs);
    }

    /**
     * Display the specified QR access log.
     */
    public function show(QrAccessLog $qrAccessLog): QrAccessLogResource
    {
        return new QrAccessLogResource($qrAccessLog->load('batch'));
    }
}
