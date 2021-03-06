<?php

/*
 * This file is part of the ZeldaLogs website.
 *
 * (c) 2010-2011 Julien Brochet <mewt@madalynn.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZeldaLogs\Extension;

use Silex\ExtensionInterface;
use Silex\Application;
use Silex\SilexEvents;

use Madalynn\mIRCParserExtension\mIRCParserExtension;
use ZeldaLogs\LogManager;

class ZeldaLogsExtension implements ExtensionInterface
{
    public function register(Application $app)
    {
        $app['log.manager'] = $app->share(function () use ($app) {
            return new LogManager(
                    $app['zeldalogs.directory'],
                    $app['zeldalogs.prefix'],
                    $app['zeldalogs.date.format'],
                    $app['zeldalogs.number.of.lines']
            );
        });

        $app['dispatcher']->addListener(SilexEvents::BEFORE, function() use($app) {
            $app['twig']->addExtension(new mIRCParserExtension());
        });
    }
}