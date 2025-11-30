<?php
$sidebar_tree = session()->get("sidebar_tree");

//function buildChildMenu($children): string
//{
//    $html = '<ul class="hidden pl-4 mt-2 space-y-2 border-l border-gray-200">';
//    foreach ($children as $child) {
//        $hasChildren = !empty($child['children']);
//        $html .= '<li>';
//        $html .= '<a href="' . base_url($child['fulll_url]) . '" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-gray-100">';
//        if (!empty($child['icon'])) {
//            $html .= '<span class="material-icons mr-3 text-gray-500">' . $child['icon'] . '</span>';
//        }
//        $html .= '<span class="flex-1">' . esc($child['name']) . '</span>';
//        if ($hasChildren) {
//            $html .= '<span class="material-icons text-gray-400">expand_more</span>';
//        }
//        $html .= '</a>';
//        if ($hasChildren) {
//            $html .= buildChildMenu($child['children']);
//        }
//        $html .= '</li>';
//    }
//    $html .= '</ul>';
//    return $html;
//}
//

//function buildChildMenu($children, $level = 1): string
//{
//    $html = '<ul class="hidden mt-1 space-y-1">';
//    foreach ($children as $child) {
//        $hasChildren = !empty($child['children']);
//        $html .= '<li>';
//        $html .= '<a href="' . base_url($child['full_url']) . '" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-150 group ml-' . ($level * 4) . '">';
//        if (!empty($child['icon'])) {
//            $html .= '<span class="material-icons text-gray-400 group-hover:text-gray-600 transition-colors duration-150" style="font-size: 20px;">' . $child['icon'] . '</span>';
//            $html .= '<span class="flex-1 ml-3">' . esc($child['name']) . '</span>';
//        } else {
//            $html .= '<span class="flex-1">' . esc($child['name']) . '</span>';
//        }
//        if ($hasChildren) {
//            $html .= '<span class="material-icons text-gray-400 group-hover:text-gray-600 transition-all duration-200" style="font-size: 20px;">expand_more</span>';
//        }
//        $html .= '</a>';
//        if ($hasChildren) {
//            $html .= buildChildMenu($child['children'], $level + 1);
//        }
//        $html .= '</li>';
//    }
//    $html .= '</ul>';
//    return $html;
//}

//function buildChildMenu($children, $level = 1): string
//{
//    $html = '<ul class="hidden space-y-0.5 mt-1">';
//    foreach ($children as $child) {
//        $hasChildren = !empty($child['children']);
//        $indent = $level * 3;
//        $html .= '<li>';
//        $html .= '<a href="' . base_url($child['full_url']) . '" class="flex items-center px-3 py-2.5 text-sm text-gray-600 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-all duration-200 group ml-' . $indent . '">';
//
//        if (!empty($child['icon'])) {
//            $html .= '<span class="material-symbols-rounded text-gray-400 group-hover:text-soko-500 transition-colors duration-200 mr-3" style="font-size: 18px;">' . $child['icon'] . '</span>';
//        } else {
//            // Add a subtle dot for items without icons
//            $html .= '<span class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover:bg-soko-500 transition-colors duration-200 mr-3"></span>';
//        }
//
//        $html .= '<span class="flex-1">' . esc($child['name']) . '</span>';
//
//        if ($hasChildren) {
//            $html .= '<span class="material-symbols-rounded text-gray-400 group-hover:text-gray-600 transition-all duration-200" style="font-size: 18px;">chevron_right</span>';
//        }
//        $html .= '</a>';
//
//        if ($hasChildren) {
//            $html .= buildChildMenu($child['children'], $level + 1);
//        }
//        $html .= '</li>';
//    }
//    $html .= '</ul>';
//    return $html;
//}

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

