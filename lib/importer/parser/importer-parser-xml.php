<?php

namespace Shoptet;

use Rodenastyle\StreamParser\StreamParser;

class ImporterParserXML extends ImporterParser {

  function parse() {
    StreamParser::xml($this->source)->each(function($product_collection) {

      if( $this->is_exceed() ) return;

      $product = $this->get_product_base();
      $product->import_xml_collection( $product_collection );

      Importer::enqueue_product($product);
      
      $this->products_imported++;
    });
  }

}