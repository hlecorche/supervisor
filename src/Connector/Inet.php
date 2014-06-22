<?php

/*
 * This file is part of the Indigo Supervisor package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Supervisor\Connector;

use Buzz\Message\Request;
use Buzz\Message\RequestInterface as Request;
use Buzz\Client\FileGetContents as Client;
use InvalidArgumentException;

/**
 * Connect to Supervisor using simple file_get_contents
 * allow_url_fopen must be enabled
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Inet extends RequestConnector
{
    public function __construct(Client $client, $host, $port = 9001)
    {
        $resource = parse_url($host);

        $validScheme = array('http', 'https');

        if (array_key_exists('scheme', $resource) === false or in_array($resource['scheme'], $validScheme) === false) {
            $resource['scheme'] = 'http';
        }

        if (!$resource) {
            throw new InvalidArgumentException('The following host is not a valid resource: ' . $host);
        }

        $resource['port'] = $port;
        $flags = HTTP_URL_REPLACE | HTTP_URL_STRIP_AUTH | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT;

        $this->resource = http_build_url('', $resource, $flags);
        $this->local = gethostbyname($resource['host']) == '127.0.0.1';

        parent::__construct($client);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRequest(Request $request, $content)
    {
        parent::prepareRequest($request, $content);

        $request->setHost($this->host);
    }
}
