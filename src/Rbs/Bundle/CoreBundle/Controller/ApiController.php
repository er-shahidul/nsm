<?php

namespace Rbs\Bundle\CoreBundle\Controller;

use Rbs\Bundle\SalesBundle\Entity\Sms;
use Rbs\Bundle\SalesBundle\Helper\SmsParse;
use Rbs\Bundle\SalesBundle\Helper\SmsVehicleParse;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Command;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * ApiController.
 */
class ApiController extends BaseController
{
    /**
     * @Route("/api/sms_receive", name="api_sms_receive")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $smsApiKey = "79b8428a0dea686430a7f20ccbe857bd";

        if ('POST' === $request->getMethod()) {
            if ($smsApiKey == $request->headers->get('X-API-KEY')) {
                $msisdn = $request->request->get('msisdn');
                $timestamp = $request->request->get('timestamp') ? $request->request->get('timestamp') : date('Y-m-d H:i:s');
                $message = $request->request->get('message');
                $messageid = $request->request->get('messageid') ? $request->request->get('messageid') : 0;

                if ($msisdn == null and $message == null) {
                    $response = new Response(json_encode(array("message" => 'Bad request. Invalid Parameter')), 400);
                } else {
                    try {
                        $entity = new Sms();
                        $entity->setMsg($message);
                        $entity->setDate(new \DateTime($timestamp));
                        $entity->setMobileNo($msisdn);
                        $entity->setSl($messageid);
                        $entity->setStatus('NEW');
                        $this->getDoctrine()->getManager()->persist($entity);
                        $this->getDoctrine()->getManager()->flush();

                        $smsParse = new SmsParse($this->getDoctrine()->getManager(), $this->container, $msisdn);
                        $smsParse->parse($entity);

                        $response = new Response(json_encode(array("message" => 'SMS received Successfully')), 200);
                    } catch (\Exception $e) {
                        $response = new Response(json_encode(array("message" => 'Server Internal Error')), 500);
                    }
                }
            } else {
                $response = new Response(json_encode(array("message" => 'Authentication Fail')), 401);
            }
        } else {
            $response = new Response(json_encode(array("message" => 'Invalid Request')), 404);
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/api/sms_vehicle_receive", name="api_sms_vehicle_receive")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function vehicleCreateAction(Request $request)
    {
        $smsApiKey = "79b8428a0dea686430a7f20ccbe857bd";

        if ('POST' === $request->getMethod()) {

            if ($smsApiKey == $request->headers->get('X-API-KEY')) {

                $senderPhoneNumber = $request->request->get('senderPhoneNumber');
                $message = $request->request->get('message');

                $user = $this->getDoctrine()->getRepository('RbsUserBundle:User')->findByPhoneNumber($senderPhoneNumber);

                if($user == null) {
                    $response = new Response(json_encode(array("message" => 'Invalid Phone Number')), 400);
                }else{
                    if ($message == null) {
                        $response = new Response(json_encode(array("message" => 'Bad request. Invalid Parameter')), 400);
                    } else {
                        try {
                            $smsVehicleParse = new SmsVehicleParse($this->getDoctrine()->getManager(), $user[0]);
                            $smsVehicleParse->parse($message);

                            $response = new Response(json_encode(array("message" => 'SMS received Successfully')), 200);
                        } catch (\Exception $e) {
                            $response = new Response(json_encode(array("message" => 'Server Internal Error')), 500);
                        }
                    }
                }
            } else {
                $response = new Response(json_encode(array("message" => 'Authentication Fail')), 401);
            }
        } else {
            $response = new Response(json_encode(array("message" => 'Invalid Request')), 404);
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/api/sms_order_chick", name="api_sms_order_chick")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function orderChickAction(Request $request)
    {
        $smsApiKey = "79b8428a0dea686430a7f20ccbe857bd";

        if ('POST' === $request->getMethod()) {

            if ($smsApiKey == $request->headers->get('X-API-KEY')) {

                $senderPhoneNumber = $request->request->get('senderPhoneNumber');
                $message = $request->request->get('message');

                if ($message == null) {
                    return new JsonResponse(array("message" => 'Bad request. Invalid Parameter'), 400);
                }

                $user = $this->getDoctrine()->getRepository('RbsUserBundle:User')->findByPhoneNumber($senderPhoneNumber);
                if ($user == null) {
                    return new JsonResponse(array("message" => 'Invalid Phone Number'), 400);
                }

                /** @var User $user */
                $user = $user[0];
                if ($user->getUserType() != USER::SR) {
                    return new JsonResponse(array("message" => 'Invalid Phone Number'), 400);
                }

                try {
                    $smsVehicleParse = new SmsVehicleParse($this->getDoctrine()->getManager(), $user);
                    $smsVehicleParse->parse($message);

                    $response = new JsonResponse(json_encode(array("message" => 'SMS received Successfully')), 200);
                } catch (\Exception $e) {
                    $response = new JsonResponse(json_encode(array("message" => 'Server Internal Error')), 500);
                }

            } else {
                $response = new JsonResponse(array("message" => 'Authentication Fail'), 401);
            }
        } else {
            $response = new JsonResponse(array("message" => 'Invalid Request'), 404);
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
