<?php

namespace App\Controller\V1;

use App\Entity\AccountHead;
use App\Entity\Transaction;
use App\Entity\TransactionBatch;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\ErrorHandler\Error\UndefinedMethodError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/bookkeeping", name="bookkeeping_")
 */
class BookkeepingController extends AbstractController
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
     * @Route("/manual-transaction", name="manual_transaction", methods={"POST"})
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return JsonResponse
     */
    public function createManualTransaction(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $content = $request->toArray();

        if (! array_key_exists('DEBIT', $content['transactions']) || ! array_key_exists('DEBIT', $content['transactions']) ){
            $response = new JsonResponse([
                'status' => Response::HTTP_NOT_ACCEPTABLE,
                'message' => 'Invalid transaction',
                'data' => $content,
            ],Response::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }


        $debitSum = array_sum(array_column($content['transactions']['DEBIT'], 'amount'));
        $creditSum = array_sum(array_column($content['transactions']['CREDIT'], 'amount'));


        if ($debitSum !== $creditSum){
            $response = new JsonResponse([
                'status' => Response::HTTP_NOT_ACCEPTABLE,
                'message' => 'Debit and Credit totals are not equal',
                'data' => $content,
            ],Response::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }


        $transactionBatch = new TransactionBatch();
        $transactionBatch->setReferenceNo(mt_rand(100000,999999));
        $transactionBatch->setNarration($content['remark']);
        $transactionBatch->setCreatedBy((int)$content['createdBy']);
        $transactionBatch->setCreatedAt(new \DateTimeImmutable('now'));
        $transactionBatch->setUpdatedAt(new \DateTimeImmutable('now'));


        $doctrine->getManager()->persist($transactionBatch);

        foreach ($content['transactions']['DEBIT'] as $item) {
            $findHead = $doctrine->getRepository(AccountHead::class)->find((int)$item['head']);

            $transaction = new Transaction();
            $transaction->setHead($findHead);
            $transaction->setHeadName($findHead->getName());
            $transaction->setNarration(trim($item['narration']));
            $transaction->setAmount($item['amount']);
            $transaction->setType(Transaction::TRANSACTION_TYPE_DEBIT);
            $transaction->setCreatedAt(new \DateTimeImmutable('now'));
            $transaction->setUpdatedAt(new \DateTimeImmutable('now'));

            $transactionBatch->addTransaction($transaction);

        }

        foreach ($content['transactions']['CREDIT'] as $item) {
            $findHead = $doctrine->getRepository(AccountHead::class)->find((int)$item['head']);

            $transaction = new Transaction();
            $transaction->setHead($findHead);
            $transaction->setHeadName($findHead->getName());
            $transaction->setNarration(trim($item['narration']));
            $transaction->setAmount($item['amount']);
            $transaction->setType(Transaction::TRANSACTION_TYPE_CREDIT);
            $transaction->setCreatedAt(new \DateTimeImmutable('now'));
            $transaction->setUpdatedAt(new \DateTimeImmutable('now'));

            $transactionBatch->addTransaction($transaction);

        }

        $doctrine->getManager()->flush();




//        dd($content, $debitSum, $creditSum);
/*

        $findCreditHead = $doctrine->getRepository(AccountHead::class)->findSubHead((int)$content['creditHead']);

        $findDebitHead = $doctrine->getRepository(AccountHead::class)->findSubHead((int)$content['debitHead']);

        if (! $findCreditHead || ! $findDebitHead){
            $response = new JsonResponse([
                'status' => Response::HTTP_OK,
                'message' => 'Head not found',
                'data' => $content,
            ],Response::HTTP_OK);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $debitTransaction = new Transaction();
        $debitTransaction->setHead($findDebitHead);
        $debitTransaction->setHeadName($findDebitHead->getName());
        $debitTransaction->setNarration(trim($content['remark']));
        $debitTransaction->setAmount($content['amount']);
        $debitTransaction->setType(Transaction::TRANSACTION_TYPE_DEBIT);
        $debitTransaction->setCreatedAt(new \DateTimeImmutable('now'));
        $debitTransaction->setUpdatedAt(new \DateTimeImmutable('now'));

        $doctrine->getManager()->persist($debitTransaction);

        $creditTransaction = new Transaction();
        $creditTransaction->setHead($findCreditHead);
        $creditTransaction->setHeadName($findCreditHead->getName());
        $creditTransaction->setNarration(trim($content['remark']));
        $creditTransaction->setAmount($content['amount']);
        $creditTransaction->setType(Transaction::TRANSACTION_TYPE_CREDIT);
        $creditTransaction->setCreatedAt(new \DateTimeImmutable('now'));
        $creditTransaction->setUpdatedAt(new \DateTimeImmutable('now'));

        $doctrine->getManager()->persist($creditTransaction);

        $doctrine->getManager()->flush();*/

        $response = new JsonResponse([
            'status' => Response::HTTP_CREATED,
            'message' => 'Created',
            'data' => $content,
        ],Response::HTTP_CREATED);
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }
}
