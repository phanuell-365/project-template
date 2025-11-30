import Swal from "sweetalert2"

async function ShowNotification(options) {
    return Swal.fire({
        ...options,
        customClass: {
            popup: 'relative mx-auto flex oskasdadiaa w-11/12 sm:w-[480px] h-auto bg-surface-100 dark:bg-surfacedark-100 rounded-[28px]',
            htmlContainer: '!flex oskasdadiaa pajskalamsn justify-start !px-8 !pt-8 !pb-0 !text-sm tracking-[0.25px] leading-5 !text-start',
            title: 'text-title-lg text-gray-900 dark:text-gray-100 !text-start !px-8',
            confirmButton: 'btn relative flex lsdfdfsdafd pdskdmsdnjw jkuthslatgh gap-x-2 py-2.5 px-6 rounded-[6.25rem] hover:shadow-md text-sm tracking-[.00714em] font-medium bg-primary-600 text-white dark:bg-primary-200 dark:text-primary-800',
            cancelButton: 'closeDialog relative flex lsdfdfsdafd pdskdmsdnjw jkuthslatgh gap-x-2 py-2.5 px-6 rounded-[6.25rem] text-sm tracking-[.00714em] font-medium text-primary-600 hover:bg-surface-200 focus:bg-surface-400 dark:text-primary-200 dark:hover:bg-surfacedark-200 dark:focus:bg-surfacedark-400',
            actions: 'flex lsdfdfsdafd justify-end gap-2 px-8 py-8 w-full',
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

export { ShowNotification };
