<?php
declare(strict_types=1);

$TRIBUTE_META = [
    'slug' => 'message',
    'title' => 'tr:tribute_type_message_title',
    'subtitle' => 'tr:tribute_type_message_subtitle',
    'icon' => 'fa-envelope',
    'accent' => 'red',

    'show_org' => false,
    'allow_photo_links' => false,

    'supports_delivery' => true,
    'force_delivery' => true,

    'message_label' => 'tr:tribute_type_message_message_label',
    'message_placeholder' => 'tr:tribute_type_message_message_placeholder',
    'helper_text' => 'tr:tribute_type_message_helper_text',

    'phone_label' => 'tr:tribute_phone_label',
    'phone_placeholder' => 'tr:tribute_phone_placeholder',
    'delivery_text' => 'tr:tribute_type_message_delivery_text'
];

require __DIR__ . '/_page.php';