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
        <a class="active">Réinitilisation de mot de passe</a>
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
            value="{{ old('email') }}" required autocomplete="email" autofocus
            placeholder="Veuillez entrer l'email du compte">
        @error('email')
            <div class="alert alert-danger bg-danger" role="alert">
                <strong>{{ $message }}</strong>
            </div>
        @enderror


        <div class="form-button">
            <button id="submit" type="submit" class="ibtn">Réinitialiser le mot de passe</button>


        </div>
    </form>
@endsection
