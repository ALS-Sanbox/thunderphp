<?php
$user = new Siteusers\Siteusers();

if ($csrf = csrf_verify($req->post('_token')) && $user->validate_insert($req->post())) {
    $postdata = $req->post();

    $newData = ([
            'first_name'=>$postdata['first_name'],
            'last_name' =>$postdata['last_name'],
            'image'     =>'',
            'email'     =>$postdata['email'],
            'password'     => password_hash($postdata['confirmPassword'] ?? '', PASSWORD_DEFAULT),
    ]);

    $user->insert($newData);

    redirect($vars['login_page']);
} else {

    if(!$csrf){
        $user->errors['email'] = "Form expired! Please Refresh";
        message("Form expired! Please Refresh");
    }
    set_value('errors', $user->errors);
}
