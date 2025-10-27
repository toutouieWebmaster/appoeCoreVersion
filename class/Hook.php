<?php

namespace App;
class Hook
{
    private static int $defaultPriority = 100;
    private static array $HOOKS = array(
        'cron' => [],
        'core_admin_before_dashboard' => [],
        'core_admin_after_dashboard' => [],
        'core_front_before_html' => []
    );

    /**
     * @param int|string $hook
     */
    public static function addHook(int|string $hook): void
    {
        if (!array_key_exists($hook, self::$HOOKS)) {
            self::$HOOKS[$hook] = [];
        }
    }

    /**
     * @param string $hook
     * @param array $args
     * @return bool
     */
    public static function apply(string $hook, array $args = []): bool
    {
        if (!empty(self::$HOOKS[$hook])) {
            foreach (array_sort(self::$HOOKS[$hook], 'priority') as $f) {
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
    public static function add_action($hook, $function, int $priority = 0): bool
    {
        if (!isset($function)) {
            return false;
        }

        if (!isset($priority) || !($priority > 0)) {
            $priority = self::$defaultPriority;
        }

        self::$HOOKS[$hook][] = array(
            'function' => $function,
            'priority' => $priority
        );
        return true;
    }
}