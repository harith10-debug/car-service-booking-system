<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Plate Number</label>
        <input type="text" name="plate_number" class="form-control" value="{{ old('plate_number', $vehicle->plate_number ?? '') }}" placeholder="ABC1234" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Brand</label>
        <input type="text" name="brand" class="form-control" value="{{ old('brand', $vehicle->brand ?? '') }}" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Model</label>
        <input type="text" name="model" class="form-control" value="{{ old('model', $vehicle->model ?? '') }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Year</label>
        <input type="number" name="year" class="form-control" value="{{ old('year', $vehicle->year ?? '') }}" min="1980" max="{{ date('Y') + 1 }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Color</label>
        <input type="text" name="color" class="form-control" value="{{ old('color', $vehicle->color ?? '') }}" required>
    </div>
</div>
