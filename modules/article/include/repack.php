<?php

function article_repack($id, $params = array(), $syn = 0)
{
    global $jieqiConfigs;
    global $jieqiModules;
    global $jieqi_file_postfix;
    global $jieqiTpl;
    global $jieqiSort;
    if (!$syn) {
        $article_static_url = empty($jieqiConfigs['article']['staticurl']) ? $jieqiModules['article']['url'] : $jieqiConfigs['article']['staticurl'];
        $url = $article_static_url . '/makepack.php?key=' . urlencode(md5(JIEQI_DB_USER . JIEQI_DB_PASS . JIEQI_DB_NAME)) . '&id=' . intval($id);
        $url = trim($url);
        if (strtolower(substr($url, 0, 7)) != 'http://') {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . $url;
        }
        foreach ($params as $k => $v) {
            if ($v) {
                $url .= '&packflag[]=' . urlencode($k);
            }
        }
        return jieqi_socket_url($url);
    } else {
        $article_handler = JieqiArticleHandler::getInstance('JieqiArticleHandler');
        $article = $article_handler->get($id);
        if (!is_object($article)) {
            return false;
        } else {
            $package = new JieqiPackage($id);
            $package->initPackage($article->getVars('n'), false);
            $chapter_handler = JieqiChapterHandler::getInstance('JieqiChapterHandler');
            $criteria = new CriteriaCompo(new Criteria('articleid', $id, '='));
            $criteria->setSort('chapterorder ASC, chapterid');
            $criteria->setOrder('ASC');
            $res = $chapter_handler->queryObjects($criteria);
            $i = 0;
            $articlewords = 0;
            while ($chapter = $chapter_handler->getObject($res)) {
                $package->chapters[$i] = $chapter->getVars('n');
                $i++;
                if ($chapter->getVar('chaptertype', 'n') == 0) {
                    $articlewords = $articlewords + intval($chapter->getVar('words', 'n'));
                }
                if ($chapter->getVar('chapterorder', 'n') != $i) {
                    $chapter->setVar('chapterorder', $i);
                    $chapter_handler->insert($chapter);
                }
            }
            $changeflag = false;
            if ($article->getVar('chapters', 'n') != $i) {
                $article->setVar('chapters', $i);
                $changeflag = true;
            }
            if (intval($article->getVar('words', 'n')) < $articlewords) {
                $article->setVar('words', $articlewords);
                $changeflag = true;
            }
            if ($articlewords != intval($article->getVar('freewords', 'n'))) {
                $article->setVar('freewords', $articlewords);
                $changeflag = true;
            }
            if ($changeflag) {
                $article_handler->insert($article);
            }
            $package->isload = true;
            if ($params['makeopf']) {
                $package->createOPF();
            }
            if ($params['maketxtjs']) {
                $chaptercount = count($package->chapters);
                for ($i = 1; $i <= $chaptercount; $i++) {
                    if ($package->chapters[$i - 1]['chaptertype'] == 0) {
                        $package->makeTxtjs($i);
                    }
                }
            }
            if ($params['makezip']) {
                $package->makezip();
            }
            if ($params['makefull']) {
                $package->makefulltext();
            }
            if ($params['makeumd']) {
                $package->makeumd();
            }
            if ($params['maketxtfull']) {
                $package->maketxtfull();
            }
            if ($params['makejar']) {
                $package->makejar();
            }
            if ($params['makehtml']) {
                $package->makeRead('edit', 1);
            } else {
                if ($params['makechapter']) {
                    $package->makeRead('edit', 1);
                } else {
                    if ($params['makeindex']) {
                        $package->makeRead('edit', 0);
                    }
                }
            }
            return true;
        }
    }
}
include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/article.php';
include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/chapter.php';
include_once $GLOBALS['jieqiModules']['article']['path'] . '/class/package.php';