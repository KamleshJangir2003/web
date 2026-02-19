@extends('auth.layouts.app')
<style>
    .container-fluid{
    margin-top: 60px !important;
    padding-left: 130px !important;
}
</style>
@section('content')

@php
$isAdminView = $isAdminView ?? false;
$totalRequired = $totalRequired ?? 0;
$uploadedCount = $uploadedCount ?? 0;
$verifiedCount = $verifiedCount ?? 0;
$submittedCount = $submittedCount ?? 0;
$pendingCount  = $pendingCount ?? 0;

$progress = $totalRequired > 0
    ? min(100, round(($uploadedCount / $totalRequired) * 100))
    : 0;

// Routes based on context
$uploadRoute = $isAdminView ? route('admin.employees.document.upload', ['userId' => $user->id]) : route('employee.documents.upload');
$bankRoute = $isAdminView ? route('admin.employees.document.bank-details', ['userId' => $user->id]) : route('employee.bank.details');
$submitRoute = $isAdminView ? route('admin.employees.document.submit', ['userId' => $user->id]) : route('employee.documents.submit');
@endphp

<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Employee Documents</h4>
        @if($isAdminView)
        <a href="{{ route('admin.employees.documents.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to List
        </a>
        @endif
    </div>

    <!-- Employee Info -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
            <strong>
    {{ $user->full_name ?: ($user->name ?: 'N/A') }}
