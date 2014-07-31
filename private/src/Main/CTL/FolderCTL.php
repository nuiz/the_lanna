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
     * @PUT
     * @uri /[h:id]
     */
    public function edit(){
        $item = FolderService::instance()->edit($this->reqInfo->urlParam("id"), $this->reqInfo->params(), $this->getCtx());
        return $item;
    }
} 