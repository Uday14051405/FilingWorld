
<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('product-sub-menu.destroy', $data->id))->attribute('data-submit', 'category'.$data->id)->open() }}
<div class="d-flex justify-content-end align-items-center">
    @if(!$data->trashed())
        @if($auth_user->can('product sub menu category delete'))
        <a class="me-3 delete-category" href="{{ route('product-sub-menu.destroy', $data->id) }}" data--submit="category{{$data->id}}" 
            data--ajax="true"
            data--datatable="reload"
            data--confirmation="true"
            data-title="{{ __('category',['form'=>  __('category') ]) }}"
            title="{{ __('messages.delete_form_title',['form'=>  __('messages.product_sub_menu_category') ]) }}"
            data--message='{{ __("messages.delete_msg") }}'>
            <i class="far fa-trash-alt text-danger"></i>
        </a>
        @endif
    @endif
    @if(auth()->user()->hasAnyRole(['admin']) && $data->trashed())
        <a href="{{ route('product.sub-menu.action',['id' => $data->id, 'type' => 'restore']) }}"
            title="{{ __('messages.restore_form_title',['form' => __('messages.product_sub_menu_category') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.restore_form_title',['form'=>  __('messages.product_sub_menu_category') ]) }}"
            data-message='{{ __("messages.restore_msg") }}'
            data-datatable="reload"
            class="me-2">
            <i class="fas fa-redo text-secondary"></i>
        </a>
        <a href="{{ route('product.sub-menu.action',['id' => $data->id, 'type' => 'forcedelete']) }}"
            title="{{ __('messages.forcedelete_form_title',['form' => __('messages.product_sub_menu_category') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.forcedelete_form_title',['form'=>  __('messages.product_sub_menu_category') ]) }}"
            data-message='{{ __("messages.forcedelete_msg") }}'
            data-datatable="reload"
            class="me-2">
            <i class="far fa-trash-alt text-danger"></i>
        </a>
    @endif
</div>

{{ html()->form()->close()}}
