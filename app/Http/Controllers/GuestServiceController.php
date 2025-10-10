<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\ServiceRequest;
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

            return redirect()->route('guest.services.index')
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
        $requests = ServiceRequest::where('guest_id', auth()->id()) // Changed from user_id to guest_id
                                ->with('service')
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);
        
        return view('guest.services.history', compact('requests'));
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

        return redirect()->route('guest.services.requests.history')
                       ->with('success', 'Booking cancelled successfully.');
    }
}
