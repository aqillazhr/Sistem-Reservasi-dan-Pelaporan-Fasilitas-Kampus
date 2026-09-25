<style>
    /* ── Design tokens (Figma palette) ── */
    .rsv {
        --ink:    #260f45;
        --brand:  #501e91;
        --mid:    #bd93f8;
        --light:  #d5bbfb;
        --soft:   #fbf7ff;
        --line:   #d5bbfb;
        --free:   #c8ffc8;
        --busy:   #ff9c9c;
        --wait:   #ffe08a;
        --past:   #e5e7eb;

        width: 100%;
        color: var(--ink);
        font-family: 'Sora', Helvetica, sans-serif;
    }

    /* ── Typography ── */
    .rsv h1 {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 40px;
        color: var(--brand);
        margin: 0 0 6px;
        letter-spacing: 0;
        line-height: normal;
    }
    .rsv h2 {
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 22px;
        color: var(--brand);
        margin: 0 0 12px;
        line-height: normal;
    }
    .rsv .sub {
        margin: 0 0 24px;
        color: #5b4a75;
        font-size: 16px;
    }
    .rsv .sub a { color: var(--brand); font-weight: 600; }
    .rsv .muted { color: #7a6497; font-size: 14px; }

    /* ── Cards ── */
    .rsv .card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 22px 24px;
    }
    .rsv a.card { display: block; color: inherit; }
    .rsv a.card:hover,
    .rsv a.card:focus-visible {
        border-color: var(--brand);
        outline: 2px solid var(--brand);
        outline-offset: 2px;
    }

    /* Header card — lavender bg (seperti .rectangle di Figma) */
    .rsv .card-header {
        background-color: var(--light);
        border: none;
        border-radius: 8px;
        padding: 22px 24px;
        margin-bottom: 20px;
    }

    /* ── Buttons ── */
    .rsv .btn {
        display: inline-block;
        padding: 9px 20px;
        border: 0;
        border-radius: 5px;
        background: var(--light);
        color: #fff;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
        line-height: normal;
        transition: background .15s;
    }
    .rsv .btn:hover { background: var(--mid); }
    .rsv .btn:focus-visible { outline: 2px solid var(--ink); outline-offset: 2px; }
    .rsv .btn.primary { background: var(--brand); }
    .rsv .btn.primary:hover { background: #3d1670; }
    .rsv .btn.ghost { background: var(--soft); color: var(--ink); border: 1px solid var(--line); }
    .rsv .btn.ghost:hover { background: var(--light); }
    .rsv .btn.danger { background: #b42318; }
    .rsv .btn[disabled],
    .rsv .btn:disabled { background: #d1d5db; color: #6b7280; cursor: not-allowed; }

    /* ── Form controls ── */
    .rsv label {
        display: block;
        margin: 12px 0 5px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 14px;
        color: var(--ink);
    }
    .rsv input,
    .rsv select,
    .rsv textarea {
        width: 100%;
        padding: 9px 12px;
        border: 2px solid var(--line);
        border-radius: 8px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 300;
        font-size: 15px;
        background: #fff;
        color: var(--ink);
        transition: border-color .15s;
    }
    .rsv input:focus,
    .rsv select:focus,
    .rsv textarea:focus {
        outline: none;
        border-color: var(--brand);
    }
    .rsv .row { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
    .rsv .row > * { flex: 1 1 140px; }
    .rsv .err { color: #b42318; font-size: 13px; margin-top: 4px; }
    .rsv .alert { background: #fee4e2; border: 1px solid #fda29b; color: #7a271a; padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; }

    /* ── Status badges ── */
    .rsv .badge {
        display: inline-block;
        padding: 3px 14px;
        border-radius: 999px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 600;
        font-size: 13px;
    }
    .rsv .badge.pending   { background: var(--wait); color: #5a3e00; }
    .rsv .badge.approved  { background: var(--free); color: #1a5c1a; }
    .rsv .badge.selesai   { background: #e0f2fe; color: #0c4a6e; }
    .rsv .badge.rejected,
    .rsv .badge.cancelled { background: #f3d1d1; color: #7a271a; }

    /* ── Status tabs ── */
    .rsv .tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .rsv .tabs a {
        padding: 6px 18px;
        border-radius: 999px;
        background: var(--light);
        color: #fff;
        font-family: 'Sora', Helvetica, sans-serif;
        font-weight: 700;
        font-size: 15px;
        text-decoration: none;
        opacity: 0.65;
        transition: opacity .15s;
    }
    .rsv .tabs a:hover { opacity: 0.85; }
    .rsv .tabs a.on { background: var(--brand); opacity: 1; }

    /* ── List & item ── */
    .rsv .list  { display: grid; gap: 12px; }
    .rsv .item  { display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; align-items: center; }
    .rsv .grid2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }

    /* ── Slot board (kalender) ── */
    .rsv .board {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(108px, 1fr));
        gap: 6px;
        margin: 12px 0;
    }
    .rsv .slot {
        padding: 8px 4px;
        border: 0;
        border-radius: 6px;
        font-family: 'Sora', Helvetica, sans-serif;
        font-size: 13px;
        text-align: center;
        background: var(--free);
        color: var(--ink);
        cursor: pointer;
        transition: opacity .15s;
    }
    .rsv .slot:hover:not(:disabled) { opacity: 0.85; }
    .rsv .slot.pending  { background: var(--wait); cursor: not-allowed; }
    .rsv .slot.approved { background: var(--busy); cursor: not-allowed; }
    .rsv .slot.past     { background: var(--past); color: #6b7280; cursor: not-allowed; }
    .rsv .slot.pick     { outline: 3px solid var(--brand); }

    /* ── Legend ── */
    .rsv .legend { display: flex; gap: 16px; flex-wrap: wrap; font-size: 13px; }
    .rsv .legend i { display: inline-block; width: 12px; height: 12px; border-radius: 3px; margin-right: 4px; vertical-align: -1px; }

    /* ── Detail list (dl) ── */
    .rsv dl { display: grid; grid-template-columns: 160px 1fr; gap: 8px 16px; margin: 0; }
    .rsv dt { color: #7a6497; font-weight: 600; font-size: 14px; }
    .rsv dd { margin: 0; }

    /* ── Timeline ── */
    .rsv ol.tl { margin: 0; padding-left: 18px; }

    /* ── Dialog ── */
    .rsv dialog {
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 24px;
        max-width: 420px;
        color: var(--ink);
        font-family: 'Sora', Helvetica, sans-serif;
    }
    .rsv dialog::backdrop { background: rgba(38, 15, 69, .45); }

    /* ── Pagination ── */
    .rsv .pager { display: flex; justify-content: space-between; margin-top: 20px; }

    @media (max-width: 640px) {
        .rsv h1    { font-size: 28px; }
        .rsv dl    { grid-template-columns: 1fr; }
    }
</style>
