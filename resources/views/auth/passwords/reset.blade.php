@extends('auth.layout')

@section('content')
    <div class="website-logo-inside">
        <a href="index.html">
            <div class="logo">
                {{-- <img class="logo-size" src="{{asset('assets/images/logo-light.svg') }}" alt=""> --}}
            </div>
        </a>
    </div>
    <div class="page-links">
        <a class="active">Validation nouveau mot de passe</a>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
            value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Adresse email">

        @error('email')
            <div class="alert alert-danger bg-danger" role="alert">
                <strong>{{ $message }}</strong>
            </div>
        @enderror

        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
            required autocomplete="new-password" placeholder="Entrer un nouveau mot de passe">

        @error('password')
            <div class="alert alert-danger bg-danger" role="alert">
                <strong>{{ $message }}</strong>
            </div>
        @enderror
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
            autocomplete="new-password" placeholder="Confirmer le nouveau mot de passe">

        <div class="form-button">
            <button id="submit" type="submit" class="ibtn">Valider les informations</button>


        </div>
    </form>
@endsection
