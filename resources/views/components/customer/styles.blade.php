<style>
    :root {
        /* Skala tipografi: dipakai semua aturan font-size di bawah. */
        --text-2xs: 11px;
        --text-xs: 12px;
        --text-sm: 13.5px;
        --text-md: 15px;
        --text-lg: 17px;
        --text-xl: 20px;
        --text-2xl: 27px;

        --ink: #000000;
        --ink-soft: rgba(0, 0, 0, .74);
        --muted: rgba(0, 0, 0, .58);
        --paper: #F8FAFC;
        --surface: #FFFFFF;
        --surface-muted: rgba(254, 249, 195, .55);
        --line: rgba(22, 101, 52, .16);
        --line-strong: rgba(22, 101, 52, .28);
        --forest-950: #166534;
        --forest-900: #166534;
        --forest-800: #16A34A;
        --forest-700: #166534;
        --forest-100: rgba(254, 249, 195, .72);
        --gold-700: #EAB308;
        --gold-500: #EAB308;
        --gold-300: #EAB308;
        --gold-100: #FEF9C3;
        --danger-700: #000000;
        --danger-100: #FEF9C3;
        --blue-700: #000000;
        --blue-100: rgba(0, 0, 0, .06);
        --shadow-sm: 0 8px 24px rgba(22, 101, 52, .06);
        --shadow-md: 0 18px 50px rgba(22, 101, 52, .10);
    }

    html { scroll-behavior: smooth; }

    body.customer-body {
        margin: 0;
        background: var(--paper);
        color: var(--ink);
        font-family: "Segoe UI", "ui-sans-serif", system-ui, sans-serif;
        font-size: var(--text-md);
        line-height: 1.6;
        -webkit-font-smoothing: antialiased;
    }

    a { color: inherit; }
    button, input, textarea, select { font: inherit; }
    button { cursor: pointer; }

    .customer-shell {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .customer-container {
        width: min(1180px, calc(100% - 2rem));
        margin-inline: auto;
    }

    .display-title,
    .hero__title,
    .detail-title,
    .profile-hero__name,
    .auth-card__title {
        font-family: "Segoe UI", "ui-sans-serif", system-ui, sans-serif;
        letter-spacing: -.035em;
    }

    /* Header */
    .site-header {
        position: sticky;
        top: 0;
        z-index: 50;
        background: rgba(248, 250, 252, .97);
        border-bottom: 1px solid var(--line);
    }

    .header-main {
        min-height: 78px;
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .brand {
        display: inline-flex;
        align-items: center;
        gap: .7rem;
        flex-shrink: 0;
        text-decoration: none;
    }

    .brand-mark {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--gold-300);
        background: var(--forest-900);
        border-radius: 13px 13px 13px 4px;
        box-shadow: 0 7px 16px rgba(22, 101, 52, .16);
    }

    .brand-copy { display: grid; line-height: 1.15; }
    .brand-kicker {
        color: var(--muted);
        font-size: var(--text-2xs);
        font-weight: 700;
        letter-spacing: .16em;
        text-transform: uppercase;
    }
    .brand-name {
        color: var(--forest-950);
        font-size: var(--text-md);
        font-weight: 800;
        letter-spacing: -.02em;
        margin-top: .22rem;
    }

    .header-search {
        min-width: 180px;
        max-width: 330px;
        flex: 1;
        display: flex;
        align-items: center;
        margin-left: auto;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 12px;
        overflow: hidden;
    }

    .header-search__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        color: var(--muted);
    }

    .header-search__input {
        min-width: 0;
        flex: 1;
        height: 42px;
        padding: 0 .5rem 0 0;
        color: var(--ink);
        background: transparent;
        border: 0;
        outline: 0;
        font-size: var(--text-sm);
    }

    .header-search__input::placeholder { color: rgba(0, 0, 0, .45); }
    .header-search__button {
        height: 42px;
        padding: 0 .95rem;
        color: var(--forest-900);
        background: var(--gold-100);
        border: 0;
        border-left: 1px solid var(--line);
        font-size: var(--text-sm);
        font-weight: 700;
        transition: background .2s ease;
    }
    .header-search__button:hover { background: rgba(254, 249, 195, .8); }

    .desktop-nav {
        display: flex;
        align-items: center;
        gap: .2rem;
        margin-left: auto;
    }

    .nav-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .55rem .65rem;
        color: var(--ink-soft);
        border-radius: 9px;
        font-size: var(--text-sm);
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: color .2s ease, background .2s ease;
    }
    .nav-link:hover,
    .nav-link--active { color: var(--forest-800); background: var(--forest-100); }
    .nav-link--active::after {
        content: "";
        position: absolute;
        left: .65rem;
        right: .65rem;
        bottom: .18rem;
        height: 2px;
        background: var(--gold-500);
        border-radius: 99px;
    }
    .nav-icon { display: inline-flex; }
    .cart-link { position: relative; }
    .cart-count {
        min-width: 17px;
        height: 17px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 4px;
        color: var(--forest-950);
        background: var(--gold-300);
        border-radius: 99px;
        font-size: var(--text-2xs);
        font-weight: 800;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-left: .25rem;
    }
    .header-user {
        max-width: 120px;
        overflow: hidden;
        color: var(--muted);
        font-size: var(--text-sm);
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .header-logout {
        padding: .48rem .7rem;
        color: var(--forest-900);
        background: transparent;
        border: 1px solid var(--line-strong);
        border-radius: 9px;
        font-size: var(--text-sm);
        font-weight: 700;
    }
    .header-logout:hover { background: var(--forest-100); }

    .brand-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .notification-menu { position: relative; }
    .notification-menu > summary { list-style: none; }
    .notification-menu > summary::-webkit-details-marker { display: none; }
    .notification-bell {
        position: relative;
        display: inline-flex;
        width: 34px;
        height: 34px;
        align-items: center;
        justify-content: center;
        color: var(--forest-800);
        background: var(--surface);
        border: 1px solid var(--line-strong);
        border-radius: 9px;
        cursor: pointer;
    }
    .notification-bell:hover { background: var(--forest-100); }
    .notification-dot {
        position: absolute;
        top: -4px;
        right: -4px;
        min-width: 17px;
        padding: 1px 4px;
        color: #FFFFFF;
        background: var(--forest-800);
        border-radius: 99px;
        font-size: var(--text-2xs);
        font-weight: 800;
        line-height: 15px;
        text-align: center;
    }
    .notification-panel {
        position: absolute;
        top: calc(100% + .5rem);
        right: 0;
        z-index: 40;
        width: 290px;
        padding: .75rem;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 14px;
        box-shadow: 0 18px 40px rgba(6, 40, 26, .12);
    }
    .notification-panel__title {
        margin: 0 0 .4rem .15rem;
        color: var(--muted);
        font-size: var(--text-xs);
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .notification-item {
        display: block;
        width: 100%;
        padding: .55rem .6rem;
        text-align: left;
        background: var(--surface-muted);
        border: 1px solid var(--line);
        border-radius: 10px;
        cursor: pointer;
    }
    .notification-item + .notification-item { margin-top: .4rem; }
    .notification-item:hover { border-color: var(--gold-300); }
    .notification-item strong {
        display: block;
        color: var(--forest-900);
        font-size: var(--text-sm);
    }
    .notification-item span {
        display: block;
        margin-top: .15rem;
        color: var(--ink-soft);
        font-size: var(--text-xs);
        line-height: 1.5;
    }
    .notification-panel__empty {
        padding: .5rem .15rem;
        color: var(--muted);
        font-size: var(--text-sm);
    }
    .notification-panel__more {
        display: block;
        margin-top: .5rem;
        padding: .4rem;
        color: var(--forest-800);
        border-radius: 8px;
        font-size: var(--text-xs);
        font-weight: 800;
        text-align: center;
        text-decoration: none;
    }
    .notification-panel__more:hover { background: var(--forest-100); }

    /* Katalog: jangkar area filter agar posisi gulir tetap terjaga. */
    .catalog-anchor {
        scroll-margin-top: 90px;
    }

    /* Checkout: penanda field wajib dan bagian data pelengkap. */
    .required-mark {
        display: inline-block;
        margin-left: .2rem;
        padding: .05rem .3rem;
        color: var(--forest-800);
        background: var(--gold-300);
        border-radius: 99px;
        font-size: var(--text-2xs);
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .checkout-required {
        display: grid;
        gap: 1.1rem;
    }

    .checkout-required__grid {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 640px) {
        .checkout-required__grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .checkout-optional {
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px dashed var(--line-strong);
    }

    .checkout-optional > summary {
        cursor: pointer;
        color: var(--forest-800);
        font-size: var(--text-sm);
        font-weight: 800;
    }

    .checkout-optional > summary:hover { color: var(--forest-900); }

    /* Daftar notifikasi. */
    .notification-list__item {
        display: flex;
        flex-direction: column;
        gap: .25rem;
        padding: .9rem 1rem;
        border-bottom: 1px solid var(--line);
    }

    .notification-list__item:last-child { border-bottom: 0; }

    .notification-list__item--unread {
        background: var(--surface-muted);
    }

    .notification-list__title {
        color: var(--forest-900);
        font-size: var(--text-sm);
        font-weight: 800;
    }

    .notification-list__meta {
        color: var(--muted);
        font-size: var(--text-xs);
    }

    .mobile-menu-toggle {
        display: none;
        width: 40px;
        height: 40px;
        align-items: center;
        justify-content: center;
        color: var(--forest-900);
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 10px;
    }
    .mobile-menu {
        display: none;
        padding: 0 0 1rem;
    }
    .mobile-menu.is-open { display: block; }
    .mobile-menu__search { margin-bottom: .7rem; }
    .mobile-menu__links { display: grid; gap: .25rem; }
    .mobile-menu__link {
        display: flex;
        align-items: center;
        gap: .65rem;
        padding: .7rem .75rem;
        color: var(--ink-soft);
        border-radius: 9px;
        font-size: var(--text-sm);
        font-weight: 600;
        text-decoration: none;
    }
    .mobile-menu__link:hover,
    .mobile-menu__link--active { color: var(--forest-900); background: var(--forest-100); }
    .mobile-menu__meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: .75rem;
        padding: .75rem;
        color: var(--muted);
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 10px;
        font-size: var(--text-sm);
    }
    .mobile-menu__logout { color: var(--danger-700); font-weight: 700; }

    /* Layout and typography */
    .page-main { flex: 1; padding: 2.5rem 0 4.5rem; }
    .page-header { margin-bottom: 2rem; }
    .page-header__eyebrow,
    .section-kicker,
    .hero__eyebrow {
        color: var(--forest-700);
        font-size: var(--text-xs);
        font-weight: 800;
        letter-spacing: .16em;
        text-transform: uppercase;
    }
    .page-header__title {
        margin: .4rem 0 .55rem;
        color: var(--forest-950);
        font-size: clamp(1.8rem, 3vw, 2.65rem);
        line-height: 1.08;
    }
    .page-header__description { max-width: 620px; color: var(--muted); font-size: var(--text-sm); }
    .page-header__actions { display: flex; flex-wrap: wrap; gap: .65rem; margin-top: 1.2rem; }
    .section-kicker { display: block; margin-bottom: .45rem; }
    .display-title { margin: 0; color: var(--forest-950); font-size: clamp(2rem, 4vw, 3.35rem); line-height: 1.04; }
    .lede { max-width: 610px; color: var(--muted); font-size: var(--text-md); }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        min-height: 42px;
        padding: .65rem 1rem;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: var(--text-sm);
        font-weight: 800;
        line-height: 1;
        text-decoration: none;
        transition: transform .18s ease, background .18s ease, border-color .18s ease, color .18s ease;
    }
    .btn:hover { transform: translateY(-1px); }
    .btn--primary { color: #FFFFFF; background: var(--forest-800); box-shadow: 0 8px 18px rgba(22, 101, 52, .16); }
    .btn--primary:hover { background: var(--forest-900); }
    .btn--secondary { color: var(--forest-900); background: var(--surface); border-color: var(--line-strong); }
    .btn--secondary:hover { background: var(--forest-100); border-color: rgba(22, 101, 52, .3); }
    .btn--gold { color: var(--forest-950); background: var(--gold-300); }
    .btn--gold:hover { background: #EAB308; }
    .btn--quiet { color: var(--forest-800); background: transparent; border-color: transparent; }
    .btn--quiet:hover { background: var(--forest-100); }
    .btn--danger { color: var(--danger-700); background: var(--danger-100); border-color: rgba(234, 179, 8, .3); }
    .btn--danger:hover { background: rgba(254, 249, 195, .85); }
    .btn--small { min-height: 34px; padding: .45rem .7rem; font-size: var(--text-sm); }

    .text-link {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        color: var(--forest-800);
        font-size: var(--text-sm);
        font-weight: 800;
        text-decoration: none;
    }
    .text-link:hover { color: var(--forest-950); text-decoration: underline; text-underline-offset: 3px; }

    .surface {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
    }
    .surface__header { padding: 1.2rem 1.3rem .9rem; border-bottom: 1px solid var(--line); }
    .surface__title { margin: 0; color: var(--forest-950); font-size: var(--text-md); font-weight: 800; }
    .surface__subtitle { margin: .2rem 0 0; color: var(--muted); font-size: var(--text-sm); }

    /* Flash messages */
    .flash {
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        margin-bottom: 1.25rem;
        padding: .8rem .9rem;
        border: 1px solid;
        border-radius: 11px;
        font-size: var(--text-sm);
        font-weight: 600;
    }
    .flash__icon { flex-shrink: 0; margin-top: .1rem; }
    .flash--success { color: var(--forest-800); background: var(--forest-100); border-color: rgba(22, 101, 52, .25); }
    .flash--error { color: var(--danger-700); background: var(--danger-100); border-color: rgba(234, 179, 8, .4); }
    .flash--validation { color: #000000; background: var(--gold-100); border-color: rgba(234, 179, 8, .45); }
    .flash ul { margin: .2rem 0 0; padding-left: 1.1rem; }

    /* Hero */
    .hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1.08fr) minmax(300px, .92fr);
        gap: clamp(1.5rem, 5vw, 4.5rem);
        align-items: center;
        min-height: 370px;
        margin-bottom: 1.1rem;
        padding: clamp(1.5rem, 4vw, 3.2rem);
        background: var(--forest-950);
        border-radius: 22px;
        overflow: hidden;
    }
    .hero::after {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        right: -90px;
        bottom: -130px;
        border: 1px solid rgba(234, 179, 8, .25);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero__copy { position: relative; z-index: 1; }
    .hero__eyebrow { color: var(--gold-300); }
    .hero__title { max-width: 600px; margin: .65rem 0 1rem; color: #FFFFFF; font-size: clamp(2.2rem, 5vw, 4.25rem); line-height: .99; }
    .hero__description { max-width: 520px; color: rgba(255, 255, 255, .72); font-size: var(--text-md); }
    .hero__actions { display: flex; flex-wrap: wrap; gap: .65rem; margin-top: 1.5rem; }
    .hero__actions .btn--secondary { color: #FFFFFF; background: transparent; border-color: rgba(255, 255, 255, .3); }
    .hero__actions .btn--secondary:hover { background: rgba(255,255,255,.08); }
    .hero__note { display: flex; align-items: center; gap: .45rem; margin-top: 1.15rem; color: rgba(255, 255, 255, .58); font-size: var(--text-sm); }
    .hero__art {
        position: relative;
        min-height: 275px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .hero__art-orbit {
        position: absolute;
        width: 245px;
        height: 245px;
        border: 1px solid rgba(234, 179, 8, .35);
        border-radius: 50%;
    }
    .hero__art-orbit::before,
    .hero__art-orbit::after {
        content: "";
        position: absolute;
        border-radius: 50%;
    }
    .hero__art-orbit::before { width: 13px; height: 13px; top: 15px; right: 28px; background: var(--gold-300); }
    .hero__art-orbit::after { width: 8px; height: 8px; left: 14px; bottom: 42px; background: rgba(22, 101, 52, .35); }
    .hero__art-sprout { position: relative; z-index: 1; color: rgba(254, 249, 195, .8); }
    .hero__art-label {
        position: absolute;
        right: 1%;
        bottom: 10%;
        z-index: 2;
        max-width: 145px;
        padding: .65rem .7rem;
        color: var(--forest-950);
        background: var(--gold-300);
        border-radius: 10px 10px 10px 2px;
        box-shadow: 0 12px 24px rgba(0,0,0,.16);
    }
    .hero__art-label strong { display: block; font-size: var(--text-sm); }
    .hero__art-label span { display: block; margin-top: .15rem; color: rgba(22, 101, 52, .72); font-size: var(--text-2xs); line-height: 1.4; }
    .hero__art-stamp {
        position: absolute;
        top: 4%;
        left: 3%;
        color: rgba(234, 179, 8, .7);
        font-size: var(--text-2xs);
        font-weight: 800;
        letter-spacing: .16em;
        text-transform: uppercase;
        transform: rotate(-8deg);
    }

    .trust-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: .75rem;
        margin-bottom: 2.5rem;
    }
    .trust-item {
        display: flex;
        align-items: center;
        gap: .7rem;
        padding: .85rem 1rem;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 12px;
    }
    .trust-item__icon { color: var(--forest-700); }
    .trust-item__copy strong { display: block; color: var(--ink); font-size: var(--text-sm); }
    .trust-item__copy span { display: block; color: var(--muted); font-size: var(--text-xs); }

    /* Catalog */
    .catalog-toolbar { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin-bottom: 1.2rem; }
    .catalog-toolbar__heading h2 { margin: 0; color: var(--forest-950); font-size: var(--text-xl); letter-spacing: -.02em; }
    .catalog-toolbar__heading p { margin: .25rem 0 0; color: var(--muted); font-size: var(--text-sm); }
    .catalog-toolbar__meta { color: var(--muted); font-size: var(--text-sm); white-space: nowrap; }
    .filter-list { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.4rem; }
    .filter-link {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: .45rem .8rem;
        color: var(--ink-soft);
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 99px;
        font-size: var(--text-sm);
        font-weight: 700;
        text-decoration: none;
        transition: all .18s ease;
    }
    .filter-link:hover { color: var(--forest-800); border-color: rgba(22, 101, 52, .3); }
    .filter-link--active { color: #FFFFFF; background: var(--forest-800); border-color: var(--forest-800); }
    .product-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
    .product-grid--related { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .product-card { min-width: 0; background: var(--surface); border: 1px solid var(--line); border-radius: 15px; overflow: hidden; box-shadow: var(--shadow-sm); transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
    .product-card:hover { transform: translateY(-3px); border-color: rgba(22, 101, 52, .25); box-shadow: var(--shadow-md); }
    .product-card__link { display: flex; height: 100%; flex-direction: column; text-decoration: none; }
    .product-card__body { display: flex; flex: 1; flex-direction: column; padding: 1rem; }
    .product-card__topline { display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
    .product-card__category { color: var(--forest-700); font-size: var(--text-xs); font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
    .product-card__name { margin: .55rem 0 .25rem; color: var(--forest-950); font-size: var(--text-md); font-weight: 800; line-height: 1.25; }
    .product-card__description { margin: 0; color: var(--muted); font-size: var(--text-sm); line-height: 1.55; }
    .product-card__footer { display: flex; align-items: center; justify-content: space-between; gap: .5rem; margin-top: auto; padding-top: 1rem; }
    .product-card__price { color: var(--forest-900); font-size: var(--text-md); font-weight: 800; }
    .product-card__arrow { color: var(--forest-700); transition: transform .2s ease; }
    .product-card:hover .product-card__arrow { transform: translateX(3px); }

    .product-art { position: relative; height: 210px; min-height: 210px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: rgba(254, 249, 195, .55); }
    .product-art--large { height: 430px; min-height: 430px; }
    .product-art--compact { height: 76px; min-height: 76px; width: 76px; flex-shrink: 0; border-radius: 10px; }
    .product-art__image { width: 100%; height: 100%; min-height: inherit; display: block; object-fit: cover; }
    .product-art__placeholder { position: relative; width: 100%; height: 100%; min-height: inherit; display: flex; align-items: center; justify-content: center; background: rgba(254, 249, 195, .45); overflow: hidden; }
    .product-art__sun { position: absolute; width: 110px; height: 110px; top: 22px; right: 26px; background: #FEF9C3; border-radius: 50%; opacity: .8; }
    .product-art__soil { position: absolute; width: 160px; height: 70px; bottom: -24px; left: 50%; background: rgba(22, 101, 52, .14); border-radius: 50% 50% 0 0; transform: translateX(-50%); }
    .product-art__leaf { position: relative; z-index: 1; color: var(--forest-800); }
    .product-art__label { position: absolute; left: 14px; bottom: 12px; z-index: 2; color: var(--forest-800); font-size: var(--text-2xs); font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }

    .availability { display: inline-flex; align-items: center; gap: .3rem; color: var(--muted); font-size: var(--text-xs); font-weight: 700; white-space: nowrap; }
    .availability::before { content: ""; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .availability--good { color: var(--forest-700); }
    .availability--low { color: var(--gold-700); }
    .availability--empty { color: var(--danger-700); }

    /* Product detail */
    .breadcrumb { display: flex; align-items: center; flex-wrap: wrap; gap: .4rem; margin-bottom: 1.25rem; color: var(--muted); font-size: var(--text-sm); }
    .breadcrumb__link { color: var(--forest-700); font-weight: 700; text-decoration: none; }
    .breadcrumb__link:hover { text-decoration: underline; text-underline-offset: 3px; }
    .breadcrumb__current { color: var(--muted); }
    .detail-layout { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, .9fr); gap: 1.2rem; align-items: stretch; }
    .detail-visual { overflow: hidden; border-radius: 18px; box-shadow: var(--shadow-sm); }
    .detail-copy { display: flex; flex-direction: column; padding: clamp(1.2rem, 3vw, 2rem); background: var(--surface); border: 1px solid var(--line); border-radius: 18px; box-shadow: var(--shadow-sm); }
    .detail-category { color: var(--forest-700); font-size: var(--text-xs); font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .detail-title { margin: .55rem 0 .7rem; color: var(--forest-950); font-size: clamp(2rem, 4vw, 3.2rem); line-height: 1.03; }
    .detail-description { margin: 0; color: var(--ink-soft); font-size: var(--text-sm); line-height: 1.75; }
    .detail-price-row { display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin-top: 1.5rem; padding-top: 1.2rem; border-top: 1px solid var(--line); }
    .detail-price { color: var(--forest-900); font-size: var(--text-2xl); font-weight: 800; letter-spacing: -.04em; }
    .detail-price__unit { color: var(--muted); font-size: var(--text-sm); font-weight: 500; }
    .spec-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .65rem; margin-top: 1.1rem; }
    .spec-item { padding: .75rem; background: var(--surface-muted); border-radius: 10px; }
    .spec-item__label { color: var(--muted); font-size: var(--text-xs); }
    .spec-item__value { margin-top: .18rem; color: var(--ink); font-size: var(--text-sm); font-weight: 800; }
    .purchase-panel { margin-top: auto; padding-top: 1.35rem; }
    .purchase-form { display: flex; align-items: end; gap: .65rem; }
    .quantity-control { width: 105px; }
    .form-label { display: block; margin-bottom: .4rem; color: var(--ink-soft); font-size: var(--text-sm); font-weight: 800; }
    .form-input, .form-select, .form-textarea {
        width: 100%;
        color: var(--ink);
        background: var(--surface);
        border: 1px solid var(--line-strong);
        border-radius: 9px;
        outline: 0;
        font-size: var(--text-sm);
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }
    .form-input, .form-select { height: 42px; padding: 0 .75rem; }
    .form-textarea { min-height: 100px; padding: .7rem .75rem; resize: vertical; }
    .form-input:focus, .form-select:focus, .form-textarea:focus { border-color: var(--forest-700); box-shadow: 0 0 0 3px rgba(22, 101, 52, .12); }
    .form-input::placeholder, .form-textarea::placeholder { color: rgba(0, 0, 0, .45); }
    .form-hint { margin-top: .35rem; color: var(--muted); font-size: var(--text-xs); }
    .form-error { margin-top: .35rem; color: var(--danger-700); font-size: var(--text-xs); font-weight: 600; }
    .detail-note { display: flex; align-items: flex-start; gap: .45rem; margin-top: .8rem; color: var(--muted); font-size: var(--text-xs); }
    .detail-note svg { flex-shrink: 0; color: var(--forest-700); }

    /* Order and content layouts */
    .order-steps { display: flex; align-items: center; width: 100%; margin: 0 0 1.7rem; }
    .order-step { position: relative; display: flex; align-items: center; gap: .45rem; flex: 1; color: var(--muted); font-size: var(--text-xs); font-weight: 700; }
    .order-step:not(:last-child)::after { content: ""; height: 1px; flex: 1; margin: 0 .45rem; background: var(--line-strong); }
    .order-step__number { width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--muted); background: var(--surface); border: 1px solid var(--line-strong); border-radius: 50%; font-size: var(--text-xs); }
    .order-step--active { color: var(--forest-800); }
    .order-step--active .order-step__number { color: #FFFFFF; background: var(--forest-800); border-color: var(--forest-800); }
    .order-step--done .order-step__number { color: var(--forest-900); background: var(--gold-300); border-color: var(--gold-300); }
    .order-step--done:not(:last-child)::after { background: var(--gold-300); }
    .content-layout { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(260px, .7fr); gap: 1.2rem; align-items: start; }
    .content-main, .content-aside { display: grid; gap: 1rem; min-width: 0; }
    .content-aside { position: sticky; top: 100px; }

    /* Tables and order rows */
    .order-table-wrap { overflow-x: auto; }
    .order-table { width: 100%; border-collapse: collapse; min-width: 620px; }
    .order-table__head { color: var(--muted); background: var(--surface-muted); font-size: var(--text-xs); font-weight: 800; letter-spacing: .08em; text-align: left; text-transform: uppercase; }
    .order-table__head th { padding: .85rem 1rem; }
    .order-table__row { border-top: 1px solid var(--line); transition: background .18s ease; }
    .order-table__row:hover { background: #F8FAFC; }
    .order-table__cell { padding: 1rem; color: var(--ink-soft); font-size: var(--text-sm); vertical-align: middle; }
    .order-table__primary { color: var(--forest-950); font-weight: 800; }
    .order-table__secondary { display: block; margin-top: .15rem; color: var(--muted); font-size: var(--text-xs); }
    .order-table__action { text-align: right; }
    .status-pill { display: inline-flex; align-items: center; min-height: 24px; padding: .3rem .55rem; border-radius: 99px; font-size: var(--text-xs); font-weight: 800; white-space: nowrap; }
    .status-pill--green { color: var(--forest-800); background: var(--forest-100); }
    .status-pill--gold { color: var(--gold-700); background: var(--gold-100); }
    .status-pill--red { color: var(--danger-700); background: var(--danger-100); }
    .status-pill--blue { color: var(--blue-700); background: var(--blue-100); }
    .status-pill--neutral { color: var(--ink-soft); background: var(--surface-muted); }

    .order-list { display: grid; }
    .order-list__item { display: block; padding: 1.1rem 1.25rem; color: inherit; border-bottom: 1px solid var(--line); text-decoration: none; transition: background .18s ease; }
    .order-list__item:last-child { border-bottom: 0; }
    .order-list__item:hover { background: #F8FAFC; }
    .order-list__top { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; }
    .order-list__label { display: block; color: var(--muted); font-size: var(--text-xs); }
    .order-list__number { display: block; margin-top: .18rem; color: var(--forest-950); font-size: var(--text-sm); letter-spacing: .01em; }
    .order-list__bottom { display: flex; align-items: center; gap: 1rem; margin-top: .9rem; color: var(--muted); font-size: var(--text-xs); }
    .order-list__bottom > span:first-child { display: inline-flex; align-items: center; gap: .35rem; }
    .order-list__bottom > strong { margin-left: auto; color: var(--forest-900); font-size: var(--text-sm); }
    .order-list__action { display: inline-flex; align-items: center; gap: .3rem; color: var(--forest-800); font-weight: 800; }

    /* Cart and checkout */
    .cart-layout { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(270px, .6fr); gap: 1.2rem; align-items: start; }
    .cart-list { overflow: hidden; }
    .cart-row { display: grid; grid-template-columns: 76px minmax(0, 1fr) auto auto auto; gap: .85rem; align-items: center; padding: 1rem; border-bottom: 1px solid var(--line); }
    .cart-row:last-child { border-bottom: 0; }
    .cart-row__art { overflow: hidden; border-radius: 10px; }
    .cart-row__art .product-art--compact { width: 100%; }
    .cart-row__content { min-width: 0; }
    .cart-row__name { margin: 0; color: var(--forest-950); font-size: var(--text-sm); font-weight: 800; }
    .cart-row__meta { margin: .25rem 0 0; color: var(--muted); font-size: var(--text-xs); }
    .cart-row__quantity { display: flex; align-items: center; gap: .35rem; }
    .cart-row__quantity .form-input { width: 64px; height: 34px; padding-inline: .45rem; }
    .cart-row__subtotal { min-width: 100px; color: var(--forest-900); font-size: var(--text-sm); font-weight: 800; text-align: right; }
    .cart-row__remove { color: var(--danger-700); background: transparent; border: 0; font-size: var(--text-xs); font-weight: 700; }
    .cart-row__remove:hover { text-decoration: underline; }
    .cart-row__remove-form { display: inline-flex; }
    .cart-row__spacer { display: block; }
    .summary-card { padding: 1.2rem; }
    .summary-card__title { margin: 0 0 1rem; color: var(--forest-950); font-size: var(--text-md); font-weight: 800; }
    .summary-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .45rem 0; color: var(--muted); font-size: var(--text-sm); }
    .summary-row strong { color: var(--ink); }
    .summary-total { margin-top: .55rem; padding-top: .85rem; border-top: 1px solid var(--line); color: var(--forest-950); font-size: var(--text-md); font-weight: 800; }
    .summary-total strong { color: var(--forest-900); font-size: var(--text-xl); letter-spacing: -.03em; }
    .summary-note { margin: 1rem 0 0; padding-top: .9rem; color: var(--muted); border-top: 1px solid var(--line); font-size: var(--text-xs); line-height: 1.6; }
    .checkout-form { display: grid; gap: .85rem; margin-top: 1rem; }

    /* Empty state */
    .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 270px; padding: 2rem 1.25rem; text-align: center; background: var(--surface); border: 1px dashed var(--line-strong); border-radius: 16px; }
    .empty-state__icon { width: 48px; height: 48px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: .8rem; color: var(--forest-700); background: var(--forest-100); border-radius: 14px 14px 14px 4px; }
    .empty-state__eyebrow { color: var(--forest-700); font-size: var(--text-2xs); font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .empty-state__title { margin: .35rem 0 .35rem; color: var(--forest-950); font-size: var(--text-lg); font-weight: 800; }
    .empty-state__description { max-width: 390px; margin: 0; color: var(--muted); font-size: var(--text-sm); }
    .empty-state .btn { margin-top: 1.1rem; }

    /* Profile */
    .profile-hero { display: flex; align-items: center; justify-content: space-between; gap: 1.2rem; padding: 1.4rem; background: var(--forest-950); border-radius: 18px; color: #FFFFFF; overflow: hidden; }
    .profile-hero__identity { display: flex; align-items: center; gap: 1rem; min-width: 0; }
    .profile-avatar { width: 72px; height: 72px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--forest-950); background: var(--gold-300); border: 4px solid rgba(255,255,255,.18); border-radius: 22px 22px 22px 5px; font-size: var(--text-lg); font-weight: 800; }
    .profile-hero__copy { min-width: 0; }
    .profile-hero__name { margin: 0; color: #FFFFFF; font-size: var(--text-xl); line-height: 1.05; }
    .profile-hero__meta { margin: .4rem 0 0; color: rgba(255,255,255,.65); font-size: var(--text-sm); }
    .profile-hero .btn--secondary { color: #FFFFFF; background: transparent; border-color: rgba(255,255,255,.3); }
    .profile-hero .btn--secondary:hover { background: rgba(255,255,255,.1); }
    .profile-grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(240px, .55fr); gap: 1.2rem; margin-top: 1.2rem; align-items: start; }
    .profile-section { padding: 1.2rem; }
    .profile-section__title { margin: 0 0 .9rem; color: var(--forest-950); font-size: var(--text-md); font-weight: 800; }
    .profile-list { display: grid; }
    .profile-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .75rem 0; border-bottom: 1px solid var(--line); }
    .profile-row:last-child { border-bottom: 0; }
    .profile-row__label { color: var(--muted); font-size: var(--text-sm); }
    .profile-row__value { max-width: 65%; color: var(--ink); font-size: var(--text-sm); font-weight: 700; text-align: right; overflow-wrap: anywhere; }
    .profile-actions { display: grid; align-content: start; gap: .7rem; }
    .profile-note { padding: .9rem; color: var(--ink-soft); background: var(--surface-muted); border-radius: 11px; font-size: var(--text-sm); line-height: 1.65; }

    /* Auth */
    .auth-layout { display: grid; grid-template-columns: minmax(0, .9fr) minmax(360px, 1.1fr); gap: 1.2rem; align-items: stretch; max-width: 940px; margin: 1rem auto 0; }
    .auth-aside { position: relative; display: flex; min-height: 500px; flex-direction: column; justify-content: space-between; padding: 2rem; color: #FFFFFF; background: var(--forest-950); border-radius: 20px; overflow: hidden; }
    .auth-aside::after { content: ""; position: absolute; width: 270px; height: 270px; right: -110px; bottom: -90px; border: 1px solid rgba(234, 179, 8, .3); border-radius: 50%; }
    .auth-aside__eyebrow { position: relative; z-index: 1; color: var(--gold-300); font-size: var(--text-xs); font-weight: 800; letter-spacing: .15em; text-transform: uppercase; }
    .auth-aside__title { position: relative; z-index: 1; max-width: 330px; margin: .75rem 0 .7rem; color: #FFFFFF; font-family: "Segoe UI", "ui-sans-serif", system-ui, sans-serif; font-size: clamp(2rem, 4vw, 3.2rem); line-height: 1.02; letter-spacing: -.04em; }
    .auth-aside__copy { position: relative; z-index: 1; max-width: 310px; margin: 0; color: rgba(255,255,255,.68); font-size: var(--text-sm); }
    .auth-aside__art { position: relative; z-index: 1; display: flex; align-items: end; justify-content: space-between; gap: 1rem; margin-top: 2rem; color: rgba(254, 249, 195, .75); }
    .auth-aside__art small { color: rgba(255,255,255,.55); font-size: var(--text-xs); }
    .auth-card { padding: clamp(1.3rem, 4vw, 2.2rem); background: var(--surface); border: 1px solid var(--line); border-radius: 20px; box-shadow: var(--shadow-md); }
    .auth-card__header { margin-bottom: 1.4rem; }
    .auth-card__eyebrow { color: var(--forest-700); font-size: var(--text-xs); font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
    .auth-card__title { margin: .4rem 0 .45rem; color: var(--forest-950); font-size: var(--text-2xl); line-height: 1.05; }
    .auth-card__description { margin: 0; color: var(--muted); font-size: var(--text-sm); }
    .auth-form { display: grid; gap: .9rem; }
    .auth-form__row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; color: var(--muted); font-size: var(--text-sm); }
    .check-label { display: inline-flex; align-items: center; gap: .45rem; cursor: pointer; }
    .check-label input { accent-color: var(--forest-800); }
    .auth-footer { margin-top: 1.3rem; color: var(--muted); font-size: var(--text-sm); text-align: center; }
    .auth-footer a { color: var(--forest-800); font-weight: 800; text-decoration: none; }
    .auth-footer a:hover { text-decoration: underline; text-underline-offset: 3px; }
    .credential-note { margin-top: 1.2rem; padding: .75rem .8rem; color: var(--gold-700); background: var(--gold-100); border: 1px solid rgba(234, 179, 8, .45); border-radius: 10px; font-size: var(--text-xs); line-height: 1.6; }
    .credential-note strong { color: var(--forest-900); }

    /* Upload */
    .upload-zone { display: flex; align-items: center; gap: .65rem; padding: .65rem; background: var(--surface-muted); border: 1px dashed var(--line-strong); border-radius: 10px; }
    .file-input { min-width: 0; flex: 1; color: var(--muted); font-size: var(--text-sm); }
    .file-input::file-selector-button { margin-right: .6rem; padding: .4rem .55rem; color: var(--forest-900); background: var(--surface); border: 1px solid var(--line-strong); border-radius: 7px; font-size: var(--text-xs); font-weight: 700; cursor: pointer; }

    /* Footer */
    .site-footer { margin-top: auto; color: rgba(255,255,255,.68); background: var(--forest-950); }
    .footer-main { display: grid; grid-template-columns: minmax(0, 1.2fr) repeat(2, minmax(120px, .55fr)); gap: 2rem; padding: 2.5rem 0 2rem; }
    .footer-brand { color: #FFFFFF; }
    .footer-brand .brand-kicker { color: rgba(255,255,255,.45); }
    .footer-brand .brand-name { color: #FFFFFF; }
    .footer-brand p { max-width: 300px; margin: .75rem 0 0; color: rgba(255,255,255,.56); font-size: var(--text-sm); }
    .footer-title { margin: 0 0 .65rem; color: var(--gold-300); font-size: var(--text-xs); font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
    .footer-links { display: grid; gap: .4rem; }
    .footer-links a { color: rgba(255,255,255,.65); font-size: var(--text-sm); text-decoration: none; }
    .footer-links a:hover { color: #FFFFFF; }
    .footer-bottom { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .9rem 0; color: rgba(255,255,255,.42); border-top: 1px solid rgba(255,255,255,.1); font-size: var(--text-xs); }

    .muted { color: var(--muted); }
    .divider { height: 1px; background: var(--line); }
    .section-divider { margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--line); }
    .hidden { display: none !important; }


    @media (min-width: 640px) {
        .customer-container { width: min(1180px, calc(100% - 3rem)); }
        .empty-state { min-height: 320px; }
    }

    @media (max-width: 980px) {
        .header-search { max-width: 260px; }
        .desktop-nav { gap: 0; }
        .nav-link { padding-inline: .45rem; }
        .product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .product-grid--related { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .hero { grid-template-columns: minmax(0, 1fr) minmax(240px, .8fr); }
        .hero__art { min-height: 230px; }
        .auth-layout { grid-template-columns: minmax(0, .8fr) minmax(340px, 1.2fr); }
    }

    @media (max-width: 860px) {
        .header-main { min-height: 68px; gap: .7rem; }
        .header-main > .header-search, .desktop-nav { display: none; }
        .mobile-menu .header-search { display: flex; }
        .mobile-menu-toggle { display: inline-flex; margin-left: auto; }
        .header-actions { display: none; }
        .page-main { padding-top: 1.6rem; }
        .hero { grid-template-columns: 1fr; min-height: 0; padding: 1.5rem; }
        .hero__art { min-height: 190px; margin-top: .5rem; }
        .hero__art-label { right: 8%; bottom: 4%; }
        .trust-strip { grid-template-columns: 1fr; }
        .content-layout, .cart-layout, .profile-grid, .auth-layout { grid-template-columns: 1fr; }
        .content-aside { position: static; }
        .auth-aside { min-height: 260px; }
        .auth-aside__art { margin-top: 1rem; }
        .footer-main { grid-template-columns: minmax(0, 1fr) repeat(2, minmax(100px, .5fr)); gap: 1rem; }
    }

    @media (max-width: 620px) {
        .customer-container { width: min(100% - 1.25rem, 1180px); }
        .brand-name { font-size: var(--text-md); }
        .brand-kicker { font-size: var(--text-2xs); }
        .page-main { padding: 1.25rem 0 3rem; }
        .hero { border-radius: 17px; }
        .hero__title { font-size: 2.35rem; }
        .hero__actions { flex-direction: column; align-items: stretch; }
        .hero__actions .btn { width: 100%; }
        .hero__art { min-height: 155px; }
        .hero__art-orbit { width: 170px; height: 170px; }
        .hero__art-label { max-width: 125px; padding: .5rem; }
        .catalog-toolbar { align-items: start; flex-direction: column; gap: .4rem; }
        .product-grid { grid-template-columns: 1fr; }
        .product-grid--related { grid-template-columns: 1fr; }
        .product-art { height: 190px; min-height: 190px; }
        .detail-layout { grid-template-columns: 1fr; }
        .detail-visual .product-art { height: 300px; min-height: 300px; }
        .detail-copy { padding: 1.1rem; }
        .detail-title { font-size: 2.25rem; }
        .purchase-form { align-items: stretch; flex-direction: column; }
        .quantity-control { width: 100%; }
        .order-steps { overflow-x: auto; padding-bottom: .25rem; }
        .order-step { min-width: 95px; }
        .order-step__label { display: none; }
        .cart-row { grid-template-columns: 64px minmax(0, 1fr) auto; gap: .6rem; }
        .cart-row__art { width: 64px; }
        .cart-row__art .product-art--compact { height: 64px; min-height: 64px; }
        .cart-row__spacer { display: none; }
        .cart-row__quantity { grid-column: 2; grid-row: 2; justify-self: start; }
        .cart-row__subtotal { grid-column: 3; grid-row: 1 / span 2; align-self: center; }
        .cart-row__remove-form { grid-column: 1 / 3; grid-row: 3; justify-self: start; }
        .profile-hero { align-items: flex-start; flex-direction: column; }
        .profile-hero .btn { width: 100%; }
        .profile-row { align-items: flex-start; flex-direction: column; gap: .2rem; }
        .profile-row__value { max-width: 100%; text-align: left; }
        .order-list__bottom { flex-wrap: wrap; }
        .order-list__action { margin-left: auto; }
        .auth-aside { padding: 1.35rem; }
        .auth-aside__title { font-size: 2.2rem; }
        .auth-card { padding: 1.25rem; }
        .footer-main { grid-template-columns: 1fr 1fr; }
        .footer-brand { grid-column: 1 / -1; }
        .footer-bottom { align-items: flex-start; flex-direction: column; }
    }

    @media print {
        .site-header, .site-footer, .btn, .order-steps, .flash { display: none !important; }
        body.customer-body { background: #FFFFFF; }
        .page-main { padding: 0; }
        .surface, .detail-copy { box-shadow: none; }
    }
</style>
