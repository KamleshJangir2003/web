@extends('auth.layouts.app')

@section('title', 'Candidate Journey Tracker')

@section('content')

<!-- Bootstrap CSS (Agar layout me already hai to duplicate nahi karega) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.main-content{
    padding-left: 130px;
   
    
}
.journey-card {
    background: white;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.journey-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
    cursor: pointer;
}
.candidate-info h4 {
    margin: 0;
    color: #2d3748;
}
.candidate-meta {
    color: #718096;
    font-size: 14px;
    margin-top: 5px;
}
.timeline {
    position: relative;
    padding-left: 40px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #e2e8f0;
}
.timeline-item {
    position: relative;
    padding-bottom: 30px;
}
.timeline-dot {
    position: absolute;
    left: -29px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid;
}
.timeline-dot.completed { background: #48bb78; border-color: #48bb78; }
.timeline-dot.pending { background: #ecc94b; border-color: #ecc94b; }
.timeline-dot.rejected { background: #f56565; border-color: #f56565; }
.timeline-dot.scheduled { background: #4299e1; border-color: #4299e1; }

.timeline-content {
    background: #f7fafc;
    padding: 15px;
    border-radius: 8px;
}
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}
.status-interested { background: #bee3f8; color: #2c5282; }
.status-lead { background: #c6f6d5; color: #22543d; }
.status-interview { background: #feebc8; color: #7c2d12; }
.status-hired { background: #9ae6b4; color: #22543d; }
.status-rejected { background: #fed7d7; color: #742a2a; }
.status-callback { background: #fbd38d; color: #744210; }
.status-not-interested { background: #e2e8f0; color: #2d3748; }

.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.stat-number {
    font-size: 32px;
    font-weight: bold;
}
.search-bar input {
    width: 100%;
    padding: 12px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
}
.collapse {
    display: none;
}
.collapse.show {
    display: block;
}
</style>
<style>
    /* 5 Columns Equal Width */
.journey-wrapper {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
}

/* Column Safe Width */
.journey-column {
    min-width: 0;
}

/* Card Size Compact */
.journey-card {
    padding: 10px;
    margin-bottom: 15px;
}

/* Heading Size Smaller */
.journey-column h4 {
    font-size: 16px;
}

/* Candidate Name */
.candidate-info h4 {
    font-size: 15px;
}

/* Meta Text */
.candidate-meta {
    font-size: 11px;
}

/* Status Badge Smaller */
.status-badge {
    font-size: 11px;
    padding: 4px 8px;
}
</style>

<div class="container-fluid py-4">

    <!-- <h2 class="mb-4"><i class="bi bi-diagram-3"></i> Candidate Journey Tracker</h2> -->

    <!-- Stats -->
    <!-- <div class="stats-row">
        <div class="stat-card">
            <div class="stat-number">{{ $journeys->count() }}</div>
            <div>Total Candidates</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $journeys->where('current_stage','hired')->count() }}</div>
            <div>Hired</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $journeys->where('final_status','rejected')->count() }}</div>
            <div>Rejected</div>
        </div>
    </div> -->

    <!-- Search -->
    <div class="search-bar mb-4">
        <input type="text" id="searchInput" placeholder="Search by name, email, or phone..." value="{{ $query ?? '' }}">
    </div>

    <!-- Accordion Wrapper -->
    <div class="journey-wrapper">

    {{-- ================= CALL BACKS ================= --}}
    <div class="journey-column">
        <h4 class="text-info mb-3">Call Backs ({{ $journeys->where('final_status','callback')->count() }})</h4>

        @foreach($journeys->where('final_status','callback')->take(10) as $journey)

        <div class="journey-card">

            <button class="journey-header w-100 border-0 bg-transparent text-start"
                    type="button"
                    data-target="#journey-callback-{{ $journey['id'] }}">

                <div class="candidate-info">
                    <h4>{{ $journey['name'] }}</h4>
                    <div class="candidate-meta">
                        {{ $journey['email'] }} |
                        {{ $journey['phone'] }} |
                        {{ $journey['role'] }}
                    </div>
                </div>

                <span class="status-badge status-callback">
                    Call Back
                </span>
            </button>

            <div id="journey-callback-{{ $journey['id'] }}" class="collapse">

                <div class="accordion-body">

                    <div class="timeline">
                        @foreach($journey['stages'] ?? [] as $stage)
                            <div class="timeline-item">
                                <div class="timeline-dot {{ $stage['status'] ?? 'pending' }}"></div>
                                <div class="timeline-content">
                                    <strong>{{ $stage['title'] ?? $stage['name'] ?? $stage['stage_name'] ?? 'Stage' }}</strong>

                                    @if(!empty($stage['date']))
                                        <br>
                                        <small>{{ \Carbon\Carbon::parse($stage['date'])->format('d M Y') }}</small>
                                    @endif

                                    @if(!empty($stage['notes']))
                                        <div class="mt-2">{{ $stage['notes'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="alert alert-info mt-3">
                        <strong>Status:</strong> Awaiting callback
                    </div>

                </div>
            </div>

        </div>

        @endforeach

        @if($journeys->where('final_status','callback')->count() > 10)
        <div class="journey-card bg-light">
            <div class="text-center py-3">
                <strong>Others ({{ $journeys->where('final_status','callback')->count() - 10 }})</strong>
            </div>
        </div>
        @endif
    </div>

    {{-- ================= NOT INTERESTED ================= --}}
    <div class="journey-column">
        <h4 class="text-secondary mb-3">Not Interested ({{ $journeys->where('final_status','not_interested')->count() }})</h4>

        @foreach($journeys->where('final_status','not_interested')->take(10) as $journey)

        <div class="journey-card">

            <button class="journey-header w-100 border-0 bg-transparent text-start"
                    type="button"
                    data-target="#journey-notinterested-{{ $journey['id'] }}">

                <div class="candidate-info">
                    <h4>{{ $journey['name'] }}</h4>
                    <div class="candidate-meta">
                        {{ $journey['email'] }} |
                        {{ $journey['phone'] }} |
                        {{ $journey['role'] }}
                    </div>
                </div>

                <span class="status-badge status-not-interested">
                    Not Interested
                </span>
            </button>

            <div id="journey-notinterested-{{ $journey['id'] }}" class="collapse">

                <div class="accordion-body">

                    <div class="timeline">
                        @foreach($journey['stages'] ?? [] as $stage)
                            <div class="timeline-item">
                                <div class="timeline-dot {{ $stage['status'] ?? 'pending' }}"></div>
                                <div class="timeline-content">
                                    <strong>{{ $stage['title'] ?? $stage['name'] ?? $stage['stage_name'] ?? 'Stage' }}</strong>

                                    @if(!empty($stage['date']))
                                        <br>
                                        <small>{{ \Carbon\Carbon::parse($stage['date'])->format('d M Y') }}</small>
                                    @endif

                                    @if(!empty($stage['notes']))
                                        <div class="mt-2">{{ $stage['notes'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="alert alert-secondary mt-3">
                        <strong>Reason:</strong><br>
                        {{ $journey['rejection_reason'] ?? 'Candidate not interested' }}
                    </div>

                </div>
            </div>

        </div>

        @endforeach

        @if($journeys->where('final_status','not_interested')->count() > 10)
        <div class="journey-card bg-light">
            <div class="text-center py-3">
                <strong>Others ({{ $journeys->where('final_status','not_interested')->count() - 10 }})</strong>
            </div>
        </div>
        @endif
    </div>

    {{-- ================= HIRED ================= --}}
    <div class="journey-column">
        <h4 class="text-success mb-3">Hired ({{ $journeys->where('current_stage','hired')->count() }})</h4>

        @foreach($journeys->where('current_stage','hired')->take(10) as $journey)

        <div class="journey-card">

            <button class="journey-header w-100 border-0 bg-transparent text-start"
                    type="button"
                    data-target="#journey-hired-{{ $journey['id'] }}">

                <div class="candidate-info">
                    <h4>{{ $journey['name'] }}</h4>
                    <div class="candidate-meta">
                        {{ $journey['email'] }} |
                        {{ $journey['phone'] }} |
                        {{ $journey['role'] }}
                    </div>
                </div>

                <span class="status-badge status-hired">
                    Hired
                </span>
            </button>

            <div id="journey-hired-{{ $journey['id'] }}" class="collapse">

                <div class="accordion-body">

                    {{-- SAME TIMELINE CODE --}}
                    <div class="timeline">
                        @foreach($journey['stages'] ?? [] as $stage)
                            <div class="timeline-item">
                                <div class="timeline-dot {{ $stage['status'] ?? 'pending' }}"></div>
                                <div class="timeline-content">
                                    <strong>{{ $stage['title'] ?? $stage['name'] ?? $stage['stage_name'] ?? 'Stage' }}</strong>

                                    @if(!empty($stage['result']))
                                        <span class="badge bg-{{ $stage['result'] === 'Rejected' ? 'danger' : ($stage['result'] === 'Selected' ? 'success' : 'warning') }} ms-2">
                                            {{ $stage['result'] }}
                                        </span>
                                    @endif
                                    <br>

                                    @if(!empty($stage['date']))
                                        <small>{{ \Carbon\Carbon::parse($stage['date'])->format('d M Y') }}</small><br>
                                    @endif

                                    @if(!empty($stage['notes']))
                                        <div class="mt-2">{{ $stage['notes'] }}</div>
                                    @endif

                                    @if(!empty($stage['reason']))
                                        <div class="mt-2 text-danger"><strong>Reason:</strong> {{ $stage['reason'] }}</div>
                                    @endif

                                    @if(!empty($stage['rejection_reason']))
                                        <div class="mt-2 text-danger"><strong>Rejection Reason:</strong> {{ $stage['rejection_reason'] }}</div>
                                    @endif

                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

        </div>

        @endforeach

        @if($journeys->where('current_stage','hired')->count() > 10)
        <div class="journey-card bg-light">
            <div class="text-center py-3">
                <strong>Others ({{ $journeys->where('current_stage','hired')->count() - 10 }})</strong>
            </div>
        </div>
        @endif
    </div>

    {{-- ================= INTERVIEW REJECT ================= --}}
    <div class="journey-column">
        <h4 class="text-warning mb-3">Interview Reject ({{ $journeys->where('final_status','interview_reject')->count() }})</h4>

        @foreach($journeys->where('final_status','interview_reject')->take(10) as $journey)

        <div class="journey-card">

            <button class="journey-header w-100 border-0 bg-transparent text-start"
                    type="button"
                    data-target="#journey-interview-{{ $journey['id'] }}">

                <div class="candidate-info">
                    <h4>{{ $journey['name'] }}</h4>
                    <div class="candidate-meta">
                        {{ $journey['email'] }} |
                        {{ $journey['phone'] }} |
                        {{ $journey['role'] }}
                    </div>
                </div>

                <span class="status-badge status-rejected">
                    Interview Reject
                </span>
            </button>

            <div id="journey-interview-{{ $journey['id'] }}" class="collapse">

                <div class="accordion-body">

                    <div class="timeline">
                        @foreach($journey['stages'] ?? [] as $stage)
                            <div class="timeline-item">
                                <div class="timeline-dot rejected"></div>
                                <div class="timeline-content">
                                    <strong>{{ $stage['name'] ?? 'Stage' }}</strong>

                                    @if(!empty($stage['date']))
                                        <br>
                                        <small>{{ \Carbon\Carbon::parse($stage['date'])->format('d M Y') }}</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="alert alert-warning mt-3">
                        <strong>Interview Rejection Reason:</strong><br>
                        {{ $journey['rejection_reason'] ?? 'No reason provided' }}
                    </div>

                </div>
            </div>

        </div>

        @endforeach

        @if($journeys->where('final_status','interview_reject')->count() > 10)
        <div class="journey-card bg-light">
            <div class="text-center py-3">
                <strong>Others ({{ $journeys->where('final_status','interview_reject')->count() - 10 }})</strong>
            </div>
        </div>
        @endif
    </div>

    {{-- ================= NOT SELECTED ================= --}}
    <div class="journey-column">
        <h4 class="text-danger mb-3">Not Selected/Rejected ({{ $journeys->where('final_status','rejected')->count() }})</h4>

        @foreach($journeys->where('final_status','rejected')->take(10) as $journey)

        <div class="journey-card">

            <button class="journey-header w-100 border-0 bg-transparent text-start"
                    type="button"
                    data-target="#journey-rejected-{{ $journey['id'] }}">

                <div class="candidate-info">
                    <h4>{{ $journey['name'] }}</h4>
                    <div class="candidate-meta">
                        {{ $journey['email'] }} |
                        {{ $journey['phone'] }} |
                        {{ $journey['role'] }}
                    </div>
                </div>

                <span class="status-badge status-rejected">
                    Not Selected
                </span>
            </button>

            <div id="journey-rejected-{{ $journey['id'] }}" class="collapse">

                <div class="accordion-body">

                    {{-- SAME TIMELINE CODE --}}
                    <div class="timeline">
                        @foreach($journey['stages'] ?? [] as $stage)
                            <div class="timeline-item">
                                <div class="timeline-dot {{ $stage['status'] ?? 'pending' }}"></div>
                                <div class="timeline-content">
                                    <strong>{{ $stage['title'] ?? $stage['name'] ?? $stage['stage_name'] ?? 'Stage' }}</strong>

                                    @if(!empty($stage['result']))
                                        <span class="badge bg-danger ms-2">
                                            {{ $stage['result'] }}
                                        </span>
                                    @endif
                                    <br>

                                    @if(!empty($stage['date']))
                                        <small>{{ \Carbon\Carbon::parse($stage['date'])->format('d M Y') }}</small><br>
                                    @endif

                                    @if(!empty($stage['notes']))
                                        <div class="mt-2">{{ $stage['notes'] }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- FINAL REJECTION ALERT SAME --}}
                    <div class="alert alert-danger mt-3">
                        <strong>Final Rejection Reason:</strong><br>
                        {{ $journey['rejection_reason'] ?? 'No reason provided' }}
                    </div>

                </div>
            </div>

        </div>

        @endforeach

        @if($journeys->where('final_status','rejected')->count() > 10)
        <div class="journey-card bg-light">
            <div class="text-center py-3">
                <strong>Others ({{ $journeys->where('final_status','rejected')->count() - 10 }})</strong>
            </div>
        </div>
        @endif
    </div>

</div>
</div>

<!-- Bootstrap JS (VERY IMPORTANT) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Live Search Functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const allCards = document.querySelectorAll('.journey-card');
    
    allCards.forEach(card => {
        const name = card.querySelector('.candidate-info h4').textContent.toLowerCase();
        const meta = card.querySelector('.candidate-meta').textContent.toLowerCase();
        
        if (name.includes(searchTerm) || meta.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

// Simple toggle
document.querySelectorAll('.journey-header').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const targetElement = document.querySelector(targetId);
        targetElement.classList.toggle('show');
    });
});
</script>

@endsection