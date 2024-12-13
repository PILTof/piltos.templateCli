<?php
/**
 * Escape character
 */
define('ESC', "\033");
/**
 * ANSI colours
 */
define('ANSI_BLACK', ESC . "[30m");
define('ANSI_RED', ESC . "[31m");
define('ANSI_GREEN', ESC . "[32m");
define('ANSI_YELLOW', ESC . "[33m");
define('ANSI_BLUE', ESC . "[34m");
define('ANSI_MAGENTA', ESC . "[35m");
define('ANSI_CYAN', ESC . "[36m");
define('ANSI_WHITE', ESC . "[37m");
/**
 * ANSI styles
 */
define('ANSI_BOLD', ESC . "[1m");
define('ANSI_ITALIC', ESC . "[3m"); // limited support. ymmv.
define('ANSI_UNDERLINE', ESC . "[4m");
define('ANSI_STRIKETHROUGH', ESC . "[9m");
/**
 * ANSI background colours
 */
define('ANSI_BACKGROUND_BLACK', ESC . "[40m");
define('ANSI_BACKGROUND_RED', ESC . "[41m");
define('ANSI_BACKGROUND_GREEN', ESC . "[42m");
define('ANSI_BACKGROUND_YELLOW', ESC . "[43m");
define('ANSI_BACKGROUND_BLUE', ESC . "[44m");
define('ANSI_BACKGROUND_MAGENTA', ESC . "[45m");
define('ANSI_BACKGROUND_CYAN', ESC . "[46m");
define('ANSI_BACKGROUND_WHITE', ESC . "[47m");
/**
 * Clear all ANSI styling
 */
define('ANSI_CLOSE', ESC . "[0m");
?>