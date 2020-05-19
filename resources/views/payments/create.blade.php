@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Mokėjimo pridėjimas') }}</div>

                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" action="/payments/store">
                        @csrf

                        <div class="form-group row">
                            <label for="type" class="col-md-4 col-form-label text-md-right">{{ __('Tipas') }}</label>

                            <div class="col-md-6 mt-2">
                                <input type="radio" name="type" id="type_expense" value="type_expense" checked
                                    onclick="$('#payment_type_div').collapse('show')"> Išlaidos</input>
                                <input type="radio" name="type" id="type_income" value="type_income" 
                                    onclick="$('#payment_type_div').collapse('hide')"> Įplaukos</input>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="caption" class="col-md-4 col-form-label text-md-right">{{ __('Pavadinimas') }}</label>

                            <div class="col-md-6">
                                <input id="caption" type="text" class="form-control @error('caption') is-invalid @enderror" 
                                name="caption" value="{{ old('caption') }}" autocomplete="caption" autofocus>

                                @error('caption')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-md-4 col-form-label text-md-right">{{ __('Aprašymas') }}</label>

                            <div class="col-md-6">
                                <textarea id="description" type="textarea" class="form-control @error('description') is-invalid @enderror" 
                                name="description" value="{{ old('description') }}" autocomplete="description"></textarea>

                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="date" class="col-md-4 col-form-label text-md-right">{{ __('Data') }}</label>

                            <div class="col-md-6">
                                <input id="date" type="date" class="form-control @error('date') is-invalid @enderror" 
                                name="date" required value="{{ now()->toDateString() }}" max="{{ now()->toDateString() }}"
                                oninvalid="this.setCustomValidity('Prašome užpildyti šį lauką')" oninput="this.setCustomValidity('')">

                                @error('date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="value" class="col-md-4 col-form-label text-md-right">{{ __('Suma') }}</label>

                            <div class="col-md-6">
                                <input id="value" type="number" step="0.01"
                                class="form-control @error('value') is-invalid @enderror" 
                                name="value" required value="{{ old('value') }}"
                                oninvalid="this.setCustomValidity('Prašome užpildyti šį lauką')" oninput="this.setCustomValidity('')">

                                @error('value')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row collapse show" id="payment_type_div">
                            <label for="payment_type" class="col-md-4 col-form-label text-md-right">{{ __('Kategorija') }}</label>

                            <div class="col-md-6">
                                <select id="payment_type" type="number" class="form-control" 
                                name="payment_type" required>
                                    @foreach($payment_types as $type)
                                        @if($type->id!=1)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="image" class= "col-md-4 col-form-label text-md-right">{{ __('Paveikslėlis') }}</label>

                            <div class="col-md-6">
                                <input type="file" class="@error('image') is-invalid @enderror" 
                                    id="image" name="image">
                                @error('image')
                                    <strong>{{ $errors->first('image') }}</strong>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Pridėti') }}
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
