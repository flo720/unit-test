<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Security\PasswordEncoder;
use AppBundle\Security\TokenManager;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DefaultController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Method("POST")
     *
     * @param Request $request
     * @param PasswordEncoder $passwordEncoder
     * @param ManagerRegistry $entityManager
     * @param TokenManager $tokenManager
     * @return array
     * @throws HttpException
     */
    public function loginAction(
        Request $request,
        PasswordEncoder $passwordEncoder,
        ManagerRegistry $entityManager,
        TokenManager $tokenManager
    )
    {
        /** @var Logger $logger */
        $logger = $this->get('monolog.logger.login');
        $infos  = [
            'username'  => $request->get('username'),
            'client_ip' => $request->getClientIp(),
        ];

        $user = $entityManager
            ->getRepository('AppBundle:User')
            ->findPartialOneByCredentials(
                $request->get('username'),
                $passwordEncoder->encrypt($request->get('password'))
            );

        if (!($user instanceof User)) {
            $logger->error('Token not created, User not found', $infos);

            throw new HttpException(Response::HTTP_NOT_FOUND, 'User not found');
        }

        $token = $tokenManager->createToken($user);

        $logger->info('Token sucessfully created', $infos);

        return ['token' => (string)$token];
    }

    /**
     * @Route("/validate-token", name="validate-token")
     * @Method("POST")
     *
     * @param Request $request
     * @param ManagerRegistry $entityManager
     * @param TokenManager $tokenManager
     * @return array
     * @throws HttpException
     */
    public function validateTokenAction(Request $request, ManagerRegistry $entityManager, TokenManager $tokenManager)
    {
        $token  = $tokenManager->parseTokenString($request->request->get('token'));
        $logger = $this->get('monolog.logger.validation');
        $infos  = ['token' => (string)$token];

        if (null === $token) {
            $logger->error('Token is not readable', $infos);

            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Invalid Token');
        }

        $user = $entityManager
            ->getRepository('AppBundle:User')
            ->findPartialOneById($token->getClaim('id'));

        if (!($user instanceof User)) {
            $logger->error('User not found', $infos);

            throw new HttpException(Response::HTTP_NOT_FOUND, 'User not found');
        }

        if (!$tokenManager->checkTokenSignature($token)) {
            $logger->error('Token signatures do not match. User concerned : ', ['user_id' => $user->getId()]);

            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Invalid Token');
        }

        if (!$tokenManager->checkTokenValidity($token)) {
            $logger->error('Token is not valid. User concerned : ', ['user_id' => $user->getId()]);

            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Invalid Token');
        }

        return $user;
    }
}
