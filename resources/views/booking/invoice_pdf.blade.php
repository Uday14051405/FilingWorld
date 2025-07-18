<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Inter;
        }

        .column {
            float: left;
            width: 30%;
            padding: 0 10px;
        }

        .row {
            margin: 0 -5px;
        }

        .row:after {
            content: "";
            display: table;
            clear: both;
        }

        .card {
            padding: 16px;
            text-align: center;
            background-color: #F6F7F9;
        }
        table tr td{
            font-size: 14px;
        }
        table thead th{
            font-size: 14px;
        }
    </style>
</head>
<?php
    use App\Models\Setting;
    $settings = Setting::whereIn('type', ['site-setup', 'general-setting'])
        ->whereIn('key', ['site-setup', 'general-setting'])
        ->get()
        ->keyBy('key');

    $app = isset($settings['site-setup']) ? json_decode($settings['site-setup']->value) : null;
    $generaldata = isset($settings['general-setting']) ? json_decode($settings['general-setting']->value) : null;

    $extraValue = 0;
?>
<body>
    <div style="padding: 24px 0 0;">
        <div style="padding-bottom: 16px; margin-bottom: 16px; border-bottom:  1px solid #ccc;">
            <div style="overflow: hidden;">
                <div style="float: left; display: inline-block;">
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height: 40px;">
                </div>
                <div style="float:right; text-align:right;">
                    <span style="color:#6C757D;">{{ __('messages.invoice_date') }}:</span><span style="color: #1C1F34; padding-right: 60px;">
                        {{ \Carbon\Carbon::parse($bookingdata->date)->format('Y-m-d') ?? '-' }}</span>
                    <span style="color:#6C757D;">  {{ __('messages.invoice_id') }}-</span><span style="color: #5F60B9;"> {{ '#' . $bookingdata->id ?? '-'}}</span>
                </div>
            </div>
        </div>
        <div>
            <p style="color: #6C757D; margin-bottom: 16px;">Thanks, you have already completed the payment for this
                invoice</p>
        </div>
        <div style="margin-bottom: 16px;">
            <div style="overflow: hidden;">
                <div style="float: left; width: 75%; display: inline-block;">
                    <h5 style="color: #1C1F34; margin: 0;">Organization information:</h5>
                    <p style="color: #6C757D;  margin-top: 12px; margin-bottom: 0;">For any questions or support
                        regarding this invoice or our services, please contact us via phone or email</p>
                </div>
                <div style="float:left; width: 25%; text-align:right;">
                    <span style="color: #1C1F34; margin-bottom: 12px;">{{ $generaldata->inquriy_email}}</span>
                    <p style="color: #1C1F34;  margin-top: 12px; margin-bottom: 0;">{{ $generaldata->helpline_number}}</p>
                </div>
            </div>
        </div>
        {{-- PAYMENT INFORMATION --}}
        <div>
            <h5 style="color: #1C1F34; margin-top: 0;">{{__('messages.payment_info')}} :</h5>
            <div style="background: #F6F7F9; padding:8px 24px;">
                <div style="display: inline-block;">
                    <span style="color: #1C1F34;">{{__('messages.payment_method')}}::</span>
                    <span style="color: #6C757D; margin-left: 16px;">{{ isset($payment) ? ucfirst($payment->payment_type) : '-' }}</span>
                </div>
                <div style="display: inline-block; padding-left: 24px;">
                    <span style="color: #1C1F34;">{{ __('messages.payment_status') }} ::</span>
                        @if(isset($payment) && $payment->payment_status)
                            <span style="color: #219653; margin-left: 16px;" >
                                {{ str_replace('_', ' ', ucfirst($payment->payment_status)) }}
                            </span>
                        @else
                            <span style="color: #FB2F2F; margin-left: 16px;">
                                {{ __('messages.pending') }}
                            </span>   
                        @endif
                </div>
            </div>
        </div>

        {{-- PERSON INFORMATION --}}

        <div style="padding: 16px 0;">
            <div class="row">
                @if ($bookingdata->customer)

                <div class="column">
                    <h5 style="margin: 8px 0;">{{__('messages.customer')}}:</h5>
                    <div class="card" style="text-align: start;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34">{{ __('messages.name') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($bookingdata->customer)->display_name ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">{{ __('messages.contact_number') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional($bookingdata->customer)->contact_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">{{ __('messages.address') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($bookingdata->customer)->address ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @if ($bookingdata->provider)
                <div class="column">
                    <h5 style="margin: 8px 0;">{{__('messages.provider')}}:</h5>
                    <div class="card">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34">{{ __('messages.name') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($bookingdata->provider)->display_name ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">{{ __('messages.contact_number') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional($bookingdata->provider)->contact_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34;">{{ __('messages.address') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{ optional($bookingdata->provider)->address ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @if(count($bookingdata->handymanAdded) > 0)
                @foreach($bookingdata->handymanAdded as $booking)
                <div class="column">
                    <h5 style="margin: 8px 0;">{{__('messages.handyman')}}:</h5>
                    <div class="card">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tbody style="background: #F6F7F9;">
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34; width:50%;">{{ __('messages.name') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($booking->handyman)->display_name ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34; width:50%;">{{ __('messages.contact_number') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($booking->handyman)->contact_number ?? '-'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding:4px; text-align: start; color: #1C1F34; width:50%;">{{ __('messages.address') }}:</td>
                                    <td style="padding:4px; text-align: start; color: #6B6B6B;">{{optional($booking->handyman)->address ?? '-'}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>

        {{-- TABLE  --}}
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc;">
            <tr> 
            <thead style="background: #F6F7F9;">
                <th style="padding:12px 30px; text-align: start;">{{__('messages.service')}}</th>
                <th style="padding:12px 30px; text-align: end;">{{__('messages.Price')}}</th>
                @if($bookingdata->service->type  == 'hourly')
                    <th style="padding:12px 30px; text-align: end;">{{__('messages.hour')}}</th>
                @else
                    <th style="padding:12px 30px; text-align: end;">{{__('messages.Qty')}}</th>
                @endif
                <th style="padding:12px 30px; text-align: end;">{{__('messages.amount')}}</th>
            </thead>

            </tr>
            
            <tbody>
                <tr>
                    <td style="padding:12px 30px; text-align: start;"> {{optional($bookingdata->service)->name ?? '-'}}</td>
                    <td style="padding:12px 30px; text-align: end;">{{ isset($bookingdata->amount) ? getPriceFormat($bookingdata->amount) : 0 }}</td>
                    @if(optional($bookingdata->service)->type  == 'hourly')
                        @php
                            $duration_minutes = $bookingdata->duration_diff / 60; // Calculate duration in minutes
                            $duration_hours = $duration_minutes > 60 ? $duration_minutes / 60 : 1; // Convert to hours if duration exceeds 60 minutes
                            // Format duration into hours:minutes format
                            $formatted_duration = gmdate('H:i', round($duration_hours * 3600));
                        @endphp
                        <td style="padding:12px 30px; text-align: end;">{{!empty($formatted_duration) ? $formatted_duration     : 0}} hr</td>
                    @else
                        <td style="padding:12px 30px; text-align: end;">{{!empty($bookingdata->quantity) ? $bookingdata->quantity : 0}}</td>
                    @endif

                    @php
                    if($bookingdata->type == 'service'){
                        if($bookingdata->service->type === 'fixed'){
                            $sub_total = ($bookingdata->amount) * ($bookingdata->quantity);
                        }else{
                            $sub_total = $bookingdata->final_total_service_price;
                        }
                    }else{
                        $sub_total = $bookingdata->amount;
                    }

                 @endphp
               <td style="padding:12px 30px; text-align: end;">{{!empty($sub_total) ? getPriceFormat($sub_total) : 0}}</td>
                </tr>
            </tbody>
        </table>

        {{-- BILLING TABLE --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: 24px;">
            <tbody style="background: #F6F7F9;">

                {{-- PRICE --}}
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{__('messages.Price')}}</td>
                    @if($bookingdata->service->type == "hourly")
                        <td style="padding:12px 30px; text-align: end; color: #1C1F34;">
                            {{ getPriceFormat($bookingdata->amount) }} * {{ $bookingdata->quantity }} / hr =
                            {{ getPriceFormat($bookingdata->final_total_service_price) }}
                        </td>
                    @else
                        <td style="padding:12px 30px; text-align: end; color: #1C1F34;">
                            {{ getPriceFormat($bookingdata->amount) }} * {{ $bookingdata->quantity }} =
                            {{ getPriceFormat($bookingdata->amount * $bookingdata->quantity) }}
                        </td>
                    @endif
                </tr>
               
                {{-- DISCOUNT --}}
                @if($bookingdata->bookingPackage == null && $bookingdata->discount > 0)
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{ __('messages.discount') }} ({{ $bookingdata->discount }}% off)</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">-{{ getPriceFormat($bookingdata->final_discount_amount) }}</td>
                </tr>
                @endif

                 {{-- COUPON --}}
                @if($bookingdata->couponAdded != null)
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{__('messages.coupon')}} ({{$bookingdata->couponAdded->code}})</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">-{{ getPriceFormat($bookingdata->final_coupon_discount_amount) }}</td>
                </tr>
                @endif
                
                 <!-- Extra Charges -->

                 @php
                 // Calculate extra charges and add-ons
                 $extraCharges = $bookingdata->bookingExtraCharge->count() > 0 ? $bookingdata->getExtraChargeValue() : 0;
                 $addonTotalPrice = $bookingdata->bookingAddonService->count() > 0 ? $bookingdata->bookingAddonService->sum('price') : 0;
                 @endphp
                 @if($extraCharges > 0)
                 <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{ __('messages.extra_charge') }}</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">+{{ getPriceFormat($extraCharges) }}</td>
                </tr>
                 @endif
                
                 @if($addonTotalPrice > 0)
                 <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{ __('messages.add_ons') }}</td>
                    <td style="padding:12px 30px; text-align: end; color: #219653;">+{{ getPriceFormat($addonTotalPrice) }}</td>
                </tr>
                @endif
                {{--  Sub-Total --}}
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{ __('messages.sub_total') }}</td>
                    <td style="padding:12px 30px; text-align: end; color: #1C1F34;">{{!empty($bookingdata->final_sub_total) ? getPriceFormat($bookingdata->final_sub_total) : 0}}</td>
                </tr>

                {{-- TAX  --}}
                @if($bookingdata->tax != "")
                    <tr>
                        <td style="padding:12px 30px; text-align: start;"></td>
                        <td style="padding:12px 30px; text-align: end;"></td>
                        <td style="padding:12px 30px; text-align: end;"></td>
                        <td style="padding:12px 30px; text-align: start; color: #6B6B6B;">{{__('messages.Tax')}} <br>
                                @foreach(json_decode($bookingdata->tax) as $key => $value)
                                    @if($value->type === 'percent')
                                        <span>({{ $value->title }} {{ $value->value }}%)</span>
                                    @else
                                        <span>({{ $value->title }} {{ getPriceFormat($value->value) }})</span>
                                    @endif
                                @endforeach
                        </td>
                        <td style="padding:12px 30px; text-align: end; color: #FB2F2F;">{{!empty($bookingdata->final_total_tax) ? getPriceFormat($bookingdata->final_total_tax) : 0}}</td>
                    </tr>
                @endif

                {{-- GRAND TOTAL --}}
                <tr>
                    <td style="padding:12px 30px; text-align: start;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: end;"></td>
                    <td style="padding:12px 30px; text-align: start; color: #1C1F34; border-top:1px solid #ccc;">{{__('messages.grand_total')}}</td>
                    <td style="padding:12px 30px; text-align: end; color: #1C1F34; border-top:1px solid #ccc;">{{ getPriceFormat($bookingdata->total_amount) ?? 0 }}</td>
                </tr>

                 <!-- Advance Payment -->
                @if($bookingdata->service->is_enable_advance_payment == 1)
                         <tr>
                            <td style="padding:12px 30px; text-align: start;"></td>
                            <td style="padding:12px 30px; text-align: end;"></td>
                            <td style="padding:12px 30px; text-align: end;"></td>
                            <td style="padding:12px 30px; text-align: start; color: #1C1F34; border-top:1px solid #ccc;">{{__('messages.advance_payment_amount')}} ({{$bookingdata->service->advance_payment_amount}}%)</td>
                            <td style="padding:12px 30px; text-align: end; color: #1C1F34; border-top:1px solid #ccc;">{{ getPriceFormat($bookingdata->advance_paid_amount) }}</td>
                        </tr>
                    @if($bookingdata->status !== "cancelled")
                        <tr>
                            <td style="padding:12px 30px; text-align: start;"></td>
                            <td style="padding:12px 30px; text-align: end;"></td>
                            <td style="padding:12px 30px; text-align: end;"></td>
                            <td style="padding:12px 30px; text-align: start; color: #1C1F34; border-top:1px solid #ccc;">{{__('messages.remaining_amount')}}
                                    @if($payment != null && $payment->payment_status !== 'paid')
                                    <span class="badge bg-warning">( {{__('messages.pending')}} )</span>
                                    @endif
                            </td>
                            <td style="padding:12px 30px; text-align: end; color: #1C1F34; border-top:1px solid #ccc;">
                                    @if($payment != null && $payment->payment_status == 'paid') 
                                    {{ __('messages.paid') }}
                                    @else
                                    {{ getPriceFormat($bookingdata->total_amount - $bookingdata->advance_paid_amount) }}
                                    @endif
                            </td>
                        </tr>
                    @endif

                    @if($bookingdata->status === "cancelled")
                            <tr>
                                <td style="padding:12px 30px; text-align: start;"></td>
                                <td style="padding:12px 30px; text-align: end;"></td>
                                <td style="padding:12px 30px; text-align: end;"></td>
                                <td style="padding:12px 30px; text-align: start; color: #1C1F34; border-top:1px solid #ccc;">{{ __('messages.cancellation_charge') }} ({{ $bookingdata->cancellation_charge }}%)</td>
                                <td style="padding:12px 30px; text-align: end; color: #1C1F34; border-top:1px solid #ccc;">{{getPriceFormat($bookingdata->cancellation_charge_amount) ?? 0}}</td>
                            </tr>
                        @if($bookingdata->advance_paid_amount > 0)
                            @php 
                                $refundamount = $bookingdata->advance_paid_amount - $bookingdata->cancellation_charge_amount
                            @endphp
                            @if($refundamount > 0)
                                <tr>
                                    <td style="padding:12px 30px; text-align: start;"></td>
                                    <td style="padding:12px 30px; text-align: end;"></td>
                                    <td style="padding:12px 30px; text-align: end;"></td>
                                    <td style="padding:12px 30px; text-align: start; color: #1C1F34; border-top:1px solid #ccc;">{{ __('messages.refund_amount') }}</td>
                                    <td style="padding:12px 30px; text-align: end; color: #1C1F34; border-top:1px solid #ccc;">{{getPriceFormat($refundamount) ?? 0}} </td>
                                
                                </tr>
                            @endif
                        @endif
                    @endif
               @endif
             
            </tbody>
        </table>
        <div class="bottom-section">
            <h4 style="margin-bottom: 8px;">{{ __('landingpage.terms_conditions') }}</h4>
            <p style="margin:8px 0; font-size: 14px;">Payment is due upon receipt. By making a booking, you agree to our service terms, including payment
                policies, warranties, and liability limitations. Cancellations within 24 hours of the service may
                incur
                a fee. Any issues with workmanship are covered under our 30-day warranty. Contact us for details at
                <a href="#" style="text-decoration: none; color: #5F60B9;">hello@24itsupport.com.</a>
            </p>
        </div>
        <footer style="margin-top: 8px;">
            <div style="display: inline; vertical-align: middle; margin-right: 10px;">
                <h5 style="display: inline;">For more information, visit our website:</h5>
                <a href="{{$generaldata->website}}" style="color: #5F60B9;">{{ $generaldata->website}}</a>
                <h5 style="display: block; margin: 8px 0 0;">© 2024 All Rights Reserved by Hottcart Ecommerce Private Limited</h5>
            </div>
        </footer>
    </div>
</body>

</html>