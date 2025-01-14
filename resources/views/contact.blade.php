@extends('layouts.app')

@section('content')
    <div class="container py-5 mt-5">
        <div class="card">

            <div class="row g-0">

                <!-- Left Side: Contact Form -->
                <div class="col-md-6 p-4">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <h3>Contact Us</h3>
                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-success">Send Message</button>
                        </div>
                    </form>
                </div>

                <div class="col-md-6">
                    <img src="{{ asset('client/logo.jpg') }}" class="img-fluid" alt="Contact Image">
                </div>
            </div>
        </div>
    </div>
@endsection
