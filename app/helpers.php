<?php
declare(strict_types=1);

namespace App;

use App\Http\Resources\User\IndexResource;

use App\Models\Users\User;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

/**
 * Class helpers
 * @package App
 */
class helpers
{
    /**
     * Get a sample filename from user file name. This is done in order to exclude Cyrillic and long names.
     *
     * @param $file
     * @return string
     */
    public static function getImageNameByFileName($file): string
    {
        return md5($file . time()) . '.' . $file->getClientOriginalExtension();
    }
}

if (!function_exists('send_push_notify'))
{
    /**
     * @param int|string $topic
     * @param string $title
     * @param string $body
     * @param string $eventName
     * @param string $another
     */
    function send_push_notify(string $type, string $title, string $body, User $user)
    {
        // if (App::isProduction()) {
            try {
                $token = $user->devices()->first();

                if ($token) {
                    $notification = Notification::fromArray([
                        'title' => $title,
                        'body' => $body,
                        'image' => asset('images/email/logo_pink.png'),
                    ]);

                    $data = [
                        'type' => $type,
                        'title' => $title,
                        'message_text' => $body,
                        'user' => IndexResource::make(\Auth::user())->toJson(),
                    ];

                    $apns = ApnsConfig::fromArray([
                        'headers' => [
                            'apns-priority' => '10',
                        ],
                        'payload' => [
                            'aps' => [
                                'alert' => [
                                    'title' => $title,
                                    'body' => $body,
                                ],
                                'badge' => $user->toMessages()->where('read_at', null)->count(),
                                'sound' => 'default',
                            ],
                        ],
                    ]);

                    $android = AndroidConfig::fromArray([
                        'ttl' => '3600s',
                        'priority' => 'normal',
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                            'icon' => asset('images/email/logo_pink.png'),
                            'color' => '#f45342',
                            'sound' => 'default',
                        ],
                    ]);

                    $message = CloudMessage::withTarget('token', $token->token)
                                            ->withNotification($notification)
                                            ->withData($data);
                    if ($token->type == 'ios') $message = $message->withApnsConfig($apns);
                    if ($token->type == 'android') $message = $message->withAndroidConfig($android);

                    $answer = Firebase::messaging()->send($message);

                }
            } catch (MessagingException | FirebaseException $e) {
                Log::debug($e->getMessage());
            }
        // }
    }
}

