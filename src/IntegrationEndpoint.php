<?php

/*
 * This file is part of the fw4/setle-api library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Setle;

use Setle\ApiAdapter\ApiAdapter;
use Setle\Request\Request;
use Setle\Response\CollectionResponse;
use Setle\Response\Response;

class IntegrationEndpoint
{
    /** @var Setle */
    protected $api;

    /** @var string */
    protected $integrationName;

    public function __construct(Setle $api, string $integration_name)
    {
        $this->api = $api;
        $this->integrationName = $integration_name;
    }

    /**
     * Get all estates for this integration.
     *
     * @throws Exception\AuthException if access token is missing or invalid
     * @throws Exception\ApiException if a server-side error occurred
     *
     * @return CollectionResponse
     */
    public function getEstates(): CollectionResponse
    {
        $request = new Request('GET', '/v1/integrations/' . $this->integrationName . '/estates');
        return new CollectionResponse($this->api->request($request));
    }

    /**
     * Get a specific estate for this integration.
     *
     * @param int|string $id ID of the estate to fetch
     *
     * @throws Exception\AuthException if access token is missing or invalid
     * @throws Exception\NotFoundException if requested estate is unavailable
     * @throws Exception\ApiException if a server-side error occurred
     *
     * @return Response
     */
    public function getEstate($id): Response
    {
        $request = new Request('GET', '/v1/integrations/' . $this->integrationName . '/estates/' . strval($id));
        return new Response($this->api->request($request));
    }
}
