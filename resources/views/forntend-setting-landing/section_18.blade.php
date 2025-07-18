
{{ html()->form('POST', route('landing_page_settings_updates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() }}

{{ html()->hidden('id',$landing_page->id)->placeholder('id')->class('form-control') }}
{{ html()->hidden('type', $tabpage)->placeholder('id')->class('form-control') }}

<div class="form-group">
    <div class="form-control d-flex align-items-center justify-content-between">
                    <label for="enable_section_18" class="mb-0">{{__('messages.enable_section_18')}}</label>
        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline">
                        <input type="checkbox" class="custom-control-input section_18" name="status" id="section_18" data-type="section_18"  {{!empty($landing_page) && $landing_page->status == 1 ? 'checked' : ''}}>
            <label class="custom-control-label" for="section_18"></label>
        </div>
    </div>
</div>
        <div class="form-section" id='enable_section_18'>
    <div class="form-group">
        {{ html()->label(trans('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
        {{ html()->text('title', old('title'))->id('title')->placeholder(trans('messages.title'))->class('form-control')->required() }}
        <small class="help-block with-errors text-danger"></small>
    </div>

    <div class="form-group" id='enable_select_service'>
        {{ html()->label(__('messages.select_name', ['select' => __('messages.subcategory')]), 'name')->class('form-control-label') }}
        <br />
        {{ html()->select('subcategory_id[]', [])
            ->value(old('subcategory_id'))
            ->class('select2js form-control subcategory_id')
            ->id('subcategory_id')
            ->attribute('data-placeholder', __('messages.select_name', ['select' => __('messages.subcategory')]))
            ->attribute('data-ajax--url', route('product.ajax-list', ['type' => 'top_subcategories', 'is_featured' => 1]))
            ->multiple()
        }}
    </div>

</div>


{{ html()->submit(__('messages.save'))->class('btn btn-md btn-primary float-md-end submit_section1') }}
{{ html()->form()->close() }}

<script>
    var enable_section_18 = $("input[name='status']").prop('checked');
    checkSection3(enable_section_18);

    $('#section_18').change(function() {
        value = $(this).prop('checked') == true ? true : false;
        checkSection3(value);
        
    });

    function checkSection3(value) {
        if (value == true) {
            $('#enable_section_18').removeClass('d-none');
            $('#title').prop('required', true);
            $('#subcategory_id').prop('required', false).trigger('change.select2');
        } else {
            $('#enable_section_18').addClass('d-none');
            $('#title').prop('required', false);
            $('#subcategory_id').prop('required', false).trigger('change.select2');
        }
    }

    ///// open select popular category ///////////
    $(document).ready(function() {
        $('.select2js').select2();

        $('#subcategory_id').on('change', function() {
            var selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.length > 16) {
                selectedOptions.pop();
                $(this).val(selectedOptions).trigger('change.select2');
            }
        });

      
    });

    var get_value = $('input[name="status"]:checked').data("type");
    getConfig(get_value)
    $('.section_18').change(function(){
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
                var section_18 = title = subcategory_ids = '';

                if (response) {
                    if (response.data.key == 'section_18') {
                        obj = JSON.parse(response.data.value);
                    }
                    if (obj !== null) {
                        var title = obj.title;
                        var subcategory_ids = obj.subcategory_id;
                    }
                    $('#title').val(title);
                    loadService(subcategory_ids);
                    
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

   
    function loadService(subcategory_ids) {
    var service_route = "{{ route('product.ajax-list', ['type' => 'top_subcategories']) }}";
    service_route = service_route.replace('amp;', '');
    var is_featured = 1;
    $.ajax({
        url: service_route,
        data: {
            is_featured: is_featured,
            ids: subcategory_ids,
        },
        success: function(result) {
            $('#subcategory_id').select2({
                width: '100%',
                placeholder: "{{ trans('messages.select_name',['select' => trans('messages.subcategory')]) }}",
                data: result.results
            });
            $('#subcategory_id').val(subcategory_ids).trigger('change');
        }
    });
}

</script>
