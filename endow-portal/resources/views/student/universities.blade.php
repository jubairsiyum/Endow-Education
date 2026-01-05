@extends('layouts.student')

@section('page-title', 'University Information')
@section('breadcrumb', 'Home / University Information')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body-custom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h4 class="fw-bold mb-2">University Information</h4>
                            <p class="text-muted mb-0">Explore our partner universities and their programs</p>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" class="form-control" id="searchUniversities" placeholder="Search universities..." style="max-width: 250px;">
                            <select class="form-select" id="filterCountry" style="max-width: 180px;">
                                <option value="">All Countries</option>
                                @foreach($universities->pluck('country')->unique()->sort() as $country)
                                <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" id="universitiesContainer">
        @forelse($universities as $university)
        <div class="col-md-6 col-lg-4 university-card" data-country="{{ $university->country }}" data-name="{{ strtolower($university->name) }}">
            <div class="card-custom h-100">
                <div class="card-body-custom">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        @if($university->logo)
                        <img src="{{ asset('storage/' . $university->logo) }}" 
                             alt="{{ $university->name }}" 
                             class="rounded"
                             style="width: 60px; height: 60px; object-fit: contain;">
                        @else
                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center"
                             style="width: 60px; height: 60px; font-size: 24px; font-weight: 700;">
                            {{ strtoupper(substr($university->name, 0, 1)) }}
                        </div>
                        @endif
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">{{ $university->name }}</h5>
                            <div class="text-muted small">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $university->city }}, {{ $university->country }}
                            </div>
                            @if($university->code)
                            <span class="badge bg-light text-dark mt-2">{{ $university->code }}</span>
                            @endif
                        </div>
                    </div>

                    @if($university->description)
                    <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $university->description }}
                    </p>
                    @endif

                    @if($university->programs->count() > 0)
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-muted">Available Programs</span>
                            <span class="badge bg-primary">{{ $university->programs->count() }}</span>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($university->programs->take(3) as $program)
                            <span class="badge bg-light text-dark small">{{ $program->name }}</span>
                            @endforeach
                            @if($university->programs->count() > 3)
                            <span class="badge bg-secondary small">+{{ $university->programs->count() - 3 }} more</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="d-flex gap-2 mt-auto">
                        @if($university->website)
                        <a href="{{ $university->website }}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-secondary flex-grow-1">
                            <i class="fas fa-globe me-1"></i>Website
                        </a>
                        @endif
                        <button type="button" 
                                class="btn btn-sm btn-primary-custom flex-grow-1"
                                data-bs-toggle="modal" 
                                data-bs-target="#universityModal{{ $university->id }}">
                            <i class="fas fa-info-circle me-1"></i>Details
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- University Detail Modal -->
        <div class="modal fade" id="universityModal{{ $university->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center gap-3">
                            @if($university->logo)
                            <img src="{{ asset('storage/' . $university->logo) }}" 
                                 alt="{{ $university->name }}" 
                                 class="rounded"
                                 style="width: 60px; height: 60px; object-fit: contain;">
                            @else
                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center"
                                 style="width: 60px; height: 60px; font-size: 24px; font-weight: 700;">
                                {{ strtoupper(substr($university->name, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <h4 class="fw-bold mb-1">{{ $university->name }}</h4>
                                <div class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $university->city }}, {{ $university->country }}
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($university->description)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">About</h6>
                            <p class="text-muted">{{ $university->description }}</p>
                        </div>
                        @endif

                        @if($university->programs->count() > 0)
                        <div class="mb-3">
                            <h6 class="fw-bold mb-3">Available Programs ({{ $university->programs->count() }})</h6>
                            <div class="list-group list-group-flush">
                                @foreach($university->programs as $program)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-semibold">{{ $program->name }}</h6>
                                            @if($program->description)
                                            <p class="text-muted small mb-2">{{ Str::limit($program->description, 100) }}</p>
                                            @endif
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <span class="badge bg-primary">{{ ucfirst($program->level) }}</span>
                                                @if($program->duration)
                                                <span class="badge bg-light text-dark">
                                                    <i class="fas fa-clock me-1"></i>{{ $program->duration }}
                                                </span>
                                                @endif
                                                @if($program->code)
                                                <span class="badge bg-secondary">{{ $program->code }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($program->tuition_fee)
                                        <div class="text-end ms-3">
                                            <div class="small text-muted">Tuition Fee</div>
                                            <div class="fw-bold text-success">
                                                {{ $program->currency ?? '$' }} {{ number_format($program->tuition_fee, 0) }}
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0">
                        @if($university->website)
                        <a href="{{ $university->website }}" 
                           target="_blank" 
                           class="btn btn-primary-custom">
                            <i class="fas fa-external-link-alt me-2"></i>Visit University Website
                        </a>
                        @endif
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body text-center py-5">
                    <i class="fas fa-university text-muted mb-3" style="font-size: 48px;"></i>
                    <h5 class="fw-bold mb-2">No Universities Available</h5>
                    <p class="text-muted mb-0">There are currently no universities in our system. Please check back later.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($universities->count() > 0)
    <div class="row mt-4" id="noResultsMessage" style="display: none;">
        <div class="col-12">
            <div class="alert alert-info mb-0 text-center">
                <i class="fas fa-search me-2"></i>No universities found matching your criteria.
            </div>
        </div>
    </div>
    @endif
</div>@endsection

@push('styles')
<style>
    .university-card {
        transition: transform 0.2s;
    }

    .university-card:hover {
        transform: translateY(-5px);
    }

    .card-custom:hover {
        box-shadow: 0 0.5rem 1rem rgba(220, 20, 60, 0.15) !important;
    }

    .modal-body {
        max-height: 70vh;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 12px;
    }

    .bg-primary {
        background-color: var(--primary) !important;
    }

    .text-primary {
        color: var(--primary) !important;
    }

    .text-success {
        color: #28a745 !important;
    }
</style>
@endpush

@push('scripts')
    // Search and Filter functionality
    const searchInput = document.getElementById('searchUniversities');
    const filterCountry = document.getElementById('filterCountry');
    const universityCards = document.querySelectorAll('.university-card');
    const noResultsMessage = document.getElementById('noResultsMessage');

    function filterUniversities() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCountry = filterCountry.value.toLowerCase();
        let visibleCount = 0;

        universityCards.forEach(card => {
            const universityName = card.dataset.name;
            const country = card.dataset.country.toLowerCase();

            const matchesSearch = universityName.includes(searchTerm);
            const matchesCountry = !selectedCountry || country === selectedCountry;

            if (matchesSearch && matchesCountry) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (noResultsMessage) {
            noResultsMessage.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    searchInput?.addEventListener('input', filterUniversities);
    filterCountry?.addEventListener('change', filterUniversities);
</script>
@endpush
@endsection
