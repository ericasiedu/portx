<!-- Topbar -->
<header class="topbar">
    <div class="topbar-left">
        <span class="topbar-btn sidebar-toggler"><i>&#9776;</i></span>

        <a class="topbar-btn d-none d-md-block" href="#" data-provide="fullscreen tooltip" title="Fullscreen">
            <i class="material-icons fullscreen-default">fullscreen</i>
            <i class="material-icons fullscreen-active">fullscreen_exit</i>
        </a>



        <div class="topbar-divider d-none d-md-block"></div>


        <form onsubmit="ContainerHistory.searchContainer(event)">
            <div class="lookup d-none d-md-block topbar-search" id="theadmin-search">
                <input class="form-control w-300px" type="text" id="container_search">
                <div class="lookup-placeholder">
                    <i class="ti-search"></i>
                    <span data-provide="typing"
                          data-type="<strong>Query</strong> Container|<strong>Query</strong> Invoice|<strong>Query</strong> Container..."
                          data-loop="false" data-type-speed="90" data-back-speed="50" data-show-cursor="false"></span>
                </div>
            </div>
            <input type="submit" style="display: none;">
        </form>
    </div>

    <div class="topbar-right">


        <div class="topbar-divider"></div>

        <ul class="topbar-btns">
            <li class="dropdown">
                <span class="topbar-btn" data-toggle="dropdown"><img class="avatar" src="/img/avatar/1.jpg" alt="..."></span>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="/user/setting"><i class="ti-settings"></i> Settings</a>
                    <a class="dropdown-item" onclick="Logout.logout()" href="#"><i class="ti-power-off"></i> Logout</a>
                </div>
            </li>

            <!-- Notifications -->
            <li class="dropdown d-none d-md-block">
                <span class="topbar-btn has-new" data-toggle="dropdown"><i class="ti-bell"></i></span>
                <div class="dropdown-menu dropdown-menu-right">

                    <div class="media-list media-list-hover media-list-divided media-list-xs">
                        <a class="media media-new" href="#">
                            <span class="avatar bg-success"><i class="ti-user"></i></span>
                            <div class="media-body">
                                <p>New user registered</p>
                                <time datetime="2018-12-14 20:00">Just now</time>
                            </div>
                        </a>

                        <a class="media" href="#">
                            <span class="avatar bg-info"><i class="ti-shopping-cart"></i></span>
                            <div class="media-body">
                                <p>New invoice created</p>
                                <time datetime="2018-12-14 20:00">2 min ago</time>
                            </div>
                        </a>

                        <a class="media" href="#">
                            <span class="avatar bg-warning"><i class="ti-face-sad"></i></span>
                            <div class="media-body">
                                <p>Invoice BDN2001 approved by <b>Ben  Eshun</b></p>
                                <time datetime="2018-12-14 20:00">24 min ago</time>
                            </div>
                        </a>

                        <a class="media" href="#">
                            <span class="avatar bg-primary"><i class="ti-money"></i></span>
                            <div class="media-body">
                                <p>New payment for BDN2001 has made through Bank</p>
                                <time datetime="2018-12-14 20:00">53 min ago</time>
                            </div>
                        </a>
                    </div>

                    <div class="dropdown-footer">
                        <div class="left">
                            <a href="#">Read all notifications</a>
                        </div>

                        <div class="right">
                            <a href="#" data-provide="tooltip" title="Mark all as read"><i class="fa fa-circle-o"></i></a>
                            <a href="#" data-provide="tooltip" title="Update"><i class="fa fa-repeat"></i></a>
                        </div>
                    </div>

                </div>
            </li>
            <!-- END Notifications -->



        </ul>

    </div>
</header>
<!-- END Topbar -->