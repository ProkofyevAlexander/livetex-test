<?php

namespace LivetexTest\Services;


class Curl
{

    private $url = '';
    private $referer = '';
    private $html = '';
    private $received_headers = array();
    private $info_headers = '';
    private $post = array();
    private $cookies = array();
    private $error = false;
    private $curl_error = false;
    private $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5 GTB6';
    private $custom_headers = array();

    public function __construct($url = '')
    {
        $this->url = $url;

        return $this;
    }

    /* SETTERS */

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    public function setCookies($cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    public function setCustomHeaders($custom_headers)
    {
        $this->custom_headers = $custom_headers;

        return $this;
    }

    public function setUserAgent($type)
    {
        if ($type == 'mobile') {
            $this->user_agent = 'Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30';
        } else {
            $this->user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5 GTB6';
        }
    }

    /* GETTERS */
    public function getUrl()
    {
        return $this->url;
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    public function getReceivedHeaders()
    {
        return $this->received_headers;
    }

    public function getInfoHeaders()
    {
        return $this->info_headers;
    }

    public function getError()
    {
        return $this->curl_error;
    }

    /* BUSINESS LOGIC */

    private function cookiesAsString()
    {
        $str = '';
        if (count($this->cookies)) {
            foreach ($this->cookies as $key => $val)
                $str .= $key . '=' . $val . '; ';
        }

        return $str;
    }


    private function setCookiesFromReceivedHeader()
    {
        $pattern = '/([^;=]+)=([^;]+);/im';

        if (isset($this->received_headers['set-cookie']))
            if (preg_match_all($pattern, $this->received_headers['set-cookie'], $matches))
                for ($i = 0; $i < count($matches[1]); $i++)
                    $this->cookies[$matches[1][$i]] = $matches[2][$i];


        return true;
    }

    private function headersToArray($string, $sep)
    {
        $ret_val = array(
            'set-cookie' => ''
        );
        $lines = explode($sep, $string);

        foreach ($lines as $line) {

            if (strpos($line, ':') !== false) {

                preg_match('/([^:]+):\s*(.+)/i', $line, $matches);

                $key = strtolower($matches[1]);
                $val = trim($matches[2]);

                if ($key == 'set-cookie' && strpos($val, ';')) {
                    $val = substr($val, 0, strpos($val, ';'));
                }

                if ($key == 'set-cookie') {
                    $ret_val[$key] .= $val . ';';
                }
                else {
                    $ret_val[$key] = $val;
                }

            } //@TODO статус ответа
            else {
                // HTTP/1.1 301 Moved Permanently
                // HTTP/1.1 200 OK
            }
        }

        return $ret_val;
    }

    public function exec()
    {

        static $curl_loops = 0;
        static $curl_max_loops = 9;

        $this->error = false;

        if ($curl_loops++ >= $curl_max_loops) {
            $curl_loops = 0;
            $this->error = 'too many loops';
        } elseif ($this->url == '') {
            $this->error = 'bad url';
        }

        if ($this->error === false) {
            $ch = curl_init($this->url);

            $headers = array();
            if (!isset($this->custom_headers['Connection'])) {
                $headers[] = 'Connection: keep-alive';
            }
            if (!isset($this->custom_headers['Accept'])) {
                $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
            }
            if (!isset($this->custom_headers['Accept-Language'])) {
                $headers[] = "Accept-Language: en-us,en;q=0.5";
            }

            if ($this->referer != '')
                $headers[] = 'Referer: ' . $this->referer;

            if (count($this->custom_headers) > 0) {
                foreach ($this->custom_headers as $key => $value) {
                    $headers[] = $key . ': ' . $value;
                }
            }

            curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);

            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            curl_setopt($ch, CURLOPT_NOBODY, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_ENCODING, '');

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($ch, CURLOPT_COOKIE, $this->cookiesAsString());

            if (count($this->post)) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->post);
            } else {
                curl_setopt($ch, CURLOPT_POST, false);
            }

            $html = curl_exec($ch);

            $sep = (strpos($html, "\n\n") === false || strpos($html, "\n\n") > strpos($html, "\r\n\r\n"))
                ? "\r\n"
                : "\n";
            $double_sep = $sep . $sep;

            $this->received_headers = $this->headersToArray(substr($html, 0, strpos($html, $double_sep)), $sep);
            $this->html = substr($html, strpos($html, $double_sep) + strlen($double_sep));

            $this->info_headers = curl_getinfo($ch);

            $curl_error_number = curl_errno($ch);

            $this->curl_error = curl_error($ch);

            curl_close($ch);

            $this->setCookiesFromReceivedHeader();

            $this->referer = $this->url;

            $http_code = $this->info_headers['http_code'];
            $last_url = parse_url($this->info_headers['url']);

            if ($curl_error_number != 0) {
                $curl_loops = 0;
                $this->error = 'curl error';
            } elseif ($http_code == 301 || $http_code == 302) {
                $url_arr = @parse_url($this->received_headers['location']);

                if (!$url_arr) {
                    //couldn't process the url to redirect to
                    $curl_loops = 0;
                    $this->error = 'parse new url error';
                } else {

                    if (!isset($url_arr['scheme']) || !$url_arr['scheme'])
                        $url_arr['scheme'] = $last_url['scheme'];
                    if (!isset($url_arr['host']) || !$url_arr['host'])
                        $url_arr['host'] = $last_url['host'];
                    if (!isset($url_arr['path']) || !$url_arr['path'])
                        $url_arr['path'] = $last_url['path'];

                    $this->referer = $this->url;
                    $this->url = $url_arr['scheme'] . '://' . $url_arr['host'] . $url_arr['path'] . (isset($url_arr['query']) ? '?' . $url_arr['query'] : '');

                    return $this->exec();

                }
            }

            $curl_loops = 0;

        }

        return $this->error === false;
    }
}
