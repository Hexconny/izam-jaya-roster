{{-- Form pemesanan produk yang sedang habis. Langsung dibuka ke WhatsApp. --}}
<section class="request" id="request-roster">
    <div class="wrap request__grid">
        <div>
            <p class="eyebrow">Produk sedang habis?</p>
            <h2>Tetap Bisa Pesan untuk Nanti</h2>
            <p>Produk yang kamu inginkan sedang habis? Kamu tetap bisa mengajukan pemesanan terlebih dahulu melalui WhatsApp. Admin akan menginformasikan ketersediaan produk dan perkiraan waktu pemesanannya.</p>
        </div>
        <form class="request__form" data-request-form data-wa="{{ \App\Models\Contact::whatsapp() }}">
            <label>Nama
                <input type="text" name="nama" required maxlength="60" placeholder="Nama kamu">
            </label>
            <label>Nama produk / ukuran / jumlah yang diinginkan
                <textarea name="detail" rows="4" required maxlength="500" placeholder="Contoh: roster beton motif minimalis, ukuran 20 x 20 cm, sebanyak 50 pcs"></textarea>
            </label>
            <button class="btn btn--primary" type="submit">Ajukan Pesanan via WhatsApp</button>
        </form>
    </div>
</section>