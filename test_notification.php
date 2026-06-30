<?php
require_once __DIR__ . '/includes/send_fcm_notification.php';

// Notify all admins
sendAdminNotification(
    '📩 New Inquiry Received!',
    'submitted a new inquiry.',
    'admin/inquiries.php'
);

// OR notify just one admin if needed later
// sendNotificationToAdmin(
//     $assignedAdminId,
//     '🧳 Booking Assigned to You',
//     $customerName . ' booked "' . $packageName . '".',
//     'admin/bookings.php'
// );
?>