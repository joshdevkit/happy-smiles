@extends('admin.app')

@section('header')
    <style>
        .active_list {
            color: green !important;

        }

        .schedul_span {
            margin-left: 10rem;
            padding: 10px;
            margin-right: 3px;
        }

        .hover-pointer {
            cursor: pointer;
        }
    </style>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-left">
                        <li class="breadcrumb-item"><a>Dashboard</a></li>
                        <li class="breadcrumb-item ">About Clients</li>
                        <li class="breadcrumb-item active_list">About Appointment</li>
                        <li class="breadcrumb-item">Profile</li>
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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="calendar"></div>


            </div>
        </div>
        <div class="card mt-5">
            <div class="card-header">
                <h4>Pending Appointments</h4>
            </div>
            <div class="card-body">
                <table class="table" id="dataTable">
                    <thead class="bg-black text-white">
                        <tr>
                            <th>ID</th>
                            <th>Patient Name</th>
                            <th>Service</th>
                            <th>Start Date/Time</th>
                            <th>End Date/Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $data)
                            @foreach ($data->record as $row)
                                <tr>
                                    <td>{{ $row->id }}</td>
                                    <td>
                                        @if ($row->user_id)
                                            {{ $row->user->first_name }} {{ $row->user->middle_name }}
                                            {{ $row->user->last_name }} {{ $row->user->suffix ?? '' }}
                                        @elseif (!$row->user_id && $row->is_guest)
                                            {{ $row->guest_name }}
                                        @else
                                            {{ $row->walk_in_name }}
                                        @endif
                                    </td>

                                    <td>{{ $row->service->name ?? 'N/A' }}</td>
                                    <td>{{ date('F d, Y', strtotime($data->date_added)) }}
                                        {{ date('h:i A', strtotime($row->start_time)) }}
                                    </td>
                                    <td>
                                        {{ date('F d, Y', strtotime($data->date_added)) }}
                                        {{ date('h:i A', strtotime($row->end_time)) }}
                                    </td>
                                    <td>
                                        @if ($row->status == 'Pending')
                                            <!-- Confirm Button -->
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                                data-target="#confirmModal{{ $row->id }}">
                                                Confirm
                                            </button>

                                            <!-- Decline Button -->
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#declineModal{{ $row->id }}">
                                                Decline
                                            </button>

                                            <!-- Confirm Modal -->
                                            <div class="modal fade" id="confirmModal{{ $row->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="confirmModalLabel{{ $row->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title"
                                                                id="confirmModalLabel{{ $row->id }}">Confirm
                                                                Appointment</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to <strong>confirm</strong> this
                                                            appointment?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Cancel</button>
                                                            <form action="{{ route('confirm', $row->id) }}" method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="btn btn-success">Confirm</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Decline Modal -->
                                            <div class="modal fade" id="declineModal{{ $row->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="declineModalLabel{{ $row->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title"
                                                                id="declineModalLabel{{ $row->id }}">Decline
                                                                Appointment</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <form action="{{ route('decline', $row->id) }}" method="POST"
                                                            onsubmit="return validateDecline({{ $row->id }})">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to <strong>decline</strong> this
                                                                    appointment?</p>

                                                                <div class="form-group">
                                                                    <label for="remarks{{ $row->id }}">Remarks <span
                                                                            class="text-danger">*</span></label>
                                                                    <textarea name="remarks" id="remarks{{ $row->id }}" class="form-control" rows="3"
                                                                        placeholder="Enter remarks..." required></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit"
                                                                    class="btn btn-danger">Decline</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge badge-info">{{ $row->status }}</span>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <script>
        function validateDecline(id) {
            const remarks = document.getElementById(`remarks${id}`).value.trim();
            if (remarks === "") {
                alert("Please provide remarks before declining.");
                return false;
            }
            return true;
        }
    </script>
@endsection
