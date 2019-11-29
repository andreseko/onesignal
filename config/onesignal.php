<?php
return array(
    /*
	|--------------------------------------------------------------------------
	| One Signal App Id
	|--------------------------------------------------------------------------
	|
	|
	*/
    'app_id' => env('ONESIGNAL_APP_ID', 'YOUR_API_KEY'),
    /*
	|--------------------------------------------------------------------------
	| Rest API Key
	|--------------------------------------------------------------------------
	|
    |
	|
	*/
    'rest_api_key' => env('ONESIGNAL_REST_API_ID', 'YOUR_REST_API_KEY'),
    'user_auth_key' => env('ONESIGNAL_AUTH_KEY', 'YOUR_AUTH_USER_KEY'),
    /*
	|--------------------------------------------------------------------------
	| General
	|--------------------------------------------------------------------------
	|
    |
	|
	*/
    'title' => env('ONESIGNAL_DEFAULT_TITLE', null),
    'subtitle' => env('ONESIGNAL_DEFAULT_SUBTITLE', null),
    /*
	|--------------------------------------------------------------------------
	| iOS Configuration
	|--------------------------------------------------------------------------
	|
    |
	|
	*/
    'ios_sound' => env('ONESIGNAL_IOS_SOUND', null),
    'ios_badgeType' => env('ONESIGNAL_IOS_BADGE_TYPE', 'Increase'),
    'ios_badgeCount' => env('ONESIGNAL_IOS_BADGE_COUNT', 1),
    /*
	|--------------------------------------------------------------------------
	| Android configuration
	|--------------------------------------------------------------------------
	|
    |
	|
	*/
    'android_sound' => env('ONESIGNAL_ANDROID_SOUND', null),
    'small_icon' => env('ONESIGNAL_ANDROID_SMALL_ICON', null),
    'large_icon' => env('ONESIGNAL_ANDROID_LARGE_ICON', null),
    'android_led_color' => env('ONESIGNAL_ANDROID_LED_COLOR', null),
    'android_accent_color' => env('ONESIGNAL_ANDROID_ACCENT_COLOR', null),
    'android_visibility' => env('ONESIGNAL_ANDROID_VISIBILITY', 1),
    'android_background_layout' => [
        'image' => env('ONESIGNAL_ANDROID_BACKGROUND_IMAGE', null)
        , 'headings_color' => env('ONESIGNAL_ANDROID_HEADINGS_COLOR', null)
        , 'contents_color' => env('ONESIGNAL_ANDROID_CONTENTS_COLOR', null)
    ],
);