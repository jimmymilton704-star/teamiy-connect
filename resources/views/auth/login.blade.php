@extends('layouts.auth')
@section('content')
    <div class="login-form-wrap">
        <form class="login-form" action="{{ route('auth.login') }}" method="post" id="loginForm">
            @csrf
            <img class="logo" src="assets/logo.png" alt="Teamiy" style="filter:none">
            <h2>Welcome back</h2>
            <p class="sub">Sign in to your employee portal.</p>
            <label class="label">Work email</label>
            <input class="input" name="email" type="email">
            <div style="height:18px"></div>
            <label class="label">Password</label>
            <div class="password-wrapper">
                <input class="input" type="password" name="password" id="password">
                <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
            </div>
            <div class="login-row">
                <label class="checkrow"><input type="checkbox" checked=""
                        style="width:16px;height:16px;accent-color:var(--primary)">Remember me</label>
                <span class="link">Forgot password?</span>
            </div>
            <button type="submit" class="btn btn-primary btn-block"
                style="padding:14px;font-size:15px;box-shadow:0 8px 20px -8px rgba(5,125,176,.6)">Sign in</button>

        </form>
    </div>
@endsection
