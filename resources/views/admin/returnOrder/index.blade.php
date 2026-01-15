@extends('layouts.app')

@section('header-title', __('Orders'))

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table border-left-right table-responsive-lg">
                    <thead>
                        <tr>
                            <th style="min-width: 85px">{{ __('Order ID') }}</th>
                            <th>{{ __('Return Date') }}</th>
                            <th>{{ __('Customer') }}</th>
                            @if ($businessModel == 'multi')
                                <th>{{ __('Shop') }}</th>
                            @endif
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($returnOrder as $order)
                            <tr>
                                <td class="w-auto">{{ $order->order->prefix . $order->order->order_code }}</td>
                                <td class="w-min">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                <td class="w-min">{{ $order->customer?->user?->name }}</td>

                                @if ($businessModel == 'multi')
                                    <td class="w-min">
                                        {{ $order->shop?->name }}
                                    </td>
                                @endif
                                <td class="w-min">
                                    {{ showCurrency($order->amount) }}
                                </td>
                                <td class="w-min">
                                    {{ $order->status }}
                                </td>
                                <td>
                                    <button class="badge rounded-pill text-bg-{{ $order->payment_status ? 'success' : 'danger' }}">{{ $order->payment_status ? 'Paid' : 'Unpaid' }}</button>
                                </td>
                                <td class="w-min">
                                    @hasPermission('admin.returnOrder.show')
                                        <a href="{{ route('admin.returnOrder.show', $order->id) }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-title="{{ __('view details') }}"
                                            class="circleIcon svg-bg">
                                            <img src="{{ asset('assets/icons-admin/eye.svg') }}" alt="icon"
                                                loading="lazy" />
                                        </a>
                                    @endhasPermission
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
        {{ $returnOrder->links() }}
    </div>

@endsection
