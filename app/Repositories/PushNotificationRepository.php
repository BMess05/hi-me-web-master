<?php 
namespace App\Repositories;

use Edujugon\PushNotification\PushNotification;

class PushNotificationRepository 
{
    public function sendPushnotification($device_token,$title,$message)
    {
        $push = new PushNotification('fcm');
        $push->setConfig([
            'priority' => 'high',
            'time_to_live' => 3,
            'dry_run' => false
        ]);

        $extraNotificationData = [
            /* "message"    => $notificationData, */
            'title'      => $title,
            'body'       => $message,
            'sound'      => 'default',
            /*  'notificationType' => $type,
            'badge'      => $badge,
            'message'    => $message,
            'requestid'  => $requestid,
            'image'      => $notimage,
            'requestType' => $requestType */
        ];

        $push->setMessage([
            'notification' => [
                'title' => $title,
                'body'  => $message,
                'sound' => 'default'
            ],
            'data' => $extraNotificationData
        ])
            //->setApiKey('Server-API-Key')
            ->setApiKey('AAAA8xon_Uw:APA91bHIb3RbR168Eef47CUpYHf5Sg-Maz5SGTg75zjD7jfj2YRp1TSn4JvWxL6ABZLHkkP6uohnASk5EDj7PDkDgcRNrKgeoEv2EnudR7QnsnQwXIrMFFC8Rn3KWLDhTr0zRazSCoyb')
            ->setDevicesToken($device_token)
            ->send();
    }
}

?>