//function buildSidebarMenu($menuTree): string
//{
//    $html = '<nav class="p-4">';
//    foreach ($menuTree as $item) {
//        $hasChildren = !empty($item['children']);
//        $html .= '<div class="mb-4">';
////        $html .= '<a href="' . base_url($item['fulll_url]) . '" class="flex items-center p-2 text-gray-900 font-semibold rounded-lg hover:bg-gray-100">';
//        // before putting the link, check if uri is '#', if so, make it a button that does not navigate
//        if ($item['fulll_url] === '#') {
//            $html .= '<button type="button" class="flex items-center w-full p-2 text-soko-900 font-semibold rounded-lg hover:bg-soko-100">';
//        } else {
//            $html .= '<a href="' . base_url($item['fulll_url]) . '" class="flex items-center p-2 text-soko-900 font-semibold rounded-lg hover:bg-soko-100">';
//        }
//
//        if (!empty($item['icon'])) {
//            $html .= '<span class="material-icons mr-3 text-gray-500">' . $item['icon'] . '</span>';
//        }
//        $html .= '<span class="flex-1">' . esc($item['name']) . '</span>';
//        if ($hasChildren) {
//            $html .= '<span class="material-icons text-gray-400">expand_more</span>';
//        }
////        $html .= '</a>';
////        if ($hasChildren) {
////            $html .= buildChildMenu($item['children']);
////        }
////        $html .= '</div>';
//
//        if ($item['fulll_url] === '#') {
//            $html .= '</button>';
//        } else {
//            $html .= '</a>';
//        }
//        if ($hasChildren) {
//            $html .= buildChildMenu($item['children']);
//        }
//        $html .= '</div>';
//    }
//    $html .= '</nav>';
//    return $html;
//}
//

//function buildSidebarMenu($menuTree): string
//{
//    $html = '<div class="p-4"><div class="mb-6 px-3"><h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu</h2></div>';
//    $html .= '<nav class="space-y-1">';
//    foreach ($menuTree as $item) {
//        $hasChildren = !empty($item['children']);
//        $html .= '<div class="group">';
//
//        if ($item['fulll_url] === '#') {
//            $html .= '<button type="button" class="flex items-center w-full px-3 py-2.5 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 transition-colors duration-150">';
//        } else {
//            $html .= '<a href="' . base_url($item['fulll_url]) . '" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-900 rounded-lg hover:bg-gray-100 transition-colors duration-150">';
//        }
//
//        if (!empty($item['icon'])) {
//            $html .= '<span class="material-icons text-gray-600 group-hover:text-soko-600 transition-colors duration-150" style="font-size: 24px;">' . $item['icon'] . '</span>';
//            $html .= '<span class="flex-1 ml-3">' . esc($item['name']) . '</span>';
//        } else {
//            $html .= '<span class="flex-1">' . esc($item['name']) . '</span>';
//        }
//
//        if ($hasChildren) {
//            $html .= '<span class="material-icons text-gray-400 group-hover:text-gray-600 transition-all duration-200" style="font-size: 24px;">expand_more</span>';
//        }
//
//        if ($item['fulll_url] === '#') {
//            $html .= '</button>';
//        } else {
//            $html .= '</a>';
//        }
//
//        if ($hasChildren) {
//            $html .= buildChildMenu($item['children']);
//        }
//        $html .= '</div>';
//    }
//    $html .= '</nav></div>';
//    return $html;
//}

//function buildSidebarMenu($menuTree): string
//{
//    $html = '<div class="px-3 py-2"><h2 class="px-3 mb-2 text-xs font-bold text-gray-400 uppercase tracking-widest">Navigation</h2></div>';
//    $html .= '<nav class="px-2 space-y-1">';
//
//    foreach ($menuTree as $item) {
//        $hasChildren = !empty($item['children']);
//        $html .= '<div class="group/parent">';
//
//        $buttonClass = 'flex items-center w-full px-3 py-3 text-sm font-medium text-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-soko-50 hover:to-soko-100 hover:text-soko-700 transition-all duration-200';
//
//        if ($item['full_url'] === '#') {
//            $html .= '<button type="button" class="' . $buttonClass . '">';
//        } else {
//            $html .= '<a href="' . base_url($item['full_url']) . '" class="' . $buttonClass . '">';
//        }
//
//        if (!empty($item['icon'])) {
//            $html .= '<span class="material-symbols-rounded filled text-gray-500 group-hover/parent:text-soko-600 transition-colors duration-200 mr-3" style="font-size: 22px;">' . $item['icon'] . '</span>';
//        }
//
//        $html .= '<span class="flex-1 text-left">' . esc($item['name']) . '</span>';
//
//        if ($hasChildren) {
//            $html .= '<span class="material-symbols-rounded text-gray-400 group-hover/parent:text-soko-500 transition-all duration-200 submenu-icon" style="font-size: 20px;">expand_more</span>';
//        }
//
//        if ($item['full_url'] === '#') {
//            $html .= '</button>';
//        } else {
//            $html .= '</a>';
//        }
//
//        if ($hasChildren) {
//            $html .= '<div class="mt-1">' . buildChildMenu($item['children']) . '</div>';
//        }
//        $html .= '</div>';
//    }
//    $html .= '</nav>';
//    return $html;
//}

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
<!--    <link rel="stylesheet" href="--><?php //= base_url('fonts/css/icon-fonts/material-icons.css') ?><!--">-->
    <link rel="stylesheet" href="<?= base_url('css/tailwind.css') ?>?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= base_url('css/sweetalert2.min.css') ?>?v=<?= time() ?>">
    <link rel="icon" href="<?= base_url('img/favicon/favicon.ico') ?>" type="image/x-icon">

    <?= $this->renderSection('link') ?>

    <script type="module" src="<?= base_url('js/moment.js') ?>"></script>
    <script src="<?= base_url('js/lodash.min.js') ?>"></script>
    <?= script_tag(base_url('js/jquery.js')) ?>

    <title><?= $this->renderSection('title') ?></title>
