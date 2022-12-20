<?php


namespace App\Helpers;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\File as fileModel;

class UploadFileHelper
{
    /*
     * Источник загрузки файла:
     * request - загрузка через input сайта
     * link - загрузка по ссылке
     */
    private $source;
    // Адрес, по которому можно загрузить файл
    private $url;
    // Название файла в request
    private $requestFileName;
    private $request;
    // Модель, к которой привязывается файл
    private $relationModel;
    // Путь в директории storage\app\ - 'storage\app\' . $path
    private $path = 'tmp';
    private $fileName = null;
    private $fileModel;


    public function __construct()
    {
        $this->fileModel = new fileModel();
    }

    public function setRelationModel($relationModel)
    {
        $this->relationModel = $relationModel;
        return $this;
    }

    public function setLink(string $url)
    {
        $this->source = 'link';
        $this->url = $url;

        return $this;
    }

    public function setRequest(string $requestFileName, $request)
    {
        $this->source = 'request';
        $this->requestFileName = $requestFileName;
        $this->request = $request;

        return $this;
    }
    public function setPath($path)
    {
        while (mb_substr($path, 0, 1) == '\\') {
            $path = mb_substr($path, 1);
        }

        while (mb_substr($path, -1, 1) == '\\') {
            $path = mb_substr($path, 0, strlen($path) - 1);
        }

        $this->path = $path;

        return $this;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function upload()
    {
        if ($this->fileName == null) $this->fileName = '\file_' . rand(1000, 10000);

        $path = match ($this->source) {
            'link'      => $this->uploadLink(),
            'request'   => $this->uploadRequest(),
        };

        return $path;
    }

    private function uploadLink()
    {
        $storagePath = Storage::path($this->path . '' . $this->fileName);
        $path = null;

        $file = fopen($storagePath, 'w');
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_FILE, $file);

        $response= curl_exec($ch);

        curl_close($ch);
        fclose($file);

        if($response) {
            $this->relationModel->file()->create(['path' => $this->path . '' . $this->fileName]);
            $path = $this->path . '' . $this->fileName;
        }

        return $path;
    }

    private function uploadRequest()
    {
        $path = $this->request->file($this->requestFileName)->storeAs($this->path, $this->fileName);

        $this->relationModel->file()->create(['path' => $this->path . '' . $this->fileName]);

        return $path;
    }
}
