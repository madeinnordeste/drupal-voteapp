<?php

namespace Drupal\voteapp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Drupal\user\Entity\User;

class ApiBaseController extends ControllerBase
{

  public function response(array $data = [])
  {

    $response = [
      'status' => 'success',
      'time' => time(),
      'data' => $data
    ];

    return new JsonResponse($response);
  }


  public function getUserRequest(Request $request)
  {
    $authHeader = $request->headers->get('Authorization');

    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
      throw new BadRequestHttpException('Token not found.');
    }

    $token = $matches[1];
    $payload = \Drupal::service('voteapp.jwt_service')->validateToken($token);

    if (!$payload) {
      throw new AccessDeniedHttpException('Invalid token.');
    }

    $user = User::load($payload->uid);

    if (!$user || !$user->isActive()) {
      throw new AccessDeniedHttpException('Invalid user.');
    }

    return $user;
  }
}
