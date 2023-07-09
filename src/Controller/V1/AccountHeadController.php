<?php

namespace App\Controller\V1;

use App\Entity\AccountHead;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/bookkeeping", name="bookkeeping_")
 */
class AccountHeadController extends AbstractController
{
    //check API key
    public function __construct(RequestStack $requestStack, ParameterBagInterface $parameterBag)
    {
        $request = $requestStack->getCurrentRequest();

        if (! $request->headers->has('x-api-key') || $request->headers->get('x-api-key') != $parameterBag->get('API_KEY')){
            throw new UnauthorizedHttpException('','Unauthorized!');
        }
    }

    /**
     * @Route("/account-head", name="index_account_head", methods={"GET"})
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return JsonResponse
     */
    public function index(ManagerRegistry $doctrine, Request $request)
    {
        $parameters = $request->query->all();
        $totalHeads = $doctrine->getRepository(AccountHead::class)->count([]);
        $heads = $doctrine->getRepository(AccountHead::class)->getHeads($parameters);
        $response = new JsonResponse([
            'status' => Response::HTTP_OK,
            'total' => count($heads),
            'totalRecords' => $totalHeads,
            'data' => $heads,
        ],Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/account-head", name="create_account_head", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function createAccountHead(Request $request, ManagerRegistry $doctrine)
    {
        $content = $request->toArray();
        $parent = null;

        if (!isset($content['type']) || !in_array(strtoupper(trim($content['type'])), AccountHead::constantAccountHeadsArray())){
            $response = new JsonResponse([
                'status' => Response::HTTP_NOT_FOUND,
                'method' => $request->getMethod(),
                'message' => 'Type not found',
            ],Response::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        if (isset($content['parentId']) && !is_null($content['parentId'])){
           $parent = $doctrine->getRepository(AccountHead::class)->find((int)$content['parentId']);
        }

        $head = new AccountHead();
        $head->setName(isset($content['name']) ? trim($content['name']) : null);
        $head->setCode(isset($content['code']) ? $content['code'] : null);
        $head->setType(trim(strtoupper($content['type'])));
        $head->setStatus(isset($content['status']) ? $content['status'] : true);
        $head->setSlug(isset($content['name']) ? str_replace(' ', '-', strtolower(trim($content['name']))) : null);
        $head->setParent($parent);
        $head->setCreatedAt(new \DateTimeImmutable('now'));
        $head->setUpdatedAt(new \DateTimeImmutable('now'));
        $head->setCreatedBy(isset($content['createdBy']) ? $content['createdBy'] : null);

        $doctrine->getManager()->persist($head);
        $doctrine->getManager()->flush();

        $response = new JsonResponse([
            'status' => Response::HTTP_CREATED,
            'method' => $request->getMethod(),
            'message' => 'Created',
        ],Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }
}
