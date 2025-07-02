<?php
/**
 * Returns the TelegraMD patient ID for the current logged-in user.
 *
 * @since 1.0.0
 *
 * @return string|null Patient ID if found, otherwise null.
 *
 * @example
 * $patient_id = get_telegra_patient_id_for_current_user();
 * if ($patient_id) {
 *     echo "Patient ID: $patient_id";
 * }
 */
function get_telegra_patient_id_for_current_user()
{
    if (!is_user_logged_in()) {
        return null;
    }

    $user_id = get_current_user_id();
    $meta_key = 'hld_patient_' . $user_id . '_telegra_id';

    $patient_id = get_user_meta($user_id, $meta_key, true);

    return !empty($patient_id) ? $patient_id : null;
}
