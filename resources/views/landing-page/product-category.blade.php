@extends('landing-page.layouts.default')


@section('content')
<div class="section-padding">
    <div class="container">
<!-- <landing-category-section></landing-category-section> -->
    @if($submenuId)
        <product-category-page link="{{ route('product.category.data', ['submenu_id' => $submenuId]) }}"></product-category-page>
    @else
        <product-category-page link="{{ route('product.category.data') }}"></product-category-page>
    @endif
    </div>
</div>
@endsection
