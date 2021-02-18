<?php

namespace stf;

function getForm() : Form {
    $form = getBrowser()->getPage()->getForm();

    if ($form === null) {
        fail(ERROR_W07, "Current page does not contain form");
    }

    return $form;
}
