<div class="vrow" data-variant-row>
    <input type="hidden" name="variants[{{ $i }}][id]" value="{{ $row['id'] ?? '' }}">
    <label>Ukuran
        <input type="text" name="variants[{{ $i }}][size]" value="{{ $row['size'] ?? '' }}" placeholder="20 x 20 cm" maxlength="60">
    </label>
    <label>Motif
        <input type="text" name="variants[{{ $i }}][motif]" value="{{ $row['motif'] ?? '' }}" placeholder="Opsional" maxlength="60">
    </label>
    <label>Harga (Rp)
        <input type="number" name="variants[{{ $i }}][price]" value="{{ $row['price'] ?? '' }}" min="0" step="100" required>
    </label>
    <label>Stok
        <input type="number" name="variants[{{ $i }}][stock]" value="{{ $row['stock'] ?? 0 }}" min="0" step="1" required>
    </label>
    <label class="check">
        <input type="hidden" name="variants[{{ $i }}][is_available]" value="0">
        <input type="checkbox" name="variants[{{ $i }}][is_available]" value="1" @checked(! empty($row['is_available']))> Tersedia
    </label>
    <button type="button" class="danger" data-remove-variant aria-label="Hapus varian">Hapus</button>
</div>
