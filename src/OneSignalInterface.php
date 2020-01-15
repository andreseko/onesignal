<?php


namespace AndreSeko\OneSignal;

use Illuminate\Support\Carbon;

interface OneSignalInterface
{
    /**
     * setPlatform
     *
     * By default, OneSignal will send to every platform (each of these is true).
     * To only send to specific platforms, you may pass in true on one or more of these parameters corresponding to the platform you wish to send to. If you do so, all other platforms will be set to false and will not be delivered to.
     * These parameters will be ignored if sending to devices directly with include_player_ids or include_external_user_ids
     *
     * @param string $platform iOS | Android | Web
     * @param bool $enabled
     */
    public function setPlatform($platform, $enabled = true);

    /**
     * setTitle
     *
     * @param string $title
     * @param string $language
     */
    public function setTitle($title, $language = 'en');
    /**
     * setSubTitle
     *
     * @param string $subtitle
     * @param string $language
     */
    public function setSubTitle($subtitle, $language = 'en');

    /**
     * setMessage
     *
     * @param string $message
     * @param string $language
     */
    public function setMessage($message, $language = 'en');

    /**
     * addUrl
     *
     * @param string $url
     */
    public function addUrl($url = '');

    /**
     * scheduleTo
     *
     * Schedule notification for future delivery. API defaults to UTC -1100
     * ISO8601 Ex: 2015-09-24 14:00:00 GMT-0700 or 2019-02-01T03:45:27+0000
     *
     * @param Carbon|\Carbon\Carbon $date
     */
    public function scheduleTo(Carbon $date);

    /**
     * setDelayOption
     *
     * Possible values are: last-active - Deliver at the same time of day as each user last used your app
     * or timezone - Deliver at a specific time-of-day in each users own timezone.
     * If send_after is used, this takes effect after the send_after time has elapsed.
     *
     * @param string $option
     */
    public function setDelayOption($option = 'last-active');

    /**
     * addAttachments
     *
     * These are additional content attached to push notifications, primarily images.
     *
     * @param string $file
     * @param string $id
     */
    public function addAttachments($file, $id = 'id1');

    /**
     * addButtons
     *
     * Buttons to add to the notification. Icon only works for Android.
     *
     * @param string $id
     * @param string $text
     * @param string $icon
     */
    public function addButtons($id = '', $text = '', $icon = '');

    /**
     * setFilters
     *
     * @link https://documentation.onesignal.com/reference#section-send-to-users-based-on-filters
     *
     * @param string $field
     * @param string $relation relation = ">", "<", "=", "!=", "exists", "not_exists"
     * @param mixed $value
     * @param string|null $key
     * @param string|null $operator
     */
    public function setFilters(string $field, string $relation, $value, string $key = null, string $operator = null);

    /**
     * setLocation
     *
     * @link https://documentation.onesignal.com/reference#section-send-to-users-based-on-filters
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $radius in meters default is 50000 meters
     * @return void
     */
    public function setLocation(float $latitude, float $longitude, int $radius = 50);

    /**
     * configureIos
     *
     * @param string $sound
     * @param string $badgeType Describes whether to set or increase/decrease your app's iOS badge count by the ios_badgeCount specified count. Can specify None, SetTo, or Increase.
     * @param int $badgeCount Used with ios_badgeType, describes the value to set or amount to increase/decrease your app's iOS badge count by.
     *
     */
    public function configureIos($sound = null, $badgeType = 'Increase', $badgeCount = 1);

    /**
     * configureAndroid
     *
     * @param string $sound
     * @param string $smallIcon
     * @param string $largeicon
     * @param string $ledColor
     * @param string $accentColor
     * @param int $visibility Sets the lock screen visibility for apps targeting Android API level 21+ running on Android 5.0+ devices. 1 = Public | 0 Private | -1 Secret
     * @param array $backgroundLayout Allowing setting a background image for the notification. This is a JSON object containing the following keys. See our Background Image documentation for image sizes. image - Asset file, android resource name, or URL to remote image. headings_color - Title text color ARGB Hex format. Example(Blue): "FF0000FF". contents_color - Body text color ARGB Hex format. Example(Red): "FFFF0000"
     */
    public function configureAndroid($sound = null, $smallIcon = null, $largeicon = null, $ledColor = null, $accentColor = null, $visibility = 1, $backgroundLayout = ['image' => null, 'headings_color' => null, 'contents_color' => null]);

    /**
     * setUsers
     *
     * @param string $playerID
     */
    public function setUsers($playerID);

    /**
     * setData
     *
     * @param array $data
     */
    public function setData($data = []);

    /**
     * setSegments
     *
     * @param string $segment "All" "Active Users", "Inactive Users"
     */
    public function setSegments($segment = 'All');

    /**
     * excludeSegments
     *
     * @param string $segment "Active Users", "Inactive Users"
     */
    public function excludeSegments($segment);

    /**
     * Send a notification with custom parameters defined in
     * https://documentation.onesignal.com/reference#section-example-code-create-notification
     * @return mixed
     */
    public function sendNotification();

    /**
     * getNotification
     *
     * @param $notificationId
     * @return mixed
     */
    public function getNotification($notificationId);

    /**
     * getNotifications
     *
     * @param int $limit limit the notifications for page
     * @param int $offset number of the page
     * @param int $kind 0 for Dashboard, 1 for API, 3 for Automated default not set
     * @return mixed
     */
    public function getNotifications($limit = 50, $offset = 0, $kind = -1);

    /**
     * deleteNotification
     *
     * @param $notificationId
     * @return bool
     */
    public function deleteNotification($notificationId);

    /**
     * getPlayers
     *
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function getPlayers($limit = 300, $offset = 0);
}