</head>

<body class="bg-gradient-to-b from-white to-soko-200">
<!--<aside id="drawer-navigation"-->
<!--       class="sidebar bg-white w-72 fixed inline-block z-40 scrollbar-sidebar overflow-y-auto top-0 bottom-0 transition-transform xl:transition-none -translate-x-full xl:-translate-x-0"-->
<!--       tabindex="-1" aria-labelledby="drawer-navigation-label">-->
<!-- Sidebar content will be injected here -->
<!--</aside>-->

<!--<aside-->
<!--       class="sidebar bg-white w-72 fixed inline-block z-40 scrollbar-sidebar overflow-y-auto top-0 bottom-0 transition-transform xl:transition-none -translate-x-full xl:-translate-x-0 shadow-xl border-r border-gray-200"-->
<!--       tabindex="-1" aria-labelledby="drawer-navigation-label">-->
<!-- Logo Section -->
<!--    <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 z-10">-->
<!--        <img src="--><?php //= base_url('img/app-logo.png') ?><!--" alt="app-logo" class="h-8 w-auto"/>-->
<!--    </div>-->
<!--    <div id="drawer-navigation" class="mt-4">-->
<!-- Sidebar content will be injected here -->
<!-- Sidebar menu will be populated here -->
<!--    </div>-->
<!--</aside>-->

<!--<aside class="sidebar bg-white w-72 fixed inline-block z-40 scrollbar-sidebar overflow-y-auto top-0 bottom-0 transition-transform sm:transition-none -translate-x-full sm:translate-x-0 shadow-2xl border-r border-gray-200/80"-->
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

<!--<main class="flex flex-col w-full relative min-h-[100dvh] overflow-y-auto lg:ml-72">-->
<main class="flex flex-col w-full relative min-h-[100dvh] overflow-y-auto md:ml-72">
    <div
            class="side-overlay hidden fixed inset-0 bg-gray-600 backdrop-blur-[3px] bg-opacity-75 transition-all duration-100 z-30"></div>

    <?= view('components/flash-message') ?>

    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img src="<?= base_url('img/app-logo.png') ?>" alt="app-logo"
                 class="mx-auto h-10 w-auto"/>
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                <?= $this->renderSection('heading') ?>
            </h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</main>
<script type="module" src="<?= base_url('js/anime.esm.min.js') ?>"></script>
<script src="<?= base_url('js/flowbite.min.js') ?>"></script>

<?= $this->renderSection('bottom-scripts') ?>
</body>
<script>
    // Sidebar toggle functionality (we'll use jQuery for easier selections)
    // $(document).ready(function () {
    //     $('.sidebar-toggle').on('click', function () {
    //         $('.sidebar').toggleClass('-translate-x-full');
    //         $('.side-overlay').toggleClass('hidden');
    //     });
    //     $('.side-overlay').on('click', function () {
    //         $('.sidebar').addClass('-translate-x-full');
    //         $('.side-overlay').addClass('hidden');
    //     });
    //
    //     // Handle submenu toggles
    //     $('.sidebar button').on('click', function () {
    //         $(this).next('ul').slideToggle();
    //
    //         // Toggle the expand_more icon to expand_less
    //         const icon = $(this).find('.material-icons').last();
    //         if (icon.text() === 'expand_more') {
    //             icon.text('expand_less');
    //         } else {
    //             icon.text('expand_more');
    //         }
    //     })
    // });

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

        // Rest of your existing code...
    });
</script>
</html>
