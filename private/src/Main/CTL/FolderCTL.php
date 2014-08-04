<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/26/14
 * Time: 4:50 PM
 */

namespace Main\CTL;
use Main\Service\FolderService;

/**
 * @Restful
 * @uri /folder
 */
class FolderCTL extends BaseCTL {
    /**
     * @POST
     */
    public function add(){
        $item = FolderService::instance()->add($this->reqInfo->params(), $this->getCtx());
        return $item;
    }

    /**
     * @PUT
     * @uri /[h:id]
     */
    public function edit(){
        $item = FolderService::instance()->edit($this->reqInfo->urlParam("id"), $this->reqInfo->params(), $this->getCtx());
        return $item;
    }

    /**
     * @GET
     * @uri /[h:id]
     */
    public function get(){
        $item = FolderService::instance()->get($this->reqInfo->urlParam("id"), $this->getCtx());
        return $item;
    }

    /**
     * @DELETE
     * @uri /[h:id]
     */
    public function delete(){
        FolderService::instance()->delete($this->reqInfo->urlParam("id"));
        return array("success"=> true);
    }
}