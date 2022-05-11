<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Card\Deck;
use App\Card\DeckWith2Jokers;
use App\Card\Player;

class CardController extends AbstractController
{
    /**
     * @Route("/card/deck/shuffle", name="card-deck-shuffle")
     */
    public function shuffleCards(SessionInterface $session): Response
    {
        $session->clear();

        $deck = new Deck();
        $deck->getAllCards();
        $shuffleCards = $deck->shuffleCards();

        $data = [
            'title' => 'Blandar kort',
            'cards' => $shuffleCards,
        ];

        return $this->render('card-deck.html.twig', $data);
    }

    /**
     * @Route("/card/deck/draw", name="card-deck-draw")
     */
    public function drawCard(SessionInterface $session): Response
    {
        $deck = $session->get("deck") ?? new Deck();

        if ($deck->cardsLeft() == 52) {
            $deck->shuffleCards();
        }

        $drawCards = $deck->drawCard(1);
        $cardsLeft = $deck->cardsLeft();

        $session->set("deck", $deck);

        $data = [
            'title' => 'Dra ett kort',
            'cards' => $drawCards,
            'cardLeft' => $cardsLeft,
        ];

        return $this->render('card-draw.html.twig', $data);
    }

    /**
     * @Route("/card/deck/draw/{number}", name="card-deck-draw-number")
     */
    public function drawNrOfCards(int $number, SessionInterface $session): Response
    {
        $deck = $session->get("deck") ?? new Deck();

        if ($deck->cardsLeft() == 52) {
            $deck->shuffleCards();
        }

        $drawCards = $deck->drawCard($number);
        $cardsLeft = $deck->cardsLeft();

        $session->set("deck", $deck);

        $data = [
            'title' => 'Dra ett eller flera kort',
            'cards' => $drawCards,
            'cardLeft' => $cardsLeft,
        ];
        return $this->render('card-draw.html.twig', $data);
    }

    /**
     * @Route("/card/deck/deal/{players}/{cards}", name="card-deck-deal-players-cards")
     */
    public function dealToPlayers(int $players, int $cards, SessionInterface $session): Response
    {
        $deck = $session->get("deck") ?? new Deck();

        if ($deck->cardsLeft() == 52) {
            $deck->shuffleCards();
        }

        $playerArray = [];

        for ($x = 0; $x < $players; $x++) {
            $playerArray[] = new Player($x + 1, $deck->drawCard($cards));
        }

        $cardsLeft = $deck->cardsLeft();

        $session->set("deck", $deck);

        $data = [
            'title' => 'Ger ett antal kort till ett antal spelare',
            'players' => $playerArray,
            'cardLeft' => $cardsLeft,
        ];
        return $this->render('card-deal-to-players.html.twig', $data);
    }
}
