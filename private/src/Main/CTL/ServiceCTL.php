<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/22/14
 * Time: 5:11 PM
 */

namespace Main\CTL;
use Main\Service\ServiceService;

/**
 * @Restful
 * @uri /service
 */
class ServiceCTL extends BaseCTL {
    /**
     * @POST
     */
    public function add(){
        $item = ServiceService::instance()->add($this->reqInfo->params(), $this->getCtx());
        return $item;
    }

    /**
     * @GET
     * @uri /[h:id]
     */
    public function get(){
        $item = ServiceService::instance()->get($this->reqInfo->urlParam('id'), $this->getCtx());
        return $item;
    }

    /**
     * @PUT
     * @uri /[h:id]
     */
    public function edit(){
        $item = ServiceService::instance()->edit($this->reqInfo->urlParam('id'), $this->reqInfo->params(), $this->getCtx());
        return $item;
    }

    /**
     * @DELETE
     * @uri /[h:id]
     */
    public function delete(){
        $item = ServiceService::instance()->delete($this->reqInfo->urlParam('id'), $this->getCtx());
        return $item;
    }

    /**
     * @GET
     * @uri /[h:id]/pictures
     */
    public function getPictures(){
        $items = ServiceService::instance()->getItemPictures($this->reqInfo->urlParam('id'));
        return $items;
    }

    /**
     * @POST
     * @uri /[h:id]/pictures
     */
    public function addPictures(){
        $items = ServiceService::instance()->addPictures($this->reqInfo->urlParam('id'), $this->reqInfo->param('pictures'));
        return $items;
    }

    /**
     * @DELETE
     * @uri /[h:id]/pictures
     */
    public function deleteItemPictures(){
        $items = ServiceService::instance()->deletePictures($this->reqInfo->urlParam('id'), $this->reqInfo->params());
        return $items;
    }
}