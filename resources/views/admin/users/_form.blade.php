<div class="row g-3">
    <div class="col-12">
        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control form-control-sm" required
               value="{{ old('name') }}" placeholder="Full Name">
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control form-control-sm" required
               value="{{ old('email') }}" placeholder="user@example.com">
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Username</label>
        <input type="text" name="username" class="form-control form-control-sm"
               value="{{ old('username') }}" placeholder="optional">
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
        <select name="role_id" class="form-select form-select-sm" required>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Status</label>
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active_cb"
                   value="1" checked>
            <label class="form-check-label" for="is_active_cb">Active</label>
        </div>
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">
            Password @if(isset($edit)) <small class="text-muted">(leave blank to keep)</small> @else <span class="text-danger">*</span> @endif
        </label>
        <input type="password" name="password" class="form-control form-control-sm"
               {{ isset($edit) ? '' : 'required' }} placeholder="Min 8 characters">
    </div>
    <div class="col-sm-6">
        <label class="form-label fw-semibold">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control form-control-sm"
               placeholder="Repeat password">
    </div>
</div>
