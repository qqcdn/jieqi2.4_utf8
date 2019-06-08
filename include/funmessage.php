<?php

function jieqi_sendmessage($params)
{
    global $message_handler;
    global $query;
    if (!is_array($params) || empty($params['title'])) {
        return false;
    }
    $message = array();
    $message['siteid'] = isset($params['siteid']) ? intval($params['siteid']) : JIEQI_SITE_ID;
    $message['postdate'] = isset($params['postdate']) ? intval($params['postdate']) : JIEQI_NOW_TIME;
    $message['fromid'] = isset($params['fromid']) ? intval($params['fromid']) : 0;
    $message['fromname'] = isset($params['fromname']) ? trim($params['fromname']) : $_SESSION['jieqiUserName'];
    $message['toid'] = isset($params['toid']) ? intval($params['toid']) : 0;
    $message['toname'] = isset($params['toname']) ? trim($params['toname']) : '';
    $message['title'] = isset($params['title']) ? trim($params['title']) : '';
    $message['content'] = isset($params['content']) ? trim($params['content']) : '';
    $message['messagetype'] = isset($params['messagetype']) ? intval($params['messagetype']) : 0;
    $message['isread'] = isset($params['isread']) ? intval($params['isread']) : 0;
    $message['fromdel'] = isset($params['fromdel']) ? intval($params['fromdel']) : 0;
    $message['todel'] = isset($params['todel']) ? intval($params['todel']) : 0;
    $message['enablebbcode'] = isset($params['enablebbcode']) ? intval($params['enablebbcode']) : 1;
    $message['enablehtml'] = isset($params['enablehtml']) ? intval($params['enablehtml']) : 0;
    $message['enablesmilies'] = isset($params['enablesmilies']) ? intval($params['enablesmilies']) : 0;
    $message['attachsig'] = isset($params['attachsig']) ? intval($params['attachsig']) : 0;
    $message['attachment'] = isset($params['attachment']) ? intval($params['attachment']) : 0;
    if (!isset($message_handler) || !is_a($message_handler, 'JieqiUsersHandler')) {
        include_once JIEQI_ROOT_PATH . '/class/message.php';
        $message_handler = JieqiMessageHandler::getInstance('JieqiMessageHandler');
    }
    $newMessage = $message_handler->create();
    $newMessage->setVars($message);
    if ($message_handler->insert($newMessage)) {
        if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
            jieqi_includedb();
            $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
        }
        $sql = 'SELECT * FROM ' . jieqi_dbprefix('system_online') . ' WHERE uid = ' . intval($message['toid']) . ' ORDER BY updatetime DESC LIMIT 0, 1';
        $query->execute($sql);
        $row = $query->getRow();
        if (is_array($row)) {
            jieqi_upusersession($row['sid'], array('jieqiNewMessage+' => 1));
        }
        return $newMessage;
    } else {
        return false;
    }
}