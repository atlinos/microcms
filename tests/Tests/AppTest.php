<?php

namespace MicroCMS\Tests;

use Silex\Application;
use Silex\WebTestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class AppTest extends WebTestCase
{
    /**
     * @dataProvider provideUrls
     */
    public function testPageIsSuccessfull($url)
    {
        $client = $this->createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * {@inheritDoc}
     */
    public function createApplication()
    {
        $app = new Application();

        require __DIR__ . '/../../app/config/dev.php';
        require __DIR__ . '/../../app/app.php';
        require __DIR__ . '/../../app/routes.php';

        unset($app['exception_handler']);

        $app['session.test'] = true;

        $app['security.access_rules'] = [];

        return $app;
    }

    public function provideUrls()
    {
        return [
            ['/'],
            ['/article/1'],
            ['/login'],
            ['/admin'],
            ['/admin/article/add'],
            ['/admin/article/1/edit'],
            ['/admin/comment/1/edit'],
            ['/admin/user/add'],
            ['/admin/user/1/edit'],
        ];
    }
}