<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.list') }}</h5>
                            @if($auth_user->can('productfaq add'))
                            <a href="{{ route('productfaq.create',['service_id'=> $service_id]) }}" class=" float-end me-1 btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> {{ trans('messages.add_form_title',['form' => trans('messages.productfaq')  ]) }}</a>
                            @endif
                        </div>
                      
                    </div>
                </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-end">
                                <div class="col-md-3">
                                    <div class="d-flex justify-content-end">                                    
                                        <div class="input-group input-group-search ml-auto">
                                            <span class="input-group-text" id="addon-wrapping"><i class="fas fa-search"></i></span>
                                            <input type="text" class="form-control dt-search" placeholder="Search..." aria-label="Search" aria-describedby="addon-wrapping" aria-controls="dataTableBuilder">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                    <table id="datatable" class="table table-striped border">

                                    </table>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {

        window.renderedDataTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                dom: '<"row align-items-center"><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" l><"col-md-6" p><"col-md-6" i>><"clear">',
                ajax: {
                  "type"   : "GET",
                  "url"    : '{{ route("productfaq.index_data",['service_id' => $service_id]) }}',
                  "data"   : function( d ) {
                    d.search = {
                      value: $('.dt-search').val()
                    };
                    d.filter = {
                      column_status: $('#column_status').val()
                    }
                  },
                },
                columns: [
                    {
                        name: 'DT_RowIndex',
                        data: 'DT_RowIndex',
                        title: "{{__('messages.no')}}",
                        exportable: false,
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'title',
                        name: 'title',
                        title: "{{ __('messages.title') }}"
                    },
                    {
                        data: 'service_id',
                        name: 'service_id',
                        title: "{{ __('messages.product') }}"
                    },
                    {
                        data: 'status',
                        name: 'status',
                        title: "{{ __('messages.status') }}"
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        title: "{{ __('messages.action') }}",
                        className: 'text-end'
                    }
                    
                ],
                language: {
          processing: "{{ __('messages.processing') }}" // Set your custom processing text
        }
                
            });
      });
</script>
</x-master-layout>