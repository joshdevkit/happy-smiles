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
                    Generate Report
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
                                <th>Service Name</th>
                                <th>Client Name</th>
                                <th>Payment Date</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">No data generated yet.</td>
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
                e.preventDefault(); // Prevent default form submission

                let year = $('#year').val();
                let month = $('#month').val();

                // Show loading message
                $('#reportTable tbody').html(
                    '<tr><td colspan="6" class="text-center">Loading...</td></tr>');

                $.ajax({
                    url: "{{ route('reports.generate') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        year: year,
                        month: month
                    },
                    success: function(response) {
                        console.log(response);

                        let rows = '';
                        let totalRevenue = 0; // Variable to hold the total revenue

                        if (response.schedules.length > 0) {
                            $.each(response.schedules, function(index, scheduleData) {
                                let serviceName = scheduleData.schedule.service.name ??
                                    'N/A';
                                let clientName = scheduleData.schedule.is_guest == 1 ?
                                    (scheduleData.schedule.guest_name ?? 'Guest') :
                                    (scheduleData.schedule.user?.first_name + " " +
                                        scheduleData.schedule.user?.middle_name + " " +
                                        scheduleData.schedule.user?.last_name ?? 'N/A');
                                let paymentDate = scheduleData.payment_date ?
                                    new Date(scheduleData.payment_date)
                                    .toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'long',
                                        day: '2-digit'
                                    }) :
                                    'N/A';

                                let paymentMethod = scheduleData.payment_method ??
                                    'N/A';
                                let addedFee = parseFloat(scheduleData.added_fee ??
                                    '0.00'); // Parse as float to accumulate
                                totalRevenue += addedFee; // Add to total revenue

                                rows += `
                                <tr>
                                    <td>${serviceName}</td>
                                    <td>${clientName}</td>
                                    <td>${paymentDate}</td>
                                    <td>₱ ${addedFee.toFixed(2)}</td>
                                </tr>
                                `;
                            });
                        } else {
                            rows =
                                '<tr><td colspan="4" class="text-center">No records found.</td></tr>';
                        }

                        $('#reportTable tbody').html(rows);
                        let totalRow = `
                        <tr>
                            <td colspan="3" class="text-right">Total</td>
                            <td>₱ ${totalRevenue.toFixed(2)}</td> <!-- Display total revenue -->
                        </tr>
                        `;
                        $('#reportTable tfoot').html(totalRow);
                    },

                    error: function(xhr) {
                        console.error(xhr.responseText);
                        $('#reportTable tbody').html(
                            '<tr><td colspan="7" class="text-center text-danger">An error occurred. Please try again.</td></tr>'
                        );
                    }
                });
            });
        });
    </script>
@endsection
