@php
    $selectedPackageId = old('service_package_id', $booking->service_package_id ?? request('service_package_id'));
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Customer Name</label>
        <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Vehicle</label>
        <select name="vehicle_id" class="form-select" required>
            <option value="">-- Select vehicle --</option>
            @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $booking->vehicle_id ?? '') == $vehicle->id)>
                    {{ $vehicle->plate_number }} - {{ $vehicle->brand }} {{ $vehicle->model }}
                </option>
            @endforeach
        </select>
        @if($vehicles->isEmpty())
            <div class="small text-danger mt-1">Please add a vehicle first.</div>
        @endif
    </div>

    <div class="col-12 mb-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-3">
            <div>
                <label class="form-label mb-1">Service Package</label>
                <p class="text-muted small mb-0">Click a service card or use the dropdown below. The selected service will be submitted as the booking package.</p>
            </div>
            <span class="section-badge align-self-lg-start"><i class="bi bi-hand-index-thumb"></i> Select one service</span>
        </div>

        <div class="service-selection-grid mb-3">
            @forelse($packages as $package)
                <div class="service-option-card {{ (string) $selectedPackageId === (string) $package->id ? 'selected' : '' }}"
                     data-service-card
                     data-service-group="booking-form"
                     data-package-id="{{ $package->id }}"
                     data-target-select="service_package_id"
                     tabindex="0"
                     role="button"
                     aria-label="Select {{ $package->package_name }}">
                    <input type="radio" aria-hidden="true" @checked((string) $selectedPackageId === (string) $package->id)>
                    <span class="service-icon mb-3"><i class="bi bi-tools"></i></span>
                    <h3 class="h6 service-option-title mb-2">{{ $package->package_name }}</h3>
                    <p class="service-option-desc mb-0">{{ $package->description ?: 'Service package for your vehicle maintenance appointment.' }}</p>
                    <div class="service-option-meta">
                        <span class="duration-pill"><i class="bi bi-clock me-1"></i>{{ $package->estimated_duration }} min</span>
                        <span class="price-pill">RM {{ number_format($package->price, 2) }}</span>
                    </div>
                </div>
            @empty
                <div class="alert alert-info mb-0">No active service packages are available.</div>
            @endforelse
        </div>

        <label class="form-label small text-muted">Selected Service Package</label>
        <select id="service_package_id" name="service_package_id" class="form-select service-package-select" required>
            <option value="">-- Select service package --</option>
            @foreach($packages as $package)
                <option value="{{ $package->id }}" @selected((string) $selectedPackageId === (string) $package->id)>
                    {{ $package->package_name }} - RM {{ number_format($package->price, 2) }}
                </option>
            @endforeach
        </select>
        <div class="selected-service-helper alert alert-success mt-2 mb-0 py-2" data-selected-helper="service_package_id"></div>
    </div>

    <div class="col-md-3 mb-3">
        <label class="form-label">Preferred Date</label>
        <input type="date" name="preferred_date" class="form-control" value="{{ old('preferred_date', isset($booking) ? $booking->preferred_date->format('Y-m-d') : '') }}" required>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Preferred Time</label>
        <input type="time" name="preferred_time" class="form-control" value="{{ old('preferred_time', isset($booking) ? substr($booking->preferred_time,0,5) : '') }}" required>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label">Additional Notes</label>
        <textarea name="additional_notes" class="form-control" rows="4" placeholder="Describe any issue or request">{{ old('additional_notes', $booking->additional_notes ?? '') }}</textarea>
    </div>
</div>
