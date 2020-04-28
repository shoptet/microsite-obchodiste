<?php

namespace Shoptet;

abstract class IdentificationNumberApi {

  protected $in;

  function __construct( $in ) {
    $this->in = $in;
  }

  abstract function is_valid();

  abstract function get_company();

  protected function create_request( string $url ) {
    $curl = curl_init($url);

    curl_setopt_array($curl, [
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_SSL_VERIFYPEER => 0
    ]);

    $content = curl_exec($curl);

    if ( !$content ) {
      throw new \Exception( curl_error($curl), curl_errno($curl) );
    }

    curl_close($curl);

    $xml = simplexml_load_string($content);

    if ( $xml ) {
      return $xml;
    }

    return false;
  }

}