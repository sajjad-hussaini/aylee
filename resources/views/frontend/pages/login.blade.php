@extends('frontend.layouts.master')

@section('title','E-Shop || Login Page')

@section('main-content')
    <!-- Shop Login -->
    <section class="shop login section">
        <div class="container">
            <div class="login-shell">
                <div class="row align-items-stretch">
                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="login-hero">
                            <div class="hero-badge">Welcome back</div>
                            <h3>Sign in to your account</h3>
                            <p>Access your saved addresses, order history, and enjoy a faster checkout experience.</p>
                            <ul class="feature-list">
                                <li><i class="ti-check"></i> Secure and private sign-in</li>
                                <li><i class="ti-check"></i> Track your orders anytime</li>
                                <li><i class="ti-check"></i> Faster checkout on future purchases</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="login-form">
                            <h2>Login</h2>
                            <p>Please sign in to continue shopping and manage your account.</p>
                            <!-- Form -->
                            <form class="form" method="post" action="{{route('login.submit')}}">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Your Email<span>*</span></label>
                                            <input type="email" name="email" placeholder="Enter your email" required="required" value="{{old('email')}}">
                                            @error('email')
                                                <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Your Password<span>*</span></label>
                                            <input type="password" name="password" placeholder="Enter your password" required="required" value="{{old('password')}}">
                                            @error('password')
                                                <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group login-btn">
                                            <button class="btn" type="submit">Login</button>
                                            <a href="{{route('register.form')}}" class="btn btn-outline">Register</a>
                                        </div>
                                        <div class="checkbox">
                                            <label class="checkbox-inline" for="2"><input name="news" id="2" type="checkbox">Remember me</label>
                                        </div>
                                        @if (Route::has('password.request'))
                                            <a class="lost-pass" href="{{ route('password.request') }}">
                                                Lost your password?
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                            <!--/ End Form -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Login -->
@endsection
@push('styles')
<style>
    .login-shell {
        background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(30, 64, 175, 0.12);
        overflow: hidden;
        border: 1px solid rgba(37, 99, 235, 0.12);
    }

    .login-hero {
        background: linear-gradient(135deg, #0f4c81 0%, #2563eb 45%, #38bdf8 100%);
        color: #fff;
        padding: 60px 40px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .hero-badge {
        display: inline-block;
        width: fit-content;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
    }

    .login-hero h3 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #fff;
    }

    .login-hero p {
        font-size: 15px;
        line-height: 1.7;
        color: rgba(255,255,255,0.9);
        margin-bottom: 20px;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .feature-list li {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 14px;
        color: rgba(255,255,255,0.95);
    }

    .feature-list li i {
        color: #bfdbfe;
        font-size: 16px;
    }

    .login-form {
        padding: 50px 40px;
        background: rgba(255,255,255,0.95);
    }

    .login-form h2 {
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #111827;
    }

    .login-form p {
        color: #64748b;
        margin-bottom: 24px;
    }

    .shop.login .form .btn {
        margin-right: 0;
    }

    .login-btn {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 12px;
    }

    .login-btn .btn {
        min-width: 115px;
        border-radius: 8px;
        padding: 12px 18px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .login-btn .btn:hover {
        transform: translateY(-1px);
    }

    .btn-outline {
        background: #fff !important;
        color: #2563eb !important;
        border: 1px solid #bfdbfe;
        text-decoration: none;
    }

    .btn-outline:hover {
        background: #eff6ff !important;
        color: #1d4ed8 !important;
    }

    .checkbox {
        margin-top: 12px;
    }

    .lost-pass {
        display: inline-block;
        margin-top: 10px;
        color: #2563eb;
        font-weight: 600;
    }

    @media (max-width: 991px) {
        .login-form {
            padding: 35px 24px;
        }
    }
</style>
@endpush