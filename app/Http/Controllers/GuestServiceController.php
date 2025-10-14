<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Payment;
use Auth;
use Carbon\Carbon; // Add this import
use Illuminate\Support\Facades\Log; // Add this import

class GuestServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::where('is_available', true);

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('category')
                         ->orderBy('name')
                         ->paginate(12);

        return view('guest.services.index', compact('services'));
    }

    public function show(Service $service)
    {
        // Get related services in the same category (excluding current service)
        $relatedServices = Service::where('category', $service->category)
                                 ->where('is_available', true)
                                 ->where('id', '!=', $service->id)
                                 ->limit(3)
                                 ->get();

        return view('guest.services.show', compact('service', 'relatedServices'));
    }

    public function create(Service $service)
    {
        // Check if service is available
        if (!$service->is_available) {
            return redirect()->route('guest.services.show', $service)
                           ->with('error', 'This service is currently unavailable for booking.');
        }

        return view('guest.services.request', compact('service'));
    }

    public function store(Request $request)
    {
        \Log::info('Service request form submission:', $request->all());

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'service_type' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'scheduled_date' => 'required|date|after:now',
            'guests_count' => 'required|integer|min:1|max:20',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        try {
            // Get available columns in the table
            $tableColumns = \Schema::getColumnListing('service_requests');
            \Log::info('Available columns in service_requests table:', $tableColumns);

            // Show user what columns exist for debugging
            if (empty($tableColumns)) {
                return back()->withInput()
                            ->with('error', 'Service requests table not found. Please contact administrator.');
            }

            // Build data array with only basic required fields first
            $serviceRequestData = [];

            // Add fields based on what exists
            if (in_array('service_id', $tableColumns)) {
                $serviceRequestData['service_id'] = $validated['service_id'];
            }

            if (in_array('description', $tableColumns)) {
                $serviceRequestData['description'] = $validated['description'];
            }

            if (in_array('status', $tableColumns)) {
                $serviceRequestData['status'] = 'pending';
            }

            // Try different user ID column names
            if (in_array('guest_id', $tableColumns)) {
                $serviceRequestData['guest_id'] = Auth::id();
            } elseif (in_array('user_id', $tableColumns)) {
                $serviceRequestData['user_id'] = Auth::id();
            } elseif (in_array('customer_id', $tableColumns)) {
                $serviceRequestData['customer_id'] = Auth::id();
            }

            // Add other fields only if columns exist
            $optionalFields = [
                'guest_name' => Auth::user()->name,
                'guest_email' => Auth::user()->email,
                'room_id' => Auth::user()->room_id ?? null,
                'deadline' => $validated['scheduled_date'],
                'priority' => 'medium',
            ];

            // Try different column name variations
            $columnVariations = [
                'service_type' => ['service_type', 'type', 'service_name'],
                'scheduled_date' => ['scheduled_date', 'scheduled_at', 'booking_date', 'appointment_date'],
                'guests_count' => ['guests_count', 'guest_count', 'number_of_guests', 'pax'],
                'manager_notes' => ['manager_notes', 'notes', 'special_requests', 'comments'],
            ];

            // Add service type
            foreach ($columnVariations['service_type'] as $column) {
                if (in_array($column, $tableColumns)) {
                    $serviceRequestData[$column] = $validated['service_type'];
                    break;
                }
            }

            // Add scheduled date
            foreach ($columnVariations['scheduled_date'] as $column) {
                if (in_array($column, $tableColumns)) {
                    $serviceRequestData[$column] = $validated['scheduled_date'];
                    break;
                }
            }

            // Add guest count
            foreach ($columnVariations['guests_count'] as $column) {
                if (in_array($column, $tableColumns)) {
                    $serviceRequestData[$column] = $validated['guests_count'];
                    break;
                }
            }

            // Add special requests
            if ($validated['special_requests']) {
                foreach ($columnVariations['manager_notes'] as $column) {
                    if (in_array($column, $tableColumns)) {
                        $serviceRequestData[$column] = $validated['special_requests'];
                        break;
                    }
                }
            }

            // Add optional fields
            foreach ($optionalFields as $field => $value) {
                if (in_array($field, $tableColumns)) {
                    $serviceRequestData[$field] = $value;
                }
            }

            \Log::info('Creating service request with data:', $serviceRequestData);
            \Log::info('Available table columns:', $tableColumns);

            // Make sure we have at least the minimum required data
            if (empty($serviceRequestData)) {
                return back()->withInput()
                            ->with('error', 'No compatible columns found. Available columns: ' . implode(', ', $tableColumns));
            }

            $serviceRequest = ServiceRequest::create($serviceRequestData);
            
            \Log::info('Service request created successfully:', $serviceRequest->toArray());

            // Create a payment record for the service request
            // For now, we'll assume services are free or have a default amount
            // You can modify this based on your service pricing structure
            $serviceAmount = 0; // Default to free, can be modified based on service pricing
            
            try {
                Payment::create([
                    'service_request_id' => $serviceRequest->id,
                    'user_id' => $serviceRequest->guest_id, // Use guest_id from service request
                    'amount' => $serviceAmount,
                    'payment_method' => 'service_request', // Special method for service requests
                    'status' => 'completed', // Service requests are considered "paid" when submitted
                    'payment_date' => now(),
                    'notes' => 'Service request payment - ' . ($serviceRequest->service_type ?? 'Service')
                ]);
                
                \Log::info('Payment record created for service request:', [
                    'service_request_id' => $serviceRequest->id,
                    'amount' => $serviceAmount
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create payment record for service request:', [
                    'service_request_id' => $serviceRequest->id,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the service request creation if payment record fails
            }

            return redirect()->route('guest.services.history')
                           ->with('success', 'Your service request has been submitted successfully! We will contact you soon to confirm your booking.');

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error creating service request:', [
                'error' => $e->getMessage(),
                'sql' => $e->getSql() ?? 'N/A',
                'data' => $serviceRequestData ?? null,
                'available_columns' => $tableColumns ?? []
            ]);
            
            return back()->withInput()
                        ->with('error', 'Database error: ' . $e->getMessage() . ' | Available columns: ' . implode(', ', $tableColumns ?? []));
                        
        } catch (\Exception $e) {
            \Log::error('General error creating service request:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'data' => $serviceRequestData ?? null,
                'available_columns' => $tableColumns ?? []
            ]);
            
            return back()->withInput()
                        ->with('error', 'Error: ' . $e->getMessage() . ' | Available columns: ' . implode(', ', $tableColumns ?? []));
        }
    }

    public function history()
    {
        try {
            $user = Auth::user();
            
            // Load service requests WITH the service relationship (this is key for pricing)
            $serviceRequests = ServiceRequest::with(['service']) // Add this relationship
                ->where(function($query) use ($user) {
                    $query->where('guest_id', $user->id)
                          ->orWhere('user_id', $user->id)
                          ->orWhere('guest_email', $user->email);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Calculate stats
            $allRequests = ServiceRequest::where(function($query) use ($user) {
                $query->where('guest_id', $user->id)
                      ->orWhere('user_id', $user->id)
                      ->orWhere('guest_email', $user->email);
            });

            $pendingRequests = (clone $allRequests)->whereIn('status', ['pending', 'confirmed'])->count();
            $inProgressRequests = (clone $allRequests)->whereIn('status', ['assigned', 'in_progress'])->count();
            $completedRequests = (clone $allRequests)->where('status', 'completed')->count();
            $cancelledRequests = (clone $allRequests)->where('status', 'cancelled')->count();
            $totalRequests = (clone $allRequests)->count();

            return view('guest.services.history', compact(
                'serviceRequests',
                'pendingRequests',
                'inProgressRequests',
                'completedRequests',
                'cancelledRequests',
                'totalRequests'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error in guest services history: ' . $e->getMessage());
            
            return view('guest.services.history', [
                'serviceRequests' => collect()->paginate(10),
                'pendingRequests' => 0,
                'inProgressRequests' => 0,
                'completedRequests' => 0,
                'cancelledRequests' => 0,
                'totalRequests' => 0
            ]);
        }
    }

    public function cancel(ServiceRequest $serviceRequest)
    {
        // Check if user owns this request
        if ($serviceRequest->guest_id !== auth()->id()) { // Changed from user_id to guest_id
            abort(403, 'Unauthorized');
        }

        // Check if cancellation is allowed (you may need to implement this method)
        if (in_array($serviceRequest->status, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'This booking cannot be cancelled.');
        }

        $serviceRequest->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'manager_notes' => ($serviceRequest->manager_notes ?? '') . "\nCancelled by guest on " . now()->format('M d, Y g:i A')
        ]);

        return redirect()->route('guest.services.history')
                       ->with('success', 'Booking cancelled successfully.');
    }

    public function requestsHistory()
    {
        $serviceRequests = ServiceRequest::where('guest_id', auth()->id())
            ->orWhere('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $pendingRequests = $serviceRequests->where('status', 'pending')->count();
        $inProgressRequests = $serviceRequests->whereIn('status', ['assigned', 'in_progress'])->count();
        $completedRequests = $serviceRequests->where('status', 'completed')->count();
        $totalRequests = $serviceRequests->count();
        
        return view('guest.services.requests.history', compact(
            'serviceRequests',
            'pendingRequests', 
            'inProgressRequests', 
            'completedRequests', 
            'totalRequests'
        ));
    }

    /**
     * Show specific service request
     */
    public function showRequest($id)
    {
        $user = Auth::user();
        
        $serviceRequest = ServiceRequest::where(function($query) use ($user) {
            $query->where('guest_id', $user->id)
                  ->orWhere('user_id', $user->id)
                  ->orWhere('guest_email', $user->email);
        })
        ->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'request' => $serviceRequest
            ]);
        }

        return view('guest.services.show-request', compact('serviceRequest'));
    }

    /**
     * Cancel a service request
     */
    public function cancelRequest($id)
    {
        $user = Auth::user();
        
        $serviceRequest = ServiceRequest::where(function($query) use ($user) {
            $query->where('guest_id', $user->id)
                  ->orWhere('user_id', $user->id)
                  ->orWhere('guest_email', $user->email);
        })
        ->findOrFail($id);

        // Only allow cancellation if the request is not already cancelled or completed
        if (in_array($serviceRequest->status, ['cancelled', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'This request cannot be cancelled.'
            ], 400);
        }

        try {
            $serviceRequest->update([
                'status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service request cancelled successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error cancelling service request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel service request.'
            ], 500);
        }
    }

    /**
     * Permanently delete a service request
     */
    public function deleteRequest($id)
    {
        $user = Auth::user();
        
        $serviceRequest = ServiceRequest::where(function($query) use ($user) {
            $query->where('guest_id', $user->id)
                  ->orWhere('user_id', $user->id)
                  ->orWhere('guest_email', $user->email);
        })
        ->findOrFail($id);

        // Only allow deletion of cancelled requests or completed requests older than 30 days
        if ($serviceRequest->status !== 'cancelled' && 
            !($serviceRequest->status === 'completed' && $serviceRequest->completed_at && $serviceRequest->completed_at < now()->subDays(30))) {
            return response()->json([
                'success' => false,
                'message' => 'Only cancelled requests or completed requests older than 30 days can be deleted.'
            ], 400);
        }

        try {
            $serviceRequest->delete();
            
            \Log::info('Service request deleted successfully', [
                'id' => $id,
                'user_id' => $user->id,
                'status' => $serviceRequest->status
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Service request deleted successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting service request: ' . $e->getMessage(), [
                'id' => $id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service request.'
            ], 500);
        }
    }

    /**
     * Delete all cancelled service requests for the current user
     */
    public function deleteAllCancelled()
    {
        $user = Auth::user();
        
        try {
            $deletedCount = ServiceRequest::where(function($query) use ($user) {
                $query->where('guest_id', $user->id)
                      ->orWhere('user_id', $user->id)
                      ->orWhere('guest_email', $user->email);
            })
            ->where('status', 'cancelled')
            ->delete();
            
            \Log::info('Bulk deleted cancelled service requests', [
                'user_id' => $user->id,
                'deleted_count' => $deletedCount
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "All cancelled service requests deleted successfully.",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting cancelled service requests: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cancelled service requests.'
            ], 500);
        }
    }
}
