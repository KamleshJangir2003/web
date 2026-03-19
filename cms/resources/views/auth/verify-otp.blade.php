<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP | Kwikster CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #5f72ff, #9b59b6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .otp-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 480px;
            padding: 45px 35px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        .btn-verify {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .btn-resend {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-resend:hover {
            background: #667eea;
            color: white;
        }
        .otp-input {
            text-align: center;
            font-size: 24px;
            letter-spacing: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="otp-card">
        <div class="text-center mb-4">
            <i class="fas fa-shield-alt" style="font-size: 48px; color: #667eea;"></i>
            <h2 class="fw-bold mb-2 mt-3">Verify OTP</h2>
            <p class="text-muted">Enter the 6-digit code sent to<br><strong>{{ session('otp_email') }}</strong></p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label">Enter OTP</label>
                <input type="text" name="otp" class="form-control otp-input" 
                       maxlength="6" required placeholder="000000" 
                       pattern="[0-9]{6}" autofocus>
            </div>

            <button type="submit" class="btn-verify mb-3">
                <i class="fas fa-check-circle me-2"></i>Verify OTP
            </button>
        </form>

        <form method="POST" action="{{ route('otp.resend') }}">
            @csrf
            <button type="submit" class="btn-resend">
                <i class="fas fa-redo me-2"></i>Resend OTP
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-muted" style="text-decoration: none;">
                <i class="fas fa-arrow-left me-1"></i>Back to Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
