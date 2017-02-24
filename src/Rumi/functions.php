<?php
/**
 * This file is part of the Rumi package.
 *
 * (c) Paweł Bobryk <bobryk.pawel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Rumi\functions;


function uniqid(){
    static $uniqid = 0;

    return md5(date('Y-m-d h:i:s').$uniqid++);
}
