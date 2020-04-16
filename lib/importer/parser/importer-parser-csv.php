<?php

namespace Shoptet;

class ImporterParserCSV extends ImporterParser {

  function parse() {
    $fp = fopen( $this->source, 'r' );
    if ( ! $fp ) return null;
  
    $header = fgetcsv( $fp, 0, ';' );
  
    while( $row = fgetcsv( $fp, 0, ';' ) ) {
      
      if ( $this->is_exceed() ) break;

      // convert columns to UTF-8
      foreach ( $row as $key => $value ) {
        $row[$key] = iconv( 'CP1250', 'UTF-8', $value );
      }
      $product_array = array_combine( $header, $row );
  
      $product = $this->get_product_base();
      $product->import_csv_array($product_array);

      Importer::enqueue_product($product);
  
      $this->products_imported++;
    }
  
    fclose($fp);
  }

}