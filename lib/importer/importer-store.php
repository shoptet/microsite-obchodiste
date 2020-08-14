<?php

namespace Shoptet;

class ImporterStore {

  const TABLE_NAME = 'importer_store';
  const DB_VERSION = 1;
  const DB_VERSION_OPTION = 'importer_store_db_version';

  static function init() {
    add_action( 'init', [ get_called_class(), 'check_db_version' ] );
    add_action( 'action_scheduler_deleted_action', [ get_called_class(), 'delete_by_action_id' ] );
  }

  static function insert( array $args ) {
    global $wpdb;
    $encoded_args = wp_json_encode($args);
    $table_name = self::get_table_name();
    $wpdb->insert( $table_name, [ 'args' => $encoded_args ] );
    return $wpdb->insert_id;
  }

  static function update_action_id( $args_id, $action_id ) {
    global $wpdb;
    $table_name = self::get_table_name();
    $updated = $wpdb->update(
      $table_name,
      [ 'action_id' => $action_id ],
      [ 'id' => $args_id ],
      [ '%d' ],
      [ '%d' ]
    );
    if ( empty( $updated ) ) {
			throw new \InvalidArgumentException( sprintf( __( 'Unidentified args ID %s', 'action-scheduler-long-args' ), $args_id ) );
		}
  }

  static function get( $args_id ) {
    global $wpdb;
    $table_name = self::get_table_name();
    $sql = "SELECT args FROM {$table_name} WHERE id=%d";
    $sql = $wpdb->prepare( $sql, $args_id );
    $args = $wpdb->get_var( $sql );
  
    if ( $args === null ) {
      throw new \InvalidArgumentException( __( 'Invalid arguments ID. No arguments found.', 'action-scheduler-long-args' ) );
    } elseif ( empty( $args ) ) {
      throw new \RuntimeException( __( 'Unknown arguments found for arguments ID.', 'action-scheduler-long-args' ) );
    } else {
      return json_decode($args, true);
    }
  }

  static function delete_by_action_id ( $action_id ) {
    global $wpdb;
    $table_name = self::get_table_name();
    $deleted = $wpdb->delete( $table_name, [ 'action_id' => $action_id ], [ '%d' ] );
  }

  static function install_db() {
    global $wpdb;
    
    if ( self::get_installed_db_version() != self::DB_VERSION_OPTION ) {
      $table_name = self::get_table_name();
	
      $charset_collate = $wpdb->get_charset_collate();
  
      $sql = "CREATE TABLE $table_name (
        id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        action_id bigint(20) UNSIGNED,
        args text NOT NULL,
        PRIMARY KEY (id)
      ) $charset_collate;";
  
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $sql );
  
      update_option( self::DB_VERSION_OPTION, self::DB_VERSION );
    }
	  
  }

  static function check_db_version() {
    if ( self::get_installed_db_version() != self::DB_VERSION_OPTION ) {
      self::install_db();
    }
  }

  static function get_installed_db_version() {
    return get_site_option( self::DB_VERSION_OPTION );
  }

  static function get_table_name() {
    global $wpdb;
    return $wpdb->prefix . self::TABLE_NAME;
  }

}

ImporterStore::init();