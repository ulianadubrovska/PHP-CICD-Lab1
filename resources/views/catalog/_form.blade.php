<div class="row">
    <div class="col-md-6">
        <x-form.input
            name="name"
            label="Назва товару"
            :value="$product->name ?? ''"
            required
        />
    </div>

    <div class="col-md-6">
        <x-form.input
            name="sku"
            label="Артикул (SKU)"
            :value="$product->sku ?? ''"
            required
        />
    </div>
</div>

<div class="mb-3">
    <label for="category_id" class="form-label">Категорія</label>

    <select
        name="category_id"
        id="category_id"
        class="form-select @error('category_id') is-invalid @enderror"
        required
    >
        <option value="">Оберіть категорію</option>

        @foreach ($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected(old('category_id', $product->category_id ?? '') == $category->id)
            >
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    @error('category_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Опис товару</label>

    <textarea
        name="description"
        id="description"
        rows="3"
        class="form-control @error('description') is-invalid @enderror"
    >{{ old('description', $product->description ?? '') }}</textarea>

    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row">
    <div class="col-md-6">
        <x-form.input
            name="price"
            label="Ціна (грн)"
            type="number"
            step="0.01"
            min="0"
            :value="$product->price ?? ''"
            required
        />
    </div>

    <div class="col-md-6">
        <x-form.input
            name="stock_quantity"
            label="Кількість на складі"
            type="number"
            min="0"
            :value="$product->stock_quantity ?? 0"
            required
        />
    </div>
</div>

<div class="form-check mb-4">
    <input type="hidden" name="is_active" value="0">

    <input
        class="form-check-input"
        type="checkbox"
        name="is_active"
        id="is_active"
        value="1"
        @checked(old('is_active', $product->is_active ?? true))
    >

    <label class="form-check-label" for="is_active">
        Товар активний
    </label>
</div>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $buttonText }}
    </button>

    <a href="{{ route('shop.catalog.index') }}"
       class="btn btn-outline-secondary">
        Скасувати
    </a>
</div>
