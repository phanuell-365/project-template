<?php
?>

<!doctype html>
<html lang="en" class="font-montserrat" data-theme="soko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>

    <link rel="icon" href="<?= base_url('img/favicon/favicon.ico') ?>" type="image/x-icon">

    <link rel="stylesheet" href="<?= base_url('fonts/css/icon-fonts/material-icons.css') ?>">

    <link rel="stylesheet" href="<?= base_url('css/tailwind.css') ?>?v=<?= time()?>">

    <script type="module" src="<?= base_url('js/anime.esm.min.js') ?>"></script>
    <script src="<?= base_url('js/jquery.slim.js') ?>"></script>

    <title>
        Not Found
    </title>

</head>
<body>
<div class="h-screen mx-auto flex flex-col justify-evenly items-center">
    <section class="bg-white">
        <div class="py-8 px-4 mx-auto max-w-screen-md text-center lg:py-16 lg:px-12">
            <svg class="mx-auto mb-4 w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path fill="currentColor"
                      d="M331.8 224.1c28.29 0 54.88 10.99 74.86 30.97l19.59 19.59c40.01-17.74 71.25-53.3 81.62-96.65c5.725-23.92 5.34-47.08 .2148-68.4c-2.613-10.88-16.43-14.51-24.34-6.604l-68.9 68.9h-75.6V97.2l68.9-68.9c7.912-7.912 4.275-21.73-6.604-24.34c-21.32-5.125-44.48-5.51-68.4 .2148c-55.3 13.23-98.39 60.22-107.2 116.4C224.5 128.9 224.2 137 224.3 145l82.78 82.86C315.2 225.1 323.5 224.1 331.8 224.1zM384 278.6c-23.16-23.16-57.57-27.57-85.39-13.9L191.1 158L191.1 95.99l-127.1-95.99L0 63.1l96 127.1l62.04 .0077l106.7 106.6c-13.67 27.82-9.251 62.23 13.91 85.39l117 117.1c14.62 14.5 38.21 14.5 52.71-.0016l52.75-52.75c14.5-14.5 14.5-38.08-.0016-52.71L384 278.6zM227.9 307L168.7 247.9l-148.9 148.9c-26.37 26.37-26.37 69.08 0 95.45C32.96 505.4 50.21 512 67.5 512s34.54-6.592 47.72-19.78l119.1-119.1C225.5 352.3 222.6 329.4 227.9 307zM64 472c-13.25 0-24-10.75-24-24c0-13.26 10.75-24 24-24S88 434.7 88 448C88 461.3 77.25 472 64 472z"/>
            </svg>
            <h1 class="mb-4 text-4xl font-bold tracking-tight leading-none text-primary lg:mb-6 md:text-5xl xl:text-6xl">
                Not Found
            </h1>
            <h2 class="mb-4 text-2xl font-bold tracking-tight leading-none text-gray-900 lg:mb-6 md:text-3xl xl:text-4xl ">
                The requested resource could not be found.
            </h2>
            <p class="font-light text-gray-500 md:text-lg xl:text-xl">
                Please check the URL or return to the homepage.
            </p>

            <!-- For easier navigation, add a button to go back to the homepage -->
            <div class="mt-8">
                <a href="<?= url_to('dashboard', $org_slug) ?>"
                   class="inline-flex items-center px-5 py-3 text-sm font-medium text-center text-white bg-primary rounded-lg hover:bg-primary focus:ring-4 focus:outline-none focus:ring-primary-dark">
                    Go to Homepage
                    <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                              clip-rule="evenodd"></path>
                    </svg>
                </a>
        </div>
    </section>
</div>

<script src="<?= base_url('js/flowbite.min.js') ?>"></script>

<script type="module">
    // import anime from 'js/anime.esm.min.js';
    //import {animate } from '<?php //= base_url('js/anime.esm.min.js') ?>//';


    // $(document).ready(function () {
        // lets add animations to the h1, h2 the icon and the p
        // const h1 = $('h1');
        // const h2 = $('h2');
        // const icon = $('svg');
        // const p = $('p');
        //
        // // lets add a delay to the animations
        // const delay = 100;
        //
        // // lets add a duration to the animations
        // const duration = 500;
        //
        // // lets add a easing to the animations
        // const easing = 'easeInOutQuad';
        //
        // // we will use anime.js to animate the elements
        // // lets animate the h1
        // animate({
        //     targets: h1[0],
        //     opacity: [0, 1],
        //     translateY: [-20, 0],
        //     delay: delay,
        //     duration: duration,
        //     easing: easing
        // });
        //
        // // lets animate the h2
        // anime({
        //     targets: h2[0],
        //     opacity: [0, 1],
        //     translateY: [-20, 0],
        //     delay: delay * 2,
        //     duration: duration,
        //     easing: easing
        // });
        //
        // // lets animate the icon
        // anime({
        //     targets: icon[0],
        //     opacity: [0, 1],
        //     translateY: [-20, 0],
        //     delay: delay * 3,
        //     duration: duration,
        //     easing: easing
        // });
        //
        // // lets animate the p
        // anime({
        //     targets: p[0],
        //     opacity: [0, 1],
        //     translateY: [-20, 0],
        //     delay: delay * 4,
        //     duration: duration,
        //     easing: easing
        // });
    // });
</script>

<script type="module">
    // Correct import for anime.js v4
    // import { animate, stagger } from 'https://ptemp.kidakwa.com/js/anime.esm.min.js';
    import {animate, stagger } from '<?= base_url('js/anime.esm.min.js') ?>';

    // Define your animation settings
    const delay = 200; // adjust as needed
    const duration = 800;
    const ease = 'outQuad'; // Note: v4 uses 'ease' not 'easing'

    // Get your elements (adjust selectors as needed)
    const h1 = document.querySelectorAll('h1');
    const h2 = document.querySelectorAll('h2');
    const icon = document.querySelectorAll('.icon'); // adjust selector
    const p = document.querySelectorAll('p');

    // V4 syntax: animate(targets, parameters)
    animate(h1[0], {
        opacity: [0, 1],
        translateY: [-20, 0],
        delay: delay,
        duration: duration,
        ease: ease
    });

    animate(h2[0], {
        opacity: [0, 1],
        translateY: [-20, 0],
        delay: delay * 2,
        duration: duration,
        ease: ease
    });

    animate(icon[0], {
        opacity: [0, 1],
        translateY: [-20, 0],
        delay: delay * 3,
        duration: duration,
        ease: ease
    });

    animate(p[0], {
        opacity: [0, 1],
        translateY: [-20, 0],
        delay: delay * 4,
        duration: duration,
        ease: ease
    });

    // Or use stagger for cleaner code:
    // const elements = [h1[0], h2[0], icon[0], p[0]];
    // animate(elements, {
    //     opacity: [0, 1],
    //     translateY: [-20, 0],
    //     delay: stagger(delay),
    //     duration: duration,
    //     ease: ease
    // });
</script>
</body>
</html>
