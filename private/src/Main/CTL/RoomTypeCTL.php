<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/19/14
 * Time: 4:54 PM
 */

namespace Main\CTL;
use Main\Service\RoomTypeService;

/**
 * @Restful
 * @uri /roomtype
 */
class RoomTypeCTL extends BaseCTL {
    /**
     * @GET
     */
    public function gets(){
        $items = RoomTypeService::instance()->gets(array(), $this->getCtx());
        return $items;
    }

    /**
     * @POST
     */
    public function add(){
        $item = RoomTypeService::instance()->add($this->reqInfo->params(), $this->getCtx());
        return $item;
    }

    /**
     * @GET
     * @uri /[h:id]
     */
    public function get(){
        $item = RoomTypeService::instance()->get($this->reqInfo->urlParam('id'), $this->getCtx());
        return $item;
    }

    /**
     * @PUT
     * @uri /[h:id]
     */
    public function edit(){
        $item = RoomTypeService::instance()->edit($this->reqInfo->urlParam('id'), $this->reqInfo->params(), $this->getCtx());
        return $item;
    }

    /**
     * @DELETE
     * @uri /[h:id]
     */
    public function delete(){
        $response = RoomTypeService::instance()->delete($this->reqInfo->urlParam('id'));
        return $response;
    }

    /**
     * @GET
     * @uri /[h:id]/pictures
     */
    public function getItemPictures(){
        $items = RoomTypeService::instance()->getItemPictures($this->reqInfo->urlParam('id'));
        return $items;
    }

    /**
     * @POST
     * @uri /[h:id]/pictures
     */
    public function addItemPictures(){
        $items = RoomTypeService::instance()->addPictures($this->reqInfo->urlParam('id'), $this->reqInfo->param('pictures', array()));
        return $items;
    }

    /**
     * @DELETE
     * @uri /[h:id]/pictures
     */
    public function deleteItemPictures(){
        $items = RoomTypeService::instance()->deletePictures($this->reqInfo->urlParam('id'), $this->reqInfo->params());
        return $items;
    }
}