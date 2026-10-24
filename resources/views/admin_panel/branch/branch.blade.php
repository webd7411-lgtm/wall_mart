@extends('admin_panel.layout.app')
@section('content')
    <div class="main-content">
        <div class="main-content-inner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3>Branch</h3>
                            @can('branches.create')
                                <button type="button" class="btn btn-primary" id="reset-form">Create</button>
                            @endcan
                        </div>
                        <div class="border mt-1 shadow rounded" style="background-color: white;">
                            <div class="col-lg-12 m-auto">
                                <div class="table-responsive mt-5 mb-5">
                                    <table id="default-datatable" class="table">
                                        <thead class="text-center">
                                            <tr>
                                                <th class="text-center">Id</th>
                                                <th class="text-center">Name</th>
                                                <th class="text-center">Type</th>
                                                <th class="text-center">Address</th>
                                                <th class="text-center">Number</th>
                                                <th class="text-center">Email</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @foreach ($branches as $branch)
                                                <tr>
                                                    <td class="id">{{ $branch->id }}</td>
                                                    <td class="name">{{ $branch->name }}</td>
                                                    <td class="type">
                                                        <span class="badge {{ ($branch->type ?? 'mart') === 'restaurant' ? 'bg-warning text-dark' : 'bg-info' }}">
                                                            {{ ($branch->type ?? 'mart') === 'restaurant' ? 'Food / Restaurant' : 'Mart' }}
                                                        </span>
                                                    </td>
                                                    <td class="address">{{ $branch->address }}</td>
                                                    <td class="number">{{ $branch->number }}</td>
                                                    <td class="email">{{ $branch->user->email ?? '-' }}</td>
                                                    <td>
                                                        @can('branches.edit')
                                                            <button class="btn btn-primary btn-sm edit-btn"
                                                                data-id="{{ $branch->id }}"
                                                                data-name="{{ $branch->name }}"
                                                                data-type="{{ $branch->type ?? 'mart' }}"
                                                                data-address="{{ $branch->address }}"
                                                                data-number="{{ $branch->number }}"
                                                                data-user_id="{{ $branch->user_id ?? '' }}">
                                                                Edit
                                                            </button>
                                                        @endcan
                                                        @can('branches.delete')
                                                            <a href="{{ route('branch.delete', $branch->id) }}"
                                                                class="btn btn-danger btn-sm delete-btn"
                                                                data-url="{{ route('branch.delete', $branch->id) }}"
                                                                data-msg="Are you sure you want to delete this Branch"
                                                                data-method="DELETE" onclick="confirmedBox(this, event)">
                                                                Delete
                                                            </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Branch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="myform" action="{{ route('branch.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="edit_id" id="id" />
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="Enter branch name" required />
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Branch Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="mart">Mart</option>
                                <option value="restaurant">Food Branch / Restaurant</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" id="address" placeholder="Enter branch address" required />
                        </div>
                        <div class="mb-3">
                            <label for="number" class="form-label">Number</label>
                            <input type="text" name="number" class="form-control" id="number" placeholder="Enter phone number" />
                        </div>
                        <div class="mb-3">
                            <label for="user_id" class="form-label">User Email (Optional)</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">Select Email (Optional)</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->email }}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-primary save-btn" value="Submit">
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- DataTable CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>

    <!-- DataTable JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('assets/js/mycode.js') }}"></script>
    <script>
        $(document).on('submit', '.myform', function(e) {
            e.preventDefault();
            var formdata = new FormData(this);
            url = $(this).attr('action');
            method = $(this).attr('method');
            $(this).find(':submit').attr('disabled', true);
            myAjax(url, formdata, method);
        });

        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var type = $(this).data('type') || 'mart';
            var address = $(this).data('address');
            var number = $(this).data('number');
            var userId = $(this).data('user_id');

            $('#exampleModalLabel').text('Edit Branch');
            $('.save-btn').val('Update');

            $('#id').val(id);
            $('#name').val(name);
            $('#type').val(type);
            $('#address').val(address);
            $('#number').val(number);
            $('#user_id').val(userId ? userId : '');

            $("#exampleModal").modal("show");
        });

        $(document).on('click', '#reset-form', function() {
            $('#exampleModalLabel').text('Add Branch');
            $('.save-btn').val('Submit');

            $('#id').val('');
            $('#name').val('');
            $('#type').val('mart');
            $('#address').val('');
            $('#number').val('');
            $('#user_id').val('');

            $("#exampleModal").modal("show");
        });

        function confirmedBox(element, event) {
            event.preventDefault(); // Stop immediate redirect

            const message = element.getAttribute('data-msg') || 'Are you sure?';
            const url = element.getAttribute('href');

            Swal.fire({
                title: 'Confirm Deletion',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('#default-datatable').DataTable({
                "pageLength": 10,
                "lengthMenu": [5, 10, 25, 50, 100],
                "order": [
                    [0, 'desc']
                ],
                "language": {
                    "search": "Search Branch:",
                    "lengthMenu": "Show _MENU_ entries"
                }
            });
        });
    </script>
@endsection
