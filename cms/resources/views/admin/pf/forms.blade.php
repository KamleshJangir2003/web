@extends('auth.layouts.app')

<style>
/* ================================
   PF FORMS PAGE – FINAL SAFE CSS
   (NO sidebar conflict)
================================ */
.main-content{
    margin-left: 250px !important;

}
/* Page wrapper */
.pf-page {
    margin-left: 0 !important;
    padding: 15px;
    width: 100%;
    box-sizing: border-box;
}

/* Card styling */
.pf-page .card {
    border-radius: 10px;
}

.pf-page .card-header {
    border-bottom: 1px solid #e9ecef;
}

.pf-page h4 {
    font-weight: 600;
    color: #343a40;
}

.pf-page h5 {
    font-weight: 600;
    color: #495057;
}

/* PDF Preview */
.pf-page iframe {
    width: 100%;
    height: 320px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    box-shadow: inset 0 0 0 1px #dee2e6;
}

/* Buttons */
.pf-page .btn {
    border-radius: 6px;
    font-weight: 500;
}

.pf-page .btn + .btn {
    margin-left: 10px;
}

/* Print both */
.pf-page .btn-lg {
    padding: 10px 28px;
    font-size: 16px;
}

/* Footer note */
.pf-page .text-muted {
    font-style: italic;
}

/* ================================
   MOBILE SAFE
================================ */
@media (max-width: 768px) {

    .pf-page iframe {
        height: 220px;
    }

    .pf-page .btn {
        width: 100%;
        margin-left: 0 !important;
        margin-bottom: 8px;
    }

    .pf-page .btn + .btn {
        margin-left: 0;
    }

    .pf-page .btn-lg {
        width: 100%;
    }
}
</style>

@section('content')
<div class="container-fluid pf-page">

    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">PF Forms</h4>
        </div>

        <div class="card-body">
            <div class="row">

                <!-- PF OPT-IN -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border">
                        <div class="card-header text-center bg-light">
                            <h5 class="mb-0">PF Opt-In Form</h5>
                        </div>

                        <div class="card-body text-center">
                            <iframe src="{{ asset('uploads/resumes/pf/pf_form_optin.pdf') }}"></iframe>

                            <div class="mt-3">
                                <a href="{{ asset('uploads/resumes/pf/pf_form_optin.pdf') }}"
                                   target="_blank"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-eye"></i> View Full PDF
                                </a>

                                <button class="btn btn-primary btn-sm"
                                    onclick="openForPrint('{{ asset('uploads/resumes/pf/pf_form_optin.pdf') }}')">
                                    <i class="fa fa-print"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PF OPT-OUT -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border">
                        <div class="card-header text-center bg-light">
                            <h5 class="mb-0">PF Opt-Out Form (Form 11)</h5>
                        </div>

                        <div class="card-body text-center">
                            <iframe src="{{ asset('uploads/resumes/pf/pf_form_optout.pdf') }}"></iframe>

                            <div class="mt-3">
                                <a href="{{ asset('uploads/resumes/pf/pf_form_optout.pdf') }}"
                                   target="_blank"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fa fa-eye"></i> View Full PDF
                                </a>

                                <button class="btn btn-primary btn-sm"
                                    onclick="openForPrint('{{ asset('uploads/resumes/pf/pf_form_optout.pdf') }}')">
                                    <i class="fa fa-print"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- PRINT BOTH -->
            <div class="text-center mt-4">
                <button class="btn btn-success btn-lg" onclick="printBoth()">
                    <i class="fa fa-print"></i> Print Both Forms
                </button>
            </div>

            <p class="text-muted text-center mt-2" style="font-size:13px;">
                Note: PDFs are previewed inline. Use browser print (Ctrl + P).
            </p>
        </div>
    </div>

</div>

<script>
function openForPrint(url) {
    const win = window.open(url, '_blank');
    if (!win) {
        alert('âš ï¸ Popup blocked. Please allow popups to print the PDF.');
    }
}

function printBoth() {
    openForPrint("{{ asset('uploads/resumes/pf/pf_form_optin.pdf') }}");
    setTimeout(() => {
        openForPrint("{{ asset('uploads/resumes/pf/pf_form_optout.pdf') }}");
    }, 1200);
}
</script>
@endsection
