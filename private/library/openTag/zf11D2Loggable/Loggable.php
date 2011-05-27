<?php

namespace Opentag\Loggable;

/**
 * This interface is not necessary but can be implemented for
 * Domain Objects which in some cases needs to be identified as
 * Loggable
 * 
 * @author James A Helly <james@wednesday-london.com>,  Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @package Opentag.Loggable
 * @subpackage Loggable
 * @link http://www.gediminasm.org
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
interface Loggable
{
    // this interface is not necessary to implement
    
    /**
     * @Opentag:Loggable
     * to mark the class as loggable use class annotation @Opentag:Loggable
     * this object will contain now a history
     * available options:
     *         logEntryClass="My\LogEntryObject" (optional) defaultly will use internal object class
     * example:
     * 
     * @Opentag:Loggable(logEntryClass="My\LogEntryObject")
     * class MyEntity
     */
}