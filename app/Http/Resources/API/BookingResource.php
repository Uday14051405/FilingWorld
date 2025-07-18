<?php

namespace App\Http\Resources\API;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\BookingStatus;
use App\Models\Setting;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use App\Traits\TranslationTrait;
class BookingResource extends JsonResource
{
    use TranslationTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $headerValue = $request->header('language-code') ?? session()->get('locale', 'en');
        $extraValue = 0;
        if($this->bookingExtraCharge->count() > 0){
            foreach($this->bookingExtraCharge as $chrage){
                $extraValue += $chrage->price * $chrage->qty;
            }
        }
        $sitesetup = Setting::where('type','site-setup')->where('key', 'site-setup')->first();
        $datetime = json_decode($sitesetup->value);
        $payment = $this->payment()->orderBy('id','desc')->first();

        $service = optional($this->service);
        $service_name = null;
        $service_attachments = [];

        $price = null;
        $type = null;
        $discount = null;
        $advance_payment_amount = 0;

        $total_rating = 0;
        $total_review = 0;

        if ($this->type == 'service') {
            $service_name = $this->getTranslation($service->translations, $headerValue, 'name', $service->name) ?? $service->name;
            $service_attachments = getAttachments($service->getMedia('service_attachment') ?? [], null);

            $price = $service->price;
            $type = $service->type;
            $discount = $service->discount;
            $advance_payment_amount = $service->advance_payment_amount ?? 0;

            $total_rating = (float) number_format(max(optional($service->serviceRating)->avg('rating') ?? 0, 0), 2);
            $total_review = optional($service->serviceRating)->count() ?? 0;
        } elseif ($this->type == 'product') {
            $product = Product::find($this->service_id ?? null);
            if ($product) {
                $service_name = $this->getTranslation($product->translations, $headerValue, 'name', $product->name) ?? $product->name;
                $service_attachments = getAttachments($product->getMedia('service_attachment') ?? [], null);

                $price = $product->price;
                $type = $product->type; // If product has type (like 'hourly'), else default to 'fixed'
                $discount = $product->discount;
                $advance_payment_amount = $product->advance_payment_amount ?? 0;

                $total_rating = (float) number_format(max(optional($product->serviceRating)->avg('rating') ?? 0, 0), 2);
                $total_review = optional($product->serviceRating)->count() ?? 0;
            }
        }


        return [
            'id'                    => $this->id,
            'address'               => $this->address,
            'customer_id'           => $this->customer_id,
            'service_id'            => $this->service_id,
            'provider_id'           => $this->provider_id,
            'date'                  => $this->date,
            'booking_date'          => date("$datetime->date_format $datetime->time_format", strtotime($this->date)),
            'price' => $price,
            'type' => $type,
            'discount' => $discount,
            'status'                => $this->status,
            'status_label'          => BookingStatus::bookingStatus($this->status),
            'description'           => $this->description,
            'provider_name'         => optional($this->provider)->display_name,
            'customer_name'         => optional($this->customer)->display_name,
            'service_name'          => $service_name,
            'payment_id'            => $this->payment_id,
            'payment_status'        => $payment ? $payment->payment_status : null,
            'payment_method'        => $payment ? $payment->payment_type : null,
            'provider_name'         => optional($this->provider)->display_name ?? null,
            'customer_name'         => optional($this->customer)->display_name ?? null,
            'provider_image'        => getSingleMedia($this->provider, 'profile_image',null),
            'provider_is_verified'  => (bool) optional($this->provider)->is_verified,  
            'customer_image'        => getSingleMedia($this->customer, 'profile_image',null),
            // 'service_name'          => optional($this->service)->name ?? null,
            'handyman'              => isset($this->handymanAdded) ? $this->handymanAdded->map(function($handymanMapping) {
                $handymanMapping->handyman->handyman_image = getSingleMedia($handymanMapping->handyman, 'profile_image', null);
                $handymanMapping->handyman->is_verified = $handymanMapping->handyman->is_verified ? 1 : 0;
                return $handymanMapping;
            }) : [],
            // 'service_attchments'    => getAttachments(optional($this->service)->getMedia('service_attachment'),null),
            'service_attchments' => $service_attachments,
            'duration_diff'         => $this->duration_diff,
            'booking_address_id'    => $this->booking_address_id,
            'duration_diff_hour' => ($type === 'hourly') ? convertToHoursMins($this->duration_diff) : null,
            'taxes'                 => $this->getTaxData($this->tax),
            'quantity'              => $this->quantity,
            'coupon_data'           => isset($this->couponAdded) ? $this->couponAdded : null,
            'total_amount'          => $this->total_amount,
            'total_rating' => $total_rating,
            'amount'                => $this->amount,
            'extra_charges'         => BookingChargesResource::collection($this->bookingExtraCharge),
            'extra_charges_value'   => $extraValue,
            'booking_type'          => $this->type,
            'booking_slot'          => $this->booking_slot,
            'total_review' => $total_review,
            'booking_package'       => new BookingPackageResource($this->bookingPackage),
            'advance_paid_amount'   => $this->advance_paid_amount == null ? 0:(double) $this->advance_paid_amount,
            'advance_payment_amount'=> (bool) $advance_payment_amount,

        ];
    }

    private function getTaxData()
    {
        $taxData = json_decode($this->tax, true);
        if (is_array($taxData)) {
            $taxData = array_map(function ($item) {
                $item['id'] = (int) $item['id'];
                $item['value'] = (float) $item['value'];
                return $item;
            }, $taxData);
        } else {           
            $taxData = []; 
        }
    
        return $taxData;
    }
}
