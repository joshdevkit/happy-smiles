@extends('admin.app')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid px-3">
        <div class="card">
            <div class="card-header">
                <h4 class="text-success">
                    Generate Insights Report
                </h4>
            </div>
            <div class="card-body">
                <form id="reportForm">
                    @csrf
                    @php
                        $year = date('Y');
                        $endingYear = $year + 10;
                    @endphp
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="year" class="form-label">Year</label>
                            <select name="year" id="year" class="form-control">
                                <option value="">Select Year</option>
                                @for ($i = $year; $i <= $endingYear; $i++)
                                    <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="month" class="form-label">Month</label>
                            <select name="month" id="month" class="form-control">
                                <option value="">Select Month</option>
                                <option value="1">January</option>
                                <option value="2">February</option>
                                <option value="3">March</option>
                                <option value="4">April</option>
                                <option value="5">May</option>
                                <option value="6">June</option>
                                <option value="7">July</option>
                                <option value="8">August</option>
                                <option value="9">September</option>
                                <option value="10">October</option>
                                <option value="11">November</option>
                                <option value="12">December</option>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-50">Generate</button>
                        </div>
                    </div>
                </form>

                <div class="mt-4">
                    <h4>Generated Report</h4>
                    <table id="reportTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Total Paid Appointments</th>
                                <th>Total Patient</th>
                                <th>Total Unattended Patient</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="3" class="text-center">No data generated yet.</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right">Total</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('#reportForm').on('submit', function(e) {
                e.preventDefault();

                let year = $('#year').val();
                let month = $('#month').val();

                $('#reportTable tbody').html(
                    '<tr><td colspan="6" class="text-center">Loading...</td></tr>');

                $.ajax({
                    url: "{{ route('generate_insights') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        year: year,
                        month: month
                    },
                    success: function(response) {
                        console.log(response);

                        if (response.totalSuccess !== undefined && response.totalUnattended !==
                            undefined) {
                            $('#reportTable tbody').html(`
                                <tr>
                                    <td>${response.totalAppointment}</td>
                                    <td>${response.totalSuccess}</td>
                                    <td>${response.totalUnattended}</td>
                                </tr>
                            `);

                            $('#reportTable tfoot td:last').text(response.totalSuccess +
                                response.totalUnattended + response.totalAppointment);
                        } else {
                            $('#reportTable tbody').html(`
                <tr>
                    <td colspan="2" class="text-center text-warning">No data found for the selected month.</td>
                </tr>
            `);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        $('#reportTable tbody').html(`
            <tr>
                <td colspan="2" class="text-center text-danger">An error occurred. Please try again.</td>
            </tr>
        `);
                    }
                });

            });
        });
    </script>
@endsection
