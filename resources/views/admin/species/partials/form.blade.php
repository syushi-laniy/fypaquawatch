<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Species Name</label>
        <input class="form-control" type="text" name="name" value="{{ old('name', $species?->name) }}"
               placeholder="Example: Guppy" required>
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="4"
                  placeholder="Care notes or species description">{{ old('description', $species?->description) }}</textarea>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Minimum pH</label>
        <input class="form-control" type="number" step="0.01" min="0" max="14" name="min_ph"
               value="{{ old('min_ph', $species?->min_ph) }}" required>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Maximum pH</label>
        <input class="form-control" type="number" step="0.01" min="0" max="14" name="max_ph"
               value="{{ old('max_ph', $species?->max_ph) }}" required>
    </div>

    <div class="col-12">
        <label class="form-label">Image Path</label>
        <input class="form-control" type="text" name="image_path" value="{{ old('image_path', $species?->image_path) }}"
               placeholder="Example: images/species/guppy.jpeg">
    </div>

    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="species-active"
                   {{ old('is_active', $species?->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="species-active">Active</label>
        </div>
    </div>
</div>
