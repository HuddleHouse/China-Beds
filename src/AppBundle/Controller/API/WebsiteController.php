<?php

namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebsiteBundle\Controller\BaseController;
use WebsiteBundle\Entity\ContactForm;

/**
 * Website API controller.
 *
 * @Route("/api")
 */
class WebsiteController extends BaseController
{
    /**
     * Creates a new ledger entry.
     *
     * @Route("/api_submit_contact_form", name="api_submit_contact_form")
     * @Method({"POST"})
     */
    public function submitContactFormAction(Request $request)
    {
        $form_data = $request->get('data');

        $contactForm = new ContactForm();
        $html = '<center><h2>New Contact Form submission</h2></center><br><br><div><ul>';
        $data = array();

        foreach($form_data as $item) {
            $html .= "<li>".$item['name'] .": ".$item['value']."</li>";
            $data[$item['name']] = $item['value'];
        }

        $html .= '</ul></div>';

        $contactForm->setName($data['Name']);
        $contactForm->setEmail($data['Email']);
        $contactForm->setAddress($data['Address']);
        $contactForm->setAddress2($data['Address2']);
        $contactForm->setCity($data['City']);
        $contactForm->setState($data['State']);
        $contactForm->setZip($data['Zip']);
        $contactForm->setContactReason($data['contact_reason']);
        $contactForm->setMessage($data['Message']);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($contactForm);
        $em->flush();


        $admin = $em->getRepository('AppBundle:User')->getAllAdminArray();
        foreach($admin as $ad) {
            $email = array(
                'subject' => 'New Contact Form Submission',
                'to' => $ad->getEmail(),
                'body' => $html
            );

            try {
                $this->get('email_service')->sendEmail($email);
            }
            catch(\Exception $e) {
                return new JsonResponse(array('success' => false, 'message' => $e->getMessage()));
            }

        }
//        $this->addFlash('notice', 'Contact form submitted successfully.');

        return JsonResponse::create(['success' => true]);
    }

}