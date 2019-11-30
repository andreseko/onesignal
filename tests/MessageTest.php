<?php
//declare(strict_types=1);

namespace AndreSeko\OneSignalTests;

use AndreSeko\OneSignal\OneSignal;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use stdClass;

class MessageTest extends TestCase
{
    public function testSendMessageForIosDevices()
    {
        $dotenv = Dotenv::create(__DIR__ . '/../');
        $dotenv->load();

        $oneSignal = new OneSignal(env('ONESIGNAL_APP_ID'), env('ONESIGNAL_REST_API_ID'));
        $oneSignal->setPlatform(OneSignal::IOS);
        $oneSignal->setPlatform(OneSignal::ANDROID, false);
        $oneSignal->setPlatform(OneSignal::WEB, false);
        $oneSignal->setTitle(env('ONESIGNAL_DEFAULT_TITLE'));
        $oneSignal->setSubTitle(env('ONESIGNAL_DEFAULT_SUBTITLE'));
        $oneSignal->configureIos(env('ONESIGNAL_IOS_SOUND'));
        $oneSignal->setMessage('Test from API');

        $response = $oneSignal->sendNotification();

        $this->assertIsInt($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSendMessageForAndroidDevices()
    {
        $dotenv = Dotenv::create(__DIR__ . '/../');
        $dotenv->load();

        $oneSignal = new OneSignal(env('ONESIGNAL_APP_ID'), env('ONESIGNAL_REST_API_ID'));
        $oneSignal->setPlatform(OneSignal::ANDROID);
        $oneSignal->setPlatform(OneSignal::IOS, false);
        $oneSignal->setPlatform(OneSignal::WEB, false);
        $oneSignal->setTitle(env('ONESIGNAL_DEFAULT_TITLE'));
        $oneSignal->setSubTitle(env('ONESIGNAL_DEFAULT_SUBTITLE'));
        $oneSignal->configureAndroid(env('ONESIGNAL_ANDROID_SOUND'));
        $oneSignal->setMessage('Test from API');

        $response = $oneSignal->sendNotification();

        $this->assertIsInt($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testScheduleAMessage()
    {
        $dotenv = Dotenv::create(__DIR__ . '/../');
        $dotenv->load();

        $oneSignal = new OneSignal(env('ONESIGNAL_APP_ID'), env('ONESIGNAL_REST_API_ID'));
        $oneSignal->setTitle(env('ONESIGNAL_DEFAULT_TITLE'));
        $oneSignal->setSubTitle(env('ONESIGNAL_DEFAULT_SUBTITLE'));
        $oneSignal->setMessage('Test from API');
        $oneSignal->schedule(date('Y-m-d'), date('H:i:s', strtotime('+1 minutes')));

        $response = $oneSignal->sendNotification();

        $this->assertIsInt($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSendAMessageForInteligentDelivery()
    {
        $dotenv = Dotenv::create(__DIR__ . '/../');
        $dotenv->load();

        $oneSignal = new OneSignal(env('ONESIGNAL_APP_ID'), env('ONESIGNAL_REST_API_ID'));
        $oneSignal->setTitle(env('ONESIGNAL_DEFAULT_TITLE'));
        $oneSignal->setSubTitle(env('ONESIGNAL_DEFAULT_SUBTITLE'));
        $oneSignal->setMessage('Test from API');
        $oneSignal->setDelayOption();

        $response = $oneSignal->sendNotification();

        $this->assertIsInt($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSendAMessageForASegment()
    {
        $dotenv = Dotenv::create(__DIR__ . '/../');
        $dotenv->load();

        $oneSignal = new OneSignal(env('ONESIGNAL_APP_ID'), env('ONESIGNAL_REST_API_ID'));
        $oneSignal->setTitle(env('ONESIGNAL_DEFAULT_TITLE'));
        $oneSignal->setSubTitle(env('ONESIGNAL_DEFAULT_SUBTITLE'));
        $oneSignal->setMessage('Test from API');
        $oneSignal->setSegments('Inactive Users');

        $response = $oneSignal->sendNotification();

        $this->assertIsInt($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSendAMessageAndExcludeASegment()
    {
        $dotenv = Dotenv::create(__DIR__ . '/../');
        $dotenv->load();

        $oneSignal = new OneSignal(env('ONESIGNAL_APP_ID'), env('ONESIGNAL_REST_API_ID'));
        $oneSignal->setTitle(env('ONESIGNAL_DEFAULT_TITLE'));
        $oneSignal->setSubTitle(env('ONESIGNAL_DEFAULT_SUBTITLE'));
        $oneSignal->setMessage('Test from API');
        $oneSignal->setSegments('All');
        $oneSignal->excludeSegments('Active Users');

        $response = $oneSignal->sendNotification();

        $this->assertIsInt($response->getStatusCode());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetNotifications()
    {
        $dotenv = Dotenv::create(__DIR__ . '/../');
        $dotenv->load();

        $oneSignal = new OneSignal(env('ONESIGNAL_APP_ID'), env('ONESIGNAL_REST_API_ID'));
        $response = $oneSignal->getNotifications();
        $this->assertIsArray($response->notifications);
    }

    public function testGetNotification()
    {
        $dotenv = Dotenv::create(__DIR__ . '/../');
        $dotenv->load();

        $oneSignal = new OneSignal(env('ONESIGNAL_APP_ID'), env('ONESIGNAL_REST_API_ID'));
        $response = $oneSignal->getNotifications();
        $id = $response->notifications[0]->id;
        $notification = $oneSignal->getNotification($id);
        $this->assertInstanceOf(stdClass::class, $notification);
    }

//    public function testDeleteNotification()
//    {
//        $dotenv = Dotenv::create(__DIR__ . '/../');
//        $dotenv->load();
//
//        $oneSignal = new OneSignal(env('ONESIGNAL_APP_ID'), env('ONESIGNAL_REST_API_ID'));
//        $response = $oneSignal->getNotifications();
//        $id = $response->notifications[0]->id;
//        $response = $oneSignal->deleteNotification($id);
//        $this->assertIsBool($response);
//        $this->assertEquals(true, $response);
//    }
}