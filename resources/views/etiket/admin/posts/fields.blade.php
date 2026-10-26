@php
    $values = old('_form') === $prefix ? old() : [];
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label for="{{ $prefix }}_id_gate" class="form-label">Jalur / Gate</label>
        <select name="id_gate" id="{{ $prefix }}_id_gate" class="form-select" required>
            @foreach($gates as $gate)
            <option value="{{ $gate->id }}" @selected((string) ($values['id_gate'] ?? request('gate_id')) === (string) $gate->id)>{{ $gate->nama }} ({{ $gate->destinasi?->nama }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6"><label for="{{ $prefix }}_nama" class="form-label">Nama pos</label><input name="nama" id="{{ $prefix }}_nama" value="{{ $values['nama'] ?? '' }}" maxlength="255" class="form-control" required></div>
    <div class="col-md-6"><label for="{{ $prefix }}_urutan" class="form-label">Urutan dalam jalur</label><input type="number" name="urutan" id="{{ $prefix }}_urutan" value="{{ $values['urutan'] ?? '' }}" min="0" class="form-control" required></div>
    <div class="col-md-6"><label for="{{ $prefix }}_altitude" class="form-label">Ketinggian (mdpl)</label><input type="number" name="altitude" id="{{ $prefix }}_altitude" value="{{ $values['altitude'] ?? '' }}" min="0" max="2147483647" step="1" class="form-control"><small class="text-muted">Kosongkan jika belum diketahui.</small></div>
    <div class="col-md-6"><label for="{{ $prefix }}_water_source_status" class="form-label">Sumber mata air</label><select name="water_source_status" id="{{ $prefix }}_water_source_status" class="form-select" required>@foreach(['Belum diketahui', 'Tersedia', 'Tidak tersedia'] as $option)<option value="{{ $option }}" @selected(($values['water_source_status'] ?? 'Belum diketahui') === $option)>{{ $option }}</option>@endforeach</select></div>
    <div class="col-md-6"><label for="{{ $prefix }}_camping_status" class="form-label">Berkemah</label><select name="camping_status" id="{{ $prefix }}_camping_status" class="form-select" required>@foreach(['Belum diketahui', 'Boleh', 'Tidak boleh'] as $option)<option value="{{ $option }}" @selected(($values['camping_status'] ?? 'Belum diketahui') === $option)>{{ $option }}</option>@endforeach</select></div>
    <div class="col-md-6"><label for="{{ $prefix }}_distance_to_summit_m" class="form-label">Jarak ke puncak (m)</label><input type="number" name="distance_to_summit_m" id="{{ $prefix }}_distance_to_summit_m" value="{{ $values['distance_to_summit_m'] ?? '' }}" min="0" max="2147483647" step="1" class="form-control"><small class="text-muted">Jarak mengikuti jalur pendakian; kosongkan jika belum diketahui.</small></div>
    <div class="col-md-6"><label for="{{ $prefix }}_radius_meter" class="form-label">Radius checkpoint (m)</label><input type="number" name="radius_meter" id="{{ $prefix }}_radius_meter" value="{{ $values['radius_meter'] ?? 150 }}" min="50" max="500" class="form-control"></div>
    <div class="col-md-6"><label for="{{ $prefix }}_latitude" class="form-label">Latitude</label><input type="number" step="any" min="-90" max="90" name="latitude" id="{{ $prefix }}_latitude" value="{{ $values['latitude'] ?? '' }}" class="form-control" required></div>
    <div class="col-md-6"><label for="{{ $prefix }}_longitude" class="form-label">Longitude</label><input type="number" step="any" min="-180" max="180" name="longitude" id="{{ $prefix }}_longitude" value="{{ $values['longitude'] ?? '' }}" class="form-control" required></div>
    <div class="col-12"><label for="{{ $prefix }}_detail" class="form-label">Detail</label><textarea name="detail" id="{{ $prefix }}_detail" maxlength="1000" class="form-control" rows="2">{{ $values['detail'] ?? '' }}</textarea></div>
    <div class="col-12">
        <label for="{{ $prefix }}_image" class="form-label">Gambar pos</label>
        <input type="file" name="image" id="{{ $prefix }}_image" accept="image/jpeg,image/png,image/webp" class="form-control" aria-describedby="{{ $prefix }}_image_help">
        <small id="{{ $prefix }}_image_help" class="text-muted">Satu gambar per pos. JPG, PNG, atau WebP, maksimal 5 MB. Gambar baru menggantikan gambar sebelumnya dan dipakai untuk naik maupun turun. Jika penyimpanan gagal, pilih ulang file.</small>
        <div class="border rounded bg-light p-2 mt-2 text-center">
            <img id="{{ $prefix }}_image_preview" alt="Preview gambar pos" class="img-fluid d-none" style="max-height:240px;object-fit:contain">
            <span id="{{ $prefix }}_image_empty" class="text-muted">Belum ada gambar.</span>
        </div>
    </div>
    @if($prefix === 'edit')
    <div class="col-md-6"><label for="edit_status" class="form-label">Status</label><select name="status" id="edit_status" class="form-select"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div>
    @endif
</div>
