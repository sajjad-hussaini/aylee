@extends('backend.layouts.master')

@section('title','Order Detail')

@section('main-content')
<div class="card">
<h5 class="card-header">Order       <a href="{{route('order.pdf',$order->id)}}" class=" btn btn-sm btn-primary shadow-sm float-right"><i class="fas fa-download fa-sm text-white-50"></i> Generate PDF</a>
  </h5>
  <div class="card-body">
    @if($order)
    <div class="order-track-section mb-4">
      <h4 class="text-center pb-3">Order Tracking</h4>
      @php
          $statusMessage = [
              'new' => 'Your order has been placed and is awaiting confirmation.',
              'process' => 'Your order is being processed and prepared for shipping.',
              'delivered' => 'Your order has been delivered successfully.',
              'cancel' => 'Your order has been canceled.'
          ][$order->status] ?? 'Order status unknown.';
      @endphp
      <div class="tracking-status-bar mb-3">
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
      <div class="text-center mb-4"><strong>{{ $statusMessage }}</strong></div>

      @if($order->statusHistory->count())
        <div class="order-history mb-4">
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
    <table class="table table-striped table-hover">
      <thead>
        <tr>
            <th>S.N.</th>
            <th>Order No.</th>
            <th>Name</th>
            <th>Email</th>
            <th>Quantity</th>
            <th>Charge</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
            <td>{{$order->id}}</td>
            <td>{{$order->order_number}}</td>
            <td>{{$order->first_name}} {{$order->last_name}}</td>
            <td>{{$order->email}}</td>
            <td>{{$order->quantity}}</td>
            <td>RS {{$order->total_amount}}</td>
            <td>RS {{number_format($order->total_amount,2)}}</td>
            <td>
                @if($order->status=='new')
                  <span class="badge badge-primary">{{$order->status}}</span>
                @elseif($order->status=='process')
                  <span class="badge badge-warning">{{$order->status}}</span>
                @elseif($order->status=='delivered')
                  <span class="badge badge-success">{{$order->status}}</span>
                @else
                  <span class="badge badge-danger">{{$order->status}}</span>
                @endif
            </td>
            <td>
                <a href="{{route('order.edit',$order->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                <form method="POST" action="{{route('order.destroy',[$order->id])}}">
                  @csrf
                  @method('delete')
                      <button class="btn btn-danger btn-sm dltBtn" data-id={{$order->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                </form>
            </td>

        </tr>
      </tbody>
    </table>

    <section class="confirmation_part section_padding">
      <div class="order_boxes">
        <div class="row">
          <div class="col-lg-6 col-lx-4">
            <div class="order-info">
              <h4 class="text-center pb-4">ORDER INFORMATION</h4>
              <table class="table">
                    <tr class="">
                        <td>Order Number</td>
                        <td> : {{$order->order_number}}</td>
                    </tr>
                    <tr>
                        <td>Order Date</td>
                        <td> : {{$order->created_at->format('D d M, Y')}} at {{$order->created_at->format('g : i a')}} </td>
                    </tr>
                    <tr>
                        <td>Quantity</td>
                        <td> : {{$order->quantity}}</td>
                    </tr>
                    <tr>
                        <td>Order Status</td>
                        <td> : {{$order->status}}</td>
                    </tr>
                    <tr>
                        <td>Shipping Charge</td>
                        <td> : $ {{$order->total_amount}}</td>
                    </tr>
                    <tr>
                      <td>Coupon</td>
                      <td> : $ {{number_format($order->coupon,2)}}</td>
                    </tr>
                    <tr>
                        <td>Total Amount</td>
                        <td> : $ {{number_format($order->total_amount,2)}}</td>
                    </tr>
                    <tr>
                        <td>Payment Method</td>
                        <td> : @if($order->payment_method=='cod') Cash on Delivery @else Paypal @endif</td>
                    </tr>
                    <tr>
                        <td>Payment Status</td>
                        <td> : {{$order->payment_status}}</td>
                    </tr>
              </table>
            </div>
          </div>

          <div class="col-lg-6 col-lx-4">
            <div class="shipping-info">
              <h4 class="text-center pb-4">SHIPPING INFORMATION</h4>
              <table class="table">
                    <tr class="">
                        <td>Full Name</td>
                        <td> : {{$order->first_name}} {{$order->last_name}}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td> : {{$order->email}}</td>
                    </tr>
                    <tr>
                        <td>Phone No.</td>
                        <td> : {{$order->phone}}</td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td> : {{$order->address1}}, {{$order->address2}}</td>
                    </tr>
                    <tr>
                        <td>Country</td>
                        <td> : {{$order->country}}</td>
                    </tr>
                    <tr>
                        <td>Post Code</td>
                        <td> : {{$order->post_code}}</td>
                    </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
    @endif

  </div>
</div>
@endsection

@push('styles')
<style>
    .order-info,.shipping-info{
        background:#ECECEC;
        padding:20px;
    }
    .order-info h4,.shipping-info h4{
        text-decoration: underline;
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
