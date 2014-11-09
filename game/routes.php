<?php


if ($user->requiresLogin()) {
$router->get('', function() use ($htmlTemplate, $csrfToken, $translator) {
return $htmlTemplate->render('login.phtml', [
'csrfToken' => $csrfToken,
'username' => null,
'login' => true,
'title' => 'Login',
]);
});
$router->post('', function() use ($htmlTemplate, $csrfToken, $request, $user, $request) {
if ($csrfToken->validate($request->post('csrfToken')) && $user->login($request->post('username'), $request->post('password'), $request->getIp())) {
header('Location: ' . $request->getUrl());
exit;
}
return $htmlTemplate->render('login.phtml', [
'csrfToken' => $csrfToken,
'username' => $request->post('username'),
'login' => true,
]);
});
return;
}
