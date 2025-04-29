<?php

namespace App;
class Hook
{
    private static $defaultPriority = 100;
    private static $HOOKS = array(
        'cron' => [],
        'core_admin_before_dashboard' => [],
        'core_admin_after_dashboard' => [],
        'core_front_before_html' => []
    );

    /**
     * @param $hook
     */
    public static function addHook($hook)
    {
        if (!array_key_exists($hook, self::$HOOKS)) {
            self::$HOOKS[$hook] = [];
        }
    }

    /**
     * @param $hook
     * @param array $args
     * @return bool
     */
    public static function apply($hook, array $args = [])
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