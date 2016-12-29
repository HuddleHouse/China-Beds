<?php


namespace AppBundle\Controller;
use InventoryBundle\Entity\Channel;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Retailer controller.
 *
 * @Route("/retailer")
 */
class RetailerController extends Controller
{
      /**
     * @Route("/affiliates", name="retailer_affiliates")
     */
    public function affiliatesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('AppBundle:Retailer:affiliates.html.twig');
    }

     /**
     * @Route("/get/retailer/users/{channel}", name="retailer_affiliates_get_users")
     */
    public function usersAction(Channel $channel)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->getAllRetailersArray($channel);


        $userObjects = array();
        foreach ($users as $user) {
            if ( $user->getAddressLatitude() ) {
                $userObjects[] = [
                    'id' => $user->getId(),
                    'company_name' => $user->getCompanyName(),
                    'first_name' => $user->getFirstName(),
                    'last_name' => $user->getLastName(),
                    'address1' => $user->getAddress1(),
                    'address2' => $user->getAddress2(),
                    'city' => $user->getCity(),
                    'state' => $user->getState() ? $user->getState()->getAbbreviation() : null,
                    'zip' => $user->getZip(),
                    'phone' => $user->getPhone(),
                    'lat' => $user->getAddressLatitude(),
                    'long' => $user->getAddressLongitude()
                ];
            }
        }
        return new JsonResponse($userObjects, 200);

        return $this->render('AppBundle:Retailer:users.html.twig', array(
            'users' => $users
        ));
    }

    private function latLongConvert($address)
    {
        $url = "http://maps.google.com/maps/api/geocode/json?address=".urlencode($address)."&sensor=false&region=false";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response);

        $lat = 0;
        $long = 0;

        if (is_object($response_a)) {
            if (count($response_a->results)>0) {
                $lat = $response_a->results[0]->geometry->location->lat;
                $long = $response_a->results[0]->geometry->location->lng;
            }
        }

         return ['lat' => $lat, 'long' => $long];

    }
}
