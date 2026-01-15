@extends('layouts.app')
@section('header-title', __('Subscription List'))

@section('content')
    <div>
        <h4>{{ __('Subscription List') }}</h4>
    </div>

    <form action="" method="GET" class="card card-body">

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <x-select label="Shop" name="shop">
                    <option value="">
                        {{ __('All Shop') }}
                    </option>
                    @foreach ($shops as $shop)
                        <option value="{{ $shop->id }}" {{ request('shop') == $shop->id ? 'selected' : '' }}>
                            {{ $shop->name }}
                        </option>
                    @endforeach
                </x-select>
            </div>
        </div>

        <div class="mt-2 d-flex gap-2 justify-content-end">
            <a href="{{ route('admin.subscription-plan.subscription.list') }}" class="btn btn-light py-2 px-4">
                {{ __('Reset') }}
            </a>
            <button type="submit" class="btn btn-primary py-2 px-4">
                {{ __('Filter Data') }}
            </button>
        </div>
    </form>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border-left-right table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}.</th>
                                <th>{{ __('Plan') }}</th>
                                <th class="text-center">{{ __('Shop') }}</th>
                                <th class="text-center">{{ __('Price') }}</th>
                                <th class="text-center">{{ __('Duration') }}</th>
                                <th class="text-center" style="min-width: 120px">{{ __('Sale Limit') }}</th>
                                <th class="text-center" style="min-width: 120px">{{ __('Remaining Sales') }}</th>
                                <th class="text-center" style="min-width: 120px">{{ __('Payment Method') }}</th>
                                <th class="text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        @forelse($subscriptions as $key => $subscription)
                            <tr>
                                <td class="text-center">{{ ++$key }}</td>

                                <td>
                                    {{ $subscription->plan?->name ?? '' }}
                                </td>

                                <td>{{ $subscription->shop->name }}</td>

                                <td class="text-center">
                                    {{ showCurrency($subscription->price) }}
                                </td>

                                <td class="text-center">
                                    {{ $subscription->duration }}
                                </td>
                                <td class="text-center">
                                    {{ $subscription->sale_limit }}
                                </td>
                                <td class="text-center">
                                    {{ $subscription->remaining_sales }}
                                </td>
                                <td class="text-center">
                                    {{ $subscription->payment->payment_method }}
                                </td>

                                <td class="text-center">
                                    @if ($subscription->status == 'pending')
                                        <div class="d-flex gap-3 justify-content-center">
                                            @hasPermission('admin.subscription-plan.subscription.status')
                                                <a href="{{ route('admin.subscription-plan.subscription.status', $subscription->id) }}"
                                                    class="btn btn-danger btn-sm confirmApprove">{{ __('Pending') }}</a>
                                            @endhasPermission
                                        </div>
                                    @else
                                        <a class="btn btn-success btn-sm ">{{ __('Approved') }}</a>
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $subscriptions->withQueryString()->links() }}
        </div>

        <form action="" method="POST" class="d-none" id="deleteForm">
            @csrf
            @method('DELETE')
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        $(".confirmApprove").on("click", function(e) {
            e.preventDefault();
            const url = $(this).attr("href");
            Swal.fire({
                title: "Are you sure?",
                text: "You want to approve this Subscription",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Approve it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script>
@endpush
