<div class="row">
    <div class="col-md-8 mb-3"><label class="form-label">Plan Name</label><input name="plan_name" class="form-control" value="{{ old('plan_name', $subscriptionPlan->plan_name ?? '') }}" required></div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            <option value="Active" @selected(old('status', $subscriptionPlan->status ?? 'Active') === 'Active')>Active</option>
            <option value="Inactive" @selected(old('status', $subscriptionPlan->status ?? '') === 'Inactive')>Inactive</option>
        </select>
    </div>
    <div class="col-12 mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ old('description', $subscriptionPlan->description ?? '') }}</textarea></div>
    <div class="col-md-3 mb-3"><label class="form-label">Price (RM)</label><input type="number" step="0.01" min="0" name="monthly_price" class="form-control" value="{{ old('monthly_price', $subscriptionPlan->monthly_price ?? '') }}" required></div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Billing Cycle</label>
        <select name="billing_cycle" class="form-select" required>
            <option value="Monthly" @selected(old('billing_cycle', $subscriptionPlan->billing_cycle ?? 'Monthly') === 'Monthly')>Monthly</option>
            <option value="Yearly" @selected(old('billing_cycle', $subscriptionPlan->billing_cycle ?? '') === 'Yearly')>Yearly</option>
        </select>
    </div>
    <div class="col-md-3 mb-3"><label class="form-label">Discount %</label><input type="number" step="0.01" min="0" max="100" name="discount_percentage" class="form-control" value="{{ old('discount_percentage', $subscriptionPlan->discount_percentage ?? 0) }}" required></div>
    <div class="col-md-3 mb-3"><label class="form-label">Priority Level</label><input type="number" min="1" max="10" name="priority_level" class="form-control" value="{{ old('priority_level', $subscriptionPlan->priority_level ?? 1) }}" required></div>
    <div class="col-12 mb-3"><label class="form-label">Benefits</label><textarea name="benefits" class="form-control" rows="5" placeholder="One benefit per line">{{ old('benefits', $subscriptionPlan->benefits ?? '') }}</textarea></div>
</div>
