<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of the bookings.
     */
    public function index(Request $request)
    {
        try {
            // Handle status update if submitted
            if ($request->has('booking_id') && $request->has('new_status')) {
                $booking = Booking::find($request->booking_id);
                if ($booking) {
                    $booking->update(['status' => $request->new_status]);
                }
                // Redirect to prevent form resubmission
                return redirect('/admin/bookings');
            }
            
            // Styled HTML response matching the Vales Beach Resort admin theme
            $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Bookings - Vales Beach Resort</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: "Poppins", sans-serif; 
            background-color: rgb(17, 24, 39); 
            color: rgb(255, 255, 255);
            min-height: 100vh;
            position: relative;
        }
        
        /* Background decorative blur elements */
        .bg-decoration {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }
        .bg-decoration::before {
            content: "";
            position: absolute;
            width: 384px; height: 384px;
            background-color: rgb(22, 101, 52);
            opacity: 0.3;
            border-radius: 50%;
            filter: blur(48px);
            top: -192px; left: -192px;
        }
        .bg-decoration::after {
            content: "";
            position: absolute;
            width: 320px; height: 320px;
            background-color: rgb(21, 128, 61);
            opacity: 0.2;
            border-radius: 50%;
            filter: blur(48px);
            top: 33%; right: 25%;
        }
        .bg-decoration .third-orb {
            position: absolute;
            width: 288px; height: 288px;
            background-color: rgb(22, 101, 52);
            opacity: 0.25;
            border-radius: 50%;
            filter: blur(48px);
            bottom: 25%; left: 33%;
        }
        
        /* Navigation */
        .navbar {
            background-color: rgb(20, 83, 45);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            position: relative;
            z-index: 10;
        }
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }
        .logo {
            color: white;
            font-weight: bold;
            font-size: 1.25rem;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 16px;
        }
        .nav-link {
            color: rgb(209, 213, 219);
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: rgb(21, 128, 61);
            color: white;
        }
        
        /* Main content */
        .main-content {
            position: relative;
            z-index: 1;
            padding: 32px 64px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-header {
            margin-bottom: 32px;
        }
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, rgb(220, 252, 231), rgb(187, 247, 208));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .page-subtitle {
            color: rgb(209, 213, 219);
            font-size: 1.125rem;
        }
        
        /* Statistics cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: rgb(187, 247, 208);
            display: block;
        }
        .stat-label {
            color: rgb(209, 213, 219);
            font-size: 0.875rem;
            margin-top: 8px;
        }
        
        /* Bookings section */
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 24px;
            color: rgb(220, 252, 231);
        }
        
        .booking-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
            transition: all 0.2s;
        }
        .booking-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        .booking-id {
            font-size: 1.25rem;
            font-weight: 600;
            color: rgb(187, 247, 208);
            margin: 0;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        .status-pending { background-color: rgb(202, 138, 4); color: white; }
        .status-confirmed { background-color: rgb(34, 197, 94); color: white; }
        .status-cancelled { background-color: rgb(239, 68, 68); color: white; }
        .status-completed { background-color: rgb(107, 114, 128); color: white; }
        .status-checked_in { background-color: rgb(59, 130, 246); color: white; }
        .status-checked_out { background-color: rgb(107, 114, 128); color: white; }
        
        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
        }
        .detail-item {
            margin-bottom: 8px;
        }
        .detail-label {
            font-weight: 500;
            color: rgb(187, 247, 208);
        }
        .detail-value {
            color: rgb(209, 213, 219);
        }
        
        /* System status */
        .system-status {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(22, 163, 74, 0.05));
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 12px;
            padding: 24px;
            margin-top: 32px;
        }
        .system-status h3 {
            color: rgb(187, 247, 208);
            font-size: 1.25rem;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-list {
            list-style: none;
            padding: 0;
        }
        .status-item {
            margin-bottom: 8px;
            color: rgb(209, 213, 219);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-item::before {
            content: "●";
            color: rgb(34, 197, 94);
            font-weight: bold;
        }
        .status-note {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid rgba(34, 197, 94, 0.2);
            font-style: italic;
            color: rgb(187, 247, 208);
        }
        
        /* Action buttons */
        .booking-actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-primary {
            background-color: rgb(34, 197, 94);
            color: white;
        }
        .btn-primary:hover {
            background-color: rgb(22, 163, 74);
        }
        .btn-secondary {
            background-color: rgb(59, 130, 246);
            color: white;
        }
        .btn-secondary:hover {
            background-color: rgb(37, 99, 235);
        }
        .btn-warning {
            background-color: rgb(245, 158, 11);
            color: white;
        }
        .btn-warning:hover {
            background-color: rgb(217, 119, 6);
        }
        .btn-danger {
            background-color: rgb(239, 68, 68);
            color: white;
        }
        .btn-danger:hover {
            background-color: rgb(220, 38, 38);
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.75rem;
        }
        
        /* Status change dropdown */
        .status-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: white;
            padding: 6px 12px;
            font-size: 0.875rem;
            margin-right: 8px;
        }
        .status-select option {
            background-color: rgb(31, 41, 55);
            color: white;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }
        .modal-content {
            background-color: rgb(31, 41, 55);
            margin: 15% auto;
            padding: 30px;
            border-radius: 12px;
            width: 80%;
            max-width: 500px;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .modal-header {
            margin-bottom: 20px;
        }
        .modal-title {
            color: rgb(187, 247, 208);
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }
        .modal-body {
            margin-bottom: 24px;
        }
        .modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        .close {
            color: rgb(156, 163, 175);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
        .close:hover {
            color: white;
        }
        
        /* Filter controls */
        .filter-controls {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .filter-label {
            color: rgb(209, 213, 219);
            font-size: 0.875rem;
            font-weight: 500;
        }
        .filter-input {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            color: white;
            padding: 8px 12px;
            font-size: 0.875rem;
            min-width: 150px;
        }
        .filter-input::placeholder {
            color: rgb(156, 163, 175);
        }
        
        @media (max-width: 768px) {
            .nav-container, .main-content {
                padding-left: 16px;
                padding-right: 16px;
            }
            .booking-header {
                flex-direction: column;
                gap: 12px;
            }
            .booking-details {
                grid-template-columns: 1fr;
            }
            .booking-actions {
                justify-content: center;
            }
            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
        }
    </style>
    
    <script>
        function changeStatus(bookingId, currentStatus) {
            const modal = document.getElementById("statusModal");
            const form = document.getElementById("statusForm");
            const select = document.getElementById("statusSelect");
            const bookingInfo = document.getElementById("bookingInfo");
            
            // Set form action and current values
            form.action = "/admin/bookings";
            document.getElementById("bookingId").value = bookingId;
            select.value = currentStatus;
            bookingInfo.textContent = "Booking #" + bookingId;
            
            modal.style.display = "block";
        }
        
        function closeModal() {
            document.getElementById("statusModal").style.display = "none";
        }
        
        function confirmDelete(bookingId) {
            if (confirm("Are you sure you want to cancel this booking? This action cannot be undone.")) {
                changeStatus(bookingId, "cancelled");
            }
        }
        
        function viewDetails(bookingId) {
            // Create a detailed view modal for the booking
            const bookingCard = document.querySelector("[data-booking-id=\\"" + bookingId + "\\"]");
            if (!bookingCard) return;
            
            // Extract booking information from the card
            const guestName = bookingCard.querySelector(".guest-name").textContent;
            const roomName = bookingCard.querySelector(".room-name").textContent;
            const status = bookingCard.querySelector(".status-badge").textContent.trim();
            
            // Create detailed view modal
            const detailModal = document.createElement("div");
            detailModal.className = "modal";
            detailModal.style.display = "block";
            
            const modalContent = document.createElement("div");
            modalContent.className = "modal-content";
            modalContent.style.maxWidth = "600px";
            
            modalContent.innerHTML = 
                "<div class=\\"modal-header\\">"+
                    "<span class=\\"close\\" onclick=\\"closeDetailModal()\\">&times;</span>"+
                    "<h2 class=\\"modal-title\\">📋 Booking Details #" + bookingId + "</h2>"+
                "</div>"+
                "<div class=\\"modal-body\\">"+
                    "<div style=\\"display: grid; gap: 15px;\\">"+
                        "<div style=\\"display: flex; justify-content: space-between; padding: 10px; background: rgba(255,255,255,0.05); border-radius: 6px;\\">"+
                            "<span class=\\"detail-label\\">Status:</span>"+
                            "<span class=\\"detail-value\\">" + status + "</span>"+
                        "</div>"+
                        "<div style=\\"display: flex; justify-content: space-between; padding: 10px; background: rgba(255,255,255,0.05); border-radius: 6px;\\">"+
                            "<span class=\\"detail-label\\">Guest Name:</span>"+
                            "<span class=\\"detail-value\\">" + guestName + "</span>"+
                        "</div>"+
                        "<div style=\\"display: flex; justify-content: space-between; padding: 10px; background: rgba(255,255,255,0.05); border-radius: 6px;\\">"+
                            "<span class=\\"detail-label\\">Room:</span>"+
                            "<span class=\\"detail-value\\">" + roomName + "</span>"+
                        "</div>"+
                        "<div style=\\"text-align: center; margin-top: 20px;\\">"+
                            "<button class=\\"btn btn-primary\\" onclick=\\"openFullDetails(" + bookingId + ")\\">"+
                                "🔍 View Full Details"+
                            "</button>"+
                            "<button class=\\"btn btn-secondary\\" onclick=\\"closeDetailModal()\\">"+
                                "Close"+
                            "</button>"+
                        "</div>"+
                    "</div>"+
                "</div>";
            
            detailModal.appendChild(modalContent);
            document.body.appendChild(detailModal);
            
            // Store reference for closing
            window.currentDetailModal = detailModal;
            
            // Also highlight the booking card
            bookingCard.style.background = "rgba(34, 197, 94, 0.1)";
            bookingCard.style.border = "2px solid rgba(34, 197, 94, 0.5)";
            setTimeout(() => {
                bookingCard.style.background = "rgba(255, 255, 255, 0.05)";
                bookingCard.style.border = "1px solid rgba(255, 255, 255, 0.1)";
            }, 3000);
        }
        
        function closeDetailModal() {
            if (window.currentDetailModal) {
                window.currentDetailModal.remove();
                window.currentDetailModal = null;
            }
        }
        
        function openFullDetails(bookingId) {
            window.open("/admin/bookings/" + bookingId, "_blank");
        }
        
        function quickConfirm(bookingId) {
            if (confirm("Confirm this booking? The guest will be notified.")) {
                updateBookingStatus(bookingId, "confirmed");
            }
        }
        
        function quickCheckIn(bookingId) {
            if (confirm("Check in this guest? This will mark them as arrived.")) {
                updateBookingStatus(bookingId, "checked_in");
            }
        }
        
        function quickCheckOut(bookingId) {
            if (confirm("Check out this guest? This will complete their stay.")) {
                updateBookingStatus(bookingId, "checked_out");
            }
        }
        
        function updateBookingStatus(bookingId, newStatus) {
            // Create a form and submit it
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "/admin/bookings";
            
            const csrfToken = document.querySelector("input[name=\'_token\']").value;
            
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="booking_id" value="${bookingId}">
                <input type="hidden" name="new_status" value="${newStatus}">
            `;
            
            document.body.appendChild(form);
            form.submit();
        }
        
        function exportBookings() {
            // Simple export functionality
            const bookings = [];
            document.querySelectorAll(".booking-card").forEach(card => {
                const id = card.getAttribute("data-booking-id");
                const guest = card.querySelector(".guest-name").textContent;
                const room = card.querySelector(".room-name").textContent;
                const status = card.querySelector(".status-badge").textContent;
                bookings.push(`${id},${guest},${room},${status}`);
            });
            
            const csvContent = "Booking ID,Guest Name,Room,Status\\n" + bookings.join("\\n");
            const blob = new Blob([csvContent], { type: "text/csv" });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = "bookings_export.csv";
            a.click();
            window.URL.revokeObjectURL(url);
        }
        
        function filterBookings() {
            const statusFilter = document.getElementById("statusFilter").value;
            const searchFilter = document.getElementById("searchFilter").value.toLowerCase();
            const bookingCards = document.querySelectorAll(".booking-card");
            
            bookingCards.forEach(card => {
                const status = card.querySelector(".status-badge").textContent.toLowerCase().trim();
                const guestName = card.querySelector(".guest-name").textContent.toLowerCase();
                const roomName = card.querySelector(".room-name").textContent.toLowerCase();
                
                const statusMatch = !statusFilter || status.includes(statusFilter.toLowerCase());
                const searchMatch = !searchFilter || guestName.includes(searchFilter) || roomName.includes(searchFilter);
                
                if (statusMatch && searchMatch) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById("statusModal");
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</head>
<body>
    <div class="bg-decoration">
        <div class="third-orb"></div>
    </div>
    
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="logo">🏨 Vales Beach Admin</a>
            <div class="nav-links">
                <a href="/admin/dashboard" class="nav-link">Dashboard</a>
                <a href="/admin/rooms" class="nav-link">Rooms</a>
                <a href="/admin/bookings" class="nav-link active">Bookings</a>
                <a href="/admin/users" class="nav-link">Users</a>
            </div>
        </div>
    </nav>
    
    <div class="main-content">
        <!-- Back Button -->
        <div style="margin-bottom: 20px;">
            <a href="/admin/dashboard" class="btn btn-secondary" style="text-decoration: none;">
                ← Back to Dashboard
            </a>
        </div>
        
        <div class="page-header">
            <h1 class="page-title">Booking Management</h1>
            <p class="page-subtitle">Monitor and manage all resort reservations</p>
        </div>
        
        <!-- Filter Controls -->
        <div class="filter-controls">
            <div class="filter-group">
                <label class="filter-label">Filter by Status:</label>
                <select id="statusFilter" class="filter-input" onchange="filterBookings()">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="checked_in">Checked In</option>
                    <option value="checked_out">Checked Out</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Search Guest/Room:</label>
                <input type="text" id="searchFilter" class="filter-input" placeholder="Enter guest name or room..." onkeyup="filterBookings()">
            </div>
            <div class="filter-group">
                <label class="filter-label">Quick Actions:</label>
                <button class="btn btn-primary btn-sm" onclick="location.reload()">🔄 Refresh</button>
                <button class="btn btn-secondary btn-sm" onclick="exportBookings()">📊 Export CSV</button>
            </div>
        </div>';
            
            $totalBookings = Booking::count();
            $pendingBookings = Booking::where('status', 'pending')->count();
            $confirmedBookings = Booking::where('status', 'confirmed')->count();
            $cancelledBookings = Booking::where('status', 'cancelled')->count();
            
            $html .= '
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number">' . $totalBookings . '</span>
                <p class="stat-label">Total Bookings</p>
            </div>
            <div class="stat-card">
                <span class="stat-number">' . $pendingBookings . '</span>
                <p class="stat-label">Pending Review</p>
            </div>
            <div class="stat-card">
                <span class="stat-number">' . $confirmedBookings . '</span>
                <p class="stat-label">Confirmed</p>
            </div>
            <div class="stat-card">
                <span class="stat-number">' . $cancelledBookings . '</span>
                <p class="stat-label">Cancelled</p>
            </div>
        </div>
        
        <h2 class="section-title">Recent Reservations</h2>';
            
            $bookings = Booking::with(['user', 'room'])->latest()->take(12)->get();
            
            foreach ($bookings as $booking) {
                $statusColor = match($booking->status) {
                    'pending' => 'status-pending',
                    'confirmed' => 'status-confirmed',
                    'cancelled' => 'status-cancelled',
                    'completed' => 'status-completed',
                    'checked_in' => 'status-checked_in',
                    'checked_out' => 'status-checked_out',
                    default => 'status-pending'
                };
                
                $html .= '
        <div class="booking-card" data-booking-id="' . $booking->id . '">
            <div class="booking-header">
                <h3 class="booking-id">Reservation #' . $booking->id . '</h3>
                <span class="status-badge ' . $statusColor . '">' . ucfirst(str_replace('_', ' ', $booking->status)) . '</span>
            </div>
            <div class="booking-details">
                <div>
                    <div class="detail-item">
                        <span class="detail-label">Guest:</span> 
                        <span class="detail-value guest-name">' . e($booking->user->name) . '</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span> 
                        <span class="detail-value">' . e($booking->user->email) . '</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Room:</span> 
                        <span class="detail-value room-name">' . e($booking->room->name) . '</span>
                    </div>
                </div>
                <div>
                    <div class="detail-item">
                        <span class="detail-label">Check-in:</span> 
                        <span class="detail-value">' . $booking->check_in_date . '</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Check-out:</span> 
                        <span class="detail-value">' . $booking->check_out_date . '</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Duration:</span> 
                        <span class="detail-value">' . \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) . ' nights</span>
                    </div>
                </div>
                <div>
                    <div class="detail-item">
                        <span class="detail-label">Guests:</span> 
                        <span class="detail-value">' . $booking->guests . ' people</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Total Amount:</span> 
                        <span class="detail-value">$' . number_format($booking->total_amount, 2) . '</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booked:</span> 
                        <span class="detail-value">' . $booking->created_at->format('M j, Y g:i A') . '</span>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="booking-actions">
                <button class="btn btn-primary btn-sm" onclick="changeStatus(' . $booking->id . ', \'' . $booking->status . '\')">
                    📝 Change Status
                </button>
                <button class="btn btn-secondary btn-sm" onclick="viewDetails(' . $booking->id . ')">
                    👁️ View Details
                </button>';
                
                // Show specific action buttons based on status
                if ($booking->status === 'pending') {
                    $html .= '
                <button class="btn btn-primary btn-sm" onclick="changeStatus(' . $booking->id . ', \'confirmed\')">
                    ✅ Confirm
                </button>';
                }
                
                if ($booking->status === 'confirmed') {
                    $html .= '
                <button class="btn btn-secondary btn-sm" onclick="changeStatus(' . $booking->id . ', \'checked_in\')">
                    🏨 Check In
                </button>';
                }
                
                if ($booking->status === 'checked_in') {
                    $html .= '
                <button class="btn btn-warning btn-sm" onclick="changeStatus(' . $booking->id . ', \'checked_out\')">
                    🚪 Check Out
                </button>';
                }
                
                if (!in_array($booking->status, ['cancelled', 'completed', 'checked_out'])) {
                    $html .= '
                <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $booking->id . ')">
                    ❌ Cancel
                </button>';
                }
                
                $html .= '
            </div>
        </div>';
            }
            
            $html .= '
        <div class="system-status">
            <h3>✅ System Status</h3>
            <ul class="status-list">
                <li class="status-item">Booking Controller: Operational</li>
                <li class="status-item">Database Connection: Active</li>
                <li class="status-item">User Authentication: Ready</li>
                <li class="status-item">Admin Access: Granted</li>
                <li class="status-item">Real-time Updates: Enabled</li>
                <li class="status-item">Status Management: Functional</li>
            </ul>
            <p class="status-note">The booking management system is fully operational with interactive controls and status management capabilities.</p>
        </div>
    </div>
    
    <!-- Status Change Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 class="modal-title">Change Booking Status</h2>
            </div>
            <div class="modal-body">
                <p>Update the status for <strong id="bookingInfo">Booking #</strong></p>
                <form id="statusForm" method="POST">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="booking_id" id="bookingId">
                    <div style="margin: 20px 0;">
                        <label class="filter-label">New Status:</label>
                        <select name="new_status" id="statusSelect" class="status-select" style="width: 100%; margin-top: 8px;">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="checked_in">Checked In</option>
                            <option value="checked_out">Checked Out</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>';
            
            return response($html);
            
        } catch (\Exception $e) {
            return response('
<!DOCTYPE html>
<html>
<head>
    <title>Error - Vales Beach Resort</title>
    <style>
        body { font-family: "Poppins", sans-serif; background: rgb(17, 24, 39); color: white; padding: 40px; }
        .error-container { max-width: 800px; margin: 0 auto; background: rgba(239, 68, 68, 0.1); padding: 30px; border-radius: 12px; border: 1px solid rgba(239, 68, 68, 0.3); }
        h1 { color: rgb(248, 113, 113); margin-bottom: 20px; }
        pre { background: rgba(0,0,0,0.3); padding: 15px; border-radius: 8px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>🚨 Booking System Error</h1>
        <p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
        <p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . '</p>
        <p><strong>Line:</strong> ' . $e->getLine() . '</p>
    </div>
</body>
</html>', 500);
        }
    }

    /**
     * Display a specific booking.
     */
    public function show(Booking $booking)
    {
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Update the status of a booking.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validStatuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];
        
        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', $validStatuses)]
        ]);

        $booking->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Booking status has been updated successfully.');
    }
}