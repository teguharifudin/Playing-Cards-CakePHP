<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Log\Log;

class CardDistributionService
{
    private array $suits = ['S', 'H', 'D', 'C'];
    private array $values = ['A', '2', '3', '4', '5', '6', '7', '8', '9', 'X', 'J', 'Q', 'K'];
    private const TOTAL_CARDS = 52;

    public function distribute(int $numPeople): string
    {
        try {
            $this->validateInput($numPeople);
            $deck = $this->generateDeck();
            $this->shuffleDeck($deck);
            $distribution = $this->distributeCardsEvenly($deck, $numPeople);
            return $this->formatOutput($distribution);
        } catch (\Exception $e) {
            Log::error('Card distribution error: ' . $e->getMessage());
            throw new \RuntimeException('Irregularity occurred');
        }
    }

    private function validateInput(int $numPeople): void
    {
        if ($numPeople <= 0) {
            throw new \InvalidArgumentException('Input value must be greater than 0');
        }
    }

    private function generateDeck(): array
    {
        try {
            $deck = [];
            foreach ($this->suits as $suit) {
                foreach ($this->values as $value) {
                    $deck[] = "$suit-$value";
                }
            }
            return $deck;
        } catch (\Exception $e) {
            throw new \RuntimeException('Irregularity occurred during deck generation');
        }
    }

    private function shuffleDeck(array &$deck): void
    {
        try {
            shuffle($deck);
        } catch (\Exception $e) {
            throw new \RuntimeException('Irregularity occurred during deck shuffling');
        }
    }

    private function distributeCardsEvenly(array $deck, int $numPeople): array
    {
        try {
            // Initialize an array with empty arrays for each player
            $distribution = array_fill(0, $numPeople, []);
            $totalCards = count($deck);

            // If there are more people than cards, give one card to each person until cards run out
            if ($numPeople > $totalCards) {
                for ($i = 0; $i < $totalCards; $i++) {
                    $distribution[$i] = [$deck[$i]];
                }
                return $distribution;
            }

            // Calculate minimum cards per person and remaining cards
            // Cast to int to ensure we don't get float values
            $minCardsPerPerson = (int)floor($totalCards / $numPeople);
            $remainingCards = (int)($totalCards % $numPeople);

            // Distribute minimum cards to each person
            for ($i = 0; $i < $numPeople; $i++) {
                $startIndex = (int)($i * $minCardsPerPerson); // Cast to int for array_slice
                $distribution[$i] = array_slice($deck, $startIndex, $minCardsPerPerson);
            }

            // Distribute remaining cards one by one to players
            for ($i = 0; $i < $remainingCards; $i++) {
                $distribution[$i][] = $deck[$totalCards - $remainingCards + $i];
            }

            return $distribution;
        } catch (\Exception $e) {
            throw new \RuntimeException('Irregularity occurred during card distribution');
        }
    }

    private function formatOutput(array $distribution): string
    {
        try {
            $output = [];
            foreach ($distribution as $index => $hand) {
                if (!empty($hand)) {
                    $output[] = implode(',', $hand);
                }
            }
            
            if (empty($output)) {
                throw new \RuntimeException('No cards could be distributed');
            }

            return implode("\n", $output);
        } catch (\Exception $e) {
            throw new \RuntimeException('Irregularity occurred during output formatting');
        }
    }
}
