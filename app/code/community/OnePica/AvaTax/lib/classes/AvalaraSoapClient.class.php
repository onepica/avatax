<?php

/**
 * AvalaraSoapClient.class.php
 *
 * @package Base
 */

/**
 * Abstract base class for all Avalara web service clients.
 *
 * Users should never create instances of this class.
 *
 * @package Base
 * @abstract
 * @see AddressServiceSoap
 * @see TaxServiceSoap
 * @author tblanchard
 *  
 * Copyright (c) 2007, Avalara.  All rights reserved.
 */

class AvalaraSoapClient 
{
    protected $client;

    public function __getLastRequest() { return $this->client->__getLastRequest(); }
    public function __getLastResponse() { return $this->client->__getLastResponse(); }
    public function __getLastRequestHeaders() { return $this->client->__getLastRequestHeaders(); }
    public function __getLastResponseHeaders() { return $this->client->__getLastResponseHeaders(); }

}



?>
