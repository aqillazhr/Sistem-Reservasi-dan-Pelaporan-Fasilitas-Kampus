{{--
    Opsi <option> untuk jam mulai ATAU jam selesai, tergantung $field
    ('start'/'end'). Sengaja SATU partial untuk keduanya (bukan ditulis dua
    kali) supaya aturan "hanya slot yang state-nya free yang boleh dipilih"
    tidak pernah bisa lupa ditulis di salah satunya saja.
--}}
<option value="">Pilih</option>
@foreach ($board as $b)
    @if ($b['state'] === 'free')
        <option value="{{ $b[$field] }}">{{ str_replace(':', '.', $b[$field]) }}</option>
    @endif
@endforeach
