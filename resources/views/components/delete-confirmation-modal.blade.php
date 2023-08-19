<div class="modal fade" tabindex="-1" id="{{ $modalId }}" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $modalTitle }}</h5>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ $formAction }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="{{ $formInputName }}" id="{{ $formInputName }}">
                    <p class="text-center delete-text">{{ $modalMessage }}</p>
                </div>
                <div class="modal-footer justify-content-evenly">
                    <button type="submit" class="btn save" id="delete_btn">حذف</button>
                    <button type="button" class="btn btn-secondary cancel" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>