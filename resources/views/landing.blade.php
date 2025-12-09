<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Tourliz</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            max-width: 500px;
            width: 100%;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            font-size: 2rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .logo-icon {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .logo-icon .triangle-blue {
            width: 0;
            height: 0;
            border-left: 15px solid #3b82f6;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
        }
        
        .logo-icon .triangle-red {
            width: 0;
            height: 0;
            border-left: 15px solid #ef4444;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            margin-left: -15px;
        }
        
        .logo-text {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -1px;
        }
        
        .logo-text .letter-i {
            display: inline-block;
            position: relative;
        }
        
        .logo-text .letter-i .dot {
            width: 12px;
            height: 12px;
            background: #3b82f6;
            border-radius: 2px;
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .logo-text .letter-i .bar {
            width: 4px;
            height: 30px;
            background: #ef4444;
            border-radius: 2px;
            margin: 0 auto;
        }
        
        .tagline {
            color: rgba(255,255,255,0.9);
            font-size: 0.9rem;
            margin-top: 5px;
            font-weight: 400;
        }
        
        .auth-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .auth-card h2 {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 2rem;
        }
        
        .auth-card p {
            color: #666;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }
        
        .form-check {
            margin-bottom: 20px;
        }
        
        .form-check-label {
            color: #666;
            font-weight: normal;
        }
        
        .switch-auth {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .switch-auth a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .switch-auth a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .auth-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Logo -->
        <div class="logo-container">
            <a href="{{ route('landing') }}" class="logo">
                <div class="logo-icon">
                    <div class="triangle-blue"></div>
                    <div class="triangle-red"></div>
                </div>
                <div class="logo-text">
                    TOUR<span class="letter-i">
                        <span class="dot"></span>
                        <span class="bar"></span>
                    </span>LIZ
                </div>
            </a>
            <div class="tagline">YOUR TRAVEL PARTNER</div>
        </div>
        
        <!-- Login Card -->
        <div class="auth-card">
            <h2><i class="bi bi-box-arrow-in-right"></i> Login</h2>
            <p>Sign in to your account to continue</p>
            
            @if ($errors->has('email') || $errors->has('password'))
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->get('email') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        @foreach ($errors->get('password') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <label for="login_email"><i class="bi bi-envelope"></i> Email Address</label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="login_email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email" 
                           autofocus
                           placeholder="Enter your email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="login_password"><i class="bi bi-lock"></i> Password</label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="login_password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Enter your password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" 
                           type="checkbox" 
                           name="remember" 
                           id="remember" 
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
            </form>
            
            <div class="switch-auth">
                Don't have an account? <a href="{{ route('register') }}">Register here</a>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
