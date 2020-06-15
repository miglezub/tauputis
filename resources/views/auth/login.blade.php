@extends('layouts.app')

@section('title', 'Prisijungimas')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Prisijungimas') }}</div>

                <div class="card-body piggy piggy-login">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Vartotojo vardas arba el. paštas') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus 
                                    minlength="4" maxlength="255"
                                    oninvalid="validation_login('email', 4)" oninput="validation_login('email', 8)">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Slaptažodis') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="current-password" minlength="8" maxlength="255"
                                    oninvalid="validation_login('password', 8)" oninput="validation_login('password', 8)">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Prisiminti') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4 py-0">
                                @if (Route::has('password.request'))
                                    <a class="btn text-main-teal pl-0" href="{{ route('password.request') }}">
                                        {{ __('Pamiršote slaptažodį?') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn bg-main-teal">
                                    {{ __('Prisijungti') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function validation_login(field_id, min_symbols) {
            var txt = '';
            if (document.getElementById(field_id).validity.valueMissing)
                var txt = "Prašome užpildyti šį lauką";
            else if (document.getElementById(field_id).validity.tooLong)
                var txt = "Laukas būti sudarytas iš mažiau nei 255 simbolių";
            else if(document.getElementById(field_id).validity.tooShort)
                var txt = "Laukas turi būti sudarytas iš mažiausiai " + min_symbols + " simbolių";
            document.getElementById(field_id).setCustomValidity(txt);
        }
    </script>
@endpush
