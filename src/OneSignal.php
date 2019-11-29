<?php


namespace AndreSeko\OneSignal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;

// TODO Adicionar um JSON encode antes de enviar.

/**
 * Class OneSignal
 * @author Andre Goncalves <andreseko@gmail.com>
 * @version 1.0.0
 * @package andreseko\OneSignal
 */
class OneSignal
{
    protected $appId;
    protected $restApiKey;
    protected $userAuthKey;
    protected $client;
    protected $headers;
    protected $additionalParams;

    const API_URL = "https://onesignal.com/api/v1";
    const ENDPOINT_NOTIFICATIONS = "/notifications";
    const ENDPOINT_PLAYERS = "/players";
    const ENDPOINT_APPS = "/apps";

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

    public function __construct($appId, $restApiKey, $userAuthKey)
    {
        $this->appId = $appId;
        $this->restApiKey = $restApiKey;
        $this->userAuthKey = $userAuthKey;
        $this->client = new Client([
            'handler' => $this->createGuzzleHandler(),
        ]);
        $this->headers = ['headers' => []];
        $this->additionalParams = [];
    }

    /**
     * setPlatform
     *
     * @param string $platform iOS | Android | Web
     */
    public function setPlatform($platform)
    {
        switch (strtoupper($platform)) {
            case 'IOS':
                $this->additionalParams['isIos'] = true;
                break;
            case 'ANDROID':
                $this->additionalParams['isAndroid'] = true;
                break;
            case 'WEB':
                $this->additionalParams['isAnyWeb'] = true;
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
     */
    public function schedule($date = '', $time = '', $gmt = 'GMT-0500')
    {
        if ($date === '') {
            $date = date('Y-m-d');
        }

        if ($time === '') {
            $date = date('H:i:s');
        }

        $this->additionalParams['send_after'] = $date . ' ' . $time . ' ' . $gmt;
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
     * @param string $segment
     */
    public function setSegments($segment = 'All') {
        $this->additionalParams['included_segments'][] = $segment;
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

    private function requiresUserAuth()
    {
        $this->headers['headers']['Authorization'] = 'Basic ' . $this->userAuthKey;
    }

    private function usesJSON()
    {
        $this->headers['headers']['Content-Type'] = 'application/json';
    }

    /**
     * Send a notification with custom parameters defined in
     * https://documentation.onesignal.com/reference#section-example-code-create-notification
     * @param array $parameters
     * @return mixed
     */
    public function sendNotification($parameters = []){
        $this->requiresAuth();
        $this->usesJSON();
        if (isset($parameters['api_key'])) {
            $this->headers['headers']['Authorization'] = 'Basic '.$parameters['api_key'];
        }
        // Make sure to use app_id
        if (!isset($parameters['app_id'])) {
            $parameters['app_id'] = $this->appId;
        }
        // Make sure to use included_segments
        if (empty($parameters['included_segments']) && empty($parameters['include_player_ids'])) {
            $parameters['included_segments'] = ['all'];
        }
        $parameters = array_merge($parameters, $this->additionalParams);
        $this->headers['body'] = json_encode($parameters);
        $this->headers['buttons'] = json_encode($parameters);
        $this->headers['verify'] = false;
        return $this->post(self::ENDPOINT_NOTIFICATIONS);
    }
}