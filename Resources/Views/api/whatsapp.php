<?php

$whatsapp = new \App\Services\WhatsappService();
echo $whatsapp->getAccessToken();