

@extends('landing-page.layouts.default')

@section('content')
<div class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-12">

                <!-- Tab panes -->
                <product-page link="{{ route('product.data', ['id' => $id, 'type' => $type, 'latitude' => $latitude, 'longitude' => $longitude, 'section' => $section]) }}"></product-page>


            </div>
        </div>
    </div>
</div>

@endsection
