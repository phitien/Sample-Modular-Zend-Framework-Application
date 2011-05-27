<?php

namespace Opentag\Loggable\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Loggable extension annotation which should be used
 * on for specific record logging on any Domain Object
 *
 * @author Boussekeyt Jules <jules.boussekeyt@gmail.com>
 * @author James A Helly <james@wednesday-london.com>,  Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @package Opentag.Loggable.Mapping
 * @subpackage Annotations
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

final class Loggable extends Annotation
{
    public $logEntryClass;
}

final class Versioned extends Annotation
{

}