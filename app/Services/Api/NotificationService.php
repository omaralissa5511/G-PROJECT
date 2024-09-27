<?php
namespace App\Services\Api;

use App\Models\Notification;
use App\Models\Notification as NotificationModel;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationService
{
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated.',
                'status' => false
            ]);
        }

        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        if ($notifications->isEmpty()) {
            return response()->json([
                'message' => 'No notifications found.',
                'status' => false
            ]);
        }

        $formattedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s')
            ];
        });

        return response()->json([
            'message' => 'User notifications retrieved successfully.',
            'data' => $formattedNotifications,
            'status' => true
        ]);
    }




    public function send($user, $title, $message, $type = 'basic')
{

// Path to the service account key JSON file
$serviceAccountPath = storage_path('app/firebase.json');

// Initialize the Firebase Factory with the service account
$factory = (new Factory)->withServiceAccount($serviceAccountPath);

// Create the Messaging instance
$messaging = $factory->createMessaging();

// Prepare the notification array
$notification = [
'title' => $title,
'body' => $message,
'sound' => 'default',
];

// Additional data payload
$data = [
'type' => $type,
'id' => $user['id'],
'message' => $message,
];

// Get all tokens for the user
$tokens = DeviceToken::where('user_id', $user['id'])->pluck('token')->toArray();

foreach ($tokens as $token) {
    // Create the CloudMessage instance
$cloudMessage = CloudMessage::withTarget('token', $token)
->withNotification($notification)
->withData($data);

try {
// Send the notification
$messaging->send($cloudMessage);
} catch (\Kreait\Firebase\Exception\MessagingException $e) {
Log::error($e->getMessage());
} catch (\Kreait\Firebase\Exception\FirebaseException $e) {
Log::error($e->getMessage());
}
}
echo $message;
// Save the notification to the database
Notification::query()->create([
'type' => 'App\Notifications\UserFollow',
'notifiable_type' => 'App\Models\User',
'notifiable_id' => $user['id'],
'message' => $message,
'title' => $title,
'data' => json_encode([
'message' => $message,
'title' => $title,
]), // The data of the notification
]);

return 1;
}

public function markAsRead($notificationId): bool
{
$notification = auth()->user()->notifications()->findOrFail($notificationId);

if (isset($notification)) {
$notification->markAsRead();
return true;
} else {
return false;
}
}

public function destroy($id): bool
{
$notification = auth()->user()->notifications()->findOrFail($id);

if (isset($notification)) {
$notification->delete();
return true;
} else {
return false;
}
}
}
