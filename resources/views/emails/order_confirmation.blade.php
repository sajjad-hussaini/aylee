<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333333;
            -webkit-text-size-adjust: none;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }
        .header {
            background-color: #1a202c;
            color: #ffffff;
            padding: 25px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 6px 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 0;
            font-size: 14px;
            color: #cbd5e1;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 25px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 4px 0;
        }
        .info-label {
            color: #64748b;
            font-weight: 500;
        }
        .info-value {
            color: #1e293b;
            font-weight: 600;
        }
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table.items-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        table.items-table td {
            padding: 12px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .totals-table td {
            padding: 6px 12px;
            font-size: 13px;
        }
        .totals-table .grand-total {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            border-top: 2px solid #e2e8f0;
            padding-top: 10px;
        }
        .address-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 16px;
            font-size: 13px;
            line-height: 1.6;
            color: #334155;
            margin-bottom: 25px;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            padding: 20px 30px;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ config('app.name', 'Luma') }}</h1>
            <p>Order Confirmation</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">Hello {{ $order->first_name }} {{ $order->last_name }},</div>
            <div class="subtitle">
                Thank you for your purchase! We have successfully received your order and are currently processing it.
            </div>

            <!-- Order Overview -->
            <div class="info-card">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="info-label" style="padding: 4px 0; font-size: 13px; color: #64748b;">Order Number:</td>
                        <td class="info-value text-right" style="padding: 4px 0; font-size: 13px; font-weight: 600; color: #1e293b;">#{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td class="info-label" style="padding: 4px 0; font-size: 13px; color: #64748b;">Order Date:</td>
                        <td class="info-value text-right" style="padding: 4px 0; font-size: 13px; font-weight: 600; color: #1e293b;">
                            {{ $order->created_at ? $order->created_at->format('M d, Y h:i A') : now()->format('M d, Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label" style="padding: 4px 0; font-size: 13px; color: #64748b;">Payment Method:</td>
                        <td class="info-value text-right" style="padding: 4px 0; font-size: 13px; font-weight: 600; color: #1e293b;">
                            {{ strtoupper($order->payment_method ?? 'COD') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label" style="padding: 4px 0; font-size: 13px; color: #64748b;">Payment Status:</td>
                        <td class="info-value text-right" style="padding: 4px 0; font-size: 13px; font-weight: 600; color: #1e293b;">
                            <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; text-transform: uppercase; background-color: {{ $order->payment_status === 'paid' ? '#dcfce7' : '#fef3c7' }}; color: {{ $order->payment_status === 'paid' ? '#166534' : '#92400e' }};">
                                {{ $order->payment_status ?? 'unpaid' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Ordered Items -->
            <div class="section-title">Items Ordered</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orderItems as $item)
                        @php
                            $title = optional($item->product)->title ?? $item->product_name ?? 'Product';
                            $price = (float) ($item->price ?? 0);
                            $qty = (int) ($item->quantity ?? 1);
                            $total = $item->amount ?? ($price * $qty);
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $title }}</strong>
                            </td>
                            <td class="text-center">{{ $qty }}</td>
                            <td class="text-right">Rs. {{ number_format($price, 2) }}</td>
                            <td class="text-right">Rs. {{ number_format($total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center" style="color: #94a3b8;">No item details available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Totals Breakdown -->
            <table class="totals-table">
                <tr>
                    <td class="text-right" style="color: #64748b;">Subtotal:</td>
                    <td class="text-right" style="width: 120px; font-weight: 500;">Rs. {{ number_format($order->sub_total, 2) }}</td>
                </tr>
                @php
                    $shippingCharge = optional($order->shipping)->price ?? ($order->delivery_charge ?? 0);
                @endphp
                <tr>
                    <td class="text-right" style="color: #64748b;">Shipping:</td>
                    <td class="text-right" style="width: 120px; font-weight: 500;">
                        {{ $shippingCharge > 0 ? 'Rs. ' . number_format($shippingCharge, 2) : 'Free' }}
                    </td>
                </tr>
                @if($order->coupon && $order->coupon > 0)
                <tr>
                    <td class="text-right" style="color: #16a34a;">Coupon Discount:</td>
                    <td class="text-right" style="width: 120px; color: #16a34a; font-weight: 500;">-Rs. {{ number_format($order->coupon, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-total">
                    <td class="text-right">Grand Total:</td>
                    <td class="text-right" style="color: #2563eb;">Rs. {{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>

            <!-- Shipping Address -->
            <div class="section-title">Shipping Details</div>
            <div class="address-box">
                <strong>{{ $order->first_name }} {{ $order->last_name }}</strong><br>
                {{ $order->address1 }}
                @if(!empty($order->address2))
                    , {{ $order->address2 }}
                @endif
                <br>
                @if(!empty($order->city))
                    {{ $order->city }}, 
                @endif
                {{ $order->country }}
                @if(!empty($order->post_code))
                    - {{ $order->post_code }}
                @endif
                <br>
                <strong>Phone:</strong> {{ $order->phone }}<br>
                <strong>Email:</strong> {{ $order->email }}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 6px 0;">If you have any questions or need assistance, please feel free to reach out to us.</p>
            <p style="margin: 0;">&copy; {{ date('Y') }} {{ config('app.name', 'Luma') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

