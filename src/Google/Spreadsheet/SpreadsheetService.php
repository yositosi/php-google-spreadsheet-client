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

use InvalidArgumentException;
use SimpleXMLElement;

/**
 * Spreadsheet Service.
 *
 * @package    Google
 * @subpackage Spreadsheet
 * @author     Asim Liaquat <asimlqt22@gmail.com>
 */
class SpreadsheetService
{
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var ServiceRequestInterface
     */
    protected $serviceRequest;

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
        $this->serviceRequest = new DefaultServiceRequest($accessToken);
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function setRequest($request)
    {
        $this->serviceRequest = $request;
    }

    /**
     * Fetches a list of spreadhsheet spreadsheets from google drive.
     *
     * @return \Google\Spreadsheet\SpreadsheetFeed
     */
    public function getSpreadsheetFeed()
    {
        $res = $this->serviceRequest->get('feeds/spreadsheets/private/full');
        return new SpreadsheetFeed($res);
    }

    /**
     * [getWorksheets description]
     * @param  Spreadsheet $spreadsheet [description]
     * @return \Google\Spreadsheet\WorksheetFeed
     */
    public function getWorksheetFeed(Spreadsheet $spreadsheet)
    {
        $url = $spreadsheet->getWorksheetsFeedUrl();
        $res = $this->serviceRequest->get($url);
        return new WorksheetFeed($res);
    }

    /**
     * [addWorksheet description]
     * 
     * @param [type]  $title    [description]
     * @param integer $rowCount [description]
     * @param integer $colCount [description]
     *
     * @return \Google\Spreadsheet\Worksheet
     */
    public function addWorksheet(Spreadsheet $spreadsheet, $title, $rowCount=100, $colCount=10)
    {
        $entry = sprintf('
            <entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">
                <title>%s</title>
                <gs:rowCount>%u</gs:rowCount>
                <gs:colCount>%u</gs:colCount>
            </entry>',
            $title,
            $rowCount,
            $colCount
        );

        $response = $this->serviceRequest->post($spreadsheet->getWorksheetsFeedUrl(), $entry);
        return new Worksheet(new SimpleXMLElement($response));
    }

    /**
     * Delete the specified worksheet
     *
     * @param \Google\Spreadsheet\Worksheet $worksheet
     */
    public function deleteWorksheet(Worksheet $worksheet)
    {
        $this->serviceRequest->delete($worksheet->getEditUrl());
    }

    /**
     * Get the list feed of this worksheet
     * 
     * @return \Google\Spreadsheet\ListFeed
     */
    public function getListFeed(Worksheet $worksheet)
    {
        $res = $this->serviceRequest->get($worksheet->getListFeedUrl());
        return new ListFeed($res);
    }

    /**
     * Get the cell feed of this worksheet
     * 
     * @return \Google\Spreadsheet\CellFeed
     */
    public function getCellFeed(Worksheet $worksheet)
    {
        $res = $this->serviceRequest->get($worksheet->getCellFeedUrl());
        return new CellFeed($res);
    }

    /**
     * Fetches a single spreadsheet from google drive by id if you decide
     * to store the id locally. This can help reduce api calls.
     *
     * @param string $id the id of the spreadsheet
     *
     * @return \Google\Spreadsheet\Spreadsheet
     *
     * @throws InvalidArgumentException
     */
    public function getSpreadsheetById($id)
    {
        if(!is_string($id) || strlen($id) < 1) {
            throw new InvalidArgumentException('Invalid spreadsheet id');
        }

        $serviceRequest = ServiceRequestFactory::getInstance();
        $serviceRequest->getRequest()->setEndpoint('feeds/spreadsheets/private/full/'. $id);
        $res = $serviceRequest->execute();
        return new Spreadsheet($res);
    }
}
