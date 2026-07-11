<div class="row">
    <div class="col-md-8 mb-3"><label class="form-label">Workshop Name</label><input name="name" class="form-control" value="{{ old('name', $workshop->name ?? '') }}" required></div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="Active" @selected(old('status', $workshop->status ?? 'Active') === 'Active')>Active</option>
            <option value="Inactive" @selected(old('status', $workshop->status ?? '') === 'Inactive')>Inactive</option>
        </select>
    </div>
    <div class="col-12 mb-3"><label class="form-label">Address</label><input name="address" class="form-control" value="{{ old('address', $workshop->address ?? '') }}" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">City</label><input name="city" class="form-control" value="{{ old('city', $workshop->city ?? '') }}" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">State</label><input name="state" class="form-control" value="{{ old('state', $workshop->state ?? 'Selangor') }}" required></div>
    <div class="col-md-4 mb-3"><label class="form-label">Postcode</label><input name="postcode" class="form-control" value="{{ old('postcode', $workshop->postcode ?? '') }}"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone', $workshop->phone ?? '') }}"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $workshop->email ?? '') }}"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Latitude</label><input type="number" step="0.0000001" name="latitude" class="form-control" value="{{ old('latitude', $workshop->latitude ?? '') }}"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Longitude</label><input type="number" step="0.0000001" name="longitude" class="form-control" value="{{ old('longitude', $workshop->longitude ?? '') }}"></div>
    <div class="col-12 mb-3"><label class="form-label">Services</label><textarea name="services" class="form-control" rows="3" placeholder="Oil service, tyres, aircond, diagnostic">{{ old('services', $workshop->services ?? '') }}</textarea></div>
    <div class="col-md-6 mb-3"><label class="form-label">Opening Hours</label><input name="opening_hours" class="form-control" value="{{ old('opening_hours', $workshop->opening_hours ?? '') }}" placeholder="Mon-Sat, 9.00 AM - 6.00 PM"></div>
    <div class="col-md-6 mb-3"><label class="form-label">Google Maps URL</label><input type="url" name="maps_url" class="form-control" value="{{ old('maps_url', $workshop->maps_url ?? '') }}"></div>
</div>
