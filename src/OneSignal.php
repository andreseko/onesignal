<?php


namespace AndreSeko\OneSignal;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Support\Carbon;
use Psr\Http\Message\ResponseInterface;

/**
 * Class OneSignal
 *
 * @author Andre Goncalves <andreseko@gmail.com>
 * @version 1.0.5
 * @package andreseko\OneSignal
 * @link https://documentation.onesignal.com/reference#create-notification to refer all customizable parameters.
 */
class OneSignal
{
    /**
     * @var string
     */
    protected $appId;
    /**
     * @var string
     */
    protected $restApiKey;
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $additionalParams;

    /**
     * Endpoints
     */
    const API_URL = "https://onesignal.com/api/v1";
    const ENDPOINT_NOTIFICATIONS = "/notifications";
    const ENDPOINT_PLAYERS = "/players";

    /**
     * Platforms
     */
    const IOS = 'iOS';
    const ANDROID = 'Android';
    const WEB = 'Web';

    /**
     * @var bool
     */
    public $requestAsync = false;
    /**
     * @var int
     */
    public $maxRetries = 2;
    /**
     * @var int
     */
    public $retryDelay = 500;
    /**
     * @var Callable
     */
    private $requestCallback;

    /**
     * OneSignal constructor.
     *
     * @param string $appId
     * @param string $restApiKey
     */
    public function __construct($appId, $restApiKey)
    {
        $this->appId = $appId;
        $this->restApiKey = $restApiKey;
        $this->client = new Client([
            'handler' => $this->createGuzzleHandler(),
        ]);
        $this->headers = ['headers' => []];
        $this->additionalParams = [];
    }

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
    public function setPlatform($platform, $enabled = true)
    {
        switch ($platform) {
            case self::IOS:
                $this->additionalParams['isIos'] = $enabled;
                break;
            case self::ANDROID:
                $this->additionalParams['isAndroid'] = $enabled;
                break;
            case self::WEB:
                $this->additionalParams['isAnyWeb'] = $enabled;
                break;
        }
    }

    /**
     * setTitle
     *
     * @param string $title
     * @param string $language
     */
    public function setTitle($title, $language = 'en')
    {
        $this->additionalParams['headings'][$language] = $title;
    }

    /**
     * setSubTitle
     *
     * @param string $subtitle
     * @param string $language
     */
    public function setSubTitle($subtitle, $language = 'en')
    {
        $this->additionalParams['subtitle'][$language] = $subtitle;
    }

    /**
     * setMessage
     *
     * @param string $message
     * @param string $language
     */
    public function setMessage($message, $language = 'en')
    {
        $this->additionalParams['contents'][$language] = $message;
    }

    /**
     * addUrl
     *
     * @param string $url
     */
    public function addUrl($url = '')
    {
        $this->additionalParams['url'] = $url;
    }

    /**
     * schedule
     *
     * Schedule notification for future delivery. API defaults to UTC -1100
     * Ex: 2015-09-24 14:00:00 GMT-0700
     *
     * @param string $date
     * @param string $time
     * @param string $gmt
     *
     * @deprecated deprecated since version 1.0.5 use method scheduleFor instead
     */
    public function schedule($date = '', $time = '', $gmt = 'GMT-0500')
    {
        trigger_error('Use method scheduleFor instead.', E_USER_DEPRECATED);

        if ($date === '') {
            $date = date('Y-m-d');
        }

        if ($time === '') {
            $date = date('H:i:s');
        }

        $this->additionalParams['send_after'] = $date . ' ' . $time . ' ' . $gmt;
    }

    /**
     * scheduleFor
     *
     * Schedule notification for future delivery. API defaults to UTC -1100
     * ISO8601 Ex: 2015-09-24 14:00:00 GMT-0700 or 2019-02-01T03:45:27+0000
     *
     * @param Carbon|\Carbon\Carbon $date
     */
    public function scheduleFor(Carbon $date)
    {
        $this->additionalParams['send_after'] = $date->format(DateTime::ISO8601);
    }

    /**
     * setDelayOption
     *
     * Possible values are: last-active - Deliver at the same time of day as each user last used your app
     * or timezone - Deliver at a specific time-of-day in each users own timezone.
     * If send_after is used, this takes effect after the send_after time has elapsed.
     *
     * @param string $option
     */
    public function setDelayOption($option = 'last-active')
    {
        $this->additionalParams['delayed_option'] = $option;
    }

