<?php
declare(strict_types=1);

namespace AndreSeko\OneSignal;

use Illuminate\Support\Facades\Facade;

/**
 * Class OneSignalFacade
 * @author Andre Goncalves <andreseko@gmail.com>
 * @version 1.0.0
 * @package andreseko\OneSignal
 */
class OneSignalFacade extends Facade
{
    /**
     * getFacadeAccessor
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'onesignal';
    }
}