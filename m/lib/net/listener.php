<?php

class JieqiRequest_Listener extends JieqiObject
{
    public $_id;
    public function __construct()
    {
        parent::__construct();
        $this->_id = md5(uniqid('http_request_', 1));
    }
    public function getId()
    {
        return $this->_id;
    }
    public function update(&$subject, $event, $data = NULL)
    {
        echo 'Notified of event: \'' . $event . '\'' . "\n" . '';
        if (NULL !== $data) {
            echo 'Additional data: ';
            var_dump($data);
        }
    }
}