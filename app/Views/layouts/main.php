<?php
$sidebar_tree = session()->get("sidebar_tree");
//dd($sidebar_tree);

function buildChildMenu($children, $level = 1): string
{
    $html = '<ul class="hidden space-y-0.5 mt-2 mb-3">';
    foreach ($children as $child) {
        $hasChildren = !empty($child['children']);
        $indent = $level * 3;

        // Add subtle background for nested items
        $bgClass = $level > 1 ? 'bg-gray-50/50' : '';

        $html .= '<li>';
        $html .= '<a href="' . base_url($child['full_url']) . '" class="flex items-center px-3 py-2.5 text-sm text-gray-600 rounded-lg hover:bg-soko-50 hover:text-soko-700 transition-all duration-200 group ml-' . $indent . ' ' . $bgClass . '">';

        if (!empty($child['icon'])) {
            $html .= '<span class="material-symbols-rounded text-gray-400 group-hover:text-soko-500 transition-colors duration-200 mr-3" style="font-size: 18px;">' . $child['icon'] . '</span>';
        } else {
            $html .= '<span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-soko-500 transition-colors duration-200 mr-3"></span>';
        }

        $html .= '<span class="flex-1">' . esc($child['name']) . '</span>';

        if ($hasChildren) {
            $html .= '<span class="material-symbols-rounded text-gray-400 group-hover:text-gray-600 transition-all duration-200" style="font-size: 18px;">chevron_right</span>';
        }
        $html .= '</a>';

        if ($hasChildren) {
            $html .= buildChildMenu($child['children'], $level + 1);
        }
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}

function buildSidebarMenu($menuTree): string
{
    $html = '<div class="px-3 py-2"><h2 class="px-3 mb-2 text-xs font-bold text-gray-400 uppercase tracking-widest">Navigation</h2></div>';
    $html .= '<nav class="px-2 space-y-2">';

    foreach ($menuTree as $item) {
        $hasChildren = !empty($item['children']);

        // Add separator between menu groups
        $html .= '<div class="group/parent border-b border-gray-100 pb-2 mb-2 last:border-0 last:pb-0 last:mb-0">';

        $buttonClass = 'flex items-center w-full px-3 py-3 text-sm font-semibold text-gray-800 rounded-xl hover:bg-gradient-to-r hover:from-soko-100 hover:to-soko-50 hover:text-soko-700 transition-all duration-200';

        if ($item['full_url'] === '#') {
            $html .= '<button type="button" class="' . $buttonClass . '">';
        } else {
            $html .= '<a href="' . base_url($item['full_url']) . '" class="' . $buttonClass . '">';
        }

        if (!empty($item['icon'])) {
            $html .= '<span class="material-symbols-rounded filled text-gray-600 group-hover/parent:text-soko-600 transition-colors duration-200 mr-3" style="font-size: 22px; font-weight: 500;">' . $item['icon'] . '</span>';
        }

        $html .= '<span class="flex-1 text-left">' . esc($item['name']) . '</span>';

        if ($hasChildren) {
            $html .= '<span class="material-symbols-rounded text-gray-400 group-hover/parent:text-soko-500 transition-all duration-200 submenu-icon" style="font-size: 20px;">expand_more</span>';
        }

        if ($item['full_url'] === '#') {
            $html .= '</button>';
        } else {
            $html .= '</a>';
        }

        if ($hasChildren) {
            $html .= '<div class="mt-1 pl-2 border-l-2 border-gray-100 ml-3">' . buildChildMenu($item['children']) . '</div>';
        }
        $html .= '</div>';
    }
    $html .= '</nav>';
    return $html;
}

if (!empty($sidebar_tree)) {
    $sidebar_html = buildSidebarMenu($sidebar_tree);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('drawer-navigation').innerHTML = " . json_encode($sidebar_html) . ";
        });
    </script>";
}
?>

<!doctype html>
<html lang="en" class="font-montserrat bg-light h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <?= $this->renderSection('meta') ?>

    <link rel="icon" href="<?= base_url('img/favicon/favicon.ico') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= base_url('css/tailwind.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('css/sweetalert2.min.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('css/select2.min.css') ?>?v=<?= time() ?>">

    <?= $this->renderSection('link') ?>

    <script type="module" src="<?= base_url('js/moment.js') ?>"></script>
    <script src="<?= base_url('js/lodash.min.js') ?>"></script>
    <?= script_tag(base_url('js/jquery.js')) ?>
    <script src="<?= base_url('js/select2.min.js') ?>"></script>
    <script src="<?= base_url('js/sweetalert2.min.js') ?>"></script>
    <script src="<?= base_url('js/new.swal.js') ?>"></script>

    <title><?= $this->renderSection('title') ?></title>
