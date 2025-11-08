<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - STTAL HRIS</title>

    <link rel="shortcut icon" href="{{ asset('mazer/dist/assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('mazer/dist/assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('mazer/dist/assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('mazer/dist/assets/compiled/css/auth.css') }}">
</head>

<body>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <h1 class="auth-title">Register</h1>
                    <p class="auth-subtitle mb-5">Create your account to access the dashboard.</p>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input id="name" type="text" class="form-control form-control-xl" name="name"
                                value="{{ old('name') }}" placeholder="Name" required autofocus autocomplete="name">
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input id="email" type="email" class="form-control form-control-xl" name="email"
                                value="{{ old('email') }}" placeholder="Email" required autocomplete="username">
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input id="password" type="password" class="form-control form-control-xl" name="password"
                                placeholder="Password" required autocomplete="new-password">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input id="password_confirmation" type="password" class="form-control form-control-xl"
                                name="password_confirmation" placeholder="Confirm Password" required autocomplete="new-password">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5" type="submit">Register</button>
                    </form>

                    <div class="text-center mt-4 text-lg fs-6">
                        <p>
                            Already have an account?
                            <a class="font-bold" href="{{ route('login') }}">Log in</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right" class="d-flex align-items-center justify-content-center">
                    <div class="logo-container text-center">
                        <img src="{{ asset('mazer/dist/assets/compiled/png/logo-sttal-kecil.png') }}" alt="STTAL Logo">
                        <h3 class="logo-title">STTAL</h3>
                        <p class="logo-subtitle">Human Resource Information System</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
