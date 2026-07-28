@extends('frontend.layouts.master')

@section('title','E-SHOP || Order Track Page')

@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Order Track</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
<section class="tracking_box_area section_gap py-5">
    <div class="container">
        <div class="tracking_box_inner">
            <p>To track your order please enter your Order ID in the box below and press the "Track" button. This was given
                to you on your receipt and in the confirmation email you should have received.</p>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form class="row tracking_form my-4" action="{{route('product.track.order')}}" method="post" novalidate="novalidate">
              @csrf
                <div class="col-md-8 form-group">
                    <input type="text" class="form-control p-2"  name="order_number" placeholder="Enter your order number" value="{{ old('order_number') }}">
                </div>
                <div class="col-md-8 form-group">
                    <button type="submit" value="submit" class="btn submit_btn">Track Order</button>
                </div>
            </form>

            @if(isset($order))
                <div class="order-track-result mt-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4>Order #{{ $order->order_number }}</h4>
                            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                            <p>{{ $statusMessage }}</p>

                            <div class="tracking-status-bar">
                                <ul class="tracking-steps">
                                    <li class="{{ in_array($order->status, ['new','process','delivered']) ? 'active' : '' }}">
                                        <span>Order Placed</span>
                                    </li>
                                    @if($order->status == 'cancel')
                                        <li class="active cancelled">
                                            <span>Cancelled</span>
                                        </li>
                                    @else
                                        <li class="{{ in_array($order->status, ['process','delivered']) ? 'active' : '' }}">
                                            <span>Processing</span>
                                        </li>
                                        <li class="{{ $order->status == 'delivered' ? 'active' : '' }}">
                                            <span>Delivered</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            @if($order->statusHistory->count())
                                <div class="order-history mt-4">
                                    <h5>Order Status Timeline</h5>
                                    <div class="order-timeline">
                                        @foreach($order->statusHistory as $history)
                                            <div class="order-timeline-item {{ $history->status == 'cancel' ? 'cancelled' : '' }}">
                                                <span class="order-timeline-marker"></span>
                                                <div class="timeline-card">
                                                    <div class="item-meta">{{ $history->created_at->format('d M Y h:i A') }} • {{ ucfirst($history->status) }}</div>
                                                    <div class="item-note">{{ $history->message }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.tracking-status-bar {
    margin-top: 1rem;
}
.tracking-steps {
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding: 0;
    margin: 0;
}
.tracking-steps li {
    flex: 1;
    min-width: 120px;
    margin: 5px;
    padding: 15px 10px;
    text-align: center;
    border-radius: 30px;
    background: #f3f3f3;
    color: #444;
    font-weight: 600;
}
.tracking-steps li.active {
    background: #28a745;
    color: #fff;
}
.tracking-steps li.cancelled {
    background: #dc3545 !important;
    color: #fff;
}
.order-timeline {
    position: relative;
    padding-left: 20px;
    margin-top: 20px;
}
.order-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e3e3;
}
.order-timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-left: 20px;
}
.order-timeline-marker {
    position: absolute;
    left: -5px;
    top: 4px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #28a745;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.25);
}
.order-timeline-item.cancelled .order-timeline-marker {
    background: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
}
.timeline-card {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 15px 18px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
}
.item-meta {
    font-weight: 700;
    color: #333;
    margin-bottom: 6px;
}
.item-note {
    color: #555;
}
</style>
@endpush