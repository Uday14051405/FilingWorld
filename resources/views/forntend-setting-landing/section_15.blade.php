
{{ html()->form('POST', route('landing_page_settings_updates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id',$landing_page->id)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->placeholder('id')->class('form-control') }}

<div class="form-group">
    <div class="form-control d-flex align-items-center justify-content-between">
                    <label for="enable_section_15" class="mb-0">{{__('messages.enable_section_15')}}</label>
        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                        <input type="checkbox" class="custom-control-input section_15" name="status" id="section_15" data-type="section_15"  {{!empty($landing_page) && $landing_page->status == 1 ? 'checked' : ''}}>
            <label class="custom-control-label" for="section_15"></label>
        </div>
    </div>
</div>
        <div class="form-section" id='enable_section_15'>
    <div class="form-group">
        {{ html()->label(trans('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
        {{ html()->text('title', old('title'))->id('title')->placeholder(trans('messages.title'))->class('form-control')->required() }}
        <small class="help-block with-errors text-danger"></small>
    </div>

    <div class="form-group" id='enable_select_service'>
        {{ html()->label(__('messages.select_name', ['select' => __('messages.product_category')]), 'name')->class('form-control-label') }}
        <br />
        {{ html()->select('service_id[]', [])
            ->value(old('service_id'))
            ->class('select2js form-control service_id')
            ->id('service_id')
            ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.product_category')]))
            ->attribute('data-ajax--url', route('product.ajax-list', ['type' => 'product_category_menu', 'is_featured' => 1]))
            ->multiple()
        }}
    </div>

</div>


{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}

<script>
    var enable_section_15 = $("input[name='status']").prop('checked');
    checkSection3(enable_section_15);

    $('#section_15').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection3(value);
        
    });

    function checkSection3(value) {
        if (value == true) {
            $('#enable_section_15').removeClass('d-none');
            $('#title').prop('required', true);
            $('#service_id').prop('required', false).trigger('change.select2');
        } else {
            $('#enable_section_15').addClass('d-none');
            $('#title').prop('required', false);
            $('#service_id').prop('required', false).trigger('change.select2');
        }
    }

    ///// open select popular category ///////////
    $(document).ready(function() {
        $('.select2js').select2();

        $('#service_id').on('change', function() {
            var selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.length > 16) {
                selectedOptions.pop();
                $(this).val(selectedOptions).trigger('change.select2');
            }
        });

      
    });

    var get_value = $('input[name="status"]:checked').data("type");
    getConfig(get_value)
    $('.section_15').change(function(){
        value = $(this).prop('checked') == true ? true : false;
        type = $(this).data("type");
        getConfig(type)

    });

    function getConfig(type) {
        var _token = $('meta[name="csrf-token"]').attr('content');
        var page = "{{$tabpage}}";
        var getDataRoute = "{{ route('getLandingLayoutPageConfig') }}";
        $.ajax({
            url: getDataRoute,
            type: "POST",
            data: {
                type: type,
                page: page,
                _token: _token
            },
            success: function (response) {
                var obj = '';
                var section_15 = title = service_ids = '';

                if (response) {
                    if (response.data.key == 'section_15') {
                        obj = JSON.parse(response.data.value);
                    }
                    if (obj !== null) {
                        var title = obj.title;
                        var service_ids = obj.service_id;
                    }
                    $('#title').val(title);
                    loadService(service_ids);
                    
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

   
    function loadService(service_ids) {
    var service_route = "{{ route('product.ajax-list', ['type' => 'product_category_menu']) }}";
    service_route = service_route.replace('amp;', '');
    var is_featured = 1;
    $.ajax({
        url: service_route,
        data: {
            is_featured: is_featured,
            ids: service_ids,
        },
        success: function(result) {
            $('#service_id').select2({
                width: '100%',
                placeholder: "{{ trans('messages.select_name',['select' => trans('messages.product_category')]) }}",
                data: result.results
            });
            $('#service_id').val(service_ids).trigger('change');
        }
    });
}

</script>
