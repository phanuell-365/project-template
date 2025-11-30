<?php

$this->extend('layouts/main');

?>

<?php $this->section('title'); ?>
Create User - <?= esc($org_name ?? 'Organization') ?>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<h1 class="text-2xl font-semibold text-gray-900">
    Welcome to <?= esc($org_name ?? 'Organization') ?>'s user creation.
</h1>

<?php $this->endSection(); ?>
