<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="keywords"
          content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 5 admin, bootstrap 5, css3 dashboard, bootstrap 5 dashboard, Matrix lite admin bootstrap 5 dashboard, frontend, responsive bootstrap 5 admin template, Matrix admin lite design, Matrix admin lite dashboard bootstrap 5 dashboard template"/>
    <meta name="description"
          content="Matrix Admin Lite Free Version is powerful and clean admin dashboard template, inpired from Bootstrap Framework"/>
    <meta name="robots" content="noindex,nofollow"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Бошқарув панели' }}</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}"/>
    <link rel="stylesheet" href="{{ asset('css/backend/dist/style.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/backend/main.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/backend/libs/float-chart.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/backend/libs/magnific-popup.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/backend/packages/select2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/backend/packages/flatpickr.min.css') }}"/>
    <script src="{{ asset('js/backend/libs/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/backend/package/select2.min.js') }}"></script>
    <script src="{{ asset('js/backend/package/ckeditor.js') }}"></script>
    <script src="{{ asset('js/backend/package/flatpickr.min.js') }}"></script>
</head>

<body>
<!-- Preloader - style you can find in spinners.css -->
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>

<!-- Main wrapper - style you can find in pages.scss -->
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
     data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <!-- Topbar header - style you can find in pages.scss -->
    <header class="topbar" data-navbarbg="skin5">
        <nav class="navbar top-navbar navbar-expand-md navbar-dark">
            <div class="navbar-header" data-logobg="skin5">
                <!-- Logo -->
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <!-- Logo icon -->
                    <b class="logo-icon ps-2">
                        <img
                            src="{{ asset('images/logo.png') }}"
                            alt="homepage"
                            class="light-logo"
                            width="55"
                            style="margin: 10px 0 0 -10px"
                        />
                    </b>
                    <!-- Logo text -->
                    <b class="logo-text ms-1">
                        <img
                            src="{{ asset('images/logo-text.png') }}"
                            alt="homepage"
                            class="light-logo"
                            height="30"
                            width="150"
                            style="margin-top: 15px"
                        />
                    </b>
                </a>

                <!-- Toggle which is visible on mobile only -->
                <a
                    class="nav-toggler waves-effect waves-light d-block d-md-none"
                    href="javascript:void(0)"
                ><i class="ti-menu ti-close"></i
                    ></a>
            </div>
            <!-- End Logo -->
            <div
                class="navbar-collapse collapse"
                id="navbarSupportedContent"
                data-navbarbg="skin5"
            >
                <!-- toggle and nav items -->
                <ul class="navbar-nav float-start me-auto">
                    <li class="nav-item d-none d-lg-block">
                        <a
                            class="nav-link sidebartoggler waves-effect waves-light"
                            href="javascript:void(0)"
                            data-sidebartype="mini-sidebar"
                        ><i class="mdi mdi-menu font-24"></i
                            ></a>
                    </li>
                    <!-- create new -->
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            id="navbarDropdown"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                      <span class="d-none d-md-block">Янги яратиш <i class="fa fa-angle-down"></i
                          ></span>
                            <span class="d-block d-md-none"
                            ><i class="fa fa-plus"></i
                                ></span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            @if (auth()->user()->role->title === 'Admin' || 'Manager' || 'Developer')
                                <li><a class="dropdown-item" href="{{ route('order.create') }}">Буюртма</a></li>
                            @endif
                            @if (auth()->user()->role->title === 'Admin' || 'Manager' || 'Moderator' || 'Developer')
                                <li><a class="dropdown-item" href="{{ route('product.index') }}">Маҳсулот</a></li>
                                <li><a class="dropdown-item" href="{{ route('raw-material.index') }}">Хомашё</a></li>
                            @endif
                            @if (auth()->user()->role->title === 'Admin' || 'Manager' || 'Developer')
                                <li><a class="dropdown-item" href="{{ route('category.create') }}">Категория</a></li>
                            @endif
                            <li>
                                <hr class="dropdown-divider"/>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('user.create') }}">Мижоз / Ходим</a>
                            </li>
                        </ul>
                    </li>
                    <!-- Search -->
                    <li class="nav-item search-box">
                        <a
                            class="nav-link waves-effect waves-dark"
                            href="javascript:void(0)"
                        ><i class="mdi mdi-magnify fs-4"></i
                            ></a>
                        <form class="app-search position-absolute">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Search &amp; enter"
                            />
                            <a class="srh-btn"><i class="mdi mdi-window-close"></i></a>
                        </form>
                    </li>
                </ul>

                <!-- Right side toggle and nav items -->
                <ul class="navbar-nav float-end">
                    <!-- Comment -->
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            id="navbarDropdown"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="mdi mdi-bell font-24"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Action</a></li>
                            <li><a class="dropdown-item" href="#">Another action</a></li>
                            <li>
                                <hr class="dropdown-divider"/>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">Something else here</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Messages -->
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle waves-effect waves-dark"
                            href="#"
                            id="2"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="font-24 mdi mdi-comment-processing"></i>
                        </a>
                        <ul
                            class="
                        dropdown-menu dropdown-menu-end
                        mailbox
                        animated
                        bounceInDown
                      "
                            aria-labelledby="2"
                        >
                            <ul class="list-style-none">
                                <li>
                                    <div class="">
                                        <!-- Message -->
                                        <a href="javascript:void(0)" class="link border-top">
                                            <div class="d-flex no-block align-items-center p-10">
                                <span
                                    class="
                                    btn btn-success btn-circle
                                    d-flex
                                    align-items-center
                                    justify-content-center
                                  "
                                ><i class="mdi mdi-calendar text-white fs-4"></i
                                    ></span>
                                                <div class="ms-2">
                                                    <h5 class="mb-0">Event today</h5>
                                                    <span class="mail-desc"
                                                    >Just a reminder that event</span
                                                    >
                                                </div>
                                            </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)" class="link border-top">
                                            <div class="d-flex no-block align-items-center p-10">
                                <span
                                    class="
                                    btn btn-info btn-circle
                                    d-flex
                                    align-items-center
                                    justify-content-center
                                  "
                                ><i class="mdi mdi-settings fs-4"></i
                                    ></span>
                                                <div class="ms-2">
                                                    <h5 class="mb-0">Settings</h5>
                                                    <span class="mail-desc"
                                                    >You can customize this template</span
                                                    >
                                                </div>
                                            </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)" class="link border-top">
                                            <div class="d-flex no-block align-items-center p-10">
                                <span
                                    class="
                                    btn btn-primary btn-circle
                                    d-flex
                                    align-items-center
                                    justify-content-center
                                  "
                                ><i class="mdi mdi-account fs-4"></i
                                    ></span>
                                                <div class="ms-2">
                                                    <h5 class="mb-0">Pavan kumar</h5>
                                                    <span class="mail-desc"
                                                    >Just see the my admin!</span
                                                    >
                                                </div>
                                            </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="javascript:void(0)" class="link border-top">
                                            <div class="d-flex no-block align-items-center p-10">
                                <span
                                    class="
                                    btn btn-danger btn-circle
                                    d-flex
                                    align-items-center
                                    justify-content-center
                                  "
                                ><i class="mdi mdi-link fs-4"></i
                                    ></span>
                                                <div class="ms-2">
                                                    <h5 class="mb-0">Luanch Admin</h5>
                                                    <span class="mail-desc"
                                                    >Just see the my new admin!</span
                                                    >
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </ul>
                    </li>

                    <!-- User profile and search -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="#"
                           id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img
                                src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/users/1.jpg') }}"
                                alt="{{ Auth::user()->username }}" class="rounded-circle" width="30"/>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end user-dd animated" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="mdi mdi-account me-1 ms-1"></i>{{ Auth::user()->username .' '.'('. (Auth::user()->role->title) .')' }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="mdi mdi-wallet me-1 ms-1"></i>{{ Auth::user()->debt }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="mdi mdi-email me-1 ms-1"></i>Хабарлар
                            </a>
                            <a class="dropdown-item" href="{{ route('user.update', Auth::user()->id) }}">
                                <i class="mdi mdi-settings me-1 ms-1"></i>Профилни таҳрирлаш
                            </a>
                            <div class="dropdown-divider"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-power-off me-1 ms-1"></i> Чиқиш
                                </button>
                            </form>

                            <div class="dropdown-divider"></div>
                            <div class="ps-4 p-10">
                                <a href="{{ route('user.edit', Auth::user()->id) }}"
                                   class="btn btn-sm btn-success btn-rounded text-white">Профилни кўриш</a>
                            </div>
                        </ul>
                    </li>
                    <!-- User profile and search -->
                </ul>
            </div>
        </nav>
    </header>
    <!-- End Topbar header -->

    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <aside class="left-sidebar" data-sidebarbg="skin5">
        <div class="scroll-sidebar">
            <nav class="sidebar-nav">
                <ul id="sidebarnav" class="pt-4">

                    @can ('fullAccess')
                        @php // General @endphp
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                     xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 512 512" xml:space="preserve" class="me-2">
                                    <polygon style="fill:#D8D8DA;"
                                             points="18.285,176.821 146.286,166.263 274.285,324.623 274.285,472.424 18.285,324.623 "/>
                                    <polygon style="fill:#003169;"
                                             points="229.107,446.264 63.465,350.63 63.465,202.901 229.107,298.534 "/>
                                    <polygon style="fill:#013F8A;"
                                             points="54.858,197.936 237.715,303.508 237.715,288.428 54.858,182.856 "/>
                                    <polygon style="fill:#C6C5CB;"
                                             points="274.285,472.424 493.715,345.737 493.715,197.936 274.285,324.623 "/>
                                    <polygon style="fill:#003169;"
                                             points="146.286,166.263 365.715,39.576 512,220.545 292.572,347.232 "/>
                                    <polygon style="fill:#013F8A;"
                                             points="146.286,166.263 0,178.394 219.429,51.707 365.715,39.576 "/>
                                    <polygon style="fill:#ACABB1;"
                                             points="73.143,356.294 73.143,208.492 219.429,292.95 219.429,440.752 "/>
                                    <path style="fill:#898890;"
                                          d="M73.143,312.825l146.286,84.458v2.414L73.143,315.239C73.143,315.239,73.143,312.825,73.143,312.825z M219.429,418.381L73.143,333.923v2.414l146.286,84.458V418.381z M73.143,294.141L219.429,378.6v-2.414L73.143,291.728C73.143,291.728,73.143,294.141,73.143,294.141z M73.143,252.409l146.286,84.458v-2.414L73.143,249.995C73.143,249.995,73.143,252.409,73.143,252.409z M73.143,229.362v2.414l146.286,84.458v-2.414L73.143,229.362z M73.143,273.043l146.286,84.458v-2.414L73.143,270.629C73.143,270.629,73.143,273.043,73.143,273.043z"/>
                                    <g>
                                        <polygon style="fill:#2487FF;"
                                                 points="310.887,345.754 347.428,324.623 347.428,366.852 310.887,388.049 	"/>
                                        <polygon style="fill:#2487FF;"
                                                 points="365.743,314.082 402.286,292.95 402.286,335.179 365.743,356.378 	"/>
                                        <polygon style="fill:#2487FF;"
                                                 points="420.601,282.411 457.144,261.279 457.144,303.508 420.601,324.706 	"/>
                                    </g>
                                </svg>
                                <span class="hide-menu">Асосий</span>
                            </a>
                            <ul aria-expanded="false" class="collapse first-level ps-3">
                                @php // Warehouse (Омборлар) @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                       href="{{ route('warehouse.index') }}" aria-expanded="false">
                                        <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                             xml:space="preserve" class="me-2"><polygon style="fill:#898890;"
                                                                                        points="13.837,232.032 110.702,224.043 207.567,343.882 207.567,455.732 13.837,343.882 "/>
                                            <polyline style="fill:#77767E;"
                                                      points="207.567,455.732 498.163,287.958 498.163,176.107 207.567,343.882 "/>
                                            <polygon style="fill:#ACABB1;"
                                                     points="110.702,224.043 401.298,56.268 512,193.218 221.406,360.992 "/>
                                            <g>
                                                <polygon style="fill:#D8D8DA;"
                                                         points="110.702,224.043 0,233.224 290.595,65.448 401.298,56.268 	"/>
                                                <polygon style="fill:#D8D8DA;"
                                                         points="27.676,351.871 27.676,271.978 166.054,351.871 166.054,431.764 	"/>
                                            </g>
                                            <polygon style="fill:#3E3D43;"
                                                     points="179.892,439.754 179.892,407.797 193.73,415.786 193.818,447.728 "/>
                                            <g>
                                                <polygon style="fill:#ACABB1;"
                                                         points="166.054,416.351 166.054,415.145 27.937,335.403 27.676,335.252 27.676,336.458 165.793,416.201 	"/>
                                                <polygon style="fill:#ACABB1;"
                                                         points="27.676,319.287 27.676,320.492 165.793,400.234 166.054,400.385 166.054,399.18	27.937,319.437 	"/>
                                                <polygon style="fill:#ACABB1;"
                                                         points="166.054,368.453 166.054,367.247 27.937,287.505 27.676,287.354 27.676,288.56 165.793,368.302 	"/>
                                                <polygon style="fill:#ACABB1;"
                                                         points="27.676,303.321 27.676,304.526 165.793,384.268 166.054,384.418 166.054,383.213 27.937,303.471 	"/>
                                                <polygon style="fill:#ACABB1;"
                                                         points="345.946,375.84 345.946,295.947 221.392,367.858 221.392,447.751 	"/>
                                            </g>
                                            <g>
                                                <polygon style="fill:#898890;"
                                                         points="345.946,344.46 345.946,343.254 221.392,415.164 221.392,416.371 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="221.392,399.199 221.392,400.405 345.946,328.494 345.946,327.288 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="221.392,383.234 221.392,384.439 345.946,312.528 345.946,311.321 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="345.946,360.426 345.946,359.22 221.392,431.131 221.392,432.337 	"/>
                                            </g>
                                            <polygon style="fill:#ACABB1;"
                                                     points="484.325,295.918 484.325,216.025 359.771,287.936 359.771,367.829 "/>
                                            <g>
                                                <polygon style="fill:#898890;"
                                                         points="359.771,319.277 359.771,320.483 484.325,248.572 484.325,247.366 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="484.325,280.504 484.325,279.298 359.771,351.21 359.771,352.416 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="484.325,264.538 484.325,263.332 359.771,335.243 359.771,336.45 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="359.771,303.311 359.771,304.517 484.325,232.607 484.325,231.4 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="194.268,175.671 194.038,175.387 84.43,184.478 82.364,185.67 82.394,186.036 193.422,176.827 303.826,313.407 305.04,312.707 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="221.943,159.693 221.714,159.409 112.105,168.499 110.04,169.691 110.07,170.057 	221.098,160.849 331.501,297.429 332.715,296.728 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="180.43,183.661 180.2,183.376 70.592,192.467 68.527,193.659 68.557,194.025	179.584,184.816 289.988,321.396 291.201,320.696 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="235.781,151.704 235.552,151.42 125.945,160.509 123.877,161.702 123.908,162.066	234.935,152.859 345.34,289.439 346.553,288.738 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="152.754,199.641 152.524,199.356 42.917,208.445 40.851,209.638 40.881,210.003	151.909,200.797 262.313,337.375 263.526,336.675 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="124.849,215.334 15.242,224.424 13.175,225.617 13.205,225.982 124.233,216.775	234.637,353.353 235.85,352.653 125.078,215.619 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="138.916,207.63 138.687,207.345 29.079,216.434 27.012,217.628 27.044,217.992	138.07,208.786 248.475,345.364 249.688,344.664 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="166.592,191.65 166.362,191.366 56.754,200.456 54.688,201.649 54.719,202.014	165.746,192.805 276.151,329.385 277.364,328.685 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="249.619,143.715 249.389,143.43 139.783,152.52 137.716,153.713 137.746,154.077	248.774,144.87 359.177,281.45 360.39,280.749 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="208.106,167.682 207.876,167.398 98.267,176.488 96.203,177.681 96.233,178.046	207.259,168.838 317.664,305.418 318.877,304.718 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="360.322,79.801 360.092,79.516 250.484,88.606 248.418,89.798 248.449,90.163 359.476,80.957 469.881,217.536 471.094,216.835 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="346.484,87.79 346.254,87.505 236.647,96.595 234.581,97.788 234.611,98.152	345.639,88.946 456.042,225.525 457.255,224.825 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="332.646,95.779 332.417,95.495 222.809,104.585 220.742,105.777 220.773,106.142	331.8,96.935 442.205,233.514 443.418,232.814 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="387.997,63.822 387.768,63.536 278.16,72.628 276.094,73.82 276.124,74.185 387.152,64.977	497.556,201.557 498.77,200.856 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="374.16,71.811 373.93,71.526 264.321,80.617 262.257,81.809 262.287,82.174 373.313,72.966	483.718,209.546 484.931,208.845 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="318.808,103.769 318.578,103.484 208.971,112.574 206.905,113.767 206.935,114.131	317.963,104.924 428.367,241.503 429.581,240.803 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="263.457,135.725 263.227,135.441 153.62,144.531 151.553,145.724 151.584,146.087	262.611,136.881 373.016,273.461 374.229,272.76 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="291.132,119.747 290.903,119.463 181.296,128.552 179.229,129.745 179.259,130.109	290.287,120.903 400.692,257.482 401.905,256.782 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="277.295,127.736 277.065,127.452 167.459,136.541 165.392,137.735 165.422,138.098	276.448,128.892 386.853,265.472 388.066,264.771 	"/>
                                                <polygon style="fill:#898890;"
                                                         points="304.971,111.758 304.741,111.474 195.133,120.563 193.067,121.756 193.098,122.12	304.124,112.913 414.529,249.492 415.743,248.792 	"/>
                                            </g>
                                            <g>
                                                <polygon style="fill:#2487FF;"
                                                         points="411.62,121.895 390.537,133.732 410.611,158.564 431.382,146.346 	"/>
                                                <polygon style="fill:#2487FF;"
                                                         points="380.316,139.968 359.234,151.804 379.307,176.637 400.079,164.418 "/>
                                            </g></svg>
                                        <span class="hide-menu">Омборлар</span>
                                    </a>
                                </li>

                                @php  // Warehouse Transfer (Омбор трансферлар) @endphp
                                 <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                    aria-expanded="false">
                                    <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                             xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 511.999 511.999"
                                             xml:space="preserve" fill="#000000" class="me-2"> <g id="SVGRepo_bgCarrier"
                                                                                                  stroke-width="0"/>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                               stroke-linejoin="round"/>
                                            <g id="SVGRepo_iconCarrier">
                                                <polygon style="fill:#FFC107;"
                                                         points="108.279,102.639 108.279,65.982 11.279,159.034 108.279,252.087 108.279,215.43 345.14,215.43 345.14,102.639 "/>
                                                <polygon style="fill:#00BCD4;"
                                                         points="403.72,409.36 403.72,446.017 500.72,352.964 403.72,259.912 403.72,296.569 166.859,296.569 166.859,409.36 "/>
                                                <g>
                                                    <path style="fill:#3d3846;"
                                                          d="M356.419,215.43V102.639c0-6.229-5.05-11.279-11.279-11.279H119.558V65.982 c0-4.52-2.698-8.603-6.855-10.376c-4.157-1.773-8.971-0.893-12.232,2.236l-97,93.052C1.253,153.023,0,155.962,0,159.034 s1.253,6.013,3.471,8.139l97,93.052c2.142,2.055,4.955,3.14,7.811,3.14c1.492,0,2.996-0.296,4.421-0.903 c4.157-1.773,6.855-5.856,6.855-10.376v-25.378H345.14C351.369,226.709,356.419,221.659,356.419,215.43z M333.861,204.151H108.279 C102.05,204.151,97,209.2,97,215.43v10.208l-69.428-66.603L97,92.433v10.206c0,6.229,5.05,11.279,11.279,11.279h225.581V204.151z"/>
                                                    <path style="fill:#3d3846;"
                                                          d="M508.528,344.825l-97-93.052c-3.262-3.129-8.077-4.007-12.232-2.236 c-4.157,1.773-6.855,5.856-6.855,10.376v25.378H166.859c-6.229,0-11.279,5.05-11.279,11.279v112.791 c0,6.229,5.05,11.279,11.279,11.279h225.581v25.378c0,4.52,2.698,8.603,6.855,10.376c1.427,0.608,2.93,0.905,4.421,0.903 c2.856,0,5.668-1.084,7.811-3.14l97-93.052c2.217-2.127,3.471-5.067,3.471-8.139S510.746,346.952,508.528,344.825z M415,419.567 V409.36c0-6.229-5.05-11.279-11.279-11.279H178.139v-90.233h225.581c6.229,0,11.279-5.05,11.279-11.279v-10.208l69.428,66.603 L415,419.567z"/>
                                                    <path style="fill:#3d3846;"
                                                          d="M278.131,341.685h-57.523c-6.229,0-11.279,5.05-11.279,11.279c0,6.229,5.05,11.279,11.279,11.279 h57.523c6.229,0,11.279-5.05,11.279-11.279C289.41,346.735,284.36,341.685,278.131,341.685z"/>
                                                    <path style="fill:#3d3846;"
                                                          d="M324.375,341.685h-5.64c-6.229,0-11.279,5.05-11.279,11.279c0,6.229,5.05,11.279,11.279,11.279h5.64 c6.229,0,11.279-5.05,11.279-11.279C335.654,346.735,330.604,341.685,324.375,341.685z"/>
                                                    <path style="fill:#3d3846;"
                                                          d="M239.782,170.313h57.523c6.229,0,11.279-5.05,11.279-11.279s-5.05-11.279-11.279-11.279h-57.523 c-6.229,0-11.279,5.05-11.279,11.279S233.552,170.313,239.782,170.313z"/>
                                                    <path style="fill:#3d3846;"
                                                          d="M193.538,170.313h5.64c6.229,0,11.279-5.05,11.279-11.279s-5.05-11.279-11.279-11.279h-5.64 c-6.229,0-11.279,5.05-11.279,11.279S187.308,170.313,193.538,170.313z"/>
                                                </g>
                                            </g>
                                        </svg>
                                    <span class="hide-menu">Омбор трансферлари</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse second-level ps-3">
                                        @php  // Output Transfer @endphp
                                        <li class="sidebar-item">
                                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                            href="{{ route('raw-material-transfer.index') }}" aria-expanded="false">
                                            <svg fill="#000000" width="25px" height="25px" viewBox="0 0 24 24" id="cursor-up-left" data-name="Flat Line" xmlns="http://www.w3.org/2000/svg" class="icon flat-line me-2">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"><path id="secondary" d="M3.1,4.46l7.21,15.92A1.17,1.17,0,0,0,12.5,20l1.26-6.23L20,12.5a1.17,1.17,0,0,0,.39-2.19L4.46,3.1A1,1,0,0,0,3.1,4.46Z" style="fill: #1c71d8; stroke-width: 2;"/><path id="primary" d="M3.1,4.46l7.21,15.92A1.17,1.17,0,0,0,12.5,20l1.26-6.23L20,12.5a1.17,1.17,0,0,0,.39-2.19L4.46,3.1A1,1,0,0,0,3.1,4.46Z" style="fill: none; stroke: #000000; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"/></g>
                                            </svg>
                                                <span class="hide-menu">Трансфер(чикиш)</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                            href="{{ route('raw-material-transfer-item.index') }}" aria-expanded="false">
                                                <svg height="25px" width="25px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.001 512.001" xml:space="preserve" fill="#000000" class="me-2">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <path style="fill:#BD6428;" d="M112,64.001h288c17.672,0,32,14.328,32,32v384c0,17.672-14.328,32-32,32H112 c-17.672,0-32-14.328-32-32v-384C80,78.329,94.328,64.001,112,64.001z"/> <path style="fill:#F5F6F6;" d="M120,480.001c-4.416,0-8-3.584-8-8v-368c0-4.416,3.584-8,8-8h272c4.416,0,8,3.584,8,8v288l-88,88H120 z"/> <path style="fill:#A35623;" d="M176,96.001h-16v-16c0-8.84,7.16-16,16-16l0,0V96.001z"/> <g> <path style="fill:#9BA7AF;" d="M164,224.001h184c6.624,0,12,5.376,12,12l0,0c0,6.624-5.376,12-12,12H164c-6.624,0-12-5.376-12-12 l0,0C152,229.377,157.376,224.001,164,224.001z"/> <path style="fill:#9BA7AF;" d="M164,272.001h184c6.624,0,12,5.376,12,12l0,0c0,6.624-5.376,12-12,12H164c-6.624,0-12-5.376-12-12 l0,0C152,277.377,157.376,272.001,164,272.001z"/> <path style="fill:#9BA7AF;" d="M164,320.001h184c6.624,0,12,5.376,12,12l0,0c0,6.624-5.376,12-12,12H164c-6.624,0-12-5.376-12-12 l0,0C152,325.377,157.376,320.001,164,320.001z"/> <path style="fill:#9BA7AF;" d="M164,368.001h104c6.624,0,12,5.376,12,12l0,0c0,6.624-5.376,12-12,12H164c-6.624,0-12-5.376-12-12 l0,0C152,373.377,157.376,368.001,164,368.001z"/> <path style="fill:#9BA7AF;" d="M204,160.001h104c6.624,0,12,5.376,12,12l0,0c0,6.624-5.376,12-12,12H204c-6.624,0-12-5.376-12-12 l0,0C192,165.377,197.376,160.001,204,160.001z"/> </g> <path style="fill:#CECECE;" d="M400,392.001h-80c-4.416,0-8,3.584-8,8v80L400,392.001z"/> <path style="fill:#D17F4D;" d="M88,88.001L88,88.001c4.416,0,8,3.584,8,8v264c0,4.416-3.584,8-8,8l0,0c-4.416,0-8-3.584-8-8v-264 C80,91.585,83.584,88.001,88,88.001z"/> <g> <path style="fill:#E9E9E9;" d="M304,408.001v16c0,4.416-3.584,8-8,8l0,0c-4.416,0-8,3.584-8,8s-3.584,8-8,8h-8 c-4.416,0-8,3.584-8,8s-3.584,8-8,8H112v8c0,4.416,3.584,8,8,8h192v-80C307.584,400.001,304,403.585,304,408.001z"/> <path style="fill:#E9E9E9;" d="M392,96.001H160c0.128,2.4,0.128,4.8,0,7.2c-0.44,8.824,6.352,16.336,15.176,16.776 c0.28,0.016,0.552,0.024,0.824,0.024h192c8.84,0,16,7.16,16,16v24l0,0c0,8.84,7.16,16,16,16l0,0v-72 C400,99.585,396.416,96.001,392,96.001z"/> </g> <path style="fill:#9BA7AF;" d="M308.72,45.121c-6.96-2.872-12.232-8.76-14.32-16c-6.184-21.208-28.392-33.384-49.6-27.2 c-13.12,3.824-23.376,14.08-27.2,27.2c-2.088,7.24-7.36,13.128-14.32,16l-17.2,6.88c-6.064,2.416-10.056,8.272-10.08,14.8v29.2 c0,8.84,7.16,16,16,16h128c8.84,0,16-7.16,16-16v-29.2c-0.024-6.528-4.016-12.384-10.08-14.8L308.72,45.121z M256,56.001 c-8.84,0-16-7.16-16-16s7.16-16,16-16c8.84,0,16,7.16,16,16S264.84,56.001,256,56.001z"/> <path style="fill:#72818B;" d="M312,64.001c-4.416,0-8,3.584-8,8l0,0c0,4.416-3.584,8-8,8h-16c-4.416,0-8,3.584-8,8l0,0 c0,4.416-3.584,8-8,8h-88c0,8.84,7.16,16,16,16h128c8.84,0,16-7.16,16-16v-32H312z"/> <path style="fill:#AFBABF;" d="M225.04,40.001c-4.416,0.144-8.112-3.328-8.248-7.744c-0.024-0.76,0.064-1.52,0.248-2.256 c4.584-17.744,20.632-30.104,38.96-30c4.416,0,8,3.584,8,8s-3.584,8-8,8c-10.96-0.016-20.544,7.392-23.28,18 C231.816,37.513,228.664,39.969,225.04,40.001z"/> <path style="fill:#A35623;" d="M400,80.001h-32c-8.84,0-16-7.16-16-16h-16v32h56c4.416,0,8,3.584,8,8v288l-88,88H120 c0,8.84,7.16,16,16,16h194.72c8.488,0.008,16.632-3.36,22.64-9.36l53.28-53.28c6-6.008,9.368-14.152,9.36-22.64V96.001 C416,87.169,408.84,80.001,400,80.001z"/> </g>
                                                </svg>
                                                <span class="hide-menu">Трансфер элементлари</span>
                                            </a>
                                        </li>

                                        @php  // Input Transfer @endphp
                                        <li class="sidebar-item">
                                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                            href="{{ route('log.material') }}" aria-expanded="false">
                                                <svg fill="#000000" width="25px" height="25px" viewBox="0 0 24 24" id="cursor-down-right" data-name="Flat Line" xmlns="http://www.w3.org/2000/svg" class="icon flat-line me-2">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"><path id="secondary" d="M20.9,19.54,13.69,3.62A1.17,1.17,0,0,0,11.5,4l-1.26,6.23L4,11.5a1.17,1.17,0,0,0-.39,2.19L19.54,20.9A1,1,0,0,0,20.9,19.54Z" style="fill: #e66100; stroke-width: 2;"/><path id="primary" d="M20.9,19.54,13.69,3.62A1.17,1.17,0,0,0,11.5,4l-1.26,6.23L4,11.5a1.17,1.17,0,0,0-.39,2.19L19.54,20.9A1,1,0,0,0,20.9,19.54Z" style="fill: none; stroke: #241f31; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"/>
                                                </g>
                                                </svg>
                                                <span class="hide-menu">Хомашёлар</span>
                                            </a>
                                        </li>
                                        <li class="sidebar-item">
                                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                            href="{{ route('log.product') }}" aria-expanded="false">
                                                <svg fill="#000000" width="25px" height="25px" viewBox="0 0 24 24" id="cursor-down-right" data-name="Flat Line" xmlns="http://www.w3.org/2000/svg" class="icon flat-line me-2">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"><path id="secondary" d="M20.9,19.54,13.69,3.62A1.17,1.17,0,0,0,11.5,4l-1.26,6.23L4,11.5a1.17,1.17,0,0,0-.39,2.19L19.54,20.9A1,1,0,0,0,20.9,19.54Z" style="fill: #2ec27e; stroke-width: 2;"/><path id="primary" d="M20.9,19.54,13.69,3.62A1.17,1.17,0,0,0,11.5,4l-1.26,6.23L4,11.5a1.17,1.17,0,0,0-.39,2.19L19.54,20.9A1,1,0,0,0,20.9,19.54Z" style="fill: none; stroke: #241f31; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"/></g>
                                                </svg>
                                            <span class="hide-menu">Махсулотлар</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                @php // Raw Materials (Хомашёлар) @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                       aria-expanded="false">
                                        <span class="hide-menu">📥 Хомашёлар</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse second-level ps-3">
                                        @php // Raw Material @endphp
                                        <li class="sidebar-item">
                                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                               href="{{ route('raw-material.index') }}" aria-expanded="false">
                                                <svg width="25px" height="25px" viewBox="0 0 64.00 64.00" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg" stroke="#000000"
                                                     stroke-width="0.00064" class="me-2">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                       stroke-linejoin="round" stroke="#CCCCCC" stroke-width="12.8">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M24.2156 20.184L28.029 20.8593L31.4912 19.0336L34.552 21.4095H38.4406L40.0463 24.9358L43.4833 26.7115L43.2576 30.588L45.4653 33.7642L43.4332 37.0904L43.9098 40.9419L40.548 42.9427L39.1682 46.5941L35.2795 46.8692L32.3442 49.4701L28.8068 47.8945L25.0184 48.8699L22.61 45.8438L18.8216 44.9434L18.1192 41.117L15.2089 38.566L16.363 34.8396L14.9832 31.2132L17.7429 28.4622L18.2195 24.6107L21.9326 23.4102L24.2156 20.184Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M26.1976 24.9858C27.4269 24.5107 28.7064 24.2856 30.011 24.3106C31.3156 24.3356 32.5951 24.6357 33.7993 25.1609C35.0036 25.6861 36.0573 26.4614 36.9604 27.4118C37.8636 28.3621 38.5661 29.4876 39.0177 30.688C40.0965 33.0889 40.1717 35.84 39.2435 38.3159C38.3152 40.7919 36.4336 42.7926 34.0502 43.893C32.8209 44.3682 31.5414 44.5933 30.2368 44.5683C28.9322 44.5433 27.6527 44.2432 26.4485 43.718C25.2442 43.1928 24.1905 42.4175 23.2874 41.4921C22.3842 40.5418 21.6817 39.4413 21.2301 38.2159C20.1513 35.815 20.0761 33.0639 21.0043 30.588C21.9075 28.112 23.7891 26.0863 26.1976 24.9858Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M44.2862 25.8112L14.0549 38.566L16.7142 44.9684L46.9706 32.2136L44.2862 25.8112Z"
                                                              fill="#2A2941"/>
                                                        <path
                                                            d="M20.051 42.3174L18.7966 42.8427C18.7213 42.8927 18.646 42.8927 18.5708 42.9177C18.4955 42.9177 18.4202 42.9177 18.345 42.8677C18.2697 42.8426 18.2195 42.7926 18.1694 42.7176C18.1192 42.6426 18.0941 42.5926 18.069 42.5175L16.6892 39.2663C16.639 39.1162 16.639 38.9662 16.6892 38.8161C16.7142 38.7411 16.7644 38.6911 16.8397 38.6411C16.8899 38.591 16.9651 38.566 17.0404 38.541L18.4202 37.9658C18.5959 37.8908 18.7715 37.8157 18.9471 37.7907C19.0976 37.7657 19.2481 37.7657 19.3987 37.7907C19.5241 37.8157 19.6496 37.8658 19.7499 37.9158C19.8503 37.9658 19.9757 38.0658 20.051 38.1409C20.1513 38.2409 20.2015 38.3659 20.2517 38.491C20.352 38.6911 20.3771 38.9162 20.3269 39.1412C20.2768 39.3663 20.1764 39.5664 20.0259 39.7165C20.2768 39.6664 20.5527 39.7165 20.7785 39.8415C21.0043 39.9666 21.1799 40.1666 21.2803 40.4167C21.3806 40.6418 21.4308 40.8919 21.3806 41.142C21.3556 41.3671 21.2301 41.5922 21.0796 41.7672C20.9541 41.8923 20.8287 41.9923 20.7033 42.0674L20.051 42.3174ZM19.198 40.4167L18.3199 40.7919L18.8467 42.0674L19.7499 41.6922C20.3269 41.4671 20.5026 41.117 20.352 40.6918C20.3269 40.5918 20.2768 40.5168 20.2015 40.4417C20.1262 40.3667 20.0259 40.3167 19.9255 40.3167C19.6746 40.2667 19.4238 40.3167 19.198 40.4167ZM17.6174 39.0412L18.069 40.1666L18.8467 39.8415C19.0224 39.7915 19.1729 39.6915 19.2983 39.5914C19.3987 39.4914 19.4489 39.3663 19.4739 39.2413C19.499 39.1412 19.499 39.0412 19.4739 38.9412C19.4489 38.8411 19.3987 38.7661 19.3234 38.7161C19.2481 38.6661 19.1729 38.616 19.0725 38.616C18.8216 38.6411 18.5708 38.6911 18.345 38.8161L17.6174 39.0412Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M24.2658 38.366L23.9647 38.491L24.5418 39.8666C24.5919 40.0166 24.5919 40.1667 24.5418 40.3167C24.5167 40.3668 24.4916 40.4418 24.4414 40.4668C24.3912 40.5168 24.341 40.5418 24.2909 40.5668C24.2407 40.5918 24.1654 40.6169 24.1153 40.6169C24.0651 40.6169 23.9898 40.5918 23.9396 40.5668C23.8142 40.4918 23.7138 40.3668 23.6637 40.2167L22.2838 36.8904C22.2336 36.7404 22.2336 36.5903 22.2838 36.4403C22.3089 36.3652 22.3591 36.3152 22.4343 36.2652C22.4845 36.2152 22.5598 36.1902 22.6351 36.1652L24.0149 35.5899C24.1654 35.5149 24.341 35.4649 24.4916 35.4149C24.617 35.3899 24.7675 35.3899 24.9181 35.4149C25.0686 35.4149 25.2191 35.4649 25.3697 35.5149C25.5202 35.5899 25.6456 35.69 25.746 35.79C25.8463 35.9151 25.9467 36.0651 25.9969 36.2152C26.0972 36.4903 26.0972 36.8154 25.9969 37.0905C25.8714 37.3906 25.6456 37.6657 25.3697 37.8408C25.5704 37.8658 25.7711 37.9408 25.9467 38.0409C26.1474 38.1409 26.3481 38.266 26.5237 38.391C26.6993 38.491 26.8499 38.6411 26.9753 38.7661C27.0506 38.8412 27.1258 38.9412 27.2011 39.0162C27.2262 39.0913 27.2262 39.1413 27.2011 39.2163C27.2011 39.2914 27.1509 39.3414 27.1258 39.4164C27.0757 39.4914 27.0004 39.5414 26.9251 39.5665C26.8499 39.5915 26.7495 39.5915 26.6743 39.5665C26.599 39.5414 26.4986 39.5164 26.4485 39.4664C26.3481 39.3914 26.2227 39.3164 26.1223 39.2413L25.5453 38.7912C25.3697 38.6661 25.194 38.5411 25.0184 38.441C24.893 38.366 24.7675 38.341 24.6421 38.341C24.4916 38.341 24.3661 38.391 24.2407 38.441L24.2658 38.366ZM23.9898 36.3652L23.2121 36.6904L23.7138 37.8908L24.4665 37.5657C24.6421 37.4907 24.7926 37.4156 24.9432 37.2906C25.0686 37.2156 25.1439 37.0905 25.194 36.9655C25.2442 36.8404 25.2442 36.6904 25.194 36.5403C25.1439 36.4403 25.0686 36.3402 24.9683 36.2652C24.8679 36.1902 24.7425 36.1652 24.6421 36.1652C24.4163 36.1902 24.1654 36.2402 23.9647 36.3402H23.9898V36.3652Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M31.9679 36.7153L31.5414 36.2651L29.8605 36.9904V37.6156C29.8856 37.7907 29.8856 37.9658 29.8605 38.1409C29.8354 38.1909 29.8103 38.2409 29.7852 38.2659C29.735 38.3159 29.71 38.3409 29.6598 38.3409C29.6096 38.3659 29.5594 38.3659 29.4842 38.3659C29.434 38.3659 29.3587 38.3659 29.3085 38.3409C29.2584 38.3159 29.2082 38.2909 29.158 38.2659C29.1078 38.2159 29.0827 38.1909 29.0577 38.1409C29.0326 38.0658 29.0326 38.0158 29.0577 37.9408C29.0577 37.8657 29.0577 37.7657 29.0577 37.6407L28.9573 34.3894C28.9573 34.2894 28.9573 34.1893 28.9573 34.0393C28.9573 33.9393 28.9573 33.8142 28.9573 33.7142C28.9824 33.6141 29.0075 33.5391 29.0827 33.4641C29.1329 33.364 29.2333 33.314 29.3336 33.264C29.434 33.239 29.5594 33.239 29.6598 33.264C29.7601 33.264 29.8354 33.314 29.9107 33.364L30.1365 33.5391L30.3873 33.8392L32.6202 36.1651C32.7456 36.2901 32.846 36.4152 32.9463 36.5903C32.9714 36.6403 32.9714 36.6903 32.9714 36.7403C32.9714 36.7903 32.9714 36.8404 32.9463 36.8904C32.9212 36.9404 32.8962 36.9904 32.846 37.0404C32.7958 37.0905 32.7456 37.1155 32.6954 37.1405H32.4947C32.4446 37.1655 32.3944 37.1655 32.3442 37.1405C32.2689 37.0905 32.2188 37.0404 32.1435 36.9904L31.9679 36.7153ZM29.8103 36.2151L31.0647 35.6899L29.6849 34.1643L29.8103 36.2151Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M34.6021 31.6383L37.2866 33.489L36.2329 30.9381C36.1827 30.788 36.1827 30.6379 36.2329 30.5129C36.258 30.4629 36.2831 30.4129 36.3081 30.3628C36.3583 30.3128 36.3834 30.2878 36.4587 30.2628C36.5089 30.2378 36.5841 30.2378 36.6343 30.2378C36.6845 30.2378 36.7597 30.2378 36.8099 30.2628C36.9354 30.3378 37.0106 30.4629 37.0608 30.5879L38.4406 33.9892C38.5912 34.3644 38.5159 34.6145 38.2148 34.7395H37.9891C37.9138 34.7645 37.8385 34.7645 37.7883 34.7395L37.5626 34.6395L37.3368 34.4894L34.7025 32.6387L35.7562 35.1647C35.8064 35.2897 35.8064 35.4398 35.7562 35.5898C35.7311 35.6398 35.706 35.6899 35.6559 35.7399C35.6057 35.7899 35.5555 35.8149 35.5053 35.8399C35.4551 35.8649 35.3799 35.8649 35.3297 35.8649C35.2795 35.8649 35.2043 35.8399 35.1541 35.8149C35.0286 35.7399 34.9534 35.6148 34.9032 35.4898L33.5234 32.1635C33.4732 32.0385 33.4481 31.9384 33.4481 31.8134C33.4481 31.7134 33.4732 31.6133 33.5234 31.5133C33.5735 31.4132 33.6488 31.3632 33.7492 31.3132C33.8244 31.2882 33.8997 31.2882 33.9499 31.3132C34 31.2882 34.0753 31.2882 34.1255 31.3132C34.2007 31.3382 34.2509 31.3882 34.3262 31.4132L34.6021 31.6383Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M39.6951 28.9623L40.7738 28.5122C41.0247 28.4121 41.2756 28.3371 41.5265 28.2871C41.7523 28.2621 42.0032 28.2871 42.229 28.3621C42.5551 28.4872 42.8311 28.6872 43.082 28.9623C43.3328 29.2375 43.5085 29.5126 43.6088 29.8627C43.7092 30.0878 43.7844 30.3379 43.8346 30.588C43.8597 30.8131 43.8597 31.0381 43.8346 31.2382C43.8095 31.4633 43.7593 31.6634 43.6841 31.8634C43.6088 32.0135 43.5335 32.1386 43.4332 32.2636C43.3328 32.3886 43.2074 32.4887 43.0569 32.5887L42.5551 32.8388L41.4763 33.314C41.3509 33.364 41.2254 33.389 41.1 33.389C40.9996 33.364 40.9244 33.314 40.8742 33.239C40.7989 33.1389 40.7488 33.0389 40.6986 32.9138L39.3187 29.7376C39.2686 29.5876 39.2686 29.4375 39.3187 29.2875C39.3689 29.2124 39.4191 29.1374 39.4943 29.0874C39.5445 29.0124 39.6198 28.9623 39.6951 28.9623ZM40.3223 29.5126L41.5265 32.4387L42.1537 32.1636L42.4798 32.0135L42.7056 31.8634C42.7809 31.7884 42.8311 31.7134 42.8813 31.6384C42.9565 31.4133 43.0067 31.1382 42.9816 30.8881C42.9565 30.638 42.8813 30.3879 42.7809 30.1628C42.6555 29.8377 42.4548 29.5126 42.2039 29.2875C42.0283 29.1374 41.8025 29.0624 41.5767 29.0874C41.3258 29.1124 41.1 29.1874 40.8742 29.2875L40.3223 29.5126Z"
                                                            fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M26.2979 17.1329L29.9859 17.7581L33.3226 16.0074L36.283 18.2833H40.0463L41.6017 21.6596L44.9385 23.3602L44.7127 27.0616L46.8452 30.1128L44.8883 33.314L45.3399 36.9904L42.1035 38.9412L40.7236 42.4425L36.9604 42.6926L34.2007 45.1935L30.7887 43.718L27.1258 44.6433L24.8177 41.7172L21.1549 40.8669L20.4775 37.2155L17.7178 34.7896L18.8467 31.2132L17.4669 27.7369L20.1262 25.0859L20.6029 21.3845L24.1905 20.2341L26.2979 17.1329Z"
                                                              fill="#FEC34E"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M26.5237 15.9324L25.7209 15.7824L23.3626 19.1586L19.3987 20.4091L18.8969 24.5357L15.9114 27.4868L17.3916 31.3133L16.1372 35.1897L19.2482 37.9408L20.0008 41.9673L24.04 42.9177L26.599 46.1189L30.6382 45.0935L34.4265 46.7441L37.5375 43.9931L41.677 43.718L43.1572 39.8665L46.7197 37.7157L46.218 33.6642L48.3756 30.1378L46.0424 26.6615L46.2932 22.5349L42.6304 20.6592L40.9244 16.9328H36.7848L33.4732 14.5569L29.8103 16.5076L26.5237 15.9324ZM26.298 17.2079L29.9859 17.8331L33.3226 16.0825L36.2831 18.3583H40.0463L41.6017 21.7346L44.9385 23.4353L44.7127 27.1367L46.8452 30.1878L44.8883 33.389L45.3399 37.0654L42.1035 39.0162L40.7237 42.5175L36.9604 42.7676L34.2007 45.2686L30.7887 43.793L27.1259 44.7184L24.8177 41.7922L21.1549 40.8919L20.4775 37.2405L17.7178 34.8146L18.8468 31.2382L17.4669 27.7619L20.1263 25.1359L20.6029 21.4345L24.1905 20.2841L26.298 17.2079Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M28.2297 21.7096C30.011 20.8593 31.993 20.5591 33.9498 20.8343C35.9067 21.1094 37.7131 21.9847 39.1682 23.3102C40.6233 24.6357 41.6519 26.3614 42.1035 28.2871C42.5551 30.1878 42.4296 32.2136 41.7272 34.0393C41.0247 35.865 39.7954 37.4656 38.1897 38.591C36.5841 39.7164 34.6523 40.3417 32.6954 40.3917C30.7385 40.4417 28.7817 39.8665 27.1258 38.7911C25.47 37.7157 24.1905 36.1651 23.4128 34.3644C22.3842 32.0385 22.3089 29.4375 23.2121 27.0616C24.1153 24.6857 25.9216 22.76 28.2297 21.7096Z"
                                                              fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M27.728 20.5341C25.6457 21.2844 23.8142 22.66 22.5347 24.4606C21.2301 26.2613 20.5026 28.4121 20.4273 30.613C20.352 32.8388 20.9542 35.0147 22.1584 36.8904C23.3375 38.7661 25.0686 40.2416 27.1008 41.142C29.1329 42.0423 31.3908 42.2924 33.5735 41.8923C35.7562 41.4921 37.7883 40.4417 39.3689 38.8911C40.9495 37.3405 42.0282 35.3398 42.4798 33.1639C42.9314 30.9881 42.7056 28.7373 41.8526 26.6865C40.7989 24.0105 38.7166 21.8596 36.0823 20.7092C33.3979 19.5588 30.4124 19.4837 27.728 20.5341ZM28.2297 21.7096C30.011 20.8593 31.993 20.5592 33.9498 20.8343C35.9067 21.1094 37.7131 21.9847 39.1682 23.3102C40.6233 24.6357 41.6268 26.3864 42.1035 28.2871C42.5802 30.1878 42.4297 32.2136 41.7272 34.0393C41.0247 35.865 39.7954 37.4656 38.1898 38.591C36.5841 39.7164 34.6523 40.3417 32.6954 40.3917C30.7135 40.4167 28.7817 39.8665 27.1259 38.7911C25.47 37.7157 24.1905 36.1651 23.4128 34.3644C22.3842 32.0385 22.3089 29.4375 23.2121 27.0616C24.1153 24.6857 25.9216 22.785 28.2297 21.7096Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M45.7413 22.5099L16.4885 34.7145L19.0725 40.8419L48.3003 28.6372L45.7413 22.5099Z"
                                                              fill="#A694FE"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M49.9812 29.3375L46.4187 20.8342L14.8076 34.0142L18.4453 42.5175L49.9812 29.3375ZM45.7413 22.4598L16.4885 34.6645L19.0726 40.7918L48.3003 28.5872L45.7413 22.4598Z"
                                                              fill="#2A2941"/>
                                                        <path
                                                            d="M22.2838 38.316L21.0545 38.8161C20.9792 38.8412 20.904 38.8662 20.8287 38.8662C20.7535 38.8662 20.6782 38.8412 20.6029 38.8161C20.5026 38.8161 20.4022 38.6411 20.327 38.466L19.0725 35.3898C19.0224 35.2398 19.0224 35.0897 19.0725 34.9397C19.0725 34.8146 19.2231 34.7396 19.3987 34.6396L20.7033 34.1144C20.8789 34.0393 21.0294 33.9893 21.205 33.9393C21.3556 33.9143 21.5061 33.9143 21.6566 33.9393C21.7821 33.9643 21.8824 34.0143 21.9828 34.0643C22.0831 34.1144 22.1835 34.1894 22.2838 34.2894C22.3591 34.3895 22.4344 34.4895 22.4845 34.6145C22.5598 34.8146 22.61 35.0147 22.5598 35.2398C22.5096 35.4399 22.4093 35.6399 22.2587 35.79C22.5096 35.74 22.7605 35.79 22.9863 35.915C23.2121 36.0401 23.3877 36.2402 23.463 36.4653C23.5633 36.6903 23.5884 36.9404 23.5633 37.1655C23.5132 37.3906 23.4128 37.6157 23.2623 37.7657C23.1619 37.8658 23.0365 37.9658 22.886 38.0409C22.6852 38.1409 22.5096 38.2409 22.2838 38.316ZM21.4559 36.4903L20.6029 36.8404L21.1047 38.0409L21.9828 37.6907C22.5096 37.4656 22.7103 37.1405 22.5598 36.7404C22.5347 36.6403 22.4845 36.5653 22.4093 36.4903C22.334 36.4152 22.2587 36.3902 22.1584 36.3652C21.9075 36.3402 21.6817 36.3902 21.4559 36.4903ZM19.9255 35.1147L20.3771 36.1901L21.1298 35.89C21.2803 35.815 21.4308 35.74 21.5814 35.6399C21.6315 35.5899 21.6566 35.5399 21.7068 35.4899C21.7319 35.4399 21.757 35.3648 21.757 35.3148C21.7821 35.2148 21.7821 35.1147 21.757 35.0147C21.6566 34.7896 21.5312 34.6896 21.3807 34.6896C21.1298 34.6896 20.904 34.7646 20.6782 34.8646L19.9255 35.1147Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M26.3481 34.5394L26.0722 34.6395L26.6241 36.015C26.6492 36.09 26.6743 36.165 26.6743 36.2401C26.6743 36.3151 26.6492 36.3901 26.6241 36.4652C26.599 36.5152 26.5739 36.5652 26.5237 36.6152C26.4736 36.6652 26.4234 36.6902 26.3732 36.7153C26.323 36.7403 26.2729 36.7653 26.1976 36.7653C26.1474 36.7653 26.0722 36.7403 26.022 36.7153C25.8965 36.6402 25.7962 36.5152 25.746 36.3651L24.3662 33.1889C24.316 33.0389 24.316 32.8888 24.3662 32.7387C24.4414 32.6137 24.5669 32.5137 24.6923 32.4636L26.0722 31.9134C26.2227 31.8384 26.3732 31.7884 26.5488 31.7384C26.6743 31.7134 26.8248 31.7134 26.9502 31.7384C27.1008 31.7384 27.2513 31.7884 27.3767 31.8384C27.5022 31.8884 27.6276 31.9885 27.7531 32.0885C27.8534 32.2135 27.9287 32.3386 28.0039 32.4636C28.1043 32.7387 28.1043 33.0389 28.0039 33.314C27.9287 33.2889 27.8534 33.2639 27.7782 33.2639C27.7029 33.2639 27.6276 33.289 27.5524 33.339C27.4771 33.389 27.4269 33.439 27.4018 33.514C27.3517 33.5891 27.3516 33.6641 27.3516 33.7391C27.3516 33.8142 27.3767 33.8892 27.4018 33.9642C27.452 34.0392 27.5022 34.0893 27.5524 34.1393C27.6025 34.1893 27.7029 34.2143 27.7782 34.2143C27.8534 34.2143 27.9287 34.2143 28.0039 34.1643C28.2046 34.2643 28.3803 34.3894 28.581 34.5144C28.7315 34.6145 28.882 34.7395 29.0075 34.8645C29.0827 34.9396 29.158 35.0146 29.2082 35.1146C29.2333 35.1647 29.2333 35.2397 29.2082 35.2897C29.1831 35.3647 29.158 35.4398 29.1078 35.4898C29.0576 35.5398 29.0075 35.5898 28.9322 35.6148C28.8569 35.6398 28.7566 35.6398 28.6813 35.6148C28.6061 35.5898 28.5308 35.5648 28.4555 35.5148L28.1545 35.2897L27.5774 34.8645C27.4269 34.7395 27.2513 34.6145 27.0757 34.5144C26.9502 34.4644 26.8248 34.4144 26.6994 34.4144C26.5739 34.4144 26.4234 34.4394 26.323 34.4894L26.3481 34.5394ZM26.0972 32.5887L25.3446 32.9138L25.8213 34.0642L26.5488 33.7391C26.7244 33.6891 26.875 33.5891 27.0255 33.489C27.1259 33.414 27.2011 33.314 27.2513 33.1889C27.3015 33.0639 27.3015 32.9138 27.2513 32.7888C27.2011 32.6887 27.1509 32.5887 27.0506 32.5387C26.9502 32.4886 26.8499 32.4386 26.7244 32.4636C26.4987 32.4636 26.2979 32.5137 26.0972 32.5887Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M33.7993 32.9639L33.3728 32.5138L31.7421 33.189V33.8143C31.7672 33.9893 31.7672 34.1394 31.7421 34.3144C31.7421 34.4145 31.6417 34.4895 31.5414 34.5395C31.4912 34.5645 31.441 34.5645 31.3908 34.5645C31.3407 34.5645 31.2905 34.5645 31.2403 34.5395C31.1901 34.5145 31.14 34.4895 31.0898 34.4645C31.0396 34.4395 31.0145 34.3895 30.9894 34.3395C30.9644 34.2644 30.9644 34.2144 30.9894 34.1394C30.9894 34.0393 30.9894 33.9393 30.9894 33.8393L30.8891 30.7381V30.413C30.864 30.3129 30.864 30.1879 30.8891 30.0878C30.9142 29.9878 30.9393 29.9128 30.9894 29.8377C31.0647 29.7627 31.14 29.6877 31.2403 29.6627C31.3407 29.6377 31.441 29.6377 31.5665 29.6627C31.6668 29.6627 31.7421 29.7127 31.8173 29.7627C31.8926 29.8127 31.9679 29.8628 32.0181 29.9378L32.2689 30.1879L34.4265 32.4137C34.552 32.5388 34.6523 32.6638 34.7276 32.8139C34.7527 32.8639 34.7527 32.9139 34.7527 32.9639C34.7527 33.014 34.7527 33.064 34.7276 33.114C34.7025 33.164 34.6774 33.214 34.6272 33.264C34.5771 33.3141 34.5269 33.3391 34.4767 33.3641C34.4265 33.3891 34.3513 33.3891 34.3011 33.3641C34.2509 33.3641 34.2007 33.3641 34.1506 33.3641L33.9749 33.214L33.7993 32.9639ZM31.717 32.4888L32.9212 31.9886L31.5414 30.538L31.717 32.4888Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M36.3834 28.087L38.9675 29.8877L37.9389 27.4367C37.8887 27.3117 37.8887 27.1616 37.9389 27.0366C37.964 26.9866 37.9891 26.9365 38.0141 26.8865C38.0643 26.8365 38.0894 26.8115 38.1396 26.7865C38.1898 26.7615 38.2399 26.7615 38.3152 26.7615C38.3654 26.7615 38.4406 26.7615 38.4908 26.7865C38.6163 26.8615 38.6915 26.9866 38.7417 27.1116L40.1215 30.3628C40.2721 30.713 40.1968 30.9631 39.9208 31.0881C39.8456 31.1131 39.7703 31.1131 39.695 31.0881C39.6198 31.1131 39.5445 31.1131 39.4943 31.0881C39.4191 31.0631 39.3438 31.0381 39.2936 30.9881L39.0929 30.838L36.4085 29.0874L37.4371 31.5133C37.4873 31.6383 37.4873 31.7884 37.4371 31.9134C37.412 31.9635 37.3869 32.0135 37.3618 32.0635C37.3117 32.1135 37.2866 32.1385 37.2364 32.1635C37.1862 32.1885 37.1361 32.2135 37.0608 32.2135C37.0106 32.2135 36.9353 32.1885 36.8852 32.1635C36.7597 32.0885 36.6845 31.9885 36.6343 31.8384L35.2293 28.6372C35.1792 28.5371 35.1541 28.4121 35.129 28.3121C35.129 28.212 35.1541 28.112 35.2043 28.037C35.2544 27.9619 35.3548 27.8869 35.4301 27.8369H35.6308C35.6809 27.8119 35.7562 27.8119 35.8064 27.8369C35.8816 27.8619 35.9318 27.9119 36.0071 27.9369L36.3834 28.087Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M41.3007 25.5611L42.3544 25.1109C42.6053 25.0109 42.8311 24.9359 43.107 24.8858C43.3328 24.8608 43.5586 24.8858 43.7593 24.9609C44.0855 25.0859 44.3615 25.261 44.6123 25.4861C44.8632 25.7362 45.0388 26.0113 45.1392 26.3364C45.2395 26.5615 45.3148 26.7866 45.365 27.0367C45.3901 27.2367 45.3901 27.4618 45.365 27.6619C45.3399 27.862 45.2897 28.0621 45.2145 28.2371C45.1392 28.3872 45.0639 28.5122 44.9636 28.6123C44.8632 28.7373 44.7378 28.8373 44.6123 28.9124L44.1357 29.1625L43.082 29.6126C42.9816 29.6627 42.8562 29.6877 42.7307 29.6877C42.6805 29.6877 42.6304 29.6627 42.6053 29.6377C42.5551 29.6126 42.53 29.5876 42.5049 29.5376C42.4297 29.4376 42.3795 29.3375 42.3293 29.2375L41.0498 26.1863C40.9996 26.0363 40.9996 25.8862 41.0498 25.7362C41.0749 25.6611 41.1251 25.6111 41.1752 25.5611C41.2254 25.5111 41.3007 25.4861 41.376 25.4611L41.3007 25.5611ZM41.8777 26.1113L43.0569 28.8624L43.659 28.6123L43.9851 28.4622C44.0604 28.4372 44.1357 28.3872 44.2109 28.3121C44.2862 28.2371 44.3364 28.1621 44.3615 28.0871C44.4367 27.862 44.4869 27.6369 44.4618 27.3868C44.4367 27.1367 44.3615 26.9116 44.2611 26.7115C44.1357 26.3864 43.96 26.1113 43.7092 25.8612C43.5335 25.7112 43.3328 25.6611 43.107 25.6611C42.8812 25.6861 42.6555 25.7612 42.4297 25.8362L41.9028 26.0613V26.1113H41.8777Z"
                                                            fill="#2A2941"/>
                                                    </g>
                                                    <g id="SVGRepo_iconCarrier">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M24.2156 20.184L28.029 20.8593L31.4912 19.0336L34.552 21.4095H38.4406L40.0463 24.9358L43.4833 26.7115L43.2576 30.588L45.4653 33.7642L43.4332 37.0904L43.9098 40.9419L40.548 42.9427L39.1682 46.5941L35.2795 46.8692L32.3442 49.4701L28.8068 47.8945L25.0184 48.8699L22.61 45.8438L18.8216 44.9434L18.1192 41.117L15.2089 38.566L16.363 34.8396L14.9832 31.2132L17.7429 28.4622L18.2195 24.6107L21.9326 23.4102L24.2156 20.184Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M26.1976 24.9858C27.4269 24.5107 28.7064 24.2856 30.011 24.3106C31.3156 24.3356 32.5951 24.6357 33.7993 25.1609C35.0036 25.6861 36.0573 26.4614 36.9604 27.4118C37.8636 28.3621 38.5661 29.4876 39.0177 30.688C40.0965 33.0889 40.1717 35.84 39.2435 38.3159C38.3152 40.7919 36.4336 42.7926 34.0502 43.893C32.8209 44.3682 31.5414 44.5933 30.2368 44.5683C28.9322 44.5433 27.6527 44.2432 26.4485 43.718C25.2442 43.1928 24.1905 42.4175 23.2874 41.4921C22.3842 40.5418 21.6817 39.4413 21.2301 38.2159C20.1513 35.815 20.0761 33.0639 21.0043 30.588C21.9075 28.112 23.7891 26.0863 26.1976 24.9858Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M44.2862 25.8112L14.0549 38.566L16.7142 44.9684L46.9706 32.2136L44.2862 25.8112Z"
                                                              fill="#2A2941"/>
                                                        <path
                                                            d="M20.051 42.3174L18.7966 42.8427C18.7213 42.8927 18.646 42.8927 18.5708 42.9177C18.4955 42.9177 18.4202 42.9177 18.345 42.8677C18.2697 42.8426 18.2195 42.7926 18.1694 42.7176C18.1192 42.6426 18.0941 42.5926 18.069 42.5175L16.6892 39.2663C16.639 39.1162 16.639 38.9662 16.6892 38.8161C16.7142 38.7411 16.7644 38.6911 16.8397 38.6411C16.8899 38.591 16.9651 38.566 17.0404 38.541L18.4202 37.9658C18.5959 37.8908 18.7715 37.8157 18.9471 37.7907C19.0976 37.7657 19.2481 37.7657 19.3987 37.7907C19.5241 37.8157 19.6496 37.8658 19.7499 37.9158C19.8503 37.9658 19.9757 38.0658 20.051 38.1409C20.1513 38.2409 20.2015 38.3659 20.2517 38.491C20.352 38.6911 20.3771 38.9162 20.3269 39.1412C20.2768 39.3663 20.1764 39.5664 20.0259 39.7165C20.2768 39.6664 20.5527 39.7165 20.7785 39.8415C21.0043 39.9666 21.1799 40.1666 21.2803 40.4167C21.3806 40.6418 21.4308 40.8919 21.3806 41.142C21.3556 41.3671 21.2301 41.5922 21.0796 41.7672C20.9541 41.8923 20.8287 41.9923 20.7033 42.0674L20.051 42.3174ZM19.198 40.4167L18.3199 40.7919L18.8467 42.0674L19.7499 41.6922C20.3269 41.4671 20.5026 41.117 20.352 40.6918C20.3269 40.5918 20.2768 40.5168 20.2015 40.4417C20.1262 40.3667 20.0259 40.3167 19.9255 40.3167C19.6746 40.2667 19.4238 40.3167 19.198 40.4167ZM17.6174 39.0412L18.069 40.1666L18.8467 39.8415C19.0224 39.7915 19.1729 39.6915 19.2983 39.5914C19.3987 39.4914 19.4489 39.3663 19.4739 39.2413C19.499 39.1412 19.499 39.0412 19.4739 38.9412C19.4489 38.8411 19.3987 38.7661 19.3234 38.7161C19.2481 38.6661 19.1729 38.616 19.0725 38.616C18.8216 38.6411 18.5708 38.6911 18.345 38.8161L17.6174 39.0412Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M24.2658 38.366L23.9647 38.491L24.5418 39.8666C24.5919 40.0166 24.5919 40.1667 24.5418 40.3167C24.5167 40.3668 24.4916 40.4418 24.4414 40.4668C24.3912 40.5168 24.341 40.5418 24.2909 40.5668C24.2407 40.5918 24.1654 40.6169 24.1153 40.6169C24.0651 40.6169 23.9898 40.5918 23.9396 40.5668C23.8142 40.4918 23.7138 40.3668 23.6637 40.2167L22.2838 36.8904C22.2336 36.7404 22.2336 36.5903 22.2838 36.4403C22.3089 36.3652 22.3591 36.3152 22.4343 36.2652C22.4845 36.2152 22.5598 36.1902 22.6351 36.1652L24.0149 35.5899C24.1654 35.5149 24.341 35.4649 24.4916 35.4149C24.617 35.3899 24.7675 35.3899 24.9181 35.4149C25.0686 35.4149 25.2191 35.4649 25.3697 35.5149C25.5202 35.5899 25.6456 35.69 25.746 35.79C25.8463 35.9151 25.9467 36.0651 25.9969 36.2152C26.0972 36.4903 26.0972 36.8154 25.9969 37.0905C25.8714 37.3906 25.6456 37.6657 25.3697 37.8408C25.5704 37.8658 25.7711 37.9408 25.9467 38.0409C26.1474 38.1409 26.3481 38.266 26.5237 38.391C26.6993 38.491 26.8499 38.6411 26.9753 38.7661C27.0506 38.8412 27.1258 38.9412 27.2011 39.0162C27.2262 39.0913 27.2262 39.1413 27.2011 39.2163C27.2011 39.2914 27.1509 39.3414 27.1258 39.4164C27.0757 39.4914 27.0004 39.5414 26.9251 39.5665C26.8499 39.5915 26.7495 39.5915 26.6743 39.5665C26.599 39.5414 26.4986 39.5164 26.4485 39.4664C26.3481 39.3914 26.2227 39.3164 26.1223 39.2413L25.5453 38.7912C25.3697 38.6661 25.194 38.5411 25.0184 38.441C24.893 38.366 24.7675 38.341 24.6421 38.341C24.4916 38.341 24.3661 38.391 24.2407 38.441L24.2658 38.366ZM23.9898 36.3652L23.2121 36.6904L23.7138 37.8908L24.4665 37.5657C24.6421 37.4907 24.7926 37.4156 24.9432 37.2906C25.0686 37.2156 25.1439 37.0905 25.194 36.9655C25.2442 36.8404 25.2442 36.6904 25.194 36.5403C25.1439 36.4403 25.0686 36.3402 24.9683 36.2652C24.8679 36.1902 24.7425 36.1652 24.6421 36.1652C24.4163 36.1902 24.1654 36.2402 23.9647 36.3402H23.9898V36.3652Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M31.9679 36.7153L31.5414 36.2651L29.8605 36.9904V37.6156C29.8856 37.7907 29.8856 37.9658 29.8605 38.1409C29.8354 38.1909 29.8103 38.2409 29.7852 38.2659C29.735 38.3159 29.71 38.3409 29.6598 38.3409C29.6096 38.3659 29.5594 38.3659 29.4842 38.3659C29.434 38.3659 29.3587 38.3659 29.3085 38.3409C29.2584 38.3159 29.2082 38.2909 29.158 38.2659C29.1078 38.2159 29.0827 38.1909 29.0577 38.1409C29.0326 38.0658 29.0326 38.0158 29.0577 37.9408C29.0577 37.8657 29.0577 37.7657 29.0577 37.6407L28.9573 34.3894C28.9573 34.2894 28.9573 34.1893 28.9573 34.0393C28.9573 33.9393 28.9573 33.8142 28.9573 33.7142C28.9824 33.6141 29.0075 33.5391 29.0827 33.4641C29.1329 33.364 29.2333 33.314 29.3336 33.264C29.434 33.239 29.5594 33.239 29.6598 33.264C29.7601 33.264 29.8354 33.314 29.9107 33.364L30.1365 33.5391L30.3873 33.8392L32.6202 36.1651C32.7456 36.2901 32.846 36.4152 32.9463 36.5903C32.9714 36.6403 32.9714 36.6903 32.9714 36.7403C32.9714 36.7903 32.9714 36.8404 32.9463 36.8904C32.9212 36.9404 32.8962 36.9904 32.846 37.0404C32.7958 37.0905 32.7456 37.1155 32.6954 37.1405H32.4947C32.4446 37.1655 32.3944 37.1655 32.3442 37.1405C32.2689 37.0905 32.2188 37.0404 32.1435 36.9904L31.9679 36.7153ZM29.8103 36.2151L31.0647 35.6899L29.6849 34.1643L29.8103 36.2151Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M34.6021 31.6383L37.2866 33.489L36.2329 30.9381C36.1827 30.788 36.1827 30.6379 36.2329 30.5129C36.258 30.4629 36.2831 30.4129 36.3081 30.3628C36.3583 30.3128 36.3834 30.2878 36.4587 30.2628C36.5089 30.2378 36.5841 30.2378 36.6343 30.2378C36.6845 30.2378 36.7597 30.2378 36.8099 30.2628C36.9354 30.3378 37.0106 30.4629 37.0608 30.5879L38.4406 33.9892C38.5912 34.3644 38.5159 34.6145 38.2148 34.7395H37.9891C37.9138 34.7645 37.8385 34.7645 37.7883 34.7395L37.5626 34.6395L37.3368 34.4894L34.7025 32.6387L35.7562 35.1647C35.8064 35.2897 35.8064 35.4398 35.7562 35.5898C35.7311 35.6398 35.706 35.6899 35.6559 35.7399C35.6057 35.7899 35.5555 35.8149 35.5053 35.8399C35.4551 35.8649 35.3799 35.8649 35.3297 35.8649C35.2795 35.8649 35.2043 35.8399 35.1541 35.8149C35.0286 35.7399 34.9534 35.6148 34.9032 35.4898L33.5234 32.1635C33.4732 32.0385 33.4481 31.9384 33.4481 31.8134C33.4481 31.7134 33.4732 31.6133 33.5234 31.5133C33.5735 31.4132 33.6488 31.3632 33.7492 31.3132C33.8244 31.2882 33.8997 31.2882 33.9499 31.3132C34 31.2882 34.0753 31.2882 34.1255 31.3132C34.2007 31.3382 34.2509 31.3882 34.3262 31.4132L34.6021 31.6383Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M39.6951 28.9623L40.7738 28.5122C41.0247 28.4121 41.2756 28.3371 41.5265 28.2871C41.7523 28.2621 42.0032 28.2871 42.229 28.3621C42.5551 28.4872 42.8311 28.6872 43.082 28.9623C43.3328 29.2375 43.5085 29.5126 43.6088 29.8627C43.7092 30.0878 43.7844 30.3379 43.8346 30.588C43.8597 30.8131 43.8597 31.0381 43.8346 31.2382C43.8095 31.4633 43.7593 31.6634 43.6841 31.8634C43.6088 32.0135 43.5335 32.1386 43.4332 32.2636C43.3328 32.3886 43.2074 32.4887 43.0569 32.5887L42.5551 32.8388L41.4763 33.314C41.3509 33.364 41.2254 33.389 41.1 33.389C40.9996 33.364 40.9244 33.314 40.8742 33.239C40.7989 33.1389 40.7488 33.0389 40.6986 32.9138L39.3187 29.7376C39.2686 29.5876 39.2686 29.4375 39.3187 29.2875C39.3689 29.2124 39.4191 29.1374 39.4943 29.0874C39.5445 29.0124 39.6198 28.9623 39.6951 28.9623ZM40.3223 29.5126L41.5265 32.4387L42.1537 32.1636L42.4798 32.0135L42.7056 31.8634C42.7809 31.7884 42.8311 31.7134 42.8813 31.6384C42.9565 31.4133 43.0067 31.1382 42.9816 30.8881C42.9565 30.638 42.8813 30.3879 42.7809 30.1628C42.6555 29.8377 42.4548 29.5126 42.2039 29.2875C42.0283 29.1374 41.8025 29.0624 41.5767 29.0874C41.3258 29.1124 41.1 29.1874 40.8742 29.2875L40.3223 29.5126Z"
                                                            fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M26.2979 17.1329L29.9859 17.7581L33.3226 16.0074L36.283 18.2833H40.0463L41.6017 21.6596L44.9385 23.3602L44.7127 27.0616L46.8452 30.1128L44.8883 33.314L45.3399 36.9904L42.1035 38.9412L40.7236 42.4425L36.9604 42.6926L34.2007 45.1935L30.7887 43.718L27.1258 44.6433L24.8177 41.7172L21.1549 40.8669L20.4775 37.2155L17.7178 34.7896L18.8467 31.2132L17.4669 27.7369L20.1262 25.0859L20.6029 21.3845L24.1905 20.2341L26.2979 17.1329Z"
                                                              fill="#FEC34E"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M26.5237 15.9324L25.7209 15.7824L23.3626 19.1586L19.3987 20.4091L18.8969 24.5357L15.9114 27.4868L17.3916 31.3133L16.1372 35.1897L19.2482 37.9408L20.0008 41.9673L24.04 42.9177L26.599 46.1189L30.6382 45.0935L34.4265 46.7441L37.5375 43.9931L41.677 43.718L43.1572 39.8665L46.7197 37.7157L46.218 33.6642L48.3756 30.1378L46.0424 26.6615L46.2932 22.5349L42.6304 20.6592L40.9244 16.9328H36.7848L33.4732 14.5569L29.8103 16.5076L26.5237 15.9324ZM26.298 17.2079L29.9859 17.8331L33.3226 16.0825L36.2831 18.3583H40.0463L41.6017 21.7346L44.9385 23.4353L44.7127 27.1367L46.8452 30.1878L44.8883 33.389L45.3399 37.0654L42.1035 39.0162L40.7237 42.5175L36.9604 42.7676L34.2007 45.2686L30.7887 43.793L27.1259 44.7184L24.8177 41.7922L21.1549 40.8919L20.4775 37.2405L17.7178 34.8146L18.8468 31.2382L17.4669 27.7619L20.1263 25.1359L20.6029 21.4345L24.1905 20.2841L26.298 17.2079Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M28.2297 21.7096C30.011 20.8593 31.993 20.5591 33.9498 20.8343C35.9067 21.1094 37.7131 21.9847 39.1682 23.3102C40.6233 24.6357 41.6519 26.3614 42.1035 28.2871C42.5551 30.1878 42.4296 32.2136 41.7272 34.0393C41.0247 35.865 39.7954 37.4656 38.1897 38.591C36.5841 39.7164 34.6523 40.3417 32.6954 40.3917C30.7385 40.4417 28.7817 39.8665 27.1258 38.7911C25.47 37.7157 24.1905 36.1651 23.4128 34.3644C22.3842 32.0385 22.3089 29.4375 23.2121 27.0616C24.1153 24.6857 25.9216 22.76 28.2297 21.7096Z"
                                                              fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M27.728 20.5341C25.6457 21.2844 23.8142 22.66 22.5347 24.4606C21.2301 26.2613 20.5026 28.4121 20.4273 30.613C20.352 32.8388 20.9542 35.0147 22.1584 36.8904C23.3375 38.7661 25.0686 40.2416 27.1008 41.142C29.1329 42.0423 31.3908 42.2924 33.5735 41.8923C35.7562 41.4921 37.7883 40.4417 39.3689 38.8911C40.9495 37.3405 42.0282 35.3398 42.4798 33.1639C42.9314 30.9881 42.7056 28.7373 41.8526 26.6865C40.7989 24.0105 38.7166 21.8596 36.0823 20.7092C33.3979 19.5588 30.4124 19.4837 27.728 20.5341ZM28.2297 21.7096C30.011 20.8593 31.993 20.5592 33.9498 20.8343C35.9067 21.1094 37.7131 21.9847 39.1682 23.3102C40.6233 24.6357 41.6268 26.3864 42.1035 28.2871C42.5802 30.1878 42.4297 32.2136 41.7272 34.0393C41.0247 35.865 39.7954 37.4656 38.1898 38.591C36.5841 39.7164 34.6523 40.3417 32.6954 40.3917C30.7135 40.4167 28.7817 39.8665 27.1259 38.7911C25.47 37.7157 24.1905 36.1651 23.4128 34.3644C22.3842 32.0385 22.3089 29.4375 23.2121 27.0616C24.1153 24.6857 25.9216 22.785 28.2297 21.7096Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M45.7413 22.5099L16.4885 34.7145L19.0725 40.8419L48.3003 28.6372L45.7413 22.5099Z"
                                                              fill="#A694FE"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M49.9812 29.3375L46.4187 20.8342L14.8076 34.0142L18.4453 42.5175L49.9812 29.3375ZM45.7413 22.4598L16.4885 34.6645L19.0726 40.7918L48.3003 28.5872L45.7413 22.4598Z"
                                                              fill="#2A2941"/>
                                                        <path
                                                            d="M22.2838 38.316L21.0545 38.8161C20.9792 38.8412 20.904 38.8662 20.8287 38.8662C20.7535 38.8662 20.6782 38.8412 20.6029 38.8161C20.5026 38.8161 20.4022 38.6411 20.327 38.466L19.0725 35.3898C19.0224 35.2398 19.0224 35.0897 19.0725 34.9397C19.0725 34.8146 19.2231 34.7396 19.3987 34.6396L20.7033 34.1144C20.8789 34.0393 21.0294 33.9893 21.205 33.9393C21.3556 33.9143 21.5061 33.9143 21.6566 33.9393C21.7821 33.9643 21.8824 34.0143 21.9828 34.0643C22.0831 34.1144 22.1835 34.1894 22.2838 34.2894C22.3591 34.3895 22.4344 34.4895 22.4845 34.6145C22.5598 34.8146 22.61 35.0147 22.5598 35.2398C22.5096 35.4399 22.4093 35.6399 22.2587 35.79C22.5096 35.74 22.7605 35.79 22.9863 35.915C23.2121 36.0401 23.3877 36.2402 23.463 36.4653C23.5633 36.6903 23.5884 36.9404 23.5633 37.1655C23.5132 37.3906 23.4128 37.6157 23.2623 37.7657C23.1619 37.8658 23.0365 37.9658 22.886 38.0409C22.6852 38.1409 22.5096 38.2409 22.2838 38.316ZM21.4559 36.4903L20.6029 36.8404L21.1047 38.0409L21.9828 37.6907C22.5096 37.4656 22.7103 37.1405 22.5598 36.7404C22.5347 36.6403 22.4845 36.5653 22.4093 36.4903C22.334 36.4152 22.2587 36.3902 22.1584 36.3652C21.9075 36.3402 21.6817 36.3902 21.4559 36.4903ZM19.9255 35.1147L20.3771 36.1901L21.1298 35.89C21.2803 35.815 21.4308 35.74 21.5814 35.6399C21.6315 35.5899 21.6566 35.5399 21.7068 35.4899C21.7319 35.4399 21.757 35.3648 21.757 35.3148C21.7821 35.2148 21.7821 35.1147 21.757 35.0147C21.6566 34.7896 21.5312 34.6896 21.3807 34.6896C21.1298 34.6896 20.904 34.7646 20.6782 34.8646L19.9255 35.1147Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M26.3481 34.5394L26.0722 34.6395L26.6241 36.015C26.6492 36.09 26.6743 36.165 26.6743 36.2401C26.6743 36.3151 26.6492 36.3901 26.6241 36.4652C26.599 36.5152 26.5739 36.5652 26.5237 36.6152C26.4736 36.6652 26.4234 36.6902 26.3732 36.7153C26.323 36.7403 26.2729 36.7653 26.1976 36.7653C26.1474 36.7653 26.0722 36.7403 26.022 36.7153C25.8965 36.6402 25.7962 36.5152 25.746 36.3651L24.3662 33.1889C24.316 33.0389 24.316 32.8888 24.3662 32.7387C24.4414 32.6137 24.5669 32.5137 24.6923 32.4636L26.0722 31.9134C26.2227 31.8384 26.3732 31.7884 26.5488 31.7384C26.6743 31.7134 26.8248 31.7134 26.9502 31.7384C27.1008 31.7384 27.2513 31.7884 27.3767 31.8384C27.5022 31.8884 27.6276 31.9885 27.7531 32.0885C27.8534 32.2135 27.9287 32.3386 28.0039 32.4636C28.1043 32.7387 28.1043 33.0389 28.0039 33.314C27.9287 33.2889 27.8534 33.2639 27.7782 33.2639C27.7029 33.2639 27.6276 33.289 27.5524 33.339C27.4771 33.389 27.4269 33.439 27.4018 33.514C27.3517 33.5891 27.3516 33.6641 27.3516 33.7391C27.3516 33.8142 27.3767 33.8892 27.4018 33.9642C27.452 34.0392 27.5022 34.0893 27.5524 34.1393C27.6025 34.1893 27.7029 34.2143 27.7782 34.2143C27.8534 34.2143 27.9287 34.2143 28.0039 34.1643C28.2046 34.2643 28.3803 34.3894 28.581 34.5144C28.7315 34.6145 28.882 34.7395 29.0075 34.8645C29.0827 34.9396 29.158 35.0146 29.2082 35.1146C29.2333 35.1647 29.2333 35.2397 29.2082 35.2897C29.1831 35.3647 29.158 35.4398 29.1078 35.4898C29.0576 35.5398 29.0075 35.5898 28.9322 35.6148C28.8569 35.6398 28.7566 35.6398 28.6813 35.6148C28.6061 35.5898 28.5308 35.5648 28.4555 35.5148L28.1545 35.2897L27.5774 34.8645C27.4269 34.7395 27.2513 34.6145 27.0757 34.5144C26.9502 34.4644 26.8248 34.4144 26.6994 34.4144C26.5739 34.4144 26.4234 34.4394 26.323 34.4894L26.3481 34.5394ZM26.0972 32.5887L25.3446 32.9138L25.8213 34.0642L26.5488 33.7391C26.7244 33.6891 26.875 33.5891 27.0255 33.489C27.1259 33.414 27.2011 33.314 27.2513 33.1889C27.3015 33.0639 27.3015 32.9138 27.2513 32.7888C27.2011 32.6887 27.1509 32.5887 27.0506 32.5387C26.9502 32.4886 26.8499 32.4386 26.7244 32.4636C26.4987 32.4636 26.2979 32.5137 26.0972 32.5887Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M33.7993 32.9639L33.3728 32.5138L31.7421 33.189V33.8143C31.7672 33.9893 31.7672 34.1394 31.7421 34.3144C31.7421 34.4145 31.6417 34.4895 31.5414 34.5395C31.4912 34.5645 31.441 34.5645 31.3908 34.5645C31.3407 34.5645 31.2905 34.5645 31.2403 34.5395C31.1901 34.5145 31.14 34.4895 31.0898 34.4645C31.0396 34.4395 31.0145 34.3895 30.9894 34.3395C30.9644 34.2644 30.9644 34.2144 30.9894 34.1394C30.9894 34.0393 30.9894 33.9393 30.9894 33.8393L30.8891 30.7381V30.413C30.864 30.3129 30.864 30.1879 30.8891 30.0878C30.9142 29.9878 30.9393 29.9128 30.9894 29.8377C31.0647 29.7627 31.14 29.6877 31.2403 29.6627C31.3407 29.6377 31.441 29.6377 31.5665 29.6627C31.6668 29.6627 31.7421 29.7127 31.8173 29.7627C31.8926 29.8127 31.9679 29.8628 32.0181 29.9378L32.2689 30.1879L34.4265 32.4137C34.552 32.5388 34.6523 32.6638 34.7276 32.8139C34.7527 32.8639 34.7527 32.9139 34.7527 32.9639C34.7527 33.014 34.7527 33.064 34.7276 33.114C34.7025 33.164 34.6774 33.214 34.6272 33.264C34.5771 33.3141 34.5269 33.3391 34.4767 33.3641C34.4265 33.3891 34.3513 33.3891 34.3011 33.3641C34.2509 33.3641 34.2007 33.3641 34.1506 33.3641L33.9749 33.214L33.7993 32.9639ZM31.717 32.4888L32.9212 31.9886L31.5414 30.538L31.717 32.4888Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M36.3834 28.087L38.9675 29.8877L37.9389 27.4367C37.8887 27.3117 37.8887 27.1616 37.9389 27.0366C37.964 26.9866 37.9891 26.9365 38.0141 26.8865C38.0643 26.8365 38.0894 26.8115 38.1396 26.7865C38.1898 26.7615 38.2399 26.7615 38.3152 26.7615C38.3654 26.7615 38.4406 26.7615 38.4908 26.7865C38.6163 26.8615 38.6915 26.9866 38.7417 27.1116L40.1215 30.3628C40.2721 30.713 40.1968 30.9631 39.9208 31.0881C39.8456 31.1131 39.7703 31.1131 39.695 31.0881C39.6198 31.1131 39.5445 31.1131 39.4943 31.0881C39.4191 31.0631 39.3438 31.0381 39.2936 30.9881L39.0929 30.838L36.4085 29.0874L37.4371 31.5133C37.4873 31.6383 37.4873 31.7884 37.4371 31.9134C37.412 31.9635 37.3869 32.0135 37.3618 32.0635C37.3117 32.1135 37.2866 32.1385 37.2364 32.1635C37.1862 32.1885 37.1361 32.2135 37.0608 32.2135C37.0106 32.2135 36.9353 32.1885 36.8852 32.1635C36.7597 32.0885 36.6845 31.9885 36.6343 31.8384L35.2293 28.6372C35.1792 28.5371 35.1541 28.4121 35.129 28.3121C35.129 28.212 35.1541 28.112 35.2043 28.037C35.2544 27.9619 35.3548 27.8869 35.4301 27.8369H35.6308C35.6809 27.8119 35.7562 27.8119 35.8064 27.8369C35.8816 27.8619 35.9318 27.9119 36.0071 27.9369L36.3834 28.087Z"
                                                            fill="#2A2941"/>
                                                        <path
                                                            d="M41.3007 25.5611L42.3544 25.1109C42.6053 25.0109 42.8311 24.9359 43.107 24.8858C43.3328 24.8608 43.5586 24.8858 43.7593 24.9609C44.0855 25.0859 44.3615 25.261 44.6123 25.4861C44.8632 25.7362 45.0388 26.0113 45.1392 26.3364C45.2395 26.5615 45.3148 26.7866 45.365 27.0367C45.3901 27.2367 45.3901 27.4618 45.365 27.6619C45.3399 27.862 45.2897 28.0621 45.2145 28.2371C45.1392 28.3872 45.0639 28.5122 44.9636 28.6123C44.8632 28.7373 44.7378 28.8373 44.6123 28.9124L44.1357 29.1625L43.082 29.6126C42.9816 29.6627 42.8562 29.6877 42.7307 29.6877C42.6805 29.6877 42.6304 29.6627 42.6053 29.6377C42.5551 29.6126 42.53 29.5876 42.5049 29.5376C42.4297 29.4376 42.3795 29.3375 42.3293 29.2375L41.0498 26.1863C40.9996 26.0363 40.9996 25.8862 41.0498 25.7362C41.0749 25.6611 41.1251 25.6111 41.1752 25.5611C41.2254 25.5111 41.3007 25.4861 41.376 25.4611L41.3007 25.5611ZM41.8777 26.1113L43.0569 28.8624L43.659 28.6123L43.9851 28.4622C44.0604 28.4372 44.1357 28.3872 44.2109 28.3121C44.2862 28.2371 44.3364 28.1621 44.3615 28.0871C44.4367 27.862 44.4869 27.6369 44.4618 27.3868C44.4367 27.1367 44.3615 26.9116 44.2611 26.7115C44.1357 26.3864 43.96 26.1113 43.7092 25.8612C43.5335 25.7112 43.3328 25.6611 43.107 25.6611C42.8812 25.6861 42.6555 25.7612 42.4297 25.8362L41.9028 26.0613V26.1113H41.8777Z"
                                                            fill="#2A2941"/>
                                                    </g>
                                                </svg>
                                                <span class="hide-menu">Хомашё турлари</span>
                                            </a>
                                        </li>

                                        @php // Raw Material Variation @endphp
                                        <li class="sidebar-item">
                                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                               href="{{ route('raw-material-variation.index') }}" aria-expanded="false">
                                                <svg width="25px" height="25px" viewBox="0 0 64 64" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg" class="me-2">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                       stroke-linejoin="round" stroke="#CCCCCC" stroke-width="12.8">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M37.4207 34.5806L45.8002 31.1042L49.6888 39.5575L40.8327 43.1588L37.4207 34.5806Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M47.2553 27.7781L45.8002 31.1043L49.7139 39.6076L50.9433 36.1313L47.2553 27.7781Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M38.4995 34.1805L45.8002 31.1043L47.2553 27.678L39.3525 30.9042L38.4995 34.1805Z"
                                                              fill="#2A2941" stroke="#2A2941" stroke-width="1.8587"
                                                              stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M28.3388 47.5356C29.3674 51.2119 30.7975 53.1127 34.937 51.5371L32.4784 45.7349L28.3388 47.5356Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M34.4102 15.1233C33.6826 15.2733 33.0052 15.5485 32.378 15.9236L32.6791 17.3491C32.1522 17.7493 31.7006 18.2495 31.3494 18.8247L29.9194 18.6496C29.5932 19.3249 29.3925 20.0502 29.2922 20.7754L30.5968 21.4757C30.5717 22.151 30.6971 22.8262 30.9229 23.4765L29.9194 24.5019C30.2204 25.1771 30.6469 25.8024 31.1487 26.3526L32.4784 25.7523C32.9801 26.2025 33.5823 26.5526 34.2345 26.7777L34.4102 28.2533C35.1377 28.4284 35.8653 28.4784 36.6179 28.4283L36.9692 27.0028C37.6215 26.8778 38.2487 26.6277 38.8257 26.2775L40.055 27.0528C40.632 26.6027 41.1338 26.0524 41.5101 25.4272L40.632 24.2017C40.9331 23.6015 41.1338 22.9513 41.184 22.276L42.5638 21.8008C42.5638 21.0505 42.4384 20.3003 42.1875 19.6L40.7324 19.55C40.4564 18.9247 40.055 18.3495 39.5783 17.8743L40.0299 16.4738C39.4529 15.9986 38.8006 15.6235 38.0981 15.3484L37.1699 16.4738C36.5176 16.2987 35.8402 16.2487 35.1628 16.3237L34.4102 15.1233ZM34.4102 18.1494C35.3635 17.7993 36.3921 17.8493 37.3204 18.2495C38.2487 18.6496 38.9762 19.4249 39.3275 20.3503C39.5282 20.8005 39.6285 21.3006 39.6285 21.8008C39.6285 22.301 39.5282 22.8012 39.3525 23.2514C39.1518 23.7266 38.8759 24.1267 38.5246 24.5019C38.1734 24.852 37.7469 25.1271 37.2953 25.3272C36.342 25.6773 35.2883 25.6273 34.3851 25.2271C33.4568 24.802 32.7293 24.0517 32.378 23.1263C32.1773 22.6512 32.077 22.151 32.1021 21.6508C32.1021 21.1506 32.2024 20.6504 32.4031 20.1752C32.6038 19.7 32.9049 19.2999 33.2561 18.9497C33.6324 18.5996 34.0589 18.3245 34.5356 18.1494H34.4102Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M40.4062 39.6826C39.8041 39.8077 39.2522 40.0327 38.7253 40.3579L38.9511 41.5333C38.4995 41.8835 38.1232 42.2836 37.8472 42.7588L36.6179 42.6337C36.3419 43.1839 36.1663 43.7592 36.0911 44.3594L37.1448 44.9096C37.1197 45.4598 37.22 46.01 37.3957 46.5102L36.5677 47.3605C36.8437 47.9107 37.1949 48.4109 37.5964 48.8361L38.7002 48.3359C39.1267 48.686 39.6034 48.9611 40.1303 49.1362L40.2557 50.3617C40.8578 50.5117 41.4599 50.5618 42.0621 50.4867L42.3631 49.3363C42.9151 49.2363 43.4168 49.0362 43.8684 48.7361L44.897 49.3613C45.3737 48.9862 45.8002 48.561 46.1263 48.0358L45.3988 47.0854C45.6497 46.6103 45.8253 46.06 45.8755 45.5348L47.0044 45.1347C47.0044 44.5345 46.9292 43.9342 46.7285 43.359H45.4991C45.2733 42.8588 44.9472 42.4087 44.5709 42.0085L44.9472 40.8831C44.4705 40.5079 43.9437 40.2078 43.3666 39.9827L42.614 40.8831C42.0871 40.733 41.5352 40.708 40.9833 40.783L40.331 39.7827L40.4062 39.6826ZM40.4062 42.1586C41.184 41.8584 42.037 41.8584 42.7896 42.1836C43.5423 42.5087 44.1444 43.1089 44.4705 43.8842C44.6211 44.2594 44.6963 44.6595 44.6963 45.0847C44.6963 45.4848 44.596 45.885 44.4454 46.2601C44.2949 46.6353 44.044 46.9854 43.7681 47.2605C43.467 47.5356 43.1409 47.7607 42.7645 47.9107C41.9868 48.2109 41.1338 48.2109 40.3811 47.8857C39.6285 47.5606 39.0264 46.9604 38.7002 46.1851C38.5497 45.8099 38.4744 45.4098 38.4744 44.9846C38.4744 44.5845 38.5748 44.1593 38.7253 43.7842C38.8759 43.409 39.1267 43.0589 39.4278 42.7838C39.6787 42.5087 40.0299 42.2836 40.4062 42.1586Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M27.1848 40.4078L27.26 23.7265L36.0158 23.6265L39.9045 27.553L39.779 40.1327L27.1848 40.4078Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M35.9155 27.3778V23.1262L40.1805 27.4279L35.9155 27.3778Z"
                                                              fill="#2A2941" stroke="#2A2941" stroke-width="1.8587"
                                                              stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M22.1671 24.3269C12.1318 28.0033 13.2357 42.7589 25.5289 45.2098C25.5289 45.2098 27.1346 47.3357 28.0879 47.5857C29.0413 47.8358 31.1989 46.7104 31.1989 46.7104L22.1671 24.3269Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M38.8006 32.5049L47.1801 29.0286L51.0687 37.4818L42.2126 41.0831L38.8006 32.5049Z"
                                                              fill="#32EDBB"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M38.5999 32.0297C38.4744 32.0798 38.3741 32.1798 38.3239 32.3298C38.2737 32.4549 38.2737 32.605 38.3239 32.73L41.7359 41.3333C41.761 41.4083 41.7861 41.4583 41.8362 41.5083C41.8864 41.5584 41.9366 41.6084 42.0119 41.6334C42.0871 41.6584 42.1373 41.6834 42.2126 41.6834C42.2878 41.6834 42.3631 41.6584 42.4133 41.6334L51.2694 38.032C51.3948 37.982 51.4952 37.882 51.5705 37.7569C51.6206 37.6319 51.6206 37.4818 51.5705 37.3318L47.6818 28.8785C47.6316 28.7535 47.5313 28.6535 47.4058 28.6034C47.2804 28.5534 47.1298 28.5534 47.0044 28.6034L38.5999 32.0798V32.0297ZM39.4779 32.78L46.879 29.6788L50.3411 37.1817L42.4885 40.3829L39.4529 32.755L39.4779 32.78Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M48.6101 25.7024L47.1801 29.0287L51.0938 37.5319L52.3231 34.0556L48.6101 25.7024Z"
                                                              fill="#FEC34E"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M49.1118 25.4522C49.0617 25.3522 49.0115 25.2771 48.9111 25.2271C48.8359 25.1771 48.7355 25.1521 48.6352 25.1521C48.5348 25.1521 48.4345 25.1771 48.3341 25.2271C48.2588 25.2771 48.1836 25.3772 48.1334 25.4522L46.7034 28.7785C46.6783 28.8535 46.6532 28.9285 46.6532 29.0036C46.6532 29.0786 46.6783 29.1536 46.7034 29.2287L50.6171 37.7319C50.6673 37.8319 50.7426 37.907 50.8178 37.957C50.8931 38.007 50.9935 38.032 51.1189 38.032C51.2193 38.032 51.3196 37.982 51.42 37.932C51.4952 37.8819 51.5705 37.7819 51.6207 37.6819L52.9252 34.2055C52.9503 34.1305 52.9754 34.0805 52.9754 34.0055C52.9754 33.9304 52.9503 33.8554 52.9252 33.8054L49.1118 25.4522ZM48.6352 26.9778L51.8214 34.0555L51.0436 36.1563L47.7571 29.0036L48.6352 26.9778Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M39.8794 32.1048L47.1801 29.0286L48.6101 25.6023L40.7324 28.8285L39.8794 32.1048Z"
                                                              fill="#A694FE"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M39.4278 31.9546C39.4027 32.0547 39.4027 32.1547 39.4278 32.2297C39.4529 32.3298 39.5031 32.4048 39.5783 32.4798C39.6536 32.5549 39.754 32.5799 39.8292 32.6049C39.9296 32.6299 40.0299 32.6049 40.1052 32.5799L47.4059 29.5037C47.456 29.4787 47.5313 29.4287 47.5815 29.4037C47.6317 29.3786 47.6567 29.3036 47.6818 29.2286L49.1369 25.8023C49.1871 25.7022 49.1871 25.6022 49.162 25.5022C49.1369 25.4021 49.0868 25.3021 49.0115 25.2271C48.9362 25.152 48.8359 25.102 48.7355 25.077C48.6352 25.052 48.5348 25.077 48.4345 25.102L40.5568 28.3282C40.4815 28.3533 40.4063 28.4033 40.3561 28.4533C40.3059 28.5033 40.2557 28.5783 40.2306 28.6534L39.4278 31.9546ZM40.7324 31.2043L41.2593 29.2036L47.732 26.5526L46.879 28.6284L40.7073 31.2043H40.7324Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M30.5215 46.3601C31.4247 49.6364 32.7292 51.262 36.3419 49.8865L34.2094 44.7595L30.5215 46.3601Z"
                                                              fill="#A694FE"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M34.7864 43.209L29.1416 45.6599L29.4176 46.6103C30.095 49.0112 31.0483 50.6118 32.5787 51.212C33.9585 51.6372 35.4638 51.5372 36.7684 50.9119L37.8723 50.4867L34.7864 43.209ZM30.5215 46.3602C31.4246 49.6364 32.7292 51.262 36.3419 49.8865L34.2094 44.7596L30.5215 46.3602Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M35.3886 13.3477C34.661 13.4977 33.9836 13.7728 33.3564 14.148L33.6575 15.5985C33.1306 15.9987 32.679 16.4989 32.3278 17.0741L30.8978 16.899C30.5716 17.5743 30.3709 18.2745 30.2706 19.0248L31.5752 19.7251C31.5501 20.4003 31.6755 21.1006 31.9264 21.7258L30.8978 22.7762C31.2239 23.4515 31.6253 24.0767 32.1271 24.6269L33.4568 24.0517C33.9836 24.5019 34.5607 24.827 35.2129 25.0521L35.3886 26.5277C36.1161 26.7027 36.8437 26.7528 37.5963 26.7027L37.9476 25.2772C38.5999 25.1521 39.2271 24.902 39.8041 24.5519L41.0334 25.3272C41.6104 24.877 42.1122 24.3268 42.4885 23.7016L41.6104 22.4761C41.9115 21.8759 42.1122 21.2257 42.1624 20.5504L43.5422 20.0752C43.5422 19.3249 43.4168 18.5746 43.1659 17.8744L41.7108 17.8244C41.4348 17.1991 41.0334 16.6239 40.5567 16.1487L41.0083 14.7482C40.4313 14.273 39.779 13.8979 39.0765 13.6228L38.1483 14.7482C37.496 14.5731 36.8186 14.5231 36.1663 14.5981L35.3635 13.3727L35.3886 13.3477ZM35.3886 16.3738C36.3419 16.0237 37.3705 16.0737 38.2988 16.4738C39.2271 16.874 39.9546 17.6493 40.3058 18.5746C40.5066 19.0248 40.6069 19.525 40.6069 20.0252C40.6069 20.5254 40.5066 21.0256 40.3309 21.4757C40.1302 21.9509 39.8543 22.3511 39.503 22.7262C39.1518 23.0764 38.7253 23.3515 38.2737 23.5515C37.3204 23.9017 36.2917 23.8517 35.3635 23.4515C34.4352 23.0513 33.7077 22.2761 33.3564 21.3507C33.1557 20.9005 33.0554 20.4003 33.0554 19.9001C33.0554 19.4 33.1557 18.8998 33.3313 18.4246C33.532 17.9494 33.808 17.5493 34.1592 17.1741C34.4854 16.849 34.9119 16.5489 35.3886 16.3738Z"
                                                              fill="#FEC34E"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M36.367 12.7224L35.9405 12.0471L35.1628 12.2222C34.3098 12.3973 33.507 12.7224 32.7543 13.1725L32.0518 13.5977L32.378 15.1733C32.1522 15.3984 31.9515 15.6235 31.7508 15.8736L30.1702 15.6985L29.819 16.4238C29.4427 17.1991 29.2169 18.0244 29.1165 18.8747L29.0413 19.65L30.4462 20.4003C30.4713 20.7254 30.5215 21.0505 30.5967 21.3757L29.4928 22.5011L29.8441 23.2014C30.2204 24.0017 30.7222 24.7269 31.3243 25.3522L31.8762 25.9274L33.2561 25.3772C33.532 25.5523 33.808 25.7273 34.1091 25.8774L34.3098 27.453L35.0624 27.628C35.9154 27.8531 36.7935 27.9031 37.6465 27.8281L38.4744 27.7531L38.8507 26.2025C39.1518 26.1025 39.4278 26.0024 39.7037 25.8774L41.0585 26.7277L41.7108 26.2275C42.3882 25.6773 42.9652 25.0271 43.4419 24.3018L43.8684 23.6265L42.915 22.326C43.0405 22.0259 43.1157 21.7258 43.191 21.4007L44.6712 20.8755V20.0752C44.6712 19.1998 44.5207 18.3245 44.2447 17.4992L43.9938 16.7489L42.4133 16.6989C42.2377 16.4238 42.062 16.1487 41.8362 15.8736L42.338 14.373L41.7359 13.8728C41.0585 13.3226 40.3059 12.8724 39.4779 12.5473L38.7253 12.2472L37.7218 13.4727C37.3956 13.4226 37.0946 13.3976 36.7684 13.3976L36.3419 12.6974L36.367 12.7224ZM35.3886 13.3476C34.661 13.4977 33.9836 13.7728 33.3564 14.1479L33.6575 15.5985C33.1306 15.9986 32.679 16.4988 32.3278 17.074L30.8978 16.899C30.5716 17.5742 30.3709 18.2745 30.2706 19.0248L31.5752 19.725C31.5501 20.4003 31.6755 21.1006 31.9264 21.7258L30.8978 22.7762C31.2239 23.4515 31.6253 24.0767 32.1271 24.6269L33.4568 24.0517C33.9836 24.5019 34.5607 24.827 35.213 25.0521L35.3886 26.5276C36.1161 26.7027 36.8437 26.7527 37.5963 26.7027L37.9476 25.2771C38.5999 25.1521 39.2271 24.902 39.8041 24.5519L41.0334 25.3272C41.6104 24.877 42.1122 24.3268 42.4885 23.7016L41.6104 22.4761C41.9115 21.8759 42.1122 21.2256 42.1624 20.5504L43.5422 20.0752C43.5422 19.3249 43.4168 18.5746 43.1659 17.8743L41.7108 17.8243C41.4348 17.1991 41.0334 16.6239 40.5567 16.1487L41.0083 14.7481C40.4313 14.273 39.779 13.8978 39.0765 13.6227L38.1483 14.7481C37.496 14.5731 36.8186 14.5231 36.1663 14.5981L35.3635 13.3726L35.3886 13.3476ZM35.3886 16.3738C36.3419 16.0236 37.3705 16.0737 38.2988 16.4738C39.2271 16.874 39.9546 17.6492 40.3059 18.5746C40.5066 19.0248 40.6069 19.525 40.6069 20.0252C40.6069 20.5253 40.5066 21.0255 40.3309 21.4757C40.1302 21.9509 39.8543 22.351 39.503 22.7262C39.1518 23.0763 38.7253 23.3514 38.2737 23.5515C37.3204 23.9016 36.2917 23.8516 35.3635 23.4515C34.4352 23.0513 33.7077 22.276 33.3564 21.3507C33.1557 20.9005 33.0554 20.4003 33.0554 19.9001C33.0554 19.3999 33.1557 18.8997 33.3313 18.4245C33.532 17.9494 33.808 17.5492 34.1593 17.1741C34.4854 16.8489 34.9119 16.5488 35.3886 16.3738ZM35.8151 17.4242C36.4925 17.1741 37.22 17.1991 37.8723 17.4992C38.5246 17.7993 39.0264 18.3245 39.2772 18.9998C39.4278 19.3249 39.503 19.675 39.503 20.0252C39.503 20.3753 39.4529 20.7254 39.3023 21.0505C39.1769 21.3757 38.9762 21.6758 38.7253 21.9259C38.4744 22.176 38.1734 22.376 37.8472 22.5011C37.1698 22.7512 36.4423 22.7262 35.79 22.4261C35.1377 22.126 34.6359 21.6008 34.385 20.9255C34.2596 20.6004 34.1843 20.2502 34.1593 19.9001C34.1593 19.55 34.2345 19.1998 34.36 18.8747C34.4854 18.5496 34.6861 18.2495 34.937 17.9994C35.1879 17.7743 35.4889 17.5742 35.8151 17.4242Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M41.6355 38.0071C41.0334 38.1321 40.4564 38.3572 39.9546 38.6823L40.1804 39.8578C39.7288 40.1829 39.3525 40.6081 39.0765 41.0833L37.8472 40.9582C37.5963 41.5084 37.4207 42.0836 37.3455 42.6839L38.3992 43.2341C38.3741 43.7843 38.4744 44.3345 38.65 44.8347L37.8221 45.66C38.073 46.2102 38.4243 46.7104 38.8508 47.1356L39.9295 46.6604C40.356 47.0105 40.8327 47.2856 41.3596 47.4607L41.5101 48.6861C42.0871 48.8362 42.7143 48.8612 43.3165 48.8112L43.6175 47.6608C44.1694 47.5607 44.6712 47.3606 45.1479 47.0605L46.1514 47.6858C46.6281 47.3106 47.0546 46.8604 47.3807 46.3603L46.6783 45.4099C46.9292 44.9097 47.0797 44.3845 47.1549 43.8593L48.2839 43.4592C48.2839 42.8589 48.1836 42.2587 48.0079 41.6835H46.7786C46.5528 41.1833 46.2267 40.7331 45.8504 40.333L46.2267 39.2075C45.75 38.8324 45.2232 38.5323 44.6461 38.3072L43.8935 39.2075C43.3666 39.0575 42.8147 39.0325 42.2627 39.0825L41.6105 38.0821L41.6355 38.0071ZM41.6355 40.483C42.4133 40.1829 43.2663 40.1829 44.0189 40.533C44.7716 40.8582 45.3737 41.4834 45.6747 42.2337C45.8253 42.6088 45.9005 43.009 45.9005 43.4341C45.9005 43.8343 45.8253 44.2344 45.6497 44.6096C45.4991 44.9847 45.2482 45.3349 44.9723 45.61C44.6712 45.8851 44.3451 46.1102 43.9437 46.2602C43.1659 46.5603 42.3129 46.5603 41.5603 46.2102C40.8076 45.8851 40.2055 45.2598 39.9045 44.5096C39.7539 44.1344 39.6787 43.7343 39.6787 43.3091C39.6787 42.9089 39.7539 42.4838 39.9295 42.1086C40.0801 41.7335 40.331 41.3834 40.632 41.1083C40.9331 40.8332 41.2843 40.6081 41.6355 40.483Z"
                                                              fill="#FEC34E"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M42.614 37.3818L42.1875 36.7065L41.4098 36.8566C40.7073 37.0317 40.055 37.3068 39.4278 37.6569L38.7253 38.0821L38.9762 39.3826C38.8508 39.5076 38.7253 39.6577 38.5999 39.8077L37.2702 39.6827L36.919 40.3829C36.5928 41.0332 36.367 41.7335 36.2918 42.4837L36.1914 43.284L37.4207 43.9093C37.4458 44.1094 37.4709 44.2844 37.496 44.4845L36.5677 45.4099L36.919 46.1351C37.22 46.8104 37.6465 47.4106 38.1483 47.9358L38.7002 48.511L39.9295 47.9608C40.0801 48.0609 40.2557 48.1609 40.4313 48.2609L40.5818 49.5614L41.3596 49.7615C42.062 49.9366 42.8147 49.9866 43.5422 49.9116L44.3451 49.8365L44.6712 48.6111L45.2232 48.411L46.3521 49.1113L47.0044 48.6111C47.5814 48.1609 48.0832 47.6107 48.4846 47.0105L48.9111 46.3352L48.1083 45.2848C48.1836 45.1097 48.2337 44.9347 48.2588 44.7346L49.4882 44.2844V43.4841C49.4882 42.7588 49.3878 42.0336 49.1369 41.3333L48.886 40.583H47.5564L47.2302 40.1078L47.6567 38.8824L47.0295 38.3822C46.4525 37.932 45.8002 37.5569 45.1228 37.3068L44.3702 37.0317L43.5172 38.032H42.915L42.6391 37.5819L42.614 37.3818ZM41.6355 38.007C41.0334 38.1321 40.4564 38.3572 39.9546 38.6823L40.1804 39.8577C39.7288 40.1829 39.3525 40.608 39.0765 41.0832L37.8472 40.9582C37.5963 41.5084 37.4207 42.0836 37.3455 42.6838L38.3992 43.234C38.3741 43.7842 38.4744 44.3344 38.6501 44.8346L37.8221 45.6599C38.073 46.2102 38.4243 46.7103 38.8508 47.1355L39.9295 46.6603C40.356 47.0105 40.8327 47.2856 41.3596 47.4606L41.5101 48.6861C42.0871 48.8362 42.7143 48.8612 43.3165 48.8111L43.6175 47.6607C44.1695 47.5607 44.6712 47.3606 45.1479 47.0605L46.1514 47.6857C46.6281 47.3106 47.0546 46.8604 47.3807 46.3602L46.6783 45.4099C46.9292 44.9097 47.0797 44.3845 47.1549 43.8593L48.2839 43.4591C48.2839 42.8589 48.1836 42.2587 48.0079 41.6834H46.7786C46.5528 41.1832 46.2267 40.7331 45.8504 40.3329L46.2267 39.2075C45.75 38.8324 45.2232 38.5322 44.6461 38.3072L43.8935 39.2075C43.3666 39.0574 42.8147 39.0324 42.2627 39.0824L41.6105 38.0821L41.6355 38.007ZM41.6355 40.458C42.4133 40.1579 43.2663 40.1579 44.0189 40.508C44.7716 40.8331 45.3737 41.4583 45.6747 42.2086C45.8253 42.5838 45.9005 42.9839 45.9005 43.4091C45.9005 43.8092 45.8253 44.2094 45.6497 44.5845C45.4991 44.9597 45.2482 45.3098 44.9723 45.5849C44.6712 45.86 44.3451 46.0851 43.9437 46.2352C43.1659 46.5353 42.3129 46.5353 41.5603 46.1851C40.8076 45.86 40.2055 45.2348 39.9045 44.4845C39.7539 44.1094 39.6787 43.7092 39.6787 43.284C39.6787 42.8839 39.7539 42.4587 39.9295 42.0836C40.0801 41.7084 40.331 41.3583 40.632 41.0832C40.9331 40.8081 41.2843 40.583 41.6355 40.458ZM42.062 41.5334C42.5387 41.3333 43.0907 41.3583 43.5673 41.5584C44.044 41.7585 44.4203 42.1586 44.621 42.6338C44.7214 42.8589 44.7716 43.134 44.7716 43.3841C44.7716 43.6342 44.7214 43.8843 44.621 44.1344C44.5207 44.3595 44.3702 44.5845 44.1945 44.7596C44.0189 44.9347 43.7931 45.0847 43.5422 45.1598C43.0656 45.3598 42.5136 45.3348 42.037 45.1347C41.5603 44.9347 41.184 44.5345 40.9833 44.0593C40.8829 43.8343 40.8327 43.5591 40.8327 43.3091C40.8327 43.059 40.8829 42.8089 40.9833 42.5588C41.0836 42.3337 41.2341 42.1086 41.4098 41.9335C41.6105 41.7835 41.8362 41.6334 42.062 41.5334Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M28.5646 38.3071L28.6148 21.6008L37.3705 21.5007L41.2592 25.4272L41.1338 38.007L28.5646 38.3071Z"
                                                              fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M27.561 20.5752L27.4858 39.3824L42.1624 39.0323L42.2878 25.0019L37.797 20.4502L27.561 20.5752ZM28.5395 38.307L28.6147 21.6006L37.3705 21.5006L41.2592 25.4271L41.1338 38.0069L28.5395 38.307Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M36.7433 25.2772C36.7433 25.3522 36.7433 25.4022 36.7684 25.4773C36.7935 25.5523 36.8437 25.6023 36.8688 25.6523C36.919 25.7024 36.9691 25.7524 37.0444 25.7774C37.1197 25.8024 37.1698 25.8274 37.2451 25.8274L41.5352 25.9024C41.6355 25.9024 41.7359 25.8774 41.8363 25.8274C41.9366 25.7774 41.9868 25.6773 42.037 25.6023C42.0871 25.5023 42.0871 25.4022 42.062 25.3022C42.037 25.2022 41.9868 25.1021 41.9115 25.0271L37.6465 20.7005C37.5713 20.6254 37.4709 20.5754 37.3706 20.5504C37.2702 20.5254 37.1698 20.5504 37.0695 20.5754C36.9691 20.6254 36.8939 20.6754 36.8437 20.7755C36.7935 20.8505 36.7433 20.9506 36.7433 21.0756V25.2772ZM37.8221 24.752V22.3011L40.2808 24.752H37.8221Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M24.3749 21.9758C14.3396 25.6522 15.4435 40.4078 27.7367 42.8587C27.7367 42.8587 29.3424 44.9845 30.2957 45.2346C31.2491 45.4847 33.4066 44.3593 33.4066 44.3593L24.3749 21.9258V21.9758Z"
                                                              fill="#FEC34E"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M35.6144 45.2347L25.3533 19.75L23.7476 20.3502C22.2173 20.9004 20.8123 21.7508 19.6081 22.8512C18.4039 23.9516 17.4505 25.2771 16.7982 26.7777C16.1459 28.2532 15.7947 29.8538 15.7696 31.4795C15.7445 33.1051 16.0707 34.7057 16.6979 36.2063C17.5509 38.3071 18.9056 40.1578 20.6618 41.6083C22.418 43.0589 24.5003 44.0592 26.7331 44.5094C27.1596 45.0346 27.6363 45.5348 28.1381 45.985C28.6148 46.4351 29.1918 46.7853 29.819 46.9854C30.4713 47.1104 31.1487 47.0854 31.801 46.9103C32.6289 46.6602 33.4317 46.3601 34.2094 45.985L35.6144 45.2347ZM24.3748 21.9758C14.3396 25.6522 15.4434 40.4079 27.7367 42.8588C27.7367 42.8588 29.3423 44.9846 30.2957 45.2347C31.249 45.4848 33.4066 44.3594 33.4066 44.3594L24.3748 21.9258V21.9758Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M15.5439 26.3525L12.6587 25.3522C12.5834 25.3271 12.5333 25.3271 12.458 25.3271C12.3827 25.3271 12.3326 25.3522 12.2573 25.3772C12.182 25.4022 12.1319 25.4522 12.0817 25.5022C12.0315 25.5522 12.0064 25.6023 11.9813 25.6773C11.9562 25.7523 11.9562 25.8023 11.9562 25.8774C11.9562 25.9524 11.9813 26.0024 12.0064 26.0774C12.0315 26.1275 12.0817 26.2025 12.1319 26.2275C12.182 26.2775 12.2322 26.3025 12.3075 26.3275L15.1926 27.3279C15.3181 27.3779 15.4686 27.3779 15.594 27.3029C15.7195 27.2529 15.8198 27.1278 15.87 27.0028C15.9202 26.8777 15.9202 26.7277 15.8449 26.6026C15.7947 26.5026 15.6693 26.4026 15.5439 26.3525Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M14.1389 38.0069L11.3541 39.2324C11.2287 39.2824 11.1283 39.4074 11.0781 39.5325C11.0279 39.6575 11.0279 39.8076 11.1032 39.9326C11.1283 39.9827 11.1785 40.0577 11.2287 40.1077C11.2788 40.1577 11.329 40.1827 11.4043 40.2078C11.4795 40.2328 11.5297 40.2328 11.605 40.2328C11.6802 40.2328 11.7304 40.2078 11.8057 40.1827L14.5905 38.9573C14.6657 38.9323 14.7159 38.9073 14.7912 38.8572C14.8414 38.8072 14.8915 38.7572 14.9166 38.6822C14.9417 38.6071 14.9668 38.5321 14.9668 38.4571C14.9668 38.3821 14.9417 38.307 14.9166 38.232C14.8915 38.157 14.8414 38.107 14.7912 38.0569C14.741 38.0069 14.6657 37.9819 14.5905 37.9569C14.5152 37.9319 14.4399 37.9319 14.3647 37.9319C14.2894 37.9569 14.2141 37.9819 14.1389 38.0069Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M22.7943 46.0601L21.565 48.8612C21.5399 48.9362 21.5148 48.9862 21.5148 49.0613C21.5148 49.1363 21.5148 49.2113 21.5399 49.2613C21.565 49.3364 21.6152 49.3864 21.6403 49.4364C21.6904 49.4864 21.7406 49.5364 21.8159 49.5615C21.8912 49.5865 21.9413 49.6115 22.0166 49.6115C22.0919 49.6115 22.1671 49.6115 22.2173 49.5865C22.2926 49.5615 22.3427 49.5114 22.3929 49.4864C22.4431 49.4364 22.4933 49.3864 22.5184 49.3114L23.7477 46.5103C23.7979 46.3852 23.7979 46.2352 23.7477 46.1101C23.6975 45.9851 23.5972 45.8851 23.4717 45.81C23.3964 45.785 23.3463 45.76 23.271 45.76C23.1957 45.76 23.1205 45.76 23.0703 45.785C22.995 45.81 22.9449 45.86 22.8947 45.8851C22.8696 45.9351 22.8194 45.9851 22.7943 46.0601Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M21.9162 16.6238L23.1455 19.4249C23.1957 19.5499 23.296 19.6499 23.4215 19.7C23.5469 19.75 23.6975 19.75 23.8229 19.7C23.9483 19.6499 24.0487 19.5499 24.0989 19.4249C24.149 19.2998 24.149 19.1498 24.0989 19.0247L22.8695 16.2236C22.8194 16.0986 22.6939 16.0236 22.5685 15.9735C22.443 15.9235 22.3176 15.9235 22.1922 15.9735C22.0667 16.0236 21.9915 16.1236 21.9413 16.2487C21.866 16.3737 21.866 16.4987 21.9162 16.6238Z"
                                                              fill="#2A2941"/>
                                                    </g>
                                                    <g id="SVGRepo_iconCarrier">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M37.4207 34.5806L45.8002 31.1042L49.6888 39.5575L40.8327 43.1588L37.4207 34.5806Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M47.2553 27.7781L45.8002 31.1043L49.7139 39.6076L50.9433 36.1313L47.2553 27.7781Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M38.4995 34.1805L45.8002 31.1043L47.2553 27.678L39.3525 30.9042L38.4995 34.1805Z"
                                                              fill="#2A2941" stroke="#2A2941" stroke-width="1.8587"
                                                              stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M28.3388 47.5356C29.3674 51.2119 30.7975 53.1127 34.937 51.5371L32.4784 45.7349L28.3388 47.5356Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M34.4102 15.1233C33.6826 15.2733 33.0052 15.5485 32.378 15.9236L32.6791 17.3491C32.1522 17.7493 31.7006 18.2495 31.3494 18.8247L29.9194 18.6496C29.5932 19.3249 29.3925 20.0502 29.2922 20.7754L30.5968 21.4757C30.5717 22.151 30.6971 22.8262 30.9229 23.4765L29.9194 24.5019C30.2204 25.1771 30.6469 25.8024 31.1487 26.3526L32.4784 25.7523C32.9801 26.2025 33.5823 26.5526 34.2345 26.7777L34.4102 28.2533C35.1377 28.4284 35.8653 28.4784 36.6179 28.4283L36.9692 27.0028C37.6215 26.8778 38.2487 26.6277 38.8257 26.2775L40.055 27.0528C40.632 26.6027 41.1338 26.0524 41.5101 25.4272L40.632 24.2017C40.9331 23.6015 41.1338 22.9513 41.184 22.276L42.5638 21.8008C42.5638 21.0505 42.4384 20.3003 42.1875 19.6L40.7324 19.55C40.4564 18.9247 40.055 18.3495 39.5783 17.8743L40.0299 16.4738C39.4529 15.9986 38.8006 15.6235 38.0981 15.3484L37.1699 16.4738C36.5176 16.2987 35.8402 16.2487 35.1628 16.3237L34.4102 15.1233ZM34.4102 18.1494C35.3635 17.7993 36.3921 17.8493 37.3204 18.2495C38.2487 18.6496 38.9762 19.4249 39.3275 20.3503C39.5282 20.8005 39.6285 21.3006 39.6285 21.8008C39.6285 22.301 39.5282 22.8012 39.3525 23.2514C39.1518 23.7266 38.8759 24.1267 38.5246 24.5019C38.1734 24.852 37.7469 25.1271 37.2953 25.3272C36.342 25.6773 35.2883 25.6273 34.3851 25.2271C33.4568 24.802 32.7293 24.0517 32.378 23.1263C32.1773 22.6512 32.077 22.151 32.1021 21.6508C32.1021 21.1506 32.2024 20.6504 32.4031 20.1752C32.6038 19.7 32.9049 19.2999 33.2561 18.9497C33.6324 18.5996 34.0589 18.3245 34.5356 18.1494H34.4102Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M40.4062 39.6826C39.8041 39.8077 39.2522 40.0327 38.7253 40.3579L38.9511 41.5333C38.4995 41.8835 38.1232 42.2836 37.8472 42.7588L36.6179 42.6337C36.3419 43.1839 36.1663 43.7592 36.0911 44.3594L37.1448 44.9096C37.1197 45.4598 37.22 46.01 37.3957 46.5102L36.5677 47.3605C36.8437 47.9107 37.1949 48.4109 37.5964 48.8361L38.7002 48.3359C39.1267 48.686 39.6034 48.9611 40.1303 49.1362L40.2557 50.3617C40.8578 50.5117 41.4599 50.5618 42.0621 50.4867L42.3631 49.3363C42.9151 49.2363 43.4168 49.0362 43.8684 48.7361L44.897 49.3613C45.3737 48.9862 45.8002 48.561 46.1263 48.0358L45.3988 47.0854C45.6497 46.6103 45.8253 46.06 45.8755 45.5348L47.0044 45.1347C47.0044 44.5345 46.9292 43.9342 46.7285 43.359H45.4991C45.2733 42.8588 44.9472 42.4087 44.5709 42.0085L44.9472 40.8831C44.4705 40.5079 43.9437 40.2078 43.3666 39.9827L42.614 40.8831C42.0871 40.733 41.5352 40.708 40.9833 40.783L40.331 39.7827L40.4062 39.6826ZM40.4062 42.1586C41.184 41.8584 42.037 41.8584 42.7896 42.1836C43.5423 42.5087 44.1444 43.1089 44.4705 43.8842C44.6211 44.2594 44.6963 44.6595 44.6963 45.0847C44.6963 45.4848 44.596 45.885 44.4454 46.2601C44.2949 46.6353 44.044 46.9854 43.7681 47.2605C43.467 47.5356 43.1409 47.7607 42.7645 47.9107C41.9868 48.2109 41.1338 48.2109 40.3811 47.8857C39.6285 47.5606 39.0264 46.9604 38.7002 46.1851C38.5497 45.8099 38.4744 45.4098 38.4744 44.9846C38.4744 44.5845 38.5748 44.1593 38.7253 43.7842C38.8759 43.409 39.1267 43.0589 39.4278 42.7838C39.6787 42.5087 40.0299 42.2836 40.4062 42.1586Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M27.1848 40.4078L27.26 23.7265L36.0158 23.6265L39.9045 27.553L39.779 40.1327L27.1848 40.4078Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M35.9155 27.3778V23.1262L40.1805 27.4279L35.9155 27.3778Z"
                                                              fill="#2A2941" stroke="#2A2941" stroke-width="1.8587"
                                                              stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M22.1671 24.3269C12.1318 28.0033 13.2357 42.7589 25.5289 45.2098C25.5289 45.2098 27.1346 47.3357 28.0879 47.5857C29.0413 47.8358 31.1989 46.7104 31.1989 46.7104L22.1671 24.3269Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M38.8006 32.5049L47.1801 29.0286L51.0687 37.4818L42.2126 41.0831L38.8006 32.5049Z"
                                                              fill="#32EDBB"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M38.5999 32.0297C38.4744 32.0798 38.3741 32.1798 38.3239 32.3298C38.2737 32.4549 38.2737 32.605 38.3239 32.73L41.7359 41.3333C41.761 41.4083 41.7861 41.4583 41.8362 41.5083C41.8864 41.5584 41.9366 41.6084 42.0119 41.6334C42.0871 41.6584 42.1373 41.6834 42.2126 41.6834C42.2878 41.6834 42.3631 41.6584 42.4133 41.6334L51.2694 38.032C51.3948 37.982 51.4952 37.882 51.5705 37.7569C51.6206 37.6319 51.6206 37.4818 51.5705 37.3318L47.6818 28.8785C47.6316 28.7535 47.5313 28.6535 47.4058 28.6034C47.2804 28.5534 47.1298 28.5534 47.0044 28.6034L38.5999 32.0798V32.0297ZM39.4779 32.78L46.879 29.6788L50.3411 37.1817L42.4885 40.3829L39.4529 32.755L39.4779 32.78Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M48.6101 25.7024L47.1801 29.0287L51.0938 37.5319L52.3231 34.0556L48.6101 25.7024Z"
                                                              fill="#FEC34E"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M49.1118 25.4522C49.0617 25.3522 49.0115 25.2771 48.9111 25.2271C48.8359 25.1771 48.7355 25.1521 48.6352 25.1521C48.5348 25.1521 48.4345 25.1771 48.3341 25.2271C48.2588 25.2771 48.1836 25.3772 48.1334 25.4522L46.7034 28.7785C46.6783 28.8535 46.6532 28.9285 46.6532 29.0036C46.6532 29.0786 46.6783 29.1536 46.7034 29.2287L50.6171 37.7319C50.6673 37.8319 50.7426 37.907 50.8178 37.957C50.8931 38.007 50.9935 38.032 51.1189 38.032C51.2193 38.032 51.3196 37.982 51.42 37.932C51.4952 37.8819 51.5705 37.7819 51.6207 37.6819L52.9252 34.2055C52.9503 34.1305 52.9754 34.0805 52.9754 34.0055C52.9754 33.9304 52.9503 33.8554 52.9252 33.8054L49.1118 25.4522ZM48.6352 26.9778L51.8214 34.0555L51.0436 36.1563L47.7571 29.0036L48.6352 26.9778Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M39.8794 32.1048L47.1801 29.0286L48.6101 25.6023L40.7324 28.8285L39.8794 32.1048Z"
                                                              fill="#A694FE"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M39.4278 31.9546C39.4027 32.0547 39.4027 32.1547 39.4278 32.2297C39.4529 32.3298 39.5031 32.4048 39.5783 32.4798C39.6536 32.5549 39.754 32.5799 39.8292 32.6049C39.9296 32.6299 40.0299 32.6049 40.1052 32.5799L47.4059 29.5037C47.456 29.4787 47.5313 29.4287 47.5815 29.4037C47.6317 29.3786 47.6567 29.3036 47.6818 29.2286L49.1369 25.8023C49.1871 25.7022 49.1871 25.6022 49.162 25.5022C49.1369 25.4021 49.0868 25.3021 49.0115 25.2271C48.9362 25.152 48.8359 25.102 48.7355 25.077C48.6352 25.052 48.5348 25.077 48.4345 25.102L40.5568 28.3282C40.4815 28.3533 40.4063 28.4033 40.3561 28.4533C40.3059 28.5033 40.2557 28.5783 40.2306 28.6534L39.4278 31.9546ZM40.7324 31.2043L41.2593 29.2036L47.732 26.5526L46.879 28.6284L40.7073 31.2043H40.7324Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M30.5215 46.3601C31.4247 49.6364 32.7292 51.262 36.3419 49.8865L34.2094 44.7595L30.5215 46.3601Z"
                                                              fill="#A694FE"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M34.7864 43.209L29.1416 45.6599L29.4176 46.6103C30.095 49.0112 31.0483 50.6118 32.5787 51.212C33.9585 51.6372 35.4638 51.5372 36.7684 50.9119L37.8723 50.4867L34.7864 43.209ZM30.5215 46.3602C31.4246 49.6364 32.7292 51.262 36.3419 49.8865L34.2094 44.7596L30.5215 46.3602Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M35.3886 13.3477C34.661 13.4977 33.9836 13.7728 33.3564 14.148L33.6575 15.5985C33.1306 15.9987 32.679 16.4989 32.3278 17.0741L30.8978 16.899C30.5716 17.5743 30.3709 18.2745 30.2706 19.0248L31.5752 19.7251C31.5501 20.4003 31.6755 21.1006 31.9264 21.7258L30.8978 22.7762C31.2239 23.4515 31.6253 24.0767 32.1271 24.6269L33.4568 24.0517C33.9836 24.5019 34.5607 24.827 35.2129 25.0521L35.3886 26.5277C36.1161 26.7027 36.8437 26.7528 37.5963 26.7027L37.9476 25.2772C38.5999 25.1521 39.2271 24.902 39.8041 24.5519L41.0334 25.3272C41.6104 24.877 42.1122 24.3268 42.4885 23.7016L41.6104 22.4761C41.9115 21.8759 42.1122 21.2257 42.1624 20.5504L43.5422 20.0752C43.5422 19.3249 43.4168 18.5746 43.1659 17.8744L41.7108 17.8244C41.4348 17.1991 41.0334 16.6239 40.5567 16.1487L41.0083 14.7482C40.4313 14.273 39.779 13.8979 39.0765 13.6228L38.1483 14.7482C37.496 14.5731 36.8186 14.5231 36.1663 14.5981L35.3635 13.3727L35.3886 13.3477ZM35.3886 16.3738C36.3419 16.0237 37.3705 16.0737 38.2988 16.4738C39.2271 16.874 39.9546 17.6493 40.3058 18.5746C40.5066 19.0248 40.6069 19.525 40.6069 20.0252C40.6069 20.5254 40.5066 21.0256 40.3309 21.4757C40.1302 21.9509 39.8543 22.3511 39.503 22.7262C39.1518 23.0764 38.7253 23.3515 38.2737 23.5515C37.3204 23.9017 36.2917 23.8517 35.3635 23.4515C34.4352 23.0513 33.7077 22.2761 33.3564 21.3507C33.1557 20.9005 33.0554 20.4003 33.0554 19.9001C33.0554 19.4 33.1557 18.8998 33.3313 18.4246C33.532 17.9494 33.808 17.5493 34.1592 17.1741C34.4854 16.849 34.9119 16.5489 35.3886 16.3738Z"
                                                              fill="#FEC34E"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M36.367 12.7224L35.9405 12.0471L35.1628 12.2222C34.3098 12.3973 33.507 12.7224 32.7543 13.1725L32.0518 13.5977L32.378 15.1733C32.1522 15.3984 31.9515 15.6235 31.7508 15.8736L30.1702 15.6985L29.819 16.4238C29.4427 17.1991 29.2169 18.0244 29.1165 18.8747L29.0413 19.65L30.4462 20.4003C30.4713 20.7254 30.5215 21.0505 30.5967 21.3757L29.4928 22.5011L29.8441 23.2014C30.2204 24.0017 30.7222 24.7269 31.3243 25.3522L31.8762 25.9274L33.2561 25.3772C33.532 25.5523 33.808 25.7273 34.1091 25.8774L34.3098 27.453L35.0624 27.628C35.9154 27.8531 36.7935 27.9031 37.6465 27.8281L38.4744 27.7531L38.8507 26.2025C39.1518 26.1025 39.4278 26.0024 39.7037 25.8774L41.0585 26.7277L41.7108 26.2275C42.3882 25.6773 42.9652 25.0271 43.4419 24.3018L43.8684 23.6265L42.915 22.326C43.0405 22.0259 43.1157 21.7258 43.191 21.4007L44.6712 20.8755V20.0752C44.6712 19.1998 44.5207 18.3245 44.2447 17.4992L43.9938 16.7489L42.4133 16.6989C42.2377 16.4238 42.062 16.1487 41.8362 15.8736L42.338 14.373L41.7359 13.8728C41.0585 13.3226 40.3059 12.8724 39.4779 12.5473L38.7253 12.2472L37.7218 13.4727C37.3956 13.4226 37.0946 13.3976 36.7684 13.3976L36.3419 12.6974L36.367 12.7224ZM35.3886 13.3476C34.661 13.4977 33.9836 13.7728 33.3564 14.1479L33.6575 15.5985C33.1306 15.9986 32.679 16.4988 32.3278 17.074L30.8978 16.899C30.5716 17.5742 30.3709 18.2745 30.2706 19.0248L31.5752 19.725C31.5501 20.4003 31.6755 21.1006 31.9264 21.7258L30.8978 22.7762C31.2239 23.4515 31.6253 24.0767 32.1271 24.6269L33.4568 24.0517C33.9836 24.5019 34.5607 24.827 35.213 25.0521L35.3886 26.5276C36.1161 26.7027 36.8437 26.7527 37.5963 26.7027L37.9476 25.2771C38.5999 25.1521 39.2271 24.902 39.8041 24.5519L41.0334 25.3272C41.6104 24.877 42.1122 24.3268 42.4885 23.7016L41.6104 22.4761C41.9115 21.8759 42.1122 21.2256 42.1624 20.5504L43.5422 20.0752C43.5422 19.3249 43.4168 18.5746 43.1659 17.8743L41.7108 17.8243C41.4348 17.1991 41.0334 16.6239 40.5567 16.1487L41.0083 14.7481C40.4313 14.273 39.779 13.8978 39.0765 13.6227L38.1483 14.7481C37.496 14.5731 36.8186 14.5231 36.1663 14.5981L35.3635 13.3726L35.3886 13.3476ZM35.3886 16.3738C36.3419 16.0236 37.3705 16.0737 38.2988 16.4738C39.2271 16.874 39.9546 17.6492 40.3059 18.5746C40.5066 19.0248 40.6069 19.525 40.6069 20.0252C40.6069 20.5253 40.5066 21.0255 40.3309 21.4757C40.1302 21.9509 39.8543 22.351 39.503 22.7262C39.1518 23.0763 38.7253 23.3514 38.2737 23.5515C37.3204 23.9016 36.2917 23.8516 35.3635 23.4515C34.4352 23.0513 33.7077 22.276 33.3564 21.3507C33.1557 20.9005 33.0554 20.4003 33.0554 19.9001C33.0554 19.3999 33.1557 18.8997 33.3313 18.4245C33.532 17.9494 33.808 17.5492 34.1593 17.1741C34.4854 16.8489 34.9119 16.5488 35.3886 16.3738ZM35.8151 17.4242C36.4925 17.1741 37.22 17.1991 37.8723 17.4992C38.5246 17.7993 39.0264 18.3245 39.2772 18.9998C39.4278 19.3249 39.503 19.675 39.503 20.0252C39.503 20.3753 39.4529 20.7254 39.3023 21.0505C39.1769 21.3757 38.9762 21.6758 38.7253 21.9259C38.4744 22.176 38.1734 22.376 37.8472 22.5011C37.1698 22.7512 36.4423 22.7262 35.79 22.4261C35.1377 22.126 34.6359 21.6008 34.385 20.9255C34.2596 20.6004 34.1843 20.2502 34.1593 19.9001C34.1593 19.55 34.2345 19.1998 34.36 18.8747C34.4854 18.5496 34.6861 18.2495 34.937 17.9994C35.1879 17.7743 35.4889 17.5742 35.8151 17.4242Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M41.6355 38.0071C41.0334 38.1321 40.4564 38.3572 39.9546 38.6823L40.1804 39.8578C39.7288 40.1829 39.3525 40.6081 39.0765 41.0833L37.8472 40.9582C37.5963 41.5084 37.4207 42.0836 37.3455 42.6839L38.3992 43.2341C38.3741 43.7843 38.4744 44.3345 38.65 44.8347L37.8221 45.66C38.073 46.2102 38.4243 46.7104 38.8508 47.1356L39.9295 46.6604C40.356 47.0105 40.8327 47.2856 41.3596 47.4607L41.5101 48.6861C42.0871 48.8362 42.7143 48.8612 43.3165 48.8112L43.6175 47.6608C44.1694 47.5607 44.6712 47.3606 45.1479 47.0605L46.1514 47.6858C46.6281 47.3106 47.0546 46.8604 47.3807 46.3603L46.6783 45.4099C46.9292 44.9097 47.0797 44.3845 47.1549 43.8593L48.2839 43.4592C48.2839 42.8589 48.1836 42.2587 48.0079 41.6835H46.7786C46.5528 41.1833 46.2267 40.7331 45.8504 40.333L46.2267 39.2075C45.75 38.8324 45.2232 38.5323 44.6461 38.3072L43.8935 39.2075C43.3666 39.0575 42.8147 39.0325 42.2627 39.0825L41.6105 38.0821L41.6355 38.0071ZM41.6355 40.483C42.4133 40.1829 43.2663 40.1829 44.0189 40.533C44.7716 40.8582 45.3737 41.4834 45.6747 42.2337C45.8253 42.6088 45.9005 43.009 45.9005 43.4341C45.9005 43.8343 45.8253 44.2344 45.6497 44.6096C45.4991 44.9847 45.2482 45.3349 44.9723 45.61C44.6712 45.8851 44.3451 46.1102 43.9437 46.2602C43.1659 46.5603 42.3129 46.5603 41.5603 46.2102C40.8076 45.8851 40.2055 45.2598 39.9045 44.5096C39.7539 44.1344 39.6787 43.7343 39.6787 43.3091C39.6787 42.9089 39.7539 42.4838 39.9295 42.1086C40.0801 41.7335 40.331 41.3834 40.632 41.1083C40.9331 40.8332 41.2843 40.6081 41.6355 40.483Z"
                                                              fill="#FEC34E"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M42.614 37.3818L42.1875 36.7065L41.4098 36.8566C40.7073 37.0317 40.055 37.3068 39.4278 37.6569L38.7253 38.0821L38.9762 39.3826C38.8508 39.5076 38.7253 39.6577 38.5999 39.8077L37.2702 39.6827L36.919 40.3829C36.5928 41.0332 36.367 41.7335 36.2918 42.4837L36.1914 43.284L37.4207 43.9093C37.4458 44.1094 37.4709 44.2844 37.496 44.4845L36.5677 45.4099L36.919 46.1351C37.22 46.8104 37.6465 47.4106 38.1483 47.9358L38.7002 48.511L39.9295 47.9608C40.0801 48.0609 40.2557 48.1609 40.4313 48.2609L40.5818 49.5614L41.3596 49.7615C42.062 49.9366 42.8147 49.9866 43.5422 49.9116L44.3451 49.8365L44.6712 48.6111L45.2232 48.411L46.3521 49.1113L47.0044 48.6111C47.5814 48.1609 48.0832 47.6107 48.4846 47.0105L48.9111 46.3352L48.1083 45.2848C48.1836 45.1097 48.2337 44.9347 48.2588 44.7346L49.4882 44.2844V43.4841C49.4882 42.7588 49.3878 42.0336 49.1369 41.3333L48.886 40.583H47.5564L47.2302 40.1078L47.6567 38.8824L47.0295 38.3822C46.4525 37.932 45.8002 37.5569 45.1228 37.3068L44.3702 37.0317L43.5172 38.032H42.915L42.6391 37.5819L42.614 37.3818ZM41.6355 38.007C41.0334 38.1321 40.4564 38.3572 39.9546 38.6823L40.1804 39.8577C39.7288 40.1829 39.3525 40.608 39.0765 41.0832L37.8472 40.9582C37.5963 41.5084 37.4207 42.0836 37.3455 42.6838L38.3992 43.234C38.3741 43.7842 38.4744 44.3344 38.6501 44.8346L37.8221 45.6599C38.073 46.2102 38.4243 46.7103 38.8508 47.1355L39.9295 46.6603C40.356 47.0105 40.8327 47.2856 41.3596 47.4606L41.5101 48.6861C42.0871 48.8362 42.7143 48.8612 43.3165 48.8111L43.6175 47.6607C44.1695 47.5607 44.6712 47.3606 45.1479 47.0605L46.1514 47.6857C46.6281 47.3106 47.0546 46.8604 47.3807 46.3602L46.6783 45.4099C46.9292 44.9097 47.0797 44.3845 47.1549 43.8593L48.2839 43.4591C48.2839 42.8589 48.1836 42.2587 48.0079 41.6834H46.7786C46.5528 41.1832 46.2267 40.7331 45.8504 40.3329L46.2267 39.2075C45.75 38.8324 45.2232 38.5322 44.6461 38.3072L43.8935 39.2075C43.3666 39.0574 42.8147 39.0324 42.2627 39.0824L41.6105 38.0821L41.6355 38.007ZM41.6355 40.458C42.4133 40.1579 43.2663 40.1579 44.0189 40.508C44.7716 40.8331 45.3737 41.4583 45.6747 42.2086C45.8253 42.5838 45.9005 42.9839 45.9005 43.4091C45.9005 43.8092 45.8253 44.2094 45.6497 44.5845C45.4991 44.9597 45.2482 45.3098 44.9723 45.5849C44.6712 45.86 44.3451 46.0851 43.9437 46.2352C43.1659 46.5353 42.3129 46.5353 41.5603 46.1851C40.8076 45.86 40.2055 45.2348 39.9045 44.4845C39.7539 44.1094 39.6787 43.7092 39.6787 43.284C39.6787 42.8839 39.7539 42.4587 39.9295 42.0836C40.0801 41.7084 40.331 41.3583 40.632 41.0832C40.9331 40.8081 41.2843 40.583 41.6355 40.458ZM42.062 41.5334C42.5387 41.3333 43.0907 41.3583 43.5673 41.5584C44.044 41.7585 44.4203 42.1586 44.621 42.6338C44.7214 42.8589 44.7716 43.134 44.7716 43.3841C44.7716 43.6342 44.7214 43.8843 44.621 44.1344C44.5207 44.3595 44.3702 44.5845 44.1945 44.7596C44.0189 44.9347 43.7931 45.0847 43.5422 45.1598C43.0656 45.3598 42.5136 45.3348 42.037 45.1347C41.5603 44.9347 41.184 44.5345 40.9833 44.0593C40.8829 43.8343 40.8327 43.5591 40.8327 43.3091C40.8327 43.059 40.8829 42.8089 40.9833 42.5588C41.0836 42.3337 41.2341 42.1086 41.4098 41.9335C41.6105 41.7835 41.8362 41.6334 42.062 41.5334Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M28.5646 38.3071L28.6148 21.6008L37.3705 21.5007L41.2592 25.4272L41.1338 38.007L28.5646 38.3071Z"
                                                              fill="white"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M27.561 20.5752L27.4858 39.3824L42.1624 39.0323L42.2878 25.0019L37.797 20.4502L27.561 20.5752ZM28.5395 38.307L28.6147 21.6006L37.3705 21.5006L41.2592 25.4271L41.1338 38.0069L28.5395 38.307Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M36.7433 25.2772C36.7433 25.3522 36.7433 25.4022 36.7684 25.4773C36.7935 25.5523 36.8437 25.6023 36.8688 25.6523C36.919 25.7024 36.9691 25.7524 37.0444 25.7774C37.1197 25.8024 37.1698 25.8274 37.2451 25.8274L41.5352 25.9024C41.6355 25.9024 41.7359 25.8774 41.8363 25.8274C41.9366 25.7774 41.9868 25.6773 42.037 25.6023C42.0871 25.5023 42.0871 25.4022 42.062 25.3022C42.037 25.2022 41.9868 25.1021 41.9115 25.0271L37.6465 20.7005C37.5713 20.6254 37.4709 20.5754 37.3706 20.5504C37.2702 20.5254 37.1698 20.5504 37.0695 20.5754C36.9691 20.6254 36.8939 20.6754 36.8437 20.7755C36.7935 20.8505 36.7433 20.9506 36.7433 21.0756V25.2772ZM37.8221 24.752V22.3011L40.2808 24.752H37.8221Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M24.3749 21.9758C14.3396 25.6522 15.4435 40.4078 27.7367 42.8587C27.7367 42.8587 29.3424 44.9845 30.2957 45.2346C31.2491 45.4847 33.4066 44.3593 33.4066 44.3593L24.3749 21.9258V21.9758Z"
                                                              fill="#FEC34E"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M35.6144 45.2347L25.3533 19.75L23.7476 20.3502C22.2173 20.9004 20.8123 21.7508 19.6081 22.8512C18.4039 23.9516 17.4505 25.2771 16.7982 26.7777C16.1459 28.2532 15.7947 29.8538 15.7696 31.4795C15.7445 33.1051 16.0707 34.7057 16.6979 36.2063C17.5509 38.3071 18.9056 40.1578 20.6618 41.6083C22.418 43.0589 24.5003 44.0592 26.7331 44.5094C27.1596 45.0346 27.6363 45.5348 28.1381 45.985C28.6148 46.4351 29.1918 46.7853 29.819 46.9854C30.4713 47.1104 31.1487 47.0854 31.801 46.9103C32.6289 46.6602 33.4317 46.3601 34.2094 45.985L35.6144 45.2347ZM24.3748 21.9758C14.3396 25.6522 15.4434 40.4079 27.7367 42.8588C27.7367 42.8588 29.3423 44.9846 30.2957 45.2347C31.249 45.4848 33.4066 44.3594 33.4066 44.3594L24.3748 21.9258V21.9758Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M15.5439 26.3525L12.6587 25.3522C12.5834 25.3271 12.5333 25.3271 12.458 25.3271C12.3827 25.3271 12.3326 25.3522 12.2573 25.3772C12.182 25.4022 12.1319 25.4522 12.0817 25.5022C12.0315 25.5522 12.0064 25.6023 11.9813 25.6773C11.9562 25.7523 11.9562 25.8023 11.9562 25.8774C11.9562 25.9524 11.9813 26.0024 12.0064 26.0774C12.0315 26.1275 12.0817 26.2025 12.1319 26.2275C12.182 26.2775 12.2322 26.3025 12.3075 26.3275L15.1926 27.3279C15.3181 27.3779 15.4686 27.3779 15.594 27.3029C15.7195 27.2529 15.8198 27.1278 15.87 27.0028C15.9202 26.8777 15.9202 26.7277 15.8449 26.6026C15.7947 26.5026 15.6693 26.4026 15.5439 26.3525Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M14.1389 38.0069L11.3541 39.2324C11.2287 39.2824 11.1283 39.4074 11.0781 39.5325C11.0279 39.6575 11.0279 39.8076 11.1032 39.9326C11.1283 39.9827 11.1785 40.0577 11.2287 40.1077C11.2788 40.1577 11.329 40.1827 11.4043 40.2078C11.4795 40.2328 11.5297 40.2328 11.605 40.2328C11.6802 40.2328 11.7304 40.2078 11.8057 40.1827L14.5905 38.9573C14.6657 38.9323 14.7159 38.9073 14.7912 38.8572C14.8414 38.8072 14.8915 38.7572 14.9166 38.6822C14.9417 38.6071 14.9668 38.5321 14.9668 38.4571C14.9668 38.3821 14.9417 38.307 14.9166 38.232C14.8915 38.157 14.8414 38.107 14.7912 38.0569C14.741 38.0069 14.6657 37.9819 14.5905 37.9569C14.5152 37.9319 14.4399 37.9319 14.3647 37.9319C14.2894 37.9569 14.2141 37.9819 14.1389 38.0069Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M22.7943 46.0601L21.565 48.8612C21.5399 48.9362 21.5148 48.9862 21.5148 49.0613C21.5148 49.1363 21.5148 49.2113 21.5399 49.2613C21.565 49.3364 21.6152 49.3864 21.6403 49.4364C21.6904 49.4864 21.7406 49.5364 21.8159 49.5615C21.8912 49.5865 21.9413 49.6115 22.0166 49.6115C22.0919 49.6115 22.1671 49.6115 22.2173 49.5865C22.2926 49.5615 22.3427 49.5114 22.3929 49.4864C22.4431 49.4364 22.4933 49.3864 22.5184 49.3114L23.7477 46.5103C23.7979 46.3852 23.7979 46.2352 23.7477 46.1101C23.6975 45.9851 23.5972 45.8851 23.4717 45.81C23.3964 45.785 23.3463 45.76 23.271 45.76C23.1957 45.76 23.1205 45.76 23.0703 45.785C22.995 45.81 22.9449 45.86 22.8947 45.8851C22.8696 45.9351 22.8194 45.9851 22.7943 46.0601Z"
                                                              fill="#2A2941"/>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                              d="M21.9162 16.6238L23.1455 19.4249C23.1957 19.5499 23.296 19.6499 23.4215 19.7C23.5469 19.75 23.6975 19.75 23.8229 19.7C23.9483 19.6499 24.0487 19.5499 24.0989 19.4249C24.149 19.2998 24.149 19.1498 24.0989 19.0247L22.8695 16.2236C22.8194 16.0986 22.6939 16.0236 22.5685 15.9735C22.443 15.9235 22.3176 15.9235 22.1922 15.9735C22.0667 16.0236 21.9915 16.1236 21.9413 16.2487C21.866 16.3737 21.866 16.4987 21.9162 16.6238Z"
                                                              fill="#2A2941"/>
                                                    </g>
                                                </svg>
                                                <span class="hide-menu">Хомашёлар</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                @php  // Finished products (Тайёр маҳсулотлар) @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                       aria-expanded="false">
                                        <span class="hide-menu">📤 Тайёр маҳсулотлар</span>
                                    </a>
                                    <ul aria-expanded="false" class="collapse second-level ps-3">
                                        @php // Product @endphp
                                        <li class="sidebar-item">
                                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                               href="{{ route('product.index') }}" aria-expanded="false">
                                                <svg width="25px" height="25px" viewBox="0 0 73 73" version="1.1"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     class="me-2">
                                                    <title>databases-and-servers/servers/architectural-models</title>
                                                    <desc>Created with Sketch.</desc>
                                                    <defs></defs>
                                                    <g id="databases-and-servers/servers/architectural-models"
                                                       stroke="none"
                                                       stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <g id="container" transform="translate(2.000000, 2.000000)"
                                                           fill="none" fill-rule="nonzero" stroke="#2C3C40"
                                                           stroke-width="2">
                                                            <rect id="mask" x="-1" y="-1" width="71" height="71"
                                                                  rx="14"></rect>
                                                        </g>
                                                        <g id="sketch-(1)" transform="translate(14.000000, 14.000000)"
                                                           fill-rule="nonzero">
                                                            <path
                                                                d="M41.1534211,9.16289474 L0.00305803169,9.16289474 C0.00305803169,9.16289474 0.0384210526,33.6733333 0.00324561404,35.0295614 C0.00298245614,35.0412281 0.00298245614,35.0528947 0.00333333333,35.0644737 C0.0386842105,36.3694737 0.573333333,37.590614 1.50868421,38.5030702 C2.44622807,39.4174561 3.68377193,39.9211404 4.99368421,39.9211404 L26.7289474,39.9211404 C26.9037719,39.9211404 27.0714912,39.8516667 27.1950877,39.7281579 L41.6196491,25.3035965 C41.7432456,25.1799123 41.8126316,25.0123684 41.8126316,24.8374561 L41.8126316,9.82201754 C41.8126316,9.45789474 41.5174561,9.16289474 41.1534211,9.16289474 Z"
                                                                id="Shape" fill="#E0E0E2"></path>
                                                            <g id="Group" transform="translate(11.842105, 9.122807)"
                                                               fill="#C6C5CA">
                                                                <polygon id="Shape"
                                                                         points="0.0842105263 17.2764912 2.77789474 17.2764912 4.93780702 10.4985088 15.3963158 0.0400877193 12.9353509 0.0400877193 2.47684211 10.4985088"></polygon>
                                                                <path
                                                                    d="M29.3113158,0.0400877193 L27.1140351,0.0400877193 L27.1140351,18.8442105 L29.7774561,16.1807895 C29.9010526,16.0571053 29.9704386,15.8895614 29.9704386,15.7146491 L29.9704386,0.699210526 C29.9705263,0.335087719 29.6753509,0.0400877193 29.3113158,0.0400877193 Z"
                                                                    id="Shape"></path>
                                                            </g>
                                                            <path
                                                                d="M30.035614,26.9392982 L11.78,26.9392982 C11.4159649,26.9392982 11.1207895,26.6442105 11.1207895,26.2800877 C11.1207895,25.9159649 11.4159649,25.6208772 11.78,25.6208772 L30.035614,25.6208772 C30.3997368,25.6208772 30.6948246,25.9159649 30.6948246,26.2800877 C30.6948246,26.6442105 30.3996491,26.9392982 30.035614,26.9392982 Z"
                                                                id="Shape" fill="#3C5156"></path>
                                                            <path
                                                                d="M5.00947368,29.8042105 C5.29377193,29.8042105 5.5722807,29.8288596 5.84385965,29.8739474 L5.84385965,2.64140351 C5.84385965,2.2772807 5.54868421,1.98219298 5.18464912,1.98219298 C2.32587719,1.98219298 0,4.30798246 0,7.16684211 L0,34.1662281 C0.339561404,31.7022807 2.4522807,29.8042105 5.00947368,29.8042105 Z"
                                                                id="Shape" fill="#919191"></path>
                                                            <path
                                                                d="M35.7852632,8.53385965 L31.8264035,4.57491228 C31.7027193,4.45131579 31.5350877,4.38192982 31.3602632,4.38192982 C31.1854386,4.38192982 31.017807,4.45140351 30.8941228,4.57491228 L16.3137719,19.1551754 C16.305,19.1639474 16.297193,19.1733333 16.2889474,19.1826316 L21.2512281,23.9999123 L35.7851754,9.46614035 C36.0427193,9.20859649 36.0427193,8.79131579 35.7852632,8.53385965 Z"
                                                                id="Shape" fill="#FFD039"></path>
                                                            <path
                                                                d="M19.4402632,22.2419298 L21.2512281,24 L35.7851754,9.46622807 C36.0426316,9.20868421 36.0426316,8.79140351 35.7851754,8.53394737 L34.4659649,7.21473684 L19.4402632,22.2419298 Z"
                                                                id="Shape" fill="#FFA304"></path>
                                                            <path
                                                                d="M16.2907018,19.1842982 C16.2323684,19.2484211 16.1858772,19.3238596 16.1560526,19.4082456 L13.9983333,25.5207895 C13.9138596,25.7601754 13.9742982,26.0267544 14.1537719,26.2063158 C14.2795614,26.3321053 14.4480702,26.3992982 14.6199123,26.3992982 C14.6935088,26.3992982 14.7675439,26.3870175 14.8392982,26.3616667 L20.9517544,24.2039474 C21.069386,24.1624561 21.1697368,24.0884211 21.2454386,23.9942105 L16.2907018,19.1842982 Z"
                                                                id="Shape" fill="#FFC89F"></path>
                                                            <path
                                                                d="M14.62,26.3992982 C14.6935965,26.3992982 14.7676316,26.3870175 14.839386,26.3616667 L20.9518421,24.2039474 C21.0694737,24.1624561 21.1698246,24.0884211 21.2455263,23.9942105 L19.4382456,22.2397368 L14.5374561,23.9936842 L13.9984211,25.520614 C13.9139474,25.76 13.974386,26.0265789 14.1538596,26.2061404 C14.2796491,26.3320175 14.4480702,26.3992982 14.62,26.3992982 Z"
                                                                id="Shape" fill="#F7B081"></path>
                                                            <path
                                                                d="M14.62,26.3992982 C14.6935965,26.3992982 14.7676316,26.3870175 14.839386,26.3616667 L18.6941228,25.0009649 L15.3528947,21.6836842 L13.9985088,25.520614 C13.9140351,25.76 13.9744737,26.0265789 14.1539474,26.2061404 C14.2796491,26.3320175 14.4480702,26.3992982 14.62,26.3992982 Z"
                                                                id="Shape" fill="#3C5156"></path>
                                                            <path
                                                                d="M16.8470175,23.167193 L14.5374561,23.9937719 L13.9984211,25.5207018 C13.9139474,25.7600877 13.974386,26.0266667 14.1538596,26.2062281 C14.2796491,26.3320175 14.4481579,26.3992105 14.62,26.3992105 C14.6935965,26.3992105 14.7676316,26.3869298 14.839386,26.3615789 L18.6941228,25.0008772 L16.8470175,23.167193 Z"
                                                                id="Shape" fill="#304144"></path>
                                                            <path
                                                                d="M37.9271053,6.39192982 L33.9681579,2.43307018 C33.710614,2.17570175 33.2933333,2.17570175 33.0358772,2.43307018 L30.4297368,5.03921053 L35.3189474,9.93245614 L37.9271053,7.32429825 C38.0507018,7.20061404 38.1200877,7.03307018 38.1200877,6.85815789 C38.1200877,6.68333333 38.050614,6.51570175 37.9271053,6.39192982 Z"
                                                                id="Shape" fill="#FF6137"></path>
                                                            <path
                                                                d="M33.5342105,8.14631579 L34.1535965,8.76614035 L35.319386,9.93192982 L37.9271053,7.32421053 C38.0507018,7.20052632 38.1200877,7.03298246 38.1200877,6.85807018 C38.1200877,6.68324561 38.050614,6.51561404 37.9271053,6.39192982 L36.6078947,5.0727193 L33.5342105,8.14631579 Z"
                                                                id="Shape" fill="#E04F32"></path>
                                                            <path
                                                                d="M39.6994737,2.70921053 L37.6507895,0.660614035 C37.2712281,0.281052632 36.7663158,0.0720175439 36.2294737,0.0720175439 C35.6926316,0.0720175439 35.187807,0.281052632 34.8081579,0.660614035 L33.5028947,1.96587719 L33.0357018,2.43307018 L32.5607018,2.90807018 L37.4609649,7.79026316 L38.1607895,7.09035088 L39.699386,5.55175439 C40.0789474,5.17219298 40.2879825,4.6672807 40.2879825,4.1304386 C40.2879825,3.59359649 40.0790351,3.08885965 39.6994737,2.70921053 Z"
                                                                id="Shape" fill="#546F7A"></path>
                                                            <g id="Group" transform="translate(32.543860, 1.052632)"
                                                               fill="#475D63">
                                                                <polygon id="Shape"
                                                                         points="0.025 1.84745614 0.0169298246 1.85552632 1.47894737 3.31201754"></polygon>
                                                                <path
                                                                    d="M7.15561404,1.65657895 L5.53894737,0.0399122807 C5.80333333,0.391315789 5.94842105,0.817017544 5.94842105,1.26614035 C5.94842105,1.80894737 5.73938596,2.31929825 5.35982456,2.70324561 L3.82122807,4.25894737 L3.13052632,4.95754386 L4.91736842,6.73780702 L5.61719298,6.03789474 L7.15578947,4.49929825 C7.53535088,4.11973684 7.74438596,3.61482456 7.74438596,3.07798246 C7.74438596,2.54114035 7.53517544,2.03622807 7.15561404,1.65657895 Z"
                                                                    id="Shape"></path>
                                                            </g>
                                                            <path
                                                                d="M44.5053509,21.1287719 C44.2590351,21.0266667 43.9755263,21.0830702 43.7869298,21.2715789 L21.3435965,43.7149123 C21.1550877,43.9035088 21.0986842,44.1870175 21.2007018,44.4333333 C21.3027193,44.6796491 21.5431579,44.8402632 21.8097368,44.8402632 L44.2531579,44.8402632 C44.617193,44.8402632 44.9123684,44.5451754 44.9123684,44.1810526 L44.9123684,21.737807 C44.9122807,21.4712281 44.7515789,21.2307018 44.5053509,21.1287719 Z M32.2835088,39.5079825 L39.4875439,32.3039474 L39.4875439,39.5079825 L32.2835088,39.5079825 Z"
                                                                id="Shape" fill="#FFCA10"></path>
                                                            <path
                                                                d="M44.5053509,21.1287719 C44.2590351,21.0266667 43.9755263,21.0830702 43.7869298,21.2715789 L42.0557018,23.002807 L42.0557018,44.8403509 L44.2529825,44.8403509 C44.6170175,44.8403509 44.912193,44.5452632 44.912193,44.1811404 L44.912193,21.737807 C44.9122807,21.4712281 44.7515789,21.2307018 44.5053509,21.1287719 Z"
                                                                id="Shape" fill="#FFA304"></path>
                                                            <g id="Group" transform="translate(41.666667, 29.122807)"
                                                               fill="#C96E14">
                                                                <path
                                                                    d="M3.24561404,0.0221929825 L0.711052632,0.0221929825 C0.346929825,0.0221929825 0.0518421053,0.317280702 0.0518421053,0.681403509 C0.0518421053,1.04552632 0.346929825,1.34061404 0.711052632,1.34061404 L3.24561404,1.34061404 L3.24561404,0.0221929825 Z"
                                                                    id="Shape"></path>
                                                                <path
                                                                    d="M3.24561404,3.32710526 L0.711052632,3.32710526 C0.346929825,3.32710526 0.0518421053,3.62219298 0.0518421053,3.98631579 C0.0518421053,4.3504386 0.346929825,4.64552632 0.711052632,4.64552632 L3.24561404,4.64552632 L3.24561404,3.32710526 Z"
                                                                    id="Shape"></path>
                                                                <path
                                                                    d="M3.24561404,6.63175439 L0.711052632,6.63175439 C0.346929825,6.63175439 0.0518421053,6.92684211 0.0518421053,7.29096491 C0.0518421053,7.65508772 0.346929825,7.95017544 0.711052632,7.95017544 L3.24561404,7.95017544 L3.24561404,6.63175439 Z"
                                                                    id="Shape"></path>
                                                                <path
                                                                    d="M3.24561404,9.93666667 L0.711052632,9.93666667 C0.346929825,9.93666667 0.0518421053,10.2318421 0.0518421053,10.5958772 C0.0518421053,10.96 0.346929825,11.2550877 0.711052632,11.2550877 L3.24561404,11.2550877 L3.24561404,9.93666667 Z"
                                                                    id="Shape"></path>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                                <span class="hide-menu">Маҳсулот турлари</span>
                                            </a>
                                        </li>

                                        @php // Product Variation @endphp
                                        <li class="sidebar-item">
                                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                               href="{{ route('product-variation.index') }}" aria-expanded="false">
                                                <svg width="25px" height="25px" viewBox="0 0 73 73" version="1.1"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     fill="#000000" class="me-2">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                       stroke-linejoin="round"/>
                                                    <g id="SVGRepo_iconCarrier"><title>
                                                            fundamentals/css/box-model</title>
                                                        <desc>Created with Sketch.</desc>
                                                        <defs></defs>
                                                        <g id="fundamentals/css/box-model" stroke="none"
                                                           stroke-width="1"
                                                           fill="none" fill-rule="evenodd">
                                                            <rect id="mask" stroke="#72512E" stroke-width="2"
                                                                  fill="none"
                                                                  fill-rule="nonzero" x="1" y="1" width="71" height="71"
                                                                  rx="14"></rect>
                                                            <g id="box" transform="translate(16.000000, 16.000000)"
                                                               fill-rule="nonzero">
                                                                <polygon id="Shape" fill="#A98258"
                                                                         points="34.7586207 0 7.24137931 0 0 11.5862069 0 42 42 42 42 11.5862069"></polygon>
                                                                <polygon id="Shape" fill="#DAAE86"
                                                                         points="7.24137931 0 0 12 42 12 34.7586207 0"></polygon>
                                                                <polygon id="Shape" fill="#D8B18B"
                                                                         points="23.5 39.5 20.5 37 17.5 39.5 16 38.25 16 42 25 42 25 38.25"></polygon>
                                                                <rect id="Rectangle-path" fill="#E8D5B2" x="14" y="23"
                                                                      width="13" height="12"></rect>
                                                                <g id="Group"
                                                                   transform="translate(16.000000, 25.000000)"
                                                                   fill="#D4C3A5">
                                                                    <path
                                                                        d="M5.25,6.4 L0.75,6.4 C0.336,6.4 0,6.7576 0,7.2 C0,7.6424 0.336,8 0.75,8 L5.25,8 C5.664,8 6,7.6424 6,7.2 C6,6.7576 5.664,6.4 5.25,6.4 Z"
                                                                        id="Shape"></path>
                                                                    <path
                                                                        d="M8.25,6.4 L7.5,6.4 C7.086,6.4 6.75,6.7576 6.75,7.2 C6.75,7.6424 7.086,8 7.5,8 L8.25,8 C8.664,8 9,7.6424 9,7.2 C9,6.7576 8.664,6.4 8.25,6.4 Z"
                                                                        id="Shape"></path>
                                                                    <path
                                                                        d="M3.75,1.6 L8.25,1.6 C8.664,1.6 9,1.2424 9,0.8 C9,0.3576 8.664,0 8.25,0 L3.75,0 C3.336,0 3,0.3576 3,0.8 C3,1.2424 3.336,1.6 3.75,1.6 Z"
                                                                        id="Shape"></path>
                                                                    <path
                                                                        d="M8.25,3.2 L6.75,3.2 C6.336,3.2 6,3.5576 6,4 C6,4.4424 6.336,4.8 6.75,4.8 L8.25,4.8 C8.664,4.8 9,4.4424 9,4 C9,3.5576 8.664,3.2 8.25,3.2 Z"
                                                                        id="Shape"></path>
                                                                    <path
                                                                        d="M0.75,4.8 L2.25,4.8 C2.664,4.8 3,4.4424 3,4 C3,3.5576 2.664,3.2 2.25,3.2 L0.75,3.2 C0.336,3.2 0,3.5576 0,4 C0,4.4424 0.336,4.8 0.75,4.8 Z"
                                                                        id="Shape"></path>
                                                                    <path
                                                                        d="M0.75,1.6 L1.5,1.6 C1.914,1.6 2.25,1.2424 2.25,0.8 C2.25,0.3576 1.914,0 1.5,0 L0.75,0 C0.336,0 0,0.3576 0,0.8 C0,1.2424 0.336,1.6 0.75,1.6 Z"
                                                                        id="Shape"></path>
                                                                    <path
                                                                        d="M3.9675,3.432 C3.8325,3.5832 3.75,3.7832 3.75,4 C3.75,4.216 3.8325,4.416 3.9675,4.568 C4.11,4.712 4.305,4.8 4.5,4.8 C4.695,4.8 4.89,4.712 5.0325,4.568 C5.1675,4.416 5.25,4.208 5.25,4 C5.25,3.792 5.1675,3.5832 5.0325,3.432 C4.7475,3.136 4.245,3.136 3.9675,3.432 Z"
                                                                        id="Shape"></path>
                                                                </g>
                                                                <rect id="Rectangle-path" fill="#F4D5BD" x="16" y="0"
                                                                      width="9" height="12"></rect>
                                                                <polygon id="Shape" fill="#D8B18B"
                                                                         points="17.5 16.4444444 20.5 20 23.5 16.4444444 25 18.2222222 25 12 16 12 16 18.2222222"></polygon>
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                                <span class="hide-menu">Маҳсулотлар</span>
                                            </a>
                                        </li>

                                        @php // Product Return @endphp
                                        <li class="sidebar-item">
                                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                               href="{{ route('product-return.index') }}" aria-expanded="false">
                                                <svg version="1.1" width="25px" height="25px" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 460 460" xml:space="preserve" class="me-2"><g>
                                                        <path style="fill:#FEE187;" d="M230,0C102.974,0,0,102.975,0,230c0,125.286,100.173,227.175,224.795,229.942L456.74,191.191
                                                        C438.295,82.646,343.799,0,230,0z"/>
                                                        <path style="fill:#FFC61B;" d="M250.468,172.408L130.97,52.909l-19.56,65.699l39.538,40.86l-19.978,206.64l93.825,93.826
                                                        C226.526,459.973,228.26,460,230,460c127.025,0,230-102.975,230-230c0-13.228-1.132-26.189-3.276-38.807l-55.345-55.346
                                                        L250.468,172.408z"/>
                                                        <path style="fill:#273B7A;" d="M303.18,93.889H130.97V52.91l-93.79,93.789l316.905,35.57v87.948L252.2,313.299l41.408,52.81
                                                        l9.572,0.001c75.05,0,136.11-61.061,136.11-136.11S378.23,93.889,303.18,93.889z"/>
                                                        <path style="fill:#121149;" d="M303.18,146.7l-266-0.001l93.79,93.791l10-30.98l154.638-10h7.572c16.81,0,30.49,13.681,30.49,30.49
                                                        s-13.68,30.49-30.49,30.49H254.2v52.81h50.98c46.01,0,81.3-37.29,81.3-83.3S349.19,146.7,303.18,146.7z"/>
                                                        <polygon style="fill:#FEE187;" points="295.608,199.509 295.608,366.109 200.602,366.109 213.283,207.749 229.768,199.509 	"/><polygon style="fill:#FFFFFF;" points="213.283,207.749 213.283,366.109 130.97,366.109 130.97,199.509 196.81,199.509 	"/><rect x="196.81" y="199.509" style="fill:#FFC61B;" width="32.959" height="49.438"/></g>
                                                </svg>
                                                <span class="hide-menu">Қайтишлар</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                @can ('hasAccess')
                                    @php // Category @endphp
                                    <li class="sidebar-item">
                                        <a class="sidebar-link waves-effect waves-dark" href="{{ route('category.index') }}"
                                           aria-expanded="false">
                                            <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg" class="me-2">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                   stroke-linejoin="round"/>
                                                <g id="SVGRepo_iconCarrier">
                                                    <path
                                                        d="M17 10H19C21 10 22 9 22 7V5C22 3 21 2 19 2H17C15 2 14 3 14 5V7C14 9 15 10 17 10Z"
                                                        stroke="#ffffff" stroke-width="1.5" stroke-miterlimit="10"
                                                        stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path
                                                        d="M5 22H7C9 22 10 21 10 19V17C10 15 9 14 7 14H5C3 14 2 15 2 17V19C2 21 3 22 5 22Z"
                                                        stroke="#ffffff" stroke-width="1.5" stroke-miterlimit="10"
                                                        stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path opacity="0.34"
                                                          d="M6 10C8.20914 10 10 8.20914 10 6C10 3.79086 8.20914 2 6 2C3.79086 2 2 3.79086 2 6C2 8.20914 3.79086 10 6 10Z"
                                                          stroke="#ffffff" stroke-width="1.5" stroke-miterlimit="10"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path opacity="0.34"
                                                          d="M18 22C20.2091 22 22 20.2091 22 18C22 15.7909 20.2091 14 18 14C15.7909 14 14 15.7909 14 18C14 20.2091 15.7909 22 18 22Z"
                                                          stroke="#ffffff" stroke-width="1.5" stroke-miterlimit="10"
                                                          stroke-linecap="round" stroke-linejoin="round"/>
                                                </g>
                                            </svg>
                                            <span class="hide-menu">Категориялар</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </li>

                        @php // Manufacturing (Ишлаб-чикариш) @endphp
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <svg width="25px" height="25px" viewBox="0 0 1024 1024" class="icon me-2" version="1.1"
                                     xmlns="http://www.w3.org/2000/svg" fill="#3d3846">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                    <g id="SVGRepo_iconCarrier">
                                        <path d="M663.4 736h192v167.5h-192z" fill="#A4A9AD"/>
                                        <path
                                            d="M681.2 790.3h82.3v39.6h-82.3zM773 843h82.3v39.6H773zM663.4 736h192v45h-192z"
                                            fill=""/>
                                        <path
                                            d="M872.4 720.3c0-5.2-4.3-9.5-9.5-9.5H655.8c-5.2 0-9.5 4.3-9.5 9.5v31.5c0 5.2 4.3 9.5 9.5 9.5h207.1c5.2 0 9.5-4.3 9.5-9.5v-31.5z"
                                            fill="#D68231"/>
                                        <path
                                            d="M738.6 82.1c-21.3 0-40.5 8.8-54.2 23-11.6-7.3-25.4-11.6-40.2-11.6-41.6 0-75.4 33.7-75.4 75.4v1c-43 6.6-76 43.7-76 88.6 0 49.5 40.1 89.6 89.6 89.6S672 308 672 258.5c0-6.5-0.7-12.7-2-18.8 10.8-4 20.5-10.3 28.3-18.5 11.6 7.3 25.4 11.6 40.2 11.6 41.6 0 75.4-33.7 75.4-75.4 0-41.6-33.7-75.3-75.3-75.3z"
                                            fill="#D1D3D3"/>
                                        <path d="M673 258.4H492l-39.8 645.1h260.5z" fill="#3d3846"/>
                                        <path
                                            d="M679 357.2H485.9l-1.8 28.5h196.7l-1.8-28.5z m3.6 57.8H482.3l-1.8 28.5h203.8l-1.7-28.5z"
                                            fill="#D68231"/>
                                        <path d="M131.4 546.8h490.9v356.7H131.4z" fill="#A4A9AD"/>
                                        <path
                                            d="M540 773.2h82.3v39.6H540zM131.2 725.2h82.3v39.6h-82.3zM411 576.2h82.3v39.6H411zM233.2 546.8h82.3v39.6h-82.3zM201.8 829.9h82.3v39.6h-82.3zM573.6 664.3c0-10.4-8.5-19-19-19H199.1c-10.4 0-19 8.5-19 19v34.9c0 10.4 8.5 19 19 19h355.5c10.4 0 19-8.5 19-19v-34.9z"
                                            fill=""/>
                                        <path
                                            d="M573.6 634.8c0-10.4-8.5-19-19-19H199.1c-10.4 0-19 8.5-19 19v34.9c0 10.4 8.5 19 19 19h355.5c10.4 0 19-8.5 19-19v-34.9z"
                                            fill="#FFFFFF"/>
                                        <path
                                            d="M554.6 615.8H199.1c-10.4 0-19 8.5-19 19V659c0-10.4 8.5-19 19-19h355.5c10.4 0 19 8.5 19 19v-24.2c0-10.4-8.5-19-19-19z"
                                            fill=""/>
                                        <path
                                            d="M131.4 472.6v74.2h81.8l-74.3-74.3c-3-3-7.5-4.6-7.5 0.1zM213.2 472.6v74.2H295l-74.3-74.3c-2.9-3-7.5-4.6-7.5 0.1zM295.1 472.6v74.2h81.8l-74.3-74.3c-3-3-7.5-4.6-7.5 0.1z"
                                            fill="#D1D3D3"/>
                                        <path
                                            d="M376.9 472.6v74.2h81.8l-74.3-74.3c-3-3-7.5-4.6-7.5 0.1zM458.7 472.6v74.2h81.8l-74.3-74.3c-3-3-7.5-4.6-7.5 0.1zM540.5 472.6v74.2h81.8L548 472.5c-2.9-3-7.5-4.6-7.5 0.1zM263.1 756.3h227.7v147.2H263.1z"
                                            fill="#D1D3D3"/>
                                        <path d="M263.1 786.7h227.7v28.5H263.1zM263.1 840.8h227.7v28.5H263.1z" fill=""/>
                                        <path
                                            d="M260.1 615.8h28.5v72.9h-28.5zM362.6 615.8h28.5v72.9h-28.5zM465.2 615.8h28.5v72.9h-28.5z"
                                            fill="#A4A9AD"/>
                                        <path
                                            d="M199.1 630.1c-2.6 0-4.7 2.2-4.7 4.7v34.9c0 2.6 2.2 4.7 4.7 4.7h355.5c2.6 0 4.7-2.2 4.7-4.7v-34.9c0-2.6-2.2-4.7-4.7-4.7H199.1zM554.6 703H199.1c-18.3 0-33.2-14.9-33.2-33.2v-34.9c0-18.3 14.9-33.2 33.2-33.2h355.5c18.3 0 33.2 14.9 33.2 33.2v34.9c0.1 18.3-14.8 33.2-33.2 33.2z"
                                            fill="#D68231"/>
                                        <path
                                            d="M957.9 933.3c0 4.2-3.5 7.7-7.7 7.7h-875c-4.2 0-7.7-3.5-7.7-7.7v-27.5c0-4.2 3.5-7.7 7.7-7.7h875.1c4.2 0 7.7 3.5 7.7 7.7v27.5z"
                                            fill="#3d3846"/>
                                    </g>
                                </svg>
                                <span class="hide-menu">Ишлаб-чикариш</span>
                            </a>

                            <ul aria-expanded="false" class="collapse second-level ps-3">
                                @php // Organization @endphp
                                @can ('hasAccess')
                                    <li class="sidebar-item">
                                        <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                           href="{{ route('organization.index') }}" aria-expanded="false">
                                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 512 512"
                                                 xml:space="preserve" width="25px" height="25px" fill="#000000"
                                                 class="me-2">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                   stroke-linejoin="round"/>
                                                <g id="SVGRepo_iconCarrier">
                                                    <rect x="38.4" y="12.8" style="fill:#6FB0B6;" width="51.2"
                                                          height="486.4"/>
                                                    <rect x="89.6" y="396.8" style="fill:#FEDEA1;" width="76.8"
                                                          height="102.4"/>
                                                    <rect x="89.6" y="12.8" style="fill:#6FB0B6;" width="76.8"
                                                          height="384"/>
                                                    <g>
                                                        <rect x="268.8" y="192" style="fill:#538489;" width="102.4"
                                                              height="102.4"/>
                                                        <rect x="371.2" y="396.8" style="fill:#538489;" width="102.4"
                                                              height="102.4"/>
                                                    </g>
                                                    <rect x="166.4" y="12.8" style="fill:#6FB0B6;" width="51.2"
                                                          height="486.4"/>
                                                    <g>
                                                        <rect x="371.2" y="294.4" style="fill:#538489;" width="102.4"
                                                              height="102.4"/>
                                                        <rect x="268.8" y="294.4" style="fill:#538489;" width="102.4"
                                                              height="102.4"/>
                                                        <rect x="268.8" y="396.8" style="fill:#538489;" width="102.4"
                                                              height="102.4"/>
                                                        <rect x="371.2" y="192" style="fill:#538489;" width="102.4"
                                                              height="102.4"/>
                                                    </g>
                                                    <path style="fill:#3d3846;"
                                                          d="M499.2,486.4h-12.8V179.2H256v307.2h-25.6V0H25.6v486.4H12.8c-7.074,0-12.8,5.726-12.8,12.8 c0,7.074,5.726,12.8,12.8,12.8h486.4c7.074,0,12.8-5.726,12.8-12.8C512,492.126,506.274,486.4,499.2,486.4z M76.8,486.4H51.2V25.6 h25.6V486.4z M153.6,486.4h-51.2v-76.8h51.2V486.4z M153.6,384h-51.2V25.6h51.2V384z M204.8,486.4h-25.6V25.6h25.6V486.4z M358.4,486.4h-76.8v-76.8h76.8V486.4z M358.4,384h-76.8v-76.8h76.8V384z M358.4,281.6h-76.8v-76.8h76.8V281.6z M460.8,486.4H384 v-76.8h76.8V486.4z M460.8,384H384v-76.8h76.8V384z M460.8,281.6H384v-76.8h76.8V281.6z"/>
                                                </g>
                                        </svg>
                                            <span class="hide-menu">Филиаллар</span>
                                        </a>
                                    </li>
                                @endcan

                                @php // Section @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                       href="{{ route('section.index') }}" aria-expanded="false">
                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 512 512"
                                             xml:space="preserve" width="25px" height="25px" fill="#3d3846"
                                             class="me-2">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                               stroke-linejoin="round"/>
                                            <g id="SVGRepo_iconCarrier">
                                                <rect x="152.511" y="98.043" style="fill:#FFD311;" width="206.979"
                                                      height="65.362"/>
                                                <polygon style="fill:#595E62;"
                                                         points="103.489,206.979 103.489,408.511 103.489,457.532 408.511,457.532 408.511,408.511 408.511,206.979 "/>
                                                <polygon style="fill:#AFB6BB;"
                                                         points="462.979,457.532 408.511,457.532 408.511,408.511 408.511,206.979 103.489,206.979 103.489,408.511 103.489,457.532 49.021,457.532 49.021,98.043 152.511,98.043 152.511,163.404 359.489,163.404 359.489,98.043 462.979,98.043 "/>
                                                <polygon style="fill:#7E8488;"
                                                         points="484.766,54.468 484.766,98.043 462.979,98.043 359.489,98.043 152.511,98.043 49.021,98.043 27.234,98.043 27.234,54.468 "/>
                                                <path style="fill:#4B5054;"
                                                      d="M205.689,206.979h-102.2v201.532v49.021h305.021v-37.613 C314.06,381.278,239.819,303.664,205.689,206.979z"/>
                                                <polygon style="fill:#989EA3;"
                                                         points="152.511,163.404 152.511,98.043 49.021,98.043 49.021,457.532 103.489,457.532 103.489,408.511 103.489,206.979 256,206.979 256,163.404 "/>
                                                <g>
                                                    <rect x="130.723" y="324.085" style="fill:#FFFFFF;" width="250.553"
                                                          height="16.34"/>
                                                    <rect x="130.723" y="285.957" style="fill:#FFFFFF;" width="250.553"
                                                          height="16.34"/>
                                                    <rect x="130.723" y="247.83" style="fill:#FFFFFF;" width="250.553"
                                                          height="16.34"/>
                                                    <rect x="130.723" y="400.34" style="fill:#FFFFFF;" width="250.553"
                                                          height="16.34"/>
                                                    <rect x="130.723" y="362.213" style="fill:#FFFFFF;" width="250.553"
                                                          height="16.34"/>
                                                    <rect x="201.532" y="122.553" style="fill:#FFFFFF;" width="108.936"
                                                          height="16.34"/>
                                                </g>
                                                <path
                                                    d="M471.149,449.362V106.213h21.787V46.298H19.064v59.915h21.787v343.149H0v16.34h512v-16.34H471.149z M35.404,89.872V62.638 h441.191v27.234H35.404z M351.319,106.213v49.021H160.681v-49.021H351.319z M57.191,155.234h57.191v-16.34H57.191v-32.681h87.149 v65.362H367.66v-65.362h87.149v32.681h-57.191v16.34h57.191v294.128h-38.128V215.149h19.064v-16.34H76.255v16.34h19.064v234.213 H57.191V155.234z M400.34,215.149v234.213H111.66V215.149H400.34z"/>
                                            </g>
                                    </svg>
                                        <span class="hide-menu">Бўлимлар</span>
                                    </a>
                                </li>

                                @php // Shift @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                       href="{{ route('shift.index') }}" aria-expanded="false">
                                        <svg width="25px" height="25px" viewBox="0 0 24 24" version="1.1"
                                             xmlns="http://www.w3.org/2000/svg"
                                             fill="#ffffff" stroke="#ffffff" class="me-2">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                               stroke-linejoin="round"/>
                                            <g id="SVGRepo_iconCarrier">
                                                <!-- Uploaded to: SVG Repo, www.svgrepo.com, Transformed by: SVG Repo Mixer Tools -->
                                                <title>ic_fluent_shifts_team_24_filled</title>
                                                <desc>Created with Sketch.</desc>
                                                <g id="🔍-Product-Icons" stroke="none" stroke-width="1" fill="none"
                                                   fill-rule="evenodd">
                                                    <g id="ic_fluent_shifts_team_24_filled" fill="#ffffff"
                                                       fill-rule="nonzero">
                                                        <path
                                                            d="M6.5,12 C9.53756612,12 12,14.4624339 12,17.5 C12,20.5375661 9.53756612,23 6.5,23 C3.46243388,23 1,20.5375661 1,17.5 C1,14.4624339 3.46243388,12 6.5,12 Z M17.75,3 C19.5449254,3 21,4.45507456 21,6.25 L21,17.75 C21,19.5449254 19.5449254,21 17.75,21 L11.9774077,21.0012092 C12.6247042,19.9906579 13,18.7891565 13,17.5 C13,15.9846422 12.4814474,14.5903989 11.6108398,13.4871142 L11.6794988,13.4967299 L11.75,13.5 L16.2482627,13.5 L16.3500333,13.4931534 C16.7161089,13.443491 16.9982627,13.1296958 16.9982627,12.75 C16.9982627,12.3703042 16.7161089,12.056509 16.3500333,12.0068466 L16.2482627,12 L12.5,12 L12.5,6.75 L12.4931534,6.64822944 C12.443491,6.28215388 12.1296958,6 11.75,6 C11.3703042,6 11.056509,6.28215388 11.0068466,6.64822944 L11,6.75 L11,12.75 L11.0048315,12.8142135 C9.83648038,11.690706 8.24890171,11 6.5,11 C5.21084353,11 4.00934208,11.3752958 2.99879075,12.0225923 L3,6.25 C3,4.45507456 4.45507456,3 6.25,3 L17.75,3 Z M6.5,17.5 L3.5,17.5 C3.25454011,17.5 3.05039163,17.6768752 3.00805567,17.9101244 L3,18 L3,18.4959046 C3,19.4903671 3.75658207,19.9935852 5,19.9935852 C6.18120738,19.9935852 6.92312074,19.5398532 6.99435906,18.6422942 L7,18.4968875 L7,18 C7,17.7238576 6.77614237,17.5 6.5,17.5 Z M9.52012224,17.4861631 L7.9091402,17.4846863 C7.95322679,17.605209 7.98229213,17.7329685 7.99406939,17.8656979 L8,18 L8,18.4968875 L7.99278617,18.7088676 C7.97362373,18.9853255 7.91658306,19.2370411 7.82523727,19.4638622 C7.95879832,19.4777061 8.10072734,19.4845107 8.25029164,19.4845107 C9.27626263,19.4845107 9.94293361,19.1646461 10.0138416,18.4487547 L10.0201222,18.3182944 L10.0201222,17.9861631 C10.0201222,17.7407032 9.84324708,17.5365547 9.60999787,17.4942187 L9.52012224,17.4861631 Z M5,14.5 C4.4467725,14.5 3.99829299,14.9484795 3.99829299,15.501707 C3.99829299,16.0549345 4.4467725,16.503414 5,16.503414 C5.5532275,16.503414 6.00170701,16.0549345 6.00170701,15.501707 C6.00170701,14.9484795 5.5532275,14.5 5,14.5 Z M8.13031287,14.7573556 C7.64811242,14.7573556 7.25721172,15.1482563 7.25721172,15.6304568 C7.25721172,16.1126572 7.64811242,16.5035579 8.13031287,16.5035579 C8.61251332,16.5035579 9.00341401,16.1126572 9.00341401,15.6304568 C9.00341401,15.1482563 8.61251332,14.7573556 8.13031287,14.7573556 Z"
                                                            id="🎨-Color"></path>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                        <span class="hide-menu">Сменалар</span>
                                    </a>
                                </li>

                                @php // Shift Output @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                       href="{{ route('shift-output.index') }}" aria-expanded="false">
                                        <svg width="22px" height="22px" viewBox="0 0 48 48"
                                             xmlns="http://www.w3.org/2000/svg" fill="#ffffff" class="me-2">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                               stroke-linejoin="round"/>
                                            <g id="SVGRepo_iconCarrier"><title>output</title>
                                                <g id="Layer_2" data-name="Layer 2">
                                                    <g id="invisible_box" data-name="invisible box">
                                                        <rect width="48" height="48" fill="none"/>
                                                    </g>
                                                    <g id="Layer_6" data-name="Layer 6">
                                                        <g>
                                                            <path
                                                                d="M45.4,22.6l-7.9-8a2.1,2.1,0,0,0-2.7-.2,1.9,1.9,0,0,0-.2,3L39.2,22H16a2,2,0,0,0,0,4H39.2l-4.6,4.6a1.9,1.9,0,0,0,.2,3,2.1,2.1,0,0,0,2.7-.2l7.9-8A1.9,1.9,0,0,0,45.4,22.6Z"/>
                                                            <path
                                                                d="M28,42H24A18,18,0,0,1,24,6h4a2,2,0,0,0,1.4-.6A2,2,0,0,0,30,4a2.4,2.4,0,0,0-.2-.9A2,2,0,0,0,28,2H23.8a22,22,0,0,0,.1,44H28a2,2,0,0,0,1.4-.6l.4-.5A2.4,2.4,0,0,0,30,44,2,2,0,0,0,28,42Z"/>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                        <span class="hide-menu">Смена махсулотлари</span>
                                    </a>
                                </li>

                                @php // Shift Output Worker @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                       href="{{ route('shift-output-worker.index') }}" aria-expanded="false">
                                        <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                             xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 392.598 392.598" xml:space="preserve" fill="#000000"
                                             class="me-2">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                               stroke-linejoin="round"/>
                                            <g id="SVGRepo_iconCarrier">
                                                <g>
                                                    <path style="fill:#FFFFFF;"
                                                          d="M243.717,104.21c-37.495,0-72.339,16.226-96.453,44.735c0.259,1.616,0.323,115.976,0.323,115.976 c0,6.012-4.848,10.925-10.925,10.925h-6.206v11.83c21.657,43.119,64.97,69.947,113.261,69.947 c69.883,0,126.642-56.824,126.642-126.642C370.36,161.099,313.535,104.21,243.717,104.21z"/>
                                                    <path style="fill:#FFFFFF;"
                                                          d="M38.917,370.812h24.178v-58.117c0-6.012,4.848-10.925,10.925-10.925s10.925,4.848,10.925,10.925 v58.117h24.178v-94.966H38.917V370.812z"/>
                                                </g>
                                                <g>
                                                    <path style="fill:#56ACE0;"
                                                          d="M348.897,230.917c0,57.794-46.998,104.792-104.792,104.792c-28.444,0-54.885-11.313-74.214-30.707 v-148.17c17.584-17.778,40.727-28.509,65.745-30.513c-1.552,1.875-2.457,4.267-2.457,6.917v97.616 c0,6.012,4.848,10.925,10.861,10.925h55.273c6.012,0,10.925-4.848,10.925-10.925c0-6.077-4.848-10.861-10.925-10.861h-44.347 v-86.691c0-2.651-0.905-5.042-2.457-6.917C306.36,130.65,348.897,175.903,348.897,230.917z"/>
                                                    <path style="fill:#56ACE0;"
                                                          d="M73.956,21.915c-14.739,0-26.764,11.96-26.764,26.764c0,14.739,11.96,26.764,26.764,26.764 c14.739,0,26.764-11.96,26.764-26.764C100.655,33.875,88.695,21.915,73.956,21.915z"/>
                                                </g>
                                                <path style="fill:#FFC10D;"
                                                      d="M126.19,253.996V153.923c0-10.537-8.145-19.071-18.36-19.911l-23.855,52.17 c-3.879,8.469-15.903,8.469-19.846,0l-23.855-52.17c-10.279,0.84-18.36,9.438-18.36,19.911v100.073L126.19,253.996L126.19,253.996z"/>
                                                <g>
                                                    <path style="fill:#3d3846;"
                                                          d="M73.956,97.164c26.828,0,48.614-21.786,48.614-48.549C122.57,21.786,100.784,0,73.956,0 S25.406,21.786,25.406,48.614C25.406,75.378,47.192,97.164,73.956,97.164z M73.956,21.915c14.739,0,26.828,11.96,26.828,26.764 c0,14.739-11.96,26.764-26.764,26.764S47.192,63.418,47.192,48.679S59.216,21.915,73.956,21.915z"/>
                                                    <path style="fill:#3d3846;"
                                                          d="M244.105,82.424c-40.404,0-78.093,16.162-105.956,44.671c-7.628-9.115-19.135-14.933-32-14.933 h-5.301c-4.267,0-8.145,2.457-9.891,6.335L74.02,155.41l-16.937-36.913c-1.745-3.879-5.624-6.335-9.891-6.335h-5.301 C18.683,112.097,0,130.844,0,153.859v110.998c0,6.012,4.848,10.925,10.925,10.925h6.206v105.891 c0,6.012,4.848,10.925,10.925,10.925h91.927c6.012,0,10.925-4.848,10.925-10.925v-54.949 c27.733,32.97,68.719,52.558,113.261,52.558c81.842,0,148.428-66.586,148.428-148.428 C392.533,149.01,325.948,82.424,244.105,82.424z M108.994,370.812H84.816v-58.117c0-6.012-4.848-10.925-10.925-10.925 c-6.077,0-10.925,4.848-10.925,10.925v58.117H38.917v-94.966h70.077V370.812z M126.19,253.996H21.851V153.923h-0.065 c0-10.537,8.145-19.071,18.36-19.911L64,186.182c3.879,8.469,15.903,8.469,19.846,0l23.855-52.17 c10.279,0.84,18.36,9.438,18.36,19.911v100.073H126.19z M244.105,357.495c-48.291,0-91.539-26.828-113.261-69.947v-11.83h6.206 c6.012,0,10.925-4.848,10.925-10.925c0,0-0.129-114.36-0.323-115.976c24.113-28.444,58.958-44.735,96.453-44.735 c69.883,0,126.642,56.824,126.642,126.642C370.748,300.735,313.923,357.495,244.105,357.495z"/>
                                                    <path style="fill:#3d3846;"
                                                          d="M299.378,219.992H255.03v-86.691c0-6.012-4.848-10.925-10.925-10.925 c-6.012,0-10.925,4.848-10.925,10.925v97.616c0,6.012,4.848,10.925,10.925,10.925h55.273c6.012,0,10.925-4.848,10.925-10.925 C310.303,224.84,305.39,219.992,299.378,219.992z"/>
                                                </g>
                                            </g>
                                    </svg>
                                        <span class="hide-menu">Смена ходимлари</span>
                                    </a>
                                </li>

                                @php // Shift Report @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                       href="{{ route('shift-report.index') }}"
                                       aria-expanded="false">
                                        <svg width="25px" height="25px" viewBox="0 0 50 50" data-name="Layer 1"
                                             id="Layer_1" xmlns="http://www.w3.org/2000/svg" fill="#000000"
                                             class="me-2">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                               stroke-linejoin="round"/>
                                            <g id="SVGRepo_iconCarrier">
                                                <defs>
                                                    <style>.cls-1 {
                                                            fill: #231f20;
                                                        }

                                                        .cls-2 {
                                                            fill: #ff8e5a;
                                                        }

                                                        .cls-3 {
                                                            fill: #ffba50;
                                                        }

                                                        .cls-4 {
                                                            fill: #deddda;
                                                        }</style>
                                                </defs>
                                                <title/>
                                                <path class="cls-1"
                                                      d="M40.911,9.207H29.234V5a.5.5,0,0,0-.5-.5H5a.5.5,0,0,0-.5.5c0,.006,0,.011,0,.017L4.5,36.2a4.59,4.59,0,0,0,4.524,4.582.149.149,0,0,0,.018,0H9.06c.01,0,.019,0,.029,0s.019,0,.029,0h11.65V45a.5.5,0,0,0,.5.5H45a.5.5,0,0,0,.5-.5l0-31.2A4.6,4.6,0,0,0,40.911,9.207Z"/>
                                                <path class="cls-2"
                                                      d="M5.5,36.2l0-30.7H28.234V9.207H17.583a.462.462,0,0,0-.1.01c-.1-.007-.2-.01-.3-.01a4.6,4.6,0,0,0-4.59,4.591s.086,22.266.086,22.406a3.591,3.591,0,0,1-3.56,3.586H9.06A3.592,3.592,0,0,1,5.5,36.2Z"/>
                                                <path class="cls-3"
                                                      d="M11.918,39.79a4.567,4.567,0,0,0,1.76-3.586c0-.1-.086-22.41-.086-22.408a3.59,3.59,0,0,1,7.179,0l0,25.994Z"/>
                                                <path class="cls-4"
                                                      d="M44.5,44.5H21.768l0-30.7a4.585,4.585,0,0,0-1.733-3.589H40.911A3.593,3.593,0,0,1,44.5,13.8Z"/>
                                                <path class="cls-1"
                                                      d="M41.13,37.239H25.136a.5.5,0,0,0,0,1H41.13a.5.5,0,0,0,0-1Z"/>
                                                <path class="cls-1"
                                                      d="M41.13,31.692H25.136a.5.5,0,0,0,0,1H41.13a.5.5,0,0,0,0-1Z"/>
                                                <path class="cls-1"
                                                      d="M41.13,26.156H25.136a.5.5,0,0,0,0,1H41.13a.5.5,0,0,0,0-1Z"/>
                                                <path class="cls-1"
                                                      d="M41.13,20.653H25.136a.5.5,0,0,0,0,1H41.13a.5.5,0,0,0,0-1Z"/>
                                                <path class="cls-1"
                                                      d="M41.13,15.106H25.136a.5.5,0,0,0,0,1H41.13a.5.5,0,0,0,0-1Z"/>
                                            </g>
                                        </svg>
                                        <span class="hide-menu">Смена ҳисоботи</span>
                                    </a>
                                </li>

                                @php // Defect Report @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                       href="{{ route('defect-report.index') }}"
                                       aria-expanded="false">
                                    <svg version="1.0" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                             width="20px" height="20px"
                                             viewBox="0 0 64 64" enable-background="new 0 0 64 64" xml:space="preserve"
                                             fill="#000000" class="me-2">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                               stroke-linejoin="round"/>
                                            <g id="SVGRepo_iconCarrier">
                                                <g>
                                                    <g>
                                                        <path fill="#F9EBB2"
                                                              d="M2,35v23c0,2.209,1.791,4,4,4s4-1.791,4-4V33H4C2.896,33,2,33.896,2,35z"/>
                                                        <path fill="#F9EBB2"
                                                              d="M60,2H14c-1.104,0-2,0.896-2,2v54c0,1.539-0.584,2.938-1.537,4H58c2.209,0,4-1.791,4-4V4 C62,2.896,61.104,2,60,2z"/>
                                                    </g>
                                                    <g>
                                                        <path fill="#394240"
                                                              d="M60,0H14c-2.211,0-4,1.789-4,4v27H4c-2.211,0-4,1.789-4,4v23c0,3.313,2.687,6,6,6h52c3.313,0,6-2.687,6-6 V4C64,1.789,62.211,0,60,0z M10,58c0,2.209-1.791,4-4,4s-4-1.791-4-4V35c0-1.104,0.896-2,2-2h6V58z M62,58c0,2.209-1.791,4-4,4 H10.463C11.416,60.938,12,59.539,12,58V4c0-1.104,0.896-2,2-2h46c1.104,0,2,0.896,2,2V58z"/>
                                                        <path fill="#394240"
                                                              d="M53,25H21c-0.553,0-1,0.447-1,1s0.447,1,1,1h32c0.553,0,1-0.447,1-1S53.553,25,53,25z"/>
                                                        <path fill="#394240"
                                                              d="M53,19H21c-0.553,0-1,0.447-1,1s0.447,1,1,1h32c0.553,0,1-0.447,1-1S53.553,19,53,19z"/>
                                                        <path fill="#394240"
                                                              d="M53,37H21c-0.553,0-1,0.447-1,1s0.447,1,1,1h32c0.553,0,1-0.447,1-1S53.553,37,53,37z"/>
                                                        <path fill="#394240"
                                                              d="M53,43H21c-0.553,0-1,0.447-1,1s0.447,1,1,1h32c0.553,0,1-0.447,1-1S53.553,43,53,43z"/>
                                                        <path fill="#394240"
                                                              d="M53,49H21c-0.553,0-1,0.447-1,1s0.447,1,1,1h32c0.553,0,1-0.447,1-1S53.553,49,53,49z"/>
                                                        <path fill="#394240"
                                                              d="M53,31H21c-0.553,0-1,0.447-1,1s0.447,1,1,1h32c0.553,0,1-0.447,1-1S53.553,31,53,31z"/>
                                                        <path fill="#394240"
                                                              d="M21,15h15c0.553,0,1-0.447,1-1s-0.447-1-1-1H21c-0.553,0-1,0.447-1,1S20.447,15,21,15z"/>
                                                    </g>
                                                    <path opacity="0.15" fill="#231F20"
                                                          d="M2,35v23c0,2.209,1.791,4,4,4s4-1.791,4-4V33H4C2.896,33,2,33.896,2,35z"/>
                                                </g>
                                            </g>
                                    </svg>
                                    <span class="hide-menu">Брак ҳисоботи</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    @php // Cash @endphp
                    @can ('hasAccess')
                        @php // Diagrams @endphp
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark" href="{{ route('charts.diagram') }}"
                               aria-expanded="false">
                                <svg fill="#000000" width="25px" height="25px" viewBox="0 0 24 24" id="diagram-bar-3"
                                     data-name="Line Color" xmlns="http://www.w3.org/2000/svg"
                                     class="icon line-color me-2">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                    <g id="SVGRepo_iconCarrier">
                                        <path id="secondary" d="M13,11v4M9,7v8m8-6v6"
                                              style="fill: none; stroke: #2ca9bc; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"/>
                                        <path id="primary" d="M3,19H21M5,3V21"
                                              style="fill: none; stroke: #ffffff; stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"/>
                                    </g>
                                </svg>
                                <span class="hide-menu">Диаграммалар</span>
                            </a>
                        </li>

                        @php // Order @endphp
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                               href="{{ route('order.index') }}" aria-expanded="false">
                                <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                     xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 507.99 507.99" xml:space="preserve" fill="#000000" class="me-2">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                    <g id="SVGRepo_iconCarrier">
                                        <path style="fill:#FFD15C;"
                                              d="M473.59,332.937l-34.6-169c-2.3-11.5-13.5-18.8-25-16.5l-191.9,39.1c-11.5,2.3-18.9,13.5-16.5,25 l34.5,169.1c2.3,11.5,13.5,18.8,25,16.5l191.9-39.2C468.49,355.537,475.89,344.437,473.59,332.937z"/>
                                        <polygon style="fill:#FF7058;"
                                                 points="344.49,161.537 355.29,214.337 348.29,224.937 337.69,217.937 330.69,228.537 320.09,221.537 312.99,232.037 302.39,225.037 291.69,172.337 "/>
                                        <g>
                                            <polygon style="fill:#FFFFFF;"
                                                     points="373.09,331.137 387.39,309.437 409.19,323.737 398.69,325.937 403.79,351.237 388.69,354.237 383.59,328.937 "/>
                                            <polygon style="fill:#FFFFFF;"
                                                     points="416.19,322.337 430.59,300.637 452.29,314.937 441.79,317.137 446.99,342.437 431.89,345.437 426.69,320.237 "/>
                                        </g>
                                        <path style="fill:#ffffff;"
                                              d="M507.59,365.737c-2.1-9.9-11.8-16.3-21.7-14.2l-203.1,42c-4.4-7.7-10.7-14.6-18.6-19.8 c-7.9-5.2-16.7-8.2-25.6-9.2l-67.1-323.2c-1-4.8-3.9-9-8-11.7s-9.1-3.6-13.9-2.6l-73.5,15.9c-10,2.2-16.3,11.9-14.1,21.8 c2.1,9.9,12,16.2,21.8,14.1l55.5-11.9l63.3,305c-7.7,4.4-14.6,10.6-19.8,18.6c-17.8,27-10.2,63.5,16.9,81.2 c9.6,6.3,20.3,9.4,30.9,9.6c19.6,0.4,38.9-8.9,50.5-26.4c5.2-7.9,8.2-16.7,9.2-25.5l203.1-42 C503.29,385.337,509.69,375.637,507.59,365.737z M250.29,434.737c-3.2,4.9-8.2,8.3-13.9,9.5c-5.8,1.2-11.6,0.1-16.6-3.2 c-4.9-3.2-8.3-8.2-9.5-13.9c-1.2-5.7-0.1-11.6,3.2-16.5c3.2-4.9,8.2-8.3,13.9-9.5c1.7-0.3,3.3-0.5,5-0.5c4.1,0.1,8.1,1.3,11.6,3.6 c4.9,3.2,8.3,8.2,9.5,13.9C254.59,424.037,253.49,429.837,250.29,434.737z"/>
                                        <path style="fill:#4CDBC4;"
                                              d="M80.39,36.737l-62.1,12.9c-12.4,2.6-20.4,14.7-17.8,27.1s14.7,20.3,27.1,17.8l62.1-12.9L80.39,36.737 z"/>
                                    </g>
                                </svg>
                                <span class="hide-menu">Буюртмалар</span>
                            </a>
                        </li>

                        @php // Order Item @endphp
                        <li class="sidebar-item">
                            <a
                                class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="{{ route('order-item.index') }}"
                                aria-expanded="false">
                                <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                     xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 502 502" xml:space="preserve" class="me-2">
                                    <g>
                                        <g>
                                            <polygon style="fill:#D1DCEB;"
                                                     points="389.81,126.779 356.721,126.779 356.721,49 263.721,49 263.721,89 134.721,89 134.721,49 41.722,49 41.722,435 198.107,435 198.107,492 460.278,492 460.278,197.247 		"/>
                                            <path style="fill:#4EC9DC;"
                                                  d="M231.721,49V36.59c0-14.685-11.905-26.59-26.59-26.59h-11.819c-14.686,0-26.59,11.905-26.59,26.59 V49h-32v40h129V49H231.721z"/>
                                        </g>
                                        <polygon style="fill:#4EC9DC;"
                                                 points="389.81,197.247 389.81,126.779 460.278,197.247 	"/>
                                        <g>
                                            <path
                                                d="M41.722,39c-5.522,0-10,4.477-10,10v386c0,5.523,4.478,10,10,10h146.386v47c0,5.523,4.478,10,10,10h262.171 c5.522,0,10-4.477,10-10V197.247c0-2.652-1.054-5.196-2.929-7.071l-70.468-70.468c-1.876-1.875-4.419-2.929-7.071-2.929h-23.089 V49c0-5.523-4.478-10-10-10h-115v-2.41c0-20.176-16.414-36.59-36.59-36.59h-11.819c-20.176,0-36.591,16.415-36.591,36.59V39 H41.722z M399.811,150.921l36.326,36.326h-36.326V150.921z M191.721,59c5.522,0,10-4.477,10-10s-4.478-10-10-10h-15v-2.41 c0-9.148,7.442-16.59,16.591-16.59h11.819c9.147,0,16.59,7.442,16.59,16.59V49c0,5.523,4.478,10,10,10h22v20h-109V59H191.721z M51.722,425V59h73v30c0,5.523,4.478,10,10,10h129c5.522,0,10-4.477,10-10V59h73v57.779H198.107c-5.522,0-10,4.477-10,10V425 H51.722z M208.107,136.779H379.81v60.468c0,5.523,4.478,10,10,10h60.468V482H208.107V136.779z"/>
                                            <path
                                                d="M243.949,253.468h125.402c5.522,0,10-4.477,10-10s-4.478-10-10-10H243.949c-5.522,0-10,4.477-10,10 S238.427,253.468,243.949,253.468z"/>
                                            <path
                                                d="M414.437,283.478H243.949c-5.522,0-10,4.477-10,10s4.478,10,10,10h170.487c5.522,0,10-4.477,10-10 S419.959,283.478,414.437,283.478z"/>
                                            <path
                                                d="M414.437,333.487H243.949c-5.522,0-10,4.477-10,10s4.478,10,10,10h170.487c5.522,0,10-4.477,10-10 S419.959,333.487,414.437,333.487z"/>
                                            <path
                                                d="M414.437,383.497H243.949c-5.522,0-10,4.477-10,10s4.478,10,10,10h170.487c5.522,0,10-4.477,10-10	S419.959,383.497,414.437,383.497z"/>
                                            <path
                                                d="M414.437,233.468h-16.67c-5.522,0-10,4.477-10,10s4.478,10,10,10h16.67c5.522,0,10-4.477,10-10 S419.959,233.468,414.437,233.468z"/>
                                        </g>
                                    </g>
                                </svg>
                                <span class="hide-menu">Буюртма элементлари</span>
                            </a>
                        </li>

                        @php // Pre Order @endphp
                        <li class="sidebar-item">
                            <a
                                class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="{{ route('pre-order.index') }}"
                                aria-expanded="false">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 512 512"
                                     xml:space="preserve" width="25px" height="25px" fill="#000000" class="me-2">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                    <g id="SVGRepo_iconCarrier">
                                        <polygon style="fill:#FFE6B8;"
                                                 points="431.28,10.199 80.72,10.199 80.72,501.801 347.283,501.801 431.28,417.803 "/>
                                        <polygon style="fill:#FFAD61;"
                                                 points="431.28,417.803 347.283,501.801 347.209,417.731 "/>
                                        <g>
                                            <rect x="122.39" y="49.976" style="fill:#BCC987;" width="79.554"
                                                  height="79.554"/>
                                            <rect x="122.39" y="177.466" style="fill:#BCC987;" width="79.554"
                                                  height="79.554"/>
                                            <rect x="122.39" y="303.936" style="fill:#BCC987;" width="79.554"
                                                  height="79.554"/>
                                        </g>
                                        <g>
                                            <path style="fill:#4D3D36;"
                                                  d="M431.279,0H80.721c-5.633,0-10.199,4.566-10.199,10.199v491.602c0,5.633,4.566,10.199,10.199,10.199 h266.562c2.705,0,5.299-1.075,7.212-2.987l83.997-83.998c1.912-1.912,2.987-4.506,2.987-7.212V10.199 C441.479,4.566,436.912,0,431.279,0z M90.92,20.398h330.161v387.201l-73.862-0.067c-0.003,0-0.006,0-0.009,0 c-2.705,0-5.299,1.075-7.212,2.987c-1.915,1.915-2.99,4.513-2.987,7.222l0.066,73.861H90.92V20.398z M357.419,427.939l49.257,0.045 l-49.213,49.213L357.419,427.939z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M240.217,63.287h69.322c5.633,0,10.199-4.566,10.199-10.199s-4.566-10.199-10.199-10.199h-69.322 c-5.633,0-10.199,4.566-10.199,10.199S234.584,63.287,240.217,63.287z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M240.217,99.952H390.63c5.633,0,10.199-4.566,10.199-10.199s-4.566-10.199-10.199-10.199H240.217 c-5.633,0-10.199,4.566-10.199,10.199S234.584,99.952,240.217,99.952z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M240.217,136.669H390.63c5.633,0,10.199-4.566,10.199-10.199c0-5.633-4.566-10.199-10.199-10.199 H240.217c-5.633,0-10.199,4.566-10.199,10.199S234.584,136.669,240.217,136.669z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M201.944,39.777H122.39c-5.633,0-10.199,4.566-10.199,10.199v79.554 c0,5.633,4.566,10.199,10.199,10.199h79.554c5.633,0,10.199-4.566,10.199-10.199V49.976 C212.143,44.343,207.577,39.777,201.944,39.777z M191.745,119.331H132.59V60.175h59.155V119.331z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M144.374,108.098c1.992,1.992,4.602,2.987,7.212,2.987c2.61,0,5.221-0.996,7.212-2.987l4.241-4.241 l4.241,4.241c1.992,1.992,4.602,2.987,7.212,2.987c2.61,0,5.221-0.996,7.212-2.987c3.983-3.983,3.983-10.441,0-14.424l-4.241-4.241 l4.241-4.241c3.983-3.983,3.984-10.441,0-14.424c-3.983-3.984-10.441-3.983-14.424,0l-4.241,4.241l-4.241-4.241 c-3.983-3.983-10.441-3.983-14.424,0c-3.983,3.983-3.983,10.441,0,14.424l4.241,4.241l-4.241,4.241 C140.391,97.657,140.39,104.116,144.374,108.098z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M240.217,190.777h69.322c5.633,0,10.199-4.566,10.199-10.199s-4.566-10.199-10.199-10.199h-69.322 c-5.633,0-10.199,4.566-10.199,10.199S234.584,190.777,240.217,190.777z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M240.217,227.442H390.63c5.633,0,10.199-4.566,10.199-10.199c0-5.633-4.566-10.199-10.199-10.199 H240.217c-5.633,0-10.199,4.566-10.199,10.199C230.018,222.876,234.584,227.442,240.217,227.442z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M240.217,264.159H390.63c5.633,0,10.199-4.566,10.199-10.199c0-5.633-4.566-10.199-10.199-10.199 H240.217c-5.633,0-10.199,4.566-10.199,10.199C230.018,259.593,234.584,264.159,240.217,264.159z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M201.944,167.267H122.39c-5.633,0-10.199,4.566-10.199,10.199v79.554 c0,5.633,4.566,10.199,10.199,10.199h79.554c5.633,0,10.199-4.566,10.199-10.199v-79.554 C212.143,171.833,207.577,167.267,201.944,167.267z M191.745,246.821H132.59v-59.155h59.155V246.821z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M344.72,296.849c-5.633,0-10.199,4.566-10.199,10.199s4.566,10.199,10.199,10.199h3.577 c5.633,0,10.199-4.566,10.199-10.199s-4.566-10.199-10.199-10.199H344.72z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M240.217,317.247h69.322c5.633,0,10.199-4.566,10.199-10.199s-4.566-10.199-10.199-10.199h-69.322 c-5.633,0-10.199,4.566-10.199,10.199S234.584,317.247,240.217,317.247z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M240.217,353.912H390.63c5.633,0,10.199-4.566,10.199-10.199s-4.566-10.199-10.199-10.199H240.217 c-5.633,0-10.199,4.566-10.199,10.199S234.584,353.912,240.217,353.912z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M400.829,380.43c0-5.633-4.566-10.199-10.199-10.199H240.217c-5.633,0-10.199,4.566-10.199,10.199 s4.566,10.199,10.199,10.199H390.63C396.263,390.629,400.829,386.063,400.829,380.43z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M201.944,293.737H122.39c-5.633,0-10.199,4.566-10.199,10.199v79.554 c0,5.633,4.566,10.199,10.199,10.199h79.554c5.633,0,10.199-4.566,10.199-10.199v-79.554 C212.143,298.303,207.577,293.737,201.944,293.737z M191.745,373.291H132.59v-59.155h59.155V373.291z"/>
                                        </g>
                                    </g>
                                </svg>
                                <span class="hide-menu">Навбатдаги буюртмалар</span>
                            </a>
                        </li>

                        @php // Pre Order Item @endphp
                        <li class="sidebar-item">
                            <a
                                class="sidebar-link waves-effect waves-dark sidebar-link"
                                href="{{ route('pre-order-item.index') }}"
                                aria-expanded="false">
                                <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                     xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 512 512" xml:space="preserve" fill="#000000" class="me-2">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                    <g id="SVGRepo_iconCarrier">
                                        <path style="fill:#FFAD61;"
                                              d="M431.733,60.61c0-17.057-13.827-30.885-30.885-30.885H111.152c-17.057,0-30.885,13.827-30.885,30.885 v11.408h351.466V60.61z"/>
                                        <path style="fill:#FFE6B8;"
                                              d="M80.268,72.018v398.899c0,17.056,13.827,30.884,30.884,30.884h289.696 c17.057,0,30.885-13.828,30.885-30.885V72.018H80.268z"/>
                                        <g>
                                            <path style="fill:#4D3D36;"
                                                  d="M70.068,470.916c0,22.654,18.43,41.084,41.084,41.084h289.695c22.654,0,41.084-18.43,41.084-41.084 V60.61c0-22.57-18.295-40.942-40.835-41.077v-9.333C401.097,4.567,396.53,0,390.898,0c-5.632,0-10.199,4.567-10.199,10.199v9.327 h-24.811v-9.327C355.888,4.567,351.321,0,345.689,0c-5.632,0-10.199,4.567-10.199,10.199v9.327h-24.081v-9.327 C311.409,4.567,306.842,0,301.21,0c-5.632,0-10.199,4.567-10.199,10.199v9.327H266.2v-9.327C266.2,4.567,261.633,0,256.001,0 c-5.632,0-10.199,4.567-10.199,10.199v9.327h-24.811v-9.327C220.991,4.567,216.424,0,210.792,0s-10.199,4.567-10.199,10.199v9.327 h-24.811v-9.327C175.782,4.567,171.215,0,165.583,0s-10.199,4.567-10.199,10.199v9.327h-24.081v-9.327 C131.302,4.567,126.735,0,121.103,0s-10.199,4.567-10.199,10.199v9.333C88.364,19.668,70.069,38.04,70.069,60.61v410.306H70.068z M90.466,60.61c0-11.322,9.146-20.537,20.436-20.673v3.481c0,5.632,4.567,10.199,10.199,10.199c5.632,0,10.199-4.567,10.199-10.199 v-3.493h24.081v3.493c0,5.632,4.567,10.199,10.199,10.199s10.199-4.567,10.199-10.199v-3.493h24.811v3.493 c0,5.632,4.567,10.199,10.199,10.199c5.632,0,10.199-4.567,10.199-10.199v-3.493h24.812v3.493c0,5.632,4.567,10.199,10.199,10.199 c5.632,0,10.199-4.567,10.199-10.199v-3.493h24.811v3.493c0,5.632,4.567,10.199,10.199,10.199c5.632,0,10.199-4.567,10.199-10.199 v-3.493h24.081v3.493c0,5.632,4.567,10.199,10.199,10.199c5.632,0,10.199-4.567,10.199-10.199v-3.493h24.811v3.493 c0,5.632,4.567,10.199,10.199,10.199c5.632,0,10.199-4.567,10.199-10.199v-3.481c11.291,0.136,20.436,9.351,20.436,20.673v1.209 H90.466V60.61z M421.535,470.916c0,11.407-9.28,20.686-20.686,20.686H111.152c-11.407,0-20.686-9.28-20.686-20.686v-388.7h331.068 V470.916z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M179.437,146.685h208.91c5.632,0,10.199-4.567,10.199-10.199s-4.567-10.199-10.199-10.199h-208.91 c-5.632,0-10.199,4.567-10.199,10.199S173.804,146.685,179.437,146.685z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M123.654,146.685h14.219c5.632,0,10.199-4.567,10.199-10.199s-4.567-10.199-10.199-10.199h-14.219 c-5.632,0-10.199,4.567-10.199,10.199S118.022,146.685,123.654,146.685z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M179.437,196.27h208.91c5.632,0,10.199-4.567,10.199-10.199c0-5.632-4.567-10.199-10.199-10.199 h-208.91c-5.632,0-10.199,4.567-10.199,10.199C169.238,191.703,173.804,196.27,179.437,196.27z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M123.654,196.27h14.219c5.632,0,10.199-4.567,10.199-10.199c0-5.632-4.567-10.199-10.199-10.199 h-14.219c-5.632,0-10.199,4.567-10.199,10.199C113.454,191.703,118.022,196.27,123.654,196.27z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M179.437,246.584h208.91c5.632,0,10.199-4.567,10.199-10.199s-4.567-10.199-10.199-10.199h-208.91 c-5.632,0-10.199,4.567-10.199,10.199S173.804,246.584,179.437,246.584z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M123.654,246.584h14.219c5.632,0,10.199-4.567,10.199-10.199s-4.567-10.199-10.199-10.199h-14.219 c-5.632,0-10.199,4.567-10.199,10.199S118.022,246.584,123.654,246.584z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M179.437,295.438h208.91c5.632,0,10.199-4.567,10.199-10.199c0-5.632-4.567-10.199-10.199-10.199 h-208.91c-5.632,0-10.199,4.567-10.199,10.199C169.238,290.871,173.804,295.438,179.437,295.438z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M123.654,295.438h14.219c5.632,0,10.199-4.567,10.199-10.199c0-5.632-4.567-10.199-10.199-10.199 h-14.219c-5.632,0-10.199,4.567-10.199,10.199C113.454,290.871,118.022,295.438,123.654,295.438z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M179.437,345.752h208.91c5.632,0,10.199-4.567,10.199-10.199c0-5.632-4.567-10.199-10.199-10.199 h-208.91c-5.632,0-10.199,4.567-10.199,10.199C169.238,341.185,173.804,345.752,179.437,345.752z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M123.654,345.752h14.219c5.632,0,10.199-4.567,10.199-10.199c0-5.632-4.567-10.199-10.199-10.199 h-14.219c-5.632,0-10.199,4.567-10.199,10.199C113.454,341.185,118.022,345.752,123.654,345.752z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M179.437,394.606h208.91c5.632,0,10.199-4.567,10.199-10.199c0-5.632-4.567-10.199-10.199-10.199 h-208.91c-5.632,0-10.199,4.567-10.199,10.199C169.238,390.039,173.804,394.606,179.437,394.606z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M123.654,394.606h14.219c5.632,0,10.199-4.567,10.199-10.199c0-5.632-4.567-10.199-10.199-10.199 h-14.219c-5.632,0-10.199,4.567-10.199,10.199C113.454,390.039,118.022,394.606,123.654,394.606z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M179.437,444.92h208.91c5.632,0,10.199-4.567,10.199-10.199c0-5.632-4.567-10.199-10.199-10.199 h-208.91c-5.632,0-10.199,4.567-10.199,10.199C169.238,440.353,173.804,444.92,179.437,444.92z"/>
                                            <path style="fill:#4D3D36;"
                                                  d="M123.654,444.92h14.219c5.632,0,10.199-4.567,10.199-10.199c0-5.632-4.567-10.199-10.199-10.199 h-14.219c-5.632,0-10.199,4.567-10.199,10.199C113.454,440.353,118.022,444.92,123.654,444.92z"/>
                                        </g>
                                    </g>
                                </svg>
                                <span class="hide-menu">НБ элементлари</span>
                            </a>
                        </li>

                        @php // Cash register @endphp
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 339.346 339.346"
                                     xml:space="preserve" width="25px" height="25px" fill="#000000" class="me-2">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                    <g id="SVGRepo_iconCarrier">
                                        <g>
                                            <g>
                                                <rect y="18.601" style="fill:#f6f5f4;" width="185.888"
                                                      height="302.145"/>
                                                <rect x="46.803" y="54.843" style="fill:#333E48;" width="92.283"
                                                      height="11"/>
                                                <rect x="62.051" y="75.905" style="fill:#333E48;" width="61.786"
                                                      height="11"/>
                                                <rect x="21.765" y="127.904" style="fill:#5C6670;" width="142.359"
                                                      height="9"/>
                                                <rect x="21.765" y="153.025" style="fill:#5C6670;" width="142.359"
                                                      height="9"/>
                                                <rect x="27.73" y="196.238" style="fill:#5C6670;" width="76.43"
                                                      height="9"/>
                                                <rect x="27.73" y="220.238" style="fill:#5C6670;" width="76.43"
                                                      height="9"/>
                                                <rect x="27.73" y="244.238" style="fill:#5C6670;" width="76.43"
                                                      height="9"/>
                                                <rect x="27.73" y="268.238" style="fill:#5C6670;" width="76.43"
                                                      height="9"/>
                                                <rect x="127.729" y="196.238" style="fill:#333E48;" width="30.43"
                                                      height="9"/>
                                                <rect x="127.729" y="220.238" style="fill:#333E48;" width="30.43"
                                                      height="9"/>
                                                <rect x="127.729" y="244.238" style="fill:#333E48;" width="30.43"
                                                      height="9"/>
                                                <rect x="127.729" y="268.238" style="fill:#333E48;" width="30.43"
                                                      height="9"/>
                                            </g>
                                            <g>
                                                <rect x="135.539" y="146.634"
                                                      transform="matrix(-0.7071 0.7071 -0.7071 -0.7071 538.8729 169.3377)"
                                                      style="fill:#006132;" width="197.653" height="99.278"/>
                                                <rect x="200.799" y="113.516"
                                                      transform="matrix(-0.7071 -0.7071 0.7071 -0.7071 261.3057 500.7798)"
                                                      style="fill:#00783E;" width="67.137" height="165.512"/>
                                                <ellipse
                                                    transform="matrix(-0.7071 -0.7071 0.7071 -0.7071 261.2996 500.782)"
                                                    style="fill:#006132;" cx="234.365" cy="196.274" rx="24.011"
                                                    ry="17.896"/>
                                            </g>
                                            <g>
                                                <circle style="fill:#D9A460;" cx="213.191" cy="134.504" r="36.424"/>
                                                <circle style="fill:#FEDD3D;" cx="213.191" cy="134.504" r="25.986"/>
                                            </g>
                                            <g>
                                                <circle style="fill:#5C6670;" cx="249.254" cy="55.451" r="28.08"/>
                                                <circle style="fill:#f6f5f4;" cx="249.254" cy="55.451" r="20.033"/>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="hide-menu">Касса</span>
                            </a>
                            <ul aria-expanded="false" class="collapse second-level ps-3">
                                @php // Expense and Income @endphp
                                <li class="sidebar-item">
                                    <a
                                        class="sidebar-link waves-effect waves-dark sidebar-link"
                                        href="{{ route('expense-and-income.index') }}"
                                        aria-expanded="false">
                                        <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                             xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 512 512" xml:space="preserve" fill="#000000" class="me-2">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                               stroke-linejoin="round"/>
                                            <g id="SVGRepo_iconCarrier">
                                                <g transform="translate(1 1)">
                                                    <polygon style="fill:#62a0ea;"
                                                             points="7.533,434.2 502.467,434.2 502.467,502.467 7.533,502.467 "/>
                                                    <g>
                                                        <path style="fill:#241f31;"
                                                              d="M502.467,511H7.533C2.413,511-1,507.587-1,502.467V434.2c0-5.12,3.413-8.533,8.533-8.533h494.933 c5.12,0,8.533,3.413,8.533,8.533v68.267C511,507.587,507.587,511,502.467,511z M16.067,493.933h477.867v-51.2H16.067V493.933z"/>
                                                        <path style="fill:#241f31;"
                                                              d="M468.333,476.867H459.8c-5.12,0-8.533-3.413-8.533-8.533c0-5.12,3.413-8.533,8.533-8.533h8.533 c5.12,0,8.533,3.413,8.533,8.533C476.867,473.453,473.453,476.867,468.333,476.867z M169.667,476.867H50.2 c-5.12,0-8.533-3.413-8.533-8.533c0-5.12,3.413-8.533,8.533-8.533h119.467c5.12,0,8.533,3.413,8.533,8.533 C178.2,473.453,174.787,476.867,169.667,476.867z"/>
                                                    </g>
                                                    <path style="fill:#62a0ea;"
                                                          d="M451.267,125.293v-56.32c0-5.973-4.267-10.24-10.24-10.24h-184.32c-5.973,0-10.24,4.267-10.24,10.24 v57.173c0,5.12-4.267,10.24-10.24,10.24h-92.16v-34.133h62.293c3.413,0,5.973-2.56,5.973-5.973V13.507 c0-3.413-2.56-5.973-5.973-5.973H47.64c-3.413,0-5.973,2.56-5.973,5.973v81.92c0,3.413,2.56,5.973,5.973,5.973h62.293v34.133H34.84 c-5.973,0-10.24,4.267-10.24,10.24v244.053c0,5.973,4.267,10.24,10.24,10.24h440.32c5.973,0,10.24-4.267,10.24-10.24V145.773 c0-5.973-4.267-10.24-10.24-10.24h-13.653C455.533,135.533,451.267,131.267,451.267,125.293L451.267,125.293z"/>
                                                    <path style="fill:#241f31;"
                                                          d="M475.16,408.6H34.84c-10.24,0-18.773-8.533-18.773-18.773V145.773 C16.067,135.533,24.6,127,34.84,127h66.56v-17.067H47.64c-7.68,0-14.507-6.827-14.507-14.507v-81.92C33.133,5.827,39.96-1,47.64-1 h158.72c7.68,0,14.507,6.827,14.507,14.507v81.92c0,7.68-6.827,14.507-14.507,14.507H152.6V127h83.627 c0.853,0,1.707-0.853,1.707-1.707v-56.32c0-10.24,8.533-18.773,18.773-18.773H441.88c9.387,0,17.92,8.533,17.92,18.773v57.173 c0,0.853,0.853,1.707,1.707,1.707h13.653c10.24,0,18.773,8.533,18.773,18.773V390.68C493.933,400.067,485.4,408.6,475.16,408.6z M34.84,144.067c-0.853,0-1.707,0.853-1.707,1.707v244.053c0,0.853,0.853,1.707,1.707,1.707h440.32 c0.853,0,1.707-0.853,1.707-1.707V145.773c0-0.853-0.853-1.707-1.707-1.707h-13.653c-10.24,0-18.773-8.533-18.773-18.773v-56.32 c0-0.853-0.853-1.707-1.707-1.707h-184.32c-0.853,0-1.707,0.853-1.707,1.707v57.173c0,10.24-8.533,18.773-18.773,18.773h-92.16 c-5.12,0-8.533-3.413-8.533-8.533v-34.133c0-5.12,3.413-8.533,8.533-8.533H203.8V16.067H50.2v76.8h59.733 c5.12,0,8.533,3.413,8.533,8.533v34.133c0,5.12-3.413,8.533-8.533,8.533H34.84z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="272.067,84.333 425.667,84.333 425.667,178.2 272.067,178.2 "/>
                                                    <path style="fill:#241f31;"
                                                          d="M425.667,186.733h-153.6c-5.12,0-8.533-3.413-8.533-8.533V84.333c0-5.12,3.413-8.533,8.533-8.533 h153.6c5.12,0,8.533,3.413,8.533,8.533V178.2C434.2,183.32,430.787,186.733,425.667,186.733z M280.6,169.667h136.533v-76.8H280.6 V169.667z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="41.667,400.067 468.333,400.067 468.333,434.2 41.667,434.2 "/>
                                                    <path style="fill:#241f31;"
                                                          d="M468.333,442.733H41.667c-5.12,0-8.533-3.413-8.533-8.533v-34.133c0-5.12,3.413-8.533,8.533-8.533 h426.667c5.12,0,8.533,3.413,8.533,8.533V434.2C476.867,439.32,473.453,442.733,468.333,442.733z M50.2,425.667h409.6V408.6H50.2 V425.667z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="272.067,212.333 306.2,212.333 306.2,246.467 272.067,246.467 "/>
                                                    <path style="fill:#241f31;"
                                                          d="M306.2,255h-34.133c-5.12,0-8.533-3.413-8.533-8.533v-34.133c0-5.12,3.413-8.533,8.533-8.533H306.2 c5.12,0,8.533,3.413,8.533,8.533v34.133C314.733,251.587,311.32,255,306.2,255z M280.6,237.933h17.067v-17.067H280.6V237.933z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="331.8,212.333 365.933,212.333 365.933,246.467 331.8,246.467 "/>
                                                    <path style="fill:#241f31;"
                                                          d="M365.933,255H331.8c-5.12,0-8.533-3.413-8.533-8.533v-34.133c0-5.12,3.413-8.533,8.533-8.533h34.133 c5.12,0,8.533,3.413,8.533,8.533v34.133C374.467,251.587,371.053,255,365.933,255z M340.333,237.933H357.4v-17.067h-17.067V237.933 z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="391.533,212.333 425.667,212.333 425.667,246.467 391.533,246.467 "/>
                                                    <path style="fill:#241f31;"
                                                          d="M425.667,255h-34.133c-5.12,0-8.533-3.413-8.533-8.533v-34.133c0-5.12,3.413-8.533,8.533-8.533 h34.133c5.12,0,8.533,3.413,8.533,8.533v34.133C434.2,251.587,430.787,255,425.667,255z M400.067,237.933h17.067v-17.067h-17.067 V237.933z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="272.067,272.067 306.2,272.067 306.2,306.2 272.067,306.2 "/>
                                                    <path style="fill:#241f31;"
                                                          d="M306.2,314.733h-34.133c-5.12,0-8.533-3.413-8.533-8.533v-34.133c0-5.12,3.413-8.533,8.533-8.533 H306.2c5.12,0,8.533,3.413,8.533,8.533V306.2C314.733,311.32,311.32,314.733,306.2,314.733z M280.6,297.667h17.067V280.6H280.6 V297.667z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="331.8,272.067 365.933,272.067 365.933,306.2 331.8,306.2 "/>
                                                    <path style="fill:#241f31;"
                                                          d="M365.933,314.733H331.8c-5.12,0-8.533-3.413-8.533-8.533v-34.133c0-5.12,3.413-8.533,8.533-8.533 h34.133c5.12,0,8.533,3.413,8.533,8.533V306.2C374.467,311.32,371.053,314.733,365.933,314.733z M340.333,297.667H357.4V280.6 h-17.067V297.667z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="391.533,272.067 425.667,272.067 425.667,306.2 391.533,306.2 "/>
                                                    <path style="fill:#241f31;"
                                                          d="M425.667,314.733h-34.133c-5.12,0-8.533-3.413-8.533-8.533v-34.133c0-5.12,3.413-8.533,8.533-8.533 h34.133c5.12,0,8.533,3.413,8.533,8.533V306.2C434.2,311.32,430.787,314.733,425.667,314.733z M400.067,297.667h17.067V280.6 h-17.067V297.667z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="272.067,331.8 306.2,331.8 306.2,365.933 272.067,365.933 "/>
                                                    <path style="fill:#241f31;"
                                                          d="M306.2,374.467h-34.133c-5.12,0-8.533-3.413-8.533-8.533V331.8c0-5.12,3.413-8.533,8.533-8.533 H306.2c5.12,0,8.533,3.413,8.533,8.533v34.133C314.733,371.053,311.32,374.467,306.2,374.467z M280.6,357.4h17.067v-17.067H280.6 V357.4z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="331.8,331.8 365.933,331.8 365.933,365.933 331.8,365.933 "/>
                                                    <path style="fill:#241f31;"
                                                          d="M365.933,374.467H331.8c-5.12,0-8.533-3.413-8.533-8.533V331.8c0-5.12,3.413-8.533,8.533-8.533 h34.133c5.12,0,8.533,3.413,8.533,8.533v34.133C374.467,371.053,371.053,374.467,365.933,374.467z M340.333,357.4H357.4v-17.067 h-17.067V357.4z"/>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="391.533,331.8 425.667,331.8 425.667,365.933 391.533,365.933 "/>
                                                    <g>
                                                        <path style="fill:#241f31;"
                                                              d="M425.667,374.467h-34.133c-5.12,0-8.533-3.413-8.533-8.533V331.8c0-5.12,3.413-8.533,8.533-8.533 h34.133c5.12,0,8.533,3.413,8.533,8.533v34.133C434.2,371.053,430.787,374.467,425.667,374.467z M400.067,357.4h17.067v-17.067 h-17.067V357.4z"/>
                                                        <path style="fill:#241f31;"
                                                              d="M203.8,365.933H50.2c-5.12,0-8.533-3.413-8.533-8.533s3.413-8.533,8.533-8.533h153.6 c5.12,0,8.533,3.413,8.533,8.533S208.92,365.933,203.8,365.933z M144.067,178.2c-5.12,0-8.533-3.413-8.533-8.533v-34.133 c0-5.12,3.413-8.533,8.533-8.533c5.12,0,8.533,3.413,8.533,8.533v34.133C152.6,174.787,149.187,178.2,144.067,178.2z M109.933,178.2c-5.12,0-8.533-3.413-8.533-8.533v-34.133c0-5.12,3.413-8.533,8.533-8.533s8.533,3.413,8.533,8.533v34.133 C118.467,174.787,115.053,178.2,109.933,178.2z M186.733,84.333c-5.12,0-8.533-3.413-8.533-8.533V50.2 c-2.56,0-4.267-0.853-5.973-2.56c-3.413-3.413-3.413-8.533,0-11.947l8.533-8.533c0.853-0.853,1.707-1.707,2.56-1.707 c0.853-0.853,2.56-0.853,3.413-0.853l0,0l0,0l0,0c0.853,0,2.56,0,3.413,0.853c0.853,0,1.707,0.853,2.56,1.707 s1.707,1.707,1.707,2.56c0.853,0.853,0.853,2.56,0.853,3.413l0,0l0,0V75.8C195.267,80.92,191.853,84.333,186.733,84.333z M152.6,84.333c-5.12,0-8.533-3.413-8.533-8.533V50.2c-2.56,0-4.267-0.853-5.973-2.56c-3.413-3.413-3.413-8.533,0-11.947 l8.533-8.533c0.853-0.853,1.707-1.707,2.56-1.707c0.853-0.853,2.56-0.853,3.413-0.853l0,0l0,0l0,0c0.853,0,2.56,0,3.413,0.853 c0.853,0,1.707,0.853,2.56,1.707s1.707,1.707,1.707,2.56c0.853,0.853,0.853,2.56,0.853,3.413l0,0l0,0V75.8 C161.133,80.92,157.72,84.333,152.6,84.333z M109.933,84.333c-2.56,0-4.267-0.853-5.973-2.56c-3.413-3.413-3.413-8.533,0-11.947 l8.533-8.533c3.413-3.413,8.533-3.413,11.947,0c3.413,3.413,3.413,8.533,0,11.947l-8.533,8.533 C114.2,83.48,112.493,84.333,109.933,84.333z M75.8,84.333c-5.12,0-8.533-3.413-8.533-8.533V50.2c-2.56,0-4.267-0.853-5.973-2.56 c-3.413-3.413-3.413-8.533,0-11.947l8.533-8.533c0.853-0.853,1.707-1.707,2.56-1.707C73.24,24.6,74.947,24.6,75.8,24.6l0,0l0,0 l0,0c0.853,0,2.56,0,3.413,0.853c0.853,0,1.707,0.853,2.56,1.707c0.853,0.853,1.707,1.707,1.707,2.56 c0.853,0.853,0.853,2.56,0.853,3.413l0,0l0,0V75.8C84.333,80.92,80.92,84.333,75.8,84.333z"/>
                                                    </g>
                                                    <polygon style="fill:#D0E8F9;"
                                                             points="67.267,203.8 186.733,203.8 186.733,357.4 67.267,357.4 "/>
                                                    <g>
                                                        <path style="fill:#241f31;"
                                                              d="M186.733,365.933H67.267c-5.12,0-8.533-3.413-8.533-8.533V203.8c0-5.12,3.413-8.533,8.533-8.533 h119.467c5.12,0,8.533,3.413,8.533,8.533v153.6C195.267,362.52,191.853,365.933,186.733,365.933z M75.8,348.867h102.4V212.333 H75.8V348.867z"/>
                                                        <path style="fill:#241f31;"
                                                              d="M237.933,383c-5.12,0-8.533-3.413-8.533-8.533V161.133c0-5.12,3.413-8.533,8.533-8.533 c5.12,0,8.533,3.413,8.533,8.533v213.333C246.467,379.587,243.053,383,237.933,383z M135.533,331.8H92.867 c-5.12,0-8.533-3.413-8.533-8.533c0-5.12,3.413-8.533,8.533-8.533h42.667c5.12,0,8.533,3.413,8.533,8.533 C144.067,328.387,140.653,331.8,135.533,331.8z M161.133,289.133H152.6c-5.12,0-8.533-3.413-8.533-8.533 c0-5.12,3.413-8.533,8.533-8.533h8.533c5.12,0,8.533,3.413,8.533,8.533S166.253,289.133,161.133,289.133z M101.4,289.133h-8.533 c-5.12,0-8.533-3.413-8.533-8.533c0-5.12,3.413-8.533,8.533-8.533h8.533c5.12,0,8.533,3.413,8.533,8.533 S106.52,289.133,101.4,289.133z M161.133,246.467h-42.667c-5.12,0-8.533-3.413-8.533-8.533c0-5.12,3.413-8.533,8.533-8.533h42.667 c5.12,0,8.533,3.413,8.533,8.533C169.667,243.053,166.253,246.467,161.133,246.467z"/>
                                                    </g>
                                                </g>
                                            </g>
                                    </svg>
                                        <span class="hide-menu">Касса (кирим ва харажат)</span></a>
                                    <ul>

                                    </ul>
                                </li>

                                @php // Cash Report @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                       href="{{ route('cash-report.index') }}"
                                       aria-expanded="false">
                                        <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                             xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 503.467 503.467" xml:space="preserve" class="me-2">
                                    <g transform="translate(1 1)">
                                        <path style="fill:#E4F2DE;" d="M61.293,419.693c-5.973,5.973-14.507,10.24-23.893,10.24h102.4V105.667
                                        c0-18.773-15.36-34.133-34.133-34.133c-9.387,0-17.92,3.413-23.893,10.24c5.973-5.973,14.507-10.24,23.893-10.24h256V3.267H3.267 V395.8c0,18.773,15.36,34.133,34.133,34.133C46.787,429.933,55.32,426.52,61.293,419.693L61.293,419.693z M498.2,105.667V498.2
                                        H139.8v-68.267V105.667c0-18.773-15.36-34.133-34.133-34.133h256h102.4C482.84,71.533,498.2,86.893,498.2,105.667L498.2,105.667z"/>
                                        <path style="fill:#80D6FA;"
                                              d="M208.067,327.533H242.2v-51.2h-34.133V327.533z M344.6,327.533h34.133V242.2H344.6V327.533z M276.333,327.533h34.133V208.067h-34.133V327.533z M412.867,327.533H447V139.8h-34.133V327.533z"/>
                                    </g>
                                            <path style="fill:#51565F;" d="M499.2,503.467H140.8c-2.56,0-4.267-1.707-4.267-4.267v-64H89.6c-2.56,0-4.267-1.707-4.267-4.267
                                    c0-2.56,1.707-4.267,4.267-4.267h46.933v-320c0-16.213-13.653-29.867-29.867-29.867S76.8,90.453,76.8,106.667V396.8
                                    c0,21.333-17.067,38.4-38.4,38.4S0,418.133,0,396.8V4.267C0,1.707,1.707,0,4.267,0h358.4c2.56,0,4.267,1.707,4.267,4.267V38.4
                                    c0,2.56-1.707,4.267-4.267,4.267S358.4,40.96,358.4,38.4V8.533H8.533V396.8c0,16.213,13.653,29.867,29.867,29.867
                                    s29.867-13.653,29.867-29.867V106.667c0-21.333,17.067-38.4,38.4-38.4s38.4,17.067,38.4,38.4v388.267h349.867V106.667
                                    c0-16.213-13.653-29.867-29.867-29.867H166.4c-2.56,0-4.267-1.707-4.267-4.267c0-2.56,1.707-4.267,4.267-4.267h298.667
                                    c21.333,0,38.4,17.067,38.4,38.4V499.2C503.467,501.76,501.76,503.467,499.2,503.467z M430.933,435.2H209.067
                                    c-2.56,0-4.267-1.707-4.267-4.267c0-2.56,1.707-4.267,4.267-4.267h221.867c2.56,0,4.267,1.707,4.267,4.267
                                    C435.2,433.493,433.493,435.2,430.933,435.2z M396.8,384h-51.2c-2.56,0-4.267-1.707-4.267-4.267c0-2.56,1.707-4.267,4.267-4.267
                                    h51.2c2.56,0,4.267,1.707,4.267,4.267C401.067,382.293,399.36,384,396.8,384z M311.467,384h-102.4c-2.56,0-4.267-1.707-4.267-4.267
                                    c0-2.56,1.707-4.267,4.267-4.267h102.4c2.56,0,4.267,1.707,4.267,4.267C315.733,382.293,314.027,384,311.467,384z M413.867,332.8
                                    h-34.133c-2.56,0-4.267-1.707-4.267-4.267v-81.067h-25.6v81.067c0,2.56-1.707,4.267-4.267,4.267h-34.133
                                    c-2.56,0-4.267-1.707-4.267-4.267v-115.2h-25.6v115.2c0,2.56-1.707,4.267-4.267,4.267H243.2c-2.56,0-4.267-1.707-4.267-4.267V281.6
                                    h-25.6v46.933c0,2.56-1.707,4.267-4.267,4.267s-4.267-1.707-4.267-4.267v-51.2c0-2.56,1.707-4.267,4.267-4.267H243.2
                                    c2.56,0,4.267,1.707,4.267,4.267v46.933h25.6v-115.2c0-2.56,1.707-4.267,4.267-4.267h34.133c2.56,0,4.267,1.707,4.267,4.267v115.2
                                    h25.6V243.2c0-2.56,1.707-4.267,4.267-4.267h34.133c2.56,0,4.267,1.707,4.267,4.267v81.067h25.6V140.8
                                    c0-2.56,1.707-4.267,4.267-4.267h17.067c2.56,0,4.267,1.707,4.267,4.267c0,2.56-1.707,4.267-4.267,4.267h-12.8v183.467
                                    C418.133,331.093,416.427,332.8,413.867,332.8z"/>
                                </svg>
                                        <span class="hide-menu">Касса ҳисоботи</span>
                                    </a>
                                </li>

                                @php // Exchange Rates @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                       href="{{ route('exchange-rates.index') }}" aria-expanded="false">
                                        <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                             xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 512 512"
                                             xml:space="preserve" fill="#3d3846" class="me-2">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                               stroke-linejoin="round"/>
                                            <g id="SVGRepo_iconCarrier">
                                                <circle style="fill:#FFC850;" cx="136.262" cy="144.782" r="128.307"/>
                                                <circle style="fill:#FFDC64;" cx="136.262" cy="144.782" r="102.645"/>
                                                <path style="fill:#FFC850;"
                                                      d="M169.438,195.398c-45.255-0.798-82.976-38.506-83.79-83.761c-0.127-7.101,0.616-14.005,2.129-20.617 c1.555-6.794-6.587-11.742-11.556-6.856c-18.81,18.499-29.083,45.645-24.352,75.009c5.73,35.569,34.433,64.272,70.002,70.002 c29.364,4.731,56.511-5.542,75.009-24.352c4.887-4.97-0.062-13.111-6.857-11.556C183.422,194.78,176.529,195.523,169.438,195.398z"/>
                                                <circle style="fill:#FFF082;" cx="183.307" cy="97.736" r="21.384"/>
                                                <circle style="fill:#FFC850;" cx="375.767" cy="367.18" r="128.307"/>
                                                <circle style="fill:#FFDC64;" cx="375.767" cy="367.18" r="102.645"/>
                                                <path style="fill:#FFC850;"
                                                      d="M408.944,417.796c-45.255-0.798-82.976-38.506-83.79-83.761c-0.127-7.101,0.616-14.005,2.129-20.617 c1.555-6.794-6.587-11.742-11.556-6.856c-18.81,18.499-29.083,45.645-24.352,75.009c5.73,35.569,34.433,64.272,70.002,70.002 c29.364,4.731,56.51-5.542,75.009-24.352c4.887-4.97-0.062-13.111-6.857-11.556C422.927,417.178,416.034,417.921,408.944,417.796z"/>
                                                <circle style="fill:#FFF082;" cx="422.813" cy="320.134" r="21.384"/>
                                                <path
                                                    d="M232.658,241.182c41.811-41.812,51.182-105.415,25.845-156.783c36.104,0.509,70.439,12.065,99.441,33.513 c25.076,18.545,44.291,43.012,56.214,71.342l-18.439-15.067c-3.429-2.803-8.481-2.295-11.284,1.136 c-2.802,3.429-2.295,8.481,1.136,11.283l38.621,31.56c1.454,1.188,3.253,1.809,5.076,1.809c0.912,0,1.829-0.156,2.713-0.473 c2.651-0.954,4.607-3.228,5.153-5.991l9.672-48.93c0.86-4.344-1.966-8.563-6.312-9.422c-4.344-0.858-8.563,1.966-9.422,6.312 l-3.589,18.156c-13.12-29.544-33.595-55.082-60.004-74.61c-32.449-23.997-70.993-36.681-111.466-36.681 c-2.25,0-4.514,0.055-6.771,0.135c-1.462-2.159-2.984-4.291-4.585-6.383c-2.691-3.518-7.723-4.188-11.242-1.498 c-3.518,2.691-4.188,7.724-1.498,11.242c36.419,47.619,31.862,115.549-10.6,158.012c-23.453,23.452-54.249,35.177-85.057,35.174 c-30.8-0.002-61.61-11.727-85.057-35.174c-46.899-46.9-46.899-123.213,0-170.113c42.462-42.462,110.393-47.019,158.012-10.6 c3.518,2.691,8.552,2.02,11.242-1.498c2.69-3.518,2.02-8.551-1.498-11.242C164.981-4.894,87.986,0.265,39.865,48.387 c-53.153,53.153-53.153,139.64,0,192.795c26.577,26.577,61.487,39.865,96.397,39.865S206.082,267.758,232.658,241.182z"/>
                                                <path
                                                    d="M484.16,284.479c-2.69-3.518-7.723-4.187-11.242-1.497c-3.518,2.691-4.188,7.724-1.497,11.242 c36.423,47.619,31.867,115.551-10.597,158.016c-23.453,23.452-54.249,35.177-85.057,35.174c-30.8-0.002-61.61-11.727-85.057-35.174 c-46.899-46.9-46.899-123.213,0-170.113c42.465-42.463,110.397-47.019,158.016-10.597c3.519,2.693,8.552,2.021,11.242-1.497 c2.691-3.518,2.021-8.551-1.497-11.242c-53.979-41.288-130.975-36.13-179.1,11.996c-42.342,42.342-50.954,105.837-25.836,156.784 c-36.109-0.507-70.446-12.063-99.45-33.513c-25.076-18.545-44.291-43.012-56.214-71.342l18.439,15.067 c3.429,2.802,8.481,2.295,11.284-1.136c2.802-3.429,2.295-8.481-1.136-11.284l-38.621-31.56c-2.181-1.782-5.138-2.289-7.788-1.337 c-2.651,0.954-4.607,3.228-5.153,5.991l-9.672,48.93c-0.86,4.344,1.966,8.563,6.312,9.422c0.525,0.104,1.048,0.154,1.564,0.154 c3.751,0,7.103-2.646,7.858-6.466l3.589-18.156c13.12,29.544,33.595,55.082,60.004,74.61 c32.449,23.997,70.993,36.681,111.466,36.681c2.254,0,4.523-0.055,6.784-0.135c4.789,7.084,10.306,13.816,16.572,20.083 c26.577,26.577,61.487,39.865,96.397,39.865s69.82-13.288,96.397-39.865C520.29,415.453,525.446,338.457,484.16,284.479z"/>
                                                <path
                                                    d="M414.794,333.502c0,4.429,3.59,8.019,8.019,8.019s8.019-3.59,8.019-8.019c0-17.489-20.102-31.518-47.046-33.864v-9.439 c0-4.429-3.59-8.019-8.019-8.019s-8.019,3.59-8.019,8.019v9.439c-26.943,2.346-47.046,16.375-47.046,33.864 c0,23.466,21.605,32.682,47.046,39.808v45.307c-18.196-1.941-31.007-10.237-31.007-17.754c0-4.429-3.59-8.019-8.019-8.019 s-8.019,3.59-8.019,8.019c0,17.489,20.102,31.519,47.046,33.864v9.439c0,4.429,3.59,8.019,8.019,8.019s8.019-3.59,8.019-8.019 v-9.439c26.943-2.346,47.046-16.375,47.046-33.864c0-23.466-21.605-32.682-47.046-39.808v-45.307 C401.982,317.688,414.794,325.985,414.794,333.502z M336.741,333.502c0-7.518,12.811-15.814,31.007-17.754v40.844 C344.804,349.669,336.741,343.411,336.741,333.502z M414.794,400.863c0,7.518-12.811,15.814-31.007,17.754v-40.844 C406.731,384.696,414.794,390.954,414.794,400.863z"/>
                                                <path
                                                    d="M136.262,84.373c10.675,0,20.929,3.918,28.873,11.03c3.299,2.954,8.369,2.674,11.323-0.624 c2.954-3.3,2.674-8.369-0.624-11.323c-10.889-9.75-24.943-15.12-39.572-15.12c-32.721,0-59.342,26.62-59.342,59.342v9.088h-9.088 c-4.429,0-8.019,3.59-8.019,8.019c0,4.429,3.59,8.019,8.019,8.019h9.088v9.088c0,32.721,26.62,59.342,59.342,59.342 c14.626,0,28.677-5.367,39.565-15.115c3.3-2.954,3.581-8.023,0.627-11.323c-2.954-3.301-8.025-3.581-11.323-0.627 c-7.943,7.11-18.196,11.027-28.869,11.027c-23.878,0-43.303-19.426-43.303-43.303v-9.088h43.303c4.429,0,8.019-3.59,8.019-8.019 s-3.59-8.019-8.019-8.019H92.958v-9.088C92.958,103.8,112.385,84.373,136.262,84.373z"/>
                                            </g>
                                        </svg>
                                        <span class="hide-menu">Валюта курслари</span>
                                    </a>
                                </li>

                                @php // Supplier @endphp
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                    href="{{ route('supplier.index') }}" aria-expanded="false">
                                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 339.346 339.346" xml:space="preserve" width="25px" height="25px" fill="#000000" class="me-2">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                            <g id="SVGRepo_iconCarrier"> <g> <g> <rect y="18.601" style="fill:#f6f5f4;" width="185.888" height="302.145"/> <rect x="46.803" y="54.843" style="fill:#333E48;" width="92.283" height="11"/> <rect x="62.051" y="75.905" style="fill:#333E48;" width="61.786" height="11"/> <rect x="21.765" y="127.904" style="fill:#5C6670;" width="142.359" height="9"/> <rect x="21.765" y="153.025" style="fill:#5C6670;" width="142.359" height="9"/> <rect x="27.73" y="196.238" style="fill:#5C6670;" width="76.43" height="9"/> <rect x="27.73" y="220.238" style="fill:#5C6670;" width="76.43" height="9"/> <rect x="27.73" y="244.238" style="fill:#5C6670;" width="76.43" height="9"/> <rect x="27.73" y="268.238" style="fill:#5C6670;" width="76.43" height="9"/> <rect x="127.729" y="196.238" style="fill:#333E48;" width="30.43" height="9"/> <rect x="127.729" y="220.238" style="fill:#333E48;" width="30.43" height="9"/> <rect x="127.729" y="244.238" style="fill:#333E48;" width="30.43" height="9"/> <rect x="127.729" y="268.238" style="fill:#333E48;" width="30.43" height="9"/> </g> <g> <rect x="135.539" y="146.634" transform="matrix(-0.7071 0.7071 -0.7071 -0.7071 538.8729 169.3377)" style="fill:#006132;" width="197.653" height="99.278"/> <rect x="200.799" y="113.516" transform="matrix(-0.7071 -0.7071 0.7071 -0.7071 261.3057 500.7798)" style="fill:#00783E;" width="67.137" height="165.512"/> <ellipse transform="matrix(-0.7071 -0.7071 0.7071 -0.7071 261.2996 500.782)" style="fill:#006132;" cx="234.365" cy="196.274" rx="24.011" ry="17.896"/> </g> <g> <circle style="fill:#D9A460;" cx="213.191" cy="134.504" r="36.424"/> <circle style="fill:#FEDD3D;" cx="213.191" cy="134.504" r="25.986"/> </g> <g> <circle style="fill:#5C6670;" cx="249.254" cy="55.451" r="28.08"/> <circle style="fill:#f6f5f4;" cx="249.254" cy="55.451" r="20.033"/> </g> </g> </g>
                                        </svg>
                                    <span class="hide-menu">Фирмалар</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    @php // Profit and Loss @endphp
                    @can ('aodAccess')
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                               href="{{ route('profit-and-loss.index') }}" aria-expanded="false">
                                <svg height="25px" width="25px" version="1.1" id="Capa_1"
                                     xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 512 512" xml:space="preserve" class="me-2">
                                    <g>
                                        <g>
                                            <g>
                                                <path style="fill:#5C546A;"
                                                      d="M407.672,501.742l-40-136c-1.242-4.234-5.672-6.688-9.93-5.414 c-4.234,1.242-6.664,5.688-5.414,9.93L363.429,408H148.571l11.101-37.742c1.25-4.242-1.18-8.688-5.414-9.93 c-4.281-1.281-8.687,1.18-9.93,5.414l-40,136c-1.25,4.242,1.18,8.688,5.414,9.93C110.5,511.898,111.258,512,112,512 c3.461,0,6.648-2.258,7.672-5.742L143.865,424h224.269l24.193,82.258c1.023,3.484,4.211,5.742,7.672,5.742 c0.742,0,1.508-0.102,2.258-0.328C406.492,510.43,408.922,505.984,407.672,501.742z"/>
                                            </g>
                                            <g>
                                                <path style="fill:#5C546A;"
                                                      d="M256,48c4.422,0,8-3.578,8-8V8c0-4.422-3.578-8-8-8s-8,3.578-8,8v32C248,44.422,251.578,48,256,48 z"/>
                                            </g>
                                        </g>
                                        <g>
                                            <path style="fill:#FFFFFF;"
                                                  d="M488,32H24c-4.422,0-8,3.578-8,8v312c0,13.234,10.766,24,24,24h432c13.234,0,24-10.766,24-24V40 C496,35.578,492.422,32,488,32z"/>
                                        </g>
                                        <g>
                                            <polygon style="fill:#FFE1B2;"
                                                     points="80,368 432,368 432,112 264,264 192,208 80,304.003 		"/>
                                        </g>
                                        <g>
                                            <polygon style="fill:#FDC88E;"
                                                     points="264,360 264,272 192,208 192,360 		"/>
                                        </g>
                                        <g>
                                            <g>
                                                <path style="fill:#FF4F19;"
                                                      d="M88,312c-2.047,0-4.094-0.781-5.656-2.344c-3.125-3.125-3.125-8.188,0-11.312l92.687-92.687 c9.359-9.359,24.578-9.359,33.937,0l49.375,49.375c3.125,3.125,8.188,3.125,11.312,0l148.687-148.687 c3.125-3.125,8.188-3.125,11.312,0c3.125,3.125,3.125,8.187,0,11.312L280.969,266.344c-9.359,9.359-24.578,9.359-33.938,0 l-49.375-49.375c-3.125-3.125-8.188-3.125-11.312,0l-92.687,92.687C92.094,311.219,90.047,312,88,312z"/>
                                            </g>
                                        </g>
                                        <g>
                                            <g>
                                                <path style="fill:#FF4F19;"
                                                      d="M424,192c-4.422,0-8-3.578-8-8v-64h-64c-4.422,0-8-3.578-8-8c0-4.422,3.578-8,8-8h72 c4.422,0,8,3.578,8,8v72C432,188.422,428.422,192,424,192z"/>
                                            </g>
                                        </g>
                                        <g>
                                            <path style="fill:#8B8996;"
                                                  d="M480,48v304c0,4.411-3.589,8-8,8H40c-4.411,0-8-3.589-8-8V48H480 M488,32H24c-4.422,0-8,3.578-8,8 v312c0,13.234,10.766,24,24,24h432c13.234,0,24-10.766,24-24V40C496,35.578,492.422,32,488,32L488,32z"/>
                                        </g>
                                        <g>
                                            <path style="fill:#9F6459;"
                                                  d="M8,56h496c4.418,0,8-3.582,8-8V32c0-4.418-3.582-8-8-8H8c-4.418,0-8,3.582-8,8v16 C0,52.418,3.582,56,8,56z"/>
                                        </g>
                                    </g>
                                    </svg>
                                <span class="hide-menu">Фойда ва зарар</span>
                            </a>
                        </li>
                    @endcan

                    @php // File @endphp
                    <li class="sidebar-item">
                        <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('file.index') }}"
                           aria-expanded="false">
                            <svg width="25px" height="25px" viewBox="0 0 32 32" fill="none"
                                 xmlns="http://www.w3.org/2000/svg" class="me-2">
                                <g clip-path="url(#clip0_901_2684)">
                                    <path
                                        d="M31 4V26C31 26.55 30.55 27 30 27H26V8C26 7.45 25.55 7 25 7H21V3H30C30.55 3 31 3.45 31 4Z"
                                        fill="#668077"/>
                                    <path
                                        d="M26 27V30C26 30.55 25.55 31 25 31H7C6.45 31 6 30.55 6 30V25V8C6 7.45 6.45 7 7 7H21H25C25.55 7 26 7.45 26 8V27Z"
                                        fill="#FFE6EA"/>
                                    <path
                                        d="M21 3V7H7C6.45 7 6 7.45 6 8V25H2C1 25 1 24 1 24V2C1 1.45 1.45 1 2 1H20C20.55 1 21 1.45 21 2V3Z"
                                        fill="#FFC44D"/>
                                    <path
                                        d="M12 13H15M12 16H20M12 20H20M12 24H20M21 7V2C21 1.447 20.553 1 20 1H2C1.447 1 1 1.447 1 2V24C1 24 1 25 2 25H3M26 27H30C30.553 27 31 26.553 31 26V4C31 3.447 30.553 3 30 3H24M26 30C26 30.553 25.553 31 25 31H7C6.447 31 6 30.553 6 30V8C6 7.447 6.447 7 7 7H25C25.553 7 26 7.447 26 8V30Z"
                                        stroke="#000000" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </g>
                                <defs>
                                    <clipPath id="clip0_901_2684">
                                        <rect width="32" height="32" fill="white"/>
                                    </clipPath>
                                </defs>
                            </svg>
                            <span class="hide-menu">Файллар</span></a>
                    </li>

                    @can ('hasAccess')
                        @php // Setting -> Stage @endphp
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                               aria-expanded="false">
                                <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                     xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 512 512" xml:space="preserve" fill="#000000" class="me-2">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                    <g id="SVGRepo_iconCarrier">
                                        <path style="fill:#56ACE0;"
                                              d="M501.801,313.316V198.684h-52.94c-4.002-13.486-9.367-26.387-15.958-38.527l37.434-37.434 l-81.059-81.058l-37.434,37.434c-12.14-6.592-25.041-11.956-38.527-15.958V10.199H198.684v52.94 c-13.486,4.002-26.387,9.367-38.527,15.958l-37.434-37.433l-81.058,81.058l37.434,37.434c-6.592,12.14-11.956,25.041-15.958,38.527 H10.199v114.634h52.94c4.002,13.486,9.367,26.387,15.958,38.527l-37.433,37.433l81.058,81.058l37.434-37.434 c12.14,6.592,25.041,11.956,38.527,15.958v52.941h114.634v-52.94c13.486-4.002,26.387-9.367,38.527-15.958l37.434,37.434 l81.058-81.058l-37.434-37.434c6.592-12.14,11.956-25.041,15.958-38.527h52.94V313.316z M256,348.038 c-50.831,0-92.038-41.207-92.038-92.038s41.207-92.038,92.038-92.038s92.038,41.207,92.038,92.038S306.831,348.038,256,348.038z"/>
                                        <path
                                            d="M313.316,512H198.684c-5.633,0-10.199-4.566-10.199-10.199v-45.473c-9.042-3.045-17.876-6.704-26.398-10.931l-32.153,32.153 c-3.982,3.983-10.441,3.983-14.424,0L34.451,396.49c-3.983-3.983-3.983-10.441,0-14.424l32.153-32.153 c-4.229-8.52-7.886-17.355-10.932-26.398H10.199C4.566,323.516,0,318.95,0,313.317V198.684c0-5.633,4.566-10.199,10.199-10.199 h45.473c3.045-9.043,6.704-17.876,10.932-26.398l-32.153-32.153c-3.983-3.983-3.983-10.441,0-14.424l81.059-81.059 c3.982-3.983,10.441-3.983,14.424,0l32.153,32.153c8.521-4.229,17.356-7.886,26.398-10.932V10.199 C188.484,4.566,193.051,0,198.684,0h114.634c5.633,0,10.199,4.566,10.199,10.199v45.473c9.042,3.045,17.876,6.704,26.398,10.932 l32.153-32.153c3.982-3.983,10.441-3.983,14.424,0l81.059,81.059c3.983,3.983,3.983,10.441,0,14.424l-32.153,32.153 c4.229,8.52,7.886,17.354,10.931,26.398h45.472c5.633,0,10.199,4.566,10.199,10.199v114.634c0,5.633-4.566,10.199-10.199,10.199 h-45.473c-3.045,9.044-6.704,17.877-10.931,26.398l32.153,32.153c3.983,3.983,3.983,10.441,0,14.424L396.49,477.55 c-3.982,3.983-10.441,3.983-14.424,0l-32.153-32.153c-8.521,4.229-17.356,7.886-26.398,10.931v45.472 C323.516,507.434,318.949,512,313.316,512z M208.883,491.602h94.236v-42.741c0-4.515,2.969-8.493,7.298-9.778 c12.688-3.766,24.989-8.861,36.563-15.144c3.969-2.155,8.883-1.443,12.079,1.751l30.222,30.222l66.634-66.634l-30.222-30.222 c-3.193-3.194-3.906-8.109-1.751-12.079c6.283-11.57,11.377-23.871,15.144-36.563c1.285-4.329,5.263-7.298,9.778-7.298h42.739 v-94.236h-42.741c-4.515,0-8.493-2.969-9.778-7.298c-3.767-12.691-8.861-24.992-15.144-36.563c-2.155-3.97-1.443-8.885,1.751-12.079 l30.222-30.222l-66.634-66.634l-30.222,30.222c-3.194,3.194-8.107,3.906-12.079,1.751c-11.573-6.284-23.874-11.378-36.563-15.144 c-4.329-1.285-7.298-5.263-7.298-9.778V20.398h-94.236v42.741c0,4.515-2.969,8.493-7.298,9.778 c-12.688,3.766-24.989,8.861-36.563,15.144c-3.97,2.154-8.885,1.442-12.079-1.751l-30.222-30.222l-66.634,66.634l30.222,30.222 c3.193,3.194,3.906,8.109,1.751,12.079c-6.283,11.572-11.378,23.873-15.144,36.563c-1.285,4.329-5.263,7.298-9.778,7.298H20.398 v94.236h42.741c4.515,0,8.493,2.969,9.778,7.298c3.766,12.689,8.861,24.99,15.144,36.563c2.155,3.97,1.443,8.885-1.751,12.079 l-30.222,30.222l66.634,66.634l30.222-30.222c3.195-3.193,8.109-3.905,12.079-1.751c11.573,6.284,23.874,11.378,36.563,15.144 c4.329,1.285,7.298,5.263,7.298,9.778v42.738H208.883z M256,358.237c-56.373,0-102.237-45.863-102.237-102.237 S199.627,153.763,256,153.763S358.237,199.627,358.237,256S312.373,358.237,256,358.237z M256,174.162 c-45.125,0-81.838,36.713-81.838,81.838c0,45.125,36.713,81.838,81.838,81.838c45.126,0,81.838-36.713,81.838-81.838 S301.126,174.162,256,174.162z"/>
                                        <path
                                            d="M256,394.954c-5.633,0-10.199-4.566-10.199-10.199c0-5.633,4.566-10.199,10.199-10.199 c50.206,0,95.15-31.792,111.838-79.111c1.873-5.312,7.697-8.099,13.011-6.226c5.312,1.874,8.099,7.698,6.226,13.011 C367.515,357.69,314.84,394.954,256,394.954z"/>
                                        <path
                                            d="M384.62,272.319c-0.16,0-0.321-0.003-0.483-0.011c-5.627-0.262-9.975-5.036-9.713-10.664 c0.088-1.875,0.132-3.774,0.132-5.643c0-5.633,4.566-10.199,10.199-10.199c5.633,0,10.199,4.566,10.199,10.199 c0,2.186-0.052,4.404-0.154,6.595C394.545,268.06,390.034,272.319,384.62,272.319z"/>
                                    </g>
                            </svg>
                                <span class="hide-menu">Созламалар</span>
                            </a>
                            <ul aria-expanded="false" class="collapse second-level ps-3">
                                <li class="sidebar-item">
                                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                       href="{{ route('stage.index') }}" aria-expanded="false">
                                        <svg width="25px" height="25px" viewBox="0 -0.5 256 256" version="1.1"
                                             xmlns="http://www.w3.org/2000/svg"
                                             preserveAspectRatio="xMidYMid" class="me-2">
                                            <g>
                                                <path
                                                    d="M88.9565187,166.907984 L121.256108,134.606322 L72.7528359,134.608395 L72.6367696,134.608395 C45.7591337,134.608395 23.9697609,156.397767 23.9697609,183.273331 C23.9697609,210.153039 45.7570611,231.94034 72.634697,231.94034 C74.6762201,231.94034 76.6845814,231.799402 78.6597809,231.554834 C68.037643,210.62974 71.4615985,184.400832 88.9565187,166.907984"
                                                    fill="#87C040"></path>
                                                <path
                                                    d="M128.336152,183.213225 L128.336152,137.534923 L94.0406366,171.834584 L93.9577321,171.915416 C74.9518775,190.92127 74.9518775,221.734797 93.9577321,240.740651 C112.963587,259.746506 143.777113,259.746506 162.782968,240.740651 C164.225506,239.298113 165.520889,237.766453 166.745802,236.199558 C144.44864,228.906035 128.338224,207.94778 128.336152,183.213225"
                                                    fill="#87C040"></path>
                                                <path
                                                    d="M167.713712,166.897621 L135.414123,134.598032 L135.414123,183.21737 C135.414123,210.097079 157.203496,231.884379 184.081131,231.884379 C210.958767,231.884379 232.74814,210.097079 232.74814,183.219443 C232.74814,181.17792 232.607203,179.169559 232.362634,177.194359 C211.437541,187.81857 185.208633,184.392541 167.713712,166.897621"
                                                    fill="#87C040"></path>
                                                <path
                                                    d="M241.546379,93.0732448 C240.103841,91.6307066 238.574253,90.335324 237.007358,89.1104101 C229.713836,111.4055 208.753508,127.517988 184.021026,127.517988 L138.344796,127.517988 L172.721144,161.896408 C191.726998,180.902262 222.542597,180.902262 241.546379,161.896408 C260.552234,142.890553 260.552234,112.077027 241.546379,93.0732448"
                                                    fill="#87C040"></path>
                                                <path
                                                    d="M184.027244,23.1080724 C181.98572,23.1080724 179.977359,23.2469374 178.00216,23.4915056 C188.624298,44.4186717 185.200342,70.6455074 167.705422,88.1404276 L135.407905,120.440017 L184.027244,120.440017 C210.902807,120.440017 232.69218,98.6506444 232.69218,71.7730085 C232.694252,44.8953725 210.904879,23.1080724 184.027244,23.1080724"
                                                    fill="#ED9A2D"></path>
                                                <path
                                                    d="M162.704208,14.305688 C143.698354,-4.69809386 112.884827,-4.69809386 93.8789728,14.3077607 C92.4385073,15.7502988 91.1431246,17.2798867 89.9182108,18.8488541 C112.213301,26.1423767 128.325789,47.100632 128.325789,71.8331142 L128.325789,117.511416 L162.621304,83.2138282 L162.704208,83.1329963 C181.710063,64.1271418 181.710063,33.3136152 162.704208,14.305688"
                                                    fill="#5698C6"></path>
                                                <path
                                                    d="M88.9958983,88.1735894 L90.3804033,89.6182001 L121.214656,120.452453 L121.247818,120.452453 L121.247818,71.9450353 L121.247818,71.828969 C121.247818,44.9513331 99.458445,23.1619603 72.5808091,23.1619603 C45.7031732,23.1619603 23.9138004,44.9492605 23.915873,71.8268964 C23.915873,73.8642743 24.054738,75.8664177 24.2972336,77.837472 C45.2368354,67.2215519 71.4989055,70.6765966 88.9958983,88.1735894"
                                                    fill="#5698C6"></path>
                                                <path
                                                    d="M19.7975924,165.515189 C20.1789531,164.383543 20.5893303,163.268477 21.0390872,162.169993 C21.0950477,162.031128 21.1447904,161.89019 21.2028235,161.75547 C21.6961052,160.574081 22.2391297,159.423781 22.809098,158.28799 C22.9334548,158.037204 23.0619567,157.790563 23.1925313,157.539777 C23.7790806,156.412276 24.396719,155.303428 25.0558097,154.223597 C25.1262786,154.107531 25.2029652,153.997682 25.273434,153.885761 C25.8993629,152.876399 26.5605262,151.896054 27.2486335,150.932289 C27.3895712,150.733318 27.5284362,150.532275 27.673519,150.335377 C28.4134416,149.328087 29.1844534,148.349814 29.9865543,147.396412 C30.1502907,147.201587 30.3160997,147.012979 30.4839813,146.824371 C31.2674287,145.916567 32.0757475,145.031562 32.9151555,144.177646 C32.9897695,144.103031 33.0581657,144.024272 33.1327798,143.949658 C34.0260756,143.052217 34.9566786,142.194156 35.9080076,141.360965 C36.1152688,141.178576 36.32253,140.998258 36.5318639,140.820014 C37.4894108,140.003404 38.4697564,139.217884 39.4791185,138.465526 C39.6117657,138.368113 39.7506307,138.276918 39.8832779,138.181578 C40.8263165,137.495544 41.7921538,136.840598 42.7787173,136.212597 C42.9735428,136.08824 43.1662958,135.959738 43.3631939,135.837454 C44.4285166,135.178363 45.5187107,134.558652 46.6296309,133.97003 C46.8638361,133.847746 47.1001139,133.729607 47.3363917,133.609396 C48.4452392,133.043572 49.5685951,132.502621 50.7168223,132.011412 C50.7727828,131.988613 50.8266707,131.961669 50.8826312,131.936797 C52.0702381,131.433153 53.2847889,130.983396 54.5138479,130.560583 C54.7791423,130.469388 55.0423641,130.378193 55.3097311,130.291143 C56.5263545,129.895274 57.7595588,129.530495 59.0114166,129.213385 C59.2020969,129.167787 59.3989951,129.126335 59.5896754,129.08281 C60.7503383,128.803008 61.9234368,128.562585 63.108971,128.357396 C63.3307405,128.320089 63.5504374,128.274492 63.772207,128.24133 C65.0592992,128.034069 66.3629723,127.878623 67.6770085,127.762556 C67.9609564,127.737685 68.2469769,127.719031 68.5309247,127.696233 C69.8905584,127.59882 71.2564099,127.530424 72.6409149,127.530424 L118.319217,127.528351 L83.940797,93.1499314 C64.9349424,74.1461495 34.1193432,74.1440769 15.1155613,93.1499314 C-3.89029323,112.155786 -3.89029323,142.969313 15.1155613,161.975167 C16.5560268,163.415633 18.0648886,164.74625 19.6297108,165.969091 C19.6815261,165.815718 19.7457771,165.668562 19.7975924,165.515189"
                                                    fill="#5698C6"></path>
                                                <path
                                                    d="M84.0547906,93.1499314 L111.274408,120.369548 L74.6886558,120.369548 L72.6139709,120.413073 C50.333389,120.413073 31.5513766,105.440522 25.7770788,85.0087104 C44.3124503,74.6145599 68.2055246,77.302738 83.9739588,93.0690996 L84.0547906,93.1499314"
                                                    fill="#446BA6"></path>
                                                <path
                                                    d="M121.229164,71.8186059 L121.229164,110.313234 L95.358818,84.4428873 L93.8623919,83.006567 C78.1063934,67.2526411 75.4119975,43.3823656 85.7771314,24.8532119 C106.233815,30.6109288 121.229164,49.4074495 121.229164,71.7025397 L121.229164,71.8186059"
                                                    fill="#446BA6"></path>
                                                <path
                                                    d="M162.853437,83.0956893 L135.63382,110.317379 L135.63382,73.7316271 L135.590295,71.6569422 C135.590295,49.3763603 150.562846,30.5922753 170.994658,24.8200501 C181.388808,43.3554216 178.70063,67.2484959 162.934268,83.0148574 L162.853437,83.0956893"
                                                    fill="#446BA6"></path>
                                                <path
                                                    d="M183.996154,120.338459 L145.501527,120.338459 L171.371873,94.4681128 L172.808193,92.9716868 C188.562119,77.2156883 212.432395,74.5212923 230.961548,84.8864263 C225.203831,105.343109 206.407311,120.338459 184.112221,120.338459 L183.996154,120.338459"
                                                    fill="#FFF101"></path>
                                                <path
                                                    d="M172.627876,161.703655 L145.408259,134.484038 L181.994011,134.484038 L184.068696,134.440513 C206.349278,134.442586 225.13129,149.415137 230.905588,169.846949 C212.368144,180.239026 188.477142,177.552921 172.708708,161.784487 L172.627876,161.703655"
                                                    fill="#45AB47"></path>
                                                <path
                                                    d="M135.526044,182.993528 L135.526044,144.4989 L161.39639,170.369247 L162.894889,171.805567 C178.648815,187.561566 181.343211,211.427696 170.978077,229.958922 C150.521394,224.201205 135.526044,205.406757 135.526044,183.107522 L135.526044,182.993528"
                                                    fill="#45AB47"></path>
                                                <path
                                                    d="M94.1587755,171.946505 L121.378392,144.724815 L121.378392,181.310567 L121.421917,183.385252 C121.421917,205.665834 106.449366,224.449919 86.0175545,230.224217 C75.6234039,211.6847 78.311582,187.793698 94.0779436,172.027337 L94.1587755,171.946505"
                                                    fill="#45AB47"></path>
                                                <path
                                                    d="M72.8046512,134.685081 L111.299279,134.685081 L85.4289326,160.555428 L83.9926123,162.051854 C68.2366138,177.807852 44.3684109,180.502248 25.8371846,170.137114 C31.5969741,149.678359 50.3914221,134.685081 72.6885849,134.685081 L72.8046512,134.685081"
                                                    fill="#45AB47"></path>
                                            </g>
                                        </svg>
                                        <span class="hide-menu">Бўлим махсулотлари</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        @php // Client @endphp
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                               href="{{ route('user.index') }}"
                               aria-expanded="false">
                                <svg width="25px" height="25px" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"
                                     version="1.1" class="me-2">
                                    <path style="fill:#427794;stroke:#2A424F"
                                          d="M 22,43 C 18,48 6.5,45 4.2,56 2,62 2,81 14,79 13,64 12,57 12,57 c 0,0 1,14 2,21 9,4 24,4 35,-1 0,-8 -1,-13 0,-18 0,-5 0,19 0,19 0,0 6,2 8,-5 3,-10 5,-24 -9,-28 -9,-1 -7,-2 -8,-2 -2,0 -18,0 -18,0 z"/>
                                    <path style="fill:#C29B82;stroke:#693311"
                                          d="m 23,38 c 0,0 1,3 -1,5 3,4 11,8 18,0 -1,-2 -1,-2 -1,-5 0,0 -16,0 -16,0 z"/>
                                    <path style="fill:#CDA68E;stroke:#693311"
                                          d="M 31,42 C 17,42 7.6,4.8 31,4.2 55,4.1 44,42 31,42 z"/>
                                    <path style="fill:#553932;stroke:#311710"
                                          d="M 17,26 C 14,16 14,3.2 31,2.4 44,3.1 49,15 44,26 44,21 45,19 43,16 39,15 33,16 28,11 27,15 15,13 17,26 z"/>
                                    <path style="fill:#5F3E20;stroke:#311710"
                                          d="m 45,65 c 5,-8 0,-25 3,-31 3,-10 7,-16 16,-16 10,0 16,8 20,17 1,2 0,6 2,11 1,4 -1,8 -1,10 0,5 -1,3 2,9 -5,13 -34,10 -42,0 z"/>
                                    <path style="fill:#D8933B;stroke:#2A424F"
                                          d="m 58,60 c -5,5 -18,3 -20,13 -2,6 -1,25 11,24 -1,-16 -3,-23 -3,-23 0,0 2,15 3,21 9,5 23,5 35,-1 0,-6 -1,-13 0,-18 1,-5 0,20 0,20 0,0 7,1 9,-6 2,-9 4,-22 -7,-25 -9,-3 -10,-5 -12,-5 -1,0 -16,0 -16,0 z"/>
                                    <path style="fill:#DEB89F;stroke:#693311"
                                          d="m 58,54 c 0,0 1,3 0,7 3,3 10,8 16,0 -1,-4 -1,-4 -1,-7 0,0 -15,0 -15,0 z"/>
                                    <path style="fill:#DBBFA8;stroke:#693311"
                                          d="M 66,59 C 52,59 43,21 66,20 86,21 79,59 66,59 z"/>
                                    <path style="fill:#5F3E20"
                                          d="m 63,27 c -3,5 -7,8 -12,9 -4,1 2,-17 13,-17 5,0 13,3 15,15 -6,1 -14,-5 -16,-7"/>
                                </svg>
                                <span class="hide-menu">Клиентлар</span>
                            </a>
                        </li>
                    @endcan

                    @php // Staff @endphp
                    @can ('fullAccess')
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                               href="{{ route('user.staff') }}" aria-expanded="false">
                                <svg fill="#ffffff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                     width="25px" height="25px"
                                     viewBox="0 0 183.405 183.405" xml:space="preserve" class="me-2">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                    <g id="SVGRepo_iconCarrier">
                                        <g>
                                            <g>
                                                <path
                                                    d="M160.766,0H64.054c-5.307,0-9.965,2.755-12.662,6.902h8.915c1.154-0.524,2.402-0.859,3.748-0.859h96.711 c4.999,0,9.061,4.064,9.061,9.064v125.577c0,4.604-3.495,8.269-7.946,8.841c-0.292,2.29-1.054,4.422-2.259,6.26h1.145 c8.336,0,15.113-6.771,15.113-15.101V15.101C175.879,6.777,169.102,0,160.766,0z"/>
                                                <path
                                                    d="M140.057,13.804H43.348c-5.307,0-9.965,2.759-12.666,6.905h8.918c1.154-0.523,2.399-0.861,3.748-0.861h96.708 c5.011,0,9.072,4.064,9.072,9.066v125.579c0,4.604-3.501,8.258-7.946,8.83c-0.292,2.289-1.06,4.42-2.259,6.271h1.139 c8.342,0,15.125-6.771,15.125-15.113V28.914C155.176,20.581,148.398,13.804,140.057,13.804z"/>
                                                <path
                                                    d="M119.347,27.611H22.639c-8.336,0-15.113,6.771-15.113,15.107v125.58c0,8.33,6.777,15.107,15.113,15.107h96.708 c8.343,0,15.126-6.771,15.126-15.107V42.718C134.473,34.388,127.689,27.611,119.347,27.611z M128.42,168.298 c0,4.993-4.067,9.066-9.073,9.066H22.639c-5.005,0-9.07-4.067-9.07-9.066V42.718c0-4.997,4.064-9.067,9.07-9.067h96.708 c5.006,0,9.073,4.07,9.073,9.067V168.298z"/>
                                                <path
                                                    d="M80.236,107.747c6.777-4.549,11.447-13.548,11.447-21.644c0-11.43-9.252-20.7-20.688-20.7 c-11.43,0-20.685,9.271-20.685,20.7c0,8.096,4.67,17.095,11.429,21.644c-16.185,4.11-28.202,18.194-28.202,28.625 c0,12.324,74.933,12.324,74.933,0C108.46,125.941,96.452,111.857,80.236,107.747z M70.996,145.604l-10.507-10.485l8.835-21.361 H69.23l-3.428-3.945c1.668,0.597,3.386,0.962,5.188,0.962c1.806,0,3.517-0.359,5.179-0.943l-3.435,3.921h-0.076l8.842,21.361 L70.996,145.604z"/>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="hide-menu">Ҳодимлар</span>
                            </a>
                        </li>
                    @endcan

                    @php // Role @endphp
                    @can ('coreAccess')
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                               href="{{ route('role.index') }}" aria-expanded="false">
                                <svg height="25px" width="25px" version="1.1" id="Layer_1"
                                     xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 499.461 499.461" xml:space="preserve" fill="#000000" class="me-2">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>
                                    <g id="SVGRepo_iconCarrier">
                                        <path style="fill:#84DBFF;"
                                              d="M388.18,249.731c0,29.257-9.404,56.424-25.078,78.367c-5.224,7.314-11.494,14.629-17.763,20.898 c-22.988,21.943-54.335,36.571-87.771,37.616l0,0c-2.09,0-5.225,0-7.314,0c-2.09,0-5.224,0-7.314,0 c-34.482-2.09-64.784-15.673-88.816-37.616c-6.269-6.269-12.539-13.584-17.763-20.898c-15.673-21.943-25.078-49.11-25.078-78.367 c0-76.278,61.649-137.927,137.927-137.927C326.531,111.804,388.18,173.453,388.18,249.731z"/>
                                        <g>
                                            <ellipse transform="matrix(-0.1961 -0.9806 0.9806 -0.1961 40.5001 438.745)"
                                                     style="fill:#F8B64C;" cx="200.096" cy="202.771" rx="15.674"
                                                     ry="6.269"/>
                                            <ellipse
                                                transform="matrix(-0.9806 -0.1961 0.1961 -0.9806 554.8722 459.9698)"
                                                style="fill:#F8B64C;" cx="300.207" cy="202.516" rx="6.269" ry="15.674"/>
                                        </g>
                                        <path style="fill:#77767b;"
                                              d="M364.147,318.694c-5.224,7.314-11.494,14.629-17.763,20.898 c-22.988,21.943-54.335,36.571-87.771,37.616h-14.629c-34.482-2.09-64.784-15.673-88.816-37.616 c-6.269-6.269-12.539-13.584-17.763-20.898c2.09-6.269,5.224-11.494,8.359-15.673c0,0,1.045,0,2.09-1.045l0,0l0,0 c2.09-1.045,6.269-2.09,12.539-5.224c12.539-5.224,30.302-12.539,44.931-20.898c2.09-1.045,5.224-3.135,7.314-4.18 c2.09-1.045,4.18-3.135,6.269-4.18l0,0l2.09-9.404c0,0,8.359,15.673,25.078,18.808c1.045,0,3.135,0,4.18,0h2.09 c2.09,0,4.18,0,6.269,0l0,0c1.045,0,2.09,0,3.135-1.045c1.045,0,2.09-1.045,3.135-1.045c2.09-1.045,3.135-1.045,4.18-2.09 c1.045,0,2.09-1.045,3.135-2.09c2.09-1.045,3.135-2.09,4.18-3.135c2.09-2.09,3.135-3.135,4.18-4.18 c2.09-3.135,3.135-5.224,3.135-5.224l2.09,9.404c19.853,14.629,60.604,30.302,70.008,34.482c1.045,0,2.09,1.045,2.09,1.045 C358.922,307.2,362.057,313.469,364.147,318.694z"/>
                                        <path style="fill:#ACB3BA;"
                                              d="M219.951,296.751l8.359,14.629l11.494-17.763l-3.135-3.135l-9.404,13.584l-10.449-19.853 C217.861,287.347,218.906,291.527,219.951,296.751z"/>
                                        <path style="fill:#F8B64C;"
                                              d="M281.6,259.135c0,0-8.359,15.673-25.078,18.808c-2.09,0-4.18,0-6.269,0c-2.09,0-4.18,0-6.269,0 c-17.763-2.09-25.078-18.808-25.078-18.808s0-3.135,0-6.269c0-2.09,0-3.135,0-5.224c14.629,17.763,31.347,20.898,31.347,20.898 s16.718-3.135,31.347-20.898c0,1.045,0,3.135,0,5.224C281.6,256,281.6,259.135,281.6,259.135z"/>
                                        <path style="fill:#F7AF48;"
                                              d="M281.6,252.865c-14.629,15.673-29.257,18.808-30.302,18.808l0,0l0,0 c-1.045,0-15.673-3.135-30.302-18.808c0-2.09,0-3.135,0-5.224c14.629,17.763,31.347,20.898,31.347,20.898 s16.718-3.135,31.347-20.898C281.6,248.686,280.555,250.776,281.6,252.865z"/>
                                        <path style="fill:#FFD15C;"
                                              d="M302.498,189.127c0,3.135-1.045,5.224-1.045,5.224c-9.404,67.918-51.2,74.188-51.2,74.188 s-42.841-7.314-51.2-74.188c0,0,0-2.09-1.045-5.224l0,0c-2.09-16.718-4.18-68.963,52.245-67.918 C306.678,120.163,304.588,171.363,302.498,189.127z"/>
                                        <path style="fill:#40596B;"
                                              d="M355.788,304.065l-9.404,36.571c-22.988,21.943-54.335,36.571-87.771,37.616h-7.314V360.49 c1.045-2.09,2.09-4.18,3.135-7.314c2.09-5.224,5.224-10.449,7.314-14.629c0-1.045,1.045-1.045,1.045-2.09 c6.269-11.494,10.449-20.898,10.449-20.898c3.135-7.314,6.269-13.584,7.314-19.853c1.045-2.09,1.045-5.224,2.09-7.314 c3.135-14.629,1.045-21.943,1.045-21.943l0,0c19.853,14.629,60.604,30.302,70.008,34.482L355.788,304.065z"/>
                                        <path style="fill:#ACB3BA;"
                                              d="M280.555,296.751l-8.359,14.629l-11.494-17.763l3.135-3.135l9.404,13.584l10.449-19.853 C282.645,287.347,281.6,291.527,280.555,296.751z"/>
                                        <path style="fill:#FFFFFF;"
                                              d="M255.478,298.841l6.269,38.661c-4.18,7.314-8.359,16.718-11.494,24.033 c-3.135-7.314-8.359-15.673-11.494-24.033l6.269-38.661l-8.359-9.404l7.314-12.539l0,0c2.09,0,4.18,0,6.269,0c2.09,0,4.18,0,6.269,0 l0,0l7.314,12.539L255.478,298.841z"/>
                                        <g>
                                            <path style="fill:#334A5E;"
                                                  d="M257.567,378.253L257.567,378.253h-7.314c-2.09,0-5.224,0-7.314,0 c-34.482-2.09-64.784-15.673-88.816-37.616l-9.404-36.571l2.09-1.045l0,0l0,0c2.09-1.045,6.269-2.09,12.539-4.18 c12.539-5.224,30.302-12.539,44.931-20.898c2.09-1.045,5.224-3.135,7.314-4.18c2.09-1.045,4.18-3.135,6.269-4.18l0,0 c0,0-2.09,7.314,1.045,21.943c0,2.09,1.045,4.18,2.09,7.314c2.09,5.224,4.18,12.539,7.314,19.853c0,0,5.224,9.404,10.449,20.898 c0,1.045,1.045,1.045,1.045,2.09c2.09,5.224,5.224,10.449,7.314,14.629c1.045,2.09,2.09,5.224,3.135,7.314 C254.433,369.894,257.567,376.163,257.567,378.253z"/>
                                            <path style="fill:#334A5E;"
                                                  d="M302.498,189.127l-2.09,2.09c0,0-2.09-27.167-14.629-45.976c0,0-7.314,11.494-35.527,11.494 c-27.167,0-35.527-11.494-35.527-11.494c-12.539,18.808-14.629,45.976-14.629,45.976l-2.09-2.09l0,0 c-2.09-16.718-4.18-68.963,52.245-67.918C306.678,120.163,304.588,171.363,302.498,189.127z"/>
                                        </g>
                                        <path style="fill:#77767b;"
                                              d="M460.278,192.261c-5.224-17.763-11.494-34.482-20.898-49.11l17.763-39.706l-59.559-59.559 l-39.706,17.763c-15.673-8.359-32.392-15.673-49.11-20.898L293.094,0h-83.592l-16.718,39.706 c-17.763,5.224-34.482,11.494-49.11,20.898l-39.706-17.763L44.408,102.4l17.763,39.706c-8.359,15.673-15.673,32.392-20.898,49.11 L0.522,206.89v83.592L40.229,307.2c5.224,17.763,11.494,34.482,20.898,49.11l-17.763,39.706l59.559,59.559l39.706-17.763 c15.673,8.359,32.392,15.673,49.11,20.898l15.673,40.751h83.592l15.673-40.751c17.763-5.224,34.482-11.494,49.11-20.898 l39.706,17.763l59.559-59.559L437.29,356.31c8.359-15.673,15.673-32.392,20.898-49.11l40.751-15.673v-83.592L460.278,192.261z M373.551,373.029c-67.918,67.918-178.678,67.918-247.641,0c-67.918-67.918-67.918-178.678,0-247.641 c67.918-67.918,178.678-67.918,247.641,0C442.514,194.351,442.514,305.11,373.551,373.029z"/>
                                    </g>
                                </svg>
                                <span class="hide-menu">Роль</span>
                            </a>
                        </li>
                    @endcan

                    <div class="d-md-none">
                        <li class="sidebar-item">
                            <a class="sidebar-link waves-effect waves-dark sidebar-link"
                               href="{{ route('logout') }}"
                               aria-expanded="false">
                                <i class="fa fa-power-off icon-small"></i>
                                <span class="hide-menu">Чиқиш</span>
                            </a>
                        </li>
                    </div>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Page wrapper  -->
    <div class="page-wrapper">
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-12 d-flex no-block align-items-center">
                    <h4 class="page-title">{{ $title ?? 'Бошқарув панели' }}</h4>
                    <div class="ms-auto text-end">
                        {{ Breadcrumbs::render() }}
                    </div>
                </div>
            </div>
        </div>

        <div id="flash-messages" class="{{ session('large_screen') ? 'd-none d-lg-block' : '' }}" style="position: fixed; top: 20px; right: 20px; z-index: 1050;">
            @foreach (['success', 'error', 'warning', 'info'] as $msg)
                @if(session($msg))
                    <div class="flash-alert d-flex justify-content-between align-items-center"
                         role="alert"
                         style="
                    min-width: 200px;
                    max-width: 400px;
                    padding: 0.75rem 1rem 0.75rem 1.5rem;
                    border-radius: 12px;
                    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
                    transform: translateX(120%);
                    color: #fff;
                    font-weight: 500;
                    letter-spacing: 0.5px;
                    position: relative;
                    overflow: hidden;
                    background:
                        @switch($msg)
                            @case('success') linear-gradient(135deg, #38b000, #70e000) @break
                            @case('error') linear-gradient(135deg, #ff3c38, #ff7b6a) @break
                            @case('warning') linear-gradient(135deg, #ffb703, #ffd60a) @break
                            @case('info') linear-gradient(135deg, #0096c7, #00b4d8) @break
                            @default #333
                        @endswitch
                 ">
                        <span style="flex: 1; font-size: 1rem;">{!! session($msg) !!}</span>
                        <button type="button" class="btn-close" aria-label="Close"
                                style="
                            width: 28px;
                            height: 28px;
                            opacity: 0.8;
                            transition: all 0.3s ease;
                        "
                                onmouseover="this.style.opacity='1'; this.style.transform='scale(1.2)';"
                                onmouseout="this.style.opacity='0.8'; this.style.transform='scale(1)';"
                        ></button>
                    </div>
                @endif
            @endforeach
        </div>

        <div id="custom-confirm-container"></div>

        {{ $slot }}

        <footer class="footer text-center">
            All Rights Reserved by Matrix-admin. Designed and Developed by
            <a href="https://www.wrappixel.com">WrapPixel</a>.
        </footer>
    </div>
    <!-- End Page wrapper  -->
</div>

<script src="{{ asset('js/backend/libs/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/backend/libs/perfect-scrollbar.jquery.min.js') }}"></script>
<script src="{{ asset('js/backend/libs/sparkline.js') }}"></script>
<script src="{{ asset('js/backend/dist/waves.js') }}"></script>
<script src="{{ asset('js/backend/dist/sidebarmenu.js') }}"></script>
<script src="{{ asset('js/backend/dist/custom.min.js') }}"></script>
<script src="{{ asset('js/backend/libs/excanvas.js') }}"></script>
<script src="{{ asset('js/backend/libs/flot/jquery.flot.js') }}"></script>
<script src="{{ asset('js/backend/libs/flot/jquery.flot.pie.js') }}"></script>
<script src="{{ asset('js/backend/libs/flot/jquery.flot.time.js') }}"></script>
<script src="{{ asset('js/backend/libs/flot/jquery.flot.stack.js') }}"></script>
<script src="{{ asset('js/backend/libs/flot/jquery.flot.crosshair.js') }}"></script>
<script src="{{ asset('js/backend/libs/flot/jquery.flot.tooltip.min.js') }}"></script>
<script src="{{ asset('js/backend/dist/chart-page-init.js') }}"></script>
<script src="{{ asset('js/backend/libs/magnific-popup/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('js/backend/libs/magnific-popup/meg.init.js') }}"></script>
<script src="{{ asset('js/backend/package/file-input.js') }}"></script>
<script src="{{ asset('js/backend/package/inputmask.min.js') }}"></script>
<script src="{{ asset('js/backend/main.js') }}"></script>

</body>
</html>
