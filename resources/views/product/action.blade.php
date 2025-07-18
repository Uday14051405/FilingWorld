
<?php
    $auth_user= authSession();
?>
{{ html()->form('DELETE', route('product.destroy', $data->id))->attribute('data--submit', 'service'.$data->id)->open() }}
<style>
    .rating i {
        cursor: pointer;
        font-size: 1.5rem;
        transition: color 0.2s;
    }
    .rating i.active {
        color: #ffc107;
    }
</style>
<div class="d-flex justify-content-end align-items-center">
    @if(!$data->trashed())
   
        @if($auth_user->can('product delete'))
        <a class="me-2" href="{{ route('product.destroy', $data->id) }}" data--submit="service{{$data->id}}"
            data--confirmation='true' 
            data--ajax="true"
            data-datatable="reload"
            data-title="{{ __('messages.delete_form_title',['form'=>  __('messages.product') ]) }}"
            title="{{ __('messages.delete_form_title',['form'=>  __('messages.product') ]) }}"
            data-message='{{ __("messages.delete_msg") }}'>
            <i class="far fa-trash-alt text-danger"></i>
        </a>
        @endif
        @if(auth()->user()->hasAnyRole(['admin','provider']))
            <a class="me-2" href="{{ route('productfaq.index',['id' => $data->id]) }}" title="{{ __('messages.add_form_title',['form' => __('messages.productfaq') ]) }}"> 
                <span class="text-primary">
                    <svg height="16" width="16" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M464 256A208 208 0 1 0 48 256a208 208 0 1 0 416 0zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm169.8-90.7c7.9-22.3 29.1-37.3 52.8-37.3l58.3 0c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24l0-13.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1l-58.3 0c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>    
                </span>
            </a>
        @endif
        <button type="button" class="open-review-modal" data-service-id="{{ $data->id }}" data-toggle="modal" data-target="#reviewModal" style="border: none; background: none; padding: 0; margin: 0;">
            <i class="fa fa-star" aria-hidden="true" style="color: orangered;"></i>
        </button>
    @endif
    @if(auth()->user()->hasAnyRole(['admin','provider']) && $data->trashed())
        @if($data->trashed())
            <a href="{{ route('product.action', ['id' => $data->id, 'type' => 'restore']) }}"
                title="{{ __('messages.restore_form_title', ['form' => __('messages.product')]) }}"
            data--submit="confirm_form"
                data--confirmation="true"
                data--ajax="true"
                data-title="{{ __('messages.restore_form_title', ['form' => __('messages.product')]) }}"
                data-message="{{ __('messages.restore_msg') }}"
            data-datatable="reload"
                class="me-2">
            <i class="fas fa-redo text-primary"></i>
        </a>
        @endif
        <a href="{{ route('product.action',['id' => $data->id, 'type' => 'forcedelete']) }}"
            title="{{ __('messages.forcedelete_form_title',['form' => __('messages.product') ]) }}"
            data--submit="confirm_form"
            data--confirmation='true'
            data--ajax='true'
            data-title="{{ __('messages.forcedelete_form_title',['form'=>  __('messages.product') ]) }}"
            data-message='{{ __("messages.forcedelete_msg") }}'
            data-datatable="reload"
            class="me-2">
            <i class="far fa-trash-alt text-danger"></i>
        </a>
    @endif
</div>
{{ html()->form()->close()}}
<!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-body py-4 px-5 text-start">
                <h4 class="text-capitalize mt-0 mb-4">Review</h4>
                <form id="reviewForm" method="POST" action="{{ route('save.booking.rating.by.admin') }}">
                @csrf
                <div class="mb-3">
                    <label for="user_id" class="form-label">Select User</label>
                    <select name="user_id" id="user_id" class="form-select" style="box-shadow: none; border: var(--bs-border-width) solid var(--bs-border-color);" required>
                        <option value="">-- Select User --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->username }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <div id="star-rating" class="rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa fa-star text-secondary" data-value="{{ $i }}"></i>
                    @endfor
                    <input type="hidden" name="rating" id="rating" value="0">
                    </div>
                </div>
                <input type="hidden" name="type" id="type" value="product">
                <input type="hidden" name="service_id" id="service_id" value="{{ $data->id }}">
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4" placeholder="Write Here..."></textarea>
                </div>
                <div class="text-start">
                    <button type="submit" class="btn btn-danger px-4">Submit</button>
                </div>
                </form>
            </div>
            </div>
        </div>
    </div>

    
    <script>
        const reviewModal = document.getElementById('reviewModal');
        const serviceIdInput = document.getElementById('service_id');

        // Handle star button click
        document.querySelectorAll('.open-review-modal').forEach(button => {
            button.addEventListener('click', function () {
                const serviceId = this.getAttribute('data-service-id');
                if (serviceIdInput) {
                    serviceIdInput.value = serviceId;
                }
            });
        });

        
        const stars = document.querySelectorAll('#star-rating i');
        const ratingInput = document.getElementById('rating');

        if (stars.length > 0) {
            stars.forEach((star, index) => {
                star.addEventListener('click', function () {
                    const ratingValue = index + 1;
                    ratingInput.value = ratingValue;

                    // Reset all stars
                    stars.forEach((s) => {
                        s.classList.remove('text-warning');
                        s.classList.add('text-secondary');
                    });

                    // Highlight selected
                    for (let i = 0; i < ratingValue; i++) {
                        stars[i].classList.remove('text-secondary');
                        stars[i].classList.add('text-warning');
                    }
                });
            });
        }
    </script>