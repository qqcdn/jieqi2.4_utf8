<?php

class DOMEvent
{
    /**
     * Returns a boolean indicating whether the event bubbles up through the DOM or not.
     *
     * @var unknown_type
     */
    public $bubbles = true;
    /**
     * Returns a boolean indicating whether the event is cancelable.
     *
     * @var unknown_type
     */
    public $cancelable = true;
    /**
     * Returns a reference to the currently registered target for the event.
     *
     * @var unknown_type
     */
    public $currentTarget;
    /**
     * Returns detail about the event, depending on the type of event.
     *
     * @var unknown_type
     * @link http://developer.mozilla.org/en/DOM/event.detail
     */
    public $detail;
    /**
     * Used to indicate which phase of the event flow is currently being evaluated.
     *
     * NOT IMPLEMENTED
     *
     * @var unknown_type
     * @link http://developer.mozilla.org/en/DOM/event.eventPhase
     */
    public $eventPhase;
    /**
     * The explicit original target of the event (Mozilla-specific).
     *
     * NOT IMPLEMENTED
     *
     * @var unknown_type
     */
    public $explicitOriginalTarget;
    /**
     * The original target of the event, before any retargetings (Mozilla-specific).
     *
     * NOT IMPLEMENTED
     *
     * @var unknown_type
     */
    public $originalTarget;
    /**
     * Identifies a secondary target for the event.
     *
     * @var unknown_type
     */
    public $relatedTarget;
    /**
     * Returns a reference to the target to which the event was originally dispatched.
     *
     * @var unknown_type
     */
    public $target;
    /**
     * Returns the time that the event was created.
     *
     * @var unknown_type
     */
    public $timeStamp;
    /**
     * Returns the name of the event (case-insensitive).
     */
    public $type;
    public $runDefault = true;
    public $data;
    public function __construct($data)
    {
        foreach ($data as $k => $v) {
            $this->{$k} = $v;
        }
        if (!$this->timeStamp) {
            $this->timeStamp = time();
        }
    }
    public function preventDefault()
    {
        $this->runDefault = false;
    }
    public function stopPropagation()
    {
        $this->bubbles = false;
    }
}
class DOMDocumentWrapper
{
    /**
     * @var DOMDocument
     */
    public $document;
    public $id;
    /**
     * @todo Rewrite as method and quess if null.
     * @var unknown_type
     */
    public $contentType = '';
    public $xpath;
    public $uuid = 0;
    public $data = array();
    public $dataNodes = array();
    public $events = array();
    public $eventsNodes = array();
    public $eventsGlobal = array();
    /**
     * @TODO iframes support http://code.google.com/p/phpquery/issues/detail?id=28
     * @var unknown_type
     */
    public $frames = array();
    /**
     * Document root, by default equals to document itself.
     * Used by documentFragments.
     *
     * @var DOMNode
     */
    public $root;
    public $isDocumentFragment;
    public $isXML = false;
    public $isXHTML = false;
    public $isHTML = false;
    public $charset;
    public function __construct($markup = NULL, $contentType = NULL, $newDocumentID = NULL)
    {
        if (isset($markup)) {
            $this->load($markup, $contentType, $newDocumentID);
        }
        $this->id = $newDocumentID ? $newDocumentID : md5(microtime());
    }
    public function load($markup, $contentType = NULL, $newDocumentID = NULL)
    {
        $this->contentType = strtolower($contentType);
        if ($markup instanceof DOMDOCUMENT) {
            $this->document = $markup;
            $this->root = $this->document;
            $this->charset = $this->document->encoding;
        } else {
            $loaded = $this->loadMarkup($markup);
        }
        if ($loaded) {
            $this->document->preserveWhiteSpace = true;
            $this->xpath = new DOMXPath($this->document);
            $this->afterMarkupLoad();
            return true;
        }
        return false;
    }
    protected function afterMarkupLoad()
    {
        if ($this->isXHTML) {
            $this->xpath->registerNamespace('html', 'http://www.w3.org/1999/xhtml');
        }
    }
    protected function loadMarkup($markup)
    {
        $loaded = false;
        if ($this->contentType) {
            self::debug('Load markup for content type ' . $this->contentType);
            list($contentType, $charset) = $this->contentTypeToArray($this->contentType);
            switch ($contentType) {
                case 'text/html':
                    phpQuery::debug('Loading HTML, content type \'' . $this->contentType . '\'');
                    $loaded = $this->loadMarkupHTML($markup, $charset);
                    break;
                case 'text/xml':
                case 'application/xhtml+xml':
                    phpQuery::debug('Loading XML, content type \'' . $this->contentType . '\'');
                    $loaded = $this->loadMarkupXML($markup, $charset);
                    break;
                default:
                    if (strpos('xml', $this->contentType) !== false) {
                        phpQuery::debug('Loading XML, content type \'' . $this->contentType . '\'');
                        $loaded = $this->loadMarkupXML($markup, $charset);
                    } else {
                        phpQuery::debug('Could not determine document type from content type \'' . $this->contentType . '\'');
                    }
            }
        } else {
            if ($this->isXML($markup)) {
                phpQuery::debug('Loading XML, isXML() == true');
                $loaded = $this->loadMarkupXML($markup);
                if (!$loaded && $this->isXHTML) {
                    phpQuery::debug('Loading as XML failed, trying to load as HTML, isXHTML == true');
                    $loaded = $this->loadMarkupHTML($markup);
                }
            } else {
                phpQuery::debug('Loading HTML, isXML() == false');
                $loaded = $this->loadMarkupHTML($markup);
            }
        }
        return $loaded;
    }
    protected function loadMarkupReset()
    {
        $this->isXML = $this->isXHTML = $this->isHTML = false;
    }
    protected function documentCreate($charset, $version = '1.0')
    {
        if (!$version) {
            $version = '1.0';
        }
        $this->document = new DOMDocument($version, $charset);
        $this->charset = $this->document->encoding;
        $this->document->formatOutput = true;
        $this->document->preserveWhiteSpace = true;
    }
    protected function loadMarkupHTML($markup, $requestedCharset = NULL)
    {
        if (phpQuery::$debug) {
            phpQuery::debug('Full markup load (HTML): ' . substr($markup, 0, 250));
        }
        $this->loadMarkupReset();
        $this->isHTML = true;
        if (!isset($this->isDocumentFragment)) {
            $this->isDocumentFragment = self::isDocumentFragmentHTML($markup);
        }
        $charset = NULL;
        $documentCharset = $this->charsetFromHTML($markup);
        $addDocumentCharset = false;
        if ($documentCharset) {
            $charset = $documentCharset;
        } else {
            if ($requestedCharset) {
                $charset = $requestedCharset;
            }
        }
        if (!$charset) {
            $charset = phpQuery::$defaultCharset;
        }
        if (!$documentCharset) {
            $documentCharset = 'ISO-8859-1';
            $addDocumentCharset = true;
        }
        $requestedCharset = strtoupper($requestedCharset);
        $documentCharset = strtoupper($documentCharset);
        phpQuery::debug('DOC: ' . $documentCharset . ' REQ: ' . $requestedCharset);
        if ($requestedCharset && $documentCharset && $requestedCharset !== $documentCharset) {
            phpQuery::debug('CHARSET CONVERT');
            if (function_exists('mb_detect_encoding')) {
                $possibleCharsets = array($documentCharset, $requestedCharset, 'AUTO');
                $docEncoding = mb_detect_encoding($markup, implode(', ', $possibleCharsets));
                if (!$docEncoding) {
                    $docEncoding = $documentCharset;
                }
                phpQuery::debug('DETECTED \'' . $docEncoding . '\'');
                if ($docEncoding !== $documentCharset) {
                }
                if ($docEncoding !== $requestedCharset) {
                    phpQuery::debug('CONVERT ' . $docEncoding . ' => ' . $requestedCharset);
                    $markup = mb_convert_encoding($markup, $requestedCharset, $docEncoding);
                    $markup = $this->charsetAppendToHTML($markup, $requestedCharset);
                    $charset = $requestedCharset;
                }
            } else {
                phpQuery::debug('TODO: charset conversion without mbstring...');
            }
        }
        $return = false;
        if ($this->isDocumentFragment) {
            phpQuery::debug('Full markup load (HTML), DocumentFragment detected, using charset \'' . $charset . '\'');
            $return = $this->documentFragmentLoadMarkup($this, $charset, $markup);
        } else {
            if ($addDocumentCharset) {
                phpQuery::debug('Full markup load (HTML), appending charset: \'' . $charset . '\'');
                $markup = $this->charsetAppendToHTML($markup, $charset);
            }
            phpQuery::debug('Full markup load (HTML), documentCreate(\'' . $charset . '\')');
            $this->documentCreate($charset);
            $return = phpQuery::$debug === 2 ? $this->document->loadHTML($markup) : @$this->document->loadHTML($markup);
            if ($return) {
                $this->root = $this->document;
            }
        }
        if ($return && !$this->contentType) {
            $this->contentType = 'text/html';
        }
        return $return;
    }
    protected function loadMarkupXML($markup, $requestedCharset = NULL)
    {
        if (phpQuery::$debug) {
            phpQuery::debug('Full markup load (XML): ' . substr($markup, 0, 250));
        }
        $this->loadMarkupReset();
        $this->isXML = true;
        $isContentTypeXHTML = $this->isXHTML();
        $isMarkupXHTML = $this->isXHTML($markup);
        if ($isContentTypeXHTML || $isMarkupXHTML) {
            self::debug('Full markup load (XML), XHTML detected');
            $this->isXHTML = true;
        }
        if (!isset($this->isDocumentFragment)) {
            $this->isDocumentFragment = $this->isXHTML ? self::isDocumentFragmentXHTML($markup) : self::isDocumentFragmentXML($markup);
        }
        $charset = NULL;
        $documentCharset = $this->charsetFromXML($markup);
        if (!$documentCharset) {
            if ($this->isXHTML) {
                $documentCharset = $this->charsetFromHTML($markup);
                if ($documentCharset) {
                    phpQuery::debug('Full markup load (XML), appending XHTML charset \'' . $documentCharset . '\'');
                    $this->charsetAppendToXML($markup, $documentCharset);
                    $charset = $documentCharset;
                }
            }
            if (!$documentCharset) {
                $charset = $requestedCharset;
            }
        } else {
            if ($requestedCharset) {
                $charset = $requestedCharset;
            }
        }
        if (!$charset) {
            $charset = phpQuery::$defaultCharset;
        }
        if ($requestedCharset && $documentCharset && $requestedCharset != $documentCharset) {
        }
        $return = false;
        if ($this->isDocumentFragment) {
            phpQuery::debug('Full markup load (XML), DocumentFragment detected, using charset \'' . $charset . '\'');
            $return = $this->documentFragmentLoadMarkup($this, $charset, $markup);
        } else {
            if ($isContentTypeXHTML && !$isMarkupXHTML) {
                if (!$documentCharset) {
                    phpQuery::debug('Full markup load (XML), appending charset \'' . $charset . '\'');
                    $markup = $this->charsetAppendToXML($markup, $charset);
                }
            }
            $this->documentCreate($charset);
            if (phpversion() < 5.1) {
                $this->document->resolveExternals = true;
                $return = phpQuery::$debug === 2 ? $this->document->loadXML($markup) : @$this->document->loadXML($markup);
            } else {
                $libxmlStatic = phpQuery::$debug === 2 ? LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_NONET : LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_NONET | LIBXML_NOWARNING | LIBXML_NOERROR;
                $return = $this->document->loadXML($markup, $libxmlStatic);
            }
            if ($return) {
                $this->root = $this->document;
            }
        }
        if ($return) {
            if (!$this->contentType) {
                if ($this->isXHTML) {
                    $this->contentType = 'application/xhtml+xml';
                } else {
                    $this->contentType = 'text/xml';
                }
            }
            return $return;
        } else {
            throw new Exception('Error loading XML markup');
        }
    }
    protected function isXHTML($markup = NULL)
    {
        if (!isset($markup)) {
            return strpos($this->contentType, 'xhtml') !== false;
        }
        return strpos($markup, '<!DOCTYPE html') !== false;
    }
    protected function isXML($markup)
    {
        return strpos(substr($markup, 0, 100), '<' . '?xml') !== false;
    }
    protected function contentTypeToArray($contentType)
    {
        $matches = explode(';', trim(strtolower($contentType)));
        if (isset($matches[1])) {
            $matches[1] = explode('=', $matches[1]);
            $matches[1] = isset($matches[1][1]) && trim($matches[1][1]) ? $matches[1][1] : $matches[1][0];
        } else {
            $matches[1] = NULL;
        }
        return $matches;
    }
    protected function contentTypeFromHTML($markup)
    {
        $matches = array();
        preg_match('@<meta[^>]+http-equiv\\s*=\\s*(["|\'])Content-Type\\1([^>]+?)>@i', $markup, $matches);
        if (isset($matches[0])) {
            preg_match('@content\\s*=\\s*(["|\'])(.+?)\\1@', $matches[0], $matches);
            if (!isset($matches[0])) {
                return array(NULL, NULL);
            }
            return $this->contentTypeToArray($matches[2]);
        } else {
            preg_match('@<meta[^>]+charset\\s*=\\s*(["|\'])([^"\']+)\\1([^>]+?)>@i', $markup, $matches);
            if (isset($matches[2])) {
                return array('text/html', trim($matches[2]));
            } else {
            }
        }
    }
    protected function charsetFromHTML($markup)
    {
        $contentType = $this->contentTypeFromHTML($markup);
        return $contentType[1];
    }
    protected function charsetFromXML($markup)
    {
        $matches = NULL;
        preg_match('@<' . '?xml[^>]+encoding\\s*=\\s*(["|\'])(.*?)\\1@i', $markup, $matches);
        return isset($matches[2]) ? strtolower($matches[2]) : NULL;
    }
    protected function charsetFixHTML($markup)
    {
        $matches = array();
        preg_match('@\\s*<meta[^>]+http-equiv\\s*=\\s*(["|\'])Content-Type\\1([^>]+?)>@i', $markup, $matches, PREG_OFFSET_CAPTURE);
        if (!isset($matches[0])) {
            return NULL;
        }
        $metaContentType = $matches[0][0];
        $markup = substr($markup, 0, $matches[0][1]) . substr($markup, $matches[0][1] + strlen($metaContentType));
        $headStart = stripos($markup, '<head>');
        $markup = substr($markup, 0, $headStart + 6) . $metaContentType . substr($markup, $headStart + 6);
        return $markup;
    }
    protected function charsetAppendToHTML($html, $charset, $xhtml = false)
    {
        $html = preg_replace('@\\s*<meta[^>]+http-equiv\\s*=\\s*(["|\'])Content-Type\\1([^>]+?)>@i', '', $html);
        $meta = '<meta http-equiv="Content-Type" content="text/html;charset=' . $charset . '" ' . ($xhtml ? '/' : '') . '>';
        if (strpos($html, '<head') === false) {
            if (strpos($html, '<html') === false) {
                return $meta . $html;
            } else {
                return preg_replace('@<html(.*?)(?(?<!\\?)>)@s', '<html\\1><head>' . $meta . '</head>', $html);
            }
        } else {
            return preg_replace('@<head(.*?)(?(?<!\\?)>)@s', '<head\\1>' . $meta, $html);
        }
    }
    protected function charsetAppendToXML($markup, $charset)
    {
        $declaration = '<' . '?xml version="1.0" encoding="' . $charset . '"?' . '>';
        return $declaration . $markup;
    }
    public static function isDocumentFragmentHTML($markup)
    {
        return stripos($markup, '<html') === false && stripos($markup, '<!doctype') === false;
    }
    public static function isDocumentFragmentXML($markup)
    {
        return stripos($markup, '<' . '?xml') === false;
    }
    public static function isDocumentFragmentXHTML($markup)
    {
        return self::isDocumentFragmentHTML($markup);
    }
    public function importAttr($value)
    {
    }
    public function import($source, $sourceCharset = NULL)
    {
        $return = array();
        if ($source instanceof DOMNODE && !$source instanceof DOMNODELIST) {
            $source = array($source);
        }
        if (is_array($source) || $source instanceof DOMNODELIST) {
            self::debug('Importing nodes to document');
            foreach ($source as $node) {
                $return[] = $this->document->importNode($node, true);
            }
        } else {
            $fake = $this->documentFragmentCreate($source, $sourceCharset);
            if ($fake === false) {
                throw new Exception('Error loading documentFragment markup');
            } else {
                return $this->import($fake->root->childNodes);
            }
        }
        return $return;
    }
    protected function documentFragmentCreate($source, $charset = NULL)
    {
        $fake = new DOMDocumentWrapper();
        $fake->contentType = $this->contentType;
        $fake->isXML = $this->isXML;
        $fake->isHTML = $this->isHTML;
        $fake->isXHTML = $this->isXHTML;
        $fake->root = $fake->document;
        if (!$charset) {
            $charset = $this->charset;
        }
        if ($source instanceof DOMNODE && !$source instanceof DOMNODELIST) {
            $source = array($source);
        }
        if (is_array($source) || $source instanceof DOMNODELIST) {
            if (!$this->documentFragmentLoadMarkup($fake, $charset)) {
                return false;
            }
            $nodes = $fake->import($source);
            foreach ($nodes as $node) {
                $fake->root->appendChild($node);
            }
        } else {
            $this->documentFragmentLoadMarkup($fake, $charset, $source);
        }
        return $fake;
    }
    private function documentFragmentLoadMarkup($fragment, $charset, $markup = NULL)
    {
        $fragment->isDocumentFragment = false;
        if ($fragment->isXML) {
            if ($fragment->isXHTML) {
                $fragment->loadMarkupXML('<?xml version="1.0" encoding="' . $charset . '"?>' . '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" ' . '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . '<fake xmlns="http://www.w3.org/1999/xhtml">' . $markup . '</fake>');
                $fragment->root = $fragment->document->firstChild->nextSibling;
            } else {
                $fragment->loadMarkupXML('<?xml version="1.0" encoding="' . $charset . '"?><fake>' . $markup . '</fake>');
                $fragment->root = $fragment->document->firstChild;
            }
        } else {
            $markup2 = phpQuery::$defaultDoctype . '<html><head><meta http-equiv="Content-Type" content="text/html;charset=' . $charset . '"></head>';
            $noBody = strpos($markup, '<body') === false;
            if ($noBody) {
                $markup2 .= '<body>';
            }
            $markup2 .= $markup;
            if ($noBody) {
                $markup2 .= '</body>';
            }
            $markup2 .= '</html>';
            $fragment->loadMarkupHTML($markup2);
            $fragment->root = $noBody ? $fragment->document->firstChild->nextSibling->firstChild->nextSibling : $fragment->document->firstChild->nextSibling->firstChild->nextSibling;
        }
        if (!$fragment->root) {
            return false;
        }
        $fragment->isDocumentFragment = true;
        return true;
    }
    protected function documentFragmentToMarkup($fragment)
    {
        phpQuery::debug('documentFragmentToMarkup');
        $tmp = $fragment->isDocumentFragment;
        $fragment->isDocumentFragment = false;
        $markup = $fragment->markup();
        if ($fragment->isXML) {
            $markup = substr($markup, 0, strrpos($markup, '</fake>'));
            if ($fragment->isXHTML) {
                $markup = substr($markup, strpos($markup, '<fake') + 43);
            } else {
                $markup = substr($markup, strpos($markup, '<fake>') + 6);
            }
        } else {
            $markup = substr($markup, strpos($markup, '<body>') + 6);
            $markup = substr($markup, 0, strrpos($markup, '</body>'));
        }
        $fragment->isDocumentFragment = $tmp;
        if (phpQuery::$debug) {
            phpQuery::debug('documentFragmentToMarkup: ' . substr($markup, 0, 150));
        }
        return $markup;
    }
    public function markup($nodes = NULL, $innerMarkup = false)
    {
        if (isset($nodes) && count($nodes) == 1 && $nodes[0] instanceof DOMDOCUMENT) {
            $nodes = NULL;
        }
        if (isset($nodes)) {
            $markup = '';
            if (!is_array($nodes) && !$nodes instanceof DOMNODELIST) {
                $nodes = array($nodes);
            }
            if ($this->isDocumentFragment && !$innerMarkup) {
                foreach ($nodes as $i => $node) {
                    if ($node->isSameNode($this->root)) {
                        $nodes = array_slice($nodes, 0, $i) + phpQuery::DOMNodeListToArray($node->childNodes) + array_slice($nodes, $i + 1);
                    }
                }
            }
            if ($this->isXML && !$innerMarkup) {
                self::debug('Getting outerXML with charset \'' . $this->charset . '\'');
                foreach ($nodes as $node) {
                    $markup .= $this->document->saveXML($node);
                }
            } else {
                $loop = array();
                if ($innerMarkup) {
                    foreach ($nodes as $node) {
                        if ($node->childNodes) {
                            foreach ($node->childNodes as $child) {
                                $loop[] = $child;
                            }
                        } else {
                            $loop[] = $node;
                        }
                    }
                } else {
                    $loop = $nodes;
                }
                self::debug('Getting markup, moving selected nodes (' . count($loop) . ') to new DocumentFragment');
                $fake = $this->documentFragmentCreate($loop);
                $markup = $this->documentFragmentToMarkup($fake);
            }
            if ($this->isXHTML) {
                self::debug('Fixing XHTML');
                $markup = self::markupFixXHTML($markup);
            }
            self::debug('Markup: ' . substr($markup, 0, 250));
            return $markup;
        } else {
            if ($this->isDocumentFragment) {
                self::debug('Getting markup, DocumentFragment detected');
                $markup = $this->documentFragmentToMarkup($this);
                return $markup;
            } else {
                self::debug('Getting markup (' . ($this->isXML ? 'XML' : 'HTML') . '), final with charset \'' . $this->charset . '\'');
                $markup = $this->isXML ? $this->document->saveXML() : $this->document->saveHTML();
                if ($this->isXHTML) {
                    self::debug('Fixing XHTML');
                    $markup = self::markupFixXHTML($markup);
                }
                self::debug('Markup: ' . substr($markup, 0, 250));
                return $markup;
            }
        }
    }
    protected static function markupFixXHTML($markup)
    {
        $markup = self::expandEmptyTag('script', $markup);
        $markup = self::expandEmptyTag('select', $markup);
        $markup = self::expandEmptyTag('textarea', $markup);
        return $markup;
    }
    public static function debug($text)
    {
        phpQuery::debug($text);
    }
    public static function expandEmptyTag($tag, $xml)
    {
        $indice = 0;
        while ($indice < strlen($xml)) {
            $pos = strpos($xml, '<' . $tag . ' ', $indice);
            if ($pos) {
                $posCierre = strpos($xml, '>', $pos);
                if ($xml[$posCierre - 1] == '/') {
                    $xml = substr_replace($xml, '></' . $tag . '>', $posCierre - 1, 2);
                }
                $indice = $posCierre;
            } else {
                break;
            }
        }
        return $xml;
    }
}
class phpQueryEvents
{
    public static function trigger($document, $type, $data = array(), $node = NULL)
    {
        $documentID = phpQuery::getDocumentID($document);
        $namespace = NULL;
        if (strpos($type, '.') !== false) {
            list($name, $namespace) = explode('.', $type);
        } else {
            $name = $type;
        }
        if (!$node) {
            if (self::issetGlobal($documentID, $type)) {
                $pq = phpQuery::getDocument($documentID);
                $pq->find('*')->add($pq->document)->trigger($type, $data);
            }
        } else {
            if (isset($data[0]) && $data[0] instanceof DOMEvent) {
                $event = $data[0];
                $event->relatedTarget = $event->target;
                $event->target = $node;
                $data = array_slice($data, 1);
            } else {
                $event = new DOMEvent(array('type' => $type, 'target' => $node, 'timeStamp' => time()));
            }
            $i = 0;
            while ($node) {
                phpQuery::debug('Triggering ' . ($i ? 'bubbled ' : '') . 'event \'' . $type . '\' on ' . 'node ' . "\n" . '');
                $event->currentTarget = $node;
                $eventNode = self::getNode($documentID, $node);
                if (isset($eventNode->eventHandlers)) {
                    foreach ($eventNode->eventHandlers as $eventType => $handlers) {
                        $eventNamespace = NULL;
                        if (strpos($type, '.') !== false) {
                            list($eventName, $eventNamespace) = explode('.', $eventType);
                        } else {
                            $eventName = $eventType;
                        }
                        if ($name != $eventName) {
                            continue;
                        }
                        if ($namespace && $eventNamespace && $namespace != $eventNamespace) {
                            continue;
                        }
                        foreach ($handlers as $handler) {
                            phpQuery::debug('Calling event handler' . "\n" . '');
                            $event->data = $handler['data'] ? $handler['data'] : NULL;
                            $params = array_merge(array($event), $data);
                            $return = phpQuery::callbackRun($handler['callback'], $params);
                            if ($return === false) {
                                $event->bubbles = false;
                            }
                        }
                    }
                }
                if (!$event->bubbles) {
                    break;
                }
                $node = $node->parentNode;
                $i++;
            }
        }
    }
    public static function add($document, $node, $type, $data, $callback = NULL)
    {
        phpQuery::debug('Binding \'' . $type . '\' event');
        $documentID = phpQuery::getDocumentID($document);
        $eventNode = self::getNode($documentID, $node);
        if (!$eventNode) {
            $eventNode = self::setNode($documentID, $node);
        }
        if (!isset($eventNode->eventHandlers[$type])) {
            $eventNode->eventHandlers[$type] = array();
        }
        $eventNode->eventHandlers[$type][] = array('callback' => $callback, 'data' => $data);
    }
    public static function remove($document, $node, $type = NULL, $callback = NULL)
    {
        $documentID = phpQuery::getDocumentID($document);
        $eventNode = self::getNode($documentID, $node);
        if (is_object($eventNode) && isset($eventNode->eventHandlers[$type])) {
            if ($callback) {
                foreach ($eventNode->eventHandlers[$type] as $k => $handler) {
                    if ($handler['callback'] == $callback) {
                        unset($eventNode->eventHandlers[$type][$k]);
                    }
                }
            } else {
                unset($eventNode->eventHandlers[$type]);
            }
        }
    }
    protected static function getNode($documentID, $node)
    {
        foreach (phpQuery::$documents[$documentID]->eventsNodes as $eventNode) {
            if ($node->isSameNode($eventNode)) {
                return $eventNode;
            }
        }
    }
    protected static function setNode($documentID, $node)
    {
        phpQuery::$documents[$documentID]->eventsNodes[] = $node;
        return phpQuery::$documents[$documentID]->eventsNodes[count(phpQuery::$documents[$documentID]->eventsNodes) - 1];
    }
    protected static function issetGlobal($documentID, $type)
    {
        return isset(phpQuery::$documents[$documentID]) ? in_array($type, phpQuery::$documents[$documentID]->eventsGlobal) : false;
    }
}
interface ICallbackNamed
{
    public function hasName();
    public function getName();
}
class CallbackParam
{
}
class phpQuery
{
    /**
     * XXX: Workaround for mbstring problems 
     * 
     * @var bool
     */
    public static $mbstringSupport = true;
    public static $debug = false;
    public static $documents = array();
    public static $defaultDocumentID;
    /**
     * Applies only to HTML.
     *
     * @var unknown_type
     */
    public static $defaultDoctype = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"' . "\n" . '"http://www.w3.org/TR/html4/loose.dtd">';
    public static $defaultCharset = 'UTF-8';
    /**
     * Static namespace for plugins.
     *
     * @var object
     */
    public static $plugins = array();
    /**
     * List of loaded plugins.
     *
     * @var unknown_type
     */
    public static $pluginsLoaded = array();
    public static $pluginsMethods = array();
    public static $pluginsStaticMethods = array();
    public static $extendMethods = array();
    /**
     * @TODO implement
     */
    public static $extendStaticMethods = array();
    /**
     * Hosts allowed for AJAX connections.
     * Dot '.' means $_SERVER['HTTP_HOST'] (if any).
     *
     * @var array
     */
    public static $ajaxAllowedHosts = array('.');
    /**
     * AJAX settings.
     *
     * @var array
     * XXX should it be static or not ?
     */
    public static $ajaxSettings = array('url' => '', 'global' => true, 'type' => 'GET', 'timeout' => NULL, 'contentType' => 'application/x-www-form-urlencoded', 'processData' => true, 'data' => NULL, 'username' => NULL, 'password' => NULL, 'accepts' => array('xml' => 'application/xml, text/xml', 'html' => 'text/html', 'script' => 'text/javascript, application/javascript', 'json' => 'application/json, text/javascript', 'text' => 'text/plain', '_default' => '*/*'));
    public static $lastModified;
    public static $active = 0;
    public static $dumpCount = 0;
    public static function pq($arg1, $context = NULL)
    {
        if ($arg1 instanceof DOMNODE && !isset($context)) {
            foreach (phpQuery::$documents as $documentWrapper) {
                $compare = $arg1 instanceof DOMDocument ? $arg1 : $arg1->ownerDocument;
                if ($documentWrapper->document->isSameNode($compare)) {
                    $context = $documentWrapper->id;
                }
            }
        }
        if (!$context) {
            $domId = self::$defaultDocumentID;
            if (!$domId) {
                throw new Exception('Can\'t use last created DOM, because there isn\'t any. Use phpQuery::newDocument() first.');
            }
        } else {
            if (is_object($context) && $context instanceof phpQueryObject) {
                $domId = $context->getDocumentID();
            } else {
                if ($context instanceof DOMDOCUMENT) {
                    $domId = self::getDocumentID($context);
                    if (!$domId) {
                        $domId = self::newDocument($context)->getDocumentID();
                    }
                } else {
                    if ($context instanceof DOMNODE) {
                        $domId = self::getDocumentID($context);
                        if (!$domId) {
                            throw new Exception('Orphaned DOMNode');
                        }
                    } else {
                        $domId = $context;
                    }
                }
            }
        }
        if ($arg1 instanceof phpQueryObject) {
            if ($arg1->getDocumentID() == $domId) {
                return $arg1;
            }
            $class = get_class($arg1);
            $phpQuery = $class != 'phpQuery' ? new $class($arg1, $domId) : new phpQueryObject($domId);
            $phpQuery->elements = array();
            foreach ($arg1->elements as $node) {
                $phpQuery->elements[] = $phpQuery->document->importNode($node, true);
            }
            return $phpQuery;
        } else {
            if ($arg1 instanceof DOMNODE || is_array($arg1) && isset($arg1[0]) && $arg1[0] instanceof DOMNODE) {
                $phpQuery = new phpQueryObject($domId);
                if (!$arg1 instanceof DOMNODELIST && !is_array($arg1)) {
                    $arg1 = array($arg1);
                }
                $phpQuery->elements = array();
                foreach ($arg1 as $node) {
                    $sameDocument = $node->ownerDocument instanceof DOMDOCUMENT && !$node->ownerDocument->isSameNode($phpQuery->document);
                    $phpQuery->elements[] = $sameDocument ? $phpQuery->document->importNode($node, true) : $node;
                }
                return $phpQuery;
            } else {
                if (self::isMarkup($arg1)) {
                    $phpQuery = new phpQueryObject($domId);
                    return $phpQuery->newInstance($phpQuery->documentWrapper->import($arg1));
                } else {
                    $phpQuery = new phpQueryObject($domId);
                    if ($context && $context instanceof phpQueryObject) {
                        $phpQuery->elements = $context->elements;
                    } else {
                        if ($context && $context instanceof DOMNODELIST) {
                            $phpQuery->elements = array();
                            foreach ($context as $node) {
                                $phpQuery->elements[] = $node;
                            }
                        } else {
                            if ($context && $context instanceof DOMNODE) {
                                $phpQuery->elements = array($context);
                            }
                        }
                    }
                    return $phpQuery->find($arg1);
                }
            }
        }
    }
    public static function selectDocument($id)
    {
        $id = self::getDocumentID($id);
        self::debug('Selecting document \'' . $id . '\' as default one');
        self::$defaultDocumentID = self::getDocumentID($id);
    }
    public static function getDocument($id = NULL)
    {
        if ($id) {
            phpQuery::selectDocument($id);
        } else {
            $id = phpQuery::$defaultDocumentID;
        }
        return new phpQueryObject($id);
    }
    public static function newDocument($markup = NULL, $contentType = NULL)
    {
        if (!$markup) {
            $markup = '';
        }
        $documentID = phpQuery::createDocumentWrapper($markup, $contentType);
        return new phpQueryObject($documentID);
    }
    public static function newDocumentHTML($markup = NULL, $charset = NULL)
    {
        $contentType = $charset ? ';charset=' . $charset : '';
        return self::newDocument($markup, 'text/html' . $contentType);
    }
    public static function newDocumentXML($markup = NULL, $charset = NULL)
    {
        $contentType = $charset ? ';charset=' . $charset : '';
        return self::newDocument($markup, 'text/xml' . $contentType);
    }
    public static function newDocumentXHTML($markup = NULL, $charset = NULL)
    {
        $contentType = $charset ? ';charset=' . $charset : '';
        return self::newDocument($markup, 'application/xhtml+xml' . $contentType);
    }
    public static function newDocumentPHP($markup = NULL, $contentType = 'text/html')
    {
        $markup = phpQuery::phpToMarkup($markup, self::$defaultCharset);
        return self::newDocument($markup, $contentType);
    }
    public static function phpToMarkup($php, $charset = 'utf-8')
    {
        $regexes = array('@(<(?!\\?)(?:[^>]|\\?>)+\\w+\\s*=\\s*)(\')([^\']*)<' . '?php?(.*?)(?:\\?>)([^\']*)\'@s', '@(<(?!\\?)(?:[^>]|\\?>)+\\w+\\s*=\\s*)(")([^"]*)<' . '?php?(.*?)(?:\\?>)([^"]*)"@s');
        foreach ($regexes as $regex) {
            while (preg_match($regex, $php, $matches)) {
                $php = preg_replace_callback($regex, array('phpQuery', '_phpToMarkupCallback'), $php);
            }
        }
        $regex = '@(^|>[^<]*)+?(<\\?php(.*?)(\\?>))@s';
        $php = preg_replace($regex, '\\1<php><!-- \\3 --></php>', $php);
        return $php;
    }
    public static function _phpToMarkupCallback($php, $charset = 'utf-8')
    {
        return $m[1] . $m[2] . htmlspecialchars('<' . '?php' . $m[4] . '?' . '>', ENT_QUOTES | ENT_NOQUOTES, $charset) . $m[5] . $m[2];
    }
    public static function _markupToPHPCallback($m)
    {
        return '<' . '?php ' . htmlspecialchars_decode($m[1]) . ' ?' . '>';
    }
    public static function markupToPHP($content)
    {
        if ($content instanceof phpQueryObject) {
            $content = $content->markupOuter();
        }
        $content = preg_replace_callback('@<php>\\s*<!--(.*?)-->\\s*</php>@s', array('phpQuery', '_markupToPHPCallback'), $content);
        $regexes = array('@(<(?!\\?)(?:[^>]|\\?>)+\\w+\\s*=\\s*)(\')([^\']*)(?:&lt;|%3C)\\?(?:php)?(.*?)(?:\\?(?:&gt;|%3E))([^\']*)\'@s', '@(<(?!\\?)(?:[^>]|\\?>)+\\w+\\s*=\\s*)(")([^"]*)(?:&lt;|%3C)\\?(?:php)?(.*?)(?:\\?(?:&gt;|%3E))([^"]*)"@s');
        foreach ($regexes as $regex) {
            while (preg_match($regex, $content)) {
                $content = preg_replace_callback($regex, create_function('$m', 'return $m[1].$m[2].$m[3]."<?php "' . "\n" . '							.str_replace(' . "\n" . '								array("%20", "%3E", "%09", "&#10;", "&#9;", "%7B", "%24", "%7D", "%22", "%5B", "%5D"),' . "\n" . '								array(" ", ">", "	", "\\n", "	", "{", "$", "}", \'"\', "[", "]"),' . "\n" . '								htmlspecialchars_decode($m[4])' . "\n" . '							)' . "\n" . '							." ?>".$m[5].$m[2];'), $content);
            }
        }
        return $content;
    }
    public static function newDocumentFile($file, $contentType = NULL)
    {
        $documentID = self::createDocumentWrapper(file_get_contents($file), $contentType);
        return new phpQueryObject($documentID);
    }
    public static function newDocumentFileHTML($file, $charset = NULL)
    {
        $contentType = $charset ? ';charset=' . $charset : '';
        return self::newDocumentFile($file, 'text/html' . $contentType);
    }
    public static function newDocumentFileXML($file, $charset = NULL)
    {
        $contentType = $charset ? ';charset=' . $charset : '';
        return self::newDocumentFile($file, 'text/xml' . $contentType);
    }
    public static function newDocumentFileXHTML($file, $charset = NULL)
    {
        $contentType = $charset ? ';charset=' . $charset : '';
        return self::newDocumentFile($file, 'application/xhtml+xml' . $contentType);
    }
    public static function newDocumentFilePHP($file, $contentType = NULL)
    {
        return self::newDocumentPHP(file_get_contents($file), $contentType);
    }
    public static function loadDocument($document)
    {
        exit('TODO loadDocument');
    }
    protected static function createDocumentWrapper($html, $contentType = NULL, $documentID = NULL)
    {
        if (function_exists('domxml_open_mem')) {
            throw new Exception('Old PHP4 DOM XML extension detected. phpQuery won\'t work until this extension is enabled.');
        }
        $document = NULL;
        if ($html instanceof DOMDOCUMENT) {
            if (self::getDocumentID($html)) {
                $document = clone $html;
            } else {
                $wrapper = new DOMDocumentWrapper($html, $contentType, $documentID);
            }
        } else {
            $wrapper = new DOMDocumentWrapper($html, $contentType, $documentID);
        }
        phpQuery::$documents[$wrapper->id] = $wrapper;
        phpQuery::selectDocument($wrapper->id);
        return $wrapper->id;
    }
    public static function extend($target, $source)
    {
        switch ($target) {
            case 'phpQueryObject':
                $targetRef =& self::$extendMethods;
                $targetRef2 =& self::$pluginsMethods;
                break;
            case 'phpQuery':
                $targetRef =& self::$extendStaticMethods;
                $targetRef2 =& self::$pluginsStaticMethods;
                break;
            default:
                throw new Exception('Unsupported $target type');
        }
        if (is_string($source)) {
            $source = array($source => $source);
        }
        foreach ($source as $method => $callback) {
            if (isset($targetRef[$method])) {
                self::debug('Duplicate method \'' . $method . '\', can\\\'t extend \'' . $target . '\'');
                continue;
            }
            if (isset($targetRef2[$method])) {
                self::debug('Duplicate method \'' . $method . '\' from plugin \'' . $targetRef2[$method] . '\',' . ' can\\\'t extend \'' . $target . '\'');
                continue;
            }
            $targetRef[$method] = $callback;
        }
        return true;
    }
    public static function plugin($class, $file = NULL)
    {
        if (in_array($class, self::$pluginsLoaded)) {
            return true;
        }
        if (!$file) {
            $file = $class . '.php';
        }
        $objectClassExists = class_exists('phpQueryObjectPlugin_' . $class);
        $staticClassExists = class_exists('phpQueryPlugin_' . $class);
        if (!$objectClassExists && !$staticClassExists) {
            require_once $file;
        }
        self::$pluginsLoaded[] = $class;
        if (class_exists('phpQueryPlugin_' . $class)) {
            $realClass = 'phpQueryPlugin_' . $class;
            $vars = get_class_vars($realClass);
            $loop = isset($vars['phpQueryMethods']) && !is_null($vars['phpQueryMethods']) ? $vars['phpQueryMethods'] : get_class_methods($realClass);
            foreach ($loop as $method) {
                if ($method == '__initialize') {
                    continue;
                }
                if (!is_callable(array($realClass, $method))) {
                    continue;
                }
                if (isset(self::$pluginsStaticMethods[$method])) {
                    throw new Exception('Duplicate method \'' . $method . '\' from plugin \'' . $c . '\' conflicts with same method from plugin \'' . self::$pluginsStaticMethods[$method] . '\'');
                    return NULL;
                }
                self::$pluginsStaticMethods[$method] = $class;
            }
            if (method_exists($realClass, '__initialize')) {
                call_user_func_array(array($realClass, '__initialize'), array());
            }
        }
        if (class_exists('phpQueryObjectPlugin_' . $class)) {
            $realClass = 'phpQueryObjectPlugin_' . $class;
            $vars = get_class_vars($realClass);
            $loop = isset($vars['phpQueryMethods']) && !is_null($vars['phpQueryMethods']) ? $vars['phpQueryMethods'] : get_class_methods($realClass);
            foreach ($loop as $method) {
                if (!is_callable(array($realClass, $method))) {
                    continue;
                }
                if (isset(self::$pluginsMethods[$method])) {
                    throw new Exception('Duplicate method \'' . $method . '\' from plugin \'' . $c . '\' conflicts with same method from plugin \'' . self::$pluginsMethods[$method] . '\'');
                    continue;
                }
                self::$pluginsMethods[$method] = $class;
            }
        }
        return true;
    }
    public static function unloadDocuments($id = NULL)
    {
        if (isset($id)) {
            if ($id = self::getDocumentID($id)) {
                unset(phpQuery::$documents[$id]);
            }
        } else {
            foreach (phpQuery::$documents as $k => $v) {
                unset(phpQuery::$documents[$k]);
            }
        }
    }
    public static function unsafePHPTags($content)
    {
        return self::markupToPHP($content);
    }
    public static function DOMNodeListToArray($DOMNodeList)
    {
        $array = array();
        if (!$DOMNodeList) {
            return $array;
        }
        foreach ($DOMNodeList as $node) {
            $array[] = $node;
        }
        return $array;
    }
    public static function isMarkup($input)
    {
        return !is_array($input) && substr(trim($input), 0, 1) == '<';
    }
    public static function debug($text)
    {
        if (self::$debug) {
            print var_dump($text);
        }
    }
    public static function ajax($options = array(), $xhr = NULL)
    {
        $options = array_merge(self::$ajaxSettings, $options);
        $documentID = isset($options['document']) ? self::getDocumentID($options['document']) : NULL;
        if ($xhr) {
            $client = $xhr;
            $client->setAuth(false);
            $client->setHeaders('If-Modified-Since', NULL);
            $client->setHeaders('Referer', NULL);
            $client->resetParameters();
        } else {
            require_once 'Zend/Http/Client.php';
            $client = new Zend_Http_Client();
            $client->setCookieJar();
        }
        if (isset($options['timeout'])) {
            $client->setConfig(array('timeout' => $options['timeout']));
        }
        foreach (self::$ajaxAllowedHosts as $k => $host) {
            if ($host == '.' && isset($_SERVER['HTTP_HOST'])) {
                self::$ajaxAllowedHosts[$k] = $_SERVER['HTTP_HOST'];
            }
        }
        $host = parse_url($options['url'], PHP_URL_HOST);
        if (!in_array($host, self::$ajaxAllowedHosts)) {
            throw new Exception('Request not permitted, host \'' . $host . '\' not present in ' . 'phpQuery::$ajaxAllowedHosts');
        }
        $jsre = '/=\\?(&|$)/';
        if (isset($options['dataType']) && $options['dataType'] == 'jsonp') {
            $jsonpCallbackParam = $options['jsonp'] ? $options['jsonp'] : 'callback';
            if (strtolower($options['type']) == 'get') {
                if (!preg_match($jsre, $options['url'])) {
                    $sep = strpos($options['url'], '?') ? '&' : '?';
                    $options['url'] .= $sep . $jsonpCallbackParam . '=?';
                }
            } else {
                if ($options['data']) {
                    $jsonp = false;
                    foreach ($options['data'] as $n => $v) {
                        if ($v == '?') {
                            $jsonp = true;
                        }
                    }
                    if (!$jsonp) {
                        $options['data'][$jsonpCallbackParam] = '?';
                    }
                }
            }
            $options['dataType'] = 'json';
        }
        if (isset($options['dataType']) && $options['dataType'] == 'json') {
            $jsonpCallback = 'json_' . md5(microtime());
            $jsonpData = $jsonpUrl = false;
            if ($options['data']) {
                foreach ($options['data'] as $n => $v) {
                    if ($v == '?') {
                        $jsonpData = $n;
                    }
                }
            }
            if (preg_match($jsre, $options['url'])) {
                $jsonpUrl = true;
            }
            if ($jsonpData !== false || $jsonpUrl) {
                $options['_jsonp'] = $jsonpCallback;
                if ($jsonpData !== false) {
                    $options['data'][$jsonpData] = $jsonpCallback;
                }
                if ($jsonpUrl) {
                    $options['url'] = preg_replace($jsre, '=' . $jsonpCallback . '\\1', $options['url']);
                }
            }
        }
        $client->setUri($options['url']);
        $client->setMethod(strtoupper($options['type']));
        if (isset($options['referer']) && $options['referer']) {
            $client->setHeaders('Referer', $options['referer']);
        }
        $client->setHeaders(array('User-Agent' => 'Mozilla/5.0 (X11; U; Linux x86; en-US; rv:1.9.0.5) Gecko' . '/2008122010 Firefox/3.0.5', 'Accept-Charset' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7', 'Accept-Language' => 'en-us,en;q=0.5'));
        if ($options['username']) {
            $client->setAuth($options['username'], $options['password']);
        }
        if (isset($options['ifModified']) && $options['ifModified']) {
            $client->setHeaders('If-Modified-Since', self::$lastModified ? self::$lastModified : 'Thu, 01 Jan 1970 00:00:00 GMT');
        }
        $client->setHeaders('Accept', isset($options['dataType']) && isset(self::$ajaxSettings['accepts'][$options['dataType']]) ? self::$ajaxSettings['accepts'][$options['dataType']] . ', */*' : self::$ajaxSettings['accepts']['_default']);
        if ($options['data'] instanceof phpQueryObject) {
            $serialized = $options['data']->serializeArray($options['data']);
            $options['data'] = array();
            foreach ($serialized as $r) {
                $options['data'][$r['name']] = $r['value'];
            }
        }
        if (strtolower($options['type']) == 'get') {
            $client->setParameterGet($options['data']);
        } else {
            if (strtolower($options['type']) == 'post') {
                $client->setEncType($options['contentType']);
                $client->setParameterPost($options['data']);
            }
        }
        if (self::$active == 0 && $options['global']) {
            phpQueryEvents::trigger($documentID, 'ajaxStart');
        }
        self::$active++;
        if (isset($options['beforeSend']) && $options['beforeSend']) {
            phpQuery::callbackRun($options['beforeSend'], array($client));
        }
        if ($options['global']) {
            phpQueryEvents::trigger($documentID, 'ajaxSend', array($client, $options));
        }
        if (phpQuery::$debug) {
            self::debug($options['type'] . ': ' . $options['url'] . "\n");
            self::debug('Options: <pre>' . var_export($options, true) . '</pre>' . "\n" . '');
        }
        $response = $client->request();
        if (phpQuery::$debug) {
            self::debug('Status: ' . $response->getStatus() . ' / ' . $response->getMessage());
            self::debug($client->getLastRequest());
            self::debug($response->getHeaders());
        }
        if ($response->isSuccessful()) {
            self::$lastModified = $response->getHeader('Last-Modified');
            $data = self::httpData($response->getBody(), $options['dataType'], $options);
            if (isset($options['success']) && $options['success']) {
                phpQuery::callbackRun($options['success'], array($data, $response->getStatus(), $options));
            }
            if ($options['global']) {
                phpQueryEvents::trigger($documentID, 'ajaxSuccess', array($client, $options));
            }
        } else {
            if (isset($options['error']) && $options['error']) {
                phpQuery::callbackRun($options['error'], array($client, $response->getStatus(), $response->getMessage()));
            }
            if ($options['global']) {
                phpQueryEvents::trigger($documentID, 'ajaxError', array($client, $response->getMessage(), $options));
            }
        }
        if (isset($options['complete']) && $options['complete']) {
            phpQuery::callbackRun($options['complete'], array($client, $response->getStatus()));
        }
        if ($options['global']) {
            phpQueryEvents::trigger($documentID, 'ajaxComplete', array($client, $options));
        }
        if ($options['global'] && !--self::$active) {
            phpQueryEvents::trigger($documentID, 'ajaxStop');
        }
        return $client;
    }
    protected static function httpData($data, $type, $options)
    {
        if (isset($options['dataFilter']) && $options['dataFilter']) {
            $data = self::callbackRun($options['dataFilter'], array($data, $type));
        }
        if (is_string($data)) {
            if ($type == 'json') {
                if (isset($options['_jsonp']) && $options['_jsonp']) {
                    $data = preg_replace('/^\\s*\\w+\\((.*)\\)\\s*$/s', '$1', $data);
                }
                $data = self::parseJSON($data);
            }
        }
        return $data;
    }
    public static function param($data)
    {
        return http_build_query($data, NULL, '&');
    }
    public static function get($url, $data = NULL, $callback = NULL, $type = NULL)
    {
        if (!is_array($data)) {
            $callback = $data;
            $data = NULL;
        }
        return phpQuery::ajax(array('type' => 'GET', 'url' => $url, 'data' => $data, 'success' => $callback, 'dataType' => $type));
    }
    public static function post($url, $data = NULL, $callback = NULL, $type = NULL)
    {
        if (!is_array($data)) {
            $callback = $data;
            $data = NULL;
        }
        return phpQuery::ajax(array('type' => 'POST', 'url' => $url, 'data' => $data, 'success' => $callback, 'dataType' => $type));
    }
    public static function getJSON($url, $data = NULL, $callback = NULL)
    {
        if (!is_array($data)) {
            $callback = $data;
            $data = NULL;
        }
        return phpQuery::ajax(array('type' => 'GET', 'url' => $url, 'data' => $data, 'success' => $callback, 'dataType' => 'json'));
    }
    public static function ajaxSetup($options)
    {
        self::$ajaxSettings = array_merge(self::$ajaxSettings, $options);
    }
    public static function ajaxAllowHost($host1, $host2 = NULL, $host3 = NULL)
    {
        $loop = is_array($host1) ? $host1 : func_get_args();
        foreach ($loop as $host) {
            if ($host && !in_array($host, phpQuery::$ajaxAllowedHosts)) {
                phpQuery::$ajaxAllowedHosts[] = $host;
            }
        }
    }
    public static function ajaxAllowURL($url1, $url2 = NULL, $url3 = NULL)
    {
        $loop = is_array($url1) ? $url1 : func_get_args();
        foreach ($loop as $url) {
            phpQuery::ajaxAllowHost(parse_url($url, PHP_URL_HOST));
        }
    }
    public static function toJSON($data)
    {
        if (function_exists('json_encode')) {
            return json_encode($data);
        }
        require_once 'Zend/Json/Encoder.php';
        return Zend_Json_Encoder::encode($data);
    }
    public static function parseJSON($json)
    {
        if (function_exists('json_decode')) {
            $return = json_decode(trim($json), true);
            if (isset($return)) {
                return $return;
            }
        }
        require_once 'Zend/Json/Decoder.php';
        return Zend_Json_Decoder::decode($json);
    }
    public static function getDocumentID($source)
    {
        if ($source instanceof DOMDOCUMENT) {
            foreach (phpQuery::$documents as $id => $document) {
                if ($source->isSameNode($document->document)) {
                    return $id;
                }
            }
        } else {
            if ($source instanceof DOMNODE) {
                foreach (phpQuery::$documents as $id => $document) {
                    if ($source->ownerDocument->isSameNode($document->document)) {
                        return $id;
                    }
                }
            } else {
                if ($source instanceof phpQueryObject) {
                    return $source->getDocumentID();
                } else {
                    if (is_string($source) && isset(phpQuery::$documents[$source])) {
                        return $source;
                    }
                }
            }
        }
    }
    public static function getDOMDocument($source)
    {
        if ($source instanceof DOMDOCUMENT) {
            return $source;
        }
        $source = self::getDocumentID($source);
        return $source ? self::$documents[$id]['document'] : NULL;
    }
    public static function makeArray($obj)
    {
        $array = array();
        if (is_object($object) && $object instanceof DOMNODELIST) {
            foreach ($object as $value) {
                $array[] = $value;
            }
        } else {
            if (is_object($object) && !$object instanceof Iterator) {
                foreach (get_object_vars($object) as $name => $value) {
                    $array[0][$name] = $value;
                }
            } else {
                foreach ($object as $name => $value) {
                    $array[0][$name] = $value;
                }
            }
        }
        return $array;
    }
    public static function inArray($value, $array)
    {
        return in_array($value, $array);
    }
    public static function each($object, $callback, $param1 = NULL, $param2 = NULL, $param3 = NULL)
    {
        $paramStructure = NULL;
        if (2 < func_num_args()) {
            $paramStructure = func_get_args();
            $paramStructure = array_slice($paramStructure, 2);
        }
        if (is_object($object) && !$object instanceof Iterator) {
            foreach (get_object_vars($object) as $name => $value) {
                phpQuery::callbackRun($callback, array($name, $value), $paramStructure);
            }
        } else {
            foreach ($object as $name => $value) {
                phpQuery::callbackRun($callback, array($name, $value), $paramStructure);
            }
        }
    }
    public static function map($array, $callback, $param1 = NULL, $param2 = NULL, $param3 = NULL)
    {
        $result = array();
        $paramStructure = NULL;
        if (2 < func_num_args()) {
            $paramStructure = func_get_args();
            $paramStructure = array_slice($paramStructure, 2);
        }
        foreach ($array as $v) {
            $vv = phpQuery::callbackRun($callback, array($v), $paramStructure);
            if (is_array($vv)) {
                foreach ($vv as $vvv) {
                    $result[] = $vvv;
                }
            } else {
                if ($vv !== NULL) {
                    $result[] = $vv;
                }
            }
        }
        return $result;
    }
    public static function callbackRun($callback, $params = array(), $paramStructure = NULL)
    {
        if (!$callback) {
            return NULL;
        }
        if ($callback instanceof CallbackParameterToReference) {
            if (isset($params[0])) {
                $callback->callback = $params[0];
            }
            return true;
        }
        if ($callback instanceof Callback) {
            $paramStructure = $callback->params;
            $callback = $callback->callback;
        }
        if (!$paramStructure) {
            return call_user_func_array($callback, $params);
        }
        $p = 0;
        foreach ($paramStructure as $i => $v) {
            $paramStructure[$i] = $v instanceof CallbackParam ? $params[$p++] : $v;
        }
        return call_user_func_array($callback, $paramStructure);
    }
    public static function merge($one, $two)
    {
        $elements = $one->elements;
        foreach ($two->elements as $node) {
            $exists = false;
            foreach ($elements as $node2) {
                if ($node2->isSameNode($node)) {
                    $exists = true;
                }
            }
            if (!$exists) {
                $elements[] = $node;
            }
        }
        return $elements;
    }
    public static function grep($array, $callback, $invert = false)
    {
        $result = array();
        foreach ($array as $k => $v) {
            $r = call_user_func_array($callback, array($v, $k));
            if ($r === !(bool) $invert) {
                $result[] = $v;
            }
        }
        return $result;
    }
    public static function unique($array)
    {
        return array_unique($array);
    }
    public static function isFunction($function)
    {
        return is_callable($function);
    }
    public static function trim($str)
    {
        return trim($str);
    }
    public static function browserGet($url, $callback, $param1 = NULL, $param2 = NULL, $param3 = NULL)
    {
        if (self::plugin('WebBrowser')) {
            $params = func_get_args();
            return self::callbackRun(array(self::$plugins, 'browserGet'), $params);
        } else {
            self::debug('WebBrowser plugin not available...');
        }
    }
    public static function browserPost($url, $data, $callback, $param1 = NULL, $param2 = NULL, $param3 = NULL)
    {
        if (self::plugin('WebBrowser')) {
            $params = func_get_args();
            return self::callbackRun(array(self::$plugins, 'browserPost'), $params);
        } else {
            self::debug('WebBrowser plugin not available...');
        }
    }
    public static function browser($ajaxSettings, $callback, $param1 = NULL, $param2 = NULL, $param3 = NULL)
    {
        if (self::plugin('WebBrowser')) {
            $params = func_get_args();
            return self::callbackRun(array(self::$plugins, 'browser'), $params);
        } else {
            self::debug('WebBrowser plugin not available...');
        }
    }
    public static function php($code)
    {
        return self::code('php', $code);
    }
    public static function code($type, $code)
    {
        return '<' . $type . '><!-- ' . trim($code) . ' --></' . $type . '>';
    }
    public static function __callStatic($method, $params)
    {
        return call_user_func_array(array(phpQuery::$plugins, $method), $params);
    }
    protected static function dataSetupNode($node, $documentID)
    {
        foreach (phpQuery::$documents[$documentID]->dataNodes as $dataNode) {
            if ($node->isSameNode($dataNode)) {
                return $dataNode;
            }
        }
        phpQuery::$documents[$documentID]->dataNodes[] = $node;
        return $node;
    }
    protected static function dataRemoveNode($node, $documentID)
    {
        foreach (phpQuery::$documents[$documentID]->dataNodes as $k => $dataNode) {
            if ($node->isSameNode($dataNode)) {
                unset(self::$documents[$documentID]->dataNodes[$k]);
                unset(self::$documents[$documentID]->data[$dataNode->dataID]);
            }
        }
    }
    public static function data($node, $name, $data, $documentID = NULL)
    {
        if (!$documentID) {
            $documentID = self::getDocumentID($node);
        }
        $document = phpQuery::$documents[$documentID];
        $node = self::dataSetupNode($node, $documentID);
        if (!isset($node->dataID)) {
            $node->dataID = ++phpQuery::$documents[$documentID]->uuid;
        }
        $id = $node->dataID;
        if (!isset($document->data[$id])) {
            $document->data[$id] = array();
        }
        if (!is_null($data)) {
            $document->data[$id][$name] = $data;
        }
        if ($name) {
            if (isset($document->data[$id][$name])) {
                return $document->data[$id][$name];
            }
        } else {
            return $id;
        }
    }
    public static function removeData($node, $name, $documentID)
    {
        if (!$documentID) {
            $documentID = self::getDocumentID($node);
        }
        $document = phpQuery::$documents[$documentID];
        $node = self::dataSetupNode($node, $documentID);
        $id = $node->dataID;
        if ($name) {
            if (isset($document->data[$id][$name])) {
                unset($document->data[$id][$name]);
            }
            $name = NULL;
            foreach ($document->data[$id] as $name) {
                break;
            }
            if (!$name) {
                self::removeData($node, $name, $documentID);
            }
        } else {
            self::dataRemoveNode($node, $documentID);
        }
    }
}
class phpQueryPlugins
{
    public function __call($method, $args)
    {
        if (isset(phpQuery::$extendStaticMethods[$method])) {
            $return = call_user_func_array(phpQuery::$extendStaticMethods[$method], $args);
        } else {
            if (isset(phpQuery::$pluginsStaticMethods[$method])) {
                $class = phpQuery::$pluginsStaticMethods[$method];
                $realClass = 'phpQueryPlugin_' . $class;
                $return = call_user_func_array(array($realClass, $method), $args);
                return isset($return) ? $return : $this;
            } else {
                throw new Exception('Method \'' . $method . '\' doesnt exist');
            }
        }
    }
}
function pq($arg1, $context = NULL)
{
    $args = func_get_args();
    return call_user_func_array(array('phpQuery', 'pq'), $args);
}
class Callback implements ICallbackNamed
{
    public $callback;
    public $params;
    protected $name;
    public function __construct($callback, $param1 = NULL, $param2 = NULL, $param3 = NULL)
    {
        $params = func_get_args();
        $params = array_slice($params, 1);
        if ($callback instanceof Callback) {
        } else {
            $this->callback = $callback;
            $this->params = $params;
        }
    }
    public function getName()
    {
        return 'Callback: ' . $this->name;
    }
    public function hasName()
    {
        return isset($this->name) && $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
class CallbackBody extends Callback
{
}
class CallbackReturnReference extends Callback implements ICallbackNamed
{
    protected $reference;
    public function callback()
    {
        return $this->reference;
    }
}
class CallbackReturnValue extends Callback implements ICallbackNamed
{
    protected $value;
    protected $name;
    public function callback()
    {
        return $this->value;
    }
    public function __toString()
    {
        return $this->getName();
    }
}
class CallbackParameterToReference extends Callback
{
}
class phpQueryObject implements Iterator, ArrayAccess
{
    public $documentID;
    /**
     * DOMDocument class.
     *
     * @var DOMDocument
     */
    public $document;
    public $charset;
    /**
     *
     * @var DOMDocumentWrapper
     */
    public $documentWrapper;
    /**
     * XPath interface.
     *
     * @var DOMXPath
     */
    public $xpath;
    /**
     * Stack of selected elements.
     * @TODO refactor to ->nodes
     * @var array
     */
    public $elements = array();
    /**
     * @access private
     */
    protected $elementsBackup = array();
    /**
     * @access private
     */
    protected $previous;
    /**
     * @access private
     * @TODO deprecate
     */
    protected $root = array();
    /**
     * Indicated if doument is just a fragment (no <html> tag).
     *
     * Every document is realy a full document, so even documentFragments can
     * be queried against <html>, but getDocument(id)->htmlOuter() will return
     * only contents of <body>.
     *
     * @var bool
     */
    public $documentFragment = true;
    /**
     * Iterator interface helper
     * @access private
     */
    protected $elementsInterator = array();
    /**
     * Iterator interface helper
     * @access private
     */
    protected $valid = false;
    /**
     * Iterator interface helper
     * @access private
     */
    protected $current;
    public function __construct($documentID)
    {
        $id = $documentID instanceof self ? $documentID->getDocumentID() : $documentID;
        if (!isset(phpQuery::$documents[$id])) {
            throw new Exception('Document with ID \'' . $id . '\' isn\'t loaded. Use phpQuery::newDocument($html) or phpQuery::newDocumentFile($file) first.');
        }
        $this->documentID = $id;
        $this->documentWrapper =& phpQuery::$documents[$id];
        $this->document =& $this->documentWrapper->document;
        $this->xpath =& $this->documentWrapper->xpath;
        $this->charset =& $this->documentWrapper->charset;
        $this->documentFragment =& $this->documentWrapper->isDocumentFragment;
        $this->root =& $this->documentWrapper->root;
        $this->elements = array($this->root);
    }
    public function __get($attr)
    {
        switch ($attr) {
            case 'length':
                return $this->size();
                break;
            default:
                return $this->{$attr};
        }
    }
    public function toReference(&$var)
    {
        return $var = $this;
    }
    public function documentFragment($state = NULL)
    {
        if ($state) {
            phpQuery::$documents[$this->getDocumentID()]['documentFragment'] = $state;
            return $this;
        }
        return $this->documentFragment;
    }
    protected function isRoot($node)
    {
        return $node instanceof DOMDOCUMENT || $node instanceof DOMELEMENT && $node->tagName == 'html' || $this->root->isSameNode($node);
    }
    protected function stackIsRoot()
    {
        return $this->size() == 1 && $this->isRoot($this->elements[0]);
    }
    public function toRoot()
    {
        $this->elements = array($this->root);
        return $this;
    }
    public function getDocumentIDRef(&$documentID)
    {
        $documentID = $this->getDocumentID();
        return $this;
    }
    public function getDocument()
    {
        return phpQuery::getDocument($this->getDocumentID());
    }
    public function getDOMDocument()
    {
        return $this->document;
    }
    public function getDocumentID()
    {
        return $this->documentID;
    }
    public function unloadDocument()
    {
        phpQuery::unloadDocuments($this->getDocumentID());
    }
    public function isHTML()
    {
        return $this->documentWrapper->isHTML;
    }
    public function isXHTML()
    {
        return $this->documentWrapper->isXHTML;
    }
    public function isXML()
    {
        return $this->documentWrapper->isXML;
    }
    public function serialize()
    {
        return phpQuery::param($this->serializeArray());
    }
    public function serializeArray($submit = NULL)
    {
        $source = $this->filter('form, input, select, textarea')->find('input, select, textarea')->andSelf()->not('form');
        $return = array();
        foreach ($source as $input) {
            $input = phpQuery::pq($input);
            if ($input->is('[disabled]')) {
                continue;
            }
            if (!$input->is('[name]')) {
                continue;
            }
            if ($input->is('[type=checkbox]') && !$input->is('[checked]')) {
                continue;
            }
            if ($submit && $input->is('[type=submit]')) {
                if ($submit instanceof DOMELEMENT && !$input->elements[0]->isSameNode($submit)) {
                    continue;
                } else {
                    if (is_string($submit) && $input->attr('name') != $submit) {
                        continue;
                    }
                }
            }
            $return[] = array('name' => $input->attr('name'), 'value' => $input->val());
        }
        return $return;
    }
    protected function debug($in)
    {
        if (!phpQuery::$debug) {
            return NULL;
        }
        print '<pre>';
        print_r($in);
        print '</pre>' . "\n" . '';
    }
    protected function isRegexp($pattern)
    {
        return in_array($pattern[mb_strlen($pattern) - 1], array('^', '*', '$'));
    }
    protected function isChar($char)
    {
        return extension_loaded('mbstring') && phpQuery::$mbstringSupport ? mb_eregi('\\w', $char) : preg_match('@\\w@', $char);
    }
    protected function parseSelector($query)
    {
        $query = trim(preg_replace('@\\s+@', ' ', preg_replace('@\\s*(>|\\+|~)\\s*@', '\\1', $query)));
        $queries = array(array());
        if (!$query) {
            return $queries;
        }
        $return =& $queries[0];
        $specialChars = array('>', ' ');
        $specialCharsMapping = array();
        $strlen = mb_strlen($query);
        $classChars = array('.', '-');
        $pseudoChars = array('-');
        $tagChars = array('*', '|', '-');
        $_query = array();
        for ($i = 0; $i < $strlen; $i++) {
            $_query[] = mb_substr($query, $i, 1);
        }
        $query = $_query;
        $i = 0;
        while ($i < $strlen) {
            $c = $query[$i];
            $tmp = '';
            if ($this->isChar($c) || in_array($c, $tagChars)) {
                while (isset($query[$i]) && ($this->isChar($query[$i]) || in_array($query[$i], $tagChars))) {
                    $tmp .= $query[$i];
                    $i++;
                }
                $return[] = $tmp;
            } else {
                if ($c == '#') {
                    $i++;
                    while (isset($query[$i]) && ($this->isChar($query[$i]) || $query[$i] == '-')) {
                        $tmp .= $query[$i];
                        $i++;
                    }
                    $return[] = '#' . $tmp;
                } else {
                    if (in_array($c, $specialChars)) {
                        $return[] = $c;
                        $i++;
                    } else {
                        if (isset($specialCharsMapping[$c])) {
                            $return[] = $specialCharsMapping[$c];
                            $i++;
                        } else {
                            if ($c == ',') {
                                $queries[] = array();
                                $return =& $queries[count($queries) - 1];
                                $i++;
                                while (isset($query[$i]) && $query[$i] == ' ') {
                                    $i++;
                                }
                            } else {
                                if ($c == '.') {
                                    while (isset($query[$i]) && ($this->isChar($query[$i]) || in_array($query[$i], $classChars))) {
                                        $tmp .= $query[$i];
                                        $i++;
                                    }
                                    $return[] = $tmp;
                                } else {
                                    if ($c == '~') {
                                        $spaceAllowed = true;
                                        $tmp .= $query[$i++];
                                        while (isset($query[$i]) && ($this->isChar($query[$i]) || in_array($query[$i], $classChars) || $query[$i] == '*' || $query[$i] == ' ' && $spaceAllowed)) {
                                            if ($query[$i] != ' ') {
                                                $spaceAllowed = false;
                                            }
                                            $tmp .= $query[$i];
                                            $i++;
                                        }
                                        $return[] = $tmp;
                                    } else {
                                        if ($c == '+') {
                                            $spaceAllowed = true;
                                            $tmp .= $query[$i++];
                                            while (isset($query[$i]) && ($this->isChar($query[$i]) || in_array($query[$i], $classChars) || $query[$i] == '*' || $spaceAllowed && $query[$i] == ' ')) {
                                                if ($query[$i] != ' ') {
                                                    $spaceAllowed = false;
                                                }
                                                $tmp .= $query[$i];
                                                $i++;
                                            }
                                            $return[] = $tmp;
                                        } else {
                                            if ($c == '[') {
                                                $stack = 1;
                                                $tmp .= $c;
                                                while (isset($query[++$i])) {
                                                    $tmp .= $query[$i];
                                                    if ($query[$i] == '[') {
                                                        $stack++;
                                                    } else {
                                                        if ($query[$i] == ']') {
                                                            $stack--;
                                                            if (!$stack) {
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                                $return[] = $tmp;
                                                $i++;
                                            } else {
                                                if ($c == ':') {
                                                    $stack = 1;
                                                    $tmp .= $query[$i++];
                                                    while (isset($query[$i]) && ($this->isChar($query[$i]) || in_array($query[$i], $pseudoChars))) {
                                                        $tmp .= $query[$i];
                                                        $i++;
                                                    }
                                                    if (isset($query[$i]) && $query[$i] == '(') {
                                                        $tmp .= $query[$i];
                                                        $stack = 1;
                                                        while (isset($query[++$i])) {
                                                            $tmp .= $query[$i];
                                                            if ($query[$i] == '(') {
                                                                $stack++;
                                                            } else {
                                                                if ($query[$i] == ')') {
                                                                    $stack--;
                                                                    if (!$stack) {
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        $return[] = $tmp;
                                                        $i++;
                                                    } else {
                                                        $return[] = $tmp;
                                                    }
                                                } else {
                                                    $i++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        foreach ($queries as $k => $q) {
            if (isset($q[0])) {
                if (isset($q[0][0]) && $q[0][0] == ':') {
                    array_unshift($queries[$k], '*');
                }
                if ($q[0] != '>') {
                    array_unshift($queries[$k], ' ');
                }
            }
        }
        return $queries;
    }
    public function get($index = NULL, $callback1 = NULL, $callback2 = NULL, $callback3 = NULL)
    {
        $return = isset($index) ? isset($this->elements[$index]) ? $this->elements[$index] : NULL : $this->elements;
        $args = func_get_args();
        $args = array_slice($args, 1);
        foreach ($args as $callback) {
            if (is_array($return)) {
                foreach ($return as $k => $v) {
                    $return[$k] = phpQuery::callbackRun($callback, array($v));
                }
            } else {
                $return = phpQuery::callbackRun($callback, array($return));
            }
        }
        return $return;
    }
    public function getString($index = NULL, $callback1 = NULL, $callback2 = NULL, $callback3 = NULL)
    {
        if ($index) {
            $return = $this->eq($index)->text();
        } else {
            $return = array();
            for ($i = 0; $i < $this->size(); $i++) {
                $return[] = $this->eq($i)->text();
            }
        }
        $args = func_get_args();
        $args = array_slice($args, 1);
        foreach ($args as $callback) {
            $return = phpQuery::callbackRun($callback, array($return));
        }
        return $return;
    }
    public function getStrings($index = NULL, $callback1 = NULL, $callback2 = NULL, $callback3 = NULL)
    {
        if ($index) {
            $return = $this->eq($index)->text();
        } else {
            $return = array();
            for ($i = 0; $i < $this->size(); $i++) {
                $return[] = $this->eq($i)->text();
            }
            $args = func_get_args();
            $args = array_slice($args, 1);
        }
        foreach ($args as $callback) {
            if (is_array($return)) {
                foreach ($return as $k => $v) {
                    $return[$k] = phpQuery::callbackRun($callback, array($v));
                }
            } else {
                $return = phpQuery::callbackRun($callback, array($return));
            }
        }
        return $return;
    }
    public function newInstance($newStack = NULL)
    {
        $class = get_class($this);
        $new = $class != 'phpQuery' ? new $class($this, $this->getDocumentID()) : new phpQueryObject($this->getDocumentID());
        $new->previous = $this;
        if (is_null($newStack)) {
            $new->elements = $this->elements;
            if ($this->elementsBackup) {
                $this->elements = $this->elementsBackup;
            }
        } else {
            if (is_string($newStack)) {
                $new->elements = phpQuery::pq($newStack, $this->getDocumentID())->stack();
            } else {
                $new->elements = $newStack;
            }
        }
        return $new;
    }
    protected function matchClasses($class, $node)
    {
        if (mb_strpos($class, '.', 1)) {
            $classes = explode('.', substr($class, 1));
            $classesCount = count($classes);
            $nodeClasses = explode(' ', $node->getAttribute('class'));
            $nodeClassesCount = count($nodeClasses);
            if ($nodeClassesCount < $classesCount) {
                return false;
            }
            $diff = count(array_diff($classes, $nodeClasses));
            if (!$diff) {
                return true;
            }
        } else {
            return in_array(substr($class, 1), explode(' ', $node->getAttribute('class')));
        }
    }
    protected function runQuery($XQuery, $selector = NULL, $compare = NULL)
    {
        if ($compare && !method_exists($this, $compare)) {
            return false;
        }
        $stack = array();
        if (!$this->elements) {
            $this->debug('Stack empty, skipping...');
        }
        foreach ($this->stack(array(1, 9, 13)) as $k => $stackNode) {
            $detachAfter = false;
            $testNode = $stackNode;
            while ($testNode) {
                if (!$testNode->parentNode && !$this->isRoot($testNode)) {
                    $this->root->appendChild($testNode);
                    $detachAfter = $testNode;
                    break;
                }
                $testNode = isset($testNode->parentNode) ? $testNode->parentNode : NULL;
            }
            $xpath = $this->documentWrapper->isXHTML ? $this->getNodeXpath($stackNode, 'html') : $this->getNodeXpath($stackNode);
            $query = $XQuery == '//' && $xpath == '/html[1]' ? '//*' : $xpath . $XQuery;
            $this->debug('XPATH: ' . $query);
            $nodes = $this->xpath->query($query);
            $this->debug('QUERY FETCHED');
            if (!$nodes->length) {
                $this->debug('Nothing found');
            }
            $debug = array();
            foreach ($nodes as $node) {
                $matched = false;
                if ($compare) {
                    phpQuery::$debug ? $this->debug('Found: ' . $this->whois($node) . ', comparing with ' . $compare . '()') : NULL;
                    $phpQueryDebug = phpQuery::$debug;
                    phpQuery::$debug = false;
                    if (call_user_func_array(array($this, $compare), array($selector, $node))) {
                        $matched = true;
                    }
                    phpQuery::$debug = $phpQueryDebug;
                } else {
                    $matched = true;
                }
                if ($matched) {
                    if (phpQuery::$debug) {
                        $debug[] = $this->whois($node);
                    }
                    $stack[] = $node;
                }
            }
            if (phpQuery::$debug) {
                $this->debug('Matched ' . count($debug) . ': ' . implode(', ', $debug));
            }
            if ($detachAfter) {
                $this->root->removeChild($detachAfter);
            }
        }
        $this->elements = $stack;
    }
    public function find($selectors, $context = NULL, $noHistory = false)
    {
        if (!$noHistory) {
            $this->elementsBackup = $this->elements;
        }
        if ($context) {
            if (!is_array($context) && $context instanceof DOMELEMENT) {
                $this->elements = array($context);
            } else {
                if (is_array($context)) {
                    $this->elements = array();
                    foreach ($context as $c) {
                        if ($c instanceof DOMELEMENT) {
                            $this->elements[] = $c;
                        }
                    }
                } else {
                    if ($context instanceof self) {
                        $this->elements = $context->elements;
                    }
                }
            }
        }
        $queries = $this->parseSelector($selectors);
        $this->debug(array('FIND', $selectors, $queries));
        $XQuery = '';
        $oldStack = $this->elements;
        $stack = array();
        foreach ($queries as $selector) {
            $this->elements = $oldStack;
            $delimiterBefore = false;
            foreach ($selector as $s) {
                $isTag = extension_loaded('mbstring') && phpQuery::$mbstringSupport ? mb_ereg_match('^[\\w|\\||-]+$', $s) || $s == '*' : preg_match('@^[\\w|\\||-]+$@', $s) || $s == '*';
                if ($isTag) {
                    if ($this->isXML()) {
                        if (mb_strpos($s, '|') !== false) {
                            $ns = $tag = NULL;
                            list($ns, $tag) = explode('|', $s);
                            $XQuery .= $ns . ':' . $tag;
                        } else {
                            if ($s == '*') {
                                $XQuery .= '*';
                            } else {
                                $XQuery .= '*[local-name()=\'' . $s . '\']';
                            }
                        }
                    } else {
                        $XQuery .= $s;
                    }
                } else {
                    if ($s[0] == '#') {
                        if ($delimiterBefore) {
                            $XQuery .= '*';
                        }
                        $XQuery .= '[@id=\'' . substr($s, 1) . '\']';
                    } else {
                        if ($s[0] == '[') {
                            if ($delimiterBefore) {
                                $XQuery .= '*';
                            }
                            $attr = trim($s, '][');
                            $execute = false;
                            if (mb_strpos($s, '=')) {
                                $value = NULL;
                                list($attr, $value) = explode('=', $attr);
                                $value = trim($value, '\'"');
                                if ($this->isRegexp($attr)) {
                                    $attr = substr($attr, 0, -1);
                                    $execute = true;
                                    $XQuery .= '[@' . $attr . ']';
                                } else {
                                    $XQuery .= '[@' . $attr . '=\'' . $value . '\']';
                                }
                            } else {
                                $XQuery .= '[@' . $attr . ']';
                            }
                            if ($execute) {
                                $this->runQuery($XQuery, $s, 'is');
                                $XQuery = '';
                                if (!$this->length()) {
                                    break;
                                }
                            }
                        } else {
                            if ($s[0] == '.') {
                                if ($delimiterBefore) {
                                    $XQuery .= '*';
                                }
                                $XQuery .= '[@class]';
                                $this->runQuery($XQuery, $s, 'matchClasses');
                                $XQuery = '';
                                if (!$this->length()) {
                                    break;
                                }
                            } else {
                                if ($s[0] == '~') {
                                    $this->runQuery($XQuery);
                                    $XQuery = '';
                                    $this->elements = $this->siblings(substr($s, 1))->elements;
                                    if (!$this->length()) {
                                        break;
                                    }
                                } else {
                                    if ($s[0] == '+') {
                                        $this->runQuery($XQuery);
                                        $XQuery = '';
                                        $subSelector = substr($s, 1);
                                        $subElements = $this->elements;
                                        $this->elements = array();
                                        foreach ($subElements as $node) {
                                            $test = $node->nextSibling;
                                            while (!$test instanceof DOMELEMENT) {
                                                $test = $test->nextSibling;
                                            }
                                            if ($test && $this->is($subSelector, $test)) {
                                                $this->elements[] = $test;
                                            }
                                        }
                                        if (!$this->length()) {
                                            break;
                                        }
                                    } else {
                                        if ($s[0] == ':') {
                                            if ($XQuery) {
                                                $this->runQuery($XQuery);
                                                $XQuery = '';
                                            }
                                            if (!$this->length()) {
                                                break;
                                            }
                                            $this->pseudoClasses($s);
                                            if (!$this->length()) {
                                                break;
                                            }
                                        } else {
                                            if ($s == '>') {
                                                $XQuery .= '/';
                                                $delimiterBefore = 2;
                                            } else {
                                                if ($s == ' ') {
                                                    $XQuery .= '//';
                                                    $delimiterBefore = 2;
                                                } else {
                                                    phpQuery::debug('Unrecognized token \'' . $s . '\'');
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $delimiterBefore = $delimiterBefore === 2;
            }
            if ($XQuery && $XQuery != '//') {
                $this->runQuery($XQuery);
                $XQuery = '';
            }
            foreach ($this->elements as $node) {
                if (!$this->elementsContainsNode($node, $stack)) {
                    $stack[] = $node;
                }
            }
        }
        $this->elements = $stack;
        return $this->newInstance();
    }
    protected function pseudoClasses($class)
    {
        $class = ltrim($class, ':');
        $haveArgs = mb_strpos($class, '(');
        if ($haveArgs !== false) {
            $args = substr($class, $haveArgs + 1, -1);
            $class = substr($class, 0, $haveArgs);
        }
        switch ($class) {
            case 'even':
            case 'odd':
                $stack = array();
                foreach ($this->elements as $i => $node) {
                    if ($class == 'even' && $i % 2 == 0) {
                        $stack[] = $node;
                    } else {
                        if ($class == 'odd' && $i % 2) {
                            $stack[] = $node;
                        }
                    }
                }
                $this->elements = $stack;
                break;
            case 'eq':
                $k = intval($args);
                $this->elements = isset($this->elements[$k]) ? array($this->elements[$k]) : array();
                break;
            case 'gt':
                $this->elements = array_slice($this->elements, $args + 1);
                break;
            case 'lt':
                $this->elements = array_slice($this->elements, 0, $args + 1);
                break;
            case 'first':
                if (isset($this->elements[0])) {
                    $this->elements = array($this->elements[0]);
                }
                break;
            case 'last':
                if ($this->elements) {
                    $this->elements = array($this->elements[count($this->elements) - 1]);
                }
                break;
            case 'contains':
                $text = trim($args, '"\'');
                $stack = array();
                foreach ($this->elements as $node) {
                    if (mb_stripos($node->textContent, $text) === false) {
                        continue;
                    }
                    $stack[] = $node;
                }
                $this->elements = $stack;
                break;
            case 'not':
                $selector = self::unQuote($args);
                $this->elements = $this->not($selector)->stack();
                break;
            case 'slice':
                $args = explode(',', str_replace(', ', ',', trim($args, '"\'')));
                $start = $args[0];
                $end = isset($args[1]) ? $args[1] : NULL;
                if (0 < $end) {
                    $end = $end - $start;
                }
                $this->elements = array_slice($this->elements, $start, $end);
                break;
            case 'has':
                $selector = trim($args, '"\'');
                $stack = array();
                foreach ($this->stack(1) as $el) {
                    if ($this->find($selector, $el, true)->length) {
                        $stack[] = $el;
                    }
                }
                $this->elements = $stack;
                break;
            case 'submit':
            case 'reset':
                $this->elements = phpQuery::merge($this->map(array($this, 'is'), 'input[type=' . $class . ']', new CallbackParam()), $this->map(array($this, 'is'), 'button[type=' . $class . ']', new CallbackParam()));
                break;
            case 'input':
                $this->elements = $this->map(array($this, 'is'), 'input', new CallbackParam())->elements;
                break;
            case 'password':
            case 'checkbox':
            case 'radio':
            case 'hidden':
            case 'image':
            case 'file':
                $this->elements = $this->map(array($this, 'is'), 'input[type=' . $class . ']', new CallbackParam())->elements;
                break;
            case 'parent':
                $this->elements = $this->map(create_function('$node', '' . "\n" . '						return $node instanceof DOMELEMENT && $node->childNodes->length' . "\n" . '							? $node : null;'))->elements;
                break;
            case 'empty':
                $this->elements = $this->map(create_function('$node', '' . "\n" . '						return $node instanceof DOMELEMENT && $node->childNodes->length' . "\n" . '							? null : $node;'))->elements;
                break;
            case 'disabled':
            case 'selected':
            case 'checked':
                $this->elements = $this->map(array($this, 'is'), '[' . $class . ']', new CallbackParam())->elements;
                break;
            case 'enabled':
                $this->elements = $this->map(create_function('$node', '' . "\n" . '						return pq($node)->not(":disabled") ? $node : null;'))->elements;
                break;
            case 'header':
                $this->elements = $this->map(create_function('$node', '$isHeader = isset($node->tagName) && in_array($node->tagName, array(' . "\n" . '							"h1", "h2", "h3", "h4", "h5", "h6", "h7"' . "\n" . '						));' . "\n" . '						return $isHeader' . "\n" . '							? $node' . "\n" . '							: null;'))->elements;
                break;
            case 'only-child':
                $this->elements = $this->map(create_function('$node', 'return pq($node)->siblings()->size() == 0 ? $node : null;'))->elements;
                break;
            case 'first-child':
                $this->elements = $this->map(create_function('$node', 'return pq($node)->prevAll()->size() == 0 ? $node : null;'))->elements;
                break;
            case 'last-child':
                $this->elements = $this->map(create_function('$node', 'return pq($node)->nextAll()->size() == 0 ? $node : null;'))->elements;
                break;
            case 'nth-child':
                $param = trim($args, '"\'');
                if (!$param) {
                    break;
                }
                if ($param[0] == 'n') {
                    $param = '1' . $param;
                }
                if ($param == 'even' || $param == 'odd') {
                    $mapped = $this->map(create_function('$node, $param', '$index = pq($node)->prevAll()->size()+1;' . "\n" . '							if ($param == "even" && ($index%2) == 0)' . "\n" . '								return $node;' . "\n" . '							else if ($param == "odd" && $index%2 == 1)' . "\n" . '								return $node;' . "\n" . '							else' . "\n" . '								return null;'), new CallbackParam(), $param);
                } else {
                    if (1 < mb_strlen($param) && $param[1] == 'n') {
                        $mapped = $this->map(create_function('$node, $param', '$prevs = pq($node)->prevAll()->size();' . "\n" . '							$index = 1+$prevs;' . "\n" . '							$b = mb_strlen($param) > 3' . "\n" . '								? $param{3}' . "\n" . '								: 0;' . "\n" . '							$a = $param{0};' . "\n" . '							if ($b && $param{2} == "-")' . "\n" . '								$b = -$b;' . "\n" . '							if ($a > 0) {' . "\n" . '								return ($index-$b)%$a == 0' . "\n" . '									? $node' . "\n" . '									: null;' . "\n" . '								phpQuery::debug($a."*".floor($index/$a)."+$b-1 == ".($a*floor($index/$a)+$b-1)." ?= $prevs");' . "\n" . '								return $a*floor($index/$a)+$b-1 == $prevs' . "\n" . '										? $node' . "\n" . '										: null;' . "\n" . '							} else if ($a == 0)' . "\n" . '								return $index == $b' . "\n" . '										? $node' . "\n" . '										: null;' . "\n" . '							else' . "\n" . '								// negative value' . "\n" . '								return $index <= $b' . "\n" . '										? $node' . "\n" . '										: null;' . "\n" . '//							if (! $b)' . "\n" . '//								return $index%$a == 0' . "\n" . '//									? $node' . "\n" . '//									: null;' . "\n" . '//							else' . "\n" . '//								return ($index-$b)%$a == 0' . "\n" . '//									? $node' . "\n" . '//									: null;' . "\n" . '							'), new CallbackParam(), $param);
                    } else {
                        $mapped = $this->map(create_function('$node, $index', '$prevs = pq($node)->prevAll()->size();' . "\n" . '							if ($prevs && $prevs == $index-1)' . "\n" . '								return $node;' . "\n" . '							else if (! $prevs && $index == 1)' . "\n" . '								return $node;' . "\n" . '							else' . "\n" . '								return null;'), new CallbackParam(), $param);
                    }
                }
                $this->elements = $mapped->elements;
                break;
            default:
                $this->debug('Unknown pseudoclass \'' . $class . '\', skipping...');
        }
    }
    protected function __pseudoClassParam($paramsString)
    {
    }
    public function is($selector, $nodes = NULL)
    {
        phpQuery::debug(array('Is:', $selector));
        if (!$selector) {
            return false;
        }
        $oldStack = $this->elements;
        $returnArray = false;
        if ($nodes && is_array($nodes)) {
            $this->elements = $nodes;
        } else {
            if ($nodes) {
                $this->elements = array($nodes);
            }
        }
        $this->filter($selector, true);
        $stack = $this->elements;
        $this->elements = $oldStack;
        if ($nodes) {
            return $stack ? $stack : NULL;
        }
        return (bool) count($stack);
    }
    public function filterCallback($callback, $_skipHistory = false)
    {
        if (!$_skipHistory) {
            $this->elementsBackup = $this->elements;
            $this->debug('Filtering by callback');
        }
        $newStack = array();
        foreach ($this->elements as $index => $node) {
            $result = phpQuery::callbackRun($callback, array($index, $node));
            if (is_null($result) || !is_null($result) && $result) {
                $newStack[] = $node;
            }
        }
        $this->elements = $newStack;
        return $_skipHistory ? $this : $this->newInstance();
    }
    public function filter($selectors, $_skipHistory = false)
    {
        if ($selectors instanceof Callback || $selectors instanceof Closure) {
            return $this->filterCallback($selectors, $_skipHistory);
        }
        if (!$_skipHistory) {
            $this->elementsBackup = $this->elements;
        }
        $notSimpleSelector = array(' ', '>', '~', '+', '/');
        if (!is_array($selectors)) {
            $selectors = $this->parseSelector($selectors);
        }
        if (!$_skipHistory) {
            $this->debug(array('Filtering:', $selectors));
        }
        $finalStack = array();
        foreach ($selectors as $selector) {
            $stack = array();
            if (!$selector) {
                break;
            }
            if (in_array($selector[0], $notSimpleSelector)) {
                $selector = array_slice($selector, 1);
            }
            foreach ($this->stack() as $node) {
                $break = false;
                foreach ($selector as $s) {
                    if (!$node instanceof DOMELEMENT) {
                        if ($s[0] == '[') {
                            $attr = trim($s, '[]');
                            if (mb_strpos($attr, '=')) {
                                list($attr, $val) = explode('=', $attr);
                                if ($attr == 'nodeType' && $node->nodeType != $val) {
                                    $break = true;
                                }
                            }
                        } else {
                            $break = true;
                        }
                    } else {
                        if ($s[0] == '#') {
                            if ($node->getAttribute('id') != substr($s, 1)) {
                                $break = true;
                            }
                        } else {
                            if ($s[0] == '.') {
                                if (!$this->matchClasses($s, $node)) {
                                    $break = true;
                                }
                            } else {
                                if ($s[0] == '[') {
                                    $attr = trim($s, '[]');
                                    if (mb_strpos($attr, '=')) {
                                        list($attr, $val) = explode('=', $attr);
                                        $val = self::unQuote($val);
                                        if ($attr == 'nodeType') {
                                            if ($val != $node->nodeType) {
                                                $break = true;
                                            }
                                        } else {
                                            if ($this->isRegexp($attr)) {
                                                $val = extension_loaded('mbstring') && phpQuery::$mbstringSupport ? quotemeta(trim($val, '"\'')) : preg_quote(trim($val, '"\''), '@');
                                                switch (substr($attr, -1)) {
                                                    case '^':
                                                        $pattern = '^' . $val;
                                                        break;
                                                    case '*':
                                                        $pattern = '.*' . $val . '.*';
                                                        break;
                                                    case '$':
                                                        $pattern = '.*' . $val . '$';
                                                        break;
                                                }
                                                $attr = substr($attr, 0, -1);
                                                $isMatch = extension_loaded('mbstring') && phpQuery::$mbstringSupport ? mb_ereg_match($pattern, $node->getAttribute($attr)) : preg_match('@' . $pattern . '@', $node->getAttribute($attr));
                                                if (!$isMatch) {
                                                    $break = true;
                                                }
                                            } else {
                                                if ($node->getAttribute($attr) != $val) {
                                                    $break = true;
                                                }
                                            }
                                        }
                                    } else {
                                        if (!$node->hasAttribute($attr)) {
                                            $break = true;
                                        }
                                    }
                                } else {
                                    if ($s[0] == ':') {
                                    } else {
                                        if (trim($s)) {
                                            if ($s != '*') {
                                                if (isset($node->tagName)) {
                                                    if ($node->tagName != $s) {
                                                        $break = true;
                                                    }
                                                } else {
                                                    if ($s == 'html' && !$this->isRoot($node)) {
                                                        $break = true;
                                                    }
                                                }
                                            }
                                        } else {
                                            if (in_array($s, $notSimpleSelector)) {
                                                $break = true;
                                                $this->debug(array('Skipping non simple selector', $selector));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($break) {
                        break;
                    }
                }
                if (!$break) {
                    $stack[] = $node;
                }
            }
            $tmpStack = $this->elements;
            $this->elements = $stack;
            foreach ($selector as $s) {
                if ($s[0] == ':') {
                    $this->pseudoClasses($s);
                }
            }
            foreach ($this->elements as $node) {
                $finalStack[] = $node;
            }
            $this->elements = $tmpStack;
        }
        $this->elements = $finalStack;
        if ($_skipHistory) {
            return $this;
        } else {
            $this->debug('Stack length after filter(): ' . count($finalStack));
            return $this->newInstance();
        }
    }
    protected static function unQuote($value)
    {
        return $value[0] == '\'' || $value[0] == '"' ? substr($value, 1, -1) : $value;
    }
    public function load($url, $data = NULL, $callback = NULL)
    {
        if ($data && !is_array($data)) {
            $callback = $data;
            $data = NULL;
        }
        if (mb_strpos($url, ' ') !== false) {
            $matches = NULL;
            if (extension_loaded('mbstring') && phpQuery::$mbstringSupport) {
                mb_ereg('^([^ ]+) (.*)$', $url, $matches);
            } else {
                preg_match('^([^ ]+) (.*)$', $url, $matches);
            }
            $url = $matches[1];
            $selector = $matches[2];
            $this->_loadSelector = $selector;
        }
        $ajax = array('url' => $url, 'type' => $data ? 'POST' : 'GET', 'data' => $data, 'complete' => $callback, 'success' => array($this, '__loadSuccess'));
        phpQuery::ajax($ajax);
        return $this;
    }
    public function __loadSuccess($html)
    {
        if ($this->_loadSelector) {
            $html = phpQuery::newDocument($html)->find($this->_loadSelector);
            unset($this->_loadSelector);
        }
        foreach ($this->stack(1) as $node) {
            phpQuery::pq($node, $this->getDocumentID())->markup($html);
        }
    }
    public function css()
    {
        return $this;
    }
    public function show()
    {
        return $this;
    }
    public function hide()
    {
        return $this;
    }
    public function trigger($type, $data = array())
    {
        foreach ($this->elements as $node) {
            phpQueryEvents::trigger($this->getDocumentID(), $type, $data, $node);
        }
        return $this;
    }
    public function triggerHandler($type, $data = array())
    {
    }
    public function bind($type, $data, $callback = NULL)
    {
        if (!isset($callback)) {
            $callback = $data;
            $data = NULL;
        }
        foreach ($this->elements as $node) {
            phpQueryEvents::add($this->getDocumentID(), $node, $type, $data, $callback);
        }
        return $this;
    }
    public function unbind($type = NULL, $callback = NULL)
    {
        foreach ($this->elements as $node) {
            phpQueryEvents::remove($this->getDocumentID(), $node, $type, $callback);
        }
        return $this;
    }
    public function change($callback = NULL)
    {
        if ($callback) {
            return $this->bind('change', $callback);
        }
        return $this->trigger('change');
    }
    public function submit($callback = NULL)
    {
        if ($callback) {
            return $this->bind('submit', $callback);
        }
        return $this->trigger('submit');
    }
    public function click($callback = NULL)
    {
        if ($callback) {
            return $this->bind('click', $callback);
        }
        return $this->trigger('click');
    }
    public function wrapAllOld($wrapper)
    {
        $wrapper = pq($wrapper)->_clone();
        if (!$wrapper->length() || !$this->length()) {
            return $this;
        }
        $wrapper->insertBefore($this->elements[0]);
        $deepest = $wrapper->elements[0];
        while ($deepest->firstChild && $deepest->firstChild instanceof DOMELEMENT) {
            $deepest = $deepest->firstChild;
        }
        pq($deepest)->append($this);
        return $this;
    }
    public function wrapAll($wrapper)
    {
        if (!$this->length()) {
            return $this;
        }
        return phpQuery::pq($wrapper, $this->getDocumentID())->clone()->insertBefore($this->get(0))->map(array($this, '___wrapAllCallback'))->append($this);
    }
    public function ___wrapAllCallback($node)
    {
        $deepest = $node;
        while ($deepest->firstChild && $deepest->firstChild instanceof DOMELEMENT) {
            $deepest = $deepest->firstChild;
        }
        return $deepest;
    }
    public function wrapAllPHP($codeBefore, $codeAfter)
    {
        return $this->slice(0, 1)->beforePHP($codeBefore)->end()->slice(-1)->afterPHP($codeAfter)->end();
    }
    public function wrap($wrapper)
    {
        foreach ($this->stack() as $node) {
            phpQuery::pq($node, $this->getDocumentID())->wrapAll($wrapper);
        }
        return $this;
    }
    public function wrapPHP($codeBefore, $codeAfter)
    {
        foreach ($this->stack() as $node) {
            phpQuery::pq($node, $this->getDocumentID())->wrapAllPHP($codeBefore, $codeAfter);
        }
        return $this;
    }
    public function wrapInner($wrapper)
    {
        foreach ($this->stack() as $node) {
            phpQuery::pq($node, $this->getDocumentID())->contents()->wrapAll($wrapper);
        }
        return $this;
    }
    public function wrapInnerPHP($codeBefore, $codeAfter)
    {
        foreach ($this->stack(1) as $node) {
            phpQuery::pq($node, $this->getDocumentID())->contents()->wrapAllPHP($codeBefore, $codeAfter);
        }
        return $this;
    }
    public function contents()
    {
        $stack = array();
        foreach ($this->stack(1) as $el) {
            foreach ($el->childNodes as $node) {
                $stack[] = $node;
            }
        }
        return $this->newInstance($stack);
    }
    public function contentsUnwrap()
    {
        foreach ($this->stack(1) as $node) {
            if (!$node->parentNode) {
                continue;
            }
            $childNodes = array();
            foreach ($node->childNodes as $chNode) {
                $childNodes[] = $chNode;
            }
            foreach ($childNodes as $chNode) {
                $node->parentNode->insertBefore($chNode, $node);
            }
            $node->parentNode->removeChild($node);
        }
        return $this;
    }
    public function switchWith($markup)
    {
        $markup = pq($markup, $this->getDocumentID());
        $content = NULL;
        foreach ($this->stack(1) as $node) {
            pq($node)->contents()->toReference($content)->end()->replaceWith($markup->clone()->append($content));
        }
        return $this;
    }
    public function eq($num)
    {
        $oldStack = $this->elements;
        $this->elementsBackup = $this->elements;
        $this->elements = array();
        if (isset($oldStack[$num])) {
            $this->elements[] = $oldStack[$num];
        }
        return $this->newInstance();
    }
    public function size()
    {
        return count($this->elements);
    }
    public function length()
    {
        return $this->size();
    }
    public function count()
    {
        return $this->size();
    }
    public function end($level = 1)
    {
        return $this->previous ? $this->previous : $this;
    }
    public function _clone()
    {
        $newStack = array();
        $this->elementsBackup = $this->elements;
        foreach ($this->elements as $node) {
            $newStack[] = $node->cloneNode(true);
        }
        $this->elements = $newStack;
        return $this->newInstance();
    }
    public function replaceWithPHP($code)
    {
        return $this->replaceWith(phpQuery::php($code));
    }
    public function replaceWith($content)
    {
        return $this->after($content)->remove();
    }
    public function replaceAll($selector)
    {
        foreach (phpQuery::pq($selector, $this->getDocumentID()) as $node) {
            phpQuery::pq($node, $this->getDocumentID())->after($this->_clone())->remove();
        }
        return $this;
    }
    public function remove($selector = NULL)
    {
        $loop = $selector ? $this->filter($selector)->elements : $this->elements;
        foreach ($loop as $node) {
            if (!$node->parentNode) {
                continue;
            }
            if (isset($node->tagName)) {
                $this->debug('Removing \'' . $node->tagName . '\'');
            }
            $node->parentNode->removeChild($node);
            $event = new DOMEvent(array('target' => $node, 'type' => 'DOMNodeRemoved'));
            phpQueryEvents::trigger($this->getDocumentID(), $event->type, array($event), $node);
        }
        return $this;
    }
    protected function markupEvents($newMarkup, $oldMarkup, $node)
    {
        if ($node->tagName == 'textarea' && $newMarkup != $oldMarkup) {
            $event = new DOMEvent(array('target' => $node, 'type' => 'change'));
            phpQueryEvents::trigger($this->getDocumentID(), $event->type, array($event), $node);
        }
    }
    public function markup($markup = NULL, $callback1 = NULL, $callback2 = NULL, $callback3 = NULL)
    {
        $args = func_get_args();
        if ($this->documentWrapper->isXML) {
            return call_user_func_array(array($this, 'xml'), $args);
        } else {
            return call_user_func_array(array($this, 'html'), $args);
        }
    }
    public function markupOuter($callback1 = NULL, $callback2 = NULL, $callback3 = NULL)
    {
        $args = func_get_args();
        if ($this->documentWrapper->isXML) {
            return call_user_func_array(array($this, 'xmlOuter'), $args);
        } else {
            return call_user_func_array(array($this, 'htmlOuter'), $args);
        }
    }
    public function html($html = NULL, $callback1 = NULL, $callback2 = NULL, $callback3 = NULL)
    {
        if (isset($html)) {
            $nodes = $this->documentWrapper->import($html);
            $this->empty();
            foreach ($this->stack(1) as $alreadyAdded => $node) {
                if (($this->isXHTML() || $this->isHTML()) && $node->tagName == 'textarea') {
                    $oldHtml = pq($node, $this->getDocumentID())->markup();
                }
                foreach ($nodes as $newNode) {
                    $node->appendChild($alreadyAdded ? $newNode->cloneNode(true) : $newNode);
                }
                if (($this->isXHTML() || $this->isHTML()) && $node->tagName == 'textarea') {
                    $this->markupEvents($html, $oldHtml, $node);
                }
            }
            return $this;
        } else {
            $return = $this->documentWrapper->markup($this->elements, true);
            $args = func_get_args();
            foreach (array_slice($args, 1) as $callback) {
                $return = phpQuery::callbackRun($callback, array($return));
            }
            return $return;
        }
    }
    public function xml($xml = NULL, $callback1 = NULL, $callback2 = NULL, $callback3 = NULL)
    {
        $args = func_get_args();
        return call_user_func_array(array($this, 'html'), $args);
    }
    public function htmlOuter($callback1 = NULL, $callback2 = NULL, $callback3 = NULL)
    {
        $markup = $this->documentWrapper->markup($this->elements);
        $args = func_get_args();
        foreach ($args as $callback) {
            $markup = phpQuery::callbackRun($callback, array($markup));
        }
        return $markup;
    }
    public function xmlOuter($callback1 = NULL, $callback2 = NULL, $callback3 = NULL)
    {
        $args = func_get_args();
        return call_user_func_array(array($this, 'htmlOuter'), $args);
    }
    public function __toString()
    {
        return $this->markupOuter();
    }
    public function php($code = NULL)
    {
        return $this->markupPHP($code);
    }
    public function markupPHP($code = NULL)
    {
        return isset($code) ? $this->markup(phpQuery::php($code)) : phpQuery::markupToPHP($this->markup());
    }
    public function markupOuterPHP()
    {
        return phpQuery::markupToPHP($this->markupOuter());
    }
    public function children($selector = NULL)
    {
        $stack = array();
        foreach ($this->stack(1) as $node) {
            foreach ($node->childNodes as $newNode) {
                if ($newNode->nodeType != 1) {
                    continue;
                }
                if ($selector && !$this->is($selector, $newNode)) {
                    continue;
                }
                if ($this->elementsContainsNode($newNode, $stack)) {
                    continue;
                }
                $stack[] = $newNode;
            }
        }
        $this->elementsBackup = $this->elements;
        $this->elements = $stack;
        return $this->newInstance();
    }
    public function ancestors($selector = NULL)
    {
        return $this->children($selector);
    }
    public function append($content)
    {
        return $this->insert($content, 'append');
    }
    public function appendPHP($content)
    {
        return $this->insert('<php><!-- ' . $content . ' --></php>', 'append');
    }
    public function appendTo($seletor)
    {
        return $this->insert($seletor, 'appendTo');
    }
    public function prepend($content)
    {
        return $this->insert($content, 'prepend');
    }
    public function prependPHP($content)
    {
        return $this->insert('<php><!-- ' . $content . ' --></php>', 'prepend');
    }
    public function prependTo($seletor)
    {
        return $this->insert($seletor, 'prependTo');
    }
    public function before($content)
    {
        return $this->insert($content, 'before');
    }
    public function beforePHP($content)
    {
        return $this->insert('<php><!-- ' . $content . ' --></php>', 'before');
    }
    public function insertBefore($seletor)
    {
        return $this->insert($seletor, 'insertBefore');
    }
    public function after($content)
    {
        return $this->insert($content, 'after');
    }
    public function afterPHP($content)
    {
        return $this->insert('<php><!-- ' . $content . ' --></php>', 'after');
    }
    public function insertAfter($seletor)
    {
        return $this->insert($seletor, 'insertAfter');
    }
    public function insert($target, $type)
    {
        $this->debug('Inserting data with \'' . $type . '\'');
        $to = false;
        switch ($type) {
            case 'appendTo':
            case 'prependTo':
            case 'insertBefore':
            case 'insertAfter':
                $to = true;
        }
        switch (gettype($target)) {
            case 'string':
                $insertFrom = $insertTo = array();
                if ($to) {
                    $insertFrom = $this->elements;
                    if (phpQuery::isMarkup($target)) {
                        $insertTo = $this->documentWrapper->import($target);
                    } else {
                        $thisStack = $this->elements;
                        $this->toRoot();
                        $insertTo = $this->find($target)->elements;
                        $this->elements = $thisStack;
                    }
                } else {
                    $insertTo = $this->elements;
                    $insertFrom = $this->documentWrapper->import($target);
                }
                break;
            case 'object':
                $insertFrom = $insertTo = array();
                if ($target instanceof self) {
                    if ($to) {
                        $insertTo = $target->elements;
                        if ($this->documentFragment && $this->stackIsRoot()) {
                            $loop = $this->root->childNodes;
                        } else {
                            $loop = $this->elements;
                        }
                        $insertFrom = $this->getDocumentID() == $target->getDocumentID() ? $loop : $target->documentWrapper->import($loop);
                    } else {
                        $insertTo = $this->elements;
                        if ($target->documentFragment && $target->stackIsRoot()) {
                            $loop = $target->root->childNodes;
                        } else {
                            $loop = $target->elements;
                        }
                        $insertFrom = $this->getDocumentID() == $target->getDocumentID() ? $loop : $this->documentWrapper->import($loop);
                    }
                } else {
                    if ($target instanceof DOMNODE) {
                        if ($to) {
                            $insertTo = array($target);
                            if ($this->documentFragment && $this->stackIsRoot()) {
                                $loop = $this->root->childNodes;
                            } else {
                                $loop = $this->elements;
                            }
                            foreach ($loop as $fromNode) {
                                $insertFrom[] = !$fromNode->ownerDocument->isSameNode($target->ownerDocument) ? $target->ownerDocument->importNode($fromNode, true) : $fromNode;
                            }
                        } else {
                            if (!$target->ownerDocument->isSameNode($this->document)) {
                                $target = $this->document->importNode($target, true);
                            }
                            $insertTo = $this->elements;
                            $insertFrom[] = $target;
                        }
                    }
                }
                break;
        }
        phpQuery::debug('From ' . count($insertFrom) . '; To ' . count($insertTo) . ' nodes');
        foreach ($insertTo as $insertNumber => $toNode) {
            switch ($type) {
                case 'prependTo':
                case 'prepend':
                    $firstChild = $toNode->firstChild;
                    break;
                case 'insertAfter':
                case 'after':
                    $nextSibling = $toNode->nextSibling;
                    break;
            }
            foreach ($insertFrom as $fromNode) {
                $insert = $insertNumber ? $fromNode->cloneNode(true) : $fromNode;
                switch ($type) {
                    case 'appendTo':
                    case 'append':
                        $toNode->appendChild($insert);
                        $eventTarget = $insert;
                        break;
                    case 'prependTo':
                    case 'prepend':
                        $toNode->insertBefore($insert, $firstChild);
                        break;
                    case 'insertBefore':
                    case 'before':
                        if (!$toNode->parentNode) {
                            throw new Exception('No parentNode, can\'t do ' . $type . '()');
                        } else {
                            $toNode->parentNode->insertBefore($insert, $toNode);
                        }
                        break;
                    case 'insertAfter':
                    case 'after':
                        if (!$toNode->parentNode) {
                            throw new Exception('No parentNode, can\'t do ' . $type . '()');
                        } else {
                            $toNode->parentNode->insertBefore($insert, $nextSibling);
                        }
                        break;
                }
                $event = new DOMEvent(array('target' => $insert, 'type' => 'DOMNodeInserted'));
                phpQueryEvents::trigger($this->getDocumentID(), $event->type, array($event), $insert);
            }
        }
        return $this;
    }
    public function index($subject)
    {
        $index = -1;
        $subject = $subject instanceof phpQueryObject ? $subject->elements[0] : $subject;
        foreach ($this->newInstance() as $k => $node) {
            if ($node->isSameNode($subject)) {
                $index = $k;
            }
        }
        return $index;
    }
    public function slice($start, $end = NULL)
    {
        if (0 < $end) {
            $end = $end - $start;
        }
        return $this->newInstance(array_slice($this->elements, $start, $end));
    }
    public function reverse()
    {
        $this->elementsBackup = $this->elements;
        $this->elements = array_reverse($this->elements);
        return $this->newInstance();
    }
    public function text($text = NULL, $callback1 = NULL, $callback2 = NULL, $callback3 = NULL)
    {
        if (isset($text)) {
            return $this->html(htmlspecialchars($text));
        }
        $args = func_get_args();
        $args = array_slice($args, 1);
        $return = '';
        foreach ($this->elements as $node) {
            $text = $node->textContent;
            if (1 < count($this->elements) && $text) {
                $text .= "\n";
            }
            foreach ($args as $callback) {
                $text = phpQuery::callbackRun($callback, array($text));
            }
            $return .= $text;
        }
        return $return;
    }
    public function plugin($class, $file = NULL)
    {
        phpQuery::plugin($class, $file);
        return $this;
    }
    public static function extend($class, $file = NULL)
    {
        return $this->plugin($class, $file);
    }
    public function __call($method, $args)
    {
        $aliasMethods = array('clone', 'empty');
        if (isset(phpQuery::$extendMethods[$method])) {
            array_unshift($args, $this);
            return phpQuery::callbackRun(phpQuery::$extendMethods[$method], $args);
        } else {
            if (isset(phpQuery::$pluginsMethods[$method])) {
                array_unshift($args, $this);
                $class = phpQuery::$pluginsMethods[$method];
                $realClass = 'phpQueryObjectPlugin_' . $class;
                $return = call_user_func_array(array($realClass, $method), $args);
                return is_null($return) ? $this : $return;
            } else {
                if (in_array($method, $aliasMethods)) {
                    return call_user_func_array(array($this, '_' . $method), $args);
                } else {
                    throw new Exception('Method \'' . $method . '\' doesnt exist');
                }
            }
        }
    }
    public function _next($selector = NULL)
    {
        return $this->newInstance($this->getElementSiblings('nextSibling', $selector, true));
    }
    public function _prev($selector = NULL)
    {
        return $this->prev($selector);
    }
    public function prev($selector = NULL)
    {
        return $this->newInstance($this->getElementSiblings('previousSibling', $selector, true));
    }
    public function prevAll($selector = NULL)
    {
        return $this->newInstance($this->getElementSiblings('previousSibling', $selector));
    }
    public function nextAll($selector = NULL)
    {
        return $this->newInstance($this->getElementSiblings('nextSibling', $selector));
    }
    protected function getElementSiblings($direction, $selector = NULL, $limitToOne = false)
    {
        $stack = array();
        $count = 0;
        foreach ($this->stack() as $node) {
            $test = $node;
            while (isset($test->{$direction}) && $test->{$direction}) {
                $test = $test->{$direction};
                if (!$test instanceof DOMELEMENT) {
                    continue;
                }
                $stack[] = $test;
                if ($limitToOne) {
                    break;
                }
            }
        }
        if ($selector) {
            $stackOld = $this->elements;
            $this->elements = $stack;
            $stack = $this->filter($selector, true)->stack();
            $this->elements = $stackOld;
        }
        return $stack;
    }
    public function siblings($selector = NULL)
    {
        $stack = array();
        $siblings = array_merge($this->getElementSiblings('previousSibling', $selector), $this->getElementSiblings('nextSibling', $selector));
        foreach ($siblings as $node) {
            if (!$this->elementsContainsNode($node, $stack)) {
                $stack[] = $node;
            }
        }
        return $this->newInstance($stack);
    }
    public function not($selector = NULL)
    {
        if (is_string($selector)) {
            phpQuery::debug(array('not', $selector));
        } else {
            phpQuery::debug('not');
        }
        $stack = array();
        if ($selector instanceof self || $selector instanceof DOMNODE) {
            foreach ($this->stack() as $node) {
                if ($selector instanceof self) {
                    $matchFound = false;
                    foreach ($selector->stack() as $notNode) {
                        if ($notNode->isSameNode($node)) {
                            $matchFound = true;
                        }
                    }
                    if (!$matchFound) {
                        $stack[] = $node;
                    }
                } else {
                    if ($selector instanceof DOMNODE) {
                        if (!$selector->isSameNode($node)) {
                            $stack[] = $node;
                        }
                    } else {
                        if (!$this->is($selector)) {
                            $stack[] = $node;
                        }
                    }
                }
            }
        } else {
            $orgStack = $this->stack();
            $matched = $this->filter($selector, true)->stack();
            foreach ($orgStack as $node) {
                if (!$this->elementsContainsNode($node, $matched)) {
                    $stack[] = $node;
                }
            }
        }
        return $this->newInstance($stack);
    }
    public function add($selector = NULL)
    {
        if (!$selector) {
            return $this;
        }
        $stack = array();
        $this->elementsBackup = $this->elements;
        $found = phpQuery::pq($selector, $this->getDocumentID());
        $this->merge($found->elements);
        return $this->newInstance();
    }
    protected function merge()
    {
        foreach (func_get_args() as $nodes) {
            foreach ($nodes as $newNode) {
                if (!$this->elementsContainsNode($newNode)) {
                    $this->elements[] = $newNode;
                }
            }
        }
    }
    protected function elementsContainsNode($nodeToCheck, $elementsStack = NULL)
    {
        $loop = !is_null($elementsStack) ? $elementsStack : $this->elements;
        foreach ($loop as $node) {
            if ($node->isSameNode($nodeToCheck)) {
                return true;
            }
        }
        return false;
    }
    public function parent($selector = NULL)
    {
        $stack = array();
        foreach ($this->elements as $node) {
            if ($node->parentNode && !$this->elementsContainsNode($node->parentNode, $stack)) {
                $stack[] = $node->parentNode;
            }
        }
        $this->elementsBackup = $this->elements;
        $this->elements = $stack;
        if ($selector) {
            $this->filter($selector, true);
        }
        return $this->newInstance();
    }
    public function parents($selector = NULL)
    {
        $stack = array();
        if (!$this->elements) {
            $this->debug('parents() - stack empty');
        }
        foreach ($this->elements as $node) {
            $test = $node;
            while ($test->parentNode) {
                $test = $test->parentNode;
                if ($this->isRoot($test)) {
                    break;
                }
                if (!$this->elementsContainsNode($test, $stack)) {
                    $stack[] = $test;
                    continue;
                }
            }
        }
        $this->elementsBackup = $this->elements;
        $this->elements = $stack;
        if ($selector) {
            $this->filter($selector, true);
        }
        return $this->newInstance();
    }
    public function stack($nodeTypes = NULL)
    {
        if (!isset($nodeTypes)) {
            return $this->elements;
        }
        if (!is_array($nodeTypes)) {
            $nodeTypes = array($nodeTypes);
        }
        $return = array();
        foreach ($this->elements as $node) {
            if (in_array($node->nodeType, $nodeTypes)) {
                $return[] = $node;
            }
        }
        return $return;
    }
    protected function attrEvents($attr, $oldAttr, $oldValue, $node)
    {
        if (!$this->isXHTML() && !$this->isHTML()) {
            return NULL;
        }
        $event = NULL;
        $isInputValue = $node->tagName == 'input' && (in_array($node->getAttribute('type'), array('text', 'password', 'hidden')) || !$node->getAttribute('type'));
        $isRadio = $node->tagName == 'input' && $node->getAttribute('type') == 'radio';
        $isCheckbox = $node->tagName == 'input' && $node->getAttribute('type') == 'checkbox';
        $isOption = $node->tagName == 'option';
        if ($isInputValue && $attr == 'value' && $oldValue != $node->getAttribute($attr)) {
            $event = new DOMEvent(array('target' => $node, 'type' => 'change'));
        } else {
            if (($isRadio || $isCheckbox) && $attr == 'checked' && (!$oldAttr && $node->hasAttribute($attr) || !$node->hasAttribute($attr) && $oldAttr)) {
                $event = new DOMEvent(array('target' => $node, 'type' => 'change'));
            } else {
                if ($isOption && $node->parentNode && $attr == 'selected' && (!$oldAttr && $node->hasAttribute($attr) || !$node->hasAttribute($attr) && $oldAttr)) {
                    $event = new DOMEvent(array('target' => $node->parentNode, 'type' => 'change'));
                }
            }
        }
        if ($event) {
            phpQueryEvents::trigger($this->getDocumentID(), $event->type, array($event), $node);
        }
    }
    public function attr($attr = NULL, $value = NULL)
    {
        foreach ($this->stack(1) as $node) {
            if (!is_null($value)) {
                $loop = $attr == '*' ? $this->getNodeAttrs($node) : array($attr);
                foreach ($loop as $a) {
                    $oldValue = $node->getAttribute($a);
                    $oldAttr = $node->hasAttribute($a);
                    @'node'->setAttribute($a, $value);
                    $this->attrEvents($a, $oldAttr, $oldValue, $node);
                }
            } else {
                if ($attr == '*') {
                    $return = array();
                    foreach ($node->attributes as $n => $v) {
                        $return[$n] = $v->value;
                    }
                    return $return;
                } else {
                    return $node->hasAttribute($attr) ? $node->getAttribute($attr) : NULL;
                }
            }
        }
        return is_null($value) ? '' : $this;
    }
    protected function getNodeAttrs($node)
    {
        $return = array();
        foreach ($node->attributes as $n => $o) {
            $return[] = $n;
        }
        return $return;
    }
    public function attrPHP($attr, $code)
    {
        if (!is_null($code)) {
            $value = '<' . '?php ' . $code . ' ?' . '>';
        }
        foreach ($this->stack(1) as $node) {
            if (!is_null($code)) {
                $node->setAttribute($attr, $value);
            } else {
                if ($attr == '*') {
                    $return = array();
                    foreach ($node->attributes as $n => $v) {
                        $return[$n] = $v->value;
                    }
                    return $return;
                } else {
                    return $node->getAttribute($attr);
                }
            }
        }
        return $this;
    }
    public function removeAttr($attr)
    {
        foreach ($this->stack(1) as $node) {
            $loop = $attr == '*' ? $this->getNodeAttrs($node) : array($attr);
            foreach ($loop as $a) {
                $oldValue = $node->getAttribute($a);
                $node->removeAttribute($a);
                $this->attrEvents($a, $oldValue, NULL, $node);
            }
        }
        return $this;
    }
    public function val($val = NULL)
    {
        if (!isset($val)) {
            if ($this->eq(0)->is('select')) {
                $selected = $this->eq(0)->find('option[selected=selected]');
                if ($selected->is('[value]')) {
                    return $selected->attr('value');
                } else {
                    return $selected->text();
                }
            } else {
                if ($this->eq(0)->is('textarea')) {
                    return $this->eq(0)->markup();
                } else {
                    return $this->eq(0)->attr('value');
                }
            }
        } else {
            $_val = NULL;
            foreach ($this->stack(1) as $node) {
                $node = pq($node, $this->getDocumentID());
                if (is_array($val) && in_array($node->attr('type'), array('checkbox', 'radio'))) {
                    $isChecked = in_array($node->attr('value'), $val) || in_array($node->attr('name'), $val);
                    if ($isChecked) {
                        $node->attr('checked', 'checked');
                    } else {
                        $node->removeAttr('checked');
                    }
                } else {
                    if ($node->get(0)->tagName == 'select') {
                        if (!isset($_val)) {
                            $_val = array();
                            if (!is_array($val)) {
                                $_val = array((string) $val);
                            } else {
                                foreach ($val as $v) {
                                    $_val[] = $v;
                                }
                            }
                        }
                        foreach ($node['option']->stack(1) as $option) {
                            $option = pq($option, $this->getDocumentID());
                            $selected = false;
                            $selected = is_null($option->attr('value')) ? in_array($option->markup(), $_val) : in_array($option->attr('value'), $_val);
                            if ($selected) {
                                $option->attr('selected', 'selected');
                            } else {
                                $option->removeAttr('selected');
                            }
                        }
                    } else {
                        if ($node->get(0)->tagName == 'textarea') {
                            $node->markup($val);
                        } else {
                            $node->attr('value', $val);
                        }
                    }
                }
            }
        }
        return $this;
    }
    public function andSelf()
    {
        if ($this->previous) {
            $this->elements = array_merge($this->elements, $this->previous->elements);
        }
        return $this;
    }
    public function addClass($className)
    {
        if (!$className) {
            return $this;
        }
        foreach ($this->stack(1) as $node) {
            if (!$this->is('.' . $className, $node)) {
                $node->setAttribute('class', trim($node->getAttribute('class') . ' ' . $className));
            }
        }
        return $this;
    }
    public function addClassPHP($className)
    {
        foreach ($this->stack(1) as $node) {
            $classes = $node->getAttribute('class');
            $newValue = $classes ? $classes . ' <' . '?php ' . $className . ' ?' . '>' : '<' . '?php ' . $className . ' ?' . '>';
            $node->setAttribute('class', $newValue);
        }
        return $this;
    }
    public function hasClass($className)
    {
        foreach ($this->stack(1) as $node) {
            if ($this->is('.' . $className, $node)) {
                return true;
            }
        }
        return false;
    }
    public function removeClass($className)
    {
        foreach ($this->stack(1) as $node) {
            $classes = explode(' ', $node->getAttribute('class'));
            if (in_array($className, $classes)) {
                $classes = array_diff($classes, array($className));
                if ($classes) {
                    $node->setAttribute('class', implode(' ', $classes));
                } else {
                    $node->removeAttribute('class');
                }
            }
        }
        return $this;
    }
    public function toggleClass($className)
    {
        foreach ($this->stack(1) as $node) {
            if ($this->is($node, '.' . $className)) {
                $this->removeClass($className);
            } else {
                $this->addClass($className);
            }
        }
        return $this;
    }
    public function _empty()
    {
        foreach ($this->stack(1) as $node) {
            $node->nodeValue = '';
        }
        return $this;
    }
    public function each($callback, $param1 = NULL, $param2 = NULL, $param3 = NULL)
    {
        $paramStructure = NULL;
        if (1 < func_num_args()) {
            $paramStructure = func_get_args();
            $paramStructure = array_slice($paramStructure, 1);
        }
        foreach ($this->elements as $v) {
            phpQuery::callbackRun($callback, array($v), $paramStructure);
        }
        return $this;
    }
    public function callback($callback, $param1 = NULL, $param2 = NULL, $param3 = NULL)
    {
        $params = func_get_args();
        $params[0] = $this;
        phpQuery::callbackRun($callback, $params);
        return $this;
    }
    public function map($callback, $param1 = NULL, $param2 = NULL, $param3 = NULL)
    {
        $params = func_get_args();
        array_unshift($params, $this->elements);
        return $this->newInstance(call_user_func_array(array('phpQuery', 'map'), $params));
    }
    public function data($key, $value = NULL)
    {
        if (!isset($value)) {
            return phpQuery::data($this->get(0), $key, $value, $this->getDocumentID());
        } else {
            foreach ($this as $node) {
                phpQuery::data($node, $key, $value, $this->getDocumentID());
            }
            return $this;
        }
    }
    public function removeData($key)
    {
        foreach ($this as $node) {
            phpQuery::removeData($node, $key, $this->getDocumentID());
        }
        return $this;
    }
    public function rewind()
    {
        $this->debug('iterating foreach');
        $this->elementsBackup = $this->elements;
        $this->elementsInterator = $this->elements;
        $this->valid = isset($this->elements[0]) ? 1 : 0;
        $this->current = 0;
    }
    public function current()
    {
        return $this->elementsInterator[$this->current];
    }
    public function key()
    {
        return $this->current;
    }
    public function next($cssSelector = NULL)
    {
        $this->valid = isset($this->elementsInterator[$this->current + 1]) ? true : false;
        if (!$this->valid && $this->elementsInterator) {
            $this->elementsInterator = NULL;
        } else {
            if ($this->valid) {
                $this->current++;
            } else {
                return $this->_next($cssSelector);
            }
        }
    }
    public function valid()
    {
        return $this->valid;
    }
    public function offsetExists($offset)
    {
        return 0 < $this->find($offset)->size();
    }
    public function offsetGet($offset)
    {
        return $this->find($offset);
    }
    public function offsetSet($offset, $value)
    {
        $this->find($offset)->html($value);
    }
    public function offsetUnset($offset)
    {
        throw new Exception('Can\'t do unset, use array interface only for calling queries and replacing HTML.');
    }
    protected function getNodeXpath($oneNode = NULL, $namespace = NULL)
    {
        $return = array();
        $loop = $oneNode ? array($oneNode) : $this->elements;
        foreach ($loop as $node) {
            if ($node instanceof DOMDOCUMENT) {
                $return[] = '';
                continue;
            }
            $xpath = array();
            while (!$node instanceof DOMDOCUMENT) {
                $i = 1;
                $sibling = $node;
                while ($sibling->previousSibling) {
                    $sibling = $sibling->previousSibling;
                    $isElement = $sibling instanceof DOMELEMENT;
                    if ($isElement && $sibling->tagName == $node->tagName) {
                        $i++;
                    }
                }
                $xpath[] = $this->isXML() ? '*[local-name()=\'' . $node->tagName . '\'][' . $i . ']' : $node->tagName . '[' . $i . ']';
                $node = $node->parentNode;
            }
            $xpath = join('/', array_reverse($xpath));
            $return[] = '/' . $xpath;
        }
        return $oneNode ? $return[0] : $return;
    }
    public function whois($oneNode = NULL)
    {
        $return = array();
        $loop = $oneNode ? array($oneNode) : $this->elements;
        foreach ($loop as $node) {
            if (isset($node->tagName)) {
                $tag = in_array($node->tagName, array('php', 'js')) ? strtoupper($node->tagName) : $node->tagName;
                $return[] = $tag . ($node->getAttribute('id') ? '#' . $node->getAttribute('id') : '') . ($node->getAttribute('class') ? '.' . join('.', split(' ', $node->getAttribute('class'))) : '') . ($node->getAttribute('name') ? '[name="' . $node->getAttribute('name') . '"]' : '') . ($node->getAttribute('value') && strpos($node->getAttribute('value'), '<' . '?php') === false ? '[value="' . substr(str_replace("\n", '', $node->getAttribute('value')), 0, 15) . '"]' : '') . ($node->getAttribute('value') && strpos($node->getAttribute('value'), '<' . '?php') !== false ? '[value=PHP]' : '') . ($node->getAttribute('selected') ? '[selected]' : '') . ($node->getAttribute('checked') ? '[checked]' : '');
            } else {
                if ($node instanceof DOMTEXT) {
                    if (trim($node->textContent)) {
                        $return[] = 'Text:' . substr(str_replace("\n", ' ', $node->textContent), 0, 15);
                    }
                }
            }
        }
        return $oneNode && isset($return[0]) ? $return[0] : $return;
    }
    public function dump()
    {
        print 'DUMP #' . phpQuery::$dumpCount++ . ' ';
        $debug = phpQuery::$debug;
        phpQuery::$debug = false;
        var_dump($this->htmlOuter());
        return $this;
    }
    public function dumpWhois()
    {
        print 'DUMP #' . phpQuery::$dumpCount++ . ' ';
        $debug = phpQuery::$debug;
        phpQuery::$debug = false;
        var_dump('whois', $this->whois());
        phpQuery::$debug = $debug;
        return $this;
    }
    public function dumpLength()
    {
        print 'DUMP #' . phpQuery::$dumpCount++ . ' ';
        $debug = phpQuery::$debug;
        phpQuery::$debug = false;
        var_dump('length', $this->length());
        phpQuery::$debug = $debug;
        return $this;
    }
    public function dumpTree($html = true, $title = true)
    {
        $output = $title ? 'DUMP #' . phpQuery::$dumpCount++ . ' ' . "\n" . '' : '';
        $debug = phpQuery::$debug;
        phpQuery::$debug = false;
        foreach ($this->stack() as $node) {
            $output .= $this->__dumpTree($node);
        }
        phpQuery::$debug = $debug;
        print $html ? nl2br(str_replace(' ', '&nbsp;', $output)) : $output;
        return $this;
    }
    private function __dumpTree($node, $intend = 0)
    {
        $whois = $this->whois($node);
        $return = '';
        if ($whois) {
            $return .= str_repeat(' - ', $intend) . $whois . "\n";
        }
        if (isset($node->childNodes)) {
            foreach ($node->childNodes as $chNode) {
                $return .= $this->__dumpTree($chNode, $intend + 1);
            }
        }
        return $return;
    }
    public function dumpDie()
    {
        print __FILE__ . ':' . 4296;
        var_dump($this->htmlOuter());
        exit;
    }
}
define('DOMDOCUMENT', 'DOMDocument');
define('DOMELEMENT', 'DOMElement');
define('DOMNODELIST', 'DOMNodeList');
define('DOMNODE', 'DOMNode');
if (!function_exists('mb_internal_encoding')) {
    function mb_internal_encoding($enc)
    {
        return true;
    }
}
if (!function_exists('mb_regex_encoding')) {
    function mb_regex_encoding($enc)
    {
        return true;
    }
}
if (!function_exists('mb_strlen')) {
    function mb_strlen($str)
    {
        return strlen($str);
    }
}
if (!function_exists('mb_strpos')) {
    function mb_strpos($haystack, $needle, $offset = 0)
    {
        return strpos($haystack, $needle, $offset);
    }
}
if (!function_exists('mb_stripos')) {
    function mb_stripos($haystack, $needle, $offset = 0)
    {
        return stripos($haystack, $needle, $offset);
    }
}
if (!function_exists('mb_substr')) {
    function mb_substr($str, $start, $length = 0)
    {
        return substr($str, $start, $length);
    }
}
if (!function_exists('mb_substr_count')) {
    function mb_substr_count($haystack, $needle)
    {
        return substr_count($haystack, $needle);
    }
}
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/phpQuery/' . PATH_SEPARATOR . dirname(__FILE__) . '/phpQuery/plugins/');
phpQuery::$plugins = new phpQueryPlugins();
if (file_exists(dirname(__FILE__) . '/phpQuery/bootstrap.php')) {
    require_once dirname(__FILE__) . '/phpQuery/bootstrap.php';
}