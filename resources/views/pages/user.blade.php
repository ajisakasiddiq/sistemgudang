@extends('layouts.app')
@section('title')
    Data User
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
          <div class="table-responsive m-5 p-0">
            <button class="btn btn-success mb-3" onclick="openAddModal()">Add Data</button>
            <table id="users" class="table table-striped" style="width:100%">
              <thead>
                  <tr>
                      <th>No</th>
                      <th>ID</th>
                      <th>Email</th>
                      <th>Name</th>
                      <th>Action</th>
                  </tr>
              </thead>
              <tbody></tbody>
          </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

  {{-- add modal --}}
  <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Tambah Data</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="dataForm">
            <div class="mb-3">
              <label for="inputName" class="form-label">Name</label>
              <input type="text" class="form-control" id="inputName" required>
            </div>
            <div class="mb-3">
              <label for="inputEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="inputEmail" required>
            </div>
            <div id="additionalFields"></div>
            {{-- <small>password default (12345678)</small> --}}
            <!-- Tambahkan input lainnya sesuai kebutuhan -->
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
        </div>
      </div>
    </div>
  </div>
  
@endsection
@push('addon-script')
<script type="text/javascript">
  $(document).ready(function() {

    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    if (!token) {
        // Pengguna sudah login, redirect ke halaman utama atau dashboard
        window.location.href = '/Login';
    }
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Authorization': 'Bearer ' + token,
      }
    });

    $('#users').DataTable({
      "ajax": {
        "url": "api/users",
        "dataSrc": "data"
      },
      "columns": [
        { 
          "data": null, 
          "render": function (data, type, row, meta) {
            return meta.row + 1; 
          }
        },
        { "data": "id" },          
        { "data": "email" },       
        { "data": "name" },       
        {
          "data": null,
          "render": function(data, type, row) {
            return `
              <button class="btn btn-warning btn-sm" onclick="openEditModal(${row.id})">Edit</button>
              <button class="btn btn-danger btn-sm" onclick="deleteRow(${row.id})">Delete</button>`;
          }
        }
      ]
    });
  });

  function openAddModal() {
    $('#modalLabel').text('Tambah Data');
    $('#dataForm')[0].reset(); // Reset form
    $('#saveChanges').attr('onclick', 'saveData()');
    $('#additionalFields').empty();
    $('#additionalFields').append(`
        <input type="hidden" class="form-control" value="12345678" id="inputPassword">
        <small>Default Password (12345678)</small>
    `);
    $('#dataModal').modal('show');
  }


  function openEditModal(id) {
    $('#modalLabel').text('Edit Data');
    $('#dataForm')[0].reset(); // Reset form
    $('#additionalFields').empty();
    // Ambil data berdasarkan ID dan isi form (contoh menggunakan AJAX)
    $.ajax({
      url: `api/users/${id}`,
      method: 'GET',
      success: function(data) {
        $('#inputName').val(data.name);
        $('#inputEmail').val(data.email);
        // Isi input lainnya sesuai dengan data
      }
    });

    $('#saveChanges').attr('onclick', `updateData(${id})`); // Atur fungsi untuk update data
    $('#dataModal').modal('show');
  }

  function saveData() {
    const data = {
      name: $('#inputName').val(),
      email: $('#inputEmail').val(),
      password: $('#inputPassword').val(),
      // Ambil data input lainnya
    };

    $.ajax({
      url: 'api/users',
      method: 'POST',
      data: JSON.stringify(data),
      contentType: 'application/json',
      success: function(response) {
        $('#dataModal').modal('hide');
        $('#users').DataTable().ajax.reload(); // Reload DataTables untuk menampilkan data terbaru
      },
      error: function(xhr) {
        console.error('Error:', xhr.responseText);
      }
    });
  }

  function updateData(id) {
    const data = {
      name: $('#inputName').val(),
      email: $('#inputEmail').val(),
      // Ambil data input lainnya
    };

    $.ajax({
      url: `api/users/${id}`,
      method: 'PUT',
      data: JSON.stringify(data),
      contentType: 'application/json',
      success: function(response) {
        $('#dataModal').modal('hide');
        $('#users').DataTable().ajax.reload(); // Reload DataTables untuk menampilkan data terbaru
      },
      error: function(xhr) {
        console.error('Error:', xhr.responseText);
      }
    });
  }
  function deleteRow(id) {
    if (confirm('Are you sure you want to delete this item?')) {
      $.ajax({
        url: `api/users/${id}`,
        method: 'DELETE',
        success: function(response) {
          $('#users').DataTable().ajax.reload(); // Reload DataTables untuk menampilkan data terbaru
          alert('Data deleted successfully');
        },
        error: function(xhr) {
          console.error('Error:', xhr.responseText);
          alert('Failed to delete data');
        }
      });
    }
  }
</script>

@endpush