</strong>
<br>
                <small>EMP ID: {{ $user->id }}</small>
            </div>

            <div style="width:150px">
                <div class="progress" style="height:6px">
                    <div class="progress-bar bg-success"
                         style="width: {{ $progress }}%"></div>
                </div>
                <small>{{ $progress }}%</small>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">

        <!-- LEFT -->
        <div class="col-md-6">

            <!-- AADHAR -->
            @php $aadhar = $documents->where('document_type','aadhar_card')->first(); @endphp
            <div class="card mb-3">
                <div class="card-body">
                    <strong>Aadhar Card *</strong>
                    <span class="badge bg-{{ $aadhar?->status === 'verified' ? 'success' : ($aadhar?->status === 'submitted' ? 'info' : ($aadhar?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                        {{ $aadhar->status ?? 'not uploaded' }}
                    </span>

                    @if($aadhar)
                        <div class="mt-2">
                            <a href="{{ route('employee.documents.view',$aadhar->id) }}" class="btn btn-sm btn-primary">View</a>
                            <a href="{{ route('employee.documents.download',$aadhar->id) }}" class="btn btn-sm btn-secondary">Download</a>
                            <button onclick="printDocument('{{ route('employee.documents.view',$aadhar->id) }}')" class="btn btn-sm btn-info">Print</button>
                            @if($aadhar->status !== 'submitted' && $aadhar->status !== 'verified')
                            <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                @csrf
                                <input type="hidden" name="document_type" value="aadhar_card">
                                <input type="file" name="document" required class="form-control mb-2"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <button type="submit" class="btn btn-warning btn-sm">Replace</button>
                            </form>
                            @endif
                        </div>
                    @else
                        <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="document_type" value="aadhar_card">
                            <input type="file" name="document" required class="form-control mt-2"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Upload</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- PAN -->
            @php $pan = $documents->where('document_type','pan_card')->first(); @endphp
            <div class="card mb-3">
                <div class="card-body">
                    <strong>PAN Card *</strong>
                    <span class="badge bg-{{ $pan?->status === 'verified' ? 'success' : ($pan?->status === 'submitted' ? 'info' : ($pan?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                        {{ $pan->status ?? 'not uploaded' }}
                    </span>

                    @if($pan)
                        <div class="mt-2">
                            <a href="{{ route('employee.documents.view',$pan->id) }}" class="btn btn-sm btn-primary">View</a>
                            <a href="{{ route('employee.documents.download',$pan->id) }}" class="btn btn-sm btn-secondary">Download</a>
                            <button onclick="printDocument('{{ route('employee.documents.view',$pan->id) }}')" class="btn btn-sm btn-info">Print</button>
                            @if($pan->status !== 'submitted' && $pan->status !== 'verified')
                            <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                @csrf
                                <input type="hidden" name="document_type" value="pan_card">
                                <input type="file" name="document" required class="form-control mb-2"
                                       accept=".pdf,.jpg,.jpeg,.png">
                                <button type="submit" class="btn btn-warning btn-sm">Replace</button>
                            </form>
                            @endif
                        </div>
                    @else
                        <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="document_type" value="pan_card">
                            <input type="file" name="document" required class="form-control mt-2"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Upload</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- PHOTO -->
            @php $photo = $documents->where('document_type','photo')->first(); @endphp
            <div class="card mb-3">
                <div class="card-body">
                    <strong>Photo *</strong>
                    <span class="badge bg-{{ $photo?->status === 'verified' ? 'success' : ($photo?->status === 'submitted' ? 'info' : ($photo?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                        {{ $photo->status ?? 'not uploaded' }}
                    </span>

                    @if($photo)
                        <div class="mt-2">
                            <a href="{{ route('employee.documents.view',$photo->id) }}" class="btn btn-sm btn-primary">View</a>
                            <a href="{{ route('employee.documents.download',$photo->id) }}" class="btn btn-sm btn-secondary">Download</a>
                            <button onclick="printDocument('{{ route('employee.documents.view',$photo->id) }}')" class="btn btn-sm btn-info">Print</button>
                            @if($photo->status !== 'submitted' && $photo->status !== 'verified')
                            <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                @csrf
                                <input type="hidden" name="document_type" value="photo">
                                <input type="file" name="document" required class="form-control mb-2"
                                       accept=".jpg,.jpeg,.png">
                                <button type="submit" class="btn btn-warning btn-sm">Replace</button>
                            </form>
                            @endif
                        </div>
                    @else
                        <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="document_type" value="photo">
                            <input type="file" name="document" required class="form-control mt-2"
                                   accept=".jpg,.jpeg,.png">
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Upload</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- 10th / 12th (Any One Required) -->
            <div class="card mb-3">
                <div class="card-header">
                    <strong>10th / 12th Marksheet (Any One Required) *</strong>
                </div>
                <div class="card-body">
                    @php
                    $class10 = $documents->where('document_type','marksheet_10th')->first();
                    $class12 = $documents->where('document_type','marksheet_12th')->first();
                    @endphp
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong>10th/12th Marksheet</strong>
                            <span class="badge bg-{{ $class10?->status === 'verified' ? 'success' : ($class10?->status === 'submitted' ? 'info' : ($class10?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                                {{ $class10->status ?? 'not uploaded' }}
                            </span>
                            @if($class10)
                                <div class="mt-2">
                                    <a href="{{ route('employee.documents.view',$class10->id) }}" class="btn btn-sm btn-primary">View</a>
                                    <a href="{{ route('employee.documents.download',$class10->id) }}" class="btn btn-sm btn-secondary">Download</a>
                                </div>
                            @else
                                <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="document_type" value="marksheet_10th">
                                    <input type="file" name="document" class="form-control mb-2" accept=".pdf,.jpg,.jpeg,.png">
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </form>
                            @endif
                        </div>
                        <!-- <div class="col-md-6">
                            <strong>12th Marksheet</strong>
                            <span class="badge bg-{{ $class12?->status === 'verified' ? 'success' : ($class12?->status === 'submitted' ? 'info' : ($class12?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                                {{ $class12->status ?? 'not uploaded' }}
                            </span>
                            @if($class12)
                                <div class="mt-2">
                                    <a href="{{ route('employee.documents.view',$class12->id) }}" class="btn btn-sm btn-primary">View</a>
                                    <a href="{{ route('employee.documents.download',$class12->id) }}" class="btn btn-sm btn-secondary">Download</a>
                                </div>
                            @else
                                <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="document_type" value="marksheet_12th">
                                    <input type="file" name="document" class="form-control mb-2" accept=".pdf,.jpg,.jpeg,.png">
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </form>
                            @endif
                        </div> -->
                    </div>
                </div>
            </div>

            <!-- Diploma / Graduate / PG (Any One Required) -->
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Diploma / Graduate / PG (Any One Required) *</strong>
                </div>
                <div class="card-body">
                    @php
                    $diploma = $documents->where('document_type','diploma')->first();
                    $graduation = $documents->where('document_type','graduation')->first();
                    $pg = $documents->where('document_type','post_graduation')->first();
                    @endphp
                    
                    <div class="row">
                        <div class="col-md-4">
                            <strong>D/G/P</strong>
                            <span class="badge bg-{{ $diploma?->status === 'verified' ? 'success' : ($diploma?->status === 'submitted' ? 'info' : ($diploma?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                                {{ $diploma->status ?? 'not uploaded' }}
                            </span>
                            @if($diploma)
                                <div class="mt-2">
                                    <a href="{{ route('employee.documents.view',$diploma->id) }}" class="btn btn-sm btn-primary">View</a>
                                    <a href="{{ route('employee.documents.download',$diploma->id) }}" class="btn btn-sm btn-secondary">Download</a>
                                </div>
                            @else
                                <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="document_type" value="diploma">
                                    <input type="file" name="document" class="form-control mb-2" accept=".pdf,.jpg,.jpeg,.png">
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </form>
                            @endif
                        </div>
                        <!-- <div class="col-md-4">
                            <strong>Graduation</strong>
                            <span class="badge bg-{{ $graduation?->status === 'verified' ? 'success' : ($graduation?->status === 'submitted' ? 'info' : ($graduation?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                                {{ $graduation->status ?? 'not uploaded' }}
                            </span>
                            @if($graduation)
                                <div class="mt-2">
                                    <a href="{{ route('employee.documents.view',$graduation->id) }}" class="btn btn-sm btn-primary">View</a>
                                    <a href="{{ route('employee.documents.download',$graduation->id) }}" class="btn btn-sm btn-secondary">Download</a>
                                </div>
                            @else
                                <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="document_type" value="graduation">
                                    <input type="file" name="document" class="form-control mb-2" accept=".pdf,.jpg,.jpeg,.png">
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </form>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <strong>Post Graduation</strong>
                            <span class="badge bg-{{ $pg?->status === 'verified' ? 'success' : ($pg?->status === 'submitted' ? 'info' : ($pg?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                                {{ $pg->status ?? 'not uploaded' }}
                            </span>
                            @if($pg)
                                <div class="mt-2">
                                    <a href="{{ route('employee.documents.view',$pg->id) }}" class="btn btn-sm btn-primary">View</a>
                                    <a href="{{ route('employee.documents.download',$pg->id) }}" class="btn btn-sm btn-secondary">Download</a>
                                </div>
                            @else
                                <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="document_type" value="post_graduation">
                                    <input type="file" name="document" class="form-control mb-2" accept=".pdf,.jpg,.jpeg,.png">
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </form>
                            @endif
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="col-md-6">

            <!-- Passbook / Cheque (Any One Required) -->
            <div class="card mb-3">
                <div class="card-header">
                    <strong>Passbook / Cheque (Any One Required) *</strong>
                </div>
                <div class="card-body">
                    @php
                    $passbook = $documents->where('document_type','passbook')->first();
                    $cheque = $documents->where('document_type','cheque')->first();
                    @endphp
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Passbook/Cheque</strong>
                            <span class="badge bg-{{ $passbook?->status === 'verified' ? 'success' : ($passbook?->status === 'submitted' ? 'info' : ($passbook?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                                {{ $passbook->status ?? 'not uploaded' }}
                            </span>
                            @if($passbook)
                                <div class="mt-2">
                                    <a href="{{ route('employee.documents.view',$passbook->id) }}" class="btn btn-sm btn-primary">View</a>
                                    <a href="{{ route('employee.documents.download',$passbook->id) }}" class="btn btn-sm btn-secondary">Download</a>
                                    <button onclick="printDocument('{{ route('employee.documents.view',$passbook->id) }}')" class="btn btn-sm btn-info">Print</button>
                                </div>
                            @else
                                <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="document_type" value="passbook">
                                    <input type="file" name="document" class="form-control mb-2" accept=".pdf,.jpg,.jpeg,.png">
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </form>
                            @endif
                        </div>
                        <!-- <div class="col-md-6">
                            <strong>Cheque</strong>
                            <span class="badge bg-{{ $cheque?->status === 'verified' ? 'success' : ($cheque?->status === 'submitted' ? 'info' : ($cheque?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                                {{ $cheque->status ?? 'not uploaded' }}
                            </span>
                            @if($cheque)
                                <div class="mt-2">
                                    <a href="{{ route('employee.documents.view',$cheque->id) }}" class="btn btn-sm btn-primary">View</a>
                                    <a href="{{ route('employee.documents.download',$cheque->id) }}" class="btn btn-sm btn-secondary">Download</a>
                                    <button onclick="printDocument('{{ route('employee.documents.view',$cheque->id) }}')" class="btn btn-sm btn-info">Print</button>
                                </div>
                            @else
                                <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="document_type" value="cheque">
                                    <input type="file" name="document" class="form-control mb-2" accept=".pdf,.jpg,.jpeg,.png">
                                    <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                                </form>
                            @endif
                        </div> -->
                    </div>
                </div>
            </div>

            <div class="card mb-3">
    <div class="card-header">
        <strong>PF / ESI Document (PDF Required) *</strong>
    </div>
    <div class="card-body">

        @php
            $pfEsi = $documents->where('document_type','pf_esi')->first();
        @endphp

        <div class="row">
            <div class="col-md-6">
                <strong>PF / ESI PDF</strong>

                <span class="badge bg-{{ $pfEsi?->status === 'verified' ? 'success' : ($pfEsi?->status === 'submitted' ? 'info' : ($pfEsi?->status === 'uploaded' ? 'secondary' : 'warning')) }} float-end">
                    {{ $pfEsi->status ?? 'not uploaded' }}
                </span>

                @if($pfEsi)
                    <div class="mt-2">
                        <a href="{{ route('employee.documents.view',$pfEsi->id) }}" class="btn btn-sm btn-primary">View</a>
                        <a href="{{ route('employee.documents.download',$pfEsi->id) }}" class="btn btn-sm btn-secondary">Download</a>
                        <button onclick="printDocument('{{ route('employee.documents.view',$pfEsi->id) }}')" class="btn btn-sm btn-info">Print</button>
                    </div>
                @else
                    <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                        @csrf
                        <input type="hidden" name="document_type" value="pf_esi">

                        <input type="file" 
                               name="document" 
                               class="form-control mb-2" 
                               accept=".pdf" 
                               required>


                        <button type="submit" class="btn btn-primary btn-sm mt-2">
                            Upload
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</div>


            <!-- Bank Statement (Optional) -->
            @php $bankStatement = $documents->where('document_type','bank_statement')->first(); @endphp
            <div class="card mb-3">
                <div class="card-body">
                    <strong> Last Company Months Bank Statement / Salary Slip <small class="text-muted">(Optional)</small></strong>
                    <span class="badge bg-{{ $bankStatement?->status === 'verified' ? 'success' : ($bankStatement?->status === 'submitted' ? 'info' : ($bankStatement?->status === 'uploaded' ? 'secondary' : 'secondary')) }} float-end">
                        {{ $bankStatement->status ?? 'not uploaded' }}
                    </span>

                    @if($bankStatement)
                        <div class="mt-2">
                            <a href="{{ route('employee.documents.view',$bankStatement->id) }}" class="btn btn-sm btn-primary">View</a>
                            <a href="{{ route('employee.documents.download',$bankStatement->id) }}" class="btn btn-sm btn-secondary">Download</a>
                            <button onclick="printDocument('{{ route('employee.documents.view',$bankStatement->id) }}')" class="btn btn-sm btn-info">Print</button>
                            @if($bankStatement->status !== 'submitted' && $bankStatement->status !== 'verified')
                            <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                @csrf
                                <input type="hidden" name="document_type" value="bank_statement">
                                <input type="file" name="document" class="form-control mb-2" accept=".pdf,.jpg,.jpeg,.png">
                                <button type="submit" class="btn btn-warning btn-sm">Replace</button>
                            </form>
                            @endif
                        </div>
                    @else
                        <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="document_type" value="bank_statement">
                            <input type="file" name="document" class="form-control mt-2" accept=".pdf,.jpg,.jpeg,.png">
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Upload</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Experience Letter (Optional) -->
            @php $experienceLetter = $documents->where('document_type','experience_letter')->first(); @endphp
            <div class="card mb-3">
                <div class="card-body">
                    <strong>Last Company Offer Letter / Experience Letter <small class="text-muted">(Optional)</small></strong>
                    <span class="badge bg-{{ $experienceLetter?->status === 'verified' ? 'success' : ($experienceLetter?->status === 'submitted' ? 'info' : ($experienceLetter?->status === 'uploaded' ? 'secondary' : 'secondary')) }} float-end">
                        {{ $experienceLetter->status ?? 'not uploaded' }}
                    </span>

                    @if($experienceLetter)
                        <div class="mt-2">
                            <a href="{{ route('employee.documents.view',$experienceLetter->id) }}" class="btn btn-sm btn-primary">View</a>
                            <a href="{{ route('employee.documents.download',$experienceLetter->id) }}" class="btn btn-sm btn-secondary">Download</a>
                            <button onclick="printDocument('{{ route('employee.documents.view',$experienceLetter->id) }}')" class="btn btn-sm btn-info">Print</button>
                            @if($experienceLetter->status !== 'submitted' && $experienceLetter->status !== 'verified')
                            <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data" class="mt-2">
                                @csrf
                                <input type="hidden" name="document_type" value="experience_letter">
                                <input type="file" name="document" class="form-control mb-2" accept=".pdf,.jpg,.jpeg,.png">
                                <button type="submit" class="btn btn-warning btn-sm">Replace</button>
                            </form>
                            @endif
                        </div>
                    @else
                        <form method="POST" action="{{ $uploadRoute }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="document_type" value="experience_letter">
                            <input type="file" name="document" class="form-control mt-2" accept=".pdf,.jpg,.jpeg,.png">
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Upload</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="card">
                <div class="card-body">
                    @php
                    // Check required documents
                    $aadhar = $documents->where('document_type','aadhar_card')->first();
                    $pan = $documents->where('document_type','pan_card')->first();
                    $photo = $documents->where('document_type','photo')->first();
                    
                    // Check any one from 10th/12th
                    $class10 = $documents->where('document_type','marksheet_10th')->first();
                    $class12 = $documents->where('document_type','marksheet_12th')->first();
                    $hasEducation10or12 = ($class10 && in_array($class10->status, ['uploaded', 'submitted', 'verified'])) || 
                                         ($class12 && in_array($class12->status, ['uploaded', 'submitted', 'verified']));
                    
                    // Check any one from diploma/graduation/pg
                    $diploma = $documents->where('document_type','diploma')->first();
                    $graduation = $documents->where('document_type','graduation')->first();
                    $pg = $documents->where('document_type','post_graduation')->first();
                    $hasHigherEducation = ($diploma && in_array($diploma->status, ['uploaded', 'submitted', 'verified'])) || 
                                         ($graduation && in_array($graduation->status, ['uploaded', 'submitted', 'verified'])) ||
                                         ($pg && in_array($pg->status, ['uploaded', 'submitted', 'verified']));
                    
                    // Check any one from passbook/cheque
                    $passbook = $documents->where('document_type','passbook')->first();
                    $cheque = $documents->where('document_type','cheque')->first();
                    $hasBankDoc = ($passbook && in_array($passbook->status, ['uploaded', 'submitted', 'verified'])) || 
                                 ($cheque && in_array($cheque->status, ['uploaded', 'submitted', 'verified']));
                    
                    // Check if all required documents are uploaded
                    $allRequiredUploaded = ($aadhar && in_array($aadhar->status, ['uploaded', 'submitted', 'verified'])) &&
                                          ($pan && in_array($pan->status, ['uploaded', 'submitted', 'verified'])) &&
                                          ($photo && in_array($photo->status, ['uploaded', 'submitted', 'verified'])) &&
                                          $hasEducation10or12 &&
                                          $hasHigherEducation &&
                                          $hasBankDoc;
                    @endphp
                    
                    <p>Total Required: <strong>{{ $totalRequired }}</strong></p>
                    <p>Uploaded: <strong>{{ $uploadedCount }}</strong></p>
                    <p>Submitted: <strong>{{ $submittedCount }}</strong></p>
                    <p>Verified: <strong>{{ $verifiedCount }}</strong></p>
                    <p>Pending: <strong>{{ $pendingCount }}</strong></p>

                    @if($allRequiredUploaded)
                        <form method="POST" action="{{ $submitRoute }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="fa-solid fa-envelope me-1"></i> Submit & Send Offer Letter
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <strong>Required Documents Missing:</strong><br>
                            @if(!$aadhar || !in_array($aadhar->status, ['uploaded', 'submitted', 'verified']))
                                • Aadhar Card<br>
                            @endif
                            @if(!$pan || !in_array($pan->status, ['uploaded', 'submitted', 'verified']))
                                • PAN Card<br>
                            @endif
                            @if(!$photo || !in_array($photo->status, ['uploaded', 'submitted', 'verified']))
                                • Photo<br>
                            @endif
                            @if(!$hasEducation10or12)
                                • 10th or 12th Marksheet (Any One)<br>
                            @endif
                            @if(!$hasHigherEducation)
                                • Diploma/Graduation/PG (Any One)<br>
                            @endif
                            @if(!$hasBankDoc)
                                • Passbook or Cheque (Any One)<br>
                            @endif
                        </div>
                        <button type="button" class="btn btn-secondary w-100 mb-2" disabled>
                            <i class="fa-solid fa-envelope me-1"></i> Submit & Send Offer Letter
                        </button>
                    @endif
                    
                    @if($isAdminView)
                        @if($allRequiredUploaded)
                        <form method="POST" action="{{ route('admin.employees.document.generate-offer-letter', ['userId' => $user->id]) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-file-pdf me-1"></i> Generate Offer Letter
                            </button>
                        </form>
                        @else
                        <button type="button" class="btn btn-secondary w-100" disabled>
                            <i class="fa-solid fa-file-pdf me-1"></i> Generate Offer Letter
                        </button>
                        @endif
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function printDocument(url) {
    const printWindow = window.open(url, '_blank');
    printWindow.onload = function() {
        printWindow.print();
    };
}
</script>

@endsection
