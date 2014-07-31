<?php
/**
 * Created by PhpStorm.
 * User: p2
 * Date: 7/18/14
 * Time: 4:05 PM
 */

namespace Main\CTL\Contact;


use Main\CTL\BaseCTL;
use Main\Service\ContactCommentService;

/**
 * @Restful
 * @uri /contact/comment
 */
class CommentCTL extends BaseCTL {
    /**
     * @POST
     */
    public function post(){
        $comment = ContactCommentService::instance()->add($this->reqInfo->params(), $this->getCtx());
        return $comment;
    }

    /**
     * @GET
     */
    public function gets(){
        $comments = ContactCommentService::instance()->gets($this->reqInfo->inputs(), $this->getCtx());
        return $comments;
    }
}