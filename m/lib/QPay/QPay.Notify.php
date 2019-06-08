<?php

class QPayNotify extends QPayNotifyReply
{
    public final function Handle($needSign = true)
    {
        $msg = 'OK';
        $result = QPayApi::notify(array($this, 'NotifyCallBack'), $msg);
        if ($result == false) {
            $this->SetReturn_code('FAIL');
            $this->SetReturn_msg($msg);
            $this->ReplyNotify(false);
            return NULL;
        } else {
            $this->SetReturn_code('SUCCESS');
            $this->SetReturn_msg('OK');
        }
        $this->ReplyNotify($needSign);
    }
    public function NotifyProcess($data, &$msg)
    {
        return true;
    }
    public final function NotifyCallBack($data)
    {
        $msg = 'OK';
        $result = $this->NotifyProcess($data, $msg);
        if ($result == true) {
            $this->SetReturn_code('SUCCESS');
            $this->SetReturn_msg('OK');
        } else {
            $this->SetReturn_code('FAIL');
            $this->SetReturn_msg($msg);
        }
        return $result;
    }
    private final function ReplyNotify($needSign = true)
    {
        if ($needSign == true && $this->GetReturn_code($return_code) == 'SUCCESS') {
            $this->SetSign();
        }
        QPayApi::replyNotify($this->ToXml());
    }
}