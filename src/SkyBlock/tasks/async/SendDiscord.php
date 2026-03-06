<?php

namespace SkyBlock\tasks\async;

use pocketmine\scheduler\AsyncTask;

class SendDiscord extends AsyncTask {
    private string $webhook, $curlopts;

    public function __construct($webhook, $curlopts) {
        $this->webhook = $webhook;
        $this->curlopts = $curlopts;
    }

    public function onRun() : void {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->webhook);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(unserialize($this->curlopts)));
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $curlerror = curl_error($curl);
        $responsejson = (array) json_decode($response, true);
        $success = false;
        $error = '';
        if ($curlerror != "") {
            $error = "Unkown error occured, sorry xD";
        } elseif (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 204) {
            $error = $responsejson['message'];
        } elseif (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 204 or $response === "") {
            $success = true;
        }
        $result = ["Response" => $response, "Error" => $error, "success" => $success];
        $this->setResult($result);
    }

    public function onCompletion() : void {
    }
}