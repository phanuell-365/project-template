// import Swal from "sweetalert2"

async function ShowNotification(options) {
    return Swal.fire({
        buttonsStyling: false, // Disable default styling
        backdrop: 'backdrop-blur bg-opacity-90',
        // width
        allowOutsideClick: true,
        allowEscapeKey: false,
        focusConfirm: true,
        reverseButtons: true,
        ...options,
        customClass: {
            // popup: 'relative mx-auto flex flex-col w-11/12 sm:w-[480px] h-auto bg-soko-50 rounded-[28px]',
            popup: 'relative mx-auto flex flex-col w-11/12 sm:w-5/6 md:w-1/2 lg:w-2/5 xl:w-1/3 h-auto bg-white rounded-[28px] shadow-lg',
            htmlContainer: '!flex flex-col gap-4 justify-start !px-8 !pt-8 !pb-0 !text-sm tracking-[0.25px] leading-5 !text-start',
            title: 'text-title-lg text-gray-900 !text-start !px-8',
            confirmButton: 'btn btn-primary relative flex flex-row items-center justify-center gap-x-2 py-2.5 px-6 rounded-[6.25rem] text-sm tracking-[.00714em] font-medium hover:bg-primary-dark focus:bg-primary-dark',
            cancelButton: 'btn btn-secondary relative flex flex-row items-center justify-center gap-x-2 py-2.5 px-6 rounded-[6.25rem] text-sm tracking-[.00714em] font-medium hover:bg-secondary-dark focus:bg-secondary-dark',
            actions: 'flex flex-row justify-end gap-2 px-8 py-8 w-full',
            ...options.customClass,
        },
    });
}

// export { ShowNotification };
