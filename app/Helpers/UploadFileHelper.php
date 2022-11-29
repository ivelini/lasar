<?php


namespace App\Helpers;


class UploadFileHelper
{
    /*
     * Источник загрузки файла:
     * request - загрузка через input сайта
     * link - загрузка по ссылке
     */
    private $source;
    private $url;
    private $requestFileName;

    public function getLink(string $url)
    {
        $this->source = 'link';
        $this->url = $url;
    }

    public function getRequest(string $requestFileName)
    {
        $this->source = 'request';
        $this->requestFileName = $requestFileName;
    }

}
