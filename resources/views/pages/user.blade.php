@extends('layouts.app')
@section('title')
    Dashboard | Data User 
@endsection

@section('content')
<div class="container-fluid py-4">
<div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Data User</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table" id="users-table">
              <thead>
                <tr>
                  <th>id</th>
                  <th>name</th>
                  <th>email</th>
                  <th>ID</th>
                </tr>
              </thead>
          <tbody>

          </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection
@push('addon-script')
<script type="text/javascript">
     $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/api/users',
                type: 'GET',
                dataSrc: function(json) {
                    console.log(json); // Debugging: Lihat data JSON yang dikembalikan
                    return json.data;
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'created_at', name: 'created_at' },
                { data: 'updated_at', name: 'updated_at' }
            ]
        });
    });
    </script>
@endpush