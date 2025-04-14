<?php namespace App\Http\Helpers;

use DateInterval;
use DatePeriod;
use DateTime;
use DOMDocument;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Spatie\ArrayToXml\ArrayToXml;

/**
 * Class helper to request Invocies to CENACE Web Service.
 */
class CENACEInvoicesHelper
{

    /**
     * Retrieve CENACE ECDs (Estado de Cuentas) based on provided parameters and date.
     *
     * @param array $params Parameters for the request.
     * @param string $date Date for the request.
     * @param bool $includeAllFiles Flago to exclude or include Pdf,
     *                              Csv and Html files in response array data.
     *
     * @return array|null Data containing ECDs if successful, otherwise null.
     */
    public function getCENACEECDs($params, $date, $includeAllFiles = true)
    {
        $xml_request = $this->createXmlRequest($params, $date, "GetEstadoCuentas");
        $client = new Client(['http_errors' => false]);
        $headers = [
            'Content-Type'    => 'text/xml; charset=utf-8',
            "accept"          => "*/*",
            "accept-encoding" => "gzip, deflate",
            "SOAPAction"      => "http://tempuri.org/IEdoCuentaService/GetEstadoCuentas",
        ];

        $request = new Request(
            'POST',
            'https://ws01.cenace.gob.mx:8081/WSDownLoadEdoCta/EdoCuentaService.svc',
            $headers,
            $xml_request
        );
        $response = $client->send($request);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $resposeBody = (string) $response->getBody();

        $dom = new DOMDocument();
        libxml_use_internal_errors(true); //disable libxml errors

        $dom->loadXML($resposeBody);
        $getEstadoCuentasResult = $dom->getElementsByTagName('GetEstadoCuentasResult');
        $messageValue = '';
        $statusValue = '';
        $estadosDeCuentaElementArray = [];
        $estadosDeCuentaItems = [];
        $estadosDeCuentaConent = [];
    
        if (isset($getEstadoCuentasResult[0])) {
            $getEstadoCuentasResultElement = $getEstadoCuentasResult[0];
            $getEstadoCuentasResultItems = $getEstadoCuentasResultElement->childNodes;

            foreach ($getEstadoCuentasResultItems as $item) {
                $nodeName = $item->nodeName;

                if ($nodeName === 'a:EC') {
                    $estadosDeCuentaElementArray = $item->childNodes;
                } elseif ($nodeName === 'a:Msg') {
                    $messageValue = $item->nodeValue;
                } elseif ($nodeName === 'a:Status') {
                    $statusValue = $item->nodeValue;
                }
            }
        }

        foreach ($estadosDeCuentaElementArray as $item) {
            $estadosDeCuentaItems[] = $item->childNodes;
        }

        foreach ($estadosDeCuentaItems as $item) {
            if ($messageValue !== '') {
                $messageValue = substr($messageValue, -10);
            }

            $content = [
                'fileName' => $item[0]->nodeValue,
                'fileContent' => $item[4]->nodeValue,
                'subcuenta' => $item[7]->nodeValue,
                'donwloadDate' => $messageValue,
            ];
            if ($includeAllFiles) {
                $content['fileContentPdf'] = $item[3]->nodeValue;
                $content['fileContentCsv'] = $item[1]->nodeValue;
                $content['fileContentHtml'] = $item[2]->nodeValue;
            }

            $estadosDeCuentaConent[] = $content;
        }

        $dateRes = [
            'message' => $messageValue,
            'statusValue' => $statusValue,
            'data' => $estadosDeCuentaConent,
        ];

        return $dateRes;
    }

    /**
    * Retrieves and processes emitted invoices from CENACE web service for a given date range.
    *
    * This function performs the following operations:
    * 1. Makes SOAP requests to CENACE service for each date in the range
    * 2. Processes XML responses to extract invoice data
    * 3. Maps invoice statuses for specific team (teamId 93)
    * 4. Organizes results by date with invoice details and statuses
    *
    * @param int    $teamId    Team identifier for organization and status mapping
    * @param array  $params    Configuration parameters including:
    *                         - tipoDeFecha: Type of date filter ('oper' or 'fuf')
    *                         - usuario: CENACE service username
    *                         - password: CENACE service password
    *                         - sistema: System identifier
    *                         - participante: Participant identifier
    *                         - subcuenta: Subaccount identifier
    * @param string $startDate Start date of the range to fetch invoices for
    * @param string $endDate   End date of the range to fetch invoices for
    *
    * @return array Associative array organized by date containing:
    *               - message: Response message or formatted date
    *               - statusValue: Response status
    *               - data: Array of invoice data including:
    *                 - fileName: Invoice file name
    *                 - fileContent: Base64 encoded invoice content
    *                 - fileContentPdf: Base64 encoded PDF content
    *                 - subcuenta: Subaccount identifier
    *                 - donwloadDate: Date of download
    *                 - fechaAdd: Formatted date added
    *                 - estatusEnvioErp: ERP send status (for teamId 93)
    *
    * @throws \Exception From date handling or HTTP client operations
    * @uses DOMDocument For XML parsing
    * @uses Guzzle\Client For HTTP requests
    * @uses ECDMontosDiarios For status mapping queries
    */
    public function getFacturasEmitidas($params, $startDate, $endDate)
    {
        $method     = $params['tipoDeFecha'] === 'oper' ? 'GetFacturasCEN':'GetFacturasFPubCEN';
        $dateRange  = $this->getDateRange($startDate, $endDate);
        $fileNames = [];

        foreach ($dateRange as $date) {
            $xml_request = $this->createXmlRequest($params, $date, $method);
            $client      = new Client(['http_errors' => false]);
            $headers     = [
                'Content-Type'      => 'text/xml; charset=utf-8',
                "accept"            => "*/*",
                "accept-encoding"   => "gzip, deflate",
                "SOAPAction"        => "http://tempuri.org/IFacturaCENService/".$method,
            ];

            $request = new Request(
                'POST',
                'https://ws01.cenace.gob.mx:8081/WSDownLoadFac/FacturaCENService.svc',
                $headers,
                $xml_request
            );

            $response       = $client->send($request);

            $fechaAdd = new \DateTime($date);
            $fechaAdd = $fechaAdd->format('Y/m/d');

            if ($response->getStatusCode() === 200) {
                $resposeBody = (string) $response->getBody();
                $dom         = new DOMDocument();
                libxml_use_internal_errors(true); //disable libxml errors

                $dom->loadXML($resposeBody);
                $getFacturasCENResult         = $dom->getElementsByTagName($method.'Result');
                $messageValue                 = '';
                $statusValue                  = '';
                $facturasEmitidasElementArray = [];
                $facturasEmitidasItems        = [];
                $facturasEmitidasConent       = [];
        
                if (isset($getFacturasCENResult[0])) {
                    $getFacturasCENResultElement = $getFacturasCENResult[0];
                    $getFacturasCENResultItems   = $getFacturasCENResultElement->childNodes;

                    foreach ($getFacturasCENResultItems as $item) {
                        $nodeName = $item->nodeName;

                        if ($nodeName === 'a:FACT') {
                            $facturasEmitidasElementArray = $item->childNodes;
                        } elseif ($nodeName === 'a:Msg') {
                            $messageValue = $item->nodeValue;
                        } elseif ($nodeName === 'a:Status') {
                            $statusValue = $item->nodeValue;
                        }
                    }
                }

                foreach ($facturasEmitidasElementArray as $item) {
                    $facturasEmitidasItems[] = $item->childNodes;
                }

                foreach ($facturasEmitidasItems as $item) {
                    $fileName      = $item[0]->nodeValue;
                    $fileNames[]   = $fileName;
                    $dateFilename  = substr($fileName, 0, 8);
                    $dateFilename  = DateTime::createFromFormat('Ymd', $dateFilename);
                    $formattedDate = $dateFilename->format('Y-m-d');

                    if ($params['tipoDeFecha'] == 'fuf' && ! in_array($formattedDate, $dateRange)) {
                        continue;
                    }

                    if ($messageValue !== '') {
                        $messageValue = substr($messageValue, -10);
                        if ($params['tipoDeFecha'] === 'oper') {
                            $messageValue = $dateFilename->format('Y/m/d');
                        }
                    }

                    $content = [
                        'fileName'       => $fileName,
                        'fileContent'    => $item[2]->nodeValue,
                        'fileContentPdf' => $item[1]->nodeValue,
                        'subcuenta'      => $item[5]->nodeValue,
                        'donwloadDate'   => $messageValue,
                        'fechaAdd'       => $fechaAdd,
                    ];

                    $facturasEmitidasConent[] = $content;
                }

                $dateRes = [
                    'message'     => $messageValue,
                    'statusValue' => $statusValue,
                    'data'        => $facturasEmitidasConent,
                ];
    
                $allDateRes[$date] = $dateRes;
            }
        }

        return $allDateRes;
    }

    /**
     * Creates a SOAP XML request for CENACE web service operations.
     *
     * Generates a SOAP envelope XML structure using provided parameters for authentication
     * and request data. The function uses ArrayToXml to convert an array structure into
     * valid SOAP XML format.
     *
     * @param array  $params     An associative array containing request parameters:
     *                          - usuario: User credentials (string)
     *                          - password: User password (string)
     *                          - sistema: System identifier (string)
     *                          - participante: Participant identifier (string)
     *                          - subcuenta: Subaccount identifier (string)
     * @param string $singleDate Date for the request in a format expected by the service
     * @param string $method     SOAP method name, defaults to 'GetFacturasCEN'
     *
     * @return string           Generated XML string without XML declaration
     *
     * @example
     * $params = [
     *     'usuario' => 'user123',
     *     'password' => 'pass123',
     *     'sistema' => 'SYS1',
     *     'participante' => 'PART1',
     *     'subcuenta' => 'SUB1'
     * ];
     * $xml = createXmlRequest($params, '2024-01-28');
     *
     * @uses ArrayToXml For converting array structure to XML
     * @see http://schemas.xmlsoap.org/soap/envelope/ SOAP Envelope Schema
     */
    private function createXmlRequest($params, $singleDate, $method = 'GetFacturasCEN')
    {
        $usuario = trim($params['usuario']);
        $password = trim($params['password']);
        $fecha = $singleDate;
        $sistema = $params['sistema'];
        $participante = $params['participante'];
        $subcuenta = $params['subcuenta'];

        $root = [
            'rootElementName' => 'soapenv:Envelope',
            '_attributes' => [
                'xmlns:soapenv' => 'http://schemas.xmlsoap.org/soap/envelope/',
                'xmlns:tem'     => 'http://tempuri.org/',
            ],
        ];

        $array = [
            'soapenv:Header' => [],
            'soapenv:Body' => [
                "tem:$method" => [
                    'tem:usuario' => $usuario,
                    'tem:password' => $password,
                    'tem:fecha' => $fecha,
                    'tem:sistema' => $sistema,
                    'tem:participante' => $participante,
                    'tem:subcuenta' => $subcuenta,
                    'tem:tipo' => '',
                    
                ]
            ]
        ];

        $arrayToXml = new ArrayToXml($array, $root);
        $result = $arrayToXml->dropXmlDeclaration()->toXml();

        return $result;
    }

    /**
     * Generates an array of consecutive dates between two given dates (inclusive).
     *
     * @param string $startDate The start date in a format parseable by DateTime (e.g., 'Y-m-d')
     * @param string $endDate   The end date in a format parseable by DateTime (e.g., 'Y-m-d')
     *
     * @return array An array of dates in 'Y-m-d' format, including both start and end dates
     *
     * @throws \Exception If the date strings are invalid or cannot be parsed by DateTime
     */
    private function getDateRange($startDate, $endDate)
    {
        $begin = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end = $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval, $end);
    
        $dateRange = [];

        foreach ($daterange as $date) {
            $dateRange[] = $date->format('Y-m-d');
        }

        return $dateRange;
    }
}