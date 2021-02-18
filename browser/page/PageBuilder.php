<?php

namespace stf;

require_once __DIR__ . '/../parser/HtmlLexer.php';
require_once __DIR__ . '/../parser/HtmlParser.php';
require_once __DIR__ . '/../parser/TreeBuilderActions.php';
require_once __DIR__ . '/../parser/node/AbstractNode.php';
require_once __DIR__ . '/../parser/node/WsNode.php';
require_once __DIR__ . '/../parser/node/TextNode.php';
require_once __DIR__ . '/../parser/node/MiscNode.php';

require_once 'Form.php';
require_once 'TextField.php';
require_once 'Button.php';
require_once 'Page.php';
require_once 'Link.php';
require_once 'Element.php';

require_once 'FormBuilder.php';

use Exception;
use tplLib\HtmlLexer;
use tplLib\HtmlParser;
use tplLib\TagNode;
use tplLib\TextNode;
use tplLib\WsNode;
use tplLib\AbstractNode;
use tplLib\TreeBuilderActions;
use \RuntimeException;

class PageBuilder {

    private string $html;
    private $tree;

    public function __construct(string $html) {
        $this->html = $html;
        $this->tree = $this->buildNodeTree($html);
    }

    function getPage() : Page {
        $text = html_entity_decode($this->getText($this->tree));

        $page = new Page($this->html, $text,
            $this->getLinks($this->tree), $this->getForm());

        $page->setElements($this->getAllElements($this->tree));

        return $page;
    }

    private function getForm() : ?Form {
        $forms = $this->findNodesByTagNames($this->tree, ['form']);

        if (count($forms) === 0) {
            return null;
        }

        if (count($forms) > 1) {
            throw new RuntimeException("This framework supports only one form per page");
        }

        $formElements = $this->findNodesByTagNames(
            $forms[0], ['input', 'button']);

        return (new FormBuilder($forms[0], $formElements))->buildForm();
    }

    private function buildNodeTree($html) {
        try {
            $tokens = (new HtmlLexer($html))->tokenize();

            $builder = new TreeBuilderActions();

            (new HtmlParser($tokens, $builder))->parse();

        } catch (Exception $e) {
            throw $this->error($e);
        }

        return $builder->getResult();
    }

    private function error($e): RuntimeException {
        $message = sprintf("Incorrect HTML at %s \n %s\n",
            $this->locationString($e->pos), $e->message);

        return new FrameworkException(ERROR_W02, $message);
    }

    private function locationString($pos): string {
        $textParsed = substr($this->html, 0, $pos);
        $lines = explode("\n", $textParsed);
        $lineNr = count($lines);
        $colNr = strlen($lines[$lineNr - 1]) + 1; // +1: starts from 1

        return sprintf('line %s, column %s', $lineNr, $colNr);
    }

    private function findNodesByTagNames($node, $names) : array {
        $nodeList = array_filter($this->getAllTagNodes($node), function ($each) use ($names) {
            return in_array($each->getTagName(), $names);
        });

        return array_values($nodeList);
    }

    private function getAllElements(AbstractNode $node) : array {
        $nodes = $this->getAllTagNodes($node);

        return array_map(function ($node) {
            return new Element($node);
        }, $nodes);
    }

    private function getAllTagNodes(AbstractNode $node) : array {
        $result = [];

        if ($node instanceof TagNode) {
            $result[] = $node;
        }

        foreach ($node->getChildren() as $child) {
            $result = array_merge(
                $result,
                $this->getAllTagNodes($child));
        }

        return $result;
    }

    private function getLinks($tree) : array {
        $nodes = $this->findNodesByTagNames($tree, ['a']);

        return array_map(function ($linkNode) {
            return new Link($this->getLinkText($linkNode),
                $linkNode->getAttributeValue('href') ?? '',
                $linkNode->getAttributeValue('id') ?? '');
        }, $nodes);
    }

    private function getLinkText($linkNode) : string {
        return join("", $this->getTextLines($linkNode, true));
    }

    private function getText($node) : string {
        return join("\n", $this->getTextLines($node));
    }

    private function getTextLines($node, $withWhiteSpace = false) : array {
        if ($node instanceof TextNode) {
            return [$node->getText()];
        } else if ($withWhiteSpace && $node instanceof WsNode) {
            return [$node->getText()];
        }

        $childTexts = [];
        foreach ($node->getChildren() as $child) {
            $childTextLines = $this->getTextLines($child, $withWhiteSpace);
            $childTexts = [...$childTexts, ...$childTextLines];
        }

        return array_filter($childTexts);
    }
}


