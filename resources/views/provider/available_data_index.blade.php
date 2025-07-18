<x-master-layout>

    <head>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    </head>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap gap-3">
                            <h5 class="fw-bold">{{ $pageTitle ?? trans('messages.service_available_provider_engineer') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between gy-3">
                            <div class="col-md-9 col-lg-4 col-xl-3">
                                <div class="d-flex gap-3 align-items-center" style="width: max-content;">
                                        <select name="category_id" class="form-control select2">
                                            <option hidden>Select Category</option>
                                            @forelse($categories as $id => $name)
                                                <option value="{{ $id }}" {{ ($id == $categoryId) ? 'selected' : '' }}>{{ $name }}</option>
                                            @empty
                                                <option value="">No Categories Available</option>
                                            @endforelse
                                        </select>

                                        <select name="user_type" class="form-control select2" {{ ($auth_user->user_type == 'provider') ? 'hidden' : '' }}>
                                            <option hidden>Select User Type</option>
                                            <option value="provider" {{ ("provider" == $user_type) ? 'selected' : '' }}>Provider</option>
                                            <option value="engineer" {{ ("engineer" == $user_type) ? 'selected' : '' }}>Engineer</option>
                                            <option value="all">All</option>
                                        </select>

                                        <button class="btn btn-primary" id="availability_filter">{{ __('messages.apply') }}</button>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-4 col-xl-3">
                                <div class="d-flex align-items-center gap-3 justify-content-end">
                                    <div class="input-group input-group-search ms-2">
                                        <span class="input-group-text" id="addon-wrapping"><i
                                                class="fas fa-search"></i></span>
                                        <input type="text" class="form-control dt-search" placeholder="Search..."
                                            aria-label="Search" aria-describedby="addon-wrapping"
                                            aria-controls="dataTableBuilder">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="datatable" class="table table-striped border"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const canEdit = {{ json_encode($auth_user->can('provider edit')) }};
            const canDelete = {{ json_encode($auth_user->can('provider delete')) }};
            const canChangePassword = {{ json_encode($auth_user->can('provider changepassword')) }};
            const columns = [{
                    name: 'check',
                    data: 'check',
                    title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" data-type="user" onclick="selectAllTable(this)">',
                    searchable: false,
                    exportable: false,
                    orderable: false,
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                    title: "{{ __('product.lbl_update_at') }}",
                    orderable: true,
                    visible: false,
                },
                {
                    data: 'display_name',
                    name: 'display_name',
                    title: "{{ __('messages.name') }}",
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: "{{ __('messages.joining_date') }}"
                },
                {
                    data: 'providertype_id',
                    name: 'providertype_id',
                    title: "{{ __('messages.providertype') }}"
                },
                {
                    data: 'contact_number',
                    name: 'contact_number',
                    title: "{{ __('messages.contact_number') }}"
                },
                {
                    data: 'role',
                    name: 'role',
                    title: "{{ __('messages.role') }}"
                },
                {
                    data: 'wallet',
                    name: 'wallet',
                    title: "{{ __('messages.wallet_amt') }}",
                    searchable: false,
                    orderable: false,
                },
                {
                    data: 'status',
                    name: 'status',
                    title: "{{ __('messages.status') }}"
                },
            ];

            // Add the action column if the user has edit or delete permissions
            if (canEdit || canDelete || canChangePassword) {
                columns.push({
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    title: "{{ __('messages.action') }}",
                    className: 'text-end'
                });
            }

            window.renderedDataTable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                responsive: true,
                dom: '<"row align-items-center"><"table-responsive my-3 mt-3 mb-2 pb-1" rt><"row align-items-center data_table_widgets" <"col-md-6" <"d-flex align-items-center flex-wrap gap-3" l i>><"col-md-6" p>><"clear">',
                ajax: {
                    "type": "GET",
                    "url": '{!! route('available.index_data', ['list_status' => $list_status, 'categoryId' => $categoryId, 'user_type' => $user_type]) !!}',
                    "data": function(d) {
                        d.search = {
                            value: $('.dt-search').val()
                        };
                        d.filter = {
                            column_status: $('#column_status').val()
                        };
                    },
                },
                columns: columns,
                order: [
                    [1, 'desc']
                ],
                language: {
                    processing: "{{ __('messages.processing') }}" // Set your custom processing text
                }
            });

            document.getElementById('availability_filter').addEventListener('click', function() {
                let selectedCategory = document.querySelector('select[name="category_id"]').value;
                let selectedUserType = document.querySelector('select[name="user_type"]').value;

                // Update DataTable AJAX URL with new parameters
                window.renderedDataTable.ajax.url(
                    `{!! route('available.index_data') !!}?categoryId=${selectedCategory}&user_type=${selectedUserType}`
                ).load();
            });
        });

        function resetQuickAction() {
            const actionValue = $('#quick-action-type').val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        }

        $('#quick-action-type').change(function() {
            resetQuickAction();
        });

        $(document).on('click', '[data-ajax="true"]', function(e) {
            e.preventDefault();
            const button = $(this);
            const confirmation = button.data('confirmation');

            if (confirmation === 'true') {
                const message = button.data('message');
                if (confirm(message)) {
                    const submitUrl = button.data('submit');
                    const form = button.closest('form');
                    form.attr('action', submitUrl);
                    form.submit();
                }
            } else {
                const submitUrl = button.data('submit');
                const form = button.closest('form');
                form.attr('action', submitUrl);
                form.submit();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</x-master-layout>
