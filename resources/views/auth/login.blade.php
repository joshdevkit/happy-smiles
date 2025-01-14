@extends('layouts.app')

@section('content')
    <div class="container py-5 mt-5">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="bg-white shadow rounded">
                    <div class="row">
                        <div class="col-md-6 ps-0 d-none d-md-block">
                            <div class="form-right h-100 bg-success text-white text-center">
                                <img class="w-100 h-100" src="{{ asset('client/logo.jpg') }}" alt="">
                            </div>
                        </div>
                        <div class="col-md-6 pe-0">
                            <div class="form-left h-100 py-5 px-5">
                                <form action="{{ route('login') }}" class="row g-4" method="POST">
                                    @csrf
                                    <div class="col-12">
                                        <label>Email<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="bi bi-person-fill"></i></div>
                                            <input type="text" class="form-control @error('email') is-invalid @enderror"
                                                name="email" placeholder="Enter Email Address"
                                                value="{{ old('email') }}">
                                        </div>
                                        @if ($errors->has('email'))
                                            <div class="text-danger">{{ $errors->first('email') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <label>Password<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" name="password"
                                                placeholder="Enter Password">
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-12">
                                        <a href="{{ route('password.request') }}" class="float-end text-primary">Forgot
                                            Password?</a>
                                    </div>
                                    <button type="submit" class="btn btn-success px-4 float-end mt-4 w-100">Login</button>
                                    <div class="col-12 text-center">
                                        <p>Not yet registered? <a href="{{ route('register') }}"> Register here</a></p>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection