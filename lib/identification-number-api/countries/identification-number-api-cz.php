<?php

namespace Shoptet;

class IdentificationNumberApiCz extends IdentificationNumberApi {

  const URL_BASE = 'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty/';

  function is_valid() {
    return preg_match('/^[0-9]+$/', $this->in );
  }

  function get_company() {
    $api_url = self::URL_BASE . $this->in;

    // Odeslání požadavku
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Zkontroluj HTTP kód a odpověď
    if ($http_code !== 200 || !$response) {
        return false;
    }

    // Zpracování odpovědi
    $data = json_decode($response, true);

    if (isset($data['ico']) && $data['ico'] == $this->in) {
        return [
            'in' => $data['ico'],
            'tin' => $data['dic'] ?? null,
            'name' => $data['obchodniJmeno'],
            'street' => trim($data['sidlo']['nazevUlice'] . ' ' . $data['sidlo']['cisloDomovni'] . (isset($data['sidlo']['cisloOrientacni']) ? '/' . $data['sidlo']['cisloOrientacni'] : '')),
            'city' => $data['sidlo']['nazevObce'] . (isset($data['sidlo']['nazevCastiObce']) && $data['sidlo']['nazevCastiObce'] != $data['sidlo']['nazevObce'] ? ' – ' . $data['sidlo']['nazevCastiObce'] : ''),
            'zip' => $data['sidlo']['psc'],
            'region' => $this->convert_region_code($data['sidlo']['kodKraje']),
        ];
    }

    return false;
  }

  private function convert_region_code( string $code ) {
    // Region codes: https://vdp.cuzk.cz/vdp/ruian/vusc/vyhledej?search=Vyhledat
    $regions = [
      "19"  => 0,
      "35"  => 1,
      "116" => 2,
      "51"  => 3,
      "108" => 4,
      "86"  => 5,
      "78"  => 6,
      "132" => 7,
      "124" => 8,
      "94"  => 9,
      "43"  => 10,
      "27"  => 11,
      "60"  => 12, 
      "141" => 13,
    ];

    if ( isset($regions[$code]) ) {
      return $regions[$code];
    } 

    return false;
  }

}