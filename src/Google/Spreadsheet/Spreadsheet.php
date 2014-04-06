<?php
/**
 * Copyright 2013 Asim Liaquat
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Google\Spreadsheet;

use SimpleXMLElement;
use DateTime;

/**
 * Spreadsheet. Represents a single spreadsheet.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class Spreadsheet
{
    const REL_WORKSHEETS_FEED = 'http://schemas.google.com/spreadsheets/2006#worksheetsfeed';

    /**
     * The spreadsheet xml object
     * 
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * Initializes the spreadsheet object
     * 
     * @param string|SimpleXMLElement $xml
     */
    public function __construct(SimpleXMLElement $xml)
    {
        $this->xml = $xml;
    }

    /**
     * Get the spreadsheet id. Returns the actual id and not the full url
     * 
     * @return string
     */
    public function getId()
    {
        $url = $this->xml->id->__toString();
        return $url;
        //return substr($url, strrpos($url, '/')+1);
    }

    /**
     * Get the updated date
     * 
     * @return DateTime
     */
    public function getUpdated()
    {
        return new DateTime($this->xml->updated->__toString());
    }

    /**
     * Returns the title (name) of the spreadsheet
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->xml->title->__toString();
    }

    /**
     * Returns the feed url of the spreadsheet
     * 
     * @return string
     */
    public function getWorksheetsFeedUrl()
    {
        return Util::getLinkHref($this->xml, self::REL_WORKSHEETS_FEED);
    }

}