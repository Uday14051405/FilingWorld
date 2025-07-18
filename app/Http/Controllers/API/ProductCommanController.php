<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\City;
use App\Models\State;
use App\Models\Bank;
use App\Models\ProviderTaxMapping;
use App\Models\ProviderProductAddressMapping;
use App\Http\Resources\API\ProviderTaxResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\FrontendSetting;
use App\Models\ProviderType;
use App\Models\HandymanType;
use App\Models\CouponProductMapping;
use App\Models\Coupon;
use App\Models\Booking;
use App\Models\Tax;
use App\Models\AppSetting;
use App\Http\Resources\API\ServiceResource;
use App\Http\Resources\API\TypeResource;
use App\Http\Resources\API\BankResource;
use App\Http\Resources\API\CouponResource;
use App\Http\Resources\API\TaxResource;
use App\Models\Payment;
use PDF;

class ProductCommanController extends Controller
{
    public function getSearchList(Request $request){

        if($request->section){
            $section = FrontendSetting::where('key', $request->section)->first();
            $sec_data = json_decode($section->value, true);
            $serviceIds = $sec_data['service_id'] ?? [];

            $service = Product::where('service_type', 'product')
                ->with(['providers', 'category', 'serviceRating', 'translations'])
                ->orderByRaw('FIELD(id, '.implode(',', $serviceIds).') DESC')
                ->orderBy('created_at', 'desc');
            
            $cat_query = ProductCategory::where('submenu_category', 16)->pluck('id');

                $cat_ids = $cat_query->toArray();
                if($request->section == 'section_10'){
                    $service = $service->whereNotIn('category_id', $cat_ids);
                }elseif($request->section == 'section_11'){
                    $service = $service->whereIn('category_id', $cat_ids);
                }
        }else{
            $service = Product::where('status',1)->where('service_type','product')->with(['providers','category','serviceRating'])->orderBy('created_at','desc');
        }

        // $service = Product::where('status',1)->where('service_type','product')->with(['providers','category','serviceRating'])->orderBy('created_at','desc');
        if($request->has('provider_id') && $request->provider_id != '' ){
            $service->whereIn('provider_id',explode(',',$request->provider_id));
        }
        if($request->has('category_id') && $request->category_id != ''){
            $service->whereIn('category_id',explode(',',$request->category_id));
        }
        if($request->has('subcategory_id') && $request->subcategory_id != ''){
            $service->whereIn('subcategory_id',explode(',',$request->subcategory_id));
        }
        if($request->has('is_price_min') && $request->is_price_min != '' || $request->has('is_price_max') && $request->is_price_max != ''){
            $service->whereBetween('price', [$request->is_price_min, $request->is_price_max]);
        }
        if($request->has('search')){
            $service->where('name','like',"%{$request->search}%");
        }
        if($request->has('is_featured')){
            $service->where('is_featured',$request->is_featured);
        }
        if($request->has('type')){
            $service->where('type',$request->type);
        }
        if($request->has('provider_id') && $request->provider_id != '' ){
            $service->whereHas('providers', function ($a) use ($request) {
                $a->where('status', 1);
            });
        }else{
            if(default_earning_type() === 'subscription'){
                $service->whereHas('providers', function ($a) use ($request) {
                    $a->where('status', 1)->where('is_subscribe',1);
                });
            }
        }
        if ($request->has('latitude') && !empty($request->latitude) && $request->has('longitude') && !empty($request->longitude)) {
            $get_distance = getSettingKeyValue('site-setup','radious');
            $get_unit = getSettingKeyValue('site-setup','distance_type');

            $locations = $service->locationService($request->latitude,$request->longitude,$get_distance,$get_unit);
            $service_in_location = ProviderProductAddressMapping::whereIn('provider_address_id',$locations)->get()->pluck('service_id');
            $service->with('providerServiceAddress')->whereIn('id',$service_in_location);
        }
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page)){
                $per_page = $request->per_page;
            }
            if($request->per_page === 'all' ){
                $per_page = $service->count();
            }
        }

        if ($request->has('is_rating') && $request->is_rating != '') {
            $isRatings = array_map('floatval', explode(',', $request->is_rating));

            $service->whereHas('serviceRating', function ($q) use ($isRatings) {
                $conditions = implode(' OR ', array_fill(0, count($isRatings), '(AVG(rating) >= ? AND AVG(rating) <= ?)'));

                $q->select('service_id', \DB::raw('AVG(rating) as average_rating'))
                    ->groupBy('service_id')
                    ->havingRaw($conditions, array_reduce($isRatings, function ($carry, $item) {
                        return array_merge($carry, [$item, $item + 0.9]);
                    }, []));
            });
        }

        $service = $service->where('status',1)->paginate($per_page);

        $items = ServiceResource::collection($service);
        $userservices  = null;
        if($request->customer_id != null){
            $user_service = Product::where('status',1)->where('added_by',$request->customer_id)->get();
            $userservices = ServiceResource::collection($user_service);
        }
        $maxprice = (int) round($service->max('price'));
        $minprice = (int) round($service->min('price'));
        $response = [
            'data' => $items,
            'max'=> $maxprice,
            'min'=> $minprice,
            'userservices' => $userservices
        ];

        return comman_custom_response($response);
    }

    public function getCouponService(Request $request){
        $servicedata = CouponProductMapping::where('coupon_id',$request->coupon_id)->withTrashed();
        $service_id = $servicedata->pluck('service_id');
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page)){
                $per_page = $request->per_page;
            }
            if($request->per_page === 'all' ){
                $per_page = $taxes->count();
            }
        }
        $service = Product::whereIn('id',$service_id)->orderBy('id','desc')->paginate($per_page);
        $items = ServiceResource::collection($service);
        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'data' => $items,
        ];

        return comman_custom_response($items);
    }
}
