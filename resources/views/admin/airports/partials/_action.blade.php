@if ($airport->deleted_at)
    <button type="button" class="btn btn-outline-warning btn-sm"
        onclick="deleteRecord('{{ route('airports.destroy', $airport->id) }}', event)"><i class="fa fa-trash-restore"
            title="Un-hide"></i></button>
@else
    <a role="button" href="{{ route('airports.show', $airport->slug) }}" class="btn btn-outline-primary btn-sm"><i
            class="fa fa-building" title="Pull Places"></i></a>

    <button type="button" class="btn btn-outline-danger btn-sm"
        onclick="deleteRecord('{{ route('airports.destroy', $airport->id) }}', event)"><i class="fa fa-eye-slash"
            title="Hide"></i></button>

    {{-- Bootstrap Model --}}
    <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal"
        data-bs-whatever="@mdo"><i class="fa-brands fa-google"></i></button>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Set Google Api Key</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('airports.google.api.key') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="google-api-key" class="col-form-label">Google Api</label>
                            <input type="text" class="form-control" name="google_api_key" id="google-api-key">
                            <input type="hidden" name="airport_id" value="{{ $airport->id }}">
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary ms-2">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
