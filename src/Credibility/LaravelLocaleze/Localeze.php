<?php

namespace Credibility\LaravelLocaleze;

use Illuminate\Foundation\Application;

class Localeze {

    /** @var LocalezeRequester  */
    public $requester;

    /** @var array  */
    public $serviceKeys = [];

    /** @var Application  */
    public $app;

    /**
     * @param LocalezeRequester $requester
     * @param Application $app
     */
    public function __construct(LocalezeRequester $requester, Application $app)
    {
        $this->requester = $requester;
        $this->app = $app;
    }

    /**
     * @param LocalezeBusiness $business
     * @return LocalezeResponse
     */
    public function checkAvailability(LocalezeBusiness $business)
    {
        $elements = ["3722"];
        $this->addServiceKey(1510,$business->toXML());
        $response = $this->requester->run($this->serviceKeys,$elements);
        return new LocalezeResponse($response);
    }

    /**
     * @param LocalezeBusiness $business
     * @return LocalezeResponse
     */
    public function businessPost(LocalezeBusiness $business)
    {
        $elements = ["3700"];
        $this->addServiceKey(1510,$business->toXML());
        $response = $this->requester->run($this->serviceKeys,$elements);
        return new LocalezeResponse($response);
    }

    /**
     * @param string $category
     * @return LocalezeResponse
     */
    public function taxonomyQuery($category = "ACTIVEHEADINGS")
    {
        $elements = ["2935"];
        $this->addServiceKey(1501,$category);
        $response = $this->requester->run($this->serviceKeys, $elements);
        return new LocalezeResponse($response);
    }

    /**
     * @param LocalezeBusiness $business
     * @return LocalezeResponse
     */

    //does not reopen business if called again
    public function businessClose(LocalezeBusiness $business)
    {
        $elements = ["3710"];
        $this->addServiceKey(1510,$business->toXML());
        $response = $this->requester->run($this->serviceKeys,$elements);
        return new LocalezeResponse($response);
    }

    /**
     * @param LocalezeBusiness $business
     * @return LocalezeResponse
     */
    public function subscriptionAdministration(LocalezeBusiness $business)
    {
        $elements = ["3712"];
        $this->addServiceKey(1510,$business->toXML());
        //License has an ID, a status (ACTIVE or DISABLED), and a renewal method (1 - not renew, 2 - auto)
        $this->addServiceKey(1518,"LICENSE INFO");
        $response = $this->requester->run($this->serviceKeys,$elements);
        return new LocalezeResponse($response);
    }

    public function summaryOfBusinesses(LocalezeFilter $filter)
    {
        $elements = ["3721"];
        $this->addServiceKey(1513,$filter->toXml());
        $response = $this->requester->run($this->serviceKeys,$elements);
        return new LocalezeResponse($response);
    }

    /**
     * @param $phone
     * @return LocalezeResponse
     */
    public function phoneSearch($phone)
    {
        $this->addServiceKey(1,$phone);
        return $this->search();
    }

    /**
     * @param $businessName
     * @param $zip
     * @return LocalezeResponse
     */
    public function businessAndZipSearch($businessName, $zip)
    {
        $this->addServiceKey(1396,$businessName);
        $this->addServiceKey(1393, $zip);
        return $this->search();
    }

    /**
     * @param $id
     * @return LocalezeResponse
     */
    public function recordIdSearch($id)
    {
        $this->addServiceKey(1511,$id);
        return $this->search();
    }

    /**
     * @return LocalezeBusiness
     */
    public function createBusiness()
    {
        return new LocalezeBusiness();
    }

    /**
     * @return LocalezeFilter
     */
    public function createFilter()
    {
        return new LocalezeFilter();
    }

    public function getCategoryFromSic($sic)
    {
        $category = DB::select('select category_name from localeze_categories where id = ?',$sic);
        return $category;
    }

    /**
     * @return LocalezeResponse
     */
    private function search(){
        $elements = ["3720"];
        $response = $this->requester->run($this->serviceKeys,$elements);
        return new LocalezeResponse($response);
    }

    /**
     * @param $id
     * @param $value
     */
    private function addServiceKey($id,$value)
    {
        $this->serviceKeys[] = ["id" => $id, "value" => $value];
    }
}