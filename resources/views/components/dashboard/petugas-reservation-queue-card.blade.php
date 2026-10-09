<a href="{{ route('petugas.reservations.index') }}" style="
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    flex: 1; min-width: 271px; height: 145px; background: rgba(255, 255, 255, 0.7); border: 1px solid rgba(151, 71, 255, 0.7); border-radius: 8px;
    text-decoration: none; color: inherit; font-family: 'Sora', Helvetica, sans-serif;">
    <div style="display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#525151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <span style="color: #525151; font-size: 16px; font-weight: 700;">Reservasi Menunggu</span>
    </div>
    <span style="color: #000; font-size: 48px; font-weight: 700; margin-top: 10px;">{{ $pendingCount }}</span>
</a>