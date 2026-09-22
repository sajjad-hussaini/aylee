<?php

namespace App\Services;

use App\Mail\NewOrderAdminMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderMailService
{
    /**
     * Send email notifications to customer and admin for a newly placed order.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public static function sendOrderNotifications(Order $order): void
    {
        try {
            // Eager load necessary relations
            $order->loadMissing(['items.product', 'cart_info.product', 'shipping', 'user']);

            // 1. Send confirmation email to Customer
            if (!empty($order->email)) {
                Mail::to($order->email)->send(new OrderConfirmationMail($order));
            }

            // 2. Resolve Admin email(s)
            // $adminEmails = User::where('role', 'admin')
            //     ->whereNotNull('email')
            //     ->pluck('email')
            //     ->filter()
            //     ->unique()
            //     ->toArray();

            $adminEmails[] = 'aylynasir@gmail.com';
            $fromEmail = config('mail.from.address');
            if (!empty($fromEmail)) {
                $adminEmails[] = $fromEmail;
            }

            if (empty($adminEmails)) {
                $settingEmail = Settings::first()?->email;
                if (!empty($settingEmail)) {
                    $adminEmails[] = $settingEmail;
                }
            }


            // Send notification email to Admin(s)
            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->send(new NewOrderAdminMail($order));
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send order email for order #{$order->order_number}: " . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

