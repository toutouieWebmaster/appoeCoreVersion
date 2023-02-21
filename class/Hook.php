<?php

namespace App;
class Hook
{
    private static $defaultPriority = 100;
    private static $HOOKS = array(
        'cron' => array(),
        'core_admin_before_dashboard' => array(),
        'core_admin_after_dashboard' => array(),
        'core_front_before_html' => array()
    );

    /**
     * @param $hook
     */
    public static function addHook($hook)
    {
        if (!array_key_exists($hook, self::$HOOKS)) {
            self::$HOOKS[$hook] = array();
        }
    }

    /**
     * @param $hook
     * @param array $args
     * @return bool
     */
    public static function apply($hook, array $args = array())
    {
        if (!empty(self::$HOOKS[$hook])) {
            foreach (array_sort(self::$HOOKS[$hook], 'priority', SORT_ASC) as $f) {
                call_user_func_array($f['function'], $args);
            }
        }
        return true;
    }

    /**
     * @param $hook
     * @param $function
     * @param int $priority
     * @return bool
     */
    public static function add_action($hook, $function, $priority = 0)
    {
        if (!isset($function)) {
            return false;
        }

        if (!isset($priority) || !(intval($priority) > 0)) {
            $priority = self::$defaultPriority;
        }

        self::$HOOKS[$hook][] = array(
            'function' => $function,
            'priority' => intval($priority)
        );
        return true;
    }
}