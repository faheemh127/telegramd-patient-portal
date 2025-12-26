<?php

defined('ABSPATH') || exit;

$page = $_GET['page'] ?? null;

switch ($page) {
    case 'care-team':
        include HLD_PLUGIN_PATH . 'templates/dashboard/care-team-chat.php';
        break;

    case 'payment-succeeded':
        include HLD_PLUGIN_PATH . 'templates/dashboard/payment-succeeded.php';
        break;

    case 'questionnaire-answered':
        include HLD_PLUGIN_PATH . 'templates/dashboard/questionnaire-answered.php';
        break;

    default:
        // default dashboard code is in this file
        include HLD_PLUGIN_PATH . 'templates/dashboard/default.php';
        break;
}
