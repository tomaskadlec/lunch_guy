<?php
namespace Net\TomasKadlec\d2sBundle\Service\Output;

use GuzzleHttp\Client;
use Net\TomasKadlec\d2sBundle\Service\OutputInterface;

/**
 * Class Slack
 * @package Net\TomasKadlec\d2sBundle\Service\Output
 */
class Slack implements OutputInterface
{
    public function isSupported($format)
    {
        return ($format == 'slack');
    }

    public function supports()
    {
        return [ 'slack' ];
    }

    public function format($format, $restaurantId, $menu)
    {
        if (!$this->isSupported($format))
            throw new \RuntimeException("Format ${format} is not supported.");
        $result = "*{$restaurantId}*\n";
        foreach ($menu as $dishType => $dishes) {
            $result .= "\n_{$dishType}_\n\n";
            foreach ($dishes as $dish) {
                $result .= '> • ' . $dish[0] . ' ' . $this->getEmoji($dish[0]) . ' (' . (!empty($dish[1]) ? $dish[1] .' Kč' : ' - Kč') .")\n";
            }
        }
        return $result ."\n";
    }

    public function send($format, $restaurantId, $menu, $options = [])
    {
        if (!$this->isSupported($format))
            throw new \RuntimeException("Format ${format} is not supported.");
        if (is_array($menu))
            $menu = $this->format($format, $restaurantId, $menu);
        if (!isset($options['uri']) || !isset($options['username']) || !isset($options['channel']))
            throw new \RuntimeException("Options d2s.output.slack.{uri,username,channel} are required.");

        $payload = [
            'text' => $menu,
            'channel' => $options['channel'],
            'username' => $options['username'],
        ];
        if (isset($options['icon_emoji']))
            $payload['icon_emoji'] = $options['icon_emoji'];

        $client = new Client();
        $response = $client->request(
            'POST',
            $options['uri'],
            [
                'form_params' => [
                    'payload' => json_encode($payload)
                ]
            ]
        );
        if (empty($response)){
            throw new \RuntimeException('Failed! No reason given.');
        } else if ($response->getStatusCode() != 200) {
            throw new \RuntimeException('Failed! ' . $response->getBody()->getContents());
        }
    }

    protected function getEmoji($string)
    {
        $emojis = [
            'pig' => [ 'vepř' ],
            'cow2' => [ 'hověz', 'telec' ],
	    'boar' => [ 'divoč' ],
	    'cake' => [ 'cake', 'koláč' ],
	    'chicken' => [ 'kuře' ],
	    'spaghetti' => [ 'špaget' ],
	    'tomato' => [ 'rajská', 'rajčat' ],
	    'pear' => [ 'hrušk' ],
	    'fish' => [ 'pstruh', 'kapr', 'losos' ]
        ];
        $result = [];
        foreach ($emojis as $emoji => $patterns) {
            foreach ($patterns as $pattern)
                if (preg_match("/$pattern/i", $string))
                    $result[] = ":{$emoji}:";
        }
        return join('', $result);
    }

}
