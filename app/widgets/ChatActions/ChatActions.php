<?php

use Moxl\Xec\Action\Message\Retract;

include_once WIDGETS_PATH.'ContactActions/ContactActions.php';

class ChatActions extends \Movim\Widget\Base
{
    /**
     * @brief Get a Drawer view of a contact
     */
    public function ajaxGetContact($jid)
    {
        $c = new ContactActions;
        $c->ajaxGetDrawer($jid);
    }

    /**
     * @brief Display the message dialog
     */
    public function ajaxShowMessageDialog(string $mid)
    {
        $message = $this->user->messages()
                              ->where('mid', $mid)
                              ->first();

        if ($message) {
            $view = $this->tpl();
            $view->assign('message', $message);

            Dialog::fill($view->draw('_chatactions_message'));
        }
    }

    /**
     * @brief Edit a message
     */
    public function ajaxEditMessage($mid)
    {
        $this->rpc('Dialog.clear');
        $this->rpc('Chat.editMessage', $mid);
    }

    /**
     * @brief Retract a message
     *
     * @param string $to
     * @param string $mid
     * @return void
     */
    public function ajaxHttpDaemonRetract($mid)
    {
        $retract = $this->user->messages()
                              ->where('mid', $mid)
                              ->first();

        if ($retract && $retract->originid) {
            $this->rpc('Dialog.clear');

            $r = new Retract;
            $r->setTo($retract->jidto)
              ->setOriginid($retract->originid)
              ->request();

            $retract->retract();
            $retract->save();

            $packet = new \Moxl\Xec\Payload\Packet;
            $packet->content = $retract;

            $c = new Chat;
            $c->onMessage($packet, false, true);
        }
    }
}