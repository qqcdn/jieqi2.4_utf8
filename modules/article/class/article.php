<?php
jieqi_includedb();
class JieqiArticle extends JieqiObjectData
{
    public function __construct()
    {
        parent::__construct();
        $this->initVar('articleid', JIEQI_TYPE_INT, 0, '序号', false, 11);
        $this->initVar('siteid', JIEQI_TYPE_INT, 0, '网站序号', false, 11);
        $this->initVar('sourceid', JIEQI_TYPE_INT, 0, '来源序号', false, 11);
        $this->initVar('postdate', JIEQI_TYPE_INT, 0, '发表日期', false, 11);
        $this->initVar('lastupdate', JIEQI_TYPE_INT, 0, '最后更新', false, 11);
        $this->initVar('infoupdate', JIEQI_TYPE_INT, 0, '信息更新', false, 11);
        $this->initVar('articlename', JIEQI_TYPE_TXTBOX, '', '小说标题', true, 50);
        $this->initVar('articlecode', JIEQI_TYPE_TXTBOX, '', '拼音标题', false, 100);
        $this->initVar('backupname', JIEQI_TYPE_TXTBOX, '', '备用标题', true, 50);
        $this->initVar('keywords', JIEQI_TYPE_TXTBOX, '', '关键字', false, 250);
        $this->initVar('roles', JIEQI_TYPE_TXTBOX, '', '人物', false, 200);
        $this->initVar('initial', JIEQI_TYPE_TXTBOX, '', '标题首字母', false, 1);
        $this->initVar('authorid', JIEQI_TYPE_INT, 0, '作者序号', false, 11);
        $this->initVar('author', JIEQI_TYPE_TXTBOX, '', '作者', false, 30);
        $this->initVar('posterid', JIEQI_TYPE_INT, 0, '发表者序号', false, 11);
        $this->initVar('poster', JIEQI_TYPE_TXTBOX, '', '发表者', false, 30);
        $this->initVar('agentid', JIEQI_TYPE_INT, 0, '责任编辑序号', false, 11);
        $this->initVar('agent', JIEQI_TYPE_TXTBOX, '', '责任编辑', false, 30);
        $this->initVar('reviewerid', JIEQI_TYPE_INT, 0, '书评版主序号', false, 11);
        $this->initVar('reviewer', JIEQI_TYPE_TXTBOX, '', '书评版主', false, 30);
        $this->initVar('sortid', JIEQI_TYPE_INT, 0, '主类序号', false, 3);
        $this->initVar('typeid', JIEQI_TYPE_INT, 0, '子类序号', false, 3);
        $this->initVar('libid', JIEQI_TYPE_INT, 0, '所属书库', false, 3);
        $this->initVar('intro', JIEQI_TYPE_TXTAREA, '', '内容简介', false, NULL);
        $this->initVar('notice', JIEQI_TYPE_TXTAREA, '', '本书公告', false, NULL);
        $this->initVar('foreword', JIEQI_TYPE_TXTAREA, '', '编辑点评', false, NULL);
        $this->initVar('setting', JIEQI_TYPE_TXTAREA, '', '小说参数', false, NULL);
        $this->initVar('lastvolumeid', JIEQI_TYPE_INT, 0, '末卷序号', false, 11);
        $this->initVar('lastvolume', JIEQI_TYPE_TXTBOX, '', '末卷', false, 250);
        $this->initVar('lastchapterid', JIEQI_TYPE_INT, 0, '最新章节序号', false, 11);
        $this->initVar('lastchapter', JIEQI_TYPE_TXTBOX, '', '最新章节', false, 255);
        $this->initVar('lastsummary', JIEQI_TYPE_TXTAREA, '', '最新章节摘要', false, NULL);
        $this->initVar('chapters', JIEQI_TYPE_INT, 0, '章节数', false, 6);
        $this->initVar('words', JIEQI_TYPE_INT, 0, '字数', false, 11);
        $this->initVar('daywords', JIEQI_TYPE_INT, 0, '日更新字数', false, 11);
        $this->initVar('weekwords', JIEQI_TYPE_INT, 0, '周更新字数', false, 11);
        $this->initVar('monthwords', JIEQI_TYPE_INT, 0, '月更新字数', false, 11);
        $this->initVar('prewords', JIEQI_TYPE_INT, 0, '上月更新字数', false, 11);
        $this->initVar('monthupds', JIEQI_TYPE_INT, 0, '月更新天数', false, 11);
        $this->initVar('preupds', JIEQI_TYPE_INT, 0, '上月更新天数', false, 11);
        $this->initVar('monthupdt', JIEQI_TYPE_INT, 0, '月更新日期记录', false, 11);
        $this->initVar('preupdt', JIEQI_TYPE_INT, 0, '上月更新日期记录', false, 11);
        $this->initVar('lastvisit', JIEQI_TYPE_INT, 0, '最后访问', false, 11);
        $this->initVar('dayvisit', JIEQI_TYPE_INT, 0, '日访问', false, 11);
        $this->initVar('weekvisit', JIEQI_TYPE_INT, 0, '周访问', false, 11);
        $this->initVar('monthvisit', JIEQI_TYPE_INT, 0, '月访问', false, 11);
        $this->initVar('allvisit', JIEQI_TYPE_INT, 0, '总访问', false, 11);
        $this->initVar('previsit', JIEQI_TYPE_INT, 0, '上月访问', false, 11);
        $this->initVar('lastvote', JIEQI_TYPE_INT, 0, '最后推荐', false, 11);
        $this->initVar('dayvote', JIEQI_TYPE_INT, 0, '日推荐', false, 11);
        $this->initVar('weekvote', JIEQI_TYPE_INT, 0, '周推荐', false, 11);
        $this->initVar('monthvote', JIEQI_TYPE_INT, 0, '月推荐', false, 11);
        $this->initVar('allvote', JIEQI_TYPE_INT, 0, '总推荐', false, 11);
        $this->initVar('prevote', JIEQI_TYPE_INT, 0, '上月推荐', false, 11);
        $this->initVar('lastdown', JIEQI_TYPE_INT, 0, '最后下载', false, 11);
        $this->initVar('daydown', JIEQI_TYPE_INT, 0, '日下载', false, 11);
        $this->initVar('weekdown', JIEQI_TYPE_INT, 0, '周下载', false, 11);
        $this->initVar('monthdown', JIEQI_TYPE_INT, 0, '月下载', false, 11);
        $this->initVar('alldown', JIEQI_TYPE_INT, 0, '总下载', false, 11);
        $this->initVar('predown', JIEQI_TYPE_INT, 0, '上月下载', false, 11);
        $this->initVar('lastflower', JIEQI_TYPE_INT, 0, '最后鲜花', false, 11);
        $this->initVar('dayflower', JIEQI_TYPE_INT, 0, '日鲜花', false, 11);
        $this->initVar('weekflower', JIEQI_TYPE_INT, 0, '周鲜花', false, 11);
        $this->initVar('monthflower', JIEQI_TYPE_INT, 0, '月鲜花', false, 11);
        $this->initVar('allflower', JIEQI_TYPE_INT, 0, '总鲜花', false, 11);
        $this->initVar('preflower', JIEQI_TYPE_INT, 0, '上月鲜花', false, 11);
        $this->initVar('lastegg', JIEQI_TYPE_INT, 0, '最后鸡蛋', false, 11);
        $this->initVar('dayegg', JIEQI_TYPE_INT, 0, '日鸡蛋', false, 11);
        $this->initVar('weekegg', JIEQI_TYPE_INT, 0, '周鸡蛋', false, 11);
        $this->initVar('monthegg', JIEQI_TYPE_INT, 0, '月鸡蛋', false, 11);
        $this->initVar('allegg', JIEQI_TYPE_INT, 0, '总鸡蛋', false, 11);
        $this->initVar('preegg', JIEQI_TYPE_INT, 0, '上月鸡蛋', false, 11);
        $this->initVar('lastvipvote', JIEQI_TYPE_INT, 0, '最后月票', false, 11);
        $this->initVar('dayvipvote', JIEQI_TYPE_INT, 0, '日月票', false, 11);
        $this->initVar('weekvipvote', JIEQI_TYPE_INT, 0, '周月票', false, 11);
        $this->initVar('monthvipvote', JIEQI_TYPE_INT, 0, '月月票', false, 11);
        $this->initVar('allvipvote', JIEQI_TYPE_INT, 0, '总月票', false, 11);
        $this->initVar('previpvote', JIEQI_TYPE_INT, 0, '上月月票', false, 11);
        $this->initVar('hotnum', JIEQI_TYPE_INT, 0, '热度指数', false, 11);
        $this->initVar('goodnum', JIEQI_TYPE_INT, 0, '收藏数', false, 11);
        $this->initVar('reviewsnum', JIEQI_TYPE_INT, 0, '评论数', false, 11);
        $this->initVar('ratenum', JIEQI_TYPE_INT, 0, '评分人数', false, 11);
        $this->initVar('ratesum', JIEQI_TYPE_INT, 0, '评分总分', false, 11);
        $this->initVar('rate1', JIEQI_TYPE_INT, 0, '评分1数', false, 11);
        $this->initVar('rate2', JIEQI_TYPE_INT, 0, '评分2数', false, 11);
        $this->initVar('rate3', JIEQI_TYPE_INT, 0, '评分3数', false, 11);
        $this->initVar('rate4', JIEQI_TYPE_INT, 0, '评分4数', false, 11);
        $this->initVar('rate5', JIEQI_TYPE_INT, 0, '评分5数', false, 11);
        $this->initVar('toptime', JIEQI_TYPE_INT, 0, '置顶时间', false, 11);
        $this->initVar('saleprice', JIEQI_TYPE_INT, 0, '销售价格', false, 11);
        $this->initVar('salenum', JIEQI_TYPE_INT, 0, '销售量', false, 11);
        $this->initVar('totalcost', JIEQI_TYPE_INT, 0, '总销售额', false, 11);
        $this->initVar('unionid', JIEQI_TYPE_INT, 0, '书盟网站ID', false, 1);
        $this->initVar('permission', JIEQI_TYPE_INT, 0, '授权类型', false, 1);
        $this->initVar('firstflag', JIEQI_TYPE_INT, 0, '首发标志', false, 1);
        $this->initVar('fullflag', JIEQI_TYPE_INT, 0, '完整标志', false, 1);
        $this->initVar('imgflag', JIEQI_TYPE_INT, 0, '图片标志', false, 1);
        $this->initVar('upaudit', JIEQI_TYPE_INT, 0, '更新审核标志', false, 1);
        $this->initVar('power', JIEQI_TYPE_INT, 0, '访问级别', false, 1);
        $this->initVar('display', JIEQI_TYPE_INT, 0, '显示', false, 1);
        $this->initVar('progress', JIEQI_TYPE_INT, 0, '写作进程', false, 1);
        $this->initVar('issign', JIEQI_TYPE_INT, 0, '是否签约', false, 1);
        $this->initVar('signtime', JIEQI_TYPE_INT, 0, '签约时间', false, 11);
        $this->initVar('buyout', JIEQI_TYPE_INT, 0, '是否买断', false, 1);
        $this->initVar('monthly', JIEQI_TYPE_INT, 0, '是否包月', false, 1);
        $this->initVar('discount', JIEQI_TYPE_INT, 0, '是否打折', false, 1);
        $this->initVar('quality', JIEQI_TYPE_INT, 0, '是否精品', false, 1);
        $this->initVar('isshort', JIEQI_TYPE_INT, 0, '是否短篇', false, 1);
        $this->initVar('inmatch', JIEQI_TYPE_INT, 0, '是否参赛', false, 1);
        $this->initVar('isshare', JIEQI_TYPE_INT, 0, '是否共享', false, 1);
        $this->initVar('rgroup', JIEQI_TYPE_INT, 0, '男生女生', false, 1);
        $this->initVar('ispub', JIEQI_TYPE_INT, 0, '是否出版', false, 1);
        $this->initVar('pubtime', JIEQI_TYPE_INT, 0, '出版时间', false, 11);
        $this->initVar('pubid', JIEQI_TYPE_INT, 0, '出版社ID', false, 11);
        $this->initVar('pubhouse', JIEQI_TYPE_TXTBOX, '', '出版社名', false, 100);
        $this->initVar('pubprice', JIEQI_TYPE_INT, 0, '出版价格', false, 11);
        $this->initVar('pubpages', JIEQI_TYPE_INT, 0, '书籍页码', false, 11);
        $this->initVar('pubisbn', JIEQI_TYPE_TXTBOX, '', 'ISBN代码', false, 100);
        $this->initVar('pubinfo', JIEQI_TYPE_TXTAREA, 0, '出版信息', false, NULL);
        $this->initVar('buysid', JIEQI_TYPE_INT, 0, '购买网站ID', false, 11);
        $this->initVar('buysite', JIEQI_TYPE_TXTBOX, '', '购买网站', false, 100);
        $this->initVar('buyurl', JIEQI_TYPE_TXTBOX, '', '购买网址', false, 200);
        $this->initVar('buyprice', JIEQI_TYPE_INT, 0, '购买价格', false, 11);
        $this->initVar('buyinfo', JIEQI_TYPE_TXTAREA, 0, '购买信息', false, NULL);
        $this->initVar('freetime', JIEQI_TYPE_INT, 0, '免费更新时间', false, 11);
        $this->initVar('freewords', JIEQI_TYPE_INT, 0, '免费字数', false, 11);
        $this->initVar('freestart', JIEQI_TYPE_INT, 0, '限免开始时间', false, 11);
        $this->initVar('freeend', JIEQI_TYPE_INT, 0, '限免结束时间', false, 11);
        $this->initVar('isvip', JIEQI_TYPE_INT, 0, '是否VIP', false, 1);
        $this->initVar('viptime', JIEQI_TYPE_INT, 0, 'VIP更新时间', false, 11);
        $this->initVar('vipid', JIEQI_TYPE_INT, 0, 'VIP关联ID', false, 11);
        $this->initVar('vippubid', JIEQI_TYPE_INT, 0, 'VIP编辑组ID', false, 11);
        $this->initVar('vipchapters', JIEQI_TYPE_INT, 0, 'VIP章节数', false, 6);
        $this->initVar('vipwords', JIEQI_TYPE_INT, 0, 'VIP字数', false, 11);
        $this->initVar('vipvolumeid', JIEQI_TYPE_INT, 0, 'VIP最新分卷ID', false, 11);
        $this->initVar('vipvolume', JIEQI_TYPE_TXTBOX, '', 'VIP最新分卷', false, 255);
        $this->initVar('vipchapterid', JIEQI_TYPE_INT, 0, 'VIP最新章节ID', false, 11);
        $this->initVar('vipchapter', JIEQI_TYPE_TXTBOX, '', 'VIP最新章节', false, 255);
        $this->initVar('vipsummary', JIEQI_TYPE_TXTAREA, '', 'VIP最新章节摘要', false, NULL);
    }
}
class JieqiArticleHandler extends JieqiObjectHandler
{
    public function __construct($db = '')
    {
        parent::__construct($db);
        $this->basename = 'article';
        $this->autoid = 'articleid';
        $this->dbname = 'article_article';
    }
    public function getCoverInfo($imgflag)
    {
        global $jieqiConfigs;
        if (!isset($jieqiConfigs['article'])) {
            jieqi_getconfigs('article', 'configs');
        }
        $ret = array('stype' => '', 'ltype' => '');
        if (0 < ($imgflag & 1)) {
            $ret['stype'] = $jieqiConfigs['article']['imagetype'];
        }
        if (0 < ($imgflag & 2)) {
            $ret['ltype'] = $jieqiConfigs['article']['imagetype'];
        }
        $imgtype = $imgflag >> 2;
        if (0 < $imgtype) {
            $imgtary = array(1 => '.gif', 2 => '.jpg', 3 => '.jpeg', 4 => '.png', 5 => '.bmp');
            $tmpvar = round($imgtype & 7);
            if (isset($imgtary[$tmpvar])) {
                $ret['stype'] = $imgtary[$tmpvar];
            }
            $tmpvar = round($imgtype >> 3);
            if (isset($imgtary[$tmpvar])) {
                $ret['ltype'] = $imgtary[$tmpvar];
            }
        }
        return $ret;
    }
}