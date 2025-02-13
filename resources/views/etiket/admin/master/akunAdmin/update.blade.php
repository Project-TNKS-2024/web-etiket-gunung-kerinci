@extends('etiket.admin.template.index')

@section('main')
<a href="{{ route('admins.akun.index') }}" class="btn btn-secondary mb-3">
   <i class="ti ti-arrow-left"></i> Kembali
</a>

<div class="card">
   <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><b>{{ isset($admin) ? 'Edit Admin' : 'Tambah Admin' }}</b></h5>
   </div>
   <div class="card-body">
      <form action="{{ isset($admin) ? route('admins.akun.update', $admin->id) : route('admins.akun.store') }}" method="POST">
         @csrf
         <div class="row mb-3">
            <div class="col">
               <label class="form-label">Email:</label>
               <input type="email" name="email" class="form-control"
                  value="{{ isset($admin) ? $admin->email : old('email') }}"
                  {{ isset($admin) ? 'readonly' : 'required' }}>
            </div>
            <div class="col">
               <label class="form-label">Role:</label>
               <select name="role" class="form-control" required>
                  @foreach ($roles as $role)
                  <option value="{{ $role->name }}" {{ isset($admin) && $admin->hasRole($role->name) ? 'selected' : '' }}>
                     {{ ucfirst($role->name) }}
                  </option>
                  @endforeach
               </select>
            </div>
         </div>

         <!-- Destinasi -->
         <div class="mb-3">
            <label class="form-label">Destinasi yang Dikelola:</label>
            <div class="row">
               @foreach ($destinasis as $destination)
               <div class="col-md-4">
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox" name="destination_ids[]"
                        value="{{ $destination->id }}" id="dest_{{ $destination->id }}"
                        {{ isset($admin->destinasis) && optional($admin->destinasis)->pluck('id')->contains($destination->id) ? 'checked' : '' }}
                        <label class="form-check-label" for="dest_{{ $destination->id }}">
                     {{ $destination->nama }}
                     </label>
                  </div>
               </div>
               @endforeach
            </div>
         </div>

         <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-{{ isset($admin) ? 'success' : 'primary' }} px-4 py-2">
               <i class="ti ti-save"></i> {{ isset($admin) ? 'Update' : 'Tambah' }}
            </button>
         </div>
      </form>
   </div>
</div>
@endsection