@extends('admin.app')

@section('header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Inquiries</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Inquiries</li>
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
                <h2 class="text-success">
                    Inquiries
                </h2>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered" id="dataTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inquiries as $inquiry)
                            <tr>
                                <td>{{ $inquiry->id }}</td>
                                <td>{{ $inquiry->name }}</td>
                                <td>{{ $inquiry->email }}</td>
                                <td>{{ $inquiry->subject }}</td>
                                <td>
                                    {{ strlen($inquiry->message) > 20 ? substr($inquiry->message, 0, 20) . '...' : $inquiry->message }}
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-more-btn" data-name="{{ $inquiry->name }}"
                                        data-email="{{ $inquiry->email }}" data-subject="{{ $inquiry->subject }}"
                                        data-message="{{ $inquiry->message }}">
                                        <i class="fas fa-eye"></i> View More
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Inquiry Details Modal -->
    <div class="modal fade" id="viewMoreModal" tabindex="-1" role="dialog" aria-labelledby="viewMoreModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewMoreModalLabel">Inquiry Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="inquiryName">Full Name</label>
                                <input type="text" class="form-control" id="inquiryName" value="" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="inquiryEmail">Email Address</label>
                                <input type="email" class="form-control" id="inquiryEmail" value="" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inquirySubject">Subject</label>
                            <input type="text" class="form-control" id="inquirySubject" value="" readonly>
                        </div>
                        <div class="form-group">
                            <label for="inquiryMessage">Message</label>
                            <textarea class="form-control" id="inquiryMessage" rows="4" readonly></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            $('.view-more-btn').click(function() {
                var name = $(this).data('name');
                var email = $(this).data('email');
                var subject = $(this).data('subject');
                var message = $(this).data('message');

                $('#inquiryName').val(name);
                $('#inquiryEmail').val(email);
                $('#inquirySubject').val(subject);
                $('#inquiryMessage').val(message);

                $('#viewMoreModal').modal('show');
            });
        });
    </script>
@endsection
