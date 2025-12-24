<?php

/**
 * @var $org_slug string
 */

$settings_service = service('settings_service');
$site_logo = $settings_service->getSiteLogoUrl($org_slug) ?? base_url('img/app-logo.png');
?>

<!doctype html>
<html lang="en" class="font-montserrat bg-light h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?= csrf_meta() ?>
    <?= $this->renderSection('meta') ?>
    <link rel="stylesheet" href="<?= base_url('fonts/css/icon-fonts/material-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/tailwind.css') ?>?v=<?= time()?>">
    <link rel="stylesheet" href="<?= base_url('css/sweetalert2.min.css') ?>?v=<?= time()?>">
    <link rel="icon" href="<?= base_url('img/favicon/favicon.ico') ?>" type="image/x-icon">
    <?= script_tag(base_url('js/jquery.slim.js')) ?>
<!--    --><?php //= script_tag(base_url('js/new.swal.js')) ?>
    <?= $this->renderSection('link') ?>
    <title><?= $this->renderSection('title') ?></title>
</head>
<body class="h-[100dvh]">

<?= view('components/flash-message') ?>

<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img src="<?= $site_logo ?>" alt="app-logo"
             class="mx-auto h-10 w-auto"/>
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
            <?= $this->renderSection('heading') ?>
        </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <?= $this->renderSection('content') ?>
    </div>
</div>
<script type="module" src="<?= base_url('js/anime.esm.min.js') ?>"></script>
<script src="<?= base_url('js/flowbite.min.js') ?>"></script>

<?= $this->renderSection('bottom-scripts') ?>
</body>
</html>
