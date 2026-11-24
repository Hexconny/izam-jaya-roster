{{-- Form permintaan roster khusus. Tidak disimpan di database, langsung dibuka ke WhatsApp. --}}
<section class="request" id="request-roster">
    <div class="wrap request__grid">
        <div>
            <p class="eyebrow">Motif tidak ketemu?</p>
            <h2>Request roster sesuai desain kamu</h2>
            <p>Ceritakan motif, ukuran, dan jumlahnya. Admin akan balas lewat WhatsApp dengan kemungkinan buatnya dan estimasi harga setelah roster jadi.</p>
        </div>
        <form class="request__form" data-request-form data-wa="{{ \App\Models\Contact::whatsapp() }}">
            <label>Nama
                <input type="text" name="nama" required maxlength="60" placeholder="Nama kamu">
            </label>
            <label>Motif / ukuran / jumlah yang diinginkan
                <textarea name="detail" rows="4" required maxlength="500" placeholder="Contoh: motif belah ketupat, 20 x 20 cm, sekitar 150 pcs"></textarea>
            </label>
            <button class="btn btn--primary" type="submit">Kirim request ke WhatsApp</button>
        </form>
    </div>
</section>
