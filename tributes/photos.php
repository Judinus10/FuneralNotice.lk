<?php
declare(strict_types=1);

$TRIBUTE_META = [
    'slug' => 'photos',
    'title' => 'tr:tribute_type_photos_title',
    'subtitle' => 'tr:tribute_type_photos_subtitle',
    'icon' => 'fa-image',
    'accent' => 'rose',

    'show_org' => false,
    'allow_photo_links' => true,

    'supports_delivery' => true,
    'force_delivery' => true,

    'message_label' => 'tr:tribute_type_photos_message_label',
    'message_placeholder' => 'tr:tribute_type_photos_message_placeholder',
    'helper_text' => 'tr:tribute_type_photos_helper_text',

    'phone_label' => 'tr:tribute_type_photos_phone_label',
    'phone_placeholder' => 'tr:tribute_phone_placeholder',
    'delivery_text' => 'tr:tribute_type_photos_delivery_text'
];

require __DIR__ . '/_page.php';