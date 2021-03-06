@extends('layouts.app')

@section('title', 'Registracija')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Registracija') }}</div>
                <!--img src="{{ asset('storage/piggy_bank.png') }}" alt=""-->

                <div class="card-body piggy">
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Vartotojo vardas') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                    minlength="4" maxlength="255"
                                    oninvalid="validation('name', '4')" oninput="validation('password', '8')">

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('El. paštas') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ old('email') }}" required autocomplete="email"
                                oninvalid="validation('email', '8')" oninput="validation('email', '8')"
                                minlength="8" maxlength="255">

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
                                name="password" required autocomplete="new-password" minlength="8" maxlength="255"
                                oninvalid="validation('password', '8')" oninput="validation('password', '8')">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Pakartokite slaptažodį') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" 
                                name="password_confirmation" required autocomplete="new-password" minlength="8" maxlength="255"
                                oninvalid="validation('password-confirm', '8')" oninput="validation('password', '8')">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn bg-main-teal">
                                    {{ __('Registruotis') }}
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
        function validation(field_id, min_symbols) {
            var txt = '';
            var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if (document.getElementById(field_id).validity.valueMissing)
                var txt = "Prašome užpildyti šį lauką";
            else if (document.getElementById(field_id).validity.tooLong)
                var txt = "Laukas būti sudarytas iš mažiau nei 255 simbolių";
            else if(document.getElementById(field_id).validity.tooShort)
                var txt = "Laukas turi būti sudarytas iš mažiausiai " + min_symbols + " simbolių";
            else if(field_id == 'email' && !(document.getElementById(field_id).value.match(mailformat)))
                var txt = "Netinkamas el. pašto formatas";
            if(txt != '')
                document.getElementById(field_id).setCustomValidity(txt);
            else
                document.getElementById(field_id).setCustomValidity('');
        }
    </script>
@endpush