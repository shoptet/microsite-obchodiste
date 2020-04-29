<?php

namespace Shoptet;

class IdentificationNumberApiCz extends IdentificationNumberApi {

  const URL_BASE = 'http://wwwinfo.mfcr.cz/cgi-bin/ares/darv_bas.cgi';

  function is_valid() {
    return preg_match('/^[0-9]+$/', $this->in );
  }

  function get_company() {

    $api_url = self::URL_BASE . "?ico=" . $this->in;
    $xml = $this->create_request($api_url);

    $ns = $xml->getDocNamespaces();
    $data = $xml->children($ns['are']);
    $el = $data->children($ns['D'])->VBAS;

    if (strval($el->ICO) == $this->in) {
      return [
        'in' => (string) $el->ICO,
        'tin' => (string) $el->DIC,
        'name' => (string) $el->OF,
        'street' => (string) ($el->AA->NU ?: $el->AA->N) . ' ' . (($el->AA->CO == '') ? $el->AA->CD : $el->AA->CD . '/' . $el->AA->CO),
        'city' => (string) $el->AA->N . (($el->AA->NCO && strval($el->AA->NCO) != strval($el->AA->N)) ? (' – ' . $el->AA->NCO) : '' ),
        'zip' => (string) $el->AA->PSC,
        'region' => $this->convert_region_code((string) $el->AA->AU->children($ns['U'])->KK),
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