<?php

class TextResponseFormatView extends RESTServerView {

    public function render() {
        return $this->model;
    }
}
