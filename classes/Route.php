<?php
/**
 * @package    PHP Advanced API Guide
 * @author     Davison Pro <davisonpro.coder@gmail.com>
 * @copyright  2019 DavisonPro
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */

namespace RoboticEvent;

use RoboticEvent\Api;

abstract class Route
{
    /**
     * @var \Slim\Slim
     */
    protected $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }
}
