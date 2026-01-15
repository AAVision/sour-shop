@extends('layouts.app')
@section('header-title', __('Order Details'))

@section('content')

    <div class="row my-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between gap-2 py-3">
                    <h4 class="card-title mb-0">{{ __('Return Order Details') }}</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap align-items-center">
                        <div class="flex-grow-1">
                            <div class="order-item">
                                <label class="label">{{ __('Order Id') }}:</label>
                                <span
                                    class="value">#{{ $returnOrder->order->prefix . $returnOrder->order->order_code }}</span>
                            </div>
                            <div class="order-item">
                                <label class="label">{{ __('Payment Status') }}:</label>
                                <span class="value">{{ $returnOrder->order->payment_status }}</span>
                            </div>
                            <div class="order-item">
                                <label class="label">{{ __('Order Status') }}:</label>
                                <span class="value">{{ $returnOrder->order->order_status }}</span>
                            </div>
                            <div class="order-item">
                                <label class="label">{{ __('Order Date') }}:</label>
                                <span class="value">{{ $returnOrder->order->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>

                        <div class="item-divider"></div>

                        <div class="flex-grow-1">
                            <div class="order-item">
                                <label class="label">{{ __('Return Status') }}:</label>
                                <span class="value">{{ $returnOrder->status }}</span>
                            </div>
                            <div class="order-item">
                                <label class="label">{{ __('Return Payment Status') }}:</label>
                                <span class="value">{{ $returnOrder->payment_status ? 'Paid' : 'Unpaid' }}</span>
                            </div>
                            <div class="order-item">
                                <label class="label">{{ __('Return Date') }}:</label>
                                <span
                                    class="value">{{ $returnOrder->created_at ? Carbon\Carbon::parse($returnOrder->created_at)->format('M d, Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4 mb-0">
                        <table class="table border-left-right">
                            <thead>
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Size') }}</th>
                                    <th>{{ __('Color') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th class="text-end">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($returnOrder->returnProduct as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex gap-1 align-items-center">
                                                <img src="{{ $product->product->thumbnail }}" alt="" width="40"
                                                    height="40" loading="lazy">
                                                <span>{{ $product->product->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $product->quantity ?? 0 }}</td>
                                        <td>{{ $product->size ?? '-' }}</td>
                                        <td>{{ $product->color ?? '-' }}</td>
                                        <td>
                                            {{ showCurrency($product->price) }}
                                        </td>
                                        <td class="text-end">
                                            {{ showCurrency($product->quantity * $product->price) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="max-300 ms-auto d-flex flex-column gap-1">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div> <strong>{{ __('Total') }}</strong></div>
                            <div>{{ showCurrency($returnOrder->amount) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!--##### return reason #####-->
            <div class="mt-3 card">
                <h5 class="fz-16 border-bottom px-3 py-12 m-0">{{ __('Return Reason') }}</h5>

                <div class="border-bottom px-3 py-2 d-flex  align-items-center gap-3">
                    <span class="fw-medium">{{ $returnOrder->reason }}</span>
                </div>
            </div>

            <!--##### Customer Info #####-->
            <div class="mt-3 card">
                <h5 class="fz-16 border-bottom px-3 py-12 m-0">{{ __('Customer Info') }}</h5>

                <div class="border-bottom px-3 py-2 d-flex  align-items-center gap-3">
                    <span class="text-color">{{ __('Name') }}: </span>
                    <span class="fw-medium">{{ $returnOrder->customer?->user?->name }}</span>
                </div>
                <div class="px-3 py-2 d-flex  align-items-center gap-3">
                    <span class="text-color">{{ __('Phone') }}: </span>
                    <span class="fw-medium">{{ $returnOrder->customer?->user?->phone }}</span>
                </div>
            </div>

            @if ($returnOrder->status == 'Cancelled')
                <!--##### Cancelled Info #####-->
                <div class="mt-3 card">
                    <h5 class="fz-16 border-bottom px-3 py-12 m-0">{{ __('Cancel Reason') }}</h5>

                    <div class="border-bottom px-3 py-2 d-flex  align-items-center gap-3">
                        <span class="fw-medium">{{ $returnOrder->reject_note }}</span>
                    </div>
                </div>
            @endif

        </div>

        <div class="col-lg-4">
            <!--#####  Info #####-->
            <div class="card">
                <h5 class="fz-18 border-bottom p-3 m-0">{{ __('Return Order Status') }}</h5>

                <div class="px-3 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                    <div class="text-color">{{ __('Change Order Status') }}</div>
                    <div class="dropdown">
                        <a class="btn border text-start dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $returnOrder->status }}
                        </a>
                        @if ($returnOrder->status != 'Cancelled' && $returnOrder->status != 'Refunded')
                            @hasPermission(['shop.order.status.change'])
                                <ul class="dropdown-menu order-status">
                                    @foreach ($returnStatus as $status)
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('shop.returnOrder.status.change', $returnOrder->id) }}?status={{ $status->value }}">
                                                {{ __($status->value) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endhasPermission
                        @endif
                    </div>
                </div>

                <div class="border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 p-3">
                    <div class="text-color">{{ __('Payment Status') }}</div>
                    <div class="d-flex align-items-center gap-1">
                        <span>{{ $returnOrder->payment_status ? 'Paid' : 'Unpaid' }}</span>
                    </div>
                </div>


            </div>
            <!--##### Bank Info #####-->
            <div class="card">
                <h5 class="fz-18 border-bottom p-3 m-0">{{ __('Bank Information') }}</h5>
                <div class="border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 p-3">
                    <div class="text-color">{{ __('Account No') }}</div>
                    <div class="d-flex align-items-center gap-1">
                        <span>{{ $returnOrder->bank_account_number }}</span>
                    </div>
                </div>

            </div>

            <!--##### Shipping Address #####-->
            <div class="card mt-3">
                <h5 class="fz-18 border-bottom p-3 m-0">{{ __('Return Address') }}</h5>

                <div class="border-bottom d-flex align-items-center justify-content-between gap-2 px-3 py-12">
                    <span class="fw-medium">{{ $returnOrder->return_address }}</span>
                </div>
            </div>

        </div>
    </div>

@endsection
@push('css')
    <style>
        .dropdown-menu.order-status {
            min-width: 200px;
            padding: 8px;
            border: 1px solid #e5e5e5;
            box-shadow: 0 0 10px #e5e5e5;
        }

        .dropdown-menu.order-status .dropdown-item {
            border-bottom: 1px solid #f1f1f1;
        }

        .app-theme-dark .dropdown-menu.order-status {
            border: 1px solid #343a40;
            box-shadow: 0 0 10px #343a40;
        }

        .app-theme-dark .dropdown-menu.order-status .dropdown-item {
            border-bottom: 1px solid #343a40;
        }

        .max-300 {
            max-width: 340px;
        }

        .min-w-200 {
            min-width: 200px;
            display: inline;
        }

        .item-divider {
            height: 80px;
            width: 1px;
            background: #e5e5e5;
            margin: 0 20px;
        }

        .app-theme-dark .item-divider {
            background: #343a40;
        }

        .order-item {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .order-item:last-child {
            margin-bottom: 0;
        }

        .order-item .label {
            color: #687387;
            line-height: 22px;
        }

        .app-theme-dark .order-item .label {
            color: #8f96a6;
        }

        .order-item .value {
            line-height: 22px;
            font-weight: 500;
            color: #000;
        }

        .app-theme-dark .order-item .value {
            color: #fff;
        }

        @media (max-width: 768px) {
            .item-divider {
                display: none;
            }
        }
    </style>
@endpush
