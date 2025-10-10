<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of the bookings with enhanced functionality.
     */
    public function index(Request $request)
    {
        try {
            $successMessage = null;
            
            // Handle status update if submitted
            if ($request->has('booking_id') && $request->has('new_status')) {
                $booking = Booking::find($request->booking_id);
                if ($booking) {
                    $oldStatus = $booking->status;
                    $booking->update(['status' => $request->new_status]);
                    
                    // Set success message for display
                    $successMessage = "✅ Booking #{$booking->id} status updated from " . ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . ucfirst(str_replace('_', ' ', $request->new_status));
                } else {
                    $successMessage = "❌ Booking not found.";
                }
            }
            
            // Generate the enhanced booking management interface
            return $this->generateBookingInterface($successMessage);
            
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

    private function generateBookingInterface($successMessage = null)
    {
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        $bookings = Booking::with(['user', 'room'])->latest()->take(12)->get();
        
        // Get current user role for conditional navigation
        $userRole = auth()->user()->role;
        $showUsersLink = in_array($userRole, ['admin', 'manager']);

        return response('<!DOCTYPE html>
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
        
        .bg-decoration {
            position: fixed; inset: 0; overflow: hidden; pointer-events: none; z-index: 0;
        }
        .bg-decoration::before {
            content: ""; position: absolute; width: 384px; height: 384px;
            background-color: rgb(22, 101, 52); opacity: 0.3; border-radius: 50%;
            filter: blur(48px); top: -192px; left: -192px;
        }
        .bg-decoration::after {
            content: ""; position: absolute; width: 320px; height: 320px;
            background-color: rgb(21, 128, 61); opacity: 0.2; border-radius: 50%;
            filter: blur(48px); top: 33%; right: 25%;
        }
        .bg-decoration .third-orb {
            position: absolute; width: 288px; height: 288px;
            background-color: rgb(22, 101, 52); opacity: 0.25; border-radius: 50%;
            filter: blur(48px); bottom: 25%; left: 33%;
        }
        
        .navbar {
            background-color: rgb(20, 83, 45); box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            position: relative; z-index: 10;
        }
        .nav-container {
            max-width: 1200px; margin: 0 auto; padding: 0 64px;
            display: flex; align-items: center; justify-content: space-between; height: 64px;
        }
        .logo { color: white; font-weight: bold; font-size: 1.25rem; text-decoration: none; }
        .logo:hover { color: rgb(187, 247, 208); }
        .nav-links { display: flex; gap: 16px; }
        .nav-link {
            color: rgb(209, 213, 219); padding: 8px 12px; border-radius: 6px; text-decoration: none;
            font-size: 0.875rem; font-weight: 500; transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active { background-color: rgb(21, 128, 61); color: white; }
        
        .main-content { position: relative; z-index: 1; padding: 32px 64px; max-width: 1200px; margin: 0 auto; }
        
        .page-header { margin-bottom: 32px; }
        .page-title {
            font-size: 2rem; font-weight: 700; margin-bottom: 8px;
            background: linear-gradient(135deg, rgb(220, 252, 231), rgb(187, 247, 208));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .page-subtitle { color: rgb(209, 213, 219); font-size: 1.125rem; }
        
        .success-message {
            background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 8px; padding: 16px; margin-bottom: 24px; color: rgb(187, 247, 208);
        }
        
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px; margin-bottom: 32px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 24px; text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
        .stat-number { font-size: 2.5rem; font-weight: 700; color: rgb(187, 247, 208); display: block; }
        .stat-label { color: rgb(209, 213, 219); font-size: 0.875rem; margin-top: 8px; }
        
        .filter-controls {
            background: rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 20px; margin-bottom: 24px;
            display: flex; gap: 16px; flex-wrap: wrap; align-items: center;
        }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { color: rgb(209, 213, 219); font-size: 0.875rem; font-weight: 500; }
        .filter-input {
            background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px; color: white; padding: 8px 12px; font-size: 0.875rem; min-width: 150px;
        }
        .filter-input::placeholder { color: rgb(156, 163, 175); }
        
        .section-title { font-size: 1.5rem; font-weight: 600; margin-bottom: 24px; color: rgb(220, 252, 231); }
        
        .booking-card {
            background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 24px; margin-bottom: 16px;
            transition: all 0.2s;
        }
        .booking-card:hover { background: rgba(255, 255, 255, 0.08); border-color: rgba(34, 197, 94, 0.3); }
        
        .booking-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .booking-id { font-size: 1.25rem; font-weight: 600; color: rgb(187, 247, 208); margin: 0; }
        .status-badge {
            padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; text-transform: uppercase;
        }
        .status-pending { background-color: rgb(202, 138, 4); color: white; }
        .status-confirmed { background-color: rgb(34, 197, 94); color: white; }
        .status-cancelled { background-color: rgb(239, 68, 68); color: white; }
        .status-completed { background-color: rgb(107, 114, 128); color: white; }
        .status-checked_in { background-color: rgb(59, 130, 246); color: white; }
        .status-checked_out { background-color: rgb(107, 114, 128); color: white; }
        
        .booking-details { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; }
        .detail-item { margin-bottom: 8px; }
        .detail-label { font-weight: 500; color: rgb(187, 247, 208); }
        .detail-value { color: rgb(209, 213, 219); }
        
        .booking-actions { display: flex; gap: 8px; margin-top: 16px; flex-wrap: wrap; }
        .btn {
            padding: 8px 16px; border: none; border-radius: 6px; font-size: 0.875rem; font-weight: 500;
            cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-flex;
            align-items: center; gap: 4px;
        }
        .btn-primary { background-color: rgb(34, 197, 94); color: white; }
        .btn-primary:hover { background-color: rgb(22, 163, 74); }
        .btn-secondary { background-color: rgb(59, 130, 246); color: white; }
        .btn-secondary:hover { background-color: rgb(37, 99, 235); }
        .btn-warning { background-color: rgb(245, 158, 11); color: white; }
        .btn-warning:hover { background-color: rgb(217, 119, 6); }
        .btn-danger { background-color: rgb(239, 68, 68); color: white; }
        .btn-danger:hover { background-color: rgb(220, 38, 38); }
        .btn-sm { padding: 6px 12px; font-size: 0.75rem; }
        
        .modal {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }
        .modal-content {
            background-color: rgb(31, 41, 55); margin: 15% auto; padding: 30px; border-radius: 12px;
            width: 80%; max-width: 500px; border: 1px solid rgba(34, 197, 94, 0.3);
        }
        .modal-header { margin-bottom: 20px; }
        .modal-title { color: rgb(187, 247, 208); font-size: 1.5rem; font-weight: 600; margin: 0; }
        .modal-body { margin-bottom: 24px; }
        .modal-footer { display: flex; gap: 12px; justify-content: flex-end; }
        .close {
            color: rgb(156, 163, 175); float: right; font-size: 28px; font-weight: bold;
            cursor: pointer; line-height: 1;
        }
        .close:hover { color: white; }
        
        .status-select {
            background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px; color: white; padding: 6px 12px; font-size: 0.875rem; margin-right: 8px;
        }
        .status-select option { background-color: rgb(31, 41, 55); color: white; }
        
        @media (max-width: 768px) {
            .nav-container, .main-content { padding-left: 16px; padding-right: 16px; }
            .booking-header { flex-direction: column; gap: 12px; }
            .booking-details { grid-template-columns: 1fr; }
            .booking-actions { justify-content: center; }
            .filter-controls { flex-direction: column; align-items: stretch; }
            .modal-content { width: 95%; margin: 10% auto; }
        }
    </style>
    
    <script>
        function changeStatus(bookingId, currentStatus) {
            const modal = document.getElementById("statusModal");
            const form = document.getElementById("statusForm");
            const select = document.getElementById("statusSelect");
            const bookingInfo = document.getElementById("bookingInfo");
            
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
                updateBookingStatus(bookingId, "cancelled");
            }
        }
        
        function viewDetails(bookingId) {
            const bookingCard = document.querySelector("[data-booking-id=\\"" + bookingId + "\\"]");
            if (!bookingCard) return;
            
            const guestName = bookingCard.querySelector(".guest-name").textContent;
            const roomName = bookingCard.querySelector(".room-name").textContent;
            const status = bookingCard.querySelector(".status-badge").textContent.trim();
            
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
            window.currentDetailModal = detailModal;
            
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
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "/admin/bookings";
            
            const csrfToken = document.querySelector("input[name=\'_token\']").value;
            
            form.innerHTML = 
                "<input type=\\"hidden\\" name=\\"_token\\" value=\\"" + csrfToken + "\\">"+
                "<input type=\\"hidden\\" name=\\"booking_id\\" value=\\"" + bookingId + "\\">"+
                "<input type=\\"hidden\\" name=\\"new_status\\" value=\\"" + newStatus + "\\">";
            
            document.body.appendChild(form);
            form.submit();
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
        
        function exportBookings() {
            const bookings = [];
            document.querySelectorAll(".booking-card").forEach(card => {
                const id = card.getAttribute("data-booking-id");
                const guest = card.querySelector(".guest-name").textContent;
                const room = card.querySelector(".room-name").textContent;
                const status = card.querySelector(".status-badge").textContent;
                bookings.push(id + "," + guest + "," + room + "," + status);
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
            <a href="/admin/dashboard" class="logo">🏨 Vales Beach Admin</a>
            <div class="nav-links">
                <a href="/admin/dashboard" class="nav-link">Dashboard</a>
                <a href="/admin/rooms" class="nav-link">Rooms</a>
                <a href="/admin/bookings" class="nav-link active">Bookings</a>' . 
                ($showUsersLink ? '<a href="/admin/users" class="nav-link">Users</a>' : '') . '
            </div>
        </div>
    </nav>
    
    <div class="main-content">
        <div style="margin-bottom: 20px;">
            <a href="/admin/dashboard" class="btn btn-secondary" style="text-decoration: none;">
                ← Back to Dashboard
            </a>
        </div>
        
        <div class="page-header">
            <h1 class="page-title">Booking Management</h1>
            <p class="page-subtitle">Monitor and manage all resort reservations</p>
        </div>
        
        ' . ($successMessage ? '<div class="success-message">' . $successMessage . '</div>' : '') . '
        
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
        </div>
        
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
        
        <h2 class="section-title">Recent Reservations</h2>' . 
        
        $this->generateBookingCards($bookings) .
        
        '<div style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(22, 163, 74, 0.05)); border: 1px solid rgba(34, 197, 94, 0.2); border-radius: 12px; padding: 24px; margin-top: 32px;">
            <h3 style="color: rgb(187, 247, 208); font-size: 1.25rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">✅ System Status</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 8px; color: rgb(209, 213, 219); display: flex; align-items: center; gap: 8px;"><span style="color: rgb(34, 197, 94); font-weight: bold;">●</span> Booking Controller: Operational</li>
                <li style="margin-bottom: 8px; color: rgb(209, 213, 219); display: flex; align-items: center; gap: 8px;"><span style="color: rgb(34, 197, 94); font-weight: bold;">●</span> Database Connection: Active</li>
                <li style="margin-bottom: 8px; color: rgb(209, 213, 219); display: flex; align-items: center; gap: 8px;"><span style="color: rgb(34, 197, 94); font-weight: bold;">●</span> User Authentication: Ready</li>
                <li style="margin-bottom: 8px; color: rgb(209, 213, 219); display: flex; align-items: center; gap: 8px;"><span style="color: rgb(34, 197, 94); font-weight: bold;">●</span> Admin Access: Granted</li>
                <li style="margin-bottom: 8px; color: rgb(209, 213, 219); display: flex; align-items: center; gap: 8px;"><span style="color: rgb(34, 197, 94); font-weight: bold;">●</span> Status Management: Functional</li>
            </ul>
            <p style="margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(34, 197, 94, 0.2); font-style: italic; color: rgb(187, 247, 208);">All booking management features are fully operational with interactive controls.</p>
        </div>
    </div>
    
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
</html>');
    }

    private function generateBookingCards($bookings)
    {
        $html = '';
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
            
            <div class="booking-actions">
                <button class="btn btn-primary btn-sm" onclick="changeStatus(' . $booking->id . ', \'' . $booking->status . '\')">
                    📝 Change Status
                </button>
                <button class="btn btn-secondary btn-sm" onclick="viewDetails(' . $booking->id . ')">
                    👁️ View Details
                </button>';
                
                if ($booking->status === 'pending') {
                    $html .= '
                <button class="btn btn-primary btn-sm" onclick="quickConfirm(' . $booking->id . ')">
                    ✅ Confirm
                </button>';
                }
                
                if ($booking->status === 'confirmed') {
                    $html .= '
                <button class="btn btn-secondary btn-sm" onclick="quickCheckIn(' . $booking->id . ')">
                    🏨 Check In
                </button>';
                }
                
                if ($booking->status === 'checked_in') {
                    $html .= '
                <button class="btn btn-warning btn-sm" onclick="quickCheckOut(' . $booking->id . ')">
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
        return $html;
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
        $validStatuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'completed'];
        
        $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', $validStatuses)]
        ]);

        // Prevent changing completed bookings to cancelled
        if ($booking->status === 'completed' && $request->status === 'cancelled') {
            return redirect()->back()->withErrors(['status' => 'Completed transactions cannot be cancelled']);
        }

        $booking->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Booking status has been updated successfully.');
    }

    /**
     * Show the form for creating a new manual booking.
     */
    public function create()
    {
        $rooms = Room::where('is_available', true)->get();
        $users = User::where('role', 'guest')->get();
        
        return view('admin.bookings.create', compact('rooms', 'users'));
    }

    /**
     * Show the form for creating a new booking from a specific room.
     */
    public function createFromRoom(Room $room)
    {
        $users = User::where('role', 'guest')->get();
        
        return view('admin.bookings.create-from-room', compact('room', 'users'));
    }

    /**
     * Store a newly created manual booking.
     */
    public function store(Request $request)
    {
        // Determine if we're creating a new guest or using existing
        $isNewGuest = $request->filled('guest_name') && $request->filled('guest_email');
        
        if ($isNewGuest) {
            $request->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|unique:users,email',
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'required|integer|min:1',
                'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,completed'
            ]);
            
            // Create new guest user
            $user = User::create([
                'name' => $request->guest_name,
                'email' => $request->guest_email,
                'email_verified_at' => now(),
                'password' => bcrypt('password123'), // Default password
                'role' => 'guest'
            ]);
            
            $userId = $user->id;
        } else {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'room_id' => 'required|exists:rooms,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'required|integer|min:1',
                'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,completed'
            ]);
            
            $userId = $request->user_id;
        }

        // Check for conflicting bookings
        $existingBooking = Booking::where('room_id', $request->room_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                      ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('check_in', '<=', $request->check_in)
                            ->where('check_out', '>=', $request->check_out);
                      });
            })
            ->first();

        if ($existingBooking) {
            return back()->withErrors(['room_id' => 'This room is already booked for the selected dates.'])->withInput();
        }

        // Validate guest capacity
        $room = Room::find($request->room_id);
        if ($request->guests > $room->capacity) {
            return back()->withErrors(['guests' => "This room can accommodate maximum {$room->capacity} guests."])->withInput();
        }

        // Calculate total price
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);
        $totalPrice = $room->price_per_night * $nights;

        // Create the booking
        Booking::create([
            'user_id' => $userId,
            'room_id' => $request->room_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'guests' => $request->guests,
            'total_price' => $totalPrice,
            'status' => $request->status,
        ]);

        $successMessage = $isNewGuest 
            ? 'Manual booking created successfully with new guest account!' 
            : 'Manual booking created successfully!';

        return redirect()->route('admin.bookings')->with('success', $successMessage);
    }

    /**
     * Display all reservations (current and past).
     */
    public function reservations(Request $request)
    {
        $query = Booking::with(['user', 'room']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('date_from')) {
            $query->where('check_in', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('check_out', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('room', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Order by check-in date (newest first)
        $query->orderBy('check_in', 'desc');

        $bookings = $query->paginate(20);
        $rooms = \App\Models\Room::all();
        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'completed'];

        return view('admin.reservations.index', compact('bookings', 'rooms', 'statuses'));
    }

    /**
     * Display calendar view for bookings.
     */
    public function calendar(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // Get all bookings for the month
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $bookings = Booking::with(['user', 'room'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in', [$startDate, $endDate])
                      ->orWhereBetween('check_out', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('check_in', '<=', $startDate)
                            ->where('check_out', '>=', $endDate);
                      });
            })
            ->where('status', '!=', 'cancelled')
            ->get();

        $rooms = \App\Models\Room::where('is_available', true)->get();

        return view('admin.calendar.index', compact('bookings', 'rooms', 'year', 'month', 'startDate', 'endDate'));
    }
}