    /**
     * addAttachments
     *
     * These are additional content attached to push notifications, primarily images.
     *
     * @param string $file
     * @param string $id
     */
    public function addAttachments($file, $id = 'id1')
    {
        $this->additionalParams['ios_attachments'] = array($id => $file);
        $this->additionalParams['big_picture'] = $file;
        $this->additionalParams['adm_big_picture'] = $file;
        $this->additionalParams['chrome_big_picture'] = $file;
    }

    /**
     * addButtons
     *
     * Buttons to add to the notification. Icon only works for Android.
     *
     * @param string $id
     * @param string $text
     * @param string $icon
     */
    public function addButtons($id = '', $text = '', $icon = '')
    {
        $this->additionalParams['buttons'][] = ['id' => $id, 'text' => $text, 'icon' => $icon];
    }

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
    public function setFilters(string $field, string $relation, $value, string $key = null, string $operator = null) {
        $parameter = ['field' => $field, 'relation' => $relation, 'value' => $value];

        if (!is_null($key)) {
            $parameter['key'] = $key;
        }

        if (!is_null($operator)) {
            $parameter['operator'] = $operator;
        }

        $this->additionalParams['filters'][] = $parameter;
    }

    /**
     * configureIos
     *
     * @param string $sound
     * @param string $badgeType Describes whether to set or increase/decrease your app's iOS badge count by the ios_badgeCount specified count. Can specify None, SetTo, or Increase.
     * @param int $badgeCount Used with ios_badgeType, describes the value to set or amount to increase/decrease your app's iOS badge count by.
     *
     */
    public function configureIos($sound = null, $badgeType = 'Increase', $badgeCount = 1)
    {
        if (!is_null($sound)) {
            $this->additionalParams['ios_sound'] = $sound;
        }

        if (!is_null($badgeType)) {
            $this->additionalParams['ios_badgeType'] = $badgeType;
        }

        if (!is_null($badgeCount)) {
            $this->additionalParams['ios_badgeCount'] = $badgeCount;
        }
    }

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
    public function configureAndroid($sound = null, $smallIcon = null, $largeicon = null, $ledColor = null, $accentColor = null, $visibility = 1, $backgroundLayout = ['image' => null, 'headings_color' => null, 'contents_color' => null])
    {
        if (!is_null($sound)) {
            $sound = explode('.', $sound);
            $this->additionalParams['android_sound'] = $sound[0];
        }

        if (!is_null($smallIcon)) {
            $this->additionalParams['small_icon'] = $smallIcon;
        }

        if (!is_null($largeicon)) {
            $this->additionalParams['large_icon'] = $largeicon;
        }

        if (!is_null($ledColor)) {
            $this->additionalParams['android_led_color'] = $ledColor;
        }

        if (!is_null($accentColor)) {
            $this->additionalParams['android_accent_color'] = $accentColor;
        }

        $this->additionalParams['android_visibility'] = $visibility;

        if (!is_null($backgroundLayout['image']) && !is_null($backgroundLayout['headings_color']) && !is_null($backgroundLayout['contents_color'])) {
            $this->additionalParams['android_background_layout'] = $backgroundLayout;
        }
    }

    /**
     * setUsers
     *
     * @param string $playerID
     */
    public function setUsers($playerID)
    {
        $this->additionalParams['include_player_ids'][] = $playerID;
    }

    /**
     * setData
     *
     * @param array $data
     */
    public function setData($data = [])
    {
        if (is_array($data) && count($data) > 0) {
            $this->additionalParams['data'] = $data;
        }
    }

    /**
     * setSegments
     * @param string $segment "All" "Active Users", "Inactive Users"
     */
    public function setSegments($segment = 'All')
    {
        $this->additionalParams['included_segments'][] = $segment;
    }

    public function excludeSegments($segment)
    {
        $this->additionalParams['excluded_segments'][] = $segment;
    }

    /**
     * createGuzzleHandler
     *
     * @return mixed
     */
    private function createGuzzleHandler()
    {
        return tap(HandlerStack::create(new CurlHandler()), function (HandlerStack $handlerStack) {
            $handlerStack->push(Middleware::retry(function ($retries, Psr7Request $request, Psr7Response $response = null, RequestException $exception = null) {
                if ($retries >= $this->maxRetries) {
                    return false;
                }
                if ($exception instanceof ConnectException) {
                    return true;
                }
                if ($response && $response->getStatusCode() >= 500) {
                    return true;
                }
                return false;
            }), $this->retryDelay);
        });
    }

    private function requiresAuth()
    {
        $this->headers['headers']['Authorization'] = 'Basic ' . $this->restApiKey;
    }

    private function usesJSON()
    {
        $this->headers['headers']['Content-Type'] = 'application/json';
    }

