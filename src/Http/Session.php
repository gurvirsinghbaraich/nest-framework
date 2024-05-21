<?php

namespace Nest\Framework\Http;

class Session
{
  // Stores a value in the session under the given key.
  public static function put(string $key, mixed $value)
  {
    // Set the session variable with the provided key and value.
    $_SESSION[$key] = $value;
  }

  // Retrieves a value from the session by its key.
  public static function get(string $key)
  {
    // Check if the session variable with the provided key exists.
    if (isset($_SESSION[$key])) {
      // Return the session variable value if it exists.
      return $_SESSION[$key];
    }

    // Return null if the session variable does not exist.
    return null;
  }
}
