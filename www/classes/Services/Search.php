<?php

namespace LivetexTest\Services;


class Search
{

    const TYPE_LINK = 'link';
    const TYPE_IMAGE = 'image';
    const TYPE_TEXT = 'text';

    public function makeSearch($url, $type, $text) {
        $curl = new Curl($url);
        $curl->exec();
        $html = $curl->getHtml();

        if ($type === self::TYPE_IMAGE) {
            return $this->countImages($html);
        }

        return false;
    }

    private function countImages($html) {
        preg_match_all('/<img[^>]*?src="([^"]*?)"[^>]*?>/ims', $html, $matches);

        return $matches[1];
    }

}