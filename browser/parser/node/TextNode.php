<?php

namespace tplLib;

class TextNode extends AbstractNode {

    private $text;

    public function __construct($text) {
        parent::__construct('');

        $this->text = $text;
    }

    public function getText() {
        return $this->text;
    }

    public function render($scope) {
        return $scope->replaceCurlyExpression($this->text);
    }

}