    /**
     * Send a notification with custom parameters defined in
     * https://documentation.onesignal.com/reference#section-example-code-create-notification
     * @return mixed
     */
    public function sendNotification()
    {
        $this->requiresAuth();
        $this->usesJSON();

        // Make sure to use app_id
        $this->additionalParams['app_id'] = $this->appId;

        // Make sure to use included_segments
        if (empty($this->additionalParams['included_segments']) && empty($this->additionalParams['include_player_ids'])) {
            $this->additionalParams['included_segments'] = ['All'];
        }

        $this->headers['body'] = json_encode($this->additionalParams);
        $this->headers['buttons'] = json_encode($this->additionalParams);
        $this->headers['verify'] = false;

        return $this->post(self::ENDPOINT_NOTIFICATIONS);
    }

    /**
     * getNotification
     *
     * @param $notificationId
     * @return mixed
     */
    public function getNotification($notificationId)
    {
        $this->requiresAuth();
        $this->usesJSON();
        $response = $this->get(self::ENDPOINT_NOTIFICATIONS . '/' . $notificationId . '?app_id=' . $this->appId);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * getNotifications
     *
     * @param int $limit limit the notifications for page
     * @param int $offset number of the page
     * @param int $kind 0 for Dashboard, 1 for API, 3 for Automated default not set
     * @return mixed
     */
    public function getNotifications($limit = 50, $offset = 0, $kind = -1)
    {
        $this->requiresAuth();
        $this->usesJSON();
        $endpoint = self::ENDPOINT_NOTIFICATIONS;
        $endpoint .= '?app_id=' . $this->appId;
        if (!is_null($limit)) {
            $endpoint .= "&limit=" . $limit;
        }
        if (!is_null($offset)) {
            $endpoint .= "&offset=" . $offset;
        }

        if ($kind > -1) {
            $endpoint .= "&kind=" . $kind;
        }

        $response = $this->get($endpoint);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * deleteNotification
     *
     * @param $notificationId
     * @return bool
     */
    public function deleteNotification($notificationId)
    {
        $this->requiresAuth();
        $notificationCancelNode = "/$notificationId?app_id=$this->appId";

        try {
            $response = $this->delete(self::ENDPOINT_NOTIFICATIONS . $notificationCancelNode);
            if ($response->getStatusCode() === 200) {
                return json_decode($response->getBody()->getContents())->success;
            }
        } catch (ClientException $e) {
            echo $e->getCode() . ' - ' . $e->getMessage();
        }

        return false;
    }

    /**
     * getPlayers
     *
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function getPlayers($limit = 300, $offset = 0)
    {
        $this->requiresAuth();
        $this->usesJSON();

        $endpoint = self::ENDPOINT_PLAYERS;
        $endpoint .= '?app_id=' . $this->appId;
        if (!is_null($limit)) {
            $endpoint .= "&limit=" . $limit;
        }
        if (!is_null($offset)) {
            $endpoint .= "&offset=" . $offset;
        }

        $response = $this->get($endpoint);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * post
     *
     * @param string $endPoint
     * @return PromiseInterface|ResponseInterface
     */
    public function post($endPoint)
    {
        if ($this->requestAsync === true) {
            $promise = $this->client->postAsync(self::API_URL . $endPoint, $this->headers);
            return (is_callable($this->requestCallback) ? $promise->then($this->requestCallback) : $promise);
        }
        return $this->client->post(self::API_URL . $endPoint, $this->headers);
    }

    /**
     * put
     *
     * @param string $endPoint
     * @return PromiseInterface|ResponseInterface
     */
    public function put($endPoint)
    {
        if ($this->requestAsync === true) {
            $promise = $this->client->putAsync(self::API_URL . $endPoint, $this->headers);
            return (is_callable($this->requestCallback) ? $promise->then($this->requestCallback) : $promise);
        }
        return $this->client->put(self::API_URL . $endPoint, $this->headers);
    }

    /**
     * get
     *
     * @param string $endPoint
     * @return ResponseInterface
     */
    public function get($endPoint)
    {
        return $this->client->get(self::API_URL . $endPoint, $this->headers);
    }

    /**
     * delete
     *
     * @param string $endPoint
     * @return PromiseInterface|ResponseInterface
     */
    public function delete($endPoint)
    {
        if ($this->requestAsync === true) {
            $promise = $this->client->deleteAsync(self::API_URL . $endPoint, $this->headers);
            return (is_callable($this->requestCallback) ? $promise->then($this->requestCallback) : $promise);
        }
        return $this->client->delete(self::API_URL . $endPoint, $this->headers);
    }
}