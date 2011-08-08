<?php

/*
 * This file is part of the Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Validation
 *
 * @package     Orient
 * @subpackage  Exception
 * @author      Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Exception;

use Orient\Exception;

class Validation extends Exception
{
    public function __construct($value)
    {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        
        $this->message = 'Validation of "' . $value . '" failed';
    }
}
