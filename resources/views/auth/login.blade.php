@extends('auth.layout')
@section('page-title', 'Se connecter')

@section('content')

    <div class="website-logo-inside">
        <a href="index.html">
            <div class="logo">
                {{-- <img class="logo-size" src="{{asset('assets/images/logo-light.svg') }}" alt=""> --}}
            </div>
        </a>
    </div>
    <div class="page-links">
        <a class="active">Se connecter</a>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
            value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Entrer votre email">
        @error('email')
        <div class="alert alert-danger bg-danger" role="alert">
                <strong>{{ $message }}</strong>
        </div>
        @enderror

        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
            required autocomplete="current-password" placeholder="Votre mot de passe">

        @error('password')
        <div class="alert alert-danger bg-danger" role="alert">
            <strong>{{ $message }}</strong>
    </div>
        @enderror
        <div class="form-button">
            <button id="submit" type="submit" class="ibtn">Se connecter</button>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                    Mot de passe oublié ?
                </a>
            @endif

        </div>
    </form>

@endsection
