<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#16A34A', dark: '#166534' },
                        accent: { DEFAULT: '#EAB308', light: '#FEF9C3' },
                        base: '#F8FAFC',
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #F8FAFC; }
        .badge { display: inline-flex; align-items: center; padding: .25rem .5rem; border-radius: 9999px; font-size: .7rem; font-weight: 700; white-space: nowrap; }
        .nav-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 1.25rem; height: 1.25rem; padding: 0 .3rem; margin-left: auto; border-radius: 9999px; font-size: .65rem; font-weight: 800; }
        .nav-badge--orders { background: #EAB308; color: #166534; }
        .nav-badge--verify { background: #FEF9C3; color: #166534; }
        .admin-container { width: 100%; max-width: 1400px; margin: 0 auto; }

        /* ---------- Sidebar dan navigasi ---------- */
        .admin-sidebar {
            width: 16rem;
            flex-shrink: 0;
            display: none;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .admin-nav { flex: 1; overflow-y: auto; }

        .nav-link {
            display: block;
            padding: .5rem .75rem;
            border-radius: .375rem;
            color: #FFFFFF;
            text-decoration: none;
            font-size: .875rem;
        }
        .nav-link:hover { background: rgba(255, 255, 255, .1); }
        .nav-link--active { background: rgba(255, 255, 255, .1); font-weight: 600; }

        .nav-item { display: flex; align-items: center; gap: .5rem; }
        .nav-icon { flex-shrink: 0; width: 1.25rem; text-align: center; }

        .nav-heading {
            padding: 1rem .75rem .25rem;
            color: rgba(255, 255, 255, .5);
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        /* Tombol menu dan drawer hanya untuk layar kecil. */
        .admin-mobile-toggle { display: inline-flex; }
        .admin-mobile-drawer { display: none; }
        .admin-mobile-drawer.is-open { display: block; }
        .admin-drawer-backdrop { display: none; }
        .admin-drawer-backdrop.is-open { display: block; }

        @media (min-width: 1024px) {
            .admin-sidebar { display: flex; }
            .admin-mobile-toggle,
            .admin-mobile-drawer,
            .admin-drawer-backdrop { display: none !important; }
        }

        /* Tabel dan form agar mudah dibaca di layar kecil. */
        @media (max-width: 640px) {
            .admin-container table { font-size: .8rem; }
            .admin-container .px-4, .admin-container .px-5 { padding-left: .75rem; padding-right: .75rem; }
            .admin-container .py-3, .admin-container .py-4 { padding-top: .5rem; padding-bottom: .5rem; }
            .admin-header h1 { font-size: 1rem; }
        }
    </style>
</head>
<body class="min-h-screen flex text-black">

    <!-- Sidebar (tetap terlihat pada layar lebar) -->
    <aside class="admin-sidebar bg-primary-dark text-white flex-col">
        <div class="h-16 flex items-center gap-2 px-5 font-bold text-lg border-b border-white/10 shrink-0">
            <span class="bg-accent text-primary-dark rounded-full w-9 h-9 flex items-center justify-center">🌾</span>
            SI Benih &amp; Bibit
        </div>

        <nav class="admin-nav px-3 py-4 space-y-1 text-sm" aria-label="Navigasi petugas">
            @include('admin.partials.nav-links', [
                'linkClass' => 'nav-link',
                'activeClass' => 'nav-link--active',
            ])
        </nav>
    </aside>

    <!-- Latar gelap saat drawer navigasi dibuka -->
    <div id="admin-drawer-backdrop" class="admin-drawer-backdrop fixed inset-0 bg-black/40 z-30 lg:hidden" aria-hidden="true"></div>
    <div class="flex-1 flex flex-col min-w-0">
        <header class="admin-header min-h-16 bg-white shadow-sm flex items-center justify-between px-4 lg:px-6 py-3">
            <div class="flex items-center gap-3 min-w-0">
                <button
                    id="admin-menu-toggle"
                    type="button"
                    class="admin-mobile-toggle inline-flex items-center justify-center w-10 h-10 rounded-md border border-gray-200 text-primary-dark"
                    aria-label="Buka menu navigasi"
                    aria-expanded="false"
                    aria-controls="admin-mobile-drawer"
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                        <path d="M4 7h16M4 12h16M4 17h16"></path>
                    </svg>
                </button>
                <h1 class="font-semibold text-base lg:text-lg text-primary-dark truncate">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-2 lg:gap-3">
                @if (($pendingOrdersCount ?? 0) > 0 || ($pendingPaymentsCount ?? 0) > 0)
                    <div class="hidden md:flex items-center gap-2 text-xs">
                        @if (($pendingOrdersCount ?? 0) > 0)
                            <a href="{{ route('admin.orders.index', ['status' => 'dipesan']) }}" class="bg-accent-light text-primary-dark px-2.5 py-1 rounded-full font-bold hover:brightness-95">
                                {{ $pendingOrdersCount }} pesanan baru
                            </a>
                        @endif
                        @if (($pendingPaymentsCount ?? 0) > 0)
                            <a href="{{ route('admin.paymentProofs.index') }}" class="bg-accent text-white px-2.5 py-1 rounded-full font-bold hover:brightness-95">
                                {{ $pendingPaymentsCount }} verifikasi
                            </a>
                        @endif
                    </div>
                @endif

                <span class="badge bg-accent-light text-primary-dark">{{ auth()->user()->roleDisplayLabel() }}</span>
                <span class="text-sm text-gray-600 hidden lg:inline">{{ auth()->user()->name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="bg-primary text-white text-sm px-3 py-1.5 rounded-md hover:bg-primary-dark">Keluar</button>
                </form>
            </div>
        </header>

        {{-- Drawer navigasi untuk layar kecil --}}
        <div id="admin-mobile-drawer" class="admin-mobile-drawer lg:hidden" aria-hidden="true">
            <div class="fixed inset-y-0 left-0 w-72 max-w-[85vw] bg-primary-dark text-white flex flex-col z-40">
                <div class="h-16 flex items-center justify-between gap-2 px-5 font-bold text-lg border-b border-white/10 shrink-0">
                    <span class="flex items-center gap-2">
                        <span class="bg-accent text-primary-dark rounded-full w-9 h-9 flex items-center justify-center">🌾</span>
                        SI Benih &amp; Bibit
                    </span>
                    <button type="button" id="admin-drawer-close" class="text-white/70 hover:text-white text-xl leading-none px-2" aria-label="Tutup menu navigasi">✕</button>
                </div>

                <nav class="admin-nav px-3 py-4 space-y-1 text-sm" aria-label="Navigasi petugas">
                    @include('admin.partials.nav-links', [
                        'linkClass' => 'nav-link',
                        'activeClass' => 'nav-link--active',
                    ])
                </nav>
            </div>
        </div>
        <main class="flex-1 p-4 lg:p-6">
            <div class="admin-container">
                <div class="space-y-3 mb-4 empty:mb-0">
                    @if (session('success'))
                        <x-admin.alert type="success" :message="session('success')"></x-admin.alert>
                    @endif

                    @if (session('error'))
                        <x-admin.alert type="error" :message="session('error')"></x-admin.alert>
                    @endif

                    @if (session('warning'))
                        <x-admin.alert type="warning" :message="session('warning')"></x-admin.alert>
                    @endif

                    @if ($errors->any())
                        <x-admin.alert
                            type="error"
                            title="Periksa kembali data berikut"
                            :errors="$errors->all()"
                        ></x-admin.alert>
                    @endif
                </div>

                @yield('content')
            </div>
        </main>
    </div>

{{-- Dialog konfirmasi tunggal untuk seluruh aksi yang memerlukan konfirmasi. --}}
<dialog id="admin-confirm-dialog" class="rounded-xl p-0 w-full max-w-md">
    <form method="dialog" class="p-6">
        <h3 id="admin-confirm-title" class="text-lg font-bold text-primary-dark mb-2"></h3>
        <p id="admin-confirm-message" class="text-sm text-gray-600 mb-5"></p>
        <div class="flex justify-end gap-2">
            <button value="cancel" class="px-4 py-2 rounded-md border border-gray-300 text-sm hover:bg-gray-50">Batal</button>
            <button value="confirm" id="admin-confirm-submit" class="px-4 py-2 rounded-md bg-red-600 text-white text-sm font-semibold hover:bg-red-700"></button>
        </div>
    </form>
</dialog>

<script>
    (function () {
        var dialog = document.getElementById('admin-confirm-dialog');
        if (!dialog) return;

        var titleEl = document.getElementById('admin-confirm-title');
        var messageEl = document.getElementById('admin-confirm-message');
        var submitEl = document.getElementById('admin-confirm-submit');

        document.addEventListener('submit', function (event) {
            var form = event.target;

            if (!form.dataset || !form.dataset.confirmTitle) return;

            event.preventDefault();

            titleEl.textContent = form.dataset.confirmTitle;
            messageEl.textContent = form.dataset.confirmMessage;
            submitEl.textContent = form.dataset.confirmButton;

            var isDanger = form.dataset.confirmTone === 'danger';
            submitEl.className = 'px-4 py-2 rounded-md text-white text-sm font-semibold '
                + (isDanger ? 'bg-red-600 hover:bg-red-700' : 'bg-primary hover:bg-primary-dark');

            dialog.returnValue = 'cancel';

            if (typeof dialog.showModal === 'function') {
                dialog.showModal();
            } else if (window.confirm(form.dataset.confirmMessage)) {
                form.submit();
            }

            // Dialog method="dialog" menutup sendiri; submit manual setelah konfirmasi.
            dialog.addEventListener('close', function handler() {
                dialog.removeEventListener('close', handler);
                if (dialog.returnValue === 'confirm') {
                    form.submit();
                }
            }, { once: true });
        });
    })();
</script>

<script>
    (function () {
        var toggle = document.getElementById('admin-menu-toggle');
        var drawer = document.getElementById('admin-mobile-drawer');
        var backdrop = document.getElementById('admin-drawer-backdrop');
        var closeButton = document.getElementById('admin-drawer-close');

        if (!toggle || !drawer) return;

        function setOpen(isOpen) {
            drawer.classList.toggle('is-open', isOpen);
            drawer.setAttribute('aria-hidden', String(!isOpen));
            toggle.setAttribute('aria-expanded', String(isOpen));
            toggle.setAttribute('aria-label', isOpen ? 'Tutup menu navigasi' : 'Buka menu navigasi');
            document.body.style.overflow = isOpen ? 'hidden' : '';

            if (backdrop) {
                backdrop.classList.toggle('is-open', isOpen);
                backdrop.setAttribute('aria-hidden', String(!isOpen));
            }
        }

        toggle.addEventListener('click', function () {
            setOpen(!drawer.classList.contains('is-open'));
        });

        if (closeButton) {
            closeButton.addEventListener('click', function () {
                setOpen(false);
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', function () {
                setOpen(false);
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && drawer.classList.contains('is-open')) {
                setOpen(false);
                toggle.focus();
            }
        });
    })();
</script>
</body>
</html>