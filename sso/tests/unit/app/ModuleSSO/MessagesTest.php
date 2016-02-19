<?php

use ModuleSSO\Messages;

class MessagesTest extends PHPUnit_Framework_TestCase
{
    public function testInsert()
    {
        //1. test default class
        $_SESSION[\ModuleSSO::MESSAGES_KEY] = array();
        Messages::insert('Text');

        $this->assertEquals($_SESSION[\ModuleSSO::MESSAGES_KEY], array(array('class' => 'success', 'text' => 'Text')));

        //2. test any class
        $_SESSION[\ModuleSSO::MESSAGES_KEY] = array();
        Messages::insert('Text', 'anyclass');

        $this->assertEquals($_SESSION[\ModuleSSO::MESSAGES_KEY], array(array('class' => 'anyclass', 'text' => 'Text')));

        //3. test multiple messages
        $_SESSION[\ModuleSSO::MESSAGES_KEY] = array();
        Messages::insert('m1');
        Messages::insert('m2', 'warn');
        Messages::insert('m3', 'ok');

        $this->assertEquals(
            $_SESSION[\ModuleSSO::MESSAGES_KEY],
            array(
                array('class' => 'success', 'text' => 'm1'),
                array('class' => 'warn', 'text' => 'm2'),
                array('class' => 'ok', 'text' => 'm3')
            )
        );
    }

    public function testShowMessages()
    {
        //insert some messages
        $_SESSION[\ModuleSSO::MESSAGES_KEY] = array();
        Messages::insert('m1');
        Messages::insert('m2', 'warn');
        Messages::insert('m3', 'ok');

        $messagesHTML = '<div id="messages">';
        $messagesHTML .= '<div class="message success">m1</div>';
        $messagesHTML .= '<div class="message warn">m2</div>';
        $messagesHTML .= '<div class="message ok">m3</div>';
        $messagesHTML .= '</div>';

        //1. test HTML code
        $this->assertEquals($messagesHTML, Messages::showMessages());

        //2. check if $_SESSION messages array is empty now
        $this->assertEquals(array(),  $_SESSION[\ModuleSSO::MESSAGES_KEY]);

    }
}