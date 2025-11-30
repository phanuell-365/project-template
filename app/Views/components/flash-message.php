<?php

/**
 * The structure of the flash data stored in session
 * $flash_data = [
 *   'title'   => 'Flash Message Title',
 *   'message' => 'Your flash message here'
 *   'type'    => 'success' | 'error' | 'warning' | 'info',
 *   'popup_style' => 'banner' | 'modal', // banner is default
 *   'duration' => int (in seconds),
 *   'auto_dismiss' => bool
 * ];
 */

// When retrieving flash data, use:

$flash_data = session()->getFlashdata('flash_message');

if ($flash_data) :
    $type = $flash_data['type'] ?? 'info';
    $popup_style = $flash_data['popup_style'] ?? 'banner';
    $title = $flash_data['title'] ?? '';
    $message = $flash_data['message'] ?? '';
    $duration = $flash_data['duration'] ?? 5;
    $auto_dismiss = $flash_data['auto_dismiss'] ?? false;
    ?>

    <?php if ($popup_style === 'banner') : ?>
    <div id="flash-banner" tabindex="-1"
         class="fixed top-0 left-0 z-50 flex justify-between w-full p-4 border-b
             <?= $type === 'success' ? 'border-green-200 bg-green-50 text-green-500' : '' ?>
             <?= $type === 'error' ? 'border-red-200 bg-red-50 text-red-500' : '' ?>
             <?= $type === 'warning' ? 'border-yellow-200 bg-yellow-50 text-yellow-500' : '' ?>
             <?= $type === 'info' ? 'border-blue-200 bg-blue-50 text-blue-500' : '' ?>">
        <div class="flex items-center mx-auto">
            <p class="flex items-center text-sm font-normal">
             <span class="inline-flex p-1 mr-3 rounded-full
                 <?= $type === 'success' ? 'bg-green-200' : '' ?>
                 <?= $type === 'error' ? 'bg-red-200' : '' ?>
                 <?= $type === 'warning' ? 'bg-yellow-200' : '' ?>
                 <?= $type === 'info' ? 'bg-blue-200' : '' ?>">
                 <?php if ($type === 'success') : ?>
                     <svg aria-hidden="true" class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                         <path stroke-linecap="round"
                               stroke-linejoin="round"
                               stroke-width="2"
                               d="M5 13l4 4L19 7"></path>
                     </svg>
                 <?php elseif ($type === 'error') : ?>
                     <svg aria-hidden="true" class="w-4 h-4 text-red-500" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                         <path stroke-linecap="round"
                               stroke-linejoin="round"
                               stroke-width="2"
                               d="M6 18L18 6M6 6l12 12"></path>
                     </svg>
                 <?php elseif ($type === 'warning') : ?>
                     <svg aria-hidden="true" class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                         <path stroke-linecap="round"
                               stroke-linejoin="round"
                               stroke-width="2"
                               d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                     </svg>
                 <?php elseif ($type === 'info') : ?>
                     <svg aria-hidden="true" class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                         <path stroke-linecap="round"
                               stroke-linejoin="round"
                               stroke-width="2"
                               d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                     </svg>
                 <?php endif; ?>
                 <span class="sr-only">
                     <?= ucfirst($type) ?> Message
                 </span>
             </span>
                <span>
                 <?= esc($message) ?>
             </span>
            </p>
        </div>
        <div class="flex items-center">
            <button data-dismiss-target="#flash-banner" type="button"
                    class="flex-shrink-0 inline-flex justify-center items-center
                        <?= $type === 'success' ? 'text-green-400 hover:bg-green-200 hover:text-green-900' : '' ?>
                        <?= $type === 'error' ? 'text-red-400 hover:bg-red-200 hover:text-red-900' : '' ?>
                        <?= $type === 'warning' ? 'text-yellow-400 hover:bg-yellow-200 hover:text-yellow-900' : '' ?>
                        <?= $type === 'info' ? 'text-blue-400 hover:bg-blue-200 hover:text-blue-900' : '' ?>
                        rounded-lg text-sm p-1.5">
                <svg aria-hidden="true" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                     xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                          clip-rule="evenodd"></path>
                </svg>
                <span class="sr-only">Close banner</span>
            </button>
        </div>
    </div>
<?php endif; ?>

    <script>
        // Auto-dismiss the banner after 5 seconds
        // setTimeout(() => {
        //     const banner = document.getElementById('flash-banner');
        //     if (banner) {
        //         banner.remove();
        //     }
        // }, 5000);

        <?php if ($popup_style === 'banner' && $auto_dismiss) : ?>
        setTimeout(() => {
            const banner = document.getElementById('flash-banner');
            if (banner) {
                banner.remove();
            }
        }, <?= $duration * 1000 ?>);
        <?php endif; ?>

    </script>

    <script type="module">
        // We're going to use Swal.fire for modal popups
        <?php if ($popup_style === 'modal') : ?>

        //import {ShowNotification} from '<?php //= base_url('js/new.swal.js') ?>//';
        import Swal from "<?= base_url('js/sweetalert2.esm.min.js') ?>"

        async function ShowNotification(options) {
            return Swal.fire({
                ...options,
                customClass: {
                    popup: 'relative mx-auto flex flex-col w-11/12 sm:w-[480px] h-auto bg-soko-100 rounded-[28px]',
                    htmlContainer: '!flex flex-col gap-4 justify-start !px-8 !pt-8 !pb-0 !text-sm tracking-[0.25px] leading-5 !text-start',
                    title: 'text-title-lg text-gray-900 !text-start !px-8',
                    confirmButton: 'btn btn-primary relative flex flex-row items-center justify-center gap-x-2 py-2.5 px-6 rounded-[6.25rem] text-sm tracking-[.00714em] font-medium hover:bg-primary-dark focus:bg-primary-dark',
                    cancelButton: 'btn btn-secondary relative flex flex-row items-center justify-center gap-x-2 py-2.5 px-6 rounded-[6.25rem] text-sm tracking-[.00714em] font-medium hover:bg-secondary-dark focus:bg-secondary-dark',
                    actions: 'flex flex-row justify-end gap-2 px-8 py-8 w-full',
                    ...options.customClass,
                },
                buttonsStyling: false, // Disable default styling
                backdrop: 'backdrop-blur bg-opacity-90',
                // width
                allowOutsideClick: true,
                allowEscapeKey: false,
                focusConfirm: true,
                reverseButtons: true,
            });
        }

        // Swal.fire({
        ShowNotification({
            icon: '<?= $type ?>',
            title: '<?= esc($title) ?>',
            text: '<?= esc($message) ?>',
            confirmButtonText: 'OK'
        });
        <?php endif; ?>
    </script>

<?php endif; ?>


