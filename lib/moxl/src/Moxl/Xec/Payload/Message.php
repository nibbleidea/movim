<?php

namespace Moxl\Xec\Payload;

use Movim\ChatStates;

class Message extends Payload
{
    public function handle($stanza, $parent = false)
    {
        if ($stanza->confirm
        && $stanza->confirm->attributes()->xmlns == 'http://jabber.org/protocol/http-auth') {
            return;
        }

        if ($stanza->attributes()->type == 'error') {
            return;
        }

        $message = \App\Message::findByStanza($stanza);
        $message = $message->set($stanza, $parent);

        if ($stanza->composing || $stanza->paused) {
            $from = ($message->type == 'groupchat')
                ? $message->jidfrom.'/'.$message->resource
                : $message->jidfrom;

            if ($stanza->composing) {
                (ChatStates::getInstance())->composing($from, $message->jidto, isset($message->mucpm));
            }

            if ($stanza->paused) {
                (ChatStates::getInstance())->paused($from, $message->jidto, isset($message->mucpm));
            }
        }

        if (!$message->encrypted
        && (!$message->isEmpty() || $message->isSubject())) {
            $message->save();
            $message = $message->fresh();

            if ($message->body || $message->subject) {
                $this->pack($message);

                if ($message->subject && $message->type == 'groupchat') {
                    $this->event('subject');
                }

                $this->deliver();
            }
        }
    }
}
