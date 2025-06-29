<?php


namespace App\Widget;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\RatingRepository;


class Rating extends AbstractController
{

    public function show(RatingRepository $ratingRepository){

        $rating = array();
        $rating['result'] = $ratingRepository->findOneBy(['path'=>$_SERVER['REQUEST_URI']]);
        if(!is_null($rating['result'])) {
            $rating['stars_html'] = $this->renderStars($rating['result']->getValue());
        }
        else{
            $rating['stars_html'] = $this->renderStars('4.9');
        }
        if (null === $rating) {
            return;
        }
        $ratingItems = [
            [
                'link' => 'https://kamennye-tehnologii.blizko.ru/reviews',
                'img'=> 'images/blizko.jpg',
                'alt'=> 'Близко.ру',
                'stars'=> 4.6,
                'rating'=> 5.0
            ],
            [
                'link' => 'https://yandex.ru/maps/org/kamtekh/1118245677/?ll=37.751269%2C55.762738&z=14',
                'img'=> 'images/rating-logo-yandex.png',
                'alt'=> 'Yandex',
                'stars'=> 4.7,
                'rating'=> 4.7
            ],
            [
                'link' => 'https://goo.gl/maps/PrAZMvq9TgVKp4dC7',
                'img'=> 'images/rating-logo-google.png',
                'alt'=> 'Google',
                'stars'=> 5,
                'rating'=> 5.0
            ],
            [
                'link' => 'https://go.2gis.com/caj03f',
                'img'=> 'images/rating-logo-2gis.png',
                'alt'=> '2 Gis',
                'stars'=> 5.0,
                'rating'=> 5.0
            ],
        ];

        $ratingItems1 = [
            [
                'link' => 'https://kamennye-tehnologii.blizko.ru/reviews',
                'img'=> 'images/blizko.jpg',
                'alt'=> 'Близко.ру',
                'stars'=> 5.0,
                'rating'=> 5.0
            ],
            [
                'link' => 'https://yandex.ru/maps/org/kamtekh/1118245677/?ll=37.751269%2C55.762738&z=14',
                'img'=> 'images/rating-logo-yandex.png',
                'alt'=> 'Yandex',
                'stars'=> 4.3,
                'rating'=> 4.3
            ],
            [
                'link' => 'https://goo.gl/maps/PrAZMvq9TgVKp4dC7',
                'img'=> 'images/rating-logo-google.png',
                'alt'=> 'Google',
                'stars'=> 5,
                'rating'=> 5.0
            ],
            [
                'link' => 'https://go.2gis.com/caj03f',
                'img'=> 'images/rating-logo-2gis.png',
                'alt'=> '2 Gis',
                'stars'=> 5.0,
                'rating'=> 5.0
            ],
        ];


if(!is_null($rating['result'])) {
    foreach ($ratingItems as $key => $item) {
        $ratingItems[$key]['stars_html'] = $this->renderStars($item['stars']);
    }
    return $this->render('widgets/rating.html.twig', compact('ratingItems', 'rating'));
}else{
    foreach ($ratingItems1 as $key => $item) {
        $ratingItems1[$key]['stars_html'] = $this->renderStars($item['stars']);
    }
    return $this->render('widgets/rating.html.twig', compact('ratingItems1', 'rating'));
}

    }

    private function renderStars($value, $max=5)
    {
        $html = '<div class="stars">';
        for ($i=1;$i<=$max;$i++){
            $fill_class = '';
            if ($i <= $value ) {
                $fill_class = 'on';
            }
            elseif ($i > $value && $i-1 < $value){
                $fill_class = 'half';
            }

            $html .='<span class="star '.$fill_class.'"></span>';
        }
        $html .='</div>';
        return $html;
    }

}