</head>

<body class="bg-gradient-to-b from-white to-soko-200">

<main class="w-full relative min-h-[100dvh]">
    <aside class="sidebar bg-gradient-to-b from-white to-gray-50 w-72 fixed inline-block z-40 scrollbar-sidebar overflow-y-auto top-0 bottom-0 transition-transform md:transition-none -translate-x-full md:translate-x-0 shadow-2xl border-r border-gray-200/80"
           tabindex="-1" aria-labelledby="drawer-navigation-label">
        <!-- Logo Section -->
        <div class="sticky top-0 bg-white/95 backdrop-blur-sm border-b border-gray-200/80 px-6 py-5 z-10">
            <img src="<?= base_url('img/app-logo.png') ?>" alt="app-logo" class="h-9 w-auto"/>
        </div>
        <div id="drawer-navigation" class="py-6">
            <!-- Sidebar content will be injected here -->
        </div>
    </aside>

    <section class="flex flex-col w-full relative min-h-max overflow-y-auto md:pl-72 pt:16">
        <!-- Top navbar -->
        <!-- Top navbar -->
        <nav class="sticky top-0 z-20 bg-white/95 backdrop-blur-sm border-b border-gray-200/80 shadow-sm">
            <div class="px-4 py-3 lg:px-6">
                <div class="flex items-center justify-between">
                    <!-- Left: Mobile menu toggle -->
                    <div class="flex items-center">
                        <button type="button"
                                class="sidebar-toggle inline-flex items-center p-2 text-gray-600 rounded-lg md:hidden hover:bg-soko-50 hover:text-soko-700 transition-all duration-200">
                            <span class="sr-only">Open sidebar</span>
                            <span class="material-symbols-rounded" style="font-size: 24px;">menu</span>
                        </button>
                    </div>

                    <!-- Center: Page title or breadcrumb (optional) -->
                    <div class="hidden md:flex items-center flex-1 ml-4">
                        <h1 class="text-lg font-semibold text-gray-800">
<!--                            --><?php //= $this->renderSection('page-title') ?>
                            <?= session()->get('current_permission')['name'] ?? 'Dashboard' ?>
                        </h1>
                    </div>

                    <!-- Right: User actions -->
                    <div class="flex items-center space-x-3">
                        <!-- Notifications -->
                        <button type="button"
                                class="relative p-2 text-gray-600 rounded-lg hover:bg-soko-50 hover:text-soko-700 transition-all duration-200">
                            <span class="material-symbols-rounded" style="font-size: 24px;">notifications</span>
                            <!-- Notification badge -->
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                        </button>

                        <!-- User menu dropdown -->
                        <div class="relative">
                            <button type="button"
                                    id="user-menu-button"
                                    class="flex items-center space-x-2 p-2 text-gray-700 rounded-lg hover:bg-soko-50 transition-all duration-200">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-soko-500 to-soko-600 flex items-center justify-center text-white font-semibold text-sm">
                                    <?= strtoupper(substr(session()->get('full_name') ?? 'U', 0, 1)) ?>
                                </div>
                                <span class="hidden md:block text-sm font-medium"><?= session()->get('full_name') ?? 'User' ?></span>
                                <span class="material-symbols-rounded text-gray-400" style="font-size: 20px;">expand_more</span>
                            </button>

                            <!-- Dropdown menu -->
                            <div id="user-menu"
                                 class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200/80 py-1 z-50">
                                <a href="<?= base_url('profile') ?>"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-soko-50 hover:text-soko-700 transition-colors duration-150">
                                    <span class="material-symbols-rounded text-gray-400 mr-3" style="font-size: 20px;">person</span>
                                    Profile
                                </a>
                                <a href="<?= base_url('settings') ?>"
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-soko-50 hover:text-soko-700 transition-colors duration-150">
                                    <span class="material-symbols-rounded text-gray-400 mr-3" style="font-size: 20px;">settings</span>
                                    Settings
                                </a>
                                <hr class="my-1 border-gray-200">
                                <a href="<?= route_to('logout', $org_slug) ?>"
                                   class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150">
                                    <span class="material-symbols-rounded mr-3" style="font-size: 20px;">logout</span>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div
                class="side-overlay hidden fixed inset-0 bg-gray-600 backdrop-blur-[3px] bg-opacity-75 transition-all duration-100 z-30"></div>

        <?= view('components/flash-message') ?>

        <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
            <!-- We will be rendering the content inside a card -->
            <div class="mt-10 sm:mx-auto sm:w-full bg-white/90 backdrop-blur-sm p-8 rounded-lg shadow-lg border border-gray-200/80">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </section>
</main>

<script type="module" src="<?= base_url('js/anime.esm.min.js') ?>"></script>
<script src="<?= base_url('js/flowbite.min.js') ?>"></script>

<?= $this->renderSection('bottom-scripts') ?>
</body>
<script>


    $(document).ready(function () {
        // Sidebar toggle functionality
        $('.sidebar-toggle').on('click', function () {
            $('.sidebar').toggleClass('-translate-x-full');
            $('.side-overlay').toggleClass('hidden');
        });

        $('.side-overlay').on('click', function () {
            $('.sidebar').addClass('-translate-x-full');
            $('.side-overlay').addClass('hidden');
        });

        // Handle parent menu item clicks with children
        // $(document).on('click', '.sidebar nav > div > button, .sidebar nav > div > a', function (e) {
        //     const $parent = $(this).parent();
        //     const $submenu = $parent.find('> ul');
        //
        //     if ($submenu.length > 0) {
        //         e.preventDefault();
        //         // $submenu.slideToggle(200);
        //         if ($submenu.hasClass('hidden')) {
        //             $submenu.removeClass('hidden').slideDown(200);
        //         } else {
        //             $submenu.slideUp(200, function () {
        //                 $(this).addClass('hidden');
        //             });
        //         }
        //         // Toggle expand icon
        //         const $icon = $(this).find('.material-icons').last();
        //         $icon.text($icon.text() === 'expand_more' ? 'expand_less' : 'expand_more');
        //     }
        // });
        //
        // // Handle child menu item clicks with nested children
        // $(document).on('click', '.sidebar ul a', function (e) {
        //     const $submenu = $(this).next('ul');
        //
        //     if ($submenu.length > 0) {
        //         e.preventDefault();
        //         // $submenu.slideToggle(200);
        //         if ($submenu.hasClass('hidden')) {
        //             $submenu.removeClass('hidden').slideDown(200);
        //         } else {
        //             $submenu.slideUp(200, function () {
        //                 $(this).addClass('hidden');
        //             });
        //         }
        //
        //         // Toggle expand icon
        //         const $icon = $(this).find('.material-icons').last();
        //         $icon.text($icon.text() === 'expand_more' ? 'expand_less' : 'expand_more');
        //     }
        // });

        // Handle parent menu item clicks with children
        // $(document).on('click', '.sidebar nav > div > button, .sidebar nav > div > a', function (e) {
        //     const $parent = $(this).parent();
        //     const $submenu = $parent.find('> ul');
        //
        //     if ($submenu.length > 0) {
        //         e.preventDefault();
        //
        //         if ($submenu.hasClass('hidden')) {
        //             $submenu.removeClass('hidden').hide().slideDown(200);
        //         } else {
        //             $submenu.slideUp(200, function () {
        //                 $(this).addClass('hidden');
        //             });
        //         }
        //
        //         // Toggle expand icon
        //         const $icon = $(this).find('.material-icons').last();
        //         $icon.text($icon.text() === 'expand_more' ? 'expand_less' : 'expand_more');
        //     }
        // });
        //
        // // Handle child menu item clicks with nested children
        // $(document).on('click', '.sidebar ul a', function (e) {
        //     const $submenu = $(this).next('ul');
        //
        //     if ($submenu.length > 0) {
        //         e.preventDefault();
        //
        //         if ($submenu.hasClass('hidden')) {
        //             $submenu.removeClass('hidden').hide().slideDown(200);
        //         } else {
        //             $submenu.slideUp(200, function () {
        //                 $(this).addClass('hidden');
        //             });
        //         }
        //
        //         // Toggle expand icon
        //         const $icon = $(this).find('.material-icons').last();
        //         $icon.text($icon.text() === 'expand_more' ? 'expand_less' : 'expand_more');
        //     }
        // });

        // Handle parent menu item clicks with children
        $(document).on('click', '.sidebar nav > div > button, .sidebar nav > div > a', function (e) {
            const $parent = $(this).parent();
            const $submenu = $parent.find('> div > ul');

            if ($submenu.length > 0) {
                e.preventDefault();

                if ($submenu.hasClass('hidden')) {
                    $submenu.removeClass('hidden').hide().slideDown(200);
                    $(this).find('.submenu-icon').text('expand_less');
                } else {
                    $submenu.slideUp(200, function () {
                        $(this).addClass('hidden');
                    });
                    $(this).find('.submenu-icon').text('expand_more');
                }
            }
        });

        // Handle child menu item clicks with nested children
        $(document).on('click', '.sidebar ul a', function (e) {
            const $submenu = $(this).next('ul');

            if ($submenu.length > 0) {
                e.preventDefault();

                if ($submenu.hasClass('hidden')) {
                    $submenu.removeClass('hidden').hide().slideDown(200);
                    $(this).find('.material-symbols-rounded').last().text('expand_less');
                } else {
                    $submenu.slideUp(200, function () {
                        $(this).addClass('hidden');
                    });
                    $(this).find('.material-symbols-rounded').last().text('expand_more');
                }
            }
        });

        // Highlight active menu item based on current URL
        // const currentPath = window.location.pathname;
        // $('.sidebar a').each(function () {
        //     const linkPath = new URL($(this).attr('href'), window.location.origin).pathname;
        //
        //     if (linkPath === currentPath) {
        //         $(this).addClass('bg-blue-50 text-blue-600');
        //
        //         // Expand parent menus
        //         $(this).parents('ul').removeClass('hidden').css('display', 'block');
        //
        //         $(this).parents('ul').prev('a, button').find('.material-icons').last().text('expand_less');
        //     }
        // });

        // Highlight active menu item based on current URL
        // const currentPath = window.location.pathname;
        // $('.sidebar a').each(function () {
        //     const linkPath = new URL($(this).attr('href'), window.location.origin).pathname;
        //
        //     if (linkPath === currentPath) {
        //         $(this).addClass('bg-soko-100 text-soko-700 font-semibold border-l-4 border-soko-600');
        //         $(this).find('.material-icons').addClass('text-soko-600');
        //
        //         // Expand parent menus
        //         $(this).parents('ul').removeClass('hidden').css('display', 'block');
        //         $(this).parents('ul').prev('a, button').find('.material-icons').last().text('expand_less');
        //     }
        // });

        // Highlight active menu item based on current URL
        const currentPath = window.location.pathname;

        $('.sidebar a').each(function () {
            const linkPath = new URL($(this).attr('href'), window.location.origin).pathname;

            if (linkPath === currentPath) {
                $(this).removeClass('text-gray-700 text-gray-600 hover:bg-gray-100 hover:text-gray-900')
                    .addClass('bg-gradient-to-r from-soko-500 to-soko-600 text-white shadow-md shadow-soko-500/30 border-l-4 border-soko-600 font-semibold hover:text-white');
                $(this).find('.material-symbols-rounded').removeClass('material-symbols-rounded text-gray-400 text-gray-500')
                    .addClass('text-white material-symbols-rounded-filled');
                $(this).find('.w-1\\.5').removeClass('bg-gray-300').addClass('bg-white');

                // Expand parent menus
                $(this).parents('ul').removeClass('hidden').css('display', 'block');
                $(this).parents('ul').prev('a, button').find('.submenu-icon').text('expand_less');
            }
        });

        // Close sidebar on mobile when clicking a link
        $(document).on('click', '.sidebar a:not([href="#"])', function () {
            if (window.innerWidth < 768) { // Adjust breakpoint as needed
                $('.sidebar').addClass('-translate-x-full');
                $('.side-overlay').addClass('hidden');
            }
        });

        // Ensure sidebar is visible on md+ screens
        function handleSidebarVisibility() {
            if (window.innerWidth >= 768) {
                $('.sidebar').removeClass('-translate-x-full');
            } else {
                $('.sidebar').addClass('-translate-x-full');
            }
        }

        // Run on load
        handleSidebarVisibility();

        // Run on resize
        $(window).on('resize', handleSidebarVisibility);

        // User menu dropdown toggle
        $(document).on('click', '#user-menu-button', function (e) {
            e.stopPropagation();

            const userMenu = $('#user-menu')

            userMenu.toggleClass('hidden');

            $(this).find('.material-symbols-rounded').last().text(
                userMenu.hasClass('hidden') ? 'expand_more' : 'expand_less'
            );
        });

        // Close dropdown when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#user-menu-button, #user-menu').length) {
                $('#user-menu').addClass('hidden');
                $('#user-menu-button .material-symbols-rounded').last().text('expand_more');
            }
        });
    });
</script>
</html>
