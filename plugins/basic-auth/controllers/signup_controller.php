<?php
$user = new Siteusers\Siteusers();

if ($csrf = csrf_verify($req->post('_token')) && $user->validate_insert($req->post())) {
    $postdata = $req->post();

    $newData = ([
            'first_name'=>$postdata['first_name'],
            'last_name' =>$postdata['last_name'],
            'image'     =>'',
            'email'     =>$postdata['email'],
            // Hash the actual "Password" field - not "confirmPassword", which
            // is only ever compared against it in validate_insert(), never
            // meant to be the value that's actually stored.
            'password'     => password_hash($postdata['password'] ?? '', PASSWORD_DEFAULT),
    ]);

    $user->insert($newData);

    message("Account created! Please log in.", "success");
    redirect($vars['login_page']);
} else {

    if(!$csrf){
        $user->errors['email'] = "Form expired! Please Refresh";
    }
    message(implode(' ', $user->errors), 'fail');
    set_value('errors', $user->errors);
}
