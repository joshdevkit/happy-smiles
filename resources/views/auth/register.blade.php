@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center bg-white">
                        <h4 class="text-success fw-bold">{{ __('REGISTER') }}</h4>
                    </div>
                    <div class="card-body">
                        {{-- <form method="POST" action="{{ route('register') }}">
                            @csrf






                            <div class="d-flex justify-content-center">
                                Already have an account? &nbsp;<a href="{{ route('login') }}"> Login</a>
                            </div>
                        </form> --}}
                        <form method="POST" action="{{ route('register') }}" id="registerForm">
                            @csrf

                            <!-- Step 1 -->
                            <div class="step" id="step1">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label">First Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name"
                                            value="{{ old('first_name') }}" autofocus>
                                        @error('first_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="middle_name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="middle_name" name="middle_name"
                                            value="{{ old('middle_name') }}">
                                        @error('middle_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label">Last Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name"
                                            value="{{ old('last_name') }}">
                                        @error('last_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="suffix" class="form-label">Suffix</label>
                                        <input type="text" class="form-control optional" id="suffix" name="suffix"
                                            value="{{ old('suffix') }}">
                                        @error('suffix')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Other fields -->
                            </div>

                            <!-- Step 2 -->
                            <div class="step" id="step2" style="display:none;">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="age" class="form-label">Age</label>
                                        <input type="number" class="form-control" id="age" name="age"
                                            value="{{ old('age') }}">
                                        @error('age')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth"
                                            value="{{ old('date_of_birth') }}">
                                        @error('date_of_birth')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select" id="gender" name="gender">
                                            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select
                                                Gender
                                            </option>
                                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male
                                            </option>
                                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>
                                                Female
                                            </option>
                                            <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="occupation" class="form-label">Occupation</label>
                                        <input type="text" class="form-control" id="occupation" name="occupation"
                                            value="{{ old('occupation') }}">
                                        @error('occupation')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <label for="civil_status" class="form-label">Civil Status</label>
                                        <select id="civil_status" name="civil_status" class="form-control form-select">
                                            <option value="Male">
                                                Single</option>
                                            <option value="Female">Married
                                            </option>
                                            <option value="Divorced">Divorced
                                            </option>
                                            <option value="Widowed">Widowed
                                            </option>

                                        </select>
                                        @error('civil_status')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="cellphone_no" class="form-label">Cellphone No</label>
                                        <input type="number" class="form-control" id="cellphone_no" name="cellphone_no"
                                            value="{{ old('cellphone_no') }}">
                                        @error('cellphone_no')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <hr class="mt-4">

                                    <div class="mb-2">
                                        <label for="address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="{{ old('address') }}">
                                        @error('address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="step" id="step3" style="display:none;">


                                <div class="mb-2">
                                    <label for="email" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}">
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-2">
                                    <label for="password" class="form-label">Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password">
                                        <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                            <i class="bi bi-eye"></i> <!-- Eye icon for toggle -->
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-2">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation">
                                        <span class="input-group-text" id="togglePasswordConfirmation"
                                            style="cursor: pointer;">
                                            <i class="bi bi-eye"></i> <!-- Eye icon for toggle -->
                                        </span>
                                    </div>
                                </div>


                                {{-- <div class="d-flex justify-content-center mb-2 mt-3">
                                    <button type="submit" class="btn btn-success w-50">Register</button>
                                </div> --}}
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-between mt-3">
                                <button type="button" class="btn btn-secondary" id="prevBtn"
                                    style="display:none;">Previous</button>
                                <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                                <button type="submit" class="btn btn-success" id="submitBtn"
                                    style="display:none;">Complete Registration</button>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let currentStep = 1;

            function showStep(step) {
                $('.step').hide();
                $('#step' + step).show();
                $('#prevBtn').toggle(step > 1);
                $('#nextBtn').toggle(step < 3);
                $('#submitBtn').toggle(step === 3);
            }

            function validateStep(step) {
                let isValid = true;

                $('#step' + step + ' input, #step' + step + ' select').each(function() {
                    if ($(this).hasClass('form-control') && !$(this).hasClass('optional')) {
                        if (!$(this).val()) {
                            $(this).addClass('is-invalid');
                            isValid = false;
                            setTimeout(() => {
                                $(this).removeClass('is-invalid');
                            }, 1500);
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    }
                });

                return isValid;
            }

            $('#nextBtn').click(function() {
                if (validateStep(currentStep)) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            $('#prevBtn').click(function() {
                currentStep--;
                showStep(currentStep);
            });

            $('#togglePassword').click(function() {
                let passwordField = $('#password');
                let type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);

                let icon = type === 'password' ? 'bi-eye' : 'bi-eye-slash';
                $('#togglePassword i').removeClass('bi-eye bi-eye-slash').addClass(icon);
            });

            $('#togglePasswordConfirmation').click(function() {
                let confirmPasswordField = $('#password_confirmation');
                let type = confirmPasswordField.attr('type') === 'password' ? 'text' : 'password';
                confirmPasswordField.attr('type', type);

                let icon = type === 'password' ? 'bi-eye' : 'bi-eye-slash';
                $('#togglePasswordConfirmation i').removeClass('bi-eye bi-eye-slash').addClass(icon);
            });

            showStep(currentStep);
        });
    </script>
@endsection
