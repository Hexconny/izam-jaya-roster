{{-- Dipakai create & edit. Baris varian dibuat dinamis oleh public/js/admin.js --}}
@php
    $rows = old('variants', $product->exists
        ? $product->variants->map(fn ($v) => [
            'id' => $v->id, 'size' => $v->size, 'motif' => $v->motif,
            'price' => (int) $v->price, 'stock' => $v->stock, 'is_available' => $v->is_available,
        ])->all()
        : [['size' => '', 'motif' => '', 'price' => '', 'stock' => 0, 'is_available' => true]]);
@endphp

<div class="row">
    <label>Nama produk
        <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}" required maxlength="100">
    </label>
    <label>Kategori
        <select name="category_id" required>
            <option value="">Pilih kategori</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->category_name }}</option>
            @endforeach
        </select>
    </label>
</div>

<label>Deskripsi
    <textarea name="description" rows="4" maxlength="3000">{{ old('description', $product->description) }}</textarea>
</label>

<label>Ketersediaan produk
    <select name="availability">
        <option value="tersedia" @selected(old('availability', $product->availability) === 'tersedia')>Tersedia</option>
        <option value="habis" @selected(old('availability', $product->availability) === 'habis')>Habis</option>
    </select>
</label>

<fieldset>
    <legend>Varian (ukuran, motif, harga)</legend>
    <p class="muted">Kosongkan "motif" atau "ukuran" yang tidak dipakai. Harga di katalog memakai varian termurah. Stok 0 membuat varian otomatis tampil habis. Perubahan stok tercatat di riwayat.</p>

    <div class="variants-admin" data-variants>
        @foreach ($rows as $i => $row)
            @include('admin.products._variant-row', ['i' => $i, 'row' => $row])
        @endforeach
    </div>

    <template data-variant-template>
        @include('admin.products._variant-row', ['i' => '__INDEX__', 'row' => ['size' => '', 'motif' => '', 'price' => '', 'stock' => 0, 'is_available' => true]])
    </template>

    <button type="button" class="btn btn--small" data-add-variant>+ Tambah varian</button>
</fieldset>

<fieldset>
    <legend>Foto produk</legend>
    <input type="file" name="images[]" accept="image/*" multiple>
    <small class="muted">JPG/PNG, maksimal 2 MB per foto.</small>

    @if ($product->exists && $product->images->isNotEmpty())
        <div class="photos">
            @foreach ($product->images as $img)
                <label class="photo">
                    <img src="{{ asset('storage/'.$img->image) }}" alt="">
                    <span><input type="checkbox" name="remove_images[]" value="{{ $img->id }}"> Hapus</span>
                </label>
            @endforeach
        </div>
    @endif
</fieldset>
