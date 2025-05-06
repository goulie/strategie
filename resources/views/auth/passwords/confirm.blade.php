@extends('auth.layout')
@section('page-title', 'Gestion des motes de passe')

@section('content')
    <div class="website-logo-inside">
        <a href="index.html">
            <div class="logo">
                {{-- <img class="logo-size" src="{{asset('assets/images/logo-light.svg') }}" alt=""> --}}
            </div>
        </a>
    </div>
    <div class="page-links">
        <a class="active">Confirmation de mot de passe</a>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
            required autocomplete="current-password">



        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
            required autocomplete="current-password" placeholder="Votre mot de passe">

        @error('password')
            <div class="alert alert-danger" role="alert">
                <div class="alert alert-danger bg-danger" role="alert">
                    <strong>{{ $message }}</strong>
                </div>
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


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Confirm Password') }}</div>

                    <div class="card-body">
                        {{ __('Please confirm your password before continuing.') }}

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Confirm Password') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
