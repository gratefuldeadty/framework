<?php

// Routes

// Login
if ($users->requiresLogin())
{
        $router->get('', function() use ($htmlTemplate, $translator){
                return $htmlTemplate->render('login', [
                                'username' => null,
                                'login' => true,
                                'title' => 'Login',
                        ]);        
        });
        $router->post('', function() use ($htmlTemplate, $request, $users, $request) {
                if ($users->login($request->post('username'), $request->post('password'))) {
                        header('Location: ' . $request->getUrl());
                        exit;
                }
                return $htmlTemplate->render('login', [
                                'username' => $request->post('username'),
                                'login' => true,
                        ]);
        });
        return;
}
