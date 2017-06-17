<?php
namespace Worker\Package\Utils;
use GuzzleHttp\Cookie\SetCookie;

use GuzzleHttp\Utils;
/**
 * Class FileCookieJar
 * @package Worker\Package\Utils
 */
class FileCookieJar extends \GuzzleHttp\Cookie\FileCookieJar
{
    /**
     * Saves the cookies to a file.
     *
     * @param string $filename File to save
     * @throws \RuntimeException if the file cannot be found or created
     */
    public function save($filename)
    {
        $json = [];
        foreach ($this as $cookie) {
            //此处保存所有COOKIE! 不做验证
            $json[] = $cookie->toArray();
        }

        if (false === file_put_contents($filename, json_encode($json))) {
            throw new \RuntimeException("Unable to save file {$filename}");
        }
    }



    /**
     * Load cookies from a JSON formatted file.
     *
     * Old cookies are kept unless overwritten by newly loaded ones.
     *
     * @param string $filename Cookie file to load.
     * @throws \RuntimeException if the file cannot be loaded.
     */
    public function load($filename)
    {
        $json = file_get_contents($filename);
        if (false === $json) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException("Unable to load file {$filename}");
            // @codeCoverageIgnoreEnd
        }

        $data = Utils::jsonDecode($json, true);
        if (is_array($data)) {
            foreach (Utils::jsonDecode($json, true) as $cookie) {
                $setCookie = new SetCookie($cookie);
//                if($setCookie->getDomain() == 'eWorkerlogin.51job.com'){
//                    $setCookie->setDomain('eWorker.51job.com');
//                }
                if(!$setCookie->getExpires()){
                    $setCookie->setExpires(time()+3600*24*365);
                }
                $this->setCookie($setCookie);
            }
        } elseif (strlen($data)) {
            throw new \RuntimeException("Invalid cookie file: {$filename}");
        }
    }
}
