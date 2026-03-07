<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">{{ __('Send Feedback') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="feedbackForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="feedbackDescription" class="form-label">{{ __('Description of Issue or Suggestion') }}</label>
                        <textarea class="form-control" id="feedbackDescription" name="description" rows="4" 
                            placeholder="{{ __('Describe the technical issue or suggestion...') }}" required minlength="10"></textarea>
                        <div class="form-text text-muted">
                            {{ __('Minimum 10 characters.') }}
                        </div>
                        <div class="invalid-feedback" id="descriptionError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="feedbackFiles" class="form-label">{{ __('Attachments (Optional)') }}</label>
                        <input class="form-control" type="file" id="feedbackFiles" name="files[]" multiple accept="image/*,.pdf,.doc,.docx">
                        <div class="invalid-feedback" id="filesError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="btnSendFeedback">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        {{ __('Send Feedback') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const feedbackForm = document.getElementById('feedbackForm');
    const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
    const btnSendFeedback = document.getElementById('btnSendFeedback');
    const spinner = btnSendFeedback.querySelector('.spinner-border');

    feedbackForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Basic validation
        const description = document.getElementById('feedbackDescription').value;
        const files = document.getElementById('feedbackFiles').files;
        
        let hasError = false;
        
        if (description.length < 10) {
            document.getElementById('descriptionError').innerText = 'Deskripsi minimal 10 karakter.';
            document.getElementById('feedbackDescription').classList.add('is-invalid');
            hasError = true;
        } else {
            document.getElementById('feedbackDescription').classList.remove('is-invalid');
        }

        if (files.length > 3) {
            document.getElementById('filesError').innerText = 'Maksimum 3 file.';
            document.getElementById('feedbackFiles').classList.add('is-invalid');
            hasError = true;
        } else {
            document.getElementById('feedbackFiles').classList.remove('is-invalid');
        }

        if (hasError) return;

        // AJAX Submission
        btnSendFeedback.disabled = true;
        spinner.classList.remove('d-none');

        const formData = new FormData(feedbackForm);

        fetch("{{ route('feedback.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Terima kasih! Masukan Anda telah terkirim.');
                feedbackForm.reset();
                feedbackModal.hide();
            } else {
                alert(data.message || 'Terjadi kesalahan saat mengirim masukan.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi.');
        })
        .finally(() => {
            btnSendFeedback.disabled = false;
            spinner.classList.add('d-none');
        });
    });
});
</script>
