<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\CardDistributionService;
use Cake\Http\Exception\BadRequestException;

class CardsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->cardService = new CardDistributionService();
        
        // Load RequestHandler only
        $this->loadComponent('RequestHandler');
        
        // Configure RequestHandler for JSON
        $this->RequestHandler->setConfig('inputTypeMap', [
            'json' => ['json_decode', true]
        ]);
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        // Set CORS headers
        $this->response = $this->response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:3000')
            ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With')
            ->withHeader('Access-Control-Allow-Credentials', 'true');

        // Handle OPTIONS requests
        if ($this->request->is('options')) {
            return $this->response->withStatus(200);
        }
    }

    public function distribute()
    {
        try {
            if (!$this->request->is('post')) {
                throw new BadRequestException('Method not allowed');
            }

            $data = $this->request->getData();
            
            if (empty($data)) {
                $data = $this->request->input('json_decode', true);
            }
            
            if (empty($data) || !isset($data['numPeople'])) {
                throw new BadRequestException('Invalid request data');
            }

            $numPeople = $data['numPeople'];
            
            if (!is_numeric($numPeople) || $numPeople <= 0) {
                throw new BadRequestException('Number of people must be a positive number');
            }

            $result = $this->cardService->distribute((int)$numPeople);

            return $this->response
                ->withType('application/json')
                ->withStringBody(json_encode([
                    'status' => 'success',
                    'data' => $result
                ]));

        } catch (\Exception $e) {
            return $this->response
                ->withType('application/json')
                ->withStatus(400)
                ->withStringBody(json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]));
        }
    }
}
