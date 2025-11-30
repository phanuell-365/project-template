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

/**
 * This is a helper function to display flash messages in views.
 * It sets flash data in the session which can be retrieved and displayed in the view.
 * @param string $title The title of the flash message.
 * @param string $message The content of the flash message.
 * @param string $type The type of the message: 'success', 'error', 'warning', 'info'. Default is 'info'.
 * @param string $popup_style The style of the popup: 'banner' or 'modal'. Default is 'banner'.
 * @param boolean $auto_dismiss Whether the flash message should auto-dismiss after the duration. Default is true.
 * @param int $duration The duration in seconds for which the flash message should be displayed. Default is 5 seconds.
 */
function flash_message(string $title = '', string $message = '', string $type = 'info', string $popup_style = 'banner', bool $auto_dismiss = false, int $duration = 5): void
{
    $flash_data = [
        'type'        => $type,
        'popup_style' => $popup_style,
        'title'       => $title,
        'message'     => $message,
        'duration'    => $duration,
        'auto_dismiss'=> $auto_dismiss,
    ];

    session()->setFlashdata('flash_message', $flash_data);
}
