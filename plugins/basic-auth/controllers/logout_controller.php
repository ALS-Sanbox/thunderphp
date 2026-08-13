<?php

if (!csrf_verify($req->get('_token'))) {
    message('Form expired! Please refresh and try again.', 'fail');
    redirect($vars['login_page']);
}

$ses -> logout();

redirect($vars['login_page']);