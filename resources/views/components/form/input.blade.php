@props([
    'name',
    'label',
    'type' => 'text',
    'value' => ''
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">
        {{ $label }}
    </label>

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->class([
            'form-control',
            'is-invalid' => $errors->has($name)
        ]) }}
    >

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
