<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\Service\CreateServiceRequest;
use App\Http\Requests\Service\ReadServiceRequest;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;

class ServiceController extends Controller
{
    /**
     * Create a new processing service
     *
     * POST /api/service/create
     * All validation handled by CreateServiceRequest
     */
    public function create(CreateServiceRequest $request)
    {
        $service = Service::createService([
            'user_id' => $request->user->id ?? clone($request->input('user')->_id),
            'file_id' => $request->input('file_id'),
            'type' => $request->input('type'),
            'target_language' => $request->input('target_language'),
            'organization_id' => $request->input('organization_id')
        ]);

        return response()->success(
            new ServiceResource($service),
            'Service created successfully',
            201
        );
    }

    /**
     * Get specific service by service_id and optionally download as PDF
     */
    public function read(ReadServiceRequest $request)
    {
        $userId = $request->user->id ?? clone($request->input('user')->_id);
        $serviceId = $request->input('service_id');

        $service = Service::findServiceById($serviceId, $userId);

        if (!$service) {
            return response()->error('Service not found', 404);
        }

        $services = collect([$service]);

        // Check if user requested a PDF download
        if ($request->boolean('download_pdf')) {
            $fileName = $service->file?->file_name ?? 'Service_Result';
            
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.service_results', [
                'services' => $services,
                'fileName' => $fileName
            ]);
            
            return $pdf->download("service_results_{$serviceId}.pdf");
        }

        return response()->success([
            'service_id' => $serviceId,
            'service' => new ServiceResource($service),
            'total_services' => 1
        ], 'Service retrieved successfully');
    }

    /**
     * List services for the authenticated user
     */
    public function list(Request $request)
    {
                
        // Get user from request (set by middleware)
        $user = $request->user;
        $userId = $user->id;
        
        $services = Service::getAllServices($userId);

        return response()->success([
            'user_id' => $userId,
            'services' => ServiceResource::collection($services),
            'total_services' => $services->count(),
            'filters_applied' => []
        ], 'Services retrieved successfully');
    }

    
    /**
     * Get list of supported languages for translation
     */
    public function languages()
    {
        $languages = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'ur', 'name' => 'Urdu'],
            ['code' => 'ar', 'name' => 'Arabic'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'es', 'name' => 'Spanish'],
            ['code' => 'de', 'name' => 'German'],
            ['code' => 'it', 'name' => 'Italian'],
            ['code' => 'pt', 'name' => 'Portuguese'],
            ['code' => 'ru', 'name' => 'Russian'],
            ['code' => 'zh', 'name' => 'Chinese (Simplified)'],
            ['code' => 'ja', 'name' => 'Japanese'],
            ['code' => 'ko', 'name' => 'Korean']
        ];

        return response()->success([
            'languages' => LanguageResource::collection($languages),
            'total' => count($languages)
        ], 'Supported languages fetched successfully');
    }

    /**
     * Mark all notifications as read for the user
     * 
     * POST /api/service/notifications/mark-read
     */
    public function markNotificationsRead(Request $request): JsonResponse
    {
        $user = $request->user;
        $userId = $user->id;

        Service::where('user_id', $userId)
            ->where('notification_seen', '!=', true)
            ->update(['notification_seen' => true]);

        return response()->success(null, 'Notifications marked as read');
    }
}

