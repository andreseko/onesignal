<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OneSignal
{
    private $fields = array();
    protected $appID;
    protected $restKey;
    protected $CI_instance;

    const URL_NOTIFICATION = "https://onesignal.com/api/v1/notifications";


    public function sendNotification()
    {
        if ($this->fields['isIos'] == true && $this->fields['isAndroid'] == true) {
            if (!array_key_exists('include_player_ids', $this->fields)) {
                $this->fields['included_segments'] = array('Apps');
            }
        } else if ($this->fields['isSafari'] == true && $this->fields['isChrome'] == true && $this->fields['isFirefox'] == true) {
            $this->fields['included_segments'] = array('Web browsers');
        } else if ($this->fields['segment'] != '') {
            $this->fields['included_segments'] = array($this->fields['segment']);
        } else {
            $this->fields['included_segments'] = array('All');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::URL_NOTIFICATION);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic ' . $this->restKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->fields));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0");

        $response = curl_exec($ch);

        curl_close($ch);

        return json_decode($response);
    }

    public function requestNotifications($offset = 0)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::URL_NOTIFICATION . '?app_id=' . $this->appID . '&limit=100&offset=' . $offset
        , CURLOPT_RETURNTRANSFER => true
        , CURLOPT_ENCODING => ""
        , CURLOPT_MAXREDIRS => 10
        , CURLOPT_TIMEOUT => 30
        , CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        , CURLOPT_CUSTOMREQUEST => "GET"
        , CURLOPT_HTTPHEADER => array("authorization: Basic " . $this->restKey),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function removeNotification($id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::URL_NOTIFICATION . '/' . $id . '/?app_id=' . $this->appID . ''
        , CURLOPT_RETURNTRANSFER => true
        , CURLOPT_ENCODING => ""
        , CURLOPT_MAXREDIRS => 10
        , CURLOPT_TIMEOUT => 30
        , CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
        , CURLOPT_CUSTOMREQUEST => "DELETE"
        , CURLOPT_HTTPHEADER => array("authorization: Basic " . $this->restKey),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return json_decode($response);
    }

    public function getDevices($offset = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/players?app_id=" . $this->appID . '&offset=' . $offset);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
            'Authorization: Basic ' . $this->restKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }

    public function getFields()
    {
        return $this->fields;
    }
}
