<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exceptions\DataAnalyzeException;
use App\Services\Analyzer;
use App\Services\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="data_analyze_")
 */
class DataAnalyzeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('dataAnalyze.html.twig');
    }

    /**
     * @Route("/fact", name="get_fact", methods={"POST"})
     */
    public function getFact(Request $request, Analyzer $analyzer): Response
    {
        try {
            $securityName = $request->get('securityName');
            $expression = $request->get('expression');
            $fact = $analyzer->getFact($securityName, $expression);
        } catch (DataAnalyzeException $e) {
            return $this->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            Logger::log($e->getMessage());

            return $this->json([
                'message' => 'Internal server error, please contact support',
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'message' => "The result for the security '$securityName' is '$fact'",
        ], Response::HTTP_OK);
    }
}
