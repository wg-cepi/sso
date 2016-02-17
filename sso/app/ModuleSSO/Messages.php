<?php
namespace ModuleSSO;

class Messages
{
    /**
     * Inserts message into $_SESSION
     *
     * @param string $text Body of the message
     * @param string $class HTML class attribute of message for CSS
     */
    public static function insert($text, $class = 'success')
    {
        $_SESSION[\ModuleSSO::MESSAGES_KEY][] = array('class' => $class, 'text' => $text);
    }

    /**
     * Displays and removes messages from $_SESSION
     * Messages are wrapped to <div> element with specific class and content
     *
     * @return string
     */
    public static function showMessages()
    {
        if(!empty($_SESSION[\ModuleSSO::MESSAGES_KEY])) {
            $str = '<div id="messages">';
            foreach ($_SESSION[\ModuleSSO::MESSAGES_KEY] as $k => $message) {
                $str .= '<div class="message ' . $message['class'] . '">' . $message['text'] . '</div>';
                unset($_SESSION[\ModuleSSO::MESSAGES_KEY][$k]);
            }
            $str .= '</div>';
            return $str;
        }
        return '';
    }
}