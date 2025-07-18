@extends('landing-page.layouts.default')

@section('content')

<div class="section-padding">
    <div class="container">
    <product-booking-wizard  :service="{{ $service }}" :coupons="{{ $coupons }}" :taxes="{{ $taxes }}" :user_id="{{$user_id}}" :availableserviceslot="{{ json_encode($availableserviceslot) }}" :serviceaddon="{{ isset($serviceaddon) ? $serviceaddon : 'null' }}"  :googlemapkey="'{{$googlemapkey}}'" :wallet_amount="{{$wallet_amount}}"></product-booking-wizard>
    </div>
</div>

@endsection