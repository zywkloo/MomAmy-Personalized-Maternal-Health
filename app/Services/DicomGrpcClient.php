<?php

namespace App\Services;

use Grpc\BaseStub;
use Grpc\ChannelCredentials;
use Illuminate\Contracts\Config\Repository as Config;

class DicomGrpcClient extends BaseStub
{
    public function __construct(Config $config)
    {
        $endpoint = $config->get('services.dicom.host', '127.0.0.1:50051');
        parent::__construct($endpoint, [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);
    }

    public function parse(array $request)
    {
        return $this->_simpleRequest(
            '/dicom.Parser/Parse',
            $request,
            ['\Google\Protobuf\Any', 'decode']
        );
    }
}

