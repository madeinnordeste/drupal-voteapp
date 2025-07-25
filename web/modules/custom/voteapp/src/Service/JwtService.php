<?php


namespace Drupal\voteapp\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Drupal\user\Entity\User;

class JwtService
{

  protected $secret;

  public function __construct()
  {
    $this->secret = getenv('JWT_KEY');
  }

  public function generateToken(User $user)
  {
    $payload = [
      'uid' => $user->id(),
      'name' => $user->getAccountName(),
      'exp' => time() + (3600 * 6), // 6 horas
    ];

    return JWT::encode($payload, $this->secret, 'HS256');
  }

  public function validateToken($jwt)
  {
    try {
      return JWT::decode($jwt, new Key($this->secret, 'HS256'));
    } catch (\Exception $e) {
      return null;
    }
  }
}
