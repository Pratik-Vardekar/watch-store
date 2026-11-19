<div class="modal fade" id="csvImportModal" tabindex="-1" aria-labelledby="csvImportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ $importRoute }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="csvImportModalLabel">{{ $title ?? 'Import CSV' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Upload a CSV file (Excel compatible). Use the sample template for correct column headers.
                    </p>

                    <div class="mb-3">
                        <label class="form-label">CSV File</label>
                        <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                    </div>

                    @if(!empty($sampleRoute))
                        <a href="{{ $sampleRoute }}" class="btn btn-link btn-sm px-0">
                            <i class="ti ti-download me-1"></i>Download sample CSV
                        </a>
                    @endif

                    @if(session('import_errors'))
                        <div class="alert alert-warning mt-3 mb-0 small">
                            <strong>Import issues:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-upload me-1"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('import_errors'))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('csvImportModal');
            if (modal) {
                new bootstrap.Modal(modal).show();
            }
        });
    </script>
    @endpush
@endif
