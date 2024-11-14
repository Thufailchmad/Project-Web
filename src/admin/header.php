<div class="nk-header is-light nk-header-fixed is-light">
    <div class="container-xl wide-xl">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ms-n1 me-3">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="/" class="logo-link">
                    <<img class="logo-light logo-img" src="./images/logo_web.png" srcset="./images/logo_web.png 2x" alt="logo">
                        <img class="logo-dark logo-img" src="./images/logo_web.png" srcset="./images/logo_web.png 2x" alt="logo-dark">
                        <img class="logo-small logo-img logo-img-small" src="./images/logo_web.png" srcset="./images/logo_web.png 2x" alt="logo-small">
                </a>
            </div><!-- .nk-header-brand -->
            <div class="nk-header-menu is-light">
                <div class="nk-header-menu-inner">
                    <!-- Menu -->
                    <div class="nk-menu nk-menu-main">
                        <di class="nk-menu-item has-sub">
                            <a href="admin/index.php" class="">
                                <div class="nk-block-head-content">
                                    <h6 class="nk-block-title page-title">Selamat Datang Admin</h6>
                                    <div class="nk-block-des text-soft">
                                        Di Website resmi <strong>Amanah Listrik</strong>
                                    </div>
                                </div>
                            </a>

                        </di><!-- .nk-menu-item -->
                    </div>
                    <!-- Menu -->
                </div>
            </div>
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar sm">
                                    <em class="icon ni ni-user-alt"></em>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar">

                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">Admin</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a class="dark-switch" href="#"><em class="icon ni ni-moon"></em><span>Dark Mode</span></a></li>
                                    <form action="/Logout" method="post">
                                        @csrf
                                        <li><a href="#"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
                                    </form>

                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">

                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div><!-- .nk-header-menu -->

        </div><!-- .nk-header-wrap -->
    </div><!-- .container-fliud -->
</div>