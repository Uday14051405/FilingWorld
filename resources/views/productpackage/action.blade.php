<?php
$auth_user = authSession();
?>

{{ html()->form('DELETE', route('productpackage.destroy', $servicepackage->id))->attribute('data--submit', 'servicepackage' . $servicepackage->id)->open()}}
<div class="d-flex justify-content-end align-items-center">
@if($auth_user->can('productpackage edit'))
    <a class="me-2" href="{{ route('productpackage.create',['id' => $servicepackage->id]) }}" title="{{ __('messages.update_form_title',['form' => __('messages.product_package') ]) }}"><i class="fas fa-pen text-secondary"></i></a>
@endif
@if($auth_user->can('productpackage delete'))
<a href="{{ route('productpackage.action',['id' => $servicepackage->id, 'type' => 'forcedelete']) }}" title="{{ __('messages.forcedelete_form_title',['form' => __('messages.product_package') ]) }}" data--submit="confirm_form" data--confirmation='true' data--ajax='true' data-title="{{ __('messages.forcedelete_form_title',['form'=>  __('messages.product_package') ]) }}" data-message='{{ __("messages.forcedelete_msg") }}' data-datatable="reload" class="me-3">
    <i class="far fa-trash-alt text-danger"></i>
</a>
@endif
</div>
{{ html()->form()->close() }}
