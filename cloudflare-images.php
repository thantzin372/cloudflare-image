
<?php

return [
	'account_id'   => env('CLOUDFLARE_IMAGES_ACCOUNT_ID', null),
	'token'        => env('CLOUDFLARE_IMAGES_TOKEN', null),
	'key'          => env('CLOUDFLARE_IMAGES_KEY', null),
	'delivery_url' => env('CLOUDFLARE_IMAGES_DELIVERY_URL', null),
	'variant'      => env('CLOUDFLARE_IMAGES_DEFAULT_VARIATION', null)
];