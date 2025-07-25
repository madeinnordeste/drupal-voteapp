<?php

namespace Drupal\voteapp\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


use Drupal\user\Entity\User;

class AuthController extends ApiBaseController
{

  public function login(Request $request)
  {
    $data = json_decode($request->getContent(), TRUE);
    $name = $data['username'] ?? '';
    $pass = $data['password'] ?? '';

    if (!$name || !$pass) {
      throw new BadRequestHttpException('Invalid params.');
    }

    $user = user_load_by_name($name);
    if ($user && \Drupal::service('password')->check($pass, $user->getPassword())) {

      //$token = $this->jwtService->generateToken($user);
      $token = \Drupal::service('voteapp.jwt_service')->generateToken($user);
      return $this->response(compact('token'));
    }

    throw new AccessDeniedHttpException('Invalid credentials.');
  }


  public function profile(Request $request)
  {

    $user = $this->getUserRequest($request);

    return $this->response(
      [
        'uid' => $user->id(),
        'name' => $user->getAccountName(),
        'mail' => $user->getEmail(),
        'roles' => $user->getRoles(),
      ]
    );
  }
}
