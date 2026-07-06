<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Package Name</label>
        <input type="text" name="package_name" class="form-control" value="{{ old('package_name', $servicePackage->package_name ?? '') }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="Active" @selected(old('status', $servicePackage->status ?? 'Active') === 'Active')>Active</option>
            <option value="Inactive" @selected(old('status', $servicePackage->status ?? '') === 'Inactive')>Inactive</option>
        </select>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4">{{ old('description', $servicePackage->description ?? '') }}</textarea>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Estimated Duration (minutes)</label>
        <input type="number" name="estimated_duration" class="form-control" value="{{ old('estimated_duration', $servicePackage->estimated_duration ?? '') }}" min="15" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Price (RM)</label>
        <input type="number" name="price" class="form-control" step="0.01" value="{{ old('price', $servicePackage->price ?? '') }}" min="0" required>
    </div>
</div>
