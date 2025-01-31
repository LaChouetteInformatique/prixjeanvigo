<?php
/**
 * Singleton design pattern code
 * 
 */

namespace DB_PLUGIN\Inc\Traits;

if (!trait_exists('Singleton')) {
  trait Singleton
  {
    private static $instance;

    final public static function getInstance()
    {
      if (!self::$instance) {
        // new self() will refer to the class that uses the trait
        self::$instance = new self();

        do_action(sprintf('DB_PLUGIN_singleton_init%s', get_called_class()));
      }
      return self::$instance;
    }

    public function __clone()
    {
    } // Prevent cloning of the instance
    public function __sleep()
    {
    } // Prevent serialization of the instance
    public function __wakeup()
    {
    } // Prevent deserialization of the instance
  }
}