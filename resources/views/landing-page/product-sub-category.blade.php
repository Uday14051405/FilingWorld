@extends('landing-page.layouts.default')


@section('content')
<div class="section-padding">
    <div class="container">
<!-- <landing-category-section></landing-category-section> -->
    <product-subcategory-page link="{{ route('product.subcategory.data' , ['category_id' => $category_id]) }}"></product-subcategory-page>
    </div>
</div>
@endsection
