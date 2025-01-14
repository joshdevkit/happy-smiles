@extends('client.app')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-left">
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item active">Appointments</li>
                        <li class="breadcrumb-item ">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid px-3">
        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                <table class="table" id="dataTable">
                    <thead class="bg-black text-white">
                        <tr>
                            <th>ID</th>
                            <th>Patient Name</th>
                            <th>Service</th>
                            <th>Appointment Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointment as $schedule)
                            <tr>
                                <td>{{ $schedule->id }}</td>
                                <td>{{ trim("{$schedule->user->first_name} {$schedule->user->middle_name} {$schedule->user->last_name}") }}
                                </td>
                                <td>{{ $schedule->service->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($schedule->schedule->date_added)->format('M/d/Y') }}</td>
                                <td>{{ $schedule->status }}</td>
                                <td>
                                    @php
                                        $scheduledDate = \Carbon\Carbon::parse($schedule->schedule->date_added);
                                        $threeDaysBefore = $scheduledDate->copy()->subDays(3);
                                        $twoDaysBefore = $scheduledDate->copy()->subDays(2);
                                        $today = \Carbon\Carbon::today();
                                    @endphp

                                    @if ($today->greaterThanOrEqualTo($threeDaysBefore) && $today->lessThanOrEqualTo($scheduledDate))
                                        <button type="button" class="btn btn-sm btn-primary resched" data-toggle="modal"
                                            data-target="#rescheduleModal" data-id="{{ $schedule->id }}"
                                            data-service="{{ $schedule->service->name }}">
                                            Re-schedule
                                        </button>
                                    @endif

                                    @if ($today->lessThanOrEqualTo($twoDaysBefore) && $schedule->status != 'Cancelled')
                                        <button type="button" class="btn btn-sm btn-danger cancel-btn"
                                            data-id="{{ $schedule->id }}">
                                            <i class="fas fa-times-circle"></i> Cancel Appointment
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Reschedule Modal -->
    <div class="modal fade" id="rescheduleModal" tabindex="-1" role="dialog" aria-labelledby="rescheduleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rescheduleModalLabel">Re-schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="rescheduleForm" action="{{ route('reschedule') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="schedule_id" id="scheduleId">
                        <input type="hidden" name="new_date_id" id="new_date_id">
                        <input type="hidden" name="selectedScheduleId" id="selectedScheduleId">
                        <div class="form-group">
                            <label for="service_name">Service</label>
                            <input readonly type="text" name="service_name" id="service_name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="date_to_change">Select Avaiable Date</label>
                            <input type="date" name="date_to_change" id="date_to_change" class="form-control">
                            <div class="feedback"></div>
                            <p class="link"></p>
                        </div>

                        <div class="form-group d-none" id="startTimeDataform">
                            <label for="startTimeData">Start Time</label>
                            <input type="text" name="startTimeData" id="startTimeData" class="form-control validate">
                            <div class="invalid-feedback">Choose a Start Time</div>
                        </div>
                        <div class="form-group d-none" id="endTimeDataform">
                            <label for="endTimeData">End Time</label>
                            <input type="text" name="endTimeData" id="endTimeData" class="form-control validate">
                            <div class="invalid-feedback">Choose a End Time</div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {

            var today = new Date();
            var day = ("0" + today.getDate()).slice(-2);
            var month = ("0" + (today.getMonth() + 1)).slice(-2);
            var year = today.getFullYear();
            var formattedDate = year + "-" + month + "-" + day;

            $("#date_to_change").attr("min", formattedDate);
            $(document).on('click', '.resched', function() {
                var schedID = $(this).data('id')
                var service = $(this).data('service')
                $('#service_name').val(service)
                $('#scheduleId').val(schedID)
            })

            $('#date_to_change').on('change', function() {
                var selectedDate = $(this).val()
                $.ajax({
                    url: '{{ route('check-dates') }}',
                    type: 'GET',
                    data: {
                        date: selectedDate
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.data.length === 0) {
                            $("#startTimeDataform").addClass('d-none');
                            $('#endTimeDataform').addClass('d-none');
                            $('.link').addClass('d-none').removeClass('d-none')
                            $('#date_to_change').removeClass('is-valid').addClass('is-invalid');

                            $('.feedback').html(
                                    '<div><p>Date is not available. Please choose another date.</p></div>'
                                )
                                .removeClass('text-success')
                                .addClass('text-danger');
                            $('#submitBtn').prop('disabled', true)
                            $('.link').html(
                                '<a href="{{ route('client.dashboard') }}">Check your Dashboard here...</a>'
                            )
                        } else {
                            $('.link').addClass('d-none')
                            $("#startTimeDataform").removeClass('d-none');
                            $('#endTimeDataform').removeClass('d-none');

                            $('#date_to_change').removeClass('is-invalid').addClass('is-valid');

                            $('.feedback').html(
                                    '<div><p>This Date is eligible for re-scheduling.</p></div>'
                                )
                                .removeClass('text-danger')
                                .addClass('text-success');
                        }

                        let scheduleStartTime = response.data[0].start_time;
                        let scheduleEndTime = response.data[0].end_time;
                        let responseDataid = response.data[0].id
                        $('#selectedScheduleId').val(responseDataid)
                        $('#new_date_id').val(response.data[0].id)
                        if (!$('#startTimeData').data('flatpickr')) {
                            $('#startTimeData').flatpickr({
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: "H:i",
                                time_24hr: false,
                                minTime: scheduleStartTime,
                                maxTime: scheduleEndTime,
                            });
                        }

                        if (!$('#endTimeData').data('flatpickr')) {
                            $('#endTimeData').flatpickr({
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: "H:i",
                                time_24hr: false,
                                minTime: scheduleStartTime,
                                maxTime: scheduleEndTime,
                            });
                        }
                    }
                })
            })

            $('#startTimeData').on('change', function() {
                var selectedSchedID = $('#selectedScheduleId').val()
                console.log(selectedSchedID);
                var startTime = $(this).val()
                var endTimeData = $(this).val()
                var newStartTime = startTime + ":00"
                $.ajax({
                    url: '{{ route('check-status') }}',
                    type: 'POST',
                    data: {
                        schedId: selectedSchedID,
                        startTime: newStartTime,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            $('#submitBtn').prop('disabled', true)
                            $('#startTimeData').addClass(
                                'is-invalid');
                            $('.invalid-feedback').html('<p class="text-danger">' + response
                                .message +
                                '</p>');
                        } else {
                            $('#submitBtn').prop('disabled', false)
                            $('#startTimeData').removeClass(
                                'is-invalid');
                            $('.invalid-feedback').html('<p class="text-success">' + response
                                .message +
                                '</p>');
                        }
                    }
                })
            })

            $(document).on('submit', '#rescheduleForm', function(event) {
                event.preventDefault();

                let isValid = true;
                $('.validate').each(function() {
                    if ($(this).val() === "") {
                        $(this).addClass('is-invalid');
                        $(this).removeClass('is-valid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).addClass('is-valid');
                    }
                });

                if (isValid) {
                    this.submit();
                }
            });

            $(document).on('click', '.cancel-btn', function() {
                const scheduleId = $(this).data('id'); // Get the schedule ID

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this cancellation!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, cancel it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('cancel-appointment', ['']) }}/' + scheduleId,
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire(
                                        'Cancelled!',
                                        'Your appointment has been cancelled.',
                                        'success'
                                    ).then(() => {
                                        location
                                            .reload(); // Reload the page after successful cancellation
                                    });
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Failed to cancel the appointment.',
                                        'error'
                                    );
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            })
        });
    </script>
@endsection
