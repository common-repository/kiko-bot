<?php

namespace KikoBot\Models;

use WPMVC\MVC\Traits\FindTrait;
use WPMVC\MVC\Models\OptionModel as Model;
/**
 * KikoConfig model.
 * WordPress MVC model.
 *
 * @author 1000° Digital GmbH <https://www.1000grad.de>
 * @package kiko-wp-plugin
 * @version 1.0.0
 */
class KikoConfig extends Model
{
    use FindTrait;
    /**
     * Property id.
     * @since 1.0.0
     *
     * @var string
     */
    protected $id = 'kiko_bot';

    /**
     * Aliases.
     * Mapped against custom fields functions.
     * @var array
     */
    protected $aliases = [
        // Alias "config" for custom field "config"
        'config'    => 'field_config',
        'apiKey'    => 'field_apiKey',
        'handshake'       => 'field_handshake', //handshake for apiKey post request
        // Alias "timer" for custom field "timer"
        'timer'     => 'field_timer',
        // Alias "date" for class function "get_date"
        'date'      => 'func_getDate',
    ];

    /**
     * Returns the value used for alias "date".
     * @return string
     */
    public function getDate()
    {
        return $this->timer ? date( 'Y-m-d H:i', $this->timer ) : null;
    }
}