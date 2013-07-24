<?php
/**
 * SMSAERO class for smsaero.ru
 *
 * @package server API methods
 * @autor Georgy Shpak http://sllite.ru/
 * @version 1.0
 */

class Smsaero {
    private $gate;
    private $username;
    private $password;
    private $from;
    private $typeanswer;
    private $useragent;
    
    function __construct($username,
                         $password,
                         $from,
                         $typeanswer = 'json',
                         $useragent = 'Mozilla/5.0 (Windows NT 6.1; rv:15.0) Gecko/20100101 Firefox/15.0.1')
    {
        $this->username     = $username;
        $this->password     = md5($password);
        $this->typeanswer   = "&answer={$typeanswer}";
        $this->from         = $from;
        $this->useragent    = $useragent;
        $this->gate         = 'http://gate.smsaero.ru';
    }

    /**
     * Отправить запрос на сервер
     * @param $url адрес запроса
     * @return mixed
     */
    private function send_post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gate . $url . '?' . str_replace('+', '%20', http_build_query($data)) . $this->typeanswer);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
    
    /**
     * Передача сообщения
     * @param $to Номер телефона получателя, в формате 71234567890
     * @param $text Текст сообщения, в UTF-8 кодировке
     * @param $from Подпись отправителя (например TEST)
     * @param $date Дата для отложенной отправки сообщения (количество секунд с 1 января 1970 года)
     */
    function send( $to, $text, $from=null, $date=null )
    {
        if(is_null($from))
            $from=$this->from;
        if(is_null($date))
            $date=time()+100;
        
        $response = $this->send_post(
            "/send/",
            array(
                'user'     => $this->username,
                'password' => $this->password,
                'to'       => $to,
                'text'     => $text,
                'from'     => $from,
                'date'     => $date
            )
        );
        
        return $response;
    }
    
    /**
     * Проверка состояния отправленного сообщения
     * @param $id Идентификатор сообщения, который вернул сервис при отправке сообщения
     */
    function getStatus($id)
    {
        return $this->send_post(
            "/status/",
            array(
                'user' => $this->username,
                'password' => $this->password,
                'id' => $id
            )
        );
    }

    /**
     * Проверка состояния счёта
     */
    function getBalance()
    {
        return $this->send_post(
            "/balance/",
            array(
                'user' => $this->username,
                'password' => $this->password
            )
        );
    }
    
    /**
     * Список доступных подписей отправителя
     */
    function getSenders()
    {
        return $this->send_post(
            "/senders/",
            array(
                'user' => $this->username,
                'password' => $this->password
            )
        );
    }
}
