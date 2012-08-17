<?

class PresentationFlashMixin extends Mixin
{
  public static $flash_next = array();
  public static $flash = array();
  
  static function init()
  {
    W::register_filter('flashes', 'W::flashes');
    if(isset($_COOKIE['flash_next']))
    {
      $val = json_decode($_COOKIE['flash_next']);
      if(is_array($val))
      {
        self::$flash = $val;
      }
      setcookie('flash_next', null, 0, '/');
    }
  }
  
  static function flashes($flashes)
  {
    return array_merge($flashes, self::$flash);
  }
  
  static function flash_next()
  {
    $args = func_get_args();
    $msg = call_user_func_array('self::flash_interpolate', $args);
    self::$flash_next[] = $msg;
    setcookie('flash_next', json_encode(self::$flash_next), 0, '/');
  }
  
  static function flash()
  {
    $args = func_get_args();
    $msg = call_user_func_array('self::flash_interpolate', $args);
    self::$flash[] = $msg;
  }
  
  static function has_flash()
  {
    return count(self::$flash)>0;
  }
  
  static function get_flash()
  {
    $flash = self::$flash;
    self::$flash = array();
    return $flash;
  }
  
  static function flash_interpolate()
  {
    $msg = func_get_arg(0);
    for($i=1;$i<func_num_args();$i++)
    {
      $v = func_get_arg($i);
      $msg = preg_replace("/\?/", $v, $msg);
    }
    return $msg;
  }
}