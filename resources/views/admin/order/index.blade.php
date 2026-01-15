@extends('layouts.app')

@section('header-title', __('Orders'))

@section('content')
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs">
            @php
                use App\Enums\OrderStatus;
                $orderStatuses = OrderStatus::cases();
            @endphp


                    <li class="nav-item">
                        <a href="{{ route('admin.order.index') }}"
                        class="nav-link {{ request()->url() === route('admin.order.index') ? 'active' : '' }}">
                        {{ __('All') }}
                        {{-- <span class="count statusAll">{{ $allOrders > 99 ? '99+' : $allOrders }}</span> --}}
                        </a>
                    </li>

                    @foreach ($orderStatuses as $status)

                    <li class="nav-item">
                        <a href="{{ route('admin.order.index', str_replace(' ', '_', $status->value)) }}"
                            class="nav-link {{ request()->url() === route('admin.order.index', str_replace(' ', '_', $status->value)) ? 'active' : '' }}">
                            <span>{{ __($status->value) }}</span>
                        </a>
                    </li>
                    @endforeach

            </ul>
            <div class="table-responsive">

                <table class="table border-left-right table-responsive-lg">
                    <thead>
                        <tr>
                            <th style="min-width: 85px">{{ __('Order ID') }}</th>
                            <th>{{ __('Order Date') }}</th>
                            <th>{{ __('Customer') }}</th>
                            @if ($businessModel == 'multi')
                                <th>{{ __('Shop') }}</th>
                            @endif
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Payment Method') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="w-auto">{{ $order->prefix . $order->order_code }}</td>
                                <td class="w-min">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                <td class="w-min">{{ $order->customer?->user?->name }}</td>

                                @if ($businessModel == 'multi')
                                    <td class="w-min">
                                        {{ $order->shop?->name }}
                                    </td>
                                @endif
                                <td class="w-min">
                                    {{ showCurrency($order->payable_amount) }}
                                    <br>
                                    <span class="badge rounded-pill text-bg-primary">{{ $order->payment_status }}</span>
                                </td>
                                <td class="w-min">{{ $order->payment_method }}</td>
                                <td class="w-min">
                                    @hasPermission('admin.order.show')
                                        <a href="{{ route('admin.order.show', $order->id) }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-title="{{ __('view details') }}"
                                            class="circleIcon svg-bg">
                                            <img src="{{ asset('assets/icons-admin/eye.svg') }}" alt="icon"
                                                loading="lazy" />
                                        </a>
                                    @endhasPermission
                                    <a href="{{ route('shop.download-invoice', $order->id) }}" data-bs-toggle="tooltip"
                                        data-bs-placement="left" data-bs-title="{{ __('Download Invoice') }}"
                                        class="circleIcon btn-outline-secondary">
                                        <img src="{{ asset('assets/icons-admin/download-alt.svg') }}" alt="icon"
                                            loading="lazy" />
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">
                                    {{ __('No order found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

        </div>
    </div>

    <div class="my-3">
        {{ $orders->links() }}
    </div>

@